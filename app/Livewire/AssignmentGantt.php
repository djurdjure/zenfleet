<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\OverlapCheckService;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;

/**
 * 📊 Composant Gantt des Affectations - Enterprise Grade
 *
 * Fonctionnalités selon spécifications:
 * - Vue temporelle interactive avec drag & drop
 * - Gestion des ressources (véhicules/chauffeurs)
 * - Zoom temporel (jour, semaine, mois)
 * - Détection visuelle des conflits
 * - Export PDF/PNG du planning
 * - Responsive design avec touch support
 */
class AssignmentGantt extends Component
{
    use AuthorizesRequests;

    // Vue et filtres
    #[Url(keep: true)]
    public string $viewMode = 'week'; // day, week, month

    #[Url(keep: true)]
    public string $resourceType = 'vehicles'; // vehicles, drivers

    #[Url(keep: true)]
    public string $startDate = '';

    #[Url(keep: true)]
    public string $resourceFilter = '';

    #[Url(keep: true)]
    public string $statusFilter = '';

    // État du composant
    public array $ganttData = [];
    public array $resources = [];
    public array $timeScale = [];
    public bool $isLoading = false;
    public ?Assignment $selectedAssignment = null;

    // Configuration Gantt
    public array $ganttConfig = [
        'cellWidth' => 120,
        'cellHeight' => 60,
        'headerHeight' => 80,
        'showWeekends' => true,
        'enableDragDrop' => true,
        'showConflicts' => true
    ];

    // Actions modales
    public bool $showAssignmentModal = false;
    public bool $showResourceModal = false;

    protected OverlapCheckService $overlapService;

    public function boot(OverlapCheckService $overlapService)
    {
        $this->overlapService = $overlapService;
    }

    public function mount()
    {
        $this->authorize('viewAny', Assignment::class);

        // Initialiser la date de début à cette semaine
        if (empty($this->startDate)) {
            $this->startDate = now()->startOfWeek()->format('Y-m-d');
        }

        $this->loadGanttData();
    }

    public function render()
    {
        return view('livewire.assignment-gantt', [
            'viewModes' => [
                'day' => 'Jour',
                'week' => 'Semaine',
                'month' => 'Mois'
            ],
            'resourceTypes' => [
                'vehicles' => 'Par véhicules',
                'drivers' => 'Par chauffeurs'
            ],
            'statusOptions' => Assignment::STATUSES
        ]);
    }

    /**
     * Navigation temporelle
     */
    public function previousPeriod()
    {
        $date = Carbon::parse($this->startDate);

        $this->startDate = match($this->viewMode) {
            'day' => $date->subDay()->format('Y-m-d'),
            'week' => $date->subWeek()->format('Y-m-d'),
            'month' => $date->subMonth()->format('Y-m-d'),
            default => $date->subWeek()->format('Y-m-d')
        };

        $this->loadGanttData();
    }

    public function nextPeriod()
    {
        $date = Carbon::parse($this->startDate);

        $this->startDate = match($this->viewMode) {
            'day' => $date->addDay()->format('Y-m-d'),
            'week' => $date->addWeek()->format('Y-m-d'),
            'month' => $date->addMonth()->format('Y-m-d'),
            default => $date->addWeek()->format('Y-m-d')
        };

        $this->loadGanttData();
    }

    public function goToToday()
    {
        $this->startDate = match($this->viewMode) {
            'day' => now()->format('Y-m-d'),
            'week' => now()->startOfWeek()->format('Y-m-d'),
            'month' => now()->startOfMonth()->format('Y-m-d'),
            default => now()->startOfWeek()->format('Y-m-d')
        };

        $this->loadGanttData();
    }

    /**
     * Changements de vue
     */
    public function updatedViewMode()
    {
        $this->adjustStartDateForViewMode();
        $this->loadGanttData();
    }

    public function updatedResourceType()
    {
        $this->resourceFilter = '';
        $this->loadGanttData();
    }

    public function updatedResourceFilter()
    {
        $this->loadGanttData();
    }

    public function updatedStatusFilter()
    {
        $this->loadGanttData();
    }

