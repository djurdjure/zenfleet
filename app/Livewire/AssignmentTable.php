<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\OverlapCheckService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * 📊 Composant Table des Affectations - Enterprise Grade
 *
 * Fonctionnalités selon spécifications:
 * - Colonnes: Véhicule, Chauffeur, Date/Heure remise, Date/Heure restitution, Statut, Durée, Actions
 * - Filtres: véhicule, chauffeur, statut, date début, date fin, "seulement en cours"
 * - Tri: véhicule, chauffeur, start_datetime, statut
 * - Pagination: 25/50/100 par page
 * - Actions: Voir/Éditer, Terminer (si en cours), Dupliquer, Supprimer
 * - Export CSV des résultats filtrés
 */
class AssignmentTable extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtres avec URL persistante
    #[Url(keep: true)]
    public string $search = '';

    #[Url(keep: true)]
    public string $vehicleFilter = '';

    #[Url(keep: true)]
    public string $driverFilter = '';

    #[Url(keep: true)]
    public string $statusFilter = '';

    #[Url(keep: true)]
    public string $dateFromFilter = '';

    #[Url(keep: true)]
    public string $dateToFilter = '';

    #[Url(keep: true)]
    public bool $onlyOngoing = false;

    // Tri
    #[Url(keep: true)]
    public string $sortBy = 'start_datetime';

    #[Url(keep: true)]
    public string $sortDirection = 'desc';

    // Pagination
    #[Url(keep: true)]
    public int $perPage = 25;

    // État des modales
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showTerminateModal = false;
    public bool $showDeleteModal = false;

    // Affectation sélectionnée
    public ?Assignment $selectedAssignment = null;

    // Données pour terminer une affectation
    public string $terminateDateTime = '';
    public string $terminateNotes = '';

    public function mount()
    {
        $this->authorize('viewAny', Assignment::class);

        // Initialiser la date de terminaison à maintenant
        $this->terminateDateTime = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        $assignments = $this->getAssignments();

        return view('livewire.assignment-table', [
            'assignments' => $assignments,
            'vehicleOptions' => $this->getVehicleOptions(),
            'driverOptions' => $this->getDriverOptions(),
            'statusOptions' => Assignment::STATUSES,
            'perPageOptions' => [25, 50, 100]
        ]);
    }

    /**
     * Récupère les affectations avec filtres et tri
     */
    private function getAssignments(): LengthAwarePaginator
    {
        $query = Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator']);

        // Filtres
        $this->applyFilters($query);

        // Tri
        $this->applySorting($query);

        return $query->paginate($this->perPage);
    }

    private function applyFilters($query): void
    {
        // Recherche globale
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('vehicle', function ($vehicleQuery) {
                    $vehicleQuery->where('registration_plate', 'like', "%{$this->search}%")
                               ->orWhere('brand', 'like', "%{$this->search}%")
                               ->orWhere('model', 'like', "%{$this->search}%");
                })->orWhereHas('driver', function ($driverQuery) {
                    $driverQuery->where('first_name', 'like', "%{$this->search}%")
                              ->orWhere('last_name', 'like', "%{$this->search}%");
                })->orWhere('reason', 'like', "%{$this->search}%");
            });
        }

        // Filtre véhicule
        if ($this->vehicleFilter) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        // Filtre chauffeur
        if ($this->driverFilter) {
            $query->where('driver_id', $this->driverFilter);
        }

        // Filtre statut
        if ($this->statusFilter) {
            $query->whereRaw("
                CASE
                    WHEN start_datetime > NOW() THEN 'scheduled'
                    WHEN end_datetime IS NULL OR end_datetime > NOW() THEN 'active'
                    ELSE 'completed'
                END = ?
            ", [$this->statusFilter]);
        }

        // Filtre période
        if ($this->dateFromFilter) {
            $query->where('start_datetime', '>=', $this->dateFromFilter);
        }

        if ($this->dateToFilter) {
            $query->where('start_datetime', '<=', $this->dateToFilter . ' 23:59:59');
        }

        // Seulement en cours
        if ($this->onlyOngoing) {
            $query->whereNull('end_datetime')
                  ->where('start_datetime', '<=', now());
        }
    }

    private function applySorting($query): void
    {
        switch ($this->sortBy) {
            case 'vehicle':
                $query->join('vehicles', 'assignments.vehicle_id', '=', 'vehicles.id')
                      ->orderBy('vehicles.registration_plate', $this->sortDirection)
                      ->select('assignments.*');
                break;

            case 'driver':
                $query->join('drivers', 'assignments.driver_id', '=', 'drivers.id')
                      ->orderBy('drivers.last_name', $this->sortDirection)
                      ->orderBy('drivers.first_name', $this->sortDirection)
                      ->select('assignments.*');
                break;

            case 'status':
                $query->orderByRaw("
                    CASE
                        WHEN start_datetime > NOW() THEN 1
                        WHEN end_datetime IS NULL OR end_datetime > NOW() THEN 2
                        ELSE 3
                    END {$this->sortDirection}
                ");
                break;

            default:
                $query->orderBy($this->sortBy, $this->sortDirection);
        }
    }

    /**
     * Options pour les filtres
     */
    private function getVehicleOptions()
    {
        return Vehicle::where('organization_id', auth()->user()->organization_id)
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();
    }

    private function getDriverOptions()
    {
        return Driver::where('organization_id', auth()->user()->organization_id)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('last_name')
            ->get();
    }

    /**
     * Actions de tri
     */
    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Réinitialiser les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->vehicleFilter = '';
        $this->driverFilter = '';
        $this->statusFilter = '';
        $this->dateFromFilter = '';
        $this->dateToFilter = '';
        $this->onlyOngoing = false;
        $this->resetPage();
    }

    /**
     * Changer le nombre d'éléments par page
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Actions des modales
     */
    public function openCreateModal(): void
    {
        $this->authorize('create', Assignment::class);
        $this->showCreateModal = true;
    }

    public function openEditModal(int $assignmentId): void
    {
        $this->selectedAssignment = Assignment::findOrFail($assignmentId);
        $this->authorize('update', $this->selectedAssignment);
        $this->showEditModal = true;
    }

    public function openTerminateModal(int $assignmentId): void
    {
        $this->selectedAssignment = Assignment::findOrFail($assignmentId);
        $this->authorize('update', $this->selectedAssignment);

        if (!$this->selectedAssignment->is_ongoing) {
            $this->addError('terminate', 'Cette affectation ne peut pas être terminée.');
            return;
        }

        $this->terminateDateTime = now()->format('Y-m-d\TH:i');
        $this->terminateNotes = '';
        $this->showTerminateModal = true;
    }

    public function openDeleteModal(int $assignmentId): void
    {
        $this->selectedAssignment = Assignment::findOrFail($assignmentId);
        $this->authorize('delete', $this->selectedAssignment);
        $this->showDeleteModal = true;
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showTerminateModal = false;
        $this->showDeleteModal = false;
        $this->selectedAssignment = null;
        $this->resetErrorBag();
    }

    /**
     * Terminer une affectation
     */
    public function terminateAssignment(): void
    {
        $this->validate([
            'terminateDateTime' => 'required|date|after_or_equal:' . $this->selectedAssignment->start_datetime,
            'terminateNotes' => 'nullable|string|max:1000'
        ], [
            'terminateDateTime.required' => 'La date de fin est obligatoire.',
            'terminateDateTime.after_or_equal' => 'La date de fin doit être postérieure au début de l\'affectation.',
        ]);

        $this->selectedAssignment->update([
            'end_datetime' => Carbon::parse($this->terminateDateTime),
            'notes' => $this->terminateNotes ?
                ($this->selectedAssignment->notes ? $this->selectedAssignment->notes . "\n\nTerminaison: " . $this->terminateNotes : "Terminaison: " . $this->terminateNotes) :
                $this->selectedAssignment->notes
        ]);

        $this->dispatch('assignment-terminated', [
            'message' => 'Affectation terminée avec succès.',
            'assignment_id' => $this->selectedAssignment->id
        ]);

        $this->closeModals();
    }

    /**
     * Supprimer une affectation
     */
    public function deleteAssignment(): void
    {
        $this->selectedAssignment->delete();

        $this->dispatch('assignment-deleted', [
            'message' => 'Affectation supprimée avec succès.',
            'assignment_id' => $this->selectedAssignment->id
        ]);

        $this->closeModals();
    }

    /**
     * Dupliquer une affectation
     */
    public function duplicateAssignment(int $assignmentId): void
    {
        $assignment = Assignment::findOrFail($assignmentId);
        $this->authorize('create', Assignment::class);

        // Créer une nouvelle affectation basée sur l'existante
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
     * Export CSV
     */
    public function exportCsv(): void
    {
        $this->authorize('viewAny', Assignment::class);

        // Récupérer toutes les affectations filtrées (sans pagination)
        $query = Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator']);

        $this->applyFilters($query);
        $this->applySorting($query);

        $assignments = $query->get();

        $fileName = 'affectations_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $this->dispatch('download-csv', [
            'filename' => $fileName,
            'data' => $this->prepareCsvData($assignments)
        ]);
    }

    private function prepareCsvData($assignments): array
    {
        $data = [
            ['Véhicule', 'Chauffeur', 'Date remise', 'Date restitution', 'Statut', 'Durée', 'Motif', 'Notes', 'Créé par', 'Créé le']
        ];

        foreach ($assignments as $assignment) {
            $data[] = [
                $assignment->vehicle_display,
                $assignment->driver_display,
                $assignment->start_datetime->format('d/m/Y H:i'),
                $assignment->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé',
                $assignment->status_label,
                $assignment->formatted_duration,
                $assignment->reason ?? '',
                $assignment->notes ?? '',
                $assignment->creator?->name ?? '',
                $assignment->created_at->format('d/m/Y H:i')
            ];
        }

        return $data;
    }

    /**
     * Événements Livewire
     */
    #[On('assignment-created')]
    #[On('assignment-updated')]
    public function refreshTable(): void
    {
        // Le tableau se rafraîchit automatiquement
    }

    /**
     * Recherche en temps réel
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedVehicleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDriverFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFromFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateToFilter(): void
    {
        $this->resetPage();
    }

    public function updatedOnlyOngoing(): void
    {
        $this->resetPage();
    }
}