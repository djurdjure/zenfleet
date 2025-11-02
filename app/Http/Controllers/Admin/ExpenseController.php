<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleExpense;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\ExpenseBudget;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * 🚀 ZENFLEET EXPENSE CONTROLLER - Enterprise Grade
 *
 * Contrôleur ultra-professionnel pour la gestion des dépenses de flotte
 * avec fonctionnalités enterprise avancées, analytics, et reporting
 *
 * @version 3.0-Enterprise
 * @author ZenFleet Expert Team (20+ years experience)
 */
class ExpenseController extends Controller
{
    /**
     * 🔐 Constructeur avec middleware de sécurité enterprise
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:Super Admin|Admin|Gestionnaire Flotte|Comptable')
            ->except(['show', 'index']);
    }

    /**
     * 📊 Dashboard principal des dépenses enterprise
     */
    public function index(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;

        // 🔍 Filtres avancés enterprise
        $filters = [
            'vehicle_id' => $request->get('vehicle_id'),
            'expense_type' => $request->get('expense_type'),
            'status' => $request->get('status', 'all'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'amount_min' => $request->get('amount_min'),
            'amount_max' => $request->get('amount_max'),
            'search' => $request->get('search')
        ];

        // 📈 Construction de la requête avec optimisations enterprise
        $expensesQuery = VehicleExpense::with([
            'vehicle:id,registration_plate,brand,model',
            'driver:id,first_name,last_name,employee_number',
            'approvedBy:id,name',
            'organization:id,name'
        ])
        ->where('organization_id', $organizationId)
        ->when($filters['vehicle_id'], fn($q) => $q->where('vehicle_id', $filters['vehicle_id']))
        ->when($filters['expense_type'], fn($q) => $q->where('expense_type', $filters['expense_type']))
        ->when($filters['status'] !== 'all', fn($q) => $q->where('approval_status', $filters['status']))
        ->when($filters['date_from'], fn($q) => $q->where('expense_date', '>=', $filters['date_from']))
        ->when($filters['date_to'], fn($q) => $q->where('expense_date', '<=', $filters['date_to']))
        ->when($filters['amount_min'], fn($q) => $q->where('amount', '>=', $filters['amount_min']))
        ->when($filters['amount_max'], fn($q) => $q->where('amount', '<=', $filters['amount_max']))
        ->when($filters['search'], function($q, $search) {
            $q->where(function($query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', fn($q) => $q->where('registration_plate', 'like', "%{$search}%"));
            });
        });

        // 📊 Statistiques enterprise avancées
        $stats = $this->getExpenseStats($organizationId, $filters);

        // 📄 Pagination optimisée
        $expenses = $expensesQuery->latest('expense_date')->paginate(20)->withQueryString();

        // 🚗 Données pour les filtres
        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'registration_plate', 'brand', 'model')
            ->orderBy('registration_plate')
            ->get();

        // 📊 Types de dépenses
        $expenseTypes = VehicleExpense::EXPENSE_TYPES;

        // 💰 Budgets actifs
        $budgets = ExpenseBudget::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->get();

        return view('admin.expenses.index', compact(
            'expenses',
            'stats',
            'vehicles',
            'expenseTypes',
            'budgets',
            'filters'
        ));
    }

    /**
     * 📝 Formulaire de création ultra-professionnel
     */
    public function create(): View
    {
        $organizationId = auth()->user()->organization_id;

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage')
            ->orderBy('registration_plate')
            ->get();

        $drivers = Driver::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        return view('admin.expenses.create', compact(
            'vehicles',
            'drivers'
        ));
    }

