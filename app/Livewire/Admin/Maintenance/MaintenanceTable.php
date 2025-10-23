<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Maintenance\MaintenanceService;
use App\Models\MaintenanceOperation;

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
    use WithPagination;

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
    public $perPage = 15;

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
    ];

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

    /**
     * Render component
     */
    public function render(MaintenanceService $maintenanceService)
    {
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

        return view('livewire.admin.maintenance.maintenance-table', [
            'operations' => $operations,
        ]);
    }
}