    /**
     * Chargement des données Gantt
     */
    public function loadGanttData()
    {
        $this->isLoading = true;

        try {
            $organizationId = auth()->user()->organization_id;

            // Calculer la période
            $period = $this->calculatePeriod();

            // Charger les ressources
            $this->resources = $this->loadResources($organizationId);

            // Charger les affectations
            $assignments = $this->loadAssignments($organizationId, $period);

            // Générer l'échelle temporelle
            $this->timeScale = $this->generateTimeScale($period);

            // Transformer en format Gantt
            $this->ganttData = $this->transformToGanttFormat($assignments);

        } catch (\Exception $e) {
            $this->dispatch('gantt-error', [
                'message' => 'Erreur lors du chargement: ' . $e->getMessage()
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Actions sur les affectations
     */
    public function viewAssignment(int $assignmentId)
    {
        $this->selectedAssignment = Assignment::with(['vehicle', 'driver', 'creator'])
            ->findOrFail($assignmentId);

        $this->authorize('view', $this->selectedAssignment);
        $this->showAssignmentModal = true;
    }

    public function editAssignment(int $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorize('update', $assignment);

        $this->dispatch('open-assignment-edit', [
            'assignment_id' => $assignmentId
        ]);
    }

    public function duplicateAssignment(int $assignmentId)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorize('create', Assignment::class);

        // Créer une nouvelle affectation pour demain
        $newStart = now()->addDay()->setTime(
            $assignment->start_datetime->hour,
            $assignment->start_datetime->minute
        );

        $this->dispatch('open-assignment-form', [
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'start_datetime' => $newStart->format('Y-m-d\TH:i'),
            'reason' => $assignment->reason,
            'prefill' => true
        ]);
    }

    /**
     * Drag & Drop des affectations
     */
    public function moveAssignment(int $assignmentId, string $newStart, int $newResourceId = null)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorize('update', $assignment);

        try {
            $startDate = Carbon::parse($newStart);
            $endDate = $assignment->end_datetime ?
                $startDate->copy()->addHours($assignment->duration_hours) :
                null;

            // Vérifier les conflits si la ressource change
            $vehicleId = $this->resourceType === 'vehicles' ?
                ($newResourceId ?? $assignment->vehicle_id) :
                $assignment->vehicle_id;

            $driverId = $this->resourceType === 'drivers' ?
                ($newResourceId ?? $assignment->driver_id) :
                $assignment->driver_id;

            $validation = $this->overlapService->validateAssignment(
                vehicleId: $vehicleId,
                driverId: $driverId,
                start: $startDate,
                end: $endDate,
                excludeId: $assignmentId
            );

            if (!$validation['is_valid']) {
                $this->dispatch('move-conflict', [
                    'assignment_id' => $assignmentId,
                    'errors' => $validation['errors'],
                    'suggestions' => $validation['suggestions']
                ]);
                return;
            }

            // Effectuer le déplacement
            $updateData = [
                'start_datetime' => $startDate,
                'end_datetime' => $endDate
            ];

            if ($newResourceId) {
                if ($this->resourceType === 'vehicles') {
                    $updateData['vehicle_id'] = $newResourceId;
                } else {
                    $updateData['driver_id'] = $newResourceId;
                }
            }

            $assignment->update($updateData);

            $this->dispatch('assignment-moved', [
                'assignment_id' => $assignmentId,
                'message' => 'Affectation déplacée avec succès'
            ]);

            $this->loadGanttData();

        } catch (\Exception $e) {
            $this->dispatch('move-error', [
                'assignment_id' => $assignmentId,
                'message' => 'Erreur lors du déplacement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Redimensionnement des affectations
     */
    public function resizeAssignment(int $assignmentId, string $newEnd)
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorize('update', $assignment);

        try {
            $endDate = Carbon::parse($newEnd);

            if ($endDate->lte($assignment->start_datetime)) {
                $this->dispatch('resize-error', [
                    'assignment_id' => $assignmentId,
                    'message' => 'La date de fin doit être postérieure au début'
                ]);
                return;
            }

            $assignment->update(['end_datetime' => $endDate]);

            $this->dispatch('assignment-resized', [
                'assignment_id' => $assignmentId,
                'message' => 'Durée modifiée avec succès'
            ]);

            $this->loadGanttData();

        } catch (\Exception $e) {
            $this->dispatch('resize-error', [
                'assignment_id' => $assignmentId,
                'message' => 'Erreur lors du redimensionnement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export du planning
     */
    public function exportPDF()
    {
        $this->authorize('viewAny', Assignment::class);

        $this->dispatch('export-gantt-pdf', [
            'view_mode' => $this->viewMode,
            'resource_type' => $this->resourceType,
            'start_date' => $this->startDate,
            'filename' => 'planning_' . $this->resourceType . '_' . $this->startDate . '.pdf'
        ]);
    }

    public function exportPNG()
    {
        $this->authorize('viewAny', Assignment::class);

        $this->dispatch('export-gantt-png', [
            'filename' => 'planning_' . $this->resourceType . '_' . $this->startDate . '.png'
        ]);
    }

    /**
     * Modales
     */
    public function closeModals()
    {
        $this->showAssignmentModal = false;
        $this->showResourceModal = false;
        $this->selectedAssignment = null;
    }

    /**
     * Événements Livewire
     */
    #[On('assignment-created')]
    #[On('assignment-updated')]
    #[On('assignment-deleted')]
    public function refreshGantt()
    {
        $this->loadGanttData();
    }

    /**
     * Méthodes privées
     */
    private function calculatePeriod(): array
    {
        $start = Carbon::parse($this->startDate);

        $end = match($this->viewMode) {
            'day' => $start->copy()->endOfDay(),
            'week' => $start->copy()->endOfWeek(),
            'month' => $start->copy()->endOfMonth(),
            default => $start->copy()->endOfWeek()
        };

        return [
            'start' => $start,
            'end' => $end
        ];
    }

    private function loadResources(int $organizationId): Collection
    {
        if ($this->resourceType === 'vehicles') {
            $query = Vehicle::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->select('id', 'registration_plate', 'brand', 'model');
        } else {
            $query = Driver::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->select('id', 'first_name', 'last_name', 'license_number');
        }

        if ($this->resourceFilter) {
            $query->where('id', $this->resourceFilter);
        }

        return $query->orderBy($this->resourceType === 'vehicles' ? 'registration_plate' : 'last_name')
                    ->get();
    }

    private function loadAssignments(int $organizationId, array $period): Collection
    {
        $query = Assignment::where('organization_id', $organizationId)
            ->with(['vehicle', 'driver', 'creator'])
            ->where(function ($q) use ($period) {
                $q->where(function ($query) use ($period) {
                    // Affectations avec fin définie qui intersectent la période
                    $query->whereNotNull('end_datetime')
                        ->where('start_datetime', '<', $period['end'])
                        ->where('end_datetime', '>', $period['start']);
                })->orWhere(function ($query) use ($period) {
                    // Affectations sans fin qui commencent avant la fin de période
                    $query->whereNull('end_datetime')
                        ->where('start_datetime', '<', $period['end']);
                });
            });

        // Filtrer par statut
        if ($this->statusFilter) {
            $query->whereRaw("
                CASE
                    WHEN start_datetime > NOW() THEN 'scheduled'
                    WHEN end_datetime IS NULL OR end_datetime > NOW() THEN 'active'
                    ELSE 'completed'
                END = ?
            ", [$this->statusFilter]);
        }

        // Filtrer par ressource
        if ($this->resourceFilter) {
            if ($this->resourceType === 'vehicles') {
                $query->where('vehicle_id', $this->resourceFilter);
            } else {
                $query->where('driver_id', $this->resourceFilter);
            }
        }

        return $query->get();
    }

    private function generateTimeScale(array $period): array
    {
        $scale = [];
        $current = $period['start']->copy();

        while ($current->lte($period['end'])) {
            $scale[] = [
                'date' => $current->format('Y-m-d'),
                'label' => $this->formatTimeLabel($current),
                'is_weekend' => $current->isWeekend(),
                'is_today' => $current->isToday()
            ];

            $current = match($this->viewMode) {
                'day' => $current->addHour(),
                'week' => $current->addDay(),
                'month' => $current->addDay(),
                default => $current->addDay()
            };
        }

        return $scale;
    }

    private function transformToGanttFormat(Collection $assignments): array
    {
        $ganttData = [];

        foreach ($this->resources as $resource) {
            $resourceId = $resource->id;
            $resourceAssignments = $assignments->filter(function ($assignment) use ($resourceId) {
                return $this->resourceType === 'vehicles' ?
                    $assignment->vehicle_id == $resourceId :
                    $assignment->driver_id == $resourceId;
            });

            $ganttData[$resourceId] = [
                'resource' => $resource,
                'assignments' => $resourceAssignments->map(function ($assignment) {
                    return $assignment->toGanttArray();
                })->toArray()
            ];
        }

        return $ganttData;
    }

    private function formatTimeLabel(Carbon $date): string
    {
        return match($this->viewMode) {
            'day' => $date->format('H:i'),
            'week' => $date->format('D j'),
            'month' => $date->format('j'),
            default => $date->format('D j')
        };
    }

    private function adjustStartDateForViewMode()
    {
        $date = Carbon::parse($this->startDate);

        $this->startDate = match($this->viewMode) {
            'day' => $date->format('Y-m-d'),
            'week' => $date->startOfWeek()->format('Y-m-d'),
            'month' => $date->startOfMonth()->format('Y-m-d'),
            default => $date->startOfWeek()->format('Y-m-d')
        };
    }

    /**
     * Getters pour la vue
     */
    public function getPeriodLabelProperty(): string
    {
        $start = Carbon::parse($this->startDate);

        return match($this->viewMode) {
            'day' => $start->format('l j F Y'),
            'week' => 'Semaine du ' . $start->format('j F') . ' au ' . $start->endOfWeek()->format('j F Y'),
            'month' => $start->format('F Y'),
            default => $start->format('j F Y')
        };
    }

    public function getResourceCountProperty(): int
    {
        return count($this->resources);
    }

    public function getAssignmentCountProperty(): int
    {
        return collect($this->ganttData)->sum(fn($resource) => count($resource['assignments']));
    }
}