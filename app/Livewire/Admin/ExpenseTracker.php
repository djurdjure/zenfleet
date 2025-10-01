<?php

namespace App\Livewire\Admin;

use App\Models\VehicleExpense;
use App\Models\ExpenseBudget;
use App\Models\Vehicle;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Carbon\Carbon;

class ExpenseTracker extends Component
{
    use WithFileUploads, WithPagination;

    // Propriétés pour les filtres
    public $filterCategory = '';
    public $filterVehicle = '';
    public $filterSupplier = '';
    public $filterStatus = '';
    public $filterPaymentStatus = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $search = '';

    // Propriétés pour les modals
    public $showCreateModal = false;
    public $showDetailsModal = false;
    public $showApprovalModal = false;
    public $showBudgetModal = false;

    // Propriété pour la dépense sélectionnée
    public $selectedExpense = null;

    // Propriétés du formulaire de création
    public $vehicle_id = '';
    public $supplier_id = '';
    public $driver_id = '';
    public $expense_category = '';
    public $expense_type = '';
    public $expense_subtype = '';
    public $amount_ht = '';
    public $tva_rate = 19.00;
    public $invoice_number = '';
    public $invoice_date = '';
    public $receipt_number = '';
    public $fiscal_receipt = false;
    public $odometer_reading = '';
    public $fuel_quantity = '';
    public $fuel_price_per_liter = '';
    public $fuel_type = '';
    public $expense_city = '';
    public $expense_wilaya = '';
    public $expense_date = '';
    public $description = '';
    public $internal_notes = '';
    public $tags = [];
    public $attachments = [];
    public $needs_approval = false;
    public $is_recurring = false;
    public $recurrence_pattern = '';
    public $payment_method = '';

    // Propriétés pour l'approbation
    public $approval_comments = '';

    // Propriétés pour budget
    public $budget_period = 'monthly';
    public $budget_year = '';
    public $budget_month = '';
    public $budget_quarter = '';
    public $budgeted_amount = '';
    public $budget_category = '';
    public $budget_vehicle_id = '';

    // Vue sélectionnée
    public $viewType = 'table';

    // Données de référence
    public $vehicles = [];
    public $suppliers = [];
    public $drivers = [];

    protected $rules = [
        'vehicle_id' => 'required|exists:vehicles,id',
        'expense_category' => 'required|in:maintenance_preventive,reparation,pieces_detachees,carburant,assurance,controle_technique,vignette,amendes,peage,parking,lavage,transport,formation_chauffeur,autre',
        'expense_type' => 'required|string|max:100',
        'expense_subtype' => 'nullable|string|max:100',
        'amount_ht' => 'required|numeric|min:0|max:9999999.99',
        'tva_rate' => 'required|numeric|min:0|max:100',
        'invoice_number' => 'nullable|string|max:100',
        'invoice_date' => 'nullable|date',
        'receipt_number' => 'nullable|string|max:100',
        'fiscal_receipt' => 'boolean',
        'odometer_reading' => 'nullable|integer|min:0',
        'fuel_quantity' => 'nullable|numeric|min:0',
        'fuel_price_per_liter' => 'nullable|numeric|min:0',
        'fuel_type' => 'nullable|in:essence,gasoil,gpl',
        'expense_city' => 'nullable|string|max:100',
        'expense_wilaya' => 'nullable|string|max:50',
        'expense_date' => 'required|date|before_or_equal:today',
        'description' => 'required|string|min:5|max:1000',
        'internal_notes' => 'nullable|string|max:1000',
        'tags' => 'array',
        'attachments.*' => 'nullable|file|max:10240',
        'needs_approval' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_pattern' => 'nullable|in:monthly,quarterly,yearly',
        'payment_method' => 'nullable|in:virement,cheque,especes,carte'
    ];

