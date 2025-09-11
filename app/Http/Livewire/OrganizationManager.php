<?php

namespace App\Http\Livewire;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class OrganizationManager extends Component
{
    use WithPagination;

    // ✅ PROPRIÉTÉS PUBLIQUES OBLIGATOIRES
    public string $search = '';
    public string $statusFilter = 'all';
    public string $cityFilter = 'all';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;
    public bool $showFilters = false;

    // ✅ QUERY STRING pour maintenir les filtres dans l'URL
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'cityFilter' => ['except' => 'all'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // ✅ LISTENERS
    protected $listeners = [
        'organizationUpdated' => 'refreshData',
        'organizationDeleted' => 'refreshData'
    ];

    /**
     * ✅ MÉTHODES DE RÉINITIALISATION DE PAGE
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * ✅ MÉTHODE RENDER ULTRA-ROBUSTE
     */
    public function render()
    {
        try {
            // Construction de la requête avec tous les filtres
            $query = Organization::query();

            // ✅ Filtrage par recherche
            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('city', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            }

            // ✅ Filtrage par statut
            if ($this->statusFilter !== 'all') {
                $query->where('status', $this->statusFilter);
            }

            // ✅ Filtrage par ville
            if ($this->cityFilter !== 'all') {
                $query->where('city', $this->cityFilter);
            }

            // ✅ Tri
            $query->orderBy($this->sortField, $this->sortDirection);

            // ✅ Pagination avec relations
            $organizations = $query->with(['users', 'vehicles', 'drivers'])
                                  ->withCount(['users', 'vehicles', 'drivers'])
                                  ->paginate($this->perPage);

            // ✅ Statistiques
            $stats = [
                'total' => Organization::count(),
                'active' => Organization::where('status', 'active')->count(),
                'pending' => Organization::where('status', 'pending')->count(),
                'inactive' => Organization::where('status', 'inactive')->count(),
            ];

            // ✅ Villes disponibles pour le filtre
            $cities = Organization::distinct()
                        ->whereNotNull('city')
                        ->pluck('city')
                        ->filter()
                        ->sort()
                        ->values();

            Log::info('OrganizationManager rendered successfully', [
                'total_organizations' => $organizations->total(),
                'current_page' => $organizations->currentPage(),
                'filters' => [
                    'search' => $this->search,
                    'status' => $this->statusFilter,
                    'city' => $this->cityFilter,
                ]
            ]);

            return view('livewire.organization-manager', [
                'organizations' => $organizations,
                'stats' => $stats,
                'cities' => $cities,
            ]);

        } catch (\Exception $e) {
            Log::error('OrganizationManager render failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // ✅ FALLBACK D'URGENCE
            return view('livewire.organization-manager', [
                'organizations' => collect()->paginate($this->perPage),
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'pending' => 0,
                    'inactive' => 0,
                ],
                'cities' => collect(),
            ]);
        }
    }

    /**
     * ✅ ACTIONS UTILISATEUR
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->cityFilter = 'all';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function updateStatus($organizationId, $status)
    {
        try {
            $organization = Organization::findOrFail($organizationId);
            $organization->update(['status' => $status]);
            
            $this->emit('organizationUpdated');
            session()->flash('success', 'Statut mis à jour avec succès');
            
        } catch (\Exception $e) {
            Log::error('Update status failed', [
                'organization_id' => $organizationId,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Erreur lors de la mise à jour du statut');
        }
    }

    public function refreshData()
    {
        // Force le rafraîchissement des données
        $this->render();
    }
}
