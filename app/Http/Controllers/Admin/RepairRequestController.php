<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveRepairRequestRequest;
use App\Http\Requests\RejectRepairRequestRequest;
use App\Http\Requests\StoreRepairRequestRequest;
use App\Models\Driver;
use App\Models\RepairRequest;
use App\Models\RepairCategory;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleDepot;
use App\Services\RepairRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View as BladeView;
use Inertia\Inertia;
use Inertia\Response;

/**
 * RepairRequestController - Gestion des demandes de réparation
 *
 * Workflow:
 * 1. Driver creates request → pending_supervisor
 * 2. Supervisor approves/rejects → pending_fleet_manager OR rejected_supervisor
 * 3. Fleet Manager approves/rejects → approved_final OR rejected_final
 *
 * Features:
 * - Multi-tenant isolation (organization_id)
 * - Policy-based authorization
 * - RepairRequestService injection for business logic
 * - Inertia responses for Vue.js frontend
 * - Auto-history and notifications
 *
 * @version 1.0-Enterprise
 */
class RepairRequestController extends Controller
{
    /**
     * Constructor with dependency injection.
     */
    public function __construct(
        protected RepairRequestService $repairService
    ) {
    }

    /**
     * Display a listing of repair requests.
     *
     * Filters based on user role:
     * - Super Admin / Admin / Fleet Manager: all in organization
     * - Supervisor: team requests only
     * - Driver: own requests only
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', RepairRequest::class);

        $user = $request->user();
        $query = RepairRequest::with([
            'driver.user',
            'vehicle',
            'supervisor',
            'fleetManager',
            'category',
        ])
            ->where('organization_id', $user->organization_id);

        // 🔐 FILTRAGE PAR RÔLE
        if ($user->hasRole('Chauffeur')) {
            // Driver: own requests only
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('Supervisor')) {
            // Supervisor: team requests only
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            });
        }
        // Admin/Fleet Manager/Super Admin: all in organization (no filter)

        // 🔍 FILTRES DE RECHERCHE
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%")
                    ->orWhere('uuid', 'ilike', "%{$search}%");
            });
        }

        // 📊 TRI
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // 📄 PAGINATION
        $repairRequests = $query->paginate(
            $request->input('per_page', 15)
        )->withQueryString();

        return Inertia::render('RepairRequests/Index', [
            'repairRequests' => $repairRequests,
            'filters' => $request->only(['status', 'urgency', 'driver_id', 'vehicle_id', 'search']),
            'sort' => [
                'field' => $sortField,
                'direction' => $sortDirection,
            ],
            'can' => [
                'create' => $request->user()->can('repair-requests.create'),
                'approveLevel1' => $request->user()->can('repair-requests.approve.level1'),
                'approveLevel2' => $request->user()->can('repair-requests.approve.level2'),
                'export' => $request->user()->can('repair-requests.export'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new repair request.
     */
    public function create(Request $request): BladeView|Response
    {
        $this->authorize('create', RepairRequest::class);

        $user = $request->user();

        // 📋 DONNÉES POUR LE FORMULAIRE
        $drivers = Driver::with('user')
            ->where('organization_id', $user->organization_id)
            ->whereNull('deleted_at')
            ->get();

        // REFACTORED: Utilisation du scope active() au lieu de where('status', 'active')
        $vehicles = Vehicle::where('organization_id', $user->organization_id)
            ->active() // Scope: status_id = 1 (Actif)
            ->whereNull('deleted_at')
            ->get();

        // ✅ UTILISER RepairCategory au lieu de VehicleCategory
        $categories = RepairCategory::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // 🎯 DÉTECTION: Blade ou Inertia
        if ($request->wantsJson() || $request->header('X-Inertia')) {
            // Format Inertia pour Vue.js
            return Inertia::render('RepairRequests/Create', [
                'drivers' => $drivers->map(fn($driver) => [
                    'id' => $driver->id,
                    'name' => $driver->user->name ?? 'N/A',
                    'license_number' => $driver->license_number,
                    'supervisor_id' => $driver->supervisor_id,
                ]),
                'vehicles' => $vehicles->map(fn($vehicle) => [
                    'id' => $vehicle->id,
                    'name' => $vehicle->vehicle_name ?? $vehicle->registration_plate,
                    'registration_plate' => $vehicle->registration_plate,
                    'brand' => $vehicle->brand,
                    'model' => $vehicle->model,
                ]),
                'categories' => $categories,
                'urgencyLevels' => [
                    RepairRequest::URGENCY_LOW => 'Faible',
                    RepairRequest::URGENCY_NORMAL => 'Normal',
                    RepairRequest::URGENCY_HIGH => 'Élevé',
                    RepairRequest::URGENCY_CRITICAL => 'Critique',
                ],
            ]);
        }

        // 🎨 Vue Blade pour navigation standard
        return view('admin.repair-requests.create', compact('drivers', 'vehicles', 'categories'));
    }

