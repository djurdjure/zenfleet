<?php

namespace App\Livewire;

use App\Models\RepairRequest;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Assignment;
use App\Models\RepairCategory;
use App\Services\RepairRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

/**
 * RepairRequestsIndex - Composant Enterprise-Grade pour la gestion des demandes de réparation
 * 
 * Features Enterprise:
 * - Filtrage avancé multi-critères avec Alpine.js
 * - Statistiques en temps réel
 * - Export des données (CSV, Excel, PDF)
 * - Tri intelligent multi-colonnes
 * - Actions groupées
 * - Interface responsive ultra-moderne
 * 
 * @version 2.0.0 - Enterprise Edition
 * @package App\Livewire
 */
class RepairRequestsIndex extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    /**
     * 🔍 PROPRIÉTÉS DE RECHERCHE ET FILTRAGE
     */
    public string $search = '';
    public string $statusFilter = '';
    public string $urgencyFilter = '';
    public string $categoryFilter = '';
    public string $vehicleFilter = '';
    public string $driverFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public bool $showFilters = false;
    public $vehicleOptions = [];
    public $driverOptions = [];
    
    /**
     * 📊 PROPRIÉTÉS DE TRI ET AFFICHAGE
     */
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 20;
    public array $selectedRequests = [];
    public bool $selectAll = false;

    /**
     * 🎯 DECISION WORKFLOW MODAL
     */
    public bool $showDecisionModal = false;
    public ?int $decisionRequestId = null;
    public string $decisionAction = 'approve';
    public string $decisionComment = '';
    
    /**
     * 📈 PROPRIÉTÉS STATISTIQUES
     */
    public array $statistics = [];
    
    /**
     * 🎛️ LISTENERS ÉVÉNEMENTS LIVEWIRE
     */
    protected $listeners = [
        'repair-request-created' => 'handleRequestCreated',
        'repair-request-updated' => 'handleRequestUpdated',
        'repair-request-deleted' => 'handleRequestDeleted',
        'refresh-statistics' => 'loadStatistics',
        'apply-bulk-action' => 'applyBulkAction',
    ];

    /**
     * 🔄 QUERY STRING POUR PERSISTENCE DES FILTRES
     */
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'urgencyFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'perPage' => ['except' => 20],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * 🚀 INITIALISATION DU COMPOSANT
     */
    public function mount(): void
    {
        $this->authorize('viewAny', RepairRequest::class);
        $this->loadStatistics();
        $this->loadFilterOptions();
    }

    /**
     * 📊 CHARGEMENT DES STATISTIQUES
     */
    public function loadStatistics(): void
    {
        $user = auth()->user();
        $baseQuery = RepairRequest::where('organization_id', $user->organization_id);
        
        // Application des scopes selon le rôle
        $baseQuery = $this->applyScopesByRole($baseQuery);
        
        $this->statistics = [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery->clone()->whereIn('status', [
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER
            ])->count(),
            'approved' => $baseQuery->clone()->whereIn('status', [
                RepairRequest::STATUS_APPROVED_SUPERVISOR,
                RepairRequest::STATUS_APPROVED_FINAL
            ])->count(),
            'rejected' => $baseQuery->clone()->whereIn('status', [
                RepairRequest::STATUS_REJECTED_SUPERVISOR,
                RepairRequest::STATUS_REJECTED_FINAL
            ])->count(),
            'critical' => $baseQuery->clone()->where('urgency', RepairRequest::URGENCY_CRITICAL)->count(),
            'high' => $baseQuery->clone()->where('urgency', RepairRequest::URGENCY_HIGH)->count(),
            'today' => $baseQuery->clone()->whereDate('created_at', Carbon::today())->count(),
            'week' => $baseQuery->clone()->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * 🔍 CHARGEMENT DES OPTIONS DE FILTRAGE
     */
    public function loadFilterOptions(): void
    {
        $user = auth()->user();
        
        // Chargement dynamique des options selon les permissions
        if ($user->can('repair-requests.view.all')) {
            $this->vehicleOptions = Vehicle::where('organization_id', $user->organization_id)
                ->orderBy('registration_plate')
                ->get();
            
            $this->driverOptions = Driver::whereHas('user', function($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })->with('user')->get();
        }
    }

    /**
     * 🔄 RESET PAGINATION LORS DU CHANGEMENT DE FILTRES
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingUrgencyFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    /**
     * 🔀 TRI DES COLONNES
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * 🔄 RÉINITIALISATION DES FILTRES
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'urgencyFilter',
            'categoryFilter',
            'vehicleFilter',
            'driverFilter',
            'dateFrom',
            'dateTo',
            'selectedRequests',
            'selectAll'
        ]);
        $this->resetPage();
        $this->loadStatistics();
    }

    /**
     * ✅ SÉLECTION DE TOUTES LES DEMANDES
     */
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedRequests = $this->repairRequests->pluck('id')->toArray();
        } else {
            $this->selectedRequests = [];
        }
    }

    /**
     * Ouvrir le modal de décision en mode approbation.
     */
    public function openApproveModal(int $requestId): void
    {
        $this->openDecisionModal($requestId, 'approve');
    }

    /**
     * Ouvrir le modal de décision en mode rejet.
     */
    public function openRejectModal(int $requestId): void
    {
        $this->openDecisionModal($requestId, 'reject');
    }

    /**
     * Fermer et réinitialiser le modal de décision.
     */
    public function closeDecisionModal(): void
    {
        $this->showDecisionModal = false;
        $this->decisionRequestId = null;
        $this->decisionAction = 'approve';
        $this->decisionComment = '';
        $this->resetErrorBag('decisionComment');
    }

    /**
     * Soumettre une décision d'approbation/rejet selon le niveau courant.
     */
    public function submitDecision(): void
    {
        $repairRequest = $this->resolveDecisionRequest();
        if (! $repairRequest) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Demande introuvable ou non accessible.'
            ]);
            $this->closeDecisionModal();
            return;
        }

        $user = auth()->user();
        $service = app(RepairRequestService::class);
        $comment = trim($this->decisionComment);

        if ($this->decisionAction === 'reject' && $comment === '') {
            $this->addError('decisionComment', 'La raison du rejet est obligatoire.');
            return;
        }

        try {
            $action = $this->decisionAction;

            if ($repairRequest->status === RepairRequest::STATUS_PENDING_SUPERVISOR) {
                if ($action === 'approve') {
                    $this->authorize('approveLevelOne', $repairRequest);
                    $service->approveBySupervisor($repairRequest, $user, $comment ?: null);
                } else {
                    $this->authorize('rejectLevelOne', $repairRequest);
                    $service->rejectBySupervisor($repairRequest, $user, $comment);
                }
            } elseif ($repairRequest->status === RepairRequest::STATUS_PENDING_FLEET_MANAGER) {
                if ($action === 'approve') {
                    $this->authorize('approveLevelTwo', $repairRequest);
                    $service->approveByFleetManager($repairRequest, $user, $comment ?: null);
                } else {
                    $this->authorize('rejectLevelTwo', $repairRequest);
                    $service->rejectByFleetManager($repairRequest, $user, $comment);
                }
            } else {
                throw new \RuntimeException('Cette demande n\'est plus en attente de validation.');
            }

            $this->closeDecisionModal();
            $this->loadStatistics();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $action === 'approve'
                    ? 'Décision enregistrée: demande approuvée.'
                    : 'Décision enregistrée: demande rejetée.'
            ]);
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Impossible d\'enregistrer la décision: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 🔐 APPLICATION DES SCOPES SELON LE RÔLE
     */
    private function applyScopesByRole($query)
    {
        $user = auth()->user();

        if ($user->can('repair-requests.view.all')) {
            return $query;
        }

        if ($user->can('repair-requests.view.team')) {
            if ($this->isSupervisorRole($user)) {
                $query->where(function($q) use ($user) {
                    $q->whereHas('driver', function ($subQ) use ($user) {
                        $subQ->where('supervisor_id', $user->id);
                    })->orWhere('supervisor_id', $user->id);
                });
            } elseif ($this->isFleetManagerRole($user) && $user->depot_id) {
                // Scope team by vehicle depot instead of a non-existent repair_requests.depot_id column.
                $query->whereHas('vehicle', function ($subQ) use ($user) {
                    $subQ->where('depot_id', $user->depot_id);
                });
            }

            return $query;
        }

        if ($user->can('repair-requests.view.own')) {
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * 📋 RÉCUPÉRATION DES DEMANDES AVEC FILTRES
     */
    public function getRepairRequestsProperty()
    {
        $user = auth()->user();
        
        $query = RepairRequest::with([
            'driver.user',
            'vehicle',
            'supervisor',
            'fleetManager',
            'category',
        ])
        ->where('organization_id', $user->organization_id);

        // Application des scopes selon le rôle
        $query = $this->applyScopesByRole($query);

        // 🔍 RECHERCHE GLOBALE
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', $search)
                    ->orWhere('description', 'ilike', $search)
                    ->orWhere('uuid', 'ilike', $search)
                    ->orWhereHas('driver.user', function ($q) use ($search) {
                        $q->where('name', 'ilike', $search);
                    })
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('registration_plate', 'ilike', $search)
                            ->orWhere('vehicle_name', 'ilike', $search);
                    });
            });
        }

        // 📊 FILTRES SPÉCIFIQUES
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->urgencyFilter)) {
            $query->where('urgency', $this->urgencyFilter);
        }

        if (!empty($this->categoryFilter)) {
            $query->where('category_id', $this->categoryFilter);
        }

        if (!empty($this->vehicleFilter)) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        if (!empty($this->driverFilter)) {
            $query->where('driver_id', $this->driverFilter);
        }

        // 📅 FILTRE PAR DATES
        if (!empty($this->dateFrom)) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if (!empty($this->dateTo)) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // 📊 TRI
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * 🎨 CONFIGURATION DES STATUTS
     */
    public function getStatusesProperty(): array
    {
        return [
            RepairRequest::STATUS_PENDING_SUPERVISOR => [
                'label' => 'En attente superviseur',
                'color' => 'yellow',
                'icon' => 'clock'
            ],
            RepairRequest::STATUS_APPROVED_SUPERVISOR => [
                'label' => 'Approuvé superviseur',
                'color' => 'blue',
                'icon' => 'check'
            ],
            RepairRequest::STATUS_REJECTED_SUPERVISOR => [
                'label' => 'Rejeté superviseur',
                'color' => 'red',
                'icon' => 'x-circle'
            ],
            RepairRequest::STATUS_PENDING_FLEET_MANAGER => [
                'label' => 'En attente gestionnaire',
                'color' => 'orange',
                'icon' => 'user-check'
            ],
            RepairRequest::STATUS_APPROVED_FINAL => [
                'label' => 'Approuvé final',
                'color' => 'green',
                'icon' => 'check-circle'
            ],
            RepairRequest::STATUS_REJECTED_FINAL => [
                'label' => 'Rejeté final',
                'color' => 'gray',
                'icon' => 'x-octagon'
            ],
        ];
    }

    /**
     * 🚨 NIVEAUX D'URGENCE
     */
    public function getUrgencyLevelsProperty(): array
    {
        return [
            RepairRequest::URGENCY_LOW => [
                'label' => 'Faible',
                'color' => 'green',
                'icon' => 'arrow-down'
            ],
            RepairRequest::URGENCY_NORMAL => [
                'label' => 'Normal',
                'color' => 'blue',
                'icon' => 'minus'
            ],
            RepairRequest::URGENCY_HIGH => [
                'label' => 'Élevé',
                'color' => 'orange',
                'icon' => 'arrow-up'
            ],
            RepairRequest::URGENCY_CRITICAL => [
                'label' => 'Critique',
                'color' => 'red',
                'icon' => 'alert-triangle'
            ],
        ];
    }

    /**
     * 📁 CATÉGORIES DE RÉPARATION
     */
    public function getCategoriesProperty()
    {
        return RepairCategory::where('organization_id', auth()->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * 🚗 VÉHICULES DISPONIBLES
     *
     * Récupère les véhicules actifs pour l'organisation courante
     * Si l'utilisateur est chauffeur, ne retourne que ses véhicules assignés
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesProperty()
    {
        $user = auth()->user();

        // Utilisation du scope 'active()' du modèle Vehicle (status_id = 1)
        $query = Vehicle::where('organization_id', $user->organization_id)
            ->active(); // REFACTORED: utilisation du Query Scope

        if ($this->isDriverRole($user)) {
            $driverId = Driver::query()
                ->where('organization_id', $user->organization_id)
                ->where('user_id', $user->id)
                ->value('id');

            if (! $driverId) {
                return collect();
            }

            $referenceTime = now();
            $query->whereHas('assignments', function($q) use ($driverId, $referenceTime) {
                $q->where('driver_id', $driverId)
                    ->where('status', '!=', Assignment::STATUS_CANCELLED)
                    ->where('start_datetime', '<=', $referenceTime)
                    ->where(function ($dateQuery) use ($referenceTime) {
                        $dateQuery->whereNull('end_datetime')
                            ->orWhere('end_datetime', '>=', $referenceTime);
                    });
            });
        }

        return $query->orderBy('registration_plate')->get();
    }

    /**
     * 📤 EXPORT DES DONNÉES
     */
    public function exportData(string $format = 'csv'): void
    {
        if (!auth()->user()->can('repair-requests.export')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Permission refusée pour exporter les demandes de réparation.'
            ]);
            return;
        }

        $this->dispatch('export-repair-requests', [
            'format' => $format,
            'filters' => [
                'search' => $this->search,
                'status' => $this->statusFilter,
                'urgency' => $this->urgencyFilter,
                'category' => $this->categoryFilter,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
            ]
        ]);
    }

    /**
     * 🔄 ACTIONS GROUPÉES
     */
    public function applyBulkAction(string $action): void
    {
        if (empty($this->selectedRequests)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'Veuillez sélectionner au moins une demande.'
            ]);
            return;
        }

        $user = auth()->user();
        $permissionsByAction = [
            'approve' => ['repair-requests.approve.level1', 'repair-requests.approve.level2', 'repair-requests.approve'],
            'reject' => ['repair-requests.reject.level1', 'repair-requests.reject.level2', 'repair-requests.reject'],
            'export' => ['repair-requests.export'],
            'delete' => ['repair-requests.delete'],
        ];
        $required = $permissionsByAction[$action] ?? [];
        if (!empty($required) && !collect($required)->some(fn($perm) => $user->can($perm))) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Permission refusée pour cette action.'
            ]);
            return;
        }

        switch ($action) {
            case 'approve':
                $this->bulkApprove();
                break;
            case 'reject':
                $this->bulkReject();
                break;
            case 'export':
                $this->bulkExport();
                break;
            case 'delete':
                $this->bulkDelete();
                break;
        }
    }

    /**
     * ✅ APPROBATION GROUPÉE
     */
    private function bulkApprove(): void
    {
        $count = 0;
        $service = app(RepairRequestService::class);
        $user = auth()->user();

        foreach ($this->selectedRequests as $requestId) {
            $request = RepairRequest::find($requestId);
            if (!$request) {
                continue;
            }

            $approved = false;
            if ($request->status === RepairRequest::STATUS_PENDING_SUPERVISOR && $user->can('approveLevelOne', $request)) {
                $service->approveBySupervisor($request, $user, 'Approuvé en masse');
                $approved = true;
            } elseif ($request->status === RepairRequest::STATUS_PENDING_FLEET_MANAGER && $user->can('approveLevelTwo', $request)) {
                $service->approveByFleetManager($request, $user, 'Approuvé en masse');
                $approved = true;
            }

            if ($approved) {
                $count++;
            }
        }
        
        $this->selectedRequests = [];
        $this->selectAll = false;
        $this->loadStatistics();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} demande(s) approuvée(s) avec succès."
        ]);
    }

    /**
     * ❌ REJET GROUPÉ
     */
    private function bulkReject(): void
    {
        $count = 0;
        $service = app(RepairRequestService::class);
        $user = auth()->user();

        foreach ($this->selectedRequests as $requestId) {
            $request = RepairRequest::find($requestId);
            if (!$request) {
                continue;
            }

            $rejected = false;
            if ($request->status === RepairRequest::STATUS_PENDING_SUPERVISOR && $user->can('rejectLevelOne', $request)) {
                $service->rejectBySupervisor($request, $user, 'Rejet en masse');
                $rejected = true;
            } elseif ($request->status === RepairRequest::STATUS_PENDING_FLEET_MANAGER && $user->can('rejectLevelTwo', $request)) {
                $service->rejectByFleetManager($request, $user, 'Rejet en masse');
                $rejected = true;
            }

            if ($rejected) {
                $count++;
            }
        }

        $this->selectedRequests = [];
        $this->selectAll = false;
        $this->loadStatistics();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} demande(s) rejetée(s) avec succès."
        ]);
    }

    /**
     * 📤 EXPORT GROUPÉ
     */
    private function bulkExport(): void
    {
        if (!auth()->user()->can('repair-requests.export')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Permission refusée pour exporter les demandes.'
            ]);
            return;
        }

        $this->dispatch('export-repair-requests', [
            'format' => 'csv',
            'filters' => [
                'ids' => $this->selectedRequests,
                'search' => $this->search,
                'status' => $this->statusFilter,
                'urgency' => $this->urgencyFilter,
                'category' => $this->categoryFilter,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
            ]
        ]);
    }

    /**
     * 🗑️ SUPPRESSION GROUPÉE
     */
    private function bulkDelete(): void
    {
        $count = 0;
        foreach ($this->selectedRequests as $requestId) {
            $request = RepairRequest::find($requestId);
            if ($request && auth()->user()->can('delete', $request)) {
                $request->delete();
                $count++;
            }
        }

        $this->selectedRequests = [];
        $this->selectAll = false;
        $this->loadStatistics();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$count} demande(s) supprimée(s) avec succès."
        ]);
    }

    /**
     * Role alias helper: Driver in FR/EN naming.
     */
    private function isDriverRole($user): bool
    {
        return $user->isDriverOnly();
    }

    /**
     * Role alias helper: Supervisor in FR/EN naming.
     */
    private function isSupervisorRole($user): bool
    {
        return $user->hasAnyRole(['Supervisor', 'Superviseur']);
    }

    /**
     * Role alias helper: Fleet manager in FR/EN naming.
     */
    private function isFleetManagerRole($user): bool
    {
        return $user->hasAnyRole(['Fleet Manager', 'Gestionnaire Flotte', 'Chef de parc']);
    }

    /**
     * Rafraîchit la vue après création.
     */
    public function handleRequestCreated(): void
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    /**
     * Rafraîchit la vue après mise à jour.
     */
    public function handleRequestUpdated(): void
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    /**
     * Rafraîchit la vue après suppression.
     */
    public function handleRequestDeleted(): void
    {
        $this->resetPage();
        $this->loadStatistics();
    }

    /**
     * Initialise le modal de décision.
     */
    private function openDecisionModal(int $requestId, string $action): void
    {
        $repairRequest = $this->resolveDecisionRequest($requestId);
        if (! $repairRequest) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Demande introuvable ou non accessible.'
            ]);
            return;
        }

        $this->decisionRequestId = $requestId;
        $this->decisionAction = $action;
        $this->decisionComment = '';
        $this->showDecisionModal = true;
    }

    /**
     * Charge la demande de décision en respectant l'isolation org/role.
     */
    private function resolveDecisionRequest(?int $requestId = null): ?RepairRequest
    {
        $user = auth()->user();
        $id = $requestId ?? $this->decisionRequestId;
        if (! $id) {
            return null;
        }

        $query = RepairRequest::query()
            ->where('organization_id', $user->organization_id)
            ->whereKey($id);

        $query = $this->applyScopesByRole($query);

        return $query->first();
    }

    /**
     * Expose la demande courante du modal à la vue.
     */
    public function getDecisionRequestProperty(): ?RepairRequest
    {
        return $this->resolveDecisionRequest();
    }

    /**
     * 🎨 RENDU DU COMPOSANT
     */
    public function render(): View
    {
        $this->authorize('viewAny', RepairRequest::class);

        return view('livewire.repair-requests-index', [
            'repairRequests' => $this->repairRequests,
            'statuses' => $this->statuses,
            'urgencyLevels' => $this->urgencyLevels,
            'categories' => $this->categories,
            'vehicles' => $this->vehicles,
            'decisionRequest' => $this->decisionRequest,
            'statistics' => $this->statistics,
        ]);
    }
}
