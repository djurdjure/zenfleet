<?php

namespace App\Livewire\Admin\VehicleExpenses;

use App\Models\VehicleExpense;
use App\Models\Vehicle;
use App\Models\Supplier;
use App\Models\ExpenseGroup;
use App\Services\VehicleExpenseService;
use App\Services\ExpenseAnalyticsService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExpenseManager extends Component
{
    use WithPagination, AuthorizesRequests;

    // Filtres
    public $search = '';
    public $status = '';
    public $filter = '';
    public $vehicle_id = '';
    public $supplier_id = '';
    public $expense_group_id = '';
    public $category = '';
    public $payment_status = '';
    public $approval_status = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $sortField = 'expense_date';
    public $sortDirection = 'desc';
    public int $perPage = 20;
    
    // Actions en masse
    public $selectedExpenses = [];
    public $selectAll = false;
    
    // Modals
    public $showDeleteModal = false;
    public $showExportModal = false;
    public $showApprovalModal = false;
    public $expenseToDelete = null;
    public $expenseToApprove = null;
    
    // Stats
    public $totalAmount = 0;
    public $pendingCount = 0;
    public $approvedCount = 0;
    public $monthlyAverage = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'filter' => ['except' => ''],
        'vehicle_id' => ['except' => ''],
        'supplier_id' => ['except' => ''],
        'category' => ['except' => ''],
        'approval_status' => ['except' => ''],
        'perPage' => ['except' => 20],
    ];

    protected array $resetPageOnUpdate = [
        'search',
        'status',
        'filter',
        'vehicle_id',
        'supplier_id',
        'expense_group_id',
        'category',
        'payment_status',
        'approval_status',
        'dateFrom',
        'dateTo',
    ];

    protected $listeners = [
        'refreshExpensesList' => '$refresh',
        'expenseCreated' => 'handleExpenseCreated',
        'expenseUpdated' => 'handleExpenseUpdated',
        'expenseDeleted' => 'handleExpenseDeleted',
    ];

    public function mount()
    {
        $this->authorize('viewAny', VehicleExpense::class);
        $this->loadStatistics();
        $this->filter = request()->get('filter', $this->filter);
    }

    public function updating($property)
    {
        if (in_array($property, $this->resetPageOnUpdate, true)) {
            $this->resetPage();
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $expenses = $this->getFilteredExpenses();
        
        $vehicles = Vehicle::where('organization_id', Auth::user()->organization_id)
            ->orderBy('registration_plate')
            ->get();
            
        $suppliers = Supplier::where('organization_id', Auth::user()->organization_id)
            ->orderBy('company_name')
            ->get();
            
        $expenseGroups = ExpenseGroup::where('organization_id', Auth::user()->organization_id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('livewire.admin.vehicle-expenses.expense-manager', [
            'expenses' => $expenses,
            'vehicles' => $vehicles,
            'suppliers' => $suppliers,
            'expenseGroups' => $expenseGroups,
            'categories' => VehicleExpense::EXPENSE_CATEGORIES,
        ])->extends('layouts.admin.catalyst')->section('content');
    }

    private function getFilteredExpenses()
    {
        $query = VehicleExpense::with([
                'vehicle', 
                'driver', 
                'supplier',
                'expenseGroup',
                'level1Approver',
                'level2Approver'
            ])
            ->where('organization_id', Auth::user()->organization_id);

        // Recherche
        if ($this->search) {
            $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhere('receipt_number', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('expense_type', 'like', '%' . $this->search . '%')
                    ->orWhere('expense_subtype', 'like', '%' . $this->search . '%')
                    ->orWhereHas('vehicle', function ($q) {
                        $q->where('registration_plate', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('company_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filtres
        if ($this->vehicle_id) {
            $query->where('vehicle_id', $this->vehicle_id);
        }

        if ($this->supplier_id) {
            $query->where('supplier_id', $this->supplier_id);
        }
        
        if ($this->expense_group_id) {
            $query->where('expense_group_id', $this->expense_group_id);
        }

        if ($this->category) {
            $query->where('expense_category', $this->category);
        }

        if ($this->payment_status) {
            $query->where('payment_status', $this->payment_status);
        }

        if ($this->approval_status) {
            $query->where('approval_status', $this->approval_status);
        }

        if ($this->filter === 'pending_approval' && !$this->approval_status) {
            $query->whereIn('approval_status', [
                VehicleExpense::APPROVAL_PENDING_LEVEL1,
                VehicleExpense::APPROVAL_PENDING_LEVEL2,
            ]);
        }

        if ($this->dateFrom) {
            $query->whereDate('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('expense_date', '<=', $this->dateTo);
        }

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function loadStatistics()
    {
        $stats = VehicleExpense::where('organization_id', Auth::user()->organization_id)
            ->selectRaw('
                SUM(total_ttc) as total,
                COUNT(CASE WHEN approval_status = ? THEN 1 END) as pending,
                COUNT(CASE WHEN approval_status = ? THEN 1 END) as approved
            ', ['pending_level1', 'approved'])
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->first();

        $this->totalAmount = $stats->total ?? 0;
        $this->pendingCount = $stats->pending ?? 0;
        $this->approvedCount = $stats->approved ?? 0;

        // Moyenne mensuelle sur 6 mois
        $sixMonthsAgo = now()->subMonths(6);
        $avgStats = VehicleExpense::where('organization_id', Auth::user()->organization_id)
            ->where('expense_date', '>=', $sixMonthsAgo)
            ->avg('total_ttc');
        
        $this->monthlyAverage = $avgStats ?? 0;
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

    public function resetFilters()
    {
        $this->reset([
            'search',
            'status',
            'filter',
            'vehicle_id',
            'supplier_id',
            'expense_group_id',
            'category',
            'payment_status',
            'approval_status',
            'dateFrom',
            'dateTo'
        ]);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedExpenses = $this->getFilteredExpenses()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedExpenses = [];
        }
    }

    public function confirmDelete($expenseId)
    {
        $this->expenseToDelete = VehicleExpense::find($expenseId);
        if ($this->expenseToDelete) {
            $this->authorize('delete', $this->expenseToDelete);
        }
        $this->showDeleteModal = true;
    }

    public function deleteExpense()
    {
        if ($this->expenseToDelete) {
            $this->authorize('delete', $this->expenseToDelete);
            
            DB::beginTransaction();
            try {
                $this->expenseToDelete->delete();
                DB::commit();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Dépense supprimée avec succès.'
                ]);
                
                $this->loadStatistics();
                $this->showDeleteModal = false;
                $this->expenseToDelete = null;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function deleteSelected()
    {
        if (count($this->selectedExpenses) > 0) {
            DB::beginTransaction();
            try {
                $expenses = VehicleExpense::whereIn('id', $this->selectedExpenses)
                    ->where('organization_id', Auth::user()->organization_id)
                    ->get();

                $deletedCount = 0;
                foreach ($expenses as $expense) {
                    if (Auth::user()->can('delete', $expense)) {
                        $expense->delete();
                        $deletedCount++;
                    }
                }

                DB::commit();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => $deletedCount . ' dépenses supprimées avec succès.'
                ]);
                
                $this->selectedExpenses = [];
                $this->selectAll = false;
                $this->loadStatistics();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function exportSelected($format = 'csv')
    {
        $this->authorize('export', VehicleExpense::class);
        $service = app(VehicleExpenseService::class);
        
        try {
            $expenses = VehicleExpense::whereIn('id', $this->selectedExpenses)
                ->where('organization_id', Auth::user()->organization_id)
                ->get();
            
            $exportPath = $service->export($expenses, $format);
            
            // Télécharger le fichier
            return response()->download($exportPath)->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ]);
        }
    }

    public function approveExpense($expenseId)
    {
        $expense = VehicleExpense::find($expenseId);
        
        if ($expense) {
            $this->authorize('approve', $expense);
            
            DB::beginTransaction();
            try {
                $service = app(\App\Services\ExpenseApprovalService::class);
                $service->approve($expense, Auth::user(), 'Approuvé via le gestionnaire');
                
                DB::commit();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Dépense approuvée avec succès.'
                ]);
                
                $this->loadStatistics();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors de l\'approbation: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function rejectExpense($expenseId, $reason)
    {
        $expense = VehicleExpense::find($expenseId);
        
        if ($expense) {
            $this->authorize('approve', $expense);
            
            DB::beginTransaction();
            try {
                $service = app(\App\Services\ExpenseApprovalService::class);
                $service->reject($expense, Auth::user(), $reason);
                
                DB::commit();
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Dépense rejetée.'
                ]);
                
                $this->loadStatistics();
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Erreur lors du rejet: ' . $e->getMessage()
                ]);
            }
        }
    }

    // Handlers pour les événements
    public function handleExpenseCreated()
    {
        $this->loadStatistics();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Nouvelle dépense créée avec succès.'
        ]);
    }

    public function handleExpenseUpdated()
    {
        $this->loadStatistics();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Dépense mise à jour avec succès.'
        ]);
    }

    public function handleExpenseDeleted()
    {
        $this->loadStatistics();
        $this->resetPage();
    }
}