    protected $messages = [
        'vehicle_id.required' => 'Vous devez sélectionner un véhicule.',
        'expense_category.required' => 'La catégorie de dépense est obligatoire.',
        'expense_type.required' => 'Le type de dépense est obligatoire.',
        'amount_ht.required' => 'Le montant HT est obligatoire.',
        'amount_ht.numeric' => 'Le montant doit être un nombre.',
        'amount_ht.min' => 'Le montant ne peut pas être négatif.',
        'expense_date.required' => 'La date de dépense est obligatoire.',
        'expense_date.before_or_equal' => 'La date de dépense ne peut pas être dans le futur.',
        'description.required' => 'La description est obligatoire.',
        'description.min' => 'La description doit contenir au moins 5 caractères.',
        'attachments.*.max' => 'Chaque fichier ne peut pas dépasser 10 MB.',
        'fuel_quantity.required_with' => 'La quantité de carburant est requise.',
        'fuel_price_per_liter.required_with' => 'Le prix au litre est requis.'
    ];

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->budget_year = now()->year;
        $this->budget_month = now()->month;
        $this->budget_quarter = now()->quarter;
        $this->loadReferenceData();
    }

    public function render()
    {
        $expenses = $this->getFilteredExpenses();
        $stats = $this->getExpenseStats();
        $budgetAlerts = $this->getBudgetAlerts();

        if ($this->viewType === 'analytics') {
            $analytics = $this->getAnalyticsData();
            return view('livewire.admin.expense-tracker-analytics', [
                'expenses' => $expenses,
                'stats' => $stats,
                'analytics' => $analytics,
                'budgetAlerts' => $budgetAlerts
            ]);
        }

        return view('livewire.admin.expense-tracker', [
            'expenses' => $expenses,
            'stats' => $stats,
            'budgetAlerts' => $budgetAlerts,
            'expenseCategories' => VehicleExpense::getExpenseCategories(),
            'fuelTypes' => VehicleExpense::getFuelTypes(),
            'paymentMethods' => VehicleExpense::getPaymentMethods(),
            'recurrencePatterns' => VehicleExpense::getRecurrencePatterns()
        ]);
    }

    // Méthodes de filtrage
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterVehicle()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'filterCategory', 'filterVehicle', 'filterSupplier',
            'filterStatus', 'filterPaymentStatus', 'search'
        ]);
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    // Gestion des vues
    public function switchView($type)
    {
        $this->viewType = $type;
    }

    // Méthodes de gestion des modals
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->resetErrorBag();
    }

    public function openDetailsModal($expenseId)
    {
        $this->selectedExpense = VehicleExpense::with([
            'vehicle', 'supplier', 'driver', 'recordedBy', 'approvedBy'
        ])->find($expenseId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedExpense = null;
    }

    public function openApprovalModal($expenseId)
    {
        $this->selectedExpense = VehicleExpense::with(['vehicle', 'supplier'])->find($expenseId);
        $this->approval_comments = '';
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedExpense = null;
        $this->approval_comments = '';
    }

    public function openBudgetModal()
    {
        $this->resetBudgetForm();
        $this->showBudgetModal = true;
    }

    public function closeBudgetModal()
    {
        $this->showBudgetModal = false;
        $this->resetBudgetForm();
    }

    // Méthodes CRUD
    public function createExpense()
    {
        // Validation conditionnelle pour carburant
        if ($this->expense_category === 'carburant') {
            $this->rules['fuel_quantity'] = 'required|numeric|min:0.1';
            $this->rules['fuel_price_per_liter'] = 'required|numeric|min:0.1';
            $this->rules['fuel_type'] = 'required|in:essence,gasoil,gpl';
        }

        $this->validate();

        try {
            $data = $this->getExpenseData();
            $data['organization_id'] = Auth::user()->organization_id;
            $data['recorded_by'] = Auth::id();

            // Gérer l'upload des attachments
            if (!empty($this->attachments)) {
                $attachmentData = [];
                foreach ($this->attachments as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('vehicle-expenses/attachments', 'public');
                        $attachmentData[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'size' => $file->getSize(),
                            'type' => $file->getClientMimeType()
                        ];
                    }
                }
                $data['attachments'] = $attachmentData;
            }

            $expense = VehicleExpense::create($data);

            // Demander l'approbation si nécessaire
            if ($this->needs_approval) {
                $expense->requestApproval();
            }

            $this->closeCreateModal();
            $this->dispatch('expense-created');
            session()->flash('message', 'Dépense enregistrée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    public function deleteExpense($expenseId)
    {
        try {
            $expense = VehicleExpense::findOrFail($expenseId);

            // Vérifier les permissions
            if ($expense->approved && !Auth::user()->hasRole(['Super Admin', 'Admin'])) {
                session()->flash('error', 'Impossible de supprimer une dépense approuvée.');
                return;
            }

            // Supprimer les fichiers attachés
            if ($expense->attachments) {
                foreach ($expense->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $expense->delete();

            $this->dispatch('expense-deleted');
            session()->flash('message', 'Dépense supprimée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Méthodes de workflow d'approbation
    public function approveExpense()
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte'])) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à approuver cette dépense.');
                return;
            }

            $this->selectedExpense->approve($user, $this->approval_comments);

            $this->closeApprovalModal();
            $this->dispatch('expense-approved');
            session()->flash('message', 'Dépense approuvée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function rejectExpense()
    {
        if (empty(trim($this->approval_comments))) {
            $this->addError('approval_comments', 'Un commentaire est requis pour rejeter une dépense.');
            return;
        }

        try {
            $user = Auth::user();

            if (!$user->hasRole(['Super Admin', 'Admin', 'Gestionnaire Flotte'])) {
                session()->flash('error', 'Vous n\'êtes pas autorisé à rejeter cette dépense.');
                return;
            }

            $this->selectedExpense->reject($user, $this->approval_comments);

            $this->closeApprovalModal();
            $this->dispatch('expense-rejected');
            session()->flash('message', 'Dépense rejetée.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function markAsPaid($expenseId)
    {
        try {
            $expense = VehicleExpense::findOrFail($expenseId);

            if (!$expense->approved) {
                session()->flash('error', 'La dépense doit être approuvée avant d\'être marquée comme payée.');
                return;
            }

            $expense->markAsPaid($expense->payment_method);

            $this->dispatch('expense-paid');
            session()->flash('message', 'Dépense marquée comme payée.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function auditExpense($expenseId)
    {
        try {
            $expense = VehicleExpense::findOrFail($expenseId);
            $expense->audit(Auth::user());

            $this->dispatch('expense-audited');
            session()->flash('message', 'Dépense auditée avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // Gestion des budgets
    public function createBudget()
    {
        $this->validate([
            'budget_period' => 'required|in:monthly,quarterly,yearly',
            'budget_year' => 'required|integer|min:2020|max:2030',
            'budget_month' => 'nullable|integer|min:1|max:12',
            'budget_quarter' => 'nullable|integer|min:1|max:4',
            'budgeted_amount' => 'required|numeric|min:0',
            'budget_category' => 'nullable|string',
            'budget_vehicle_id' => 'nullable|exists:vehicles,id'
        ]);

        try {
            $data = [
                'organization_id' => Auth::user()->organization_id,
                'vehicle_id' => $this->budget_vehicle_id ?: null,
                'expense_category' => $this->budget_category ?: null,
                'budget_period' => $this->budget_period,
                'budget_year' => $this->budget_year,
                'budget_month' => $this->budget_period === 'monthly' ? $this->budget_month : null,
                'budget_quarter' => $this->budget_period === 'quarterly' ? $this->budget_quarter : null,
                'budgeted_amount' => $this->budgeted_amount
            ];

            ExpenseBudget::create($data);

            $this->closeBudgetModal();
            $this->dispatch('budget-created');
            session()->flash('message', 'Budget créé avec succès.');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du budget: ' . $e->getMessage());
        }
    }

    // Méthodes utilitaires privées
    private function getFilteredExpenses()
    {
        $query = VehicleExpense::with(['vehicle', 'supplier', 'driver', 'recordedBy'])
                              ->forOrganization(Auth::user()->organization_id);

        // Filtres de base
        if ($this->filterCategory) {
            $query->byCategory($this->filterCategory);
        }

        if ($this->filterVehicle) {
            $query->byVehicle($this->filterVehicle);
        }

        if ($this->filterSupplier) {
            $query->bySupplier($this->filterSupplier);
        }

        if ($this->filterStatus) {
            switch ($this->filterStatus) {
                case 'pending_approval':
                    $query->pendingApproval();
                    break;
                case 'approved':
                    $query->approved();
                    break;
                case 'requiring_audit':
                    $query->requireAudit();
                    break;
            }
        }

        if ($this->filterPaymentStatus) {
            switch ($this->filterPaymentStatus) {
                case 'paid':
                    $query->paid();
                    break;
                case 'unpaid':
                    $query->unpaid();
                    break;
            }
        }

        // Filtres par date
        if ($this->dateFrom) {
            $query->byDateRange($this->dateFrom, $this->dateTo ?: now()->format('Y-m-d'));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('expense_type', 'like', '%' . $this->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest('expense_date')->paginate(15);
    }

    private function getExpenseStats()
    {
        $organizationId = Auth::user()->organization_id;
        $currentMonth = now()->startOfMonth();
        $currentYear = now()->startOfYear();

        return [
            'total_this_month' => VehicleExpense::forOrganization($organizationId)
                                               ->whereDate('expense_date', '>=', $currentMonth)
                                               ->sum('total_ttc'),
            'total_this_year' => VehicleExpense::forOrganization($organizationId)
                                              ->whereDate('expense_date', '>=', $currentYear)
                                              ->sum('total_ttc'),
            'pending_approval' => VehicleExpense::forOrganization($organizationId)
                                               ->pendingApproval()
                                               ->count(),
            'unpaid_amount' => VehicleExpense::forOrganization($organizationId)
                                            ->approved()
                                            ->unpaid()
                                            ->sum('total_ttc'),
            'fuel_expenses_month' => VehicleExpense::forOrganization($organizationId)
                                                  ->byCategory('carburant')
                                                  ->whereDate('expense_date', '>=', $currentMonth)
                                                  ->sum('total_ttc'),
            'maintenance_expenses_month' => VehicleExpense::forOrganization($organizationId)
                                                         ->maintenanceExpenses()
                                                         ->whereDate('expense_date', '>=', $currentMonth)
                                                         ->sum('total_ttc'),
            'avg_expense_value' => VehicleExpense::forOrganization($organizationId)
                                                ->whereDate('expense_date', '>=', $currentMonth)
                                                ->avg('total_ttc') ?: 0
        ];
    }

    private function getBudgetAlerts()
    {
        $organizationId = Auth::user()->organization_id;

        return ExpenseBudget::forOrganization($organizationId)
                           ->active()
                           ->overWarningThreshold()
                           ->with(['vehicle'])
                           ->get()
                           ->map(function ($budget) {
                               return [
                                   'id' => $budget->id,
                                   'description' => $budget->scope_description,
                                   'period' => $budget->period_label,
                                   'utilization' => $budget->utilization_percentage,
                                   'status' => $budget->status,
                                   'remaining' => $budget->remaining_amount
                               ];
                           });
    }

    private function getAnalyticsData()
    {
        $organizationId = Auth::user()->organization_id;

        // Dépenses par catégorie (6 derniers mois)
        $categoryTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');

            foreach (VehicleExpense::getExpenseCategories() as $category => $label) {
                $amount = VehicleExpense::forOrganization($organizationId)
                                       ->byCategory($category)
                                       ->whereYear('expense_date', $month->year)
                                       ->whereMonth('expense_date', $month->month)
                                       ->sum('total_ttc');

                $categoryTrends[$category][$monthKey] = $amount;
            }
        }

        // Top véhicules les plus coûteux
        $topVehicles = VehicleExpense::forOrganization($organizationId)
                                    ->selectRaw('vehicle_id, SUM(total_ttc) as total_cost')
                                    ->with('vehicle')
                                    ->whereDate('expense_date', '>=', now()->subMonths(6))
                                    ->groupBy('vehicle_id')
                                    ->orderByDesc('total_cost')
                                    ->limit(10)
                                    ->get();

        // Efficacité carburant
        $fuelEfficiency = VehicleExpense::forOrganization($organizationId)
                                       ->byCategory('carburant')
                                       ->whereNotNull(['fuel_quantity', 'odometer_reading'])
                                       ->whereDate('expense_date', '>=', now()->subMonths(3))
                                       ->get()
                                       ->map(function ($expense) {
                                           $efficiency = $expense->calculateFuelEfficiency();
                                           return $efficiency ? [
                                               'vehicle' => $expense->vehicle->registration_plate,
                                               'consumption' => $efficiency['consumption_per_100km'],
                                               'cost_per_100km' => $efficiency['cost_per_100km']
                                           ] : null;
                                       })
                                       ->filter()
                                       ->values();

        return [
            'category_trends' => $categoryTrends,
            'top_vehicles' => $topVehicles,
            'fuel_efficiency' => $fuelEfficiency
        ];
    }

    private function getExpenseData()
    {
        return [
            'vehicle_id' => $this->vehicle_id,
            'supplier_id' => $this->supplier_id ?: null,
            'driver_id' => $this->driver_id ?: null,
            'expense_category' => $this->expense_category,
            'expense_type' => $this->expense_type,
            'expense_subtype' => $this->expense_subtype,
            'amount_ht' => $this->amount_ht,
            'tva_rate' => $this->tva_rate,
            'invoice_number' => $this->invoice_number,
            'invoice_date' => $this->invoice_date ?: null,
            'receipt_number' => $this->receipt_number,
            'fiscal_receipt' => $this->fiscal_receipt,
            'odometer_reading' => $this->odometer_reading ?: null,
            'fuel_quantity' => $this->fuel_quantity ?: null,
            'fuel_price_per_liter' => $this->fuel_price_per_liter ?: null,
            'fuel_type' => $this->fuel_type,
            'expense_city' => $this->expense_city,
            'expense_wilaya' => $this->expense_wilaya,
            'expense_date' => $this->expense_date,
            'description' => $this->description,
            'internal_notes' => $this->internal_notes,
            'tags' => $this->tags,
            'needs_approval' => $this->needs_approval,
            'is_recurring' => $this->is_recurring,
            'recurrence_pattern' => $this->is_recurring ? $this->recurrence_pattern : null,
            'payment_method' => $this->payment_method
        ];
    }

    private function loadReferenceData()
    {
        $organizationId = Auth::user()->organization_id;

        $this->vehicles = Vehicle::where('organization_id', $organizationId)
                                ->orderBy('registration_plate')
                                ->get();

        $this->suppliers = Supplier::where('organization_id', $organizationId)
                                 ->active()
                                 ->notBlacklisted()
                                 ->orderBy('company_name')
                                 ->get();

        $this->drivers = \App\Models\User::where('organization_id', $organizationId)
                                        ->whereHas('roles', function ($query) {
                                            $query->where('name', 'driver');
                                        })
                                        ->orderBy('name')
                                        ->get();
    }

    private function resetCreateForm()
    {
        $this->reset([
            'vehicle_id', 'supplier_id', 'driver_id', 'expense_category',
            'expense_type', 'expense_subtype', 'amount_ht', 'invoice_number',
            'invoice_date', 'receipt_number', 'fiscal_receipt', 'odometer_reading',
            'fuel_quantity', 'fuel_price_per_liter', 'fuel_type', 'expense_city',
            'expense_wilaya', 'expense_date', 'description', 'internal_notes',
            'tags', 'attachments', 'needs_approval', 'is_recurring',
            'recurrence_pattern', 'payment_method'
        ]);
        $this->tva_rate = 19.00;
        $this->expense_date = now()->format('Y-m-d');
    }

    private function resetBudgetForm()
    {
        $this->reset([
            'budget_period', 'budgeted_amount', 'budget_category', 'budget_vehicle_id'
        ]);
        $this->budget_year = now()->year;
        $this->budget_month = now()->month;
        $this->budget_quarter = now()->quarter;
        $this->budget_period = 'monthly';
    }
}