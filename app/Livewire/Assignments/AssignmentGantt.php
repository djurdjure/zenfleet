<?php

namespace App\Livewire\Assignments;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\AssignmentOverlapService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 📈 Composant Vue Gantt des Affectations
 *
 * Fonctionnalités:
 * - Diagramme de Gantt interactif
 * - Regroupement par véhicule ou chauffeur
 * - Zoom temporel (jour/semaine/mois)
 * - Création rapide par clic sur slot libre
 * - Tooltips informatifs
 * - Performance optimisée avec lazy loading
 *
 * @author ZenFleet Architecture Team
 */
class AssignmentGantt extends Component
{
    use AuthorizesRequests;

    // Configuration vue
    #[Url(keep: true)]
    public string $groupBy = 'vehicle'; // 'vehicle' ou 'driver'

    #[Url(keep: true)]
    public string $viewMode = 'week'; // 'day', 'week', 'month'

    #[Url(keep: true)]
    public string $currentDate = '';

    // Filtres
    #[Url(keep: true)]
    public string $statusFilter = '';

    #[Url(keep: true)]
    public string $resourceFilter = '';

    public bool $showOnlyActive = true;

    // Données calculées
    public array $ganttData = [];
    public array $timeScale = [];
    public array $resources = [];

    // Modal création rapide
    public bool $showQuickCreateModal = false;
    public ?string $selectedSlotStart = null;
    public ?string $selectedSlotEnd = null;
    public ?int $selectedResourceId = null;
    public string $selectedResourceType = '';

    // État
    public string $message = '';
    public string $messageType = '';

    // Services
    private AssignmentOverlapService $overlapService;

    public function boot(AssignmentOverlapService $overlapService)
    {
        $this->overlapService = $overlapService;
    }

    public function mount()
    {
        $this->authorize('viewGantt', Assignment::class);

        if (empty($this->currentDate)) {
            $this->currentDate = now()->format('Y-m-d');
        }

        $this->loadGanttData();
    }

    /**
     * Charge les données pour le Gantt
     */
    public function loadGanttData()
    {
        $startDate = $this->getViewStartDate();
        $endDate = $this->getViewEndDate();

        // Charger les ressources (véhicules ou chauffeurs)
        $this->loadResources();

        // Charger les affectations dans la période
        $assignments = $this->getAssignmentsInPeriod($startDate, $endDate);

        // Construire les données Gantt
        $this->ganttData = $this->buildGanttData($assignments);

        // Construire l'échelle temporelle
        $this->timeScale = $this->buildTimeScale($startDate, $endDate);
    }

    /**
     * Charge les ressources selon le regroupement
     */
    private function loadResources()
    {
        $organizationId = auth()->user()->organization_id;

        if ($this->groupBy === 'vehicle') {
            $query = Vehicle::where('organization_id', $organizationId)
                ->where('deleted_at', null);

            if ($this->resourceFilter) {
                $query->where('id', $this->resourceFilter);
            }

            $this->resources = $query->select('id', 'registration_plate', 'brand', 'model', 'status_id')
                ->orderBy('registration_plate')
                ->get()
                ->map(function($vehicle) {
                    return [
                        'id' => $vehicle->id,
                        'type' => 'vehicle',
                        'label' => $vehicle->registration_plate ?? ($vehicle->brand . ' ' . $vehicle->model),
                        'sublabel' => $vehicle->brand . ' ' . $vehicle->model,
                        'available' => true // TODO: calculer disponibilité
                    ];
                })
                ->toArray();

        } else {
            $query = Driver::where('organization_id', $organizationId)
                ->where('deleted_at', null);

            if ($this->resourceFilter) {
                $query->where('id', $this->resourceFilter);
            }

            $this->resources = $query->select('id', 'first_name', 'last_name', 'status_id', 'phone_number')
                ->orderBy('last_name')
                ->get()
                ->map(function($driver) {
                    return [
                        'id' => $driver->id,
                        'type' => 'driver',
                        'label' => $driver->first_name . ' ' . $driver->last_name,
                        'sublabel' => $driver->phone_number,
                        'available' => true // TODO: calculer disponibilité
                    ];
                })
                ->toArray();
        }
    }

