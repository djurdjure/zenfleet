<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RepairRequest\StoreRepairRequestRequest;
use App\Http\Requests\Admin\RepairRequest\UpdateRepairRequestRequest;
use App\Http\Requests\Admin\RepairRequest\ApprovalDecisionRequest;
use App\Models\RepairRequest;
use App\Models\Vehicle;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class RepairRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(RepairRequest::class, 'repair_request');
    }

    public function index(Request $request): View
    {
        $query = RepairRequest::with(['vehicle', 'requester', 'supervisor', 'manager', 'assignedSupplier'])
                              ->forOrganization(auth()->user()->organization_id)
                              ->latest('requested_at');

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->get('vehicle_id'));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_plate', 'like', "%{$search}%");
                  });
            });
        }

        // Filtrage par rôle
        $user = Auth::user();
        if ($user->hasRole('driver')) {
            $query->where('requested_by', $user->id);
        } elseif ($user->hasRole('supervisor')) {
            // Superviseurs voient toutes les demandes en attente + celles qu'ils ont traitées
            $query->where(function ($q) use ($user) {
                $q->where('status', RepairRequest::STATUS_PENDING)
                  ->orWhere('supervisor_id', $user->id);
            });
        }

        $repairRequests = $query->paginate(15);

        $stats = $this->getRepairStats();
        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)->get();

        return view('admin.repair-requests.index', compact('repairRequests', 'stats', 'vehicles'));
    }

    public function create(): View
    {
        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
                          ->where('status_id', '!=', 3) // Exclure véhicules hors service
                          ->orderBy('registration_plate')
                          ->get();

        return view('admin.repair-requests.create', compact('vehicles'));
    }

    public function store(StoreRepairRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['requested_by'] = auth()->id();
        $data['requested_at'] = now();

        // Gérer l'upload des photos
        if ($request->hasFile('photos')) {
            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('repair-requests/photos', 'public');
                $photos[] = $path;
            }
            $data['photos'] = $photos;
        }

        // Gérer les attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('repair-requests/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
            $data['attachments'] = $attachments;
        }

        $repairRequest = RepairRequest::create($data);

        // Notifications automatiques pour les demandes urgentes
        if ($repairRequest->isUrgent()) {
            // TODO: Notifier immédiatement les superviseurs
        }

        return redirect()->route('admin.repair-requests.index')
                        ->with('success', 'Demande de réparation créée avec succès.');
    }

    public function show(RepairRequest $repairRequest): View
    {
        $repairRequest->load(['vehicle', 'requester', 'supervisor', 'manager', 'assignedSupplier']);

        return view('admin.repair-requests.show', compact('repairRequest'));
    }

    public function edit(RepairRequest $repairRequest): View
    {
        // Seules les demandes en attente peuvent être modifiées
        if ($repairRequest->status !== RepairRequest::STATUS_PENDING) {
            abort(403, 'Cette demande ne peut plus être modifiée.');
        }

        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
                          ->orderBy('registration_plate')
                          ->get();

        return view('admin.repair-requests.edit', compact('repairRequest', 'vehicles'));
    }

    public function update(UpdateRepairRequestRequest $request, RepairRequest $repairRequest): RedirectResponse
    {
        if ($repairRequest->status !== RepairRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $data = $request->validated();

        // Gérer l'upload des nouvelles photos
        if ($request->hasFile('photos')) {
            // Supprimer les anciennes photos
            if ($repairRequest->photos) {
                foreach ($repairRequest->photos as $photo) {
                    Storage::disk('public')->delete($photo);
                }
            }

            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('repair-requests/photos', 'public');
                $photos[] = $path;
            }
            $data['photos'] = $photos;
        }

        $repairRequest->update($data);

        return redirect()->route('admin.repair-requests.show', $repairRequest)
                        ->with('success', 'Demande de réparation mise à jour avec succès.');
    }

    public function destroy(RepairRequest $repairRequest): RedirectResponse
    {
        if (!in_array($repairRequest->status, [RepairRequest::STATUS_PENDING, RepairRequest::STATUS_REJECTED])) {
            return redirect()->back()->with('error', 'Cette demande ne peut pas être supprimée.');
        }

        // Supprimer les fichiers associés
        if ($repairRequest->photos) {
            foreach ($repairRequest->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        if ($repairRequest->attachments) {
            foreach ($repairRequest->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $repairRequest->delete();

        return redirect()->route('admin.repair-requests.index')
                        ->with('success', 'Demande de réparation supprimée avec succès.');
    }

    // Méthodes de workflow

    public function approve(ApprovalDecisionRequest $request, RepairRequest $repairRequest): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$repairRequest->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à approuver cette demande.'
                ], 403);
            }

            $success = $repairRequest->approveBySupervisor($user, $request->get('comments'));

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Demande approuvée avec succès.' : 'Erreur lors de l\'approbation.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(ApprovalDecisionRequest $request, RepairRequest $repairRequest): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$repairRequest->canBeApprovedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à rejeter cette demande.'
                ], 403);
            }

            $comments = $request->get('comments');
            if (empty($comments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un commentaire est requis pour rejeter une demande.'
                ], 422);
            }

            $success = $repairRequest->rejectBySupervisor($user, $comments);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Demande rejetée.' : 'Erreur lors du rejet.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateRepairRequest(ApprovalDecisionRequest $request, RepairRequest $repairRequest): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$repairRequest->canBeValidatedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à valider cette demande.'
                ], 403);
            }

            $success = $repairRequest->validateByManager($user, $request->get('comments'));

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Demande validée avec succès.' : 'Erreur lors de la validation.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectByManager(ApprovalDecisionRequest $request, RepairRequest $repairRequest): JsonResponse
    {
        try {
            $user = Auth::user();

            if (!$repairRequest->canBeValidatedBy($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à rejeter cette demande.'
                ], 403);
            }

            $comments = $request->get('comments');
            if (empty($comments)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un commentaire est requis pour rejeter une demande.'
                ], 422);
            }

            $success = $repairRequest->rejectByManager($user, $comments);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Demande rejetée par le manager.' : 'Erreur lors du rejet.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignSupplier(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        try {
            $success = $repairRequest->assignToSupplier($request->get('supplier_id'));

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Fournisseur assigné avec succès.' : 'Erreur lors de l\'assignation.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function startWork(RepairRequest $repairRequest): JsonResponse
    {
        try {
            $success = $repairRequest->startWork();

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Travaux démarrés.' : 'Erreur lors du démarrage.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeWork(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        $request->validate([
            'actual_cost' => 'required|numeric|min:0',
            'completion_notes' => 'nullable|string|max:1000',
            'final_rating' => 'nullable|numeric|between:1,10',
            'work_photos' => 'nullable|array',
            'work_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $workPhotos = null;
            if ($request->hasFile('work_photos')) {
                $workPhotos = [];
                foreach ($request->file('work_photos') as $photo) {
                    $path = $photo->store('repair-requests/work-photos', 'public');
                    $workPhotos[] = $path;
                }
            }

            $success = $repairRequest->completeWork(
                $request->get('actual_cost'),
                $request->get('completion_notes'),
                $workPhotos,
                $request->get('final_rating')
            );

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Travaux complétés avec succès.' : 'Erreur lors de la complétion.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, RepairRequest $repairRequest): JsonResponse
    {
        try {
            $success = $repairRequest->cancel($request->get('reason'));

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Demande annulée.' : 'Erreur lors de l\'annulation.',
                'status' => $repairRequest->fresh()->status_label
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    // Dashboard et statistiques
    public function dashboard(): View
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Statistiques générales
        $stats = $this->getRepairStats();

        // Demandes récentes
        $recentRequests = RepairRequest::with(['vehicle', 'requester'])
                                     ->forOrganization($organizationId)
                                     ->latest('requested_at')
                                     ->limit(5)
                                     ->get();

        // Demandes en attente de traitement selon le rôle
        $pendingRequests = collect();
        if ($user->hasRole('supervisor')) {
            $pendingRequests = RepairRequest::awaitingSupervisorApproval()
                                          ->forOrganization($organizationId)
                                          ->with(['vehicle', 'requester'])
                                          ->get();
        } elseif ($user->hasRole('fleet_manager')) {
            $pendingRequests = RepairRequest::awaitingManagerValidation()
                                          ->forOrganization($organizationId)
                                          ->with(['vehicle', 'requester'])
                                          ->get();
        }

        // Coûts par mois (6 derniers mois)
        $monthlyCosts = $this->getMonthlyCosts($organizationId);

        return view('admin.repair-requests.dashboard', compact(
            'stats',
            'recentRequests',
            'pendingRequests',
            'monthlyCosts'
        ));
    }

    // Méthodes utilitaires privées

    private function getRepairStats(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'total' => RepairRequest::forOrganization($organizationId)->count(),
            'pending' => RepairRequest::pending()->forOrganization($organizationId)->count(),
            'urgent' => RepairRequest::urgent()->forOrganization($organizationId)->count(),
            'in_progress' => RepairRequest::inProgress()->forOrganization($organizationId)->count(),
            'completed_this_month' => RepairRequest::completed()
                                               ->forOrganization($organizationId)
                                               ->whereMonth('work_completed_at', now()->month)
                                               ->count(),
            'avg_cost' => RepairRequest::completed()
                                     ->forOrganization($organizationId)
                                     ->whereNotNull('actual_cost')
                                     ->avg('actual_cost') ?? 0,
            'total_cost_this_year' => RepairRequest::completed()
                                               ->forOrganization($organizationId)
                                               ->whereYear('work_completed_at', now()->year)
                                               ->sum('actual_cost') ?? 0
        ];
    }

    private function getMonthlyCosts(int $organizationId): array
    {
        $costs = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $costs[$date->format('Y-m')] = RepairRequest::completed()
                                                      ->forOrganization($organizationId)
                                                      ->whereYear('work_completed_at', $date->year)
                                                      ->whereMonth('work_completed_at', $date->month)
                                                      ->sum('actual_cost') ?? 0;
        }
        return $costs;
    }
}