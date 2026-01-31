<?php

// app/Livewire/Admin/OrganizationTable.php

namespace App\Livewire\Admin;

use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $wilaya = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $sortField = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public array $selectedOrganizations = [];

    public bool $selectAll = false;
    public int $perPage = 20;
    public bool $showDeleteModal = false;
    public ?int $deleteOrganizationId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'wilaya' => ['except' => ''],
        'type' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 20],
    ];

    public function mount($initialFilters = [])
    {
        $this->search = $initialFilters['search'] ?? '';
        $this->status = $initialFilters['status'] ?? '';
        $this->wilaya = $initialFilters['wilaya'] ?? '';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedWilaya()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleStatus($organizationId)
    {
        $organization = Organization::find($organizationId);
        if ($organization) {
            $newStatus = $organization->status === 'active' ? 'inactive' : 'active';
            $organization->update(['status' => $newStatus]);

            $this->dispatch('status-updated', [
                'message' => "Statut de {$organization->name} mis à jour",
                'type' => 'success',
            ]);
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedOrganizations)) {
            return;
        }

        Organization::whereIn('id', $this->selectedOrganizations)->delete();
        $this->selectedOrganizations = [];
        $this->selectAll = false;

        $this->dispatch('bulk-action-completed', [
            'message' => count($this->selectedOrganizations).' organisations supprimées',
            'type' => 'success',
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedOrganizations = $this->organizations->pluck('id')->toArray();
        } else {
            $this->selectedOrganizations = [];
        }
    }

    public function confirmDelete(int $organizationId): void
    {
        $this->deleteOrganizationId = $organizationId;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteOrganizationId = null;
    }

    public function deleteOrganization(): void
    {
        if (!$this->deleteOrganizationId) {
            return;
        }

        $organization = Organization::find($this->deleteOrganizationId);
        if ($organization) {
            $organization->delete();
        }

        $this->cancelDelete();
        $this->dispatch('status-updated', [
            'message' => 'Organisation supprimée avec succès',
            'type' => 'success',
        ]);
    }

    public function getDeletingOrganizationProperty(): ?Organization
    {
        if (!$this->deleteOrganizationId) {
            return null;
        }

        return Organization::find($this->deleteOrganizationId);
    }

    #[On('refreshTable')]
    public function refreshTable()
    {
        // Force refresh
    }

    public function getOrganizationsProperty()
    {
        $query = Organization::query()
            ->withCount([
                'activeUsers as users_count',
                'vehicles',
                'driversModel as drivers_count'
            ]);

        // Filtres
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('legal_name', 'ilike', "%{$this->search}%")
                    ->orWhere('city', 'ilike', "%{$this->search}%")
                    ->orWhere('nif', 'ilike', "%{$this->search}%");
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->wilaya) {
            $query->where('wilaya', $this->wilaya);
        }

        if ($this->type) {
            $query->where('organization_type', $this->type);
        }

        // Tri sécurisé
        $allowedSorts = ['name', 'status', 'created_at', 'users_count', 'vehicles_count', 'drivers_count'];
        if (in_array($this->sortField, $allowedSorts)) {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $analytics = [
            'total' => Organization::count(),
            'active' => Organization::where('status', 'active')->count(),
            'users' => User::count(),
            'vehicles' => Vehicle::count(),
        ];

        return view('livewire.admin.organization-table', [
            'organizations' => $this->organizations,
            'analytics' => $analytics,
            'filterOptions' => [
                'statuses' => [
                    'active' => 'Actif',
                    'inactive' => 'Inactif',
                    'pending' => 'En attente',
                    'suspended' => 'Suspendu',
                ],
                'wilayas' => \App\Models\AlgeriaWilaya::active()
                    ->orderBy('name_fr')
                    ->pluck('name_fr', 'code')
                    ->toArray(),
                'types' => [
                    'enterprise' => 'Grande Entreprise',
                    'sme' => 'PME',
                    'startup' => 'Startup',
                    'public' => 'Secteur Public',
                ],
            ],
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