    /**
     * Récupère les affectations dans la période
     */
    private function getAssignmentsInPeriod(Carbon $start, Carbon $end)
    {
        return Assignment::where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->showOnlyActive, fn($q) => $q->whereIn('status', [
                Assignment::STATUS_ACTIVE,
                Assignment::STATUS_SCHEDULED
            ]))
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    // Affectations avec fin définie qui intersectent
                    $q->whereNotNull('end_datetime')
                        ->where('start_datetime', '<', $end)
                        ->where('end_datetime', '>', $start);
                })->orWhere(function ($q) use ($start) {
                    // Affectations sans fin qui commencent avant la fin de période
                    $q->whereNull('end_datetime')
                        ->where('start_datetime', '<=', $start->copy()->addDays(30));
                });
            })
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Construit les données Gantt
     */
    private function buildGanttData($assignments)
    {
        $data = [];

        foreach ($assignments as $assignment) {
            $resourceId = $this->groupBy === 'vehicle' ? $assignment->vehicle_id : $assignment->driver_id;

            if (!isset($data[$resourceId])) {
                $data[$resourceId] = [];
            }

            $data[$resourceId][] = [
                'id' => $assignment->id,
                'title' => $this->groupBy === 'vehicle' ? $assignment->driver_display : $assignment->vehicle_display,
                'start' => $assignment->start_datetime,
                'end' => $assignment->end_datetime,
                'status' => $assignment->status,
                'color' => $this->getStatusColor($assignment->status),
                'borderColor' => $this->getStatusBorderColor($assignment->status),
                'textColor' => $this->getStatusTextColor($assignment->status),
                'isOngoing' => $assignment->is_ongoing,
                'canEdit' => auth()->user()->can('update', $assignment),
                'tooltip' => [
                    'vehicle' => $assignment->vehicle_display,
                    'driver' => $assignment->driver_display,
                    'start' => $assignment->start_datetime->format('d/m/Y H:i'),
                    'end' => $assignment->end_datetime?->format('d/m/Y H:i') ?? 'En cours',
                    'duration' => $assignment->formatted_duration,
                    'reason' => $assignment->reason,
                    'notes' => $assignment->notes,
                    'status' => $assignment->status_label
                ]
            ];
        }

        return $data;
    }

    /**
     * Construit l'échelle temporelle
     */
    private function buildTimeScale(Carbon $start, Carbon $end)
    {
        $scale = [];
        $current = $start->copy();

        switch ($this->viewMode) {
            case 'day':
                while ($current->lte($end)) {
                    $scale[] = [
                        'date' => $current->copy(),
                        'label' => $current->format('H:i'),
                        'fullLabel' => $current->format('d/m/Y H:i'),
                        'isToday' => $current->isToday(),
                        'isWorkingHour' => $current->hour >= 8 && $current->hour < 18
                    ];
                    $current->addHour();
                }
                break;

            case 'week':
                while ($current->lte($end)) {
                    $scale[] = [
                        'date' => $current->copy(),
                        'label' => $current->format('D d/m'),
                        'fullLabel' => $current->format('l d/m/Y'),
                        'isToday' => $current->isToday(),
                        'isWeekend' => $current->isWeekend()
                    ];
                    $current->addDay();
                }
                break;

            case 'month':
                while ($current->lte($end)) {
                    $scale[] = [
                        'date' => $current->copy(),
                        'label' => $current->format('d'),
                        'fullLabel' => $current->format('d/m/Y'),
                        'isToday' => $current->isToday(),
                        'isWeekend' => $current->isWeekend(),
                        'isFirstOfMonth' => $current->day === 1
                    ];
                    $current->addDay();
                }
                break;
        }

        return $scale;
    }

    /**
     * Calcul des dates de début/fin selon le mode de vue
     */
    private function getViewStartDate(): Carbon
    {
        $date = Carbon::parse($this->currentDate);

        return match($this->viewMode) {
            'day' => $date->copy()->startOfDay(),
            'week' => $date->copy()->startOfWeek(),
            'month' => $date->copy()->startOfMonth(),
            default => $date->copy()->startOfWeek()
        };
    }

    private function getViewEndDate(): Carbon
    {
        $date = Carbon::parse($this->currentDate);

        return match($this->viewMode) {
            'day' => $date->copy()->endOfDay(),
            'week' => $date->copy()->endOfWeek(),
            'month' => $date->copy()->endOfMonth(),
            default => $date->copy()->endOfWeek()
        };
    }

    /**
     * Navigation temporelle
     */
    public function previousPeriod()
    {
        $date = Carbon::parse($this->currentDate);

        $this->currentDate = match($this->viewMode) {
            'day' => $date->subDay()->format('Y-m-d'),
            'week' => $date->subWeek()->format('Y-m-d'),
            'month' => $date->subMonth()->format('Y-m-d'),
            default => $date->subWeek()->format('Y-m-d')
        };

        $this->loadGanttData();
    }

    public function nextPeriod()
    {
        $date = Carbon::parse($this->currentDate);

        $this->currentDate = match($this->viewMode) {
            'day' => $date->addDay()->format('Y-m-d'),
            'week' => $date->addWeek()->format('Y-m-d'),
            'month' => $date->addMonth()->format('Y-m-d'),
            default => $date->addWeek()->format('Y-m-d')
        };

        $this->loadGanttData();
    }

    public function goToToday()
    {
        $this->currentDate = now()->format('Y-m-d');
        $this->loadGanttData();
    }

    /**
     * Changement de vue
     */
    public function updatedGroupBy()
    {
        $this->resourceFilter = '';
        $this->loadGanttData();
    }

    public function updatedViewMode()
    {
        $this->loadGanttData();
    }

    public function updatedStatusFilter()
    {
        $this->loadGanttData();
    }

    public function updatedResourceFilter()
    {
        $this->loadGanttData();
    }

    public function updatedShowOnlyActive()
    {
        $this->loadGanttData();
    }

    /**
     * Création rapide d'affectation
     */
    public function openQuickCreate(string $resourceType, int $resourceId, string $slotStart, ?string $slotEnd = null)
    {
        $this->authorize('create', Assignment::class);

        $this->selectedResourceType = $resourceType;
        $this->selectedResourceId = $resourceId;
        $this->selectedSlotStart = $slotStart;
        $this->selectedSlotEnd = $slotEnd;
        $this->showQuickCreateModal = true;
    }

    public function closeQuickCreateModal()
    {
        $this->showQuickCreateModal = false;
        $this->selectedResourceType = '';
        $this->selectedResourceId = null;
        $this->selectedSlotStart = null;
        $this->selectedSlotEnd = null;
    }

    /**
     * Événements
     */

    #[On('assignment-saved')]
    public function onAssignmentSaved()
    {
        $this->loadGanttData();
        $this->closeQuickCreateModal();
        $this->setMessage('Affectation créée avec succès', 'success');
    }

    #[On('assignment-updated')]
    public function onAssignmentUpdated()
    {
        $this->loadGanttData();
        $this->setMessage('Affectation mise à jour', 'success');
    }

    #[On('assignment-deleted')]
    public function onAssignmentDeleted()
    {
        $this->loadGanttData();
        $this->setMessage('Affectation supprimée', 'success');
    }

    /**
     * Helpers couleurs de statut
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            Assignment::STATUS_SCHEDULED => '#3B82F6',
            Assignment::STATUS_ACTIVE => '#10B981',
            Assignment::STATUS_COMPLETED => '#6B7280',
            Assignment::STATUS_CANCELLED => '#EF4444',
            default => '#9CA3AF'
        };
    }

    private function getStatusBorderColor(string $status): string
    {
        return match($status) {
            Assignment::STATUS_SCHEDULED => '#2563EB',
            Assignment::STATUS_ACTIVE => '#059669',
            Assignment::STATUS_COMPLETED => '#4B5563',
            Assignment::STATUS_CANCELLED => '#DC2626',
            default => '#6B7280'
        };
    }

    private function getStatusTextColor(string $status): string
    {
        return match($status) {
            Assignment::STATUS_COMPLETED => '#9CA3AF',
            default => '#FFFFFF'
        };
    }

    /**
     * Helpers
     */
    private function setMessage(string $message, string $type = 'info')
    {
        $this->message = $message;
        $this->messageType = $type;
        $this->dispatch('auto-hide-message');
    }

    public function clearMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    /**
     * Calcule la position horizontale d'une affectation dans le Gantt
     */
    public function calculateAssignmentPosition(string $startDateTime): int
    {
        $startDate = Carbon::parse($startDateTime);
        $viewStart = $this->getViewStartDate();

        $slotWidth = match($this->viewMode) {
            'day' => 80,
            'week' => 120,
            'month' => 40,
            default => 120
        };

        return match($this->viewMode) {
            'day' => $startDate->diffInHours($viewStart) * $slotWidth,
            'week' => $startDate->diffInDays($viewStart) * $slotWidth,
            'month' => $startDate->diffInDays($viewStart) * $slotWidth,
            default => $startDate->diffInDays($viewStart) * $slotWidth
        };
    }

    /**
     * Calcule la largeur d'une affectation dans le Gantt
     */
    public function calculateAssignmentWidth(string $startDateTime, ?string $endDateTime = null): int
    {
        $startDate = Carbon::parse($startDateTime);
        $endDate = $endDateTime ? Carbon::parse($endDateTime) : $startDate->copy()->addDay();

        $slotWidth = match($this->viewMode) {
            'day' => 80,
            'week' => 120,
            'month' => 40,
            default => 120
        };

        $duration = match($this->viewMode) {
            'day' => max(1, $startDate->diffInHours($endDate)),
            'week' => max(1, $startDate->diffInDays($endDate)),
            'month' => max(1, $startDate->diffInDays($endDate)),
            default => max(1, $startDate->diffInDays($endDate))
        };

        // Largeur minimale de 20px pour la visibilité
        return max(20, $duration * $slotWidth);
    }

    /**
     * Méthodes pour API front-end
     */
    public function getGanttDataAsJson()
    {
        return json_encode([
            'resources' => $this->resources,
            'assignments' => $this->ganttData,
            'timeScale' => $this->timeScale,
            'viewMode' => $this->viewMode,
            'groupBy' => $this->groupBy,
            'currentDate' => $this->currentDate
        ]);
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.assignments.assignment-gantt', [
            'statusOptions' => Assignment::STATUSES,
            'vehicleOptions' => $this->groupBy === 'vehicle' ?
                Vehicle::where('organization_id', auth()->user()->organization_id)
                    ->select('id', 'registration_plate', 'brand', 'model')
                    ->orderBy('registration_plate')
                    ->get() : [],
            'driverOptions' => $this->groupBy === 'driver' ?
                Driver::where('organization_id', auth()->user()->organization_id)
                    ->select('id', 'first_name', 'last_name')
                    ->orderBy('last_name')
                    ->get() : []
        ]);
    }
}