<?php

namespace App\Livewire\Admin\VehicleExpenses;

use App\Services\ExpenseAnalyticsService;
use App\Services\VehicleExpenseService;
use App\Models\VehicleExpense;
use App\Models\Vehicle;
use App\Models\ExpenseGroup;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExpenseAnalytics extends Component
{
    // Filtres
    public $period = 'month'; // month, quarter, year, custom
    public $startDate;
    public $endDate;
    public $vehicle_id = '';
    public $category = '';
    public $expense_group_id = '';
    
    // Données analytics
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

    public function mount()
    {
        $this->initializeDates();
        $this->loadAnalytics();
    }

    private function initializeDates()
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
        }
    }

    public function render()
    {
        $plateColumn = Schema::hasColumn('vehicles', 'license_plate') ? 'license_plate' : 'registration_plate';

        $vehicles = Vehicle::where('organization_id', Auth::user()->organization_id)
            ->active()
            ->orderBy($plateColumn)
            ->get();
            
        $expenseGroups = ExpenseGroup::where('organization_id', Auth::user()->organization_id)
            ->active()
            ->orderBy('name')
            ->get();
            
        return view('livewire.admin.vehicle-expenses.expense-analytics', [
            'vehicles' => $vehicles,
            'expenseGroups' => $expenseGroups,
            'categories' => VehicleExpense::EXPENSE_CATEGORIES,
        ]);
    }

    public function loadAnalytics()
    {
        $service = app(ExpenseAnalyticsService::class);
        
        // Appliquer les filtres
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'vehicle_id' => $this->vehicle_id ?: null,
            'category' => $this->category ?: null,
            'expense_group_id' => $this->expense_group_id ?: null,
        ];
        
        try {
            // Charger les statistiques du dashboard
            $this->dashboardStats = $service->getDashboardStats($filters);
            
            // Charger les données selon le mode d'affichage
            switch ($this->viewMode) {
                case 'tco':
                    $this->loadTCOAnalysis($service, $filters);
                    break;
                case 'trends':
                    $this->loadTrendsAnalysis($service, $filters);
                    break;
                case 'suppliers':
                    $this->loadSuppliersAnalysis($service, $filters);
                    break;
                case 'budgets':
                    $this->loadBudgetsAnalysis($service, $filters);
                    break;
                default:
                    $this->loadDashboardData($service, $filters);
            }
            
            // Charger les métriques avancées si activées
            if ($this->showAdvancedMetrics) {
                $this->loadAdvancedMetrics($service, $filters);
            }

            // Keep frontend chart widgets in sync after every Livewire refresh.
            $this->dispatch('dashboard:data-updated');
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors du chargement des analytics: ' . $e->getMessage()
            ]);
        }
    }

    private function loadDashboardData($service, $filters)
    {
        // Répartition par catégorie
        $this->categoryBreakdown = $service->getCategoryBreakdown($filters);
        $this->preparePieChartData($this->categoryBreakdown);
        
        // Coûts par véhicule (Top 10)
        $this->vehicleCosts = collect($service->getVehicleCosts($filters))
            ->take(10)
            ->toArray();
        
        // Tendances mensuelles
        $this->trends = $service->getTrends(array_merge($filters, ['period' => 'monthly']));
        $this->prepareLineChartData($this->trends);
        
        // Score de conformité
        $this->complianceScore = $service->getComplianceScore($filters);
    }

    private function loadTCOAnalysis($service, $filters)
    {
        $this->tcoData = $service->calculateTCO($filters);
        
        // Préparer les données pour graphique TCO
        if ($this->tcoData && isset($this->tcoData['vehicles'])) {
            $labels = [];
            $data = [];
            
            foreach ($this->tcoData['vehicles'] as $vehicle) {
                $labels[] = $vehicle['license_plate'] ?? 'N/A';
                $data[] = $vehicle['tco'] ?? 0;
            }
            
            $this->chartLabels = $labels;
            $this->chartData = $data;
        }
    }

    private function loadTrendsAnalysis($service, $filters)
    {
        // Tendances avec différentes périodes
        $this->trends = [
            'daily' => $service->getTrends(array_merge($filters, ['period' => 'daily'])),
            'weekly' => $service->getTrends(array_merge($filters, ['period' => 'weekly'])),
            'monthly' => $service->getTrends(array_merge($filters, ['period' => 'monthly'])),
            'yearly' => $service->getTrends(array_merge($filters, ['period' => 'yearly'])),
        ];
        
        // Prédictions
        $this->predictions = $service->getPredictions($filters);
        
        // Patterns saisonniers
        if (isset($this->trends['monthly']['seasonal_patterns'])) {
            $this->prepareSeasonalChart($this->trends['monthly']['seasonal_patterns']);
        }
    }

    private function loadSuppliersAnalysis($service, $filters)
    {
        $this->supplierAnalysis = $service->getSupplierAnalysis($filters);
        
        // Préparer graphique top fournisseurs
        if ($this->supplierAnalysis && isset($this->supplierAnalysis['top_suppliers'])) {
            $labels = [];
            $amounts = [];
            $counts = [];
            
            foreach ($this->supplierAnalysis['top_suppliers'] as $supplier) {
                $labels[] = $supplier['name'] ?? 'Non spécifié';
                $amounts[] = $supplier['total_amount'] ?? 0;
                $counts[] = $supplier['expense_count'] ?? 0;
            }
            
            $this->chartLabels = $labels;
            $this->chartData = [
                'amounts' => $amounts,
                'counts' => $counts,
            ];
        }
    }

    private function loadBudgetsAnalysis($service, $filters)
    {
        $this->budgetAnalysis = $service->analyzeBudgets($filters);
        
        // Préparer graphique utilisation budget
        if ($this->budgetAnalysis && isset($this->budgetAnalysis['groups'])) {
            $labels = [];
            $allocated = [];
            $used = [];
            $percentages = [];
            
            foreach ($this->budgetAnalysis['groups'] as $group) {
                $labels[] = $group['name'] ?? 'N/A';
                $allocated[] = $group['budget_allocated'] ?? 0;
                $used[] = $group['budget_used'] ?? 0;
                $percentages[] = $group['usage_percentage'] ?? 0;
            }
            
            $this->chartLabels = $labels;
            $this->chartData = [
                'allocated' => $allocated,
                'used' => $used,
                'percentages' => $percentages,
            ];
        }
    }

    private function loadAdvancedMetrics($service, $filters)
    {
        // Métriques d'efficacité
        $this->efficiencyMetrics = $service->getEfficiencyMetrics($filters);
        
        // Performance des chauffeurs
        $driverPerformance = $service->getDriverPerformance($filters);
        if ($driverPerformance) {
            $this->efficiencyMetrics['driver_performance'] = $driverPerformance;
        }
    }

    private function preparePieChartData($categoryData)
    {
        if (!$categoryData || !isset($categoryData['categories'])) {
            return;
        }
        
        $labels = [];
        $values = [];
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', 
            '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6',
            '#F97316', '#06B6D4', '#84CC16', '#A855F7'
        ];
        
        $i = 0;
        foreach ($categoryData['categories'] as $cat) {
            $labels[] = VehicleExpense::EXPENSE_CATEGORIES[$cat['category']] ?? $cat['category'];
            $values[] = $cat['total'] ?? 0;
            $i++;
        }
        
        $this->pieChartData = [
            'labels' => $labels,
            'datasets' => [[
                'data' => $values,
                'backgroundColor' => array_slice($colors, 0, count($labels)),
            ]]
        ];
    }

    private function prepareLineChartData($trendsData)
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
                ]
            ]
        ];
    }

    private function prepareSeasonalChart($seasonalData)
    {
        // Préparer les données pour afficher les patterns saisonniers
        // Implémentation spécifique selon les besoins
    }

    // Mise à jour des filtres
    public function updatedPeriod()
    {
        $this->initializeDates();
        $this->loadAnalytics();
    }

    public function updatedStartDate()
    {
        $this->period = 'custom';
        $this->loadAnalytics();
    }

    public function updatedEndDate()
    {
        $this->period = 'custom';
        $this->loadAnalytics();
    }

    public function updatedVehicleId()
    {
        $this->loadAnalytics();
    }

    public function updatedCategory()
    {
        $this->loadAnalytics();
    }

    public function updatedExpenseGroupId()
    {
        $this->loadAnalytics();
    }

    public function updatedViewMode()
    {
        $this->loadAnalytics();
    }

    // Actions
    public function toggleAdvancedMetrics()
    {
        $this->showAdvancedMetrics = !$this->showAdvancedMetrics;
        if ($this->showAdvancedMetrics) {
            $this->loadAnalytics();
        }
    }

    public function exportData($format = 'csv')
    {
        $service = app(VehicleExpenseService::class);
        
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'vehicle_id' => $this->vehicle_id ?: null,
            'category' => $this->category ?: null,
            'expense_group_id' => $this->expense_group_id ?: null,
        ];
        
        try {
            $expenses = VehicleExpense::where('organization_id', Auth::user()->organization_id)
                ->when($filters['start_date'], fn($q) => $q->whereDate('expense_date', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('expense_date', '<=', $filters['end_date']))
                ->when($filters['vehicle_id'], fn($q) => $q->where('vehicle_id', $filters['vehicle_id']))
                ->when($filters['category'], fn($q) => $q->where('category', $filters['category']))
                ->when($filters['expense_group_id'], fn($q) => $q->where('expense_group_id', $filters['expense_group_id']))
                ->get();
            
            $exportPath = $service->export($expenses, $format);
            
            return response()->download($exportPath)->deleteFileAfterSend();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ]);
        }
    }

    public function generateReport()
    {
        // Générer un rapport PDF complet
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Génération du rapport en cours...'
        ]);
        
        // TODO: Implémenter la génération de rapport PDF
    }
}