    /**
     * Store a newly created repair request in storage.
     */
    public function store(StoreRepairRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', RepairRequest::class);

        try {
            $repairRequest = $this->repairService->createRequest($request->validated());

            return redirect()
                ->route('admin.repair-requests.show', $repairRequest)
                ->with('success', 'Demande de réparation créée avec succès. Le superviseur a été notifié.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la demande: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified repair request.
     */
    public function show(Request $request, RepairRequest $repairRequest): BladeView|Response
    {
        $this->authorize('view', $repairRequest);

        // 🔄 CHARGER TOUTES LES RELATIONS
        $repairRequest->load([
            'driver.user',
            'driver.supervisor',
            'vehicle',
            'supervisor',
            'fleetManager',
            'category',
            'maintenanceOperation',
        ]);

        // 🎯 DÉTECTION: Blade ou Inertia
        if ($request->wantsJson() || $request->header('X-Inertia')) {
            return Inertia::render('RepairRequests/Show', [
                'repairRequest' => $repairRequest,
                'can' => [
                    'update' => $request->user()->can('update', $repairRequest),
                    'delete' => $request->user()->can('delete', $repairRequest),
                    'approveLevel1' => $request->user()->can('approveLevelOne', $repairRequest),
                    'rejectLevel1' => $request->user()->can('rejectLevelOne', $repairRequest),
                    'approveLevel2' => $request->user()->can('approveLevelTwo', $repairRequest),
                    'rejectLevel2' => $request->user()->can('rejectLevelTwo', $repairRequest),
                    'viewHistory' => $request->user()->can('viewHistory', $repairRequest),
                ],
            ]);
        }

        // 🎨 Vue Blade pour navigation standard
        return view('admin.repair-requests.show', compact('repairRequest'));
    }

    /**
     * Approve repair request by supervisor (Level 1).
     */
    public function approveSupervisor(
        ApproveRepairRequestRequest $request,
        RepairRequest $repairRequest
    ): RedirectResponse {
        $this->authorize('approveLevelOne', $repairRequest);

        try {
            $approved = $this->repairService->approveBySupervisor(
                $repairRequest,
                $request->user(),
                $request->input('comment')
            );

            return redirect()
                ->route('repair-requests.show', $approved)
                ->with('success', 'Demande approuvée avec succès. Les gestionnaires de flotte ont été notifiés.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    /**
     * Reject repair request by supervisor (Level 1).
     */
    public function rejectSupervisor(
        RejectRepairRequestRequest $request,
        RepairRequest $repairRequest
    ): RedirectResponse {
        $this->authorize('rejectLevelOne', $repairRequest);

        try {
            $rejected = $this->repairService->rejectBySupervisor(
                $repairRequest,
                $request->user(),
                $request->input('reason')
            );

            return redirect()
                ->route('repair-requests.show', $rejected)
                ->with('warning', 'Demande rejetée. Le chauffeur a été notifié.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors du rejet: ' . $e->getMessage());
        }
    }

    /**
     * Approve repair request by fleet manager (Level 2).
     */
    public function approveFleetManager(
        ApproveRepairRequestRequest $request,
        RepairRequest $repairRequest
    ): RedirectResponse {
        $this->authorize('approveLevelTwo', $repairRequest);

        try {
            $approved = $this->repairService->approveByFleetManager(
                $repairRequest,
                $request->user(),
                $request->input('comment')
            );

            return redirect()
                ->route('repair-requests.show', $approved)
                ->with('success', 'Demande approuvée définitivement. Une opération de maintenance a été créée automatiquement.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'approbation finale: ' . $e->getMessage());
        }
    }

    /**
     * Reject repair request by fleet manager (Level 2).
     */
    public function rejectFleetManager(
        RejectRepairRequestRequest $request,
        RepairRequest $repairRequest
    ): RedirectResponse {
        $this->authorize('rejectLevelTwo', $repairRequest);

        try {
            $rejected = $this->repairService->rejectByFleetManager(
                $repairRequest,
                $request->user(),
                $request->input('reason')
            );

            return redirect()
                ->route('repair-requests.show', $rejected)
                ->with('warning', 'Demande rejetée définitivement. Le superviseur et le chauffeur ont été notifiés.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors du rejet final: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified repair request from storage.
     */
    public function destroy(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $this->authorize('delete', $repairRequest);

        try {
            // 🗑️ SOFT DELETE
            $repairRequest->delete();

            return redirect()
                ->route('repair-requests.index')
                ->with('success', 'Demande de réparation supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
