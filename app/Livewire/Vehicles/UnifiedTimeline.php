<?php

namespace App\Livewire\Vehicles;

use App\Models\Vehicle;
use App\Models\DepotAssignmentHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * UnifiedTimeline Livewire Component
 *
 * Displays a comprehensive timeline of vehicle events:
 * - Depot assignments (DepotAssignmentHistory)
 * - Driver assignments (assignments table)
 * - Maintenances (if available)
 * - Expenses (if relevant)
 *
 * Features:
 * - Chronological order (newest first)
 * - Event type filtering
 * - Color-coded by action
 * - Distinct icons per event type
 *
 * @package App\Livewire\Vehicles
 */
class UnifiedTimeline extends Component
{
    public $vehicleId;
    public $vehicle;

    // Filters
    public $showDepotEvents = true;
    public $showDriverEvents = true;
    public $showMaintenanceEvents = true;
    public $showExpenseEvents = false;

    public $limit = 50;
    public $timelineEvents;

    protected $listeners = [
        'refreshVehicleData' => 'loadTimeline',
        'depot-assigned' => 'loadTimeline',
        'depot-unassigned' => 'loadTimeline',
    ];

    public function mount($vehicleId)
    {
        $this->vehicleId = $vehicleId;
        $this->loadVehicle();
        $this->loadTimeline();
    }

    public function render()
    {
        return view('livewire.vehicles.unified-timeline');
    }

    protected function loadVehicle()
    {
        $this->vehicle = Vehicle::where('id', $this->vehicleId)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();
    }

    public function loadTimeline()
    {
        $events = collect();

        // Load depot assignment history
        if ($this->showDepotEvents) {
            $depotEvents = DepotAssignmentHistory::forVehicle($this->vehicleId)
                ->with(['depot', 'previousDepot', 'assignedBy'])
                ->get()
                ->map(function ($history) {
                    return [
                        'type' => 'depot',
                        'date' => $history->assigned_at,
                        'action' => $history->action,
                        'icon' => $this->getIconForDepotAction($history->action),
                        'color' => $history->actionColor,
                        'title' => $history->actionLabel,
                        'description' => $this->getDepotDescription($history),
                        'user' => $history->assignedBy,
                        'notes' => $history->notes,
                        'data' => $history,
                    ];
                });

            $events = $events->merge($depotEvents);
        }

        // Load driver assignments
        if ($this->showDriverEvents) {
            $driverAssignments = $this->vehicle->assignments()
                ->with(['driver.user', 'assignedBy'])
                ->get()
                ->map(function ($assignment) {
                    $isActive = !$assignment->returned_at;

                    return [
                        'type' => 'driver',
                        'date' => $assignment->assigned_at,
                        'action' => $isActive ? 'assigned' : 'returned',
                        'icon' => 'user-check',
                        'color' => $isActive ? 'purple' : 'gray',
                        'title' => $isActive ? 'Affecté au chauffeur' : 'Retourné par le chauffeur',
                        'description' => $assignment->driver->user->name ?? 'Chauffeur inconnu',
                        'user' => $assignment->assignedBy,
                        'notes' => $assignment->notes,
                        'data' => $assignment,
                    ];
                });

            $events = $events->merge($driverAssignments);
        }

        // Load maintenance events
        if ($this->showMaintenanceEvents && method_exists($this->vehicle, 'maintenances')) {
            $maintenanceEvents = $this->vehicle->maintenances()
                ->get()
                ->map(function ($maintenance) {
                    return [
                        'type' => 'maintenance',
                        'date' => $maintenance->scheduled_date ?? $maintenance->created_at,
                        'action' => $maintenance->status ?? 'scheduled',
                        'icon' => 'wrench',
                        'color' => $this->getMaintenanceColor($maintenance->status ?? 'scheduled'),
                        'title' => 'Maintenance',
                        'description' => $maintenance->description ?? $maintenance->type ?? 'Maintenance planifiée',
                        'user' => $maintenance->createdBy ?? null,
                        'notes' => $maintenance->notes ?? null,
                        'data' => $maintenance,
                    ];
                });

            $events = $events->merge($maintenanceEvents);
        }

        // Load expense events (optional)
        if ($this->showExpenseEvents && method_exists($this->vehicle, 'expenses')) {
            $expenseEvents = $this->vehicle->expenses()
                ->limit(20)
                ->get()
                ->map(function ($expense) {
                    return [
                        'type' => 'expense',
                        'date' => $expense->expense_date ?? $expense->created_at,
                        'action' => 'expense',
                        'icon' => 'credit-card',
                        'color' => 'yellow',
                        'title' => 'Dépense',
                        'description' => $expense->description ?? $expense->category ?? 'Dépense enregistrée',
                        'user' => $expense->createdBy ?? null,
                        'notes' => number_format($expense->amount, 2) . ' DA',
                        'data' => $expense,
                    ];
                });

            $events = $events->merge($expenseEvents);
        }

        // Sort by date descending and limit
        $this->timelineEvents = $events
            ->sortByDesc('date')
            ->take($this->limit)
            ->values();
    }

    protected function getDepotDescription($history)
    {
        if ($history->isTransfer()) {
            $from = $history->previousDepot?->name ?? 'Inconnu';
            $to = $history->depot?->name ?? 'Inconnu';
            return "De {$from} vers {$to}";
        }

        if ($history->isAssignment()) {
            return $history->depot?->name ?? 'Dépôt inconnu';
        }

        if ($history->isUnassignment()) {
            return $history->previousDepot?->name ?? 'Dépôt inconnu';
        }

        return '';
    }

    protected function getIconForDepotAction($action)
    {
        return match($action) {
            'assigned' => 'building-2',
            'unassigned' => 'x-circle',
            'transferred' => 'arrow-right-left',
            default => 'building-2',
        };
    }

    protected function getMaintenanceColor($status)
    {
        return match($status) {
            'scheduled' => 'blue',
            'in_progress' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function toggleFilter($filter)
    {
        $this->$filter = !$this->$filter;
        $this->loadTimeline();
    }

    public function loadMore()
    {
        $this->limit += 25;
        $this->loadTimeline();
    }

    public function getFilterStats()
    {
        return [
            'depot' => DepotAssignmentHistory::forVehicle($this->vehicleId)->count(),
            'driver' => $this->vehicle->assignments()->count(),
            'maintenance' => method_exists($this->vehicle, 'maintenances')
                ? $this->vehicle->maintenances()->count()
                : 0,
            'expense' => method_exists($this->vehicle, 'expenses')
                ? $this->vehicle->expenses()->count()
                : 0,
        ];
    }
}
