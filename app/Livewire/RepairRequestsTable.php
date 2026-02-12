<?php

namespace App\Livewire;

use App\Models\RepairRequest;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * RepairRequestsTable - Table interactive des demandes de réparation
 *
 * Features:
 * - Filtrage temps réel (search, status, urgency)
 * - Pagination
 * - Scopes par rôle (Driver: own, Supervisor: team, Admin/Fleet: all)
 * - Tri multi-colonnes
 * - Actions contextuelles selon permissions
 *
 * @version 1.0-Livewire3
 */
class RepairRequestsTable extends Component
{
    use WithPagination;

    /**
     * 🔍 PROPRIÉTÉS DE RECHERCHE ET FILTRES
     */
    public string $search = '';
    public string $statusFilter = '';
    public string $urgencyFilter = '';
    public string $driverFilter = '';
    public string $vehicleFilter = '';

    /**
     * 📊 PROPRIÉTÉS DE TRI ET PAGINATION
     */
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 15;

    /**
     * 🎛️ LISTENERS POUR ÉVÉNEMENTS
     */
    protected $listeners = [
        'repair-request-updated' => '$refresh',
        'refresh-table' => '$refresh',
    ];

    /**
     * 🔄 RESET PAGINATION QUAND FILTRES CHANGENT
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

    public function updatingDriverFilter(): void
    {
        $this->resetPage();
    }

    public function updatingVehicleFilter(): void
    {
        $this->resetPage();
    }

    /**
     * 📊 TRI DES COLONNES
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
     * 🔄 RESET TOUS LES FILTRES
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'urgencyFilter',
            'driverFilter',
            'vehicleFilter',
            'sortField',
            'sortDirection',
        ]);
        $this->resetPage();
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

        // 🔐 FILTRAGE PAR RÔLE
        if ($user->isDriverOnly()) {
            // Driver: own requests only
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasAnyRole(['Supervisor', 'Superviseur'])) {
            // Supervisor: team requests only
            $query->whereHas('driver', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            });
        }
        // Admin/Fleet Manager/Super Admin: all in organization (no additional filter)

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

        if (!empty($this->driverFilter)) {
            $query->where('driver_id', $this->driverFilter);
        }

        if (!empty($this->vehicleFilter)) {
            $query->where('vehicle_id', $this->vehicleFilter);
        }

        // 📊 TRI
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * 📄 STATUTS DISPONIBLES
     */
    public function getStatusesProperty(): array
    {
        return [
            RepairRequest::STATUS_PENDING_SUPERVISOR => 'En attente superviseur',
            RepairRequest::STATUS_APPROVED_SUPERVISOR => 'Approuvé superviseur',
            RepairRequest::STATUS_REJECTED_SUPERVISOR => 'Rejeté superviseur',
            RepairRequest::STATUS_PENDING_FLEET_MANAGER => 'En attente gestionnaire',
            RepairRequest::STATUS_APPROVED_FINAL => 'Approuvé final',
            RepairRequest::STATUS_REJECTED_FINAL => 'Rejeté final',
        ];
    }

    /**
     * 🚨 NIVEAUX D'URGENCE
     */
    public function getUrgencyLevelsProperty(): array
    {
        return [
            RepairRequest::URGENCY_LOW => 'Faible',
            RepairRequest::URGENCY_NORMAL => 'Normal',
            RepairRequest::URGENCY_HIGH => 'Élevé',
            RepairRequest::URGENCY_CRITICAL => 'Critique',
        ];
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.repair-requests-table', [
            'repairRequests' => $this->repairRequests,
            'statuses' => $this->statuses,
            'urgencyLevels' => $this->urgencyLevels,
        ]);
    }
}
