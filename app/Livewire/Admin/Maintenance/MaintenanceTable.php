<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Maintenance\MaintenanceService;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Supplier;
use App\Models\Vehicle;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * 📊 COMPOSANT TABLE MAINTENANCE ENTERPRISE
 * 
 * Tableau interactif avec tri, pagination, actions inline
 * Pattern: Livewire 3 + Alpine.js
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceTable extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filters
    public $search = '';
    public $status = '';
    public $maintenanceTypeId = '';
    public $providerId = '';
    public $vehicleId = '';
    public $category = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $overdue = false;

    // Sorting
    public $sortField = 'scheduled_date';
    public $sortDirection = 'desc';

    // Pagination
    public int $perPage = 15;

    // Listeners
    protected $listeners = [
        'refreshTable' => '$refresh',
        'operationUpdated' => '$refresh',
        'operationDeleted' => '$refresh',
    ];

    // Query string
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'scheduled_date'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', MaintenanceOperation::class);
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->reset([
            'search',
            'status',
            'maintenanceTypeId',
            'providerId',
            'vehicleId',
            'category',
            'dateFrom',
            'dateTo',
            'overdue'
        ]);

        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Sort by field
     */
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

    /**
     * Update filters
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingMaintenanceTypeId()
    {
        $this->resetPage();
    }

    public function updatingProviderId()
    {
        $this->resetPage();
    }

    public function updatingVehicleId()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingOverdue()
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render(MaintenanceService $maintenanceService)
    {
        $this->authorize('viewAny', MaintenanceOperation::class);

        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'maintenance_type_id' => $this->maintenanceTypeId,
            'provider_id' => $this->providerId,
            'vehicle_id' => $this->vehicleId,
            'category' => $this->category,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'overdue' => $this->overdue,
            'sort' => $this->sortField,
            'direction' => $this->sortDirection,
        ];

        $operations = $maintenanceService->getOperations($filters, $this->perPage);
        $analytics = $maintenanceService->getAnalytics();

        $organizationId = auth()->user()?->organization_id;

        $maintenanceTypes = MaintenanceType::where('organization_id', $organizationId)
            ->orderBy('name')
            ->get(['id', 'name', 'category']);

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->orderBy('registration_plate')
            ->get(['id', 'registration_plate', 'brand', 'model']);

        $maintenanceSupplierTypes = [
            Supplier::TYPE_MECANICIEN,
            Supplier::TYPE_PEINTURE_CARROSSERIE,
            Supplier::TYPE_ELECTRICITE_AUTO,
            Supplier::TYPE_PNEUMATIQUES,
            Supplier::TYPE_CONTROLE_TECHNIQUE,
            Supplier::TYPE_PIECES_DETACHEES,
        ];

        $providers = Supplier::where('organization_id', $organizationId)
            ->whereIn('supplier_type', $maintenanceSupplierTypes)
            ->where('is_active', true)
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'contact_phone']);

        return view('livewire.admin.maintenance.maintenance-table', [
            'operations' => $operations,
            'analytics' => $analytics,
            'maintenanceTypes' => $maintenanceTypes,
            'vehicles' => $vehicles,
            'providers' => $providers,
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
