<?php

namespace App\Livewire\Admin\Assignments;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 * 📋 ASSIGNMENT INDEX - ENTERPRISE LIVEWIRE COMPONENT
 * 
 * Modernizes the assignment management interface with:
 * - Real-time filtering and sorting
 * - Instant pagination
 * - Integrated actions (End, Delete, Export)
 * - Enterprise-grade UI/UX
 */
class AssignmentIndex extends Component
{
    use WithPagination;

    // 🔍 Filters
    #[Url(as: 'q')]
    public $search = '';

    #[Url]
    public $status = '';

    #[Url]
    public $date_from = '';

    #[Url]
    public $date_to = '';

    // ↕️ Sorting
    #[Url]
    public $sort_by = 'created_at';

    #[Url]
    public $sort_order = 'desc';

    // 🛡️ Modal States
    public $showEndModal = false;
    public $endingAssignmentId = null;
    public $endDate = '';  // ✅ NOUVEAU: Date séparée pour calendrier Flatpickr
    public $endTime = '';  // ✅ NOUVEAU: Heure séparée pour SlimSelect
    public $endMileage = '';
    public $endNotes = '';
    public $endingAssignmentVehicle = '';
    public $endingAssignmentDriver = '';
    public $endingAssignmentCurrentMileage = null;

    // 🗑️ Delete Confirmation
    public $showDeleteModal = false;
    public $deletingAssignmentId = null;
    public $deletingAssignmentDescription = '';

    // 🔢 Pagination
    public $per_page = 20;

