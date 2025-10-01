<?php

namespace App\Livewire\Assignments;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\AssignmentOverlapService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 📊 Composant Table des Affectations
 *
 * Fonctionnalités:
 * - Table paginée avec tri et filtres
 * - Actions rapides (terminer, éditer, supprimer)
 * - Export CSV
 * - Recherche temps réel
 * - États vides informatifs
 *
 * @author ZenFleet Architecture Team
 */
class AssignmentTable extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtres et recherche
    #[Url(keep: true)]
    public string $search = '';

    #[Url(keep: true)]
    public string $statusFilter = '';

    #[Url(keep: true)]
    public string $vehicleFilter = '';

    #[Url(keep: true)]
    public string $driverFilter = '';

    #[Url(keep: true)]
    public string $dateFromFilter = '';

    #[Url(keep: true)]
    public string $dateToFilter = '';

    #[Url(keep: true)]
    public bool $onlyOngoing = false;

    // Tri
    #[Url(keep: true)]
    public string $sortField = 'start_datetime';

    #[Url(keep: true)]
    public string $sortDirection = 'desc';

    // Pagination
    #[Url(keep: true)]
    public int $perPage = 25;

    // Modal et actions
    public bool $showFormModal = false;
    public bool $showEndModal = false;
    public bool $showDeleteModal = false;
    public ?Assignment $selectedAssignment = null;

    // Données pour les selects
    public $vehicles = [];
    public $drivers = [];

    // Messages
    public string $message = '';
    public string $messageType = '';

    public function mount()
    {
        $this->authorize('viewAny', Assignment::class);
        $this->loadSelectData();
    }

    /**
     * Charge les données pour les selects de filtrage
     */
    private function loadSelectData()
    {
        $organizationId = auth()->user()->organization_id;

        $this->vehicles = Vehicle::where('organization_id', $organizationId)
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'label' => $v->registration_plate ?? ($v->brand . ' ' . $v->model)
            ]);

        $this->drivers = Driver::where('organization_id', $organizationId)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'label' => $d->first_name . ' ' . $d->last_name
            ]);
    }

    /**
     * Requête principale avec filtres
     */
    public function getAssignmentsProperty()
    {
        return Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->whereHas('vehicle', function ($vehicle) {
                        $vehicle->where('registration_plate', 'like', '%' . $this->search . '%')
                            ->orWhere('brand', 'like', '%' . $this->search . '%')
                            ->orWhere('model', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('driver', function ($driver) {
                        $driver->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('reason', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->vehicleFilter, fn($q) => $q->where('vehicle_id', $this->vehicleFilter))
            ->when($this->driverFilter, fn($q) => $q->where('driver_id', $this->driverFilter))
            ->when($this->dateFromFilter, fn($q) => $q->where('start_datetime', '>=', $this->dateFromFilter))
            ->when($this->dateToFilter, fn($q) => $q->where('start_datetime', '<=', $this->dateToFilter . ' 23:59:59'))
            ->when($this->onlyOngoing, fn($q) => $q->ongoing())
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    /**
     * Met à jour la recherche et reset la pagination
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedVehicleFilter()
    {
        $this->resetPage();
    }

    public function updatedDriverFilter()
    {
        $this->resetPage();
    }

    public function updatedOnlyOngoing()
    {
        $this->resetPage();
    }

    /**
     * Tri des colonnes
     */
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Réinitialiser les filtres
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->vehicleFilter = '';
        $this->driverFilter = '';
        $this->dateFromFilter = '';
        $this->dateToFilter = '';
        $this->onlyOngoing = false;
        $this->resetPage();
    }

    /**
     * Actions rapides
     */

    public function openCreateModal()
    {
        $this->authorize('create', Assignment::class);
        $this->selectedAssignment = null;
        $this->showFormModal = true;
    }

    public function openEditModal(Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        $this->selectedAssignment = $assignment;
        $this->showFormModal = true;
    }

    public function openEndModal(Assignment $assignment)
    {
        $this->authorize('end', $assignment);

        if (!$assignment->canBeEnded()) {
            $this->setMessage('Cette affectation ne peut pas être terminée.', 'error');
            return;
        }

        $this->selectedAssignment = $assignment;
        $this->showEndModal = true;
    }

    public function confirmEnd()
    {
        if (!$this->selectedAssignment || !$this->selectedAssignment->canBeEnded()) {
            $this->setMessage('Action non autorisée.', 'error');
            return;
        }

        $this->authorize('end', $this->selectedAssignment);

        if ($this->selectedAssignment->end()) {
            $this->setMessage(
                "Affectation terminée avec succès. Véhicule {$this->selectedAssignment->vehicle_display} restitué.",
                'success'
            );

            // Émettre événement pour rafraîchir autres composants
            $this->dispatch('assignment-ended', $this->selectedAssignment->id);
        } else {
            $this->setMessage('Erreur lors de la fin de l\'affectation.', 'error');
        }

        $this->closeEndModal();
    }

    public function openDeleteModal(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        if (!$assignment->canBeDeleted()) {
            $this->setMessage('Cette affectation ne peut pas être supprimée.', 'error');
            return;
        }

        $this->selectedAssignment = $assignment;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if (!$this->selectedAssignment || !$this->selectedAssignment->canBeDeleted()) {
            $this->setMessage('Action non autorisée.', 'error');
            return;
        }

        $this->authorize('delete', $this->selectedAssignment);

        $vehicleDisplay = $this->selectedAssignment->vehicle_display;

        if ($this->selectedAssignment->delete()) {
            $this->setMessage("Affectation supprimée avec succès ({$vehicleDisplay}).", 'success');
            $this->dispatch('assignment-deleted', $this->selectedAssignment->id);
        } else {
            $this->setMessage('Erreur lors de la suppression.', 'error');
        }

        $this->closeDeleteModal();
    }

    public function duplicateAssignment(Assignment $assignment)
    {
        $this->authorize('create', Assignment::class);

        // Créer une nouvelle affectation basée sur l'existante
        $this->selectedAssignment = new Assignment([
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'reason' => $assignment->reason,
            'notes' => 'Copie de l\'affectation #' . $assignment->id . ($assignment->notes ? "\n\n" . $assignment->notes : ''),
            'estimated_duration_hours' => $assignment->estimated_duration_hours,
        ]);

        $this->showFormModal = true;
    }

    /**
     * Export CSV
     */
    public function exportCsv()
    {
        $this->authorize('export', Assignment::class);

        $assignments = Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator'])
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->whereHas('vehicle', function ($vehicle) {
                        $vehicle->where('registration_plate', 'like', '%' . $this->search . '%')
                            ->orWhere('brand', 'like', '%' . $this->search . '%')
                            ->orWhere('model', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('driver', function ($driver) {
                        $driver->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->vehicleFilter, fn($q) => $q->where('vehicle_id', $this->vehicleFilter))
            ->when($this->driverFilter, fn($q) => $q->where('driver_id', $this->driverFilter))
            ->when($this->dateFromFilter, fn($q) => $q->where('start_datetime', '>=', $this->dateFromFilter))
            ->when($this->dateToFilter, fn($q) => $q->where('start_datetime', '<=', $this->dateToFilter . ' 23:59:59'))
            ->when($this->onlyOngoing, fn($q) => $q->ongoing())
            ->orderBy('start_datetime', 'desc')
            ->get();

        $filename = 'affectations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Véhicule',
                'Chauffeur',
                'Date/Heure remise',
                'Date/Heure restitution',
                'Statut',
                'Durée',
                'Motif',
                'Notes',
                'Créé par',
                'Créé le'
            ], ';');

            // Données
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->id,
                    $assignment->vehicle_display,
                    $assignment->driver_display,
                    $assignment->start_datetime->format('d/m/Y H:i'),
                    $assignment->end_datetime?->format('d/m/Y H:i') ?? 'En cours',
                    $assignment->status_label,
                    $assignment->formatted_duration,
                    $assignment->reason,
                    $assignment->notes,
                    $assignment->creator?->name ?? 'Système',
                    $assignment->created_at->format('d/m/Y H:i')
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Gestionnaires de modales
     */

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->selectedAssignment = null;
    }

    public function closeEndModal()
    {
        $this->showEndModal = false;
        $this->selectedAssignment = null;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedAssignment = null;
    }

    /**
     * Écoute des événements
     */

    #[On('assignment-saved')]
    public function onAssignmentSaved($assignmentId)
    {
        $this->setMessage('Affectation sauvegardée avec succès.', 'success');
        $this->closeFormModal();
    }

    #[On('assignment-form-cancelled')]
    public function onFormCancelled()
    {
        $this->closeFormModal();
    }

    /**
     * Helpers
     */

    private function setMessage(string $message, string $type = 'info')
    {
        $this->message = $message;
        $this->messageType = $type;

        // Auto-hide après 5 secondes
        $this->dispatch('auto-hide-message');
    }

    public function clearMessage()
    {
        $this->message = '';
        $this->messageType = '';
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.assignments.assignment-table', [
            'assignments' => $this->assignments,
            'statusOptions' => Assignment::STATUSES,
        ]);
    }
}