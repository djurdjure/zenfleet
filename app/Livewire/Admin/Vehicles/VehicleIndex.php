<?php

namespace App\Livewire\Admin\Vehicles;

use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\Depot;
use App\Models\VehicleType;
use App\Models\FuelType;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

/**
 * 🚗 VEHICLE INDEX - ENTERPRISE LIVEWIRE COMPONENT
 * 
 * Remplace le "God Controller" par une approche moderne et réactive.
 * Intègre la logique de filtrage, tri, et actions de masse.
 */
class VehicleIndex extends Component
{
    use WithPagination;

    // 🔍 Filtres
    public $search = '';
    public $status_id = '';
    public $vehicle_type_id = '';
    public $fuel_type_id = '';
    public $depot_id = '';
    public $visibility = 'active'; // active, archived

    public $per_page = 20;

    // ↕️ Tri
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // 📦 Sélection & Bulk Actions
    public $selectedVehicles = [];
    public $selectAll = false;

    // 🛡️ Modal States (Entangled)
    public $bulkDepotId = '';
    public $bulkStatusId = null;
    public $showBulkDepotModal = false;
    public $showBulkStatusModal = false;
    public $showBulkArchiveModal = false;

    // Individual Actions States - Boolean flags + ID storage
    public ?int $restoringVehicleId = null;
    public bool $showRestoreModal = false;

    public ?int $forceDeletingVehicleId = null;
    public bool $showForceDeleteModal = false;

    public ?int $archivingVehicleId = null;
    public bool $showArchiveModal = false;

    // 🔄 Individual Status Change State
    public ?int $individualStatusVehicleId = null;
    public ?int $individualStatusId = null;
    public bool $showIndividualStatusModal = false;

    // 🧠 Computed Properties for Modals
    public function getRestoringVehicleProperty()
    {
        return $this->restoringVehicleId ? Vehicle::withTrashed()->find($this->restoringVehicleId) : null;
    }

    public function getForceDeletingVehicleProperty()
    {
        return $this->forceDeletingVehicleId ? Vehicle::withTrashed()->find($this->forceDeletingVehicleId) : null;
    }

    public function getArchivingVehicleProperty()
    {
        return $this->archivingVehicleId ? Vehicle::find($this->archivingVehicleId) : null;
    }

    public function getIndividualStatusVehicleProperty()
    {
        return $this->individualStatusVehicleId ? Vehicle::find($this->individualStatusVehicleId) : null;
    }

    // 🔄 Query String
    protected $queryString = [
        'search' => ['except' => ''],
        'status_id' => ['except' => ''],
        'vehicle_type_id' => ['except' => ''],
        'fuel_type_id' => ['except' => ''],
        'depot_id' => ['except' => ''],
        'visibility' => ['except' => 'active'],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedVisibility()
    {
        $this->resetPage();
    }

    /**
     * 🔄 Reset all filters to default values
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->status_id = '';
        $this->vehicle_type_id = '';
        $this->fuel_type_id = '';
        $this->depot_id = '';
        $this->visibility = 'active';
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
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
    }

    // --- BULK ACTIONS LOGIC (Migrated from VehicleBulkActions) ---

    public function toggleSelection($id)
    {
        if (in_array($id, $this->selectedVehicles)) {
            $this->selectedVehicles = array_diff($this->selectedVehicles, [$id]);
        } else {
            $this->selectedVehicles[] = $id;
        }
        $this->selectAll = false;
    }

    public function toggleAll()
    {
        $this->selectAll = !$this->selectAll;
        if ($this->selectAll) {
            // Sélectionner tous les IDs de la page courante
            $this->selectedVehicles = $this->getVehiclesQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedVehicles = [];
        }
    }

    public function bulkAssignDepot()
    {
        $this->validate([
            'bulkDepotId' => ['required', Rule::exists(Depot::class, 'id')],
            'selectedVehicles' => 'required|array|min:1'
        ]);

        if (empty($this->selectedVehicles)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun véhicule sélectionné']);
            return;
        }

        Vehicle::whereIn('id', $this->selectedVehicles)->update(['depot_id' => $this->bulkDepotId]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => count($this->selectedVehicles) . ' véhicule(s) affecté(s) au dépôt'
        ]);

        $this->resetBulkState();
    }

    public function bulkChangeStatus()
    {
        $this->validate([
            'bulkStatusId' => ['required', Rule::exists(VehicleStatus::class, 'id')],
            'selectedVehicles' => 'required|array|min:1'
        ]);

        if (empty($this->selectedVehicles)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun véhicule sélectionné']);
            return;
        }

        Vehicle::whereIn('id', $this->selectedVehicles)->update(['status_id' => $this->bulkStatusId]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => count($this->selectedVehicles) . ' véhicule(s) mis à jour'
        ]);

        $this->resetBulkState();
    }

    public function bulkArchive()
    {
        if (empty($this->selectedVehicles)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun véhicule sélectionné']);
            return;
        }

        Vehicle::whereIn('id', $this->selectedVehicles)->update(['is_archived' => true]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => count($this->selectedVehicles) . ' véhicule(s) archivé(s)'
        ]);

        $this->resetBulkState();
    }

    public function bulkRestore()
    {
        if (empty($this->selectedVehicles)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun véhicule sélectionné']);
            return;
        }

        Vehicle::withTrashed()->whereIn('id', $this->selectedVehicles)->update(['is_archived' => false]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => count($this->selectedVehicles) . ' véhicule(s) restauré(s)'
        ]);

        $this->resetBulkState();
    }

    public function bulkForceDelete()
    {
        if (empty($this->selectedVehicles)) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Aucun véhicule sélectionné']);
            return;
        }

        $vehicles = Vehicle::withTrashed()->whereIn('id', $this->selectedVehicles)->get();

        foreach ($vehicles as $vehicle) {
            $vehicle->forceDelete();
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => count($this->selectedVehicles) . ' véhicule(s) supprimé(s) définitivement'
        ]);

        $this->resetBulkState();
    }

    protected function resetBulkState()
    {
        $this->selectedVehicles = [];
        $this->selectAll = false;
        $this->bulkDepotId = '';
        $this->bulkStatusId = null;
        $this->showBulkDepotModal = false;
        $this->showBulkStatusModal = false;
        $this->showBulkArchiveModal = false;
        $this->showIndividualStatusModal = false;
    }

    // --- INDIVIDUAL ACTIONS ---

    /**
     * Toggle visibility view
     */
    public function setVisibility(string $value): void
    {
        $this->visibility = $value;
        $this->resetPage();
    }

    // --- INDIVIDUAL ACTIONS: RESTORE ---

    public function confirmRestore(int $id): void
    {
        $this->restoringVehicleId = $id;
        $this->showRestoreModal = true;
    }

    public function cancelRestore(): void
    {
        $this->restoringVehicleId = null;
        $this->showRestoreModal = false;
    }

    public function restoreVehicle(): void
    {
        if (!$this->restoringVehicleId) {
            $this->cancelRestore();
            return;
        }

        $vehicle = Vehicle::where('is_archived', true)->find($this->restoringVehicleId);

        if ($vehicle) {
            $vehicle->update(['is_archived' => false]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Véhicule restauré avec succès']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Véhicule introuvable']);
        }

        $this->cancelRestore();
    }

    // --- INDIVIDUAL ACTIONS: FORCE DELETE ---

    public function confirmForceDelete(int $id): void
    {
        $this->forceDeletingVehicleId = $id;
        $this->showForceDeleteModal = true;
    }

    public function cancelForceDelete(): void
    {
        $this->forceDeletingVehicleId = null;
        $this->showForceDeleteModal = false;
    }

    public function forceDeleteVehicle(): void
    {
        if (!$this->forceDeletingVehicleId) {
            $this->cancelForceDelete();
            return;
        }

        $vehicle = Vehicle::withTrashed()->find($this->forceDeletingVehicleId);

        if ($vehicle) {
            $vehicle->forceDelete();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Véhicule supprimé définitivement']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Véhicule introuvable']);
        }

        $this->cancelForceDelete();
    }

    // --- INDIVIDUAL ACTIONS: ARCHIVE ---

    public function confirmArchive(int $id): void
    {
        $this->archivingVehicleId = $id;
        $this->showArchiveModal = true;
    }

    public function cancelArchive(): void
    {
        $this->archivingVehicleId = null;
        $this->showArchiveModal = false;
    }

    public function archiveVehicle(): void
    {
        if (!$this->archivingVehicleId) {
            $this->cancelArchive();
            return;
        }

        $vehicle = Vehicle::where('is_archived', false)->find($this->archivingVehicleId);

        if ($vehicle) {
            $vehicle->update(['is_archived' => true]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Véhicule archivé avec succès']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Véhicule introuvable']);
        }

        $this->cancelArchive();
    }

    // --- INDIVIDUAL ACTIONS: CHANGE STATUS ---

    public function confirmIndividualStatusChange(int $id): void
    {
        $this->individualStatusVehicleId = $id;
        $vehicle = Vehicle::find($id);
        $this->individualStatusId = $vehicle ? $vehicle->status_id : null;
        $this->showIndividualStatusModal = true;
    }

    public function cancelIndividualStatusChange(): void
    {
        $this->individualStatusVehicleId = null;
        $this->individualStatusId = null;
        $this->showIndividualStatusModal = false;
    }

    public function updateIndividualStatus(): void
    {
        $this->validate([
            'individualStatusId' => ['required', Rule::exists(VehicleStatus::class, 'id')],
        ]);

        if (!$this->individualStatusVehicleId) {
            $this->cancelIndividualStatusChange();
            return;
        }

        $vehicle = Vehicle::find($this->individualStatusVehicleId);

        if ($vehicle) {
            $vehicle->update(['status_id' => $this->individualStatusId]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Statut du véhicule mis à jour']);
        } else {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Véhicule introuvable']);
        }

        $this->cancelIndividualStatusChange();
    }

    // --- EXPORT ACTIONS ---

    /**
     * 📊 Helper to gather current filters for exports
     */
    protected function getFilters(): array
    {
        return [
            'search' => $this->search,
            'status_id' => $this->status_id,
            'vehicle_type_id' => $this->vehicle_type_id,
            'fuel_type_id' => $this->fuel_type_id,
            'depot_id' => $this->depot_id,
            'visibility' => $this->visibility,
            'sort_by' => $this->sortField,
            'sort_direction' => $this->sortDirection,
            'vehicles' => $this->selectedVehicles // Support for exporting selected only
        ];
    }

    /**
     * 📄 Export to PDF using Microservice Enterprise (via Controller)
     * 
     * Solution: Livewire ne peut pas retourner du contenu binaire directement.
     * On redirige vers un contrôleur qui gère le téléchargement.
     */
    public function exportPdf()
    {
        try {
            // Stocker les filtres en session pour le contrôleur
            session(['vehicle_export_filters' => $this->getFilters()]);

            // Rediriger vers la route de téléchargement
            return redirect()->route('admin.vehicles.export.pdf');
        } catch (\Exception $e) {
            Log::error('Export PDF véhicules échoué', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export PDF: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 📗 Export to Excel using Maatwebsite
     */
    public function exportExcel()
    {
        try {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\VehiclesExport($this->getFilters()),
                'vehicules_' . date('Y-m-d_H-i') . '.xlsx'
            );
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export Excel: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 📊 Export to CSV - Redirect to Controller
     * 
     * Même pattern que PDF pour cohérence et fiabilité
     */
    public function exportCsv()
    {
        try {
            // Stocker les filtres en session pour le contrôleur
            session(['vehicle_export_filters' => $this->getFilters()]);

            // Rediriger vers la route de téléchargement
            return redirect()->route('admin.vehicles.export.csv');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export CSV: ' . $e->getMessage()
            ]);
        }
    }

    // --- DATA FETCHING ---

    public function getVehiclesQuery(): Builder
    {
        $query = Vehicle::query()
            ->with([
                'vehicleType',
                'fuelType',
                'transmissionType',
                'vehicleStatus',
                'depot',
                // Optimisation N+1 pour le chauffeur actif
                'currentAssignment.driver.user'
            ]);

        // Security Scope is now handled by UserVehicleAccessScope + RLS

        // Filters
        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('registration_plate', 'ilike', "%{$this->search}%")
                    ->orWhere('vin', 'ilike', "%{$this->search}%")
                    ->orWhere('brand', 'ilike', "%{$this->search}%")
                    ->orWhere('model', 'ilike', "%{$this->search}%");
            });
        });

        $query->when($this->status_id, fn($q) => $q->where('status_id', $this->status_id));
        $query->when($this->vehicle_type_id, fn($q) => $q->where('vehicle_type_id', $this->vehicle_type_id));
        $query->when($this->fuel_type_id, fn($q) => $q->where('fuel_type_id', $this->fuel_type_id));
        $query->when($this->depot_id, fn($q) => $q->where('depot_id', $this->depot_id));

        // Visibility Filter
        if ($this->visibility === 'archived') {
            $query->where('is_archived', true);
        } else {
            $query->where('is_archived', false);
        }

        // Sorting
        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $vehicles = $this->getVehiclesQuery()->paginate($this->per_page);

        // Reference Data (Cached)
        $vehicleStatuses = Cache::remember('vehicle_statuses', 3600, fn() => VehicleStatus::orderBy('name')->get());
        $vehicleTypes = Cache::remember('vehicle_types', 3600, fn() => VehicleType::orderBy('name')->get());
        $fuelTypes = Cache::remember('fuel_types', 3600, fn() => FuelType::orderBy('name')->get());
        $depots = Cache::remember(
            'depots_list_' . Auth::user()->organization_id,
            3600,
            fn() =>
            Depot::where('organization_id', Auth::user()->organization_id)->orderBy('name')->get()
        );

        // Analytics (Simplified for now)
        $analytics = [
            'total_vehicles' => Vehicle::count(),
            'available_vehicles' => Vehicle::whereHas('vehicleStatus', fn($q) => $q->where('name', 'Parking'))->count(),
            'assigned_vehicles' => Vehicle::whereHas('assignments', fn($q) => $q->where('status', 'active'))->count(),
            'maintenance_vehicles' => Vehicle::whereHas('vehicleStatus', fn($q) => $q->where('name', 'En maintenance'))->count(),
            'broken_vehicles' => Vehicle::whereHas('vehicleStatus', fn($q) => $q->where('name', 'En panne'))->count(),
        ];

        return view('livewire.admin.vehicles.vehicle-index', [
            'vehicles' => $vehicles,
            'vehicleStatuses' => $vehicleStatuses,
            'vehicleTypes' => $vehicleTypes,
            'fuelTypes' => $fuelTypes,
            'depots' => $depots,
            'analytics' => $analytics
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
