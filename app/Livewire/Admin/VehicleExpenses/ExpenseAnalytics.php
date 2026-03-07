<?php

namespace App\Livewire\Admin\VehicleExpenses;

use App\Models\ExpenseGroup;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Services\VehicleExpenseService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ExpenseAnalytics extends Component
{
    // Filtres
    public $period = 'month'; // month, quarter, year, custom
    public $startDate;
    public $endDate;
    public $vehicle_id = '';
    public $category = '';
    public $expense_group_id = '';

    // Donnees analytics
    public $dashboardStats = [];
    public $tcoData = [];
    public $categoryBreakdown = [];
    public $vehicleCosts = [];
    public $trends = [];
    public $supplierAnalysis = [];
    public $budgetAnalysis = [];
    public $efficiencyMetrics = [];
    public $predictions = [];
    public $complianceScore = 0;

    // Graphiques
    public $chartLabels = [];
    public $chartData = [];
    public $pieChartData = [];

    // Options d'affichage
    public $viewMode = 'dashboard'; // dashboard, tco, trends, suppliers, budgets
    public $showAdvancedMetrics = false;

    protected $listeners = [
        'refreshAnalytics' => 'loadAnalytics',
        'exportAnalytics' => 'exportData',
    ];

    public function mount(): void
    {
        $this->initializeDates();
        $this->loadAnalytics();
    }

    private function initializeDates(): void
    {
        switch ($this->period) {
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = Carbon::now()->startOfQuarter()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            default:
                if (!$this->startDate) {
                    $this->startDate = Carbon::now()->subMonths(6)->format('Y-m-d');
                }
                if (!$this->endDate) {
                    $this->endDate = Carbon::now()->format('Y-m-d');
                }
                break;
        }
    }

    public function render()
    {
        $plateColumn = Schema::hasColumn('vehicles', 'license_plate') ? 'license_plate' : 'registration_plate';

        $vehicles = Vehicle::where('organization_id', $this->organizationId())
            ->active()
            ->orderBy($plateColumn)
            ->get();

        $expenseGroups = ExpenseGroup::where('organization_id', $this->organizationId())
            ->active()
            ->orderBy('name')
            ->get();

        return view('livewire.admin.vehicle-expenses.expense-analytics', [
            'vehicles' => $vehicles,
            'expenseGroups' => $expenseGroups,
            'categories' => VehicleExpense::EXPENSE_CATEGORIES,
        ]);
    }

    public function loadAnalytics(): void
    {
        try {
            $this->resetVisualDatasets();

            $expenses = $this->baseExpensesQuery()->get();
            $this->dashboardStats = $this->computeDashboardStats($expenses);
            $this->complianceScore = $this->computeComplianceScore($expenses);

            switch ($this->viewMode) {
                case 'tco':
                    $this->loadTCOAnalysis($expenses);
                    break;
                case 'trends':
                    $this->loadTrendsAnalysis($expenses);
                    break;
                case 'suppliers':
                    $this->loadSuppliersAnalysis($expenses);
                    break;
                case 'budgets':
                    $this->loadBudgetsAnalysis($expenses);
                    break;
                default:
                    $this->loadDashboardData($expenses);
                    break;
            }

            if ($this->showAdvancedMetrics) {
                $this->loadAdvancedMetrics($expenses);
            }

            $this->dispatch('dashboard:data-updated');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors du chargement des analytics: ' . $e->getMessage(),
            ]);
        }
    }

    private function resetVisualDatasets(): void
    {
        $this->chartLabels = [];
        $this->chartData = [];
        $this->pieChartData = [];
    }

    private function organizationId(): int
    {
        return (int) (Auth::user()->organization_id ?? 0);
    }

    private function baseExpensesQuery()
    {
        return VehicleExpense::query()
            ->where('organization_id', $this->organizationId())
            ->when($this->startDate, fn($q) => $q->whereDate('expense_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('expense_date', '<=', $this->endDate))
            ->when($this->vehicle_id, fn($q) => $q->where('vehicle_id', $this->vehicle_id))
            ->when($this->category, fn($q) => $q->where('expense_category', $this->category))
            ->when($this->expense_group_id, fn($q) => $q->where('expense_group_id', $this->expense_group_id));
    }

    private function computeDashboardStats(Collection $expenses): array
    {
        $totalAmount = (float) $expenses->sum('total_ttc');
        $expenseCount = (int) $expenses->count();
        $vehicleCount = (int) $expenses->whereNotNull('vehicle_id')->pluck('vehicle_id')->unique()->count();

        $topCategory = $expenses
            ->groupBy('expense_category')
            ->map(fn(Collection $group, string $category) => [
                'key' => $category,
                'name' => VehicleExpense::EXPENSE_CATEGORIES[$category] ?? ucfirst(str_replace('_', ' ', $category)),
                'amount' => (float) $group->sum('total_ttc'),
            ])
            ->sortByDesc('amount')
            ->first();

        return [
            'total_amount' => $totalAmount,
            'expense_count' => $expenseCount,
            'vehicle_count' => $vehicleCount,
            'avg_per_vehicle' => $vehicleCount > 0 ? ($totalAmount / $vehicleCount) : 0.0,
            'top_category' => $topCategory ?: ['name' => 'N/A', 'amount' => 0.0],
        ];
    }

    private function computeComplianceScore(Collection $expenses): float
    {
        if ($expenses->isEmpty()) {
            return 100.0;
        }

        $invoiceCompliance = ($expenses->whereNotNull('invoice_number')->count() / $expenses->count()) * 100;
        $approvalCompliance = ($expenses->whereIn('approval_status', ['approved', 'draft'])->count() / $expenses->count()) * 100;
        $documentationCompliance = ($expenses->filter(fn($e) => mb_strlen((string) $e->description) >= 10)->count() / $expenses->count()) * 100;

        return round(($invoiceCompliance + $approvalCompliance + $documentationCompliance) / 3, 1);
    }

    private function loadDashboardData(Collection $expenses): void
    {
        $this->categoryBreakdown = $this->computeCategoryBreakdown($expenses);
        $this->preparePieChartData($this->categoryBreakdown);

        $this->vehicleCosts = $this->computeVehicleCosts($expenses)->take(10)->values()->all();

        $monthlyPeriods = $this->computeMonthlyPeriods($expenses);
        $this->trends = ['monthly' => ['periods' => $monthlyPeriods]];
        $this->prepareLineChartData(['periods' => $monthlyPeriods]);
    }

    private function loadTCOAnalysis(Collection $expenses): void
    {
        $vehicleCosts = $this->computeVehicleCosts($expenses);

        $totalTco = (float) $vehicleCosts->sum('total_cost');
        $vehicleCount = (int) $vehicleCosts->count();
        $avgTco = $vehicleCount > 0 ? $totalTco / $vehicleCount : 0.0;
        $avgCostPerKm = (float) $vehicleCosts->avg('cost_per_km');

        $this->tcoData = [
            'total_tco' => $totalTco,
            'avg_tco' => $avgTco,
            'avg_cost_per_km' => $avgCostPerKm ?: 0.0,
            'vehicles' => $vehicleCosts->values()->all(),
        ];

        $this->chartLabels = $vehicleCosts->pluck('license_plate')->values()->all();
        $this->chartData = $vehicleCosts->pluck('tco')->map(fn($v) => (float) $v)->values()->all();
    }

    private function loadTrendsAnalysis(Collection $expenses): void
    {
        $monthlyPeriods = $this->computeMonthlyPeriods($expenses);

        $this->trends = [
            'monthly' => ['periods' => $monthlyPeriods],
        ];

        $this->predictions = $this->computePredictionsFromMonthly($monthlyPeriods);
    }

    private function loadSuppliersAnalysis(Collection $expenses): void
    {
        $withSupplier = $expenses->whereNotNull('supplier_id');
        $supplierIds = $withSupplier->pluck('supplier_id')->unique()->values();

        $suppliers = Supplier::query()
            ->whereIn('id', $supplierIds)
            ->get()
            ->keyBy('id');

        $topSuppliers = $withSupplier
            ->groupBy('supplier_id')
            ->map(function (Collection $group, $supplierId) use ($suppliers) {
                $supplier = $suppliers->get((int) $supplierId);
                $paidExpenses = $group->filter(fn($e) => !empty($e->payment_date));
                $avgDelay = $paidExpenses->isEmpty()
                    ? 0
                    : round($paidExpenses->avg(function ($expense) {
                        return Carbon::parse($expense->expense_date)->diffInDays(Carbon::parse($expense->payment_date));
                    }));

                return [
                    'supplier_id' => (int) $supplierId,
                    'name' => $supplier?->company_name ?? $supplier?->name ?? 'Non défini',
                    'total_amount' => (float) $group->sum('total_ttc'),
                    'expense_count' => (int) $group->count(),
                    'payment_delay' => (int) $avgDelay,
                ];
            })
            ->sortByDesc('total_amount')
            ->values()
            ->take(10)
            ->all();

        $this->supplierAnalysis = [
            'top_suppliers' => $topSuppliers,
            'total_suppliers' => count($topSuppliers),
            'total_amount' => array_sum(array_column($topSuppliers, 'total_amount')),
        ];
    }

    private function loadBudgetsAnalysis(Collection $expenses): void
    {
        $groups = ExpenseGroup::query()
            ->where('organization_id', $this->organizationId())
            ->when($this->expense_group_id, fn($q) => $q->where('id', $this->expense_group_id))
            ->orderBy('name')
            ->get();

        $rows = $groups->map(function ($group) use ($expenses) {
            $allocated = (float) ($group->budget_allocated ?? 0);
            $used = (float) $expenses->where('expense_group_id', $group->id)->sum('total_ttc');
            $usage = $allocated > 0 ? ($used / $allocated) * 100 : 0.0;

            return [
                'id' => $group->id,
                'name' => $group->name,
                'budget_allocated' => $allocated,
                'budget_used' => $used,
                'budget_remaining' => $allocated - $used,
                'usage_percentage' => $usage,
            ];
        })->values();

        $totalAllocated = (float) $rows->sum('budget_allocated');
        $totalUsed = (float) $rows->sum('budget_used');

        $this->budgetAnalysis = [
            'groups' => $rows->all(),
            'total_allocated' => $totalAllocated,
            'total_used' => $totalUsed,
            'total_remaining' => $totalAllocated - $totalUsed,
        ];
    }

    private function loadAdvancedMetrics(Collection $expenses): void
    {
        $fuelTotal = (float) $expenses->where('expense_category', 'carburant')->sum('total_ttc');
        $maintenanceTotal = (float) $expenses
            ->whereIn('expense_category', ['maintenance_preventive', 'reparation', 'pieces_detachees'])
            ->sum('total_ttc');

        $vehicleIds = $expenses->whereNotNull('vehicle_id')->pluck('vehicle_id')->unique()->values();
        $totalDistance = 0.0;

        if ($vehicleIds->isNotEmpty() && Schema::hasTable('vehicle_mileage_readings')) {
            $readings = \DB::table('vehicle_mileage_readings')
                ->whereIn('vehicle_id', $vehicleIds)
                ->whereBetween('recorded_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay(),
                ])
                ->orderBy('recorded_at')
                ->get(['vehicle_id', 'mileage']);

            $totalDistance = $readings
                ->groupBy('vehicle_id')
                ->sum(function (Collection $group) {
                    if ($group->count() < 2) {
                        return 0;
                    }
                    return max(0, ((float) $group->last()->mileage) - ((float) $group->first()->mileage));
                });
        }

        $fuelQuantity = (float) $expenses->sum('fuel_quantity');
        $fuelEfficiency = $totalDistance > 0 ? ($fuelQuantity / $totalDistance) * 100 : 0.0;
        $maintenanceCostPerKm = $totalDistance > 0 ? ($maintenanceTotal / $totalDistance) : 0.0;

        $preventive = (float) $expenses->where('expense_category', 'maintenance_preventive')->sum('total_ttc');
        $corrective = (float) $expenses->where('expense_category', 'reparation')->sum('total_ttc');
        $maintenanceRoi = $preventive > 0 ? (($corrective - $preventive) / $preventive) * 100 : 0.0;

        $this->efficiencyMetrics = [
            'fuel_efficiency' => round($fuelEfficiency, 2),
            'maintenance_cost_per_km' => round($maintenanceCostPerKm, 2),
            'avg_downtime' => 0.0,
            'maintenance_roi' => round($maintenanceRoi, 1),
            'fuel_total' => $fuelTotal,
            'maintenance_total' => $maintenanceTotal,
        ];
    }

    private function computeCategoryBreakdown(Collection $expenses): array
    {
        $total = (float) $expenses->sum('total_ttc');

        $categories = $expenses
            ->groupBy('expense_category')
            ->map(function (Collection $group, string $category) use ($total) {
                $amount = (float) $group->sum('total_ttc');
                return [
                    'category' => $category,
                    'label' => VehicleExpense::EXPENSE_CATEGORIES[$category] ?? ucfirst(str_replace('_', ' ', $category)),
                    'total' => $amount,
                    'count' => (int) $group->count(),
                    'percentage' => $total > 0 ? ($amount / $total) * 100 : 0.0,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        return ['categories' => $categories];
    }

    private function computeVehicleCosts(Collection $expenses): Collection
    {
        $grouped = $expenses->whereNotNull('vehicle_id')->groupBy('vehicle_id');
        $vehicleIds = $grouped->keys()->map(fn($id) => (int) $id)->values();

        $vehicles = Vehicle::query()
            ->whereIn('id', $vehicleIds)
            ->get()
            ->keyBy('id');

        return $grouped
            ->map(function (Collection $vehicleExpenses, $vehicleId) use ($vehicles) {
                $vehicle = $vehicles->get((int) $vehicleId);
                $totalCost = (float) $vehicleExpenses->sum('total_ttc');
                $distance = (float) ($vehicle?->current_mileage ?? 0);

                return [
                    'vehicle_id' => (int) $vehicleId,
                    'license_plate' => $vehicle?->registration_plate ?? 'N/A',
                    'brand' => $vehicle?->brand ?? '',
                    'model' => $vehicle?->model ?? '',
                    'total_cost' => $totalCost,
                    'expense_count' => (int) $vehicleExpenses->count(),
                    'tco' => $totalCost,
                    'cost_per_km' => $distance > 0 ? ($totalCost / $distance) : 0.0,
                ];
            })
            ->sortByDesc('total_cost')
            ->values();
    }

    private function computeMonthlyPeriods(Collection $expenses): array
    {
        $start = Carbon::parse($this->startDate)->startOfMonth();
        $end = Carbon::parse($this->endDate)->endOfMonth();

        $grouped = $expenses->groupBy(fn($expense) => Carbon::parse($expense->expense_date)->format('Y-m'));
        $periods = [];

        foreach (CarbonPeriod::create($start, '1 month', $end) as $month) {
            $key = $month->format('Y-m');
            $bucket = $grouped->get($key, collect());

            $periods[] = [
                'label' => $month->translatedFormat('M Y'),
                'total_amount' => (float) $bucket->sum('total_ttc'),
                'expense_count' => (int) $bucket->count(),
            ];
        }

        return $periods;
    }

    private function computePredictionsFromMonthly(array $periods): array
    {
        $history = collect($periods)->pluck('total_amount')->map(fn($v) => (float) $v);
        $base = $history->take(-3)->avg() ?? $history->avg() ?? 0.0;

        $forecastStart = Carbon::parse($this->endDate)->startOfMonth();
        $monthly = [];

        for ($i = 1; $i <= 3; $i++) {
            $month = $forecastStart->copy()->addMonths($i);
            $amount = $base * (1 + (0.03 * ($i - 1)));
            $confidence = max(60, 90 - ($i * 5));
            $monthly[$month->translatedFormat('M Y')] = [
                'amount' => round($amount, 2),
                'confidence' => $confidence,
            ];
        }

        return ['monthly' => $monthly];
    }

    private function preparePieChartData($categoryData): void
    {
        if (!$categoryData || !isset($categoryData['categories'])) {
            return;
        }

        $labels = [];
        $values = [];
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
            '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6',
            '#F97316', '#06B6D4', '#84CC16', '#A855F7',
        ];

        foreach ($categoryData['categories'] as $cat) {
            $labels[] = VehicleExpense::EXPENSE_CATEGORIES[$cat['category']] ?? $cat['category'];
            $values[] = $cat['total'] ?? 0;
        }

        $this->pieChartData = [
            'labels' => $labels,
            'datasets' => [[
                'data' => $values,
                'backgroundColor' => array_slice($colors, 0, count($labels)),
            ]],
        ];
    }

    private function prepareLineChartData($trendsData): void
    {
        if (!$trendsData || !isset($trendsData['periods'])) {
            return;
        }

        $labels = [];
        $amounts = [];
        $counts = [];

        foreach ($trendsData['periods'] as $period) {
            $labels[] = $period['label'] ?? '';
            $amounts[] = $period['total_amount'] ?? 0;
            $counts[] = $period['expense_count'] ?? 0;
        }

        $this->chartLabels = $labels;
        $this->chartData = [
            'datasets' => [
                [
                    'label' => 'Montant Total (DZD)',
                    'data' => $amounts,
                    'borderColor' => '#3B82F6',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Nombre de Dépenses',
                    'data' => $counts,
                    'borderColor' => '#10B981',
                    'yAxisID' => 'y1',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    // Mise a jour des filtres
    public function updatedPeriod(): void
    {
        $this->initializeDates();
        $this->loadAnalytics();
    }

    public function updatedStartDate(): void
    {
        $this->period = 'custom';
        $this->loadAnalytics();
    }

    public function updatedEndDate(): void
    {
        $this->period = 'custom';
        $this->loadAnalytics();
    }

    public function updatedVehicleId(): void
    {
        $this->loadAnalytics();
    }

    public function updatedCategory(): void
    {
        $this->loadAnalytics();
    }

    public function updatedExpenseGroupId(): void
    {
        $this->loadAnalytics();
    }

    public function updatedViewMode(): void
    {
        $this->loadAnalytics();
    }

    // Actions
    public function toggleAdvancedMetrics(): void
    {
        $this->showAdvancedMetrics = !$this->showAdvancedMetrics;
        if ($this->showAdvancedMetrics) {
            $this->loadAnalytics();
        }
    }

    public function exportData($format = 'csv')
    {
        $service = app(VehicleExpenseService::class);

        try {
            $expenses = $this->baseExpensesQuery()->get();
            $exportPath = $service->export($expenses, $format);

            return response()->download($exportPath)->deleteFileAfterSend();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage(),
            ]);
        }

        return null;
    }

    public function generateReport(): void
    {
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Generation du rapport en cours...',
        ]);
    }
}
