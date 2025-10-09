<?php

namespace App\Livewire;

use App\Models\RepairRequest;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\RepairCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
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
    
    /**
     * 📊 PROPRIÉTÉS DE TRI ET AFFICHAGE
     */
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 20;
    public array $selectedRequests = [];
    public bool $selectAll = false;
    
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
        if ($user->can('view all repair requests')) {
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
     * 🔐 APPLICATION DES SCOPES SELON LE RÔLE
     */
    private function applyScopesByRole($query)
    {
        $user = auth()->user();
        
        if ($user->hasRole('Chauffeur')) {
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('Supervisor')) {
            $query->where(function($q) use ($user) {
                $q->whereHas('driver', function ($subQ) use ($user) {
                    $subQ->where('supervisor_id', $user->id);
                })->orWhere('supervisor_id', $user->id);
            });
        } elseif ($user->hasRole('Chef de parc')) {
            if ($user->depot_id) {
                $query->where('depot_id', $user->depot_id);
            }
        }
        
        return $query;
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
     */
    public function getVehiclesProperty()
    {
        $user = auth()->user();
        $query = Vehicle::where('organization_id', $user->organization_id)
            ->where('status', 'active');
        
        if ($user->hasRole('Chauffeur')) {
            $query->whereHas('assignments', function($q) use ($user) {
                $q->where('driver_id', function($subQ) use ($user) {
                    $subQ->select('id')
                        ->from('drivers')
                        ->where('user_id', $user->id);
                })->where('is_active', true);
            });
        }
        
        return $query->orderBy('registration_plate')->get();
    }

    /**
     * 📤 EXPORT DES DONNÉES
     */
    public function exportData(string $format = 'csv'): void
    {
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
        foreach ($this->selectedRequests as $requestId) {
            $request = RepairRequest::find($requestId);
            if ($request && auth()->user()->can('approve', $request)) {
                $request->approve(auth()->user());
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
     * 🎨 RENDU DU COMPOSANT
     */
    public function render(): View
    {
        return view('livewire.repair-requests-index', [
            'repairRequests' => $this->repairRequests,
            'statuses' => $this->statuses,
            'urgencyLevels' => $this->urgencyLevels,
            'categories' => $this->categories,
            'vehicles' => $this->vehicles,
            'statistics' => $this->statistics,
        ]);
    }
}