    /**
     * 💾 Enregistrement avec validation enterprise
     */
    public function store(Request $request): RedirectResponse
    {
        // 🔒 Validation enterprise ultra-stricte
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'expense_type' => 'required|in:' . implode(',', array_keys(VehicleExpense::getExpenseCategories())),
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'required|string|max:1000',
            'supplier_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'mileage_at_expense' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000'
        ], [
            'vehicle_id.required' => 'Le véhicule est obligatoire.',
            'vehicle_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'expense_type.required' => 'Le type de dépense est obligatoire.',
            'expense_type.in' => 'Le type de dépense sélectionné n\'est pas valide.',
            'expense_date.required' => 'La date de dépense est obligatoire.',
            'expense_date.before_or_equal' => 'La date de dépense ne peut pas être dans le futur.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'amount.max' => 'Le montant ne peut pas dépasser 999 999,99 DA.',
            'description.required' => 'La description est obligatoire.',
            'receipt_file.mimes' => 'Le fichier doit être au format PDF, JPG, JPEG ou PNG.',
            'receipt_file.max' => 'Le fichier ne peut pas dépasser 10 MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // 📁 Gestion du fichier reçu
            $receiptPath = null;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')->store(
                    'receipts/' . Carbon::now()->format('Y/m'),
                    'private'
                );
            }

            // 💰 Calculs enterprise
            $amountHT = $request->amount;
            $tvaRate = $request->tax_rate;
            $tvaAmount = ($amountHT * $tvaRate) / 100;

            // 📄 Génération du numéro de référence enterprise (optionnel pour l'instant)
            $invoiceNumber = $request->invoice_number ?: 'EXP-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // 💾 Création de la dépense
            $expense = VehicleExpense::create([
                'organization_id' => auth()->user()->organization_id,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'expense_category' => $request->expense_type,
                'expense_date' => $request->expense_date,
                'amount_ht' => $amountHT,
                'tva_rate' => $tvaRate,
                'description' => $request->description,
                'supplier_name' => $request->supplier_name,
                'invoice_number' => $invoiceNumber,
                'attachments' => $receiptPath ? [$receiptPath] : null,
                'odometer_reading' => $request->mileage_at_expense,
                'internal_notes' => $request->notes,
                'recorded_by' => auth()->id(),
                'needs_approval' => true,
                'payment_status' => 'pending'
            ]);

            DB::commit();

            return redirect()
                ->route('admin.expenses.index')
                ->with('success', '✅ Dépense créée avec succès ! Référence: ' . $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();

            // 🗑️ Nettoyage du fichier si erreur
            if ($receiptPath && Storage::disk('private')->exists($receiptPath)) {
                Storage::disk('private')->delete($receiptPath);
            }

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 👁️ Affichage détaillé enterprise
     */
    public function show(VehicleExpense $expense): View
    {
        $expense->load([
            'vehicle:id,registration_plate,brand,model,vin',
            'driver:id,first_name,last_name,employee_number',
            'createdBy:id,name',
            'approvedBy:id,name',
            'organization:id,name'
        ]);

        // 📊 Historique des modifications
        $auditHistory = $this->getExpenseAuditHistory($expense);

        // 💰 Informations budgétaires
        $budgetInfo = $this->getBudgetImpact($expense);

        return view('admin.expenses.show', compact(
            'expense',
            'auditHistory',
            'budgetInfo'
        ));
    }

    /**
     * ✏️ Formulaire d'édition enterprise
     */
    public function edit(VehicleExpense $expense): View
    {
        $organizationId = auth()->user()->organization_id;

        // 🔒 Vérification des permissions
        if ($expense->approval_status === 'approved' && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            abort(403, 'Impossible de modifier une dépense approuvée.');
        }

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage')
            ->orderBy('registration_plate')
            ->get();

        $drivers = Driver::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'employee_number')
            ->orderBy('first_name')
            ->get();

        $expenseTypes = VehicleExpense::EXPENSE_TYPES;
        $taxRates = VehicleExpense::TAX_RATES;

        return view('admin.expenses.edit', compact(
            'expense',
            'vehicles',
            'drivers',
            'expenseTypes',
            'taxRates'
        ));
    }

    /**
     * 🔄 Mise à jour enterprise
     */
    public function update(Request $request, VehicleExpense $expense): RedirectResponse
    {
        // 🔒 Vérification des permissions
        if ($expense->approval_status === 'approved' && !auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->back()
                ->with('error', '❌ Impossible de modifier une dépense approuvée.');
        }

        // Même validation que pour store()
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'expense_type' => 'required|in:' . implode(',', array_keys(VehicleExpense::EXPENSE_TYPES)),
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'description' => 'required|string|max:1000',
            'supplier_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'mileage_at_expense' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $oldReceiptPath = $expense->receipt_file_path;

            // 📁 Gestion du nouveau fichier
            $receiptPath = $oldReceiptPath;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')->store(
                    'receipts/' . Carbon::now()->format('Y/m'),
                    'private'
                );
            }

            // 💰 Recalculs
            $taxAmount = ($request->amount * $request->tax_rate) / 100;
            $totalAmount = $request->amount + $taxAmount;

            // 🔄 Mise à jour
            $expense->update([
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'expense_type' => $request->expense_type,
                'expense_date' => $request->expense_date,
                'amount' => $request->amount,
                'tax_rate' => $request->tax_rate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'description' => $request->description,
                'supplier_name' => $request->supplier_name,
                'invoice_number' => $request->invoice_number,
                'receipt_file_path' => $receiptPath,
                'mileage_at_expense' => $request->mileage_at_expense,
                'notes' => $request->notes,
                'updated_by' => auth()->id()
            ]);

            // 🗑️ Suppression de l'ancien fichier si nouveau
            if ($receiptPath !== $oldReceiptPath && $oldReceiptPath) {
                Storage::disk('private')->delete($oldReceiptPath);
            }

            DB::commit();

            return redirect()
                ->route('admin.expenses.show', $expense)
                ->with('success', '✅ Dépense mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la mise à jour: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 🗑️ Suppression enterprise
     */
    public function destroy(VehicleExpense $expense): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // 🗑️ Suppression du fichier
            if ($expense->receipt_file_path) {
                Storage::disk('private')->delete($expense->receipt_file_path);
            }

            $expense->delete();

            DB::commit();

            return redirect()
                ->route('admin.expenses.index')
                ->with('success', '✅ Dépense supprimée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '❌ Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Approbation enterprise
     */
    public function approve(Request $request, VehicleExpense $expense): JsonResponse
    {
        try {
            $expense->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Dépense approuvée avec succès !'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors de l\'approbation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ❌ Rejet enterprise
     */
    public function reject(Request $request, VehicleExpense $expense): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $expense->update([
                'approval_status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Dépense rejetée.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur lors du rejet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 📊 Analytics avancées enterprise
     */
    public function analytics(): View
    {
        $organizationId = auth()->user()->organization_id;

        // 📈 Données analytics complètes
        $analytics = [
            'monthly_trends' => $this->getMonthlyTrends($organizationId),
            'expense_by_type' => $this->getExpensesByType($organizationId),
            'top_vehicles' => $this->getTopExpensiveVehicles($organizationId),
            'budget_analysis' => $this->getBudgetAnalysis($organizationId),
            'cost_per_km' => $this->getCostPerKmAnalysis($organizationId)
        ];

        return view('admin.expenses.analytics', compact('analytics'));
    }

    /**
     * 📄 Export Excel enterprise
     */
    public function export(Request $request)
    {
        // Implementation d'export Excel enterprise
        // À compléter selon les besoins
        return response()->json(['message' => 'Export en développement']);
    }

    /**
     * 📊 Méthode privée : Statistiques expenses
     */
    private function getExpenseStats($organizationId, $filters): array
    {
        $query = VehicleExpense::where('organization_id', $organizationId);

        // Application des mêmes filtres que pour l'index
        $query->when($filters['vehicle_id'], fn($q) => $q->where('vehicle_id', $filters['vehicle_id']))
              ->when($filters['expense_type'], fn($q) => $q->where('expense_type', $filters['expense_type']))
              ->when($filters['status'] !== 'all', fn($q) => $q->where('approval_status', $filters['status']))
              ->when($filters['date_from'], fn($q) => $q->where('expense_date', '>=', $filters['date_from']))
              ->when($filters['date_to'], fn($q) => $q->where('expense_date', '<=', $filters['date_to']));

        return [
            'total_count' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'pending_count' => $query->where('approval_status', 'pending')->count(),
            'approved_count' => $query->where('approval_status', 'approved')->count(),
            'rejected_count' => $query->where('approval_status', 'rejected')->count(),
            'this_month_amount' => $query->whereMonth('expense_date', now()->month)->sum('total_amount'),
            'average_amount' => $query->avg('total_amount') ?? 0
        ];
    }

    /**
     * 🔢 Génération du numéro de référence
     */
    private function generateReferenceNumber(): string
    {
        $prefix = 'EXP';
        $year = date('Y');
        $month = date('m');

        // Compteur mensuel
        $count = VehicleExpense::where('organization_id', auth()->user()->organization_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $count);
    }

    /**
     * 📊 Méthodes analytics privées
     */
    private function getMonthlyTrends($organizationId): array
    {
        // Implementation des tendances mensuelles
        return [];
    }

    private function getExpensesByType($organizationId): array
    {
        // Implementation répartition par type
        return [];
    }

    private function getTopExpensiveVehicles($organizationId): array
    {
        // Implementation top véhicules coûteux
        return [];
    }

    private function getBudgetAnalysis($organizationId): array
    {
        // Implementation analyse budgétaire
        return [];
    }

    private function getCostPerKmAnalysis($organizationId): array
    {
        // Implementation coût par km
        return [];
    }

    private function getExpenseAuditHistory($expense): array
    {
        // Implementation historique audit
        return [];
    }

    private function getBudgetImpact($expense): array
    {
        // Implementation impact budgétaire
        return [];
    }
}