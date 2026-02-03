<?php

namespace App\Livewire\Admin\Suppliers;

use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Services\SupplierService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierIndex extends Component
{
    use WithPagination, AuthorizesRequests;

    public string $search = '';
    public string $supplier_type = '';
    public string $category_id = '';
    public string $wilaya = '';
    public string $is_active = '';
    public string $is_preferred = '';
    public string $is_certified = '';
    public string $min_rating = '';
    public string $sort_by = 'company_name';
    public string $sort_direction = 'asc';
    public int $perPage = 15;

    protected SupplierService $supplierService;

    protected $queryString = [
        'search' => ['except' => ''],
        'supplier_type' => ['except' => ''],
        'category_id' => ['except' => ''],
        'wilaya' => ['except' => ''],
        'is_active' => ['except' => ''],
        'is_preferred' => ['except' => ''],
        'is_certified' => ['except' => ''],
        'min_rating' => ['except' => ''],
        'sort_by' => ['except' => 'company_name'],
        'sort_direction' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    public function boot(SupplierService $supplierService): void
    {
        $this->supplierService = $supplierService;
    }

    public function mount(): void
    {
        $this->authorize('suppliers.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSupplierType(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingWilaya(): void
    {
        $this->resetPage();
    }

    public function updatingIsActive(): void
    {
        $this->resetPage();
    }

    public function updatingIsPreferred(): void
    {
        $this->resetPage();
    }

    public function updatingIsCertified(): void
    {
        $this->resetPage();
    }

    public function updatingMinRating(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function updatingSortDirection(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'supplier_type',
            'category_id',
            'wilaya',
            'is_active',
            'is_preferred',
            'is_certified',
            'min_rating',
            'sort_by',
            'sort_direction',
        ]);
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('suppliers.view');

        $filters = [
            'search' => $this->search,
            'supplier_type' => $this->supplier_type,
            'category_id' => $this->category_id,
            'wilaya' => $this->wilaya,
            'is_active' => $this->is_active,
            'is_preferred' => $this->is_preferred,
            'is_certified' => $this->is_certified,
            'min_rating' => $this->min_rating,
            'sort_by' => $this->sort_by,
            'sort_direction' => $this->sort_direction,
        ];

        $suppliers = $this->supplierService->getFilteredSuppliersAdvanced($filters, $this->perPage);
        $analytics = $this->supplierService->getAnalytics($filters);

        $categories = SupplierCategory::orderBy('name')->get();
        $types = Supplier::TYPES;
        $wilayas = Supplier::WILAYAS;

        return view('livewire.admin.suppliers.supplier-index', [
            'suppliers' => $suppliers,
            'analytics' => $analytics,
            'categories' => $categories,
            'types' => $types,
            'wilayas' => $wilayas,
        ])->extends('layouts.admin.catalyst')->section('content');
    }
}
