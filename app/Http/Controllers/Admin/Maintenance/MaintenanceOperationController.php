<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Services\Maintenance\MaintenanceService;
use App\Models\MaintenanceOperation;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use App\Models\MaintenanceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * 🔧 CONTROLLER OPÉRATIONS MAINTENANCE
 * 
 * Controller slim pattern - Délègue logique au service
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceOperationController extends Controller
{
    protected MaintenanceService $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
        $this->middleware('auth');
    }

    /**
     * Vue liste des opérations
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', MaintenanceOperation::class);

        // Récupérer les opérations avec filtres
        $operations = $this->maintenanceService->getOperations(
            $request->all(),
            $request->input('per_page', 15)
        );

        // Récupérer les analytics
        $analytics = $this->maintenanceService->getAnalytics($request->only('period'));

        // Données pour les filtres
        $vehicles = Vehicle::select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        // CORRECTION: Suppression de la colonne 'color' inexistante
        // Les couleurs sont générées dynamiquement basées sur 'category'
        $maintenanceTypes = MaintenanceType::select('id', 'name', 'category')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $providers = MaintenanceProvider::select('id', 'name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.maintenance.operations.index', compact(
            'operations',
            'analytics',
            'vehicles',
            'maintenanceTypes',
            'providers'
        ));
    }

    /**
     * 🚀 Affiche le formulaire de création - ENTERPRISE EDITION V7 - LIVEWIRE
     *
     * Nouvelle architecture utilisant Livewire pour une gestion d'état optimale:
     * - Composant Livewire MaintenanceOperationCreate
     * - Chargement des données par le composant (vehicles, types, providers)
     * - Validation temps réel côté serveur
     * - Auto-complétion intelligente
     * - UX enterprise-grade
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @version 7.0 - Refactorisation complète vers Livewire
     * @since 2025-11-23
     * @author ZenFleet Architecture Team - Expert Système Senior
     */
    public function create()
    {
        // L'autorisation est gérée dans le composant Livewire
        // Retourner simplement la vue wrapper
        return view('admin.maintenance.operations.create');
    }

    /**
     * Enregistrer nouvelle opération
     */
    public function store(Request $request)
    {
        Gate::authorize('create', MaintenanceOperation::class);

        $validated = $request->validate(MaintenanceOperation::validationRules(), MaintenanceOperation::validationMessages());

        try {
            $operation = $this->maintenanceService->createOperation($validated);

            return redirect()
                ->route('admin.maintenance.operations.show', $operation)
                ->with('success', 'Opération de maintenance créée avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Afficher détails opération
     */
    public function show(MaintenanceOperation $operation)
    {
        Gate::authorize('view', $operation);

        $operation->load([
            'vehicle',
            'maintenanceType',
            'provider',
            'schedule',
            'documents',
            'creator',
            'updater'
        ]);

        return view('admin.maintenance.operations.show', compact('operation'));
    }

    /**
     * Formulaire édition
     */
    public function edit(MaintenanceOperation $operation)
    {
        Gate::authorize('update', $operation);

        $operation->load(['vehicle', 'maintenanceType', 'provider']);

        $vehicles = Vehicle::select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        $maintenanceTypes = MaintenanceType::select('id', 'name', 'category')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $providers = MaintenanceProvider::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.maintenance.operations.edit', compact(
            'operation',
            'vehicles',
            'maintenanceTypes',
            'providers'
        ));
    }

    /**
     * Mettre à jour opération
     */
    public function update(Request $request, MaintenanceOperation $operation)
    {
        Gate::authorize('update', $operation);

        $validated = $request->validate(MaintenanceOperation::validationRules(), MaintenanceOperation::validationMessages());

        try {
            $operation = $this->maintenanceService->updateOperation($operation, $validated);

            return redirect()
                ->route('admin.maintenance.operations.show', $operation)
                ->with('success', 'Opération mise à jour avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer opération
     */
    public function destroy(MaintenanceOperation $operation)
    {
        Gate::authorize('delete', $operation);

        try {
            $this->maintenanceService->deleteOperation($operation);

            return redirect()
                ->route('admin.maintenance.operations.index')
                ->with('success', 'Opération supprimée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Démarrer une opération
     */
    public function start(MaintenanceOperation $operation)
    {
        Gate::authorize('update', $operation);

        try {
            $this->maintenanceService->startOperation($operation);

            return back()->with('success', 'Opération démarrée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Terminer une opération
     */
    public function complete(Request $request, MaintenanceOperation $operation)
    {
        Gate::authorize('update', $operation);

        $validated = $request->validate([
            'completed_date' => 'required|date',
            'mileage_at_maintenance' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        try {
            $this->maintenanceService->completeOperation($operation, $validated);

            return back()->with('success', 'Opération terminée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Annuler une opération
     */
    public function cancel(MaintenanceOperation $operation)
    {
        Gate::authorize('update', $operation);

        try {
            $this->maintenanceService->cancelOperation($operation);

            return back()->with('success', 'Opération annulée avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Vue Kanban
     */
    public function kanban()
    {
        Gate::authorize('viewAny', MaintenanceOperation::class);

        return view('admin.maintenance.operations.kanban');
    }

    /**
     * Vue Calendrier
     */
    public function calendar()
    {
        Gate::authorize('viewAny', MaintenanceOperation::class);

        return view('admin.maintenance.operations.calendar');
    }

    /**
     * Vue Timeline
     */
    public function timeline()
    {
        Gate::authorize('viewAny', MaintenanceOperation::class);

        // TODO: Créer vue timeline
        return view('admin.maintenance.operations.timeline');
    }

    /**
     * Export CSV
     */
    public function export(Request $request)
    {
        Gate::authorize('viewAny', MaintenanceOperation::class);

        // TODO: Implémenter export
        return response()->download(storage_path('app/exports/maintenance.csv'));
    }
}
