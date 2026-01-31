<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

/**
 * Composant Livewire pour la gestion de la planification des maintenances
 * Interface enterprise-grade avec calendrier interactif et gestion des récurrences
 */
class ScheduleManager extends Component
{
    use WithPagination;

    // Propriétés de filtrage et recherche
    public string $search = '';
    public string $statusFilter = 'all';
    public string $vehicleFilter = 'all';
    public string $typeFilter = 'all';
    public string $sortBy = 'urgency';
    public string $sortDirection = 'asc';
    public int $perPage = 15;

    // Propriétés pour le modal de création/édition
    public bool $showModal = false;
    public bool $editMode = false;
    public ?int $scheduleId = null;

    // Propriétés du formulaire
    public int $vehicle_id = 0;
    public int $maintenance_type_id = 0;
    public string $next_due_date = '';
    public string $next_due_mileage = '';
    public string $interval_km = '';
    public string $interval_days = '';
    public int $alert_km_before = 1000;
    public int $alert_days_before = 7;
    public bool $is_active = true;

    // Propriétés pour la vue calendrier
    public string $viewMode = 'list'; // 'list' ou 'calendar'
    public string $currentMonth = '';

    // Propriétés pour les actions en lot
    public array $selectedSchedules = [];
    public bool $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'vehicleFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'urgency'],
        'viewMode' => ['except' => 'list'],
        'perPage' => ['except' => 15],
    ];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', Rule::exists('vehicles', 'id')],
            'maintenance_type_id' => ['required', 'integer', Rule::exists('maintenance_types', 'id')],
            'next_due_date' => ['nullable', 'date', 'after:today'],
            'next_due_mileage' => ['nullable', 'integer', 'min:0'],
            'interval_km' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'interval_days' => ['nullable', 'integer', 'min:1', 'max:3650'],
            'alert_km_before' => ['integer', 'min:0', 'max:50000'],
            'alert_days_before' => ['integer', 'min:0', 'max:365'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return MaintenanceSchedule::validationMessages();
    }

    /**
     * Computed property pour les planifications avec pagination et filtres
     */
    #[Computed]
    public function schedules()
    {
        return $this->getSchedulesQuery()->paginate($this->perPage);
    }

    /**
     * Computed property pour les véhicules disponibles
     */
    #[Computed]
    public function vehicles()
    {
        return Vehicle::active()
            ->orderBy('registration_plate')
            ->get(['id', 'registration_plate', 'brand', 'model']);
    }

    /**
     * Computed property pour les types de maintenance
     */
    #[Computed]
    public function maintenanceTypes()
    {
        return MaintenanceType::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'is_recurring', 'default_interval_km', 'default_interval_days']);
    }

    /**
     * Computed property pour les statistiques du tableau de bord
     */
    #[Computed]
    public function stats()
    {
        $query = MaintenanceSchedule::active();

        return [
            'total' => $query->count(),
            'overdue' => $query->overdue()->count(),
            'due_soon' => $query->needingAlert()->count(),
            'scheduled' => $query->where('next_due_date', '>', Carbon::today()->addDays(7))->count(),
        ];
    }

    /**
     * Méthode pour construire la requête des planifications avec filtres
     */
    private function getSchedulesQuery(): Builder
    {
        $query = MaintenanceSchedule::with(['vehicle:id,registration_plate,brand,model', 'maintenanceType:id,name,category'])
            ->when($this->search, function ($q) {
                $q->whereHas('vehicle', function ($vehicleQuery) {
                    $vehicleQuery->where('registration_plate', 'ilike', "%{$this->search}%")
                        ->orWhere('brand', 'ilike', "%{$this->search}%")
                        ->orWhere('model', 'ilike', "%{$this->search}%");
                })->orWhereHas('maintenanceType', function ($typeQuery) {
                    $typeQuery->where('name', 'ilike', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                switch ($this->statusFilter) {
                    case 'overdue':
                        $q->overdue();
                        break;
                    case 'due_soon':
                        $q->needingAlert();
                        break;
                    case 'scheduled':
                        $q->where('next_due_date', '>', Carbon::today()->addDays(7));
                        break;
                    case 'inactive':
                        $q->where('is_active', false);
                        break;
                    default:
                        $q->active();
                }
            })
            ->when($this->vehicleFilter !== 'all', function ($q) {
                $q->where('vehicle_id', $this->vehicleFilter);
            })
            ->when($this->typeFilter !== 'all', function ($q) {
                $q->where('maintenance_type_id', $this->typeFilter);
            });

        // Tri
        switch ($this->sortBy) {
            case 'urgency':
                $query->orderByUrgency();
                break;
            case 'vehicle':
                $query->join('vehicles', 'maintenance_schedules.vehicle_id', '=', 'vehicles.id')
                    ->orderBy('vehicles.registration_plate', $this->sortDirection)
                    ->select('maintenance_schedules.*');
                break;
            case 'type':
                $query->join('maintenance_types', 'maintenance_schedules.maintenance_type_id', '=', 'maintenance_types.id')
                    ->orderBy('maintenance_types.name', $this->sortDirection)
                    ->select('maintenance_schedules.*');
                break;
            case 'due_date':
                $query->orderBy('next_due_date', $this->sortDirection);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Méthode pour ouvrir le modal de création
     */
    public function create(): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Méthode pour éditer une planification
     */
    public function edit(int $scheduleId): void
    {
        $schedule = MaintenanceSchedule::findOrFail($scheduleId);

        $this->scheduleId = $schedule->id;
        $this->vehicle_id = $schedule->vehicle_id;
        $this->maintenance_type_id = $schedule->maintenance_type_id;
        $this->next_due_date = $schedule->next_due_date?->format('Y-m-d') ?? '';
        $this->next_due_mileage = (string) ($schedule->next_due_mileage ?? '');
        $this->interval_km = (string) ($schedule->interval_km ?? '');
        $this->interval_days = (string) ($schedule->interval_days ?? '');
        $this->alert_km_before = $schedule->alert_km_before;
        $this->alert_days_before = $schedule->alert_days_before;
        $this->is_active = $schedule->is_active;

        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Méthode pour sauvegarder une planification
     */
    public function save(): void
    {
        $this->validate();

        $data = [
            'vehicle_id' => $this->vehicle_id,
            'maintenance_type_id' => $this->maintenance_type_id,
            'next_due_date' => $this->next_due_date ?: null,
            'next_due_mileage' => $this->next_due_mileage ? (int) $this->next_due_mileage : null,
            'interval_km' => $this->interval_km ? (int) $this->interval_km : null,
            'interval_days' => $this->interval_days ? (int) $this->interval_days : null,
            'alert_km_before' => $this->alert_km_before,
            'alert_days_before' => $this->alert_days_before,
            'is_active' => $this->is_active,
        ];

        if ($this->editMode) {
            $schedule = MaintenanceSchedule::findOrFail($this->scheduleId);
            $schedule->update($data);
            $message = 'Planification mise à jour avec succès.';
        } else {
            $data['organization_id'] = auth()->user()->organization_id;
            MaintenanceSchedule::create($data);
            $message = 'Planification créée avec succès.';
        }

        $this->showModal = false;
        $this->resetForm();
        $this->resetPage();

        session()->flash('success', $message);
        $this->dispatch('schedule-saved');
    }

    /**
     * Méthode pour supprimer une planification
     */
    public function delete(int $scheduleId): void
    {
        $schedule = MaintenanceSchedule::findOrFail($scheduleId);
        $schedule->delete();

        session()->flash('success', 'Planification supprimée avec succès.');
        $this->resetPage();
    }

    /**
     * Méthode pour basculer le statut actif/inactif
     */
    public function toggleStatus(int $scheduleId): void
    {
        $schedule = MaintenanceSchedule::findOrFail($scheduleId);
        $schedule->update(['is_active' => !$schedule->is_active]);

        $status = $schedule->is_active ? 'activée' : 'désactivée';
        session()->flash('success', "Planification {$status} avec succès.");
    }

    /**
     * Méthode pour créer une alerte manuelle
     */
    public function createAlert(int $scheduleId): void
    {
        $schedule = MaintenanceSchedule::findOrFail($scheduleId);
        $alert = $schedule->createAlertIfNeeded();

        if ($alert) {
            session()->flash('success', 'Alerte créée avec succès.');
        } else {
            session()->flash('info', 'Une alerte existe déjà pour cette planification.');
        }
    }

    /**
     * Méthode pour charger les valeurs par défaut du type de maintenance
     */
    #[On('maintenance-type-changed')]
    public function loadMaintenanceTypeDefaults(): void
    {
        if ($this->maintenance_type_id) {
            $type = MaintenanceType::find($this->maintenance_type_id);
            if ($type) {
                $this->interval_km = (string) ($type->default_interval_km ?? '');
                $this->interval_days = (string) ($type->default_interval_days ?? '');
            }
        }
    }

    /**
     * Méthode pour changer le mode de vue
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    /**
     * Méthode pour naviguer dans le calendrier
     */
    public function navigateMonth(string $direction): void
    {
        $currentDate = Carbon::createFromFormat('Y-m', $this->currentMonth);

        if ($direction === 'next') {
            $this->currentMonth = $currentDate->addMonth()->format('Y-m');
        } else {
            $this->currentMonth = $currentDate->subMonth()->format('Y-m');
        }
    }

    /**
     * Méthode pour sélectionner/désélectionner toutes les planifications
     */
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedSchedules = $this->schedules->pluck('id')->toArray();
        } else {
            $this->selectedSchedules = [];
        }
    }

    /**
     * Méthode pour les actions en lot
     */
    public function bulkAction(string $action): void
    {
        if (empty($this->selectedSchedules)) {
            session()->flash('error', 'Aucune planification sélectionnée.');
            return;
        }

        $schedules = MaintenanceSchedule::whereIn('id', $this->selectedSchedules);
        $count = count($this->selectedSchedules);

        switch ($action) {
            case 'activate':
                $schedules->update(['is_active' => true]);
                session()->flash('success', "{$count} planification(s) activée(s) avec succès.");
                break;

            case 'deactivate':
                $schedules->update(['is_active' => false]);
                session()->flash('success', "{$count} planification(s) désactivée(s) avec succès.");
                break;

            case 'delete':
                $schedules->delete();
                session()->flash('success', "{$count} planification(s) supprimée(s) avec succès.");
                break;

            case 'create_alerts':
                $alertsCreated = 0;
                foreach ($schedules->get() as $schedule) {
                    if ($schedule->createAlertIfNeeded()) {
                        $alertsCreated++;
                    }
                }
                session()->flash('success', "{$alertsCreated} alerte(s) créée(s) avec succès.");
                break;
        }

        $this->selectedSchedules = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    /**
     * Méthode pour réinitialiser le formulaire
     */
    private function resetForm(): void
    {
        $this->scheduleId = null;
        $this->vehicle_id = 0;
        $this->maintenance_type_id = 0;
        $this->next_due_date = '';
        $this->next_due_mileage = '';
        $this->interval_km = '';
        $this->interval_days = '';
        $this->alert_km_before = 1000;
        $this->alert_days_before = 7;
        $this->is_active = true;
        $this->resetValidation();
    }

    /**
     * Méthode pour fermer le modal
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Mise à jour des filtres avec réinitialisation de la pagination
     */
    public function updatedSearch(): void
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

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Méthode pour exporter les planifications
     */
    public function export(): void
    {
        $this->dispatch('export-schedules', [
            'filters' => [
                'search' => $this->search,
                'status' => $this->statusFilter,
                'vehicle' => $this->vehicleFilter,
                'type' => $this->typeFilter,
            ]
        ]);
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.maintenance.schedule-manager', [
            'schedules' => $this->schedules,
            'vehicles' => $this->vehicles,
            'maintenanceTypes' => $this->maintenanceTypes,
            'stats' => $this->stats,
        ]);
    }
}