    // 🔄 Query String Configuration
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'sort_by' => ['except' => 'created_at'],
        'sort_order' => ['except' => 'desc'],
        'per_page' => ['except' => 20],
    ];

    public function mount()
    {
        // Initialize default sort if invalid
        if (!in_array($this->sort_by, ['created_at', 'start_datetime', 'status'])) {
            $this->sort_by = 'created_at';
        }
        if (!in_array($this->sort_order, ['asc', 'desc'])) {
            $this->sort_order = 'desc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->sort_by = 'created_at';
        $this->sort_order = 'desc';
        $this->per_page = 20;
        $this->resetPage();
    }

    // ... (rest of methods)

    public function sortBy($field)
    {
        if ($this->sort_by === $field) {
            $this->sort_order = $this->sort_order === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $field;
            $this->sort_order = 'desc';
        }
    }

    // --- ACTIONS: END ASSIGNMENT ---

    public function confirmEndAssignment($id)
    {
        $assignment = Assignment::with(['vehicle', 'driver'])->find($id);

        if (!$assignment || !$assignment->canBeEnded()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Cette affectation ne peut pas être terminée.']);
            return;
        }

        $this->endingAssignmentId = $id;
        $this->endingAssignmentVehicle = $assignment->vehicle->registration_plate ?? 'N/A';
        $this->endingAssignmentDriver = $assignment->driver->full_name ?? 'N/A';
        $this->endingAssignmentCurrentMileage = $assignment->vehicle->current_mileage;

        // ✅ MODIFIÉ: Défaut séparé pour date et heure
        $this->endDate = now()->format('Y-m-d');
        $this->endTime = now()->format('H:i');
        $this->endMileage = '';
        $this->endNotes = '';

        $this->showEndModal = true;
    }

    public function endAssignment()
    {
        // ✅ MODIFIÉ: Validation des champs séparés
        $this->validate([
            'endDate' => 'required|date',
            'endTime' => 'required|date_format:H:i',
            'endMileage' => 'nullable|integer|min:0',
            'endNotes' => 'nullable|string|max:1000',
        ]);

        $assignment = Assignment::find($this->endingAssignmentId);

        if (!$assignment) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Affectation introuvable.']);
            $this->showEndModal = false;
            return;
        }

        try {
            // ✅ MODIFIÉ: Combiner date + heure avant parsing
            $fullDateTime = "{$this->endDate} {$this->endTime}";

            $assignment->end(
                Carbon::parse($fullDateTime),
                $this->endMileage ? (int)$this->endMileage : null,
                $this->endNotes
            );

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Affectation terminée avec succès.']);
            $this->showEndModal = false;
            $this->reset(['endingAssignmentId', 'endDate', 'endTime', 'endMileage', 'endNotes']);
        } catch (\Exception $e) {
            Log::error('Error ending assignment via Livewire', [
                'id' => $this->endingAssignmentId,
                'error' => $e->getMessage()
            ]);
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de la terminaison: ' . $e->getMessage()]);
        }
    }

    // --- ACTIONS: DELETE ASSIGNMENT ---

    public function confirmDelete($id)
    {
        $assignment = Assignment::with(['vehicle', 'driver'])->find($id);

        if (!$assignment || !$assignment->canBeDeleted()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Cette affectation ne peut pas être supprimée.']);
            return;
        }

        $this->deletingAssignmentId = $id;
        $this->deletingAssignmentDescription = "#{$assignment->id} - {$assignment->vehicle->registration_plate} / {$assignment->driver->full_name}";
        $this->showDeleteModal = true;
    }

    public function deleteAssignment()
    {
        if (!$this->deletingAssignmentId) return;

        $assignment = Assignment::find($this->deletingAssignmentId);

        if ($assignment) {
            try {
                // Use the controller's logic logic via repository or direct model method if available
                // Here we replicate the controller's critical checks
                if ($assignment->hasHandoverModule() && $assignment->handoverForm) {
                    $assignment->handoverForm->delete();
                }

                $assignment->delete();
                $this->dispatch('toast', ['type' => 'success', 'message' => 'Affectation supprimée avec succès.']);
            } catch (\Exception $e) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'Erreur lors de la suppression.']);
            }
        }

        $this->showDeleteModal = false;
        $this->deletingAssignmentId = null;
    }

    // --- DATA FETCHING ---

    public function getAssignmentsProperty()
    {
        $query = Assignment::query()
            ->with(['vehicle', 'driver', 'creator', 'handoverForm'])
            ->where('organization_id', Auth::user()->organization_id);

        // Search
        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'ILIKE', "%{$search}%")
                        ->orWhere('brand', 'ILIKE', "%{$search}%")
                        ->orWhere('model', 'ILIKE', "%{$search}%");
                })
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('first_name', 'ILIKE', "%{$search}%")
                            ->orWhere('last_name', 'ILIKE', "%{$search}%")
                            ->orWhereRaw("(first_name || ' ' || last_name) ILIKE ?", ["%{$search}%"]);
                    });
            });
        }

        // Status Filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Date Range Filter
        if ($this->date_from) {
            try {
                // Support both Y-m-d and d/m/Y formats
                $date = str_contains($this->date_from, '/')
                    ? Carbon::createFromFormat('d/m/Y', $this->date_from)
                    : Carbon::parse($this->date_from);

                $query->where('start_datetime', '>=', $date->startOfDay());
            } catch (\Exception $e) {
                // Ignore invalid dates
            }
        }
        if ($this->date_to) {
            try {
                $date = str_contains($this->date_to, '/')
                    ? Carbon::createFromFormat('d/m/Y', $this->date_to)
                    : Carbon::parse($this->date_to);

                $query->where('start_datetime', '<=', $date->endOfDay());
            } catch (\Exception $e) {
                // Ignore invalid dates
            }
        }

        // Sorting
        $query->orderBy($this->sort_by, $this->sort_order);

        return $query->paginate($this->per_page);
    }

    public function render()
    {
        // Statistics
        $allAssignments = Assignment::where('organization_id', Auth::user()->organization_id);
        $activeAssignments = (clone $allAssignments)->where('status', 'active')->count();
        $inProgressAssignments = (clone $allAssignments)->where('status', 'in_progress')->count();
        $scheduledAssignments = (clone $allAssignments)->where('status', 'scheduled')->count();

        return view('livewire.admin.assignments.assignment-index', [
            'assignments' => $this->assignments,
            'activeAssignments' => $activeAssignments,
            'inProgressAssignments' => $inProgressAssignments,
            'scheduledAssignments' => $scheduledAssignments,
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
