<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use App\Models\MaintenanceAlert;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Composant Livewire pour le tableau de bord des alertes de maintenance
 * Interface temps réel avec filtres avancés et actions en lot
 */
class AlertsDashboard extends Component
{
    use WithPagination;

    // Propriétés de filtrage
    public string $search = '';
    public string $priorityFilter = 'all';
    public string $typeFilter = 'all';
    public string $statusFilter = 'unacknowledged';
    public string $vehicleFilter = 'all';
    public string $dateFilter = 'all'; // all, today, week, month
    public string $sortBy = 'priority';
    public string $sortDirection = 'asc';

    // Propriétés pour les actions en lot
    public array $selectedAlerts = [];
    public bool $selectAll = false;

    // Propriétés pour les statistiques en temps réel
    public array $stats = [];
    public bool $autoRefresh = true;
    public int $refreshInterval = 30; // secondes

    // Propriétés pour les filtres avancés
    public bool $showAdvancedFilters = false;
    public string $escalationFilter = 'all';
    public int $ageFilter = 0; // heures

    protected $queryString = [
        'search' => ['except' => ''],
        'priorityFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'unacknowledged'],
        'vehicleFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'priority'],
    ];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $this->loadStats();
    }

    /**
     * Computed property pour les alertes avec pagination et filtres
     */
    #[Computed]
    public function alerts()
    {
        return $this->getAlertsQuery()->paginate(20);
    }

    /**
     * Computed property pour les véhicules avec alertes
     */
    #[Computed]
    public function vehiclesWithAlerts()
    {
        return Vehicle::whereHas('maintenanceAlerts', function ($query) {
            $query->unacknowledged();
        })->orderBy('registration_plate')->get(['id', 'registration_plate', 'brand', 'model']);
    }

    /**
     * Computed property pour les statistiques du tableau de bord
     */
    #[Computed]
    public function dashboardStats()
    {
        $baseQuery = MaintenanceAlert::query();

        return [
            'total_alerts' => $baseQuery->count(),
            'unacknowledged' => $baseQuery->unacknowledged()->count(),
            'critical' => $baseQuery->unacknowledged()->where('priority', 'critical')->count(),
            'high_priority' => $baseQuery->unacknowledged()->highPriority()->count(),
            'overdue_alerts' => $baseQuery->unacknowledged()->where('alert_type', 'overdue')->count(),
            'today_alerts' => $baseQuery->whereDate('created_at', today())->count(),
            'escalation_needed' => $this->getEscalationCount(),
            'avg_response_time' => $this->getAverageResponseTime(),
        ];
    }

    /**
     * Méthode pour construire la requête des alertes avec filtres
     */
    private function getAlertsQuery(): Builder
    {
        $query = MaintenanceAlert::with([
            'vehicle:id,registration_plate,brand,model',
            'schedule.maintenanceType:id,name,category',
            'acknowledgedBy:id,name'
        ])
        ->when($this->search, function ($q) {
            $q->where('message', 'ilike', "%{$this->search}%")
              ->orWhereHas('vehicle', function ($vehicleQuery) {
                  $vehicleQuery->where('registration_plate', 'ilike', "%{$this->search}%")
                      ->orWhere('brand', 'ilike', "%{$this->search}%")
                      ->orWhere('model', 'ilike', "%{$this->search}%");
              });
        })
        ->when($this->priorityFilter !== 'all', function ($q) {
            $q->byPriority($this->priorityFilter);
        })
        ->when($this->typeFilter !== 'all', function ($q) {
            $q->byType($this->typeFilter);
        })
        ->when($this->statusFilter !== 'all', function ($q) {
            if ($this->statusFilter === 'acknowledged') {
                $q->acknowledged();
            } else {
                $q->unacknowledged();
            }
        })
        ->when($this->vehicleFilter !== 'all', function ($q) {
            $q->forVehicle($this->vehicleFilter);
        })
        ->when($this->dateFilter !== 'all', function ($q) {
            switch ($this->dateFilter) {
                case 'today':
                    $q->whereDate('created_at', today());
                    break;
                case 'week':
                    $q->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $q->where('created_at', '>=', now()->subMonth());
                    break;
            }
        })
        ->when($this->escalationFilter === 'needed', function ($q) {
            $q->where(function ($subQuery) {
                $subQuery->where('priority', 'critical')
                    ->where('created_at', '<=', now()->subHours(2))
                    ->where('is_acknowledged', false);
            })->orWhere(function ($subQuery) {
                $subQuery->where('priority', 'high')
                    ->where('created_at', '<=', now()->subHours(8))
                    ->where('is_acknowledged', false);
            });
        })
        ->when($this->ageFilter > 0, function ($q) {
            $q->where('created_at', '<=', now()->subHours($this->ageFilter));
        });

        // Tri
        switch ($this->sortBy) {
            case 'priority':
                $query->orderByPriority();
                break;
            case 'vehicle':
                $query->join('vehicles', 'maintenance_alerts.vehicle_id', '=', 'vehicles.id')
                    ->orderBy('vehicles.registration_plate', $this->sortDirection)
                    ->select('maintenance_alerts.*');
                break;
            case 'created_at':
                $query->orderBy('created_at', $this->sortDirection);
                break;
            case 'due_date':
                $query->orderBy('due_date', $this->sortDirection);
                break;
            default:
                $query->orderByPriority();
        }

        return $query;
    }

    /**
     * Méthode pour charger les statistiques
     */
    public function loadStats(): void
    {
        $this->stats = $this->dashboardStats;
    }

    /**
     * Méthode pour acquitter une alerte
     */
    public function acknowledgeAlert(int $alertId): void
    {
        $alert = MaintenanceAlert::findOrFail($alertId);

        if ($alert->acknowledge()) {
            $this->loadStats();
            session()->flash('success', 'Alerte acquittée avec succès.');
            $this->dispatch('alert-acknowledged', ['alertId' => $alertId]);
        } else {
            session()->flash('error', 'Cette alerte est déjà acquittée.');
        }
    }

    /**
     * Méthode pour réactiver une alerte
     */
    public function unacknowledgeAlert(int $alertId): void
    {
        $alert = MaintenanceAlert::findOrFail($alertId);

        if ($alert->unacknowledge()) {
            $this->loadStats();
            session()->flash('success', 'Alerte réactivée avec succès.');
            $this->dispatch('alert-unacknowledged', ['alertId' => $alertId]);
        } else {
            session()->flash('error', 'Cette alerte n\'est pas acquittée.');
        }
    }

    /**
     * Méthode pour supprimer une alerte
     */
    public function deleteAlert(int $alertId): void
    {
        $alert = MaintenanceAlert::findOrFail($alertId);
        $alert->delete();

        $this->loadStats();
        session()->flash('success', 'Alerte supprimée avec succès.');
        $this->resetPage();
    }

    /**
     * Méthode pour créer une opération de maintenance depuis une alerte
     */
    public function createOperation(int $alertId): void
    {
        $alert = MaintenanceAlert::with(['schedule'])->findOrFail($alertId);

        $this->dispatch('create-operation-from-alert', [
            'alertId' => $alertId,
            'vehicleId' => $alert->vehicle_id,
            'scheduleId' => $alert->maintenance_schedule_id,
            'maintenanceTypeId' => $alert->schedule?->maintenance_type_id,
        ]);
    }

    /**
     * Méthode pour sélectionner/désélectionner toutes les alertes
     */
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedAlerts = $this->alerts->pluck('id')->toArray();
        } else {
            $this->selectedAlerts = [];
        }
    }

    /**
     * Méthode pour les actions en lot
     */
    public function bulkAction(string $action): void
    {
        if (empty($this->selectedAlerts)) {
            session()->flash('error', 'Aucune alerte sélectionnée.');
            return;
        }

        $alerts = MaintenanceAlert::whereIn('id', $this->selectedAlerts);
        $count = count($this->selectedAlerts);

        switch ($action) {
            case 'acknowledge':
                $alerts->where('is_acknowledged', false)
                    ->update([
                        'is_acknowledged' => true,
                        'acknowledged_by' => auth()->id(),
                        'acknowledged_at' => now(),
                    ]);
                session()->flash('success', "{$count} alerte(s) acquittée(s) avec succès.");
                break;

            case 'unacknowledge':
                $alerts->where('is_acknowledged', true)
                    ->update([
                        'is_acknowledged' => false,
                        'acknowledged_by' => null,
                        'acknowledged_at' => null,
                    ]);
                session()->flash('success', "{$count} alerte(s) réactivée(s) avec succès.");
                break;

            case 'delete':
                $alerts->delete();
                session()->flash('success', "{$count} alerte(s) supprimée(s) avec succès.");
                break;

            case 'set_priority':
                // Cette action nécessite une priorité supplémentaire
                $this->dispatch('bulk-set-priority', ['alertIds' => $this->selectedAlerts]);
                return;
        }

        $this->selectedAlerts = [];
        $this->selectAll = false;
        $this->loadStats();
        $this->resetPage();
    }

    /**
     * Méthode pour modifier la priorité en lot
     */
    #[On('priority-updated')]
    public function updatePriority(array $alertIds, string $priority): void
    {
        MaintenanceAlert::whereIn('id', $alertIds)
            ->update(['priority' => $priority]);

        $count = count($alertIds);
        session()->flash('success', "Priorité mise à jour pour {$count} alerte(s).");

        $this->selectedAlerts = [];
        $this->selectAll = false;
        $this->loadStats();
        $this->resetPage();
    }

    /**
     * Méthode pour basculer l'actualisation automatique
     */
    public function toggleAutoRefresh(): void
    {
        $this->autoRefresh = !$this->autoRefresh;

        if ($this->autoRefresh) {
            $this->dispatch('start-auto-refresh', ['interval' => $this->refreshInterval]);
        } else {
            $this->dispatch('stop-auto-refresh');
        }
    }

    /**
     * Méthode pour actualiser les données
     */
    #[On('refresh-alerts')]
    public function refreshData(): void
    {
        $this->loadStats();
        $this->resetPage();
        $this->dispatch('alerts-refreshed');
    }

    /**
     * Méthode pour basculer les filtres avancés
     */
    public function toggleAdvancedFilters(): void
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    /**
     * Méthode pour réinitialiser tous les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->priorityFilter = 'all';
        $this->typeFilter = 'all';
        $this->statusFilter = 'unacknowledged';
        $this->vehicleFilter = 'all';
        $this->dateFilter = 'all';
        $this->escalationFilter = 'all';
        $this->ageFilter = 0;
        $this->sortBy = 'priority';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    /**
     * Méthode pour exporter les alertes
     */
    public function export(): void
    {
        $this->dispatch('export-alerts', [
            'filters' => [
                'search' => $this->search,
                'priority' => $this->priorityFilter,
                'type' => $this->typeFilter,
                'status' => $this->statusFilter,
                'vehicle' => $this->vehicleFilter,
                'date' => $this->dateFilter,
            ]
        ]);
    }

    /**
     * Méthode pour obtenir le nombre d'alertes nécessitant une escalade
     */
    private function getEscalationCount(): int
    {
        return MaintenanceAlert::unacknowledged()
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('priority', 'critical')
                      ->where('created_at', '<=', now()->subHours(2));
                })->orWhere(function ($q) {
                    $q->where('priority', 'high')
                      ->where('created_at', '<=', now()->subHours(8));
                })->orWhere(function ($q) {
                    $q->where('priority', 'medium')
                      ->where('created_at', '<=', now()->subHours(24));
                });
            })
            ->count();
    }

    /**
     * Méthode pour obtenir le temps de réponse moyen
     */
    private function getAverageResponseTime(): float
    {
        return MaintenanceAlert::acknowledged()
            ->whereNotNull('acknowledged_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (acknowledged_at - created_at))/3600) as avg_hours')
            ->value('avg_hours') ?? 0;
    }

    /**
     * Mise à jour des filtres avec réinitialisation de la pagination
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedVehicleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.maintenance.alerts-dashboard', [
            'alerts' => $this->alerts,
            'vehiclesWithAlerts' => $this->vehiclesWithAlerts,
            'dashboardStats' => $this->dashboardStats,
        ]);
    }
}