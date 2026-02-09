<?php

namespace App\Livewire\Admin\VehicleExpenses;

use App\Models\VehicleExpense;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Supplier;
use App\Models\ExpenseGroup;
use App\Services\VehicleExpenseService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class ExpenseForm extends Component
{
    use WithFileUploads, AuthorizesRequests;

    // Mode (create/edit)
    public $expenseId = null;
    public $isEditMode = false;
    
    // Champs du formulaire
    public $expense_number = '';
    public $expense_date;
    public $vehicle_id = '';
    public $driver_id = '';
    public $supplier_id = '';
    public $expense_group_id = '';
    public $category = '';
    public $sub_category = '';
    public $description = '';
    public $amount = '';
    public $tax_rate = 19;
    public $tax_amount = 0;
    public $discount_amount = 0;
    public $total_amount = 0;
    public $reference_number = '';
    public $invoice_number = '';
    public $payment_method = 'bank_transfer';
    public $payment_status = 'unpaid';
    public $payment_date = null;
    public $due_date = null;
    public $notes = '';
    public $priority_level = 'normal';
    public $cost_center = '';
    public $location = '';
    public $odometer_reading = '';
    public $quantity = 1;
    public $unit_price = '';
    public $external_reference = '';
    
    // Fichiers
    public $attachments = [];
    public $existingAttachments = [];
    
    // Données pour les select
    public $vehicles = [];
    public $drivers = [];
    public $suppliers = [];
    public $expenseGroups = [];
    public $subcategories = [];
    
    // Validation temps réel
    public $showValidation = false;

    protected function rules()
    {
        $rules = [
            'expense_date' => 'required|date|before_or_equal:today',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'expense_group_id' => 'nullable|exists:expense_groups,id',
            'category' => ['required', Rule::in(array_keys(VehicleExpense::EXPENSE_CATEGORIES))],
            'sub_category' => 'nullable|string|max:100',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0|max:999999999',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'invoice_number' => 'nullable|string|max:100',
            'payment_method' => ['required', Rule::in(array_keys(VehicleExpense::PAYMENT_METHODS))],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid', 'partial', 'overdue', 'cancelled'])],
            'payment_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:expense_date',
            'notes' => 'nullable|string|max:1000',
            'priority_level' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'cost_center' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'odometer_reading' => 'nullable|numeric|min:0',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'nullable|numeric|min:0',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ];
        
        // Règle unique pour expense_number sauf en mode edit
        if (!$this->isEditMode) {
            $rules['expense_number'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('vehicle_expenses')
                    ->where('organization_id', Auth::user()->organization_id)
            ];
        }
        
        return $rules;
    }

    protected $messages = [
        'expense_date.required' => 'La date de dépense est obligatoire.',
        'expense_date.before_or_equal' => 'La date ne peut pas être dans le futur.',
        'vehicle_id.required' => 'Le véhicule est obligatoire.',
        'category.required' => 'La catégorie est obligatoire.',
        'description.required' => 'La description est obligatoire.',
        'amount.required' => 'Le montant est obligatoire.',
        'amount.min' => 'Le montant doit être positif.',
        'expense_number.unique' => 'Ce numéro de dépense existe déjà.',
        'attachments.*.max' => 'Les fichiers ne doivent pas dépasser 10 MB.',
        'attachments.*.mimes' => 'Format de fichier non autorisé.',
    ];

    public function mount($expenseId = null)
    {
        $this->loadFormData();
        
        if ($expenseId) {
            $this->loadExpense($expenseId);
        } else {
            $this->authorize('create', VehicleExpense::class);
            $this->initializeNewExpense();
        }
    }

    private function loadFormData()
    {
        $orgId = Auth::user()->organization_id;
        $plateColumn = Schema::hasColumn('vehicles', 'license_plate') ? 'license_plate' : 'registration_plate';
        
        $this->vehicles = Vehicle::where('organization_id', $orgId)
            ->active()
            ->orderBy($plateColumn)
            ->get();
            
        $this->drivers = Driver::where('organization_id', $orgId)
            ->active($orgId)
            ->orderBy('last_name')
            ->get();
            
        $this->suppliers = Supplier::where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $this->expenseGroups = ExpenseGroup::where('organization_id', $orgId)
            ->active()
            ->orderBy('name')
            ->get();
    }

    private function loadExpense($expenseId)
    {
        $expense = VehicleExpense::with(['attachments'])
            ->where('organization_id', Auth::user()->organization_id)
            ->findOrFail($expenseId);
        
        $this->authorize('update', $expense);
        
        $this->expenseId = $expense->id;
        $this->isEditMode = true;
        
        // Charger les données
        $this->expense_number = $expense->expense_number;
        $this->expense_date = $expense->expense_date?->format('Y-m-d');
        $this->vehicle_id = $expense->vehicle_id;
        $this->driver_id = $expense->driver_id;
        $this->supplier_id = $expense->supplier_id;
        $this->expense_group_id = $expense->expense_group_id;
        $this->category = $expense->category;
        $this->sub_category = $expense->sub_category;
        $this->description = $expense->description;
        $this->amount = $expense->amount;
        $this->tax_rate = $expense->tax_rate ?? 19;
        $this->tax_amount = $expense->tax_amount;
        $this->discount_amount = $expense->discount_amount ?? 0;
        $this->total_amount = $expense->total_amount;
        $this->reference_number = $expense->reference_number;
        $this->invoice_number = $expense->invoice_number;
        $this->payment_method = $expense->payment_method ?? 'bank_transfer';
        $this->payment_status = $expense->payment_status ?? 'unpaid';
        $this->payment_date = $expense->payment_date?->format('Y-m-d');
        $this->due_date = $expense->due_date?->format('Y-m-d');
        $this->notes = $expense->notes;
        $this->priority_level = $expense->priority_level ?? 'normal';
        $this->cost_center = $expense->cost_center;
        $this->location = $expense->location;
        $this->odometer_reading = $expense->odometer_reading;
        $this->quantity = $expense->quantity ?? 1;
        $this->unit_price = $expense->unit_price;
        $this->external_reference = $expense->external_reference;
        
        // Charger les pièces jointes existantes
        if ($expense->attachments && is_array($expense->attachments)) {
            $this->existingAttachments = $expense->attachments;
        }
        
        $this->updateSubcategories();
    }

    private function initializeNewExpense()
    {
        $this->expense_number = $this->generateExpenseNumber();
        $this->expense_date = now()->format('Y-m-d');
    }

    private function generateExpenseNumber()
    {
        $prefix = 'EXP';
        $year = date('Y');
        $month = date('m');
        
        $lastExpense = VehicleExpense::where('organization_id', Auth::user()->organization_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastExpense && preg_match('/EXP-\d{4}\d{2}(\d{4})/', $lastExpense->expense_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }
        
        return sprintf('%s-%s%s%04d', $prefix, $year, $month, $sequence);
    }

    public function render()
    {
        return view('livewire.admin.vehicle-expenses.expense-form');
    }

    // Calcul automatique TVA et total
    public function updatedAmount()
    {
        $this->calculateTotals();
    }

    public function updatedTaxRate()
    {
        $this->calculateTotals();
    }

    public function updatedDiscountAmount()
    {
        $this->calculateTotals();
    }

    public function updatedQuantity()
    {
        if ($this->unit_price) {
            $this->amount = $this->quantity * $this->unit_price;
        }
        $this->calculateTotals();
    }

    public function updatedUnitPrice()
    {
        if ($this->quantity) {
            $this->amount = $this->quantity * $this->unit_price;
        }
        $this->calculateTotals();
    }

    private function calculateTotals()
    {
        $amount = floatval($this->amount);
        $taxRate = floatval($this->tax_rate);
        $discount = floatval($this->discount_amount);
        
        // Calculer la TVA
        $this->tax_amount = round(($amount - $discount) * ($taxRate / 100), 2);
        
        // Calculer le total
        $this->total_amount = round($amount - $discount + $this->tax_amount, 2);
    }

    // Mise à jour des sous-catégories selon la catégorie
    public function updatedCategory()
    {
        $this->updateSubcategories();
        $this->sub_category = '';
    }

    private function updateSubcategories()
    {
        $subcategoriesMap = [
            'fuel' => ['Essence', 'Diesel', 'Électrique', 'Hybride', 'GPL', 'CNG'],
            'maintenance' => ['Vidange', 'Filtres', 'Freins', 'Pneus', 'Batterie', 'Éclairage', 'Climatisation'],
            'repair' => ['Moteur', 'Transmission', 'Suspension', 'Carrosserie', 'Électronique', 'Diagnostic'],
            'insurance' => ['Assurance Tous Risques', 'Responsabilité Civile', 'Bris de Glace', 'Vol', 'Incendie'],
            'tax' => ['Vignette', 'Taxe de Circulation', 'Taxe de Mise en Circulation', 'Contrôle Technique'],
            'parking' => ['Parking Public', 'Parking Privé', 'Stationnement', 'Garage'],
            'toll' => ['Autoroute', 'Pont', 'Tunnel', 'Péage Urbain'],
            'washing' => ['Lavage Extérieur', 'Lavage Intérieur', 'Lavage Complet', 'Polissage'],
            'fine' => ['Excès de Vitesse', 'Stationnement', 'Feu Rouge', 'Autres Infractions'],
            'other' => ['Accessoires', 'Documentation', 'Formation', 'Divers'],
        ];
        
        $this->subcategories = $subcategoriesMap[$this->category] ?? [];
    }

    // Sauvegarde
    public function save()
    {
        $this->showValidation = true;
        $this->validate();
        
        if ($this->isEditMode) {
            $expense = VehicleExpense::find($this->expenseId);
            if ($expense) {
                $this->authorize('update', $expense);
            }
        } else {
            $this->authorize('create', VehicleExpense::class);
        }
        
        DB::beginTransaction();
        try {
            $service = app(VehicleExpenseService::class);
            
            $data = [
                'expense_number' => $this->expense_number,
                'expense_date' => $this->expense_date,
                'vehicle_id' => $this->vehicle_id,
                'driver_id' => $this->driver_id ?: null,
                'supplier_id' => $this->supplier_id ?: null,
                'expense_group_id' => $this->expense_group_id ?: null,
                'category' => $this->category,
                'sub_category' => $this->sub_category,
                'description' => $this->description,
                'amount' => $this->amount,
                'tax_rate' => $this->tax_rate,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total_amount' => $this->total_amount,
                'reference_number' => $this->reference_number,
                'invoice_number' => $this->invoice_number,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_status,
                'payment_date' => $this->payment_date,
                'due_date' => $this->due_date,
                'notes' => $this->notes,
                'priority_level' => $this->priority_level,
                'cost_center' => $this->cost_center,
                'location' => $this->location,
                'odometer_reading' => $this->odometer_reading,
                'quantity' => $this->quantity,
                'unit_price' => $this->unit_price,
                'external_reference' => $this->external_reference,
                'organization_id' => Auth::user()->organization_id,
                'created_by' => Auth::id(),
            ];
            
            if ($this->isEditMode) {
                $expense = VehicleExpense::find($this->expenseId);
                $expense = $service->update($expense, $data);
            } else {
                $expense = $service->create($data);
            }
            
            // Gérer les pièces jointes
            $this->handleAttachments($expense);
            
            DB::commit();
            
            $message = $this->isEditMode 
                ? 'Dépense mise à jour avec succès.' 
                : 'Dépense créée avec succès.';
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);
            
            // Émettre l'événement
            $event = $this->isEditMode ? 'expenseUpdated' : 'expenseCreated';
            $this->dispatch($event);
            
            // Redirection
            if (!$this->isEditMode) {
                return redirect()->route('admin.vehicle-expenses.index');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ]);
        }
    }

    private function handleAttachments($expense)
    {
        $attachmentPaths = $this->existingAttachments;
        
        // Ajouter les nouveaux fichiers
        foreach ($this->attachments as $file) {
            $path = $file->store('expenses/attachments/' . $expense->id, 'public');
            $attachmentPaths[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }
        
        // Mettre à jour les pièces jointes
        $expense->update(['attachments' => $attachmentPaths]);
    }

    public function removeAttachment($index)
    {
        unset($this->existingAttachments[$index]);
        $this->existingAttachments = array_values($this->existingAttachments);
    }

    public function cancel()
    {
        return redirect()->route('admin.vehicle-expenses.index');
    }
}
