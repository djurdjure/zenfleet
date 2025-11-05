<?php

namespace App\Livewire\Depots;

use App\Models\VehicleDepot;
use App\Services\DepotAssignmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ManageDepots Livewire Component
 *
 * Enterprise-grade depot management interface with:
 * - Interactive list with real-time statistics
 * - Advanced filtering and search
 * - CRUD operations with validation
 * - Capacity management
 * - Vehicle assignment tracking
 *
 * @package App\Livewire\Depots
 */
class ManageDepots extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $statusFilter = 'all'; // all, active, inactive
    public $capacityFilter = 'all'; // all, available, full
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Modal state
    public $showModal = false;
    public $modalMode = 'create'; // create, edit, view
    public $selectedDepotId = null;

    // Form fields
    public $name = '';
    public $code = '';
    public $address = '';
    public $city = '';
    public $wilaya = '';
    public $postal_code = '';
    public $phone = '';
    public $email = '';
    public $manager_name = '';
    public $manager_phone = '';
    public $capacity = null;
    public $latitude = null;
    public $longitude = null; // Initialisé à null pour cohérence
    public $description = '';
    public $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'capacityFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:vehicle_depots,code,' . $this->selectedDepotId,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'wilaya' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1|max:10000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function mount()
    {
        // Initialize component
    }

    public function render()
    {
        $depots = $this->getDepots();
        $stats = $this->getOverallStats();

        return view('livewire.depots.manage-depots', [
            'depots' => $depots,
            'stats' => $stats,
        ]);
    }

    protected function getDepots()
    {
        $query = VehicleDepot::forOrganization(Auth::user()->organization_id)
            ->withCount('vehicles');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%')
                    ->orWhere('wilaya', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter === 'active') {
            $query->active();
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Capacity filter
        if ($this->capacityFilter === 'available') {
            $query->whereRaw('current_count < capacity')
                ->whereNotNull('capacity');
        } elseif ($this->capacityFilter === 'full') {
            $query->whereRaw('current_count >= capacity')
                ->whereNotNull('capacity');
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    protected function getOverallStats()
    {
        $orgId = Auth::user()->organization_id;

        $allDepots = VehicleDepot::forOrganization($orgId)->get();

        $totalCapacity = $allDepots->sum('capacity');
        $totalOccupied = $allDepots->sum('current_count');
        $totalAvailable = $totalCapacity - $totalOccupied;

        return [
            'total_depots' => $allDepots->count(),
            'active_depots' => $allDepots->where('is_active', true)->count(),
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'total_available' => $totalAvailable,
            'average_occupancy' => $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCapacityFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->showModal = true;
        $this->dispatch('depot-modal-open');
    }

    public function openEditModal($depotId)
    {
        $depot = VehicleDepot::where('id', $depotId)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        $this->selectedDepotId = $depot->id;
        $this->name = $depot->name;
        $this->code = $depot->code;
        $this->address = $depot->address;
        $this->city = $depot->city;
        $this->wilaya = $depot->wilaya;
        $this->postal_code = $depot->postal_code;
        $this->phone = $depot->phone;
        $this->email = $depot->email;
        $this->manager_name = $depot->manager_name;
        $this->manager_phone = $depot->manager_phone;
        $this->capacity = $depot->capacity;
        $this->latitude = $depot->latitude;
        $this->longitude = $depot->longitude;
        $this->description = $depot->description;
        $this->is_active = $depot->is_active;

        $this->modalMode = 'edit';
        $this->showModal = true;
        $this->dispatch('depot-modal-open');
    }

    public function openViewModal($depotId)
    {
        $depot = VehicleDepot::where('id', $depotId)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        $this->selectedDepotId = $depot->id;
        $this->name = $depot->name;
        $this->code = $depot->code;
        $this->address = $depot->address;
        $this->city = $depot->city;
        $this->wilaya = $depot->wilaya;
        $this->postal_code = $depot->postal_code;
        $this->phone = $depot->phone;
        $this->email = $depot->email;
        $this->manager_name = $depot->manager_name;
        $this->manager_phone = $depot->manager_phone;
        $this->capacity = $depot->capacity;
        $this->latitude = $depot->latitude;
        $this->longitude = $depot->longitude;
        $this->description = $depot->description;
        $this->is_active = $depot->is_active;

        $this->modalMode = 'view';
        $this->showModal = true;
        $this->dispatch('depot-modal-open');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
        $this->dispatch('depot-modal-close');
    }

    public function save()
    {
        $this->validate();

        // Auto-generate code if empty (Enterprise-grade feature)
        if (empty($this->code)) {
            $this->code = $this->generateDepotCode();
        }

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'wilaya' => $this->wilaya,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'manager_name' => $this->manager_name,
            'manager_phone' => $this->manager_phone,
            'capacity' => $this->capacity ? (int) $this->capacity : null, // Cast explicite en integer
            'latitude' => $this->latitude ? (float) $this->latitude : null, // Cast explicite en float
            'longitude' => $this->longitude ? (float) $this->longitude : null, // Cast explicite en float
            'description' => $this->description,
            'is_active' => (bool) $this->is_active, // Cast explicite en boolean
            'organization_id' => Auth::user()->organization_id,
        ];

        try {
            if ($this->modalMode === 'create') {
                $data['current_count'] = 0;
                $depot = VehicleDepot::create($data);
                
                \Log::info('Dépôt créé avec succès', [
                    'depot_id' => $depot->id,
                    'depot_name' => $depot->name,
                    'depot_code' => $depot->code,
                    'organization_id' => $depot->organization_id
                ]);
                
                session()->flash('success', 'Dépôt créé avec succès');
            } else {
                $depot = VehicleDepot::where('id', $this->selectedDepotId)
                    ->where('organization_id', Auth::user()->organization_id)
                    ->firstOrFail();

                $depot->update($data);
                
                \Log::info('Dépôt mis à jour avec succès', [
                    'depot_id' => $depot->id,
                    'depot_name' => $depot->name,
                ]);
                
                session()->flash('success', 'Dépôt mis à jour avec succès');
            }

            $this->closeModal();

            // Réinitialiser la pagination à la première page pour voir le nouveau dépôt
            $this->resetPage();

            // Force refresh to display new depot
            $this->dispatch('depot-saved');

        } catch (\Exception $e) {
            // Ne PAS fermer le modal en cas d'erreur - Enterprise UX
            \Log::error('Erreur enregistrement dépôt', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);
            
            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    /**
     * Generate unique depot code
     * Enterprise-grade auto-generation with collision prevention
     */
    protected function generateDepotCode(): string
    {
        $orgId = Auth::user()->organization_id;
        $prefix = 'DP';
        
        // Find the highest existing code number for this organization
        // PostgreSQL-compatible query (use INTEGER instead of UNSIGNED)
        $lastDepot = VehicleDepot::forOrganization($orgId)
            ->whereNotNull('code')
            ->where('code', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(code, 3) AS INTEGER) DESC')
            ->first();
        
        if ($lastDepot && preg_match('/^DP(\d+)$/', $lastDepot->code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Collision prevention - ensure uniqueness
        $attempts = 0;
        while (VehicleDepot::forOrganization($orgId)->where('code', $code)->exists() && $attempts < 10) {
            $nextNumber++;
            $code = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $code;
    }

    public function delete($depotId)
    {
        $depot = VehicleDepot::where('id', $depotId)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        // Check if depot has vehicles
        if ($depot->current_count > 0) {
            session()->flash('error', 'Impossible de supprimer un dépôt avec des véhicules affectés');
            return;
        }

        $depot->delete();
        session()->flash('success', 'Dépôt supprimé avec succès');
    }

    public function toggleActive($depotId)
    {
        $depot = VehicleDepot::where('id', $depotId)
            ->where('organization_id', Auth::user()->organization_id)
            ->firstOrFail();

        $depot->is_active = !$depot->is_active;
        $depot->save();

        session()->flash('success', $depot->is_active ? 'Dépôt activé' : 'Dépôt désactivé');
    }

    protected function resetForm()
    {
        $this->selectedDepotId = null;
        $this->name = '';
        $this->code = '';
        $this->address = '';
        $this->city = '';
        $this->wilaya = '';
        $this->postal_code = '';
        $this->phone = '';
        $this->email = '';
        $this->manager_name = '';
        $this->manager_phone = '';
        $this->capacity = null;
        $this->latitude = null;
        $this->longitude = null; // Reset à null pour cohérence avec l'initialisation
        $this->description = '';
        $this->is_active = true;
    }
}
