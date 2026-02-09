<?php

namespace App\Services;

use App\Models\VehicleExpense;
use App\Models\Vehicle;
use App\Models\ExpenseGroup;
use App\Models\User;
use App\Support\Analytics\AnalyticsCacheVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * ExpenseAnalyticsService - Analytics avancés pour les dépenses
 * 
 * @package App\Services
 * @version 1.0.0-Enterprise
 * @since 2025-10-27
 */
class ExpenseAnalyticsService
{
    /**
     * Obtenir les statistiques du dashboard
     * 
     * @param int $organizationId
     * @return array
     */
    public function getDashboardStats(int $organizationId): array
    {
        return $this->rememberAnalytics(
            scope: 'dashboard_stats',
            organizationId: $organizationId,
            context: ['month' => now()->format('Y-m')],
            ttl: config('analytics.cache.ttl.realtime', 300),
            callback: function () use ($organizationId): array {
                $currentMonth = now()->startOfMonth();
                $lastMonth = now()->subMonth()->startOfMonth();
                $currentYear = now()->startOfYear();

                // Statistiques du mois en cours
                $currentMonthExpenses = VehicleExpense::where('organization_id', $organizationId)
                    ->whereBetween('expense_date', [$currentMonth, now()])
                    ->get();

                // Statistiques du mois précédent pour comparaison
                $lastMonthExpenses = VehicleExpense::where('organization_id', $organizationId)
                    ->whereBetween('expense_date', [$lastMonth, $currentMonth->copy()->subDay()])
                    ->get();

                // Statistiques de l'année
                $yearExpenses = VehicleExpense::where('organization_id', $organizationId)
                    ->whereBetween('expense_date', [$currentYear, now()])
                    ->get();

                return [
                    'current_month' => [
                        'total' => $currentMonthExpenses->sum('total_ttc'),
                        'count' => $currentMonthExpenses->count(),
                        'average' => $currentMonthExpenses->avg('total_ttc') ?? 0,
                        'by_category' => $this->groupByCategory($currentMonthExpenses),
                    ],
                    'last_month' => [
                        'total' => $lastMonthExpenses->sum('total_ttc'),
                        'count' => $lastMonthExpenses->count(),
                        'average' => $lastMonthExpenses->avg('total_ttc') ?? 0,
                    ],
                    'year_to_date' => [
                        'total' => $yearExpenses->sum('total_ttc'),
                        'count' => $yearExpenses->count(),
                        'average' => $yearExpenses->avg('total_ttc') ?? 0,
                        'months' => $this->getMonthlyTrend($organizationId, now()->year),
                    ],
                    'growth' => [
                        'amount' => $currentMonthExpenses->sum('total_ttc') - $lastMonthExpenses->sum('total_ttc'),
                        'percentage' => $this->calculateGrowth(
                            $currentMonthExpenses->sum('total_ttc'),
                            $lastMonthExpenses->sum('total_ttc')
                        ),
                    ],
                    'pending_approvals' => VehicleExpense::where('organization_id', $organizationId)
                        ->whereIn('approval_status', ['pending_level1', 'pending_level2'])
                        ->count(),
                    'unpaid_expenses' => VehicleExpense::where('organization_id', $organizationId)
                        ->where('approval_status', 'approved')
                        ->where('payment_status', '!=', 'paid')
                        ->sum('total_ttc'),
                ];
            }
        );
    }

    /**
     * Analytics complets pour la page dédiée
     * 
     * @param int $organizationId
     * @param string $period
     * @param int $year
     * @return array
     */
    public function getComprehensiveAnalytics(int $organizationId, string $period, int $year): array
    {
        return $this->rememberAnalytics(
            scope: 'comprehensive',
            organizationId: $organizationId,
            context: ['period' => $period, 'year' => $year],
            ttl: config('analytics.cache.ttl.historical', 1800),
            callback: function () use ($organizationId, $period, $year): array {
                return [
                    'meta' => [
                        'organization_id' => $organizationId,
                        'period' => $period,
                        'year' => $year,
                        'generated_at' => now()->toIso8601String(),
                        'timezone' => Auth::user()?->timezone ?? config('app.timezone'),
                        'currency' => config('algeria.currency.code', 'DZD'),
                    ],
                    'period' => $period,
                    'year' => $year,
                    'tco' => $this->calculateTCO($organizationId, $year),
                    'budget_analysis' => $this->analyzeBudgets($organizationId, $year),
                    'category_breakdown' => $this->getCategoryBreakdown($organizationId, $period, $year),
                    'vehicle_costs' => $this->getVehicleCosts($organizationId, $period, $year),
                    'supplier_analysis' => $this->getSupplierAnalysis($organizationId, $year),
                    'driver_performance' => $this->getDriverPerformance($organizationId, $year),
                    'trends' => $this->getTrends($organizationId, $year),
                    'predictions' => $this->getPredictions($organizationId),
                    'efficiency_metrics' => $this->getEfficiencyMetrics($organizationId, $year),
                    'compliance_score' => $this->getComplianceScore($organizationId),
                ];
            }
        );
    }

    protected function rememberAnalytics(string $scope, int $organizationId, array $context, int $ttl, \Closure $callback): array
    {
        $key = $this->buildCacheKey($scope, $organizationId, $context);

        return Cache::remember($key, $ttl, $callback);
    }

    protected function buildCacheKey(string $scope, int $organizationId, array $context): string
    {
        ksort($context);

        $role = Auth::check()
            ? (Auth::user()->getRoleNames()->first() ?? 'user')
            : 'guest';

        return sprintf(
            'expense_analytics:%s:org:%d:role:%s:v:%d:%s',
            $scope,
            $organizationId,
            $role,
            AnalyticsCacheVersion::current('expenses', $organizationId),
            md5(json_encode($context))
        );
    }

    /**
     * Calculer le TCO (Total Cost of Ownership) par véhicule
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function calculateTCO(int $organizationId, int $year): array
    {
        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->with(['expenses' => function ($query) use ($year) {
                $query->whereYear('expense_date', $year);
            }])
            ->get();

        $tcoData = [];
        
        foreach ($vehicles as $vehicle) {
            $totalCost = $vehicle->expenses->sum('total_ttc');
            $totalDistance = $this->calculateVehicleDistance($vehicle, $year);
            
            $tcoData[] = [
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_plate,
                'brand_model' => $vehicle->brand . ' ' . $vehicle->model,
                'total_cost' => $totalCost,
                'total_distance' => $totalDistance,
                'cost_per_km' => $totalDistance > 0 ? $totalCost / $totalDistance : 0,
                'monthly_average' => $totalCost / 12,
                'breakdown' => [
                    'fuel' => $vehicle->expenses->where('expense_category', 'carburant')->sum('total_ttc'),
                    'maintenance' => $vehicle->expenses->whereIn('expense_category', [
                        'maintenance_preventive', 'reparation', 'pieces_detachees'
                    ])->sum('total_ttc'),
                    'insurance' => $vehicle->expenses->where('expense_category', 'assurance')->sum('total_ttc'),
                    'taxes' => $vehicle->expenses->whereIn('expense_category', [
                        'vignette', 'controle_technique'
                    ])->sum('total_ttc'),
                    'fines' => $vehicle->expenses->where('expense_category', 'amendes')->sum('total_ttc'),
                    'other' => $vehicle->expenses->whereNotIn('expense_category', [
                        'carburant', 'maintenance_preventive', 'reparation', 'pieces_detachees',
                        'assurance', 'vignette', 'controle_technique', 'amendes'
                    ])->sum('total_ttc'),
                ],
                'efficiency_score' => $this->calculateVehicleEfficiencyScore($vehicle, $totalCost, $totalDistance),
            ];
        }

        // Trier par coût par km
        usort($tcoData, function ($a, $b) {
            return $b['cost_per_km'] <=> $a['cost_per_km'];
        });

        return [
            'vehicles' => $tcoData,
            'summary' => [
                'total_fleet_cost' => array_sum(array_column($tcoData, 'total_cost')),
                'average_cost_per_vehicle' => count($tcoData) > 0 ? 
                    array_sum(array_column($tcoData, 'total_cost')) / count($tcoData) : 0,
                'average_cost_per_km' => count($tcoData) > 0 ?
                    array_sum(array_column($tcoData, 'cost_per_km')) / count($tcoData) : 0,
                'most_expensive' => $tcoData[0] ?? null,
                'most_efficient' => end($tcoData) ?: null,
            ],
        ];
    }

    /**
     * Analyser les budgets et leur utilisation
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function analyzeBudgets(int $organizationId, int $year): array
    {
        $groups = ExpenseGroup::where('organization_id', $organizationId)
            ->where('fiscal_year', $year)
            ->with('expenses')
            ->get();

        $analysis = [];
        $totalAllocated = 0;
        $totalUsed = 0;
        
        foreach ($groups as $group) {
            $totalAllocated += $group->budget_allocated;
            $totalUsed += $group->budget_used;
            
            $analysis[] = [
                'group' => $group->name,
                'period' => $group->fiscal_period_label,
                'allocated' => $group->budget_allocated,
                'used' => $group->budget_used,
                'remaining' => $group->budget_remaining,
                'usage_percentage' => $group->budget_usage_percentage,
                'status' => $group->budget_status_color,
                'is_over_budget' => $group->is_over_budget,
                'expenses_count' => $group->expenses->count(),
                'average_expense' => $group->expenses->avg('total_ttc') ?? 0,
                'projection' => $this->projectBudgetUsage($group),
            ];
        }

        return [
            'groups' => $analysis,
            'summary' => [
                'total_allocated' => $totalAllocated,
                'total_used' => $totalUsed,
                'total_remaining' => $totalAllocated - $totalUsed,
                'overall_usage' => $totalAllocated > 0 ? ($totalUsed / $totalAllocated) * 100 : 0,
                'groups_over_budget' => collect($analysis)->where('is_over_budget', true)->count(),
                'groups_near_threshold' => collect($analysis)->where('usage_percentage', '>=', 80)->count(),
            ],
        ];
    }

    /**
     * Obtenir la répartition par catégorie
     * 
     * @param int $organizationId
     * @param string $period
     * @param int $year
     * @return array
     */
    public function getCategoryBreakdown(int $organizationId, string $period, int $year): array
    {
        $query = VehicleExpense::where('organization_id', $organizationId);
        
        // Appliquer la période
        switch ($period) {
            case 'month':
                $query->whereMonth('expense_date', now()->month)
                      ->whereYear('expense_date', $year);
                break;
            case 'quarter':
                $quarter = ceil(now()->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                $query->whereBetween('expense_date', [
                    Carbon::create($year, $startMonth, 1),
                    Carbon::create($year, $endMonth)->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereYear('expense_date', $year);
                break;
        }
        
        $expenses = $query->get();
        $total = $expenses->sum('total_ttc');
        
        $categories = [];
        $groupedExpenses = $expenses->groupBy('expense_category');
        
        foreach ($groupedExpenses as $category => $categoryExpenses) {
            $categoryTotal = $categoryExpenses->sum('total_ttc');
            $categories[] = [
                'category' => $category,
                'label' => $this->getCategoryLabel($category),
                'total' => $categoryTotal,
                'count' => $categoryExpenses->count(),
                'percentage' => $total > 0 ? ($categoryTotal / $total) * 100 : 0,
                'average' => $categoryExpenses->avg('total_ttc'),
                'trend' => $this->getCategoryTrend($organizationId, $category, $period, $year),
            ];
        }
        
        // Trier par montant total
        usort($categories, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        return $categories;
    }

    /**
     * Obtenir les coûts par véhicule
     * 
     * @param int $organizationId
     * @param string $period
     * @param int $year
     * @return array
     */
    public function getVehicleCosts(int $organizationId, string $period, int $year): array
    {
        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->with(['expenses' => function ($query) use ($period, $year) {
                $this->applyPeriodFilter($query, $period, $year);
            }])
            ->get();

        $vehicleCosts = [];
        
        foreach ($vehicles as $vehicle) {
            if ($vehicle->expenses->count() === 0) continue;
            
            $vehicleCosts[] = [
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_plate,
                'brand_model' => $vehicle->brand . ' ' . $vehicle->model,
                'total_cost' => $vehicle->expenses->sum('total_ttc'),
                'expense_count' => $vehicle->expenses->count(),
                'average_expense' => $vehicle->expenses->avg('total_ttc'),
                'fuel_cost' => $vehicle->expenses->where('expense_category', 'carburant')->sum('total_ttc'),
                'maintenance_cost' => $vehicle->expenses->whereIn('expense_category', [
                    'maintenance_preventive', 'reparation'
                ])->sum('total_ttc'),
                'status' => $vehicle->status_id,
                'mileage' => $vehicle->current_mileage,
            ];
        }
        
        // Trier par coût total
        usort($vehicleCosts, function ($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });
        
        return [
            'vehicles' => $vehicleCosts,
            'top_5_expensive' => array_slice($vehicleCosts, 0, 5),
            'top_5_economical' => array_slice(array_reverse($vehicleCosts), 0, 5),
        ];
    }

    /**
     * Analyse des fournisseurs
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function getSupplierAnalysis(int $organizationId, int $year): array
    {
        $expenses = VehicleExpense::where('organization_id', $organizationId)
            ->whereYear('expense_date', $year)
            ->whereNotNull('supplier_id')
            ->with('supplier')
            ->get()
            ->groupBy('supplier_id');

        $suppliers = [];
        
        foreach ($expenses as $supplierId => $supplierExpenses) {
            $supplier = $supplierExpenses->first()->supplier;
            if (!$supplier) continue;
            
            $suppliers[] = [
                'supplier_id' => $supplierId,
                'name' => $supplier->name,
                'total_amount' => $supplierExpenses->sum('total_ttc'),
                'transaction_count' => $supplierExpenses->count(),
                'average_transaction' => $supplierExpenses->avg('total_ttc'),
                'categories' => $supplierExpenses->groupBy('expense_category')
                    ->map(fn($group) => [
                        'count' => $group->count(),
                        'total' => $group->sum('total_ttc')
                    ]),
                'payment_terms' => $this->analyzePaymentTerms($supplierExpenses),
            ];
        }
        
        // Trier par montant total
        usort($suppliers, function ($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });
        
        return [
            'suppliers' => $suppliers,
            'top_10' => array_slice($suppliers, 0, 10),
            'total_suppliers' => count($suppliers),
            'total_amount' => array_sum(array_column($suppliers, 'total_amount')),
        ];
    }

    /**
     * Performance des chauffeurs
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function getDriverPerformance(int $organizationId, int $year): array
    {
        $drivers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Chauffeur');
            })
            ->where('organization_id', $organizationId)
            ->with(['driverExpenses' => function ($query) use ($year) {
                $query->whereYear('expense_date', $year);
            }])
            ->get();

        $performance = [];
        
        foreach ($drivers as $driver) {
            if (!$driver->driverExpenses) continue;
            
            $fuelExpenses = $driver->driverExpenses->where('expense_category', 'carburant');
            $fines = $driver->driverExpenses->where('expense_category', 'amendes');
            
            $performance[] = [
                'driver_id' => $driver->id,
                'name' => $driver->name,
                'total_expenses' => $driver->driverExpenses->sum('total_ttc'),
                'expense_count' => $driver->driverExpenses->count(),
                'fuel_cost' => $fuelExpenses->sum('total_ttc'),
                'fuel_efficiency' => $this->calculateDriverFuelEfficiency($fuelExpenses),
                'fines_count' => $fines->count(),
                'fines_total' => $fines->sum('total_ttc'),
                'score' => $this->calculateDriverScore($driver->driverExpenses),
            ];
        }
        
        // Trier par score
        usort($performance, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return $performance;
    }

    /**
     * Obtenir les tendances
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function getTrends(int $organizationId, int $year): array
    {
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $expenses = VehicleExpense::where('organization_id', $organizationId)
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->get();
            
            $monthlyData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month)->format('F'),
                'total' => $expenses->sum('total_ttc'),
                'count' => $expenses->count(),
                'by_category' => $expenses->groupBy('expense_category')
                    ->map(fn($group) => $group->sum('total_ttc')),
            ];
        }
        
        return [
            'monthly' => $monthlyData,
            'growth_rate' => $this->calculateGrowthRate($monthlyData),
            'seasonal_patterns' => $this->identifySeasonalPatterns($monthlyData),
            'forecast_next_month' => $this->forecastNextMonth($monthlyData),
        ];
    }

    /**
     * Prédictions ML simples
     * 
     * @param int $organizationId
     * @return array
     */
    public function getPredictions(int $organizationId): array
    {
        // Récupérer les données historiques
        $historicalData = $this->getHistoricalData($organizationId, 12);
        
        // Prédictions simples basées sur les moyennes mobiles
        return [
            'next_month_estimated' => $this->predictNextMonth($historicalData),
            'next_quarter_estimated' => $this->predictNextQuarter($historicalData),
            'maintenance_alerts' => $this->predictMaintenanceNeeds($organizationId),
            'budget_warnings' => $this->predictBudgetOverruns($organizationId),
            'confidence_level' => $this->calculateConfidenceLevel($historicalData),
        ];
    }

    /**
     * Métriques d'efficacité
     * 
     * @param int $organizationId
     * @param int $year
     * @return array
     */
    public function getEfficiencyMetrics(int $organizationId, int $year): array
    {
        $expenses = VehicleExpense::where('organization_id', $organizationId)
            ->whereYear('expense_date', $year)
            ->get();
        
        $vehicles = Vehicle::where('organization_id', $organizationId)->count();
        $activeVehicles = Vehicle::where('organization_id', $organizationId)
            ->active()
            ->count();
        
        return [
            'cost_per_vehicle' => $vehicles > 0 ? $expenses->sum('total_ttc') / $vehicles : 0,
            'cost_per_active_vehicle' => $activeVehicles > 0 ? 
                $expenses->sum('total_ttc') / $activeVehicles : 0,
            'approval_efficiency' => [
                'average_time_to_approval' => $this->calculateAverageApprovalTime($expenses),
                'rejection_rate' => $this->calculateRejectionRate($expenses),
                'auto_approved_percentage' => $this->calculateAutoApprovedPercentage($expenses),
            ],
            'payment_efficiency' => [
                'average_time_to_payment' => $this->calculateAveragePaymentTime($expenses),
                'on_time_payment_rate' => $this->calculateOnTimePaymentRate($expenses),
            ],
            'process_efficiency' => [
                'digital_invoices_percentage' => $this->calculateDigitalInvoicesPercentage($expenses),
                'duplicate_detection_rate' => $this->calculateDuplicateDetectionRate($expenses),
            ],
        ];
    }

    /**
     * Score de conformité
     * 
     * @param int $organizationId
     * @return array
     */
    public function getComplianceScore(int $organizationId): array
    {
        $expenses = VehicleExpense::where('organization_id', $organizationId)
            ->whereMonth('expense_date', now()->month)
            ->get();
        
        $scores = [
            'invoice_compliance' => $this->calculateInvoiceCompliance($expenses),
            'approval_compliance' => $this->calculateApprovalCompliance($expenses),
            'documentation_compliance' => $this->calculateDocumentationCompliance($expenses),
            'budget_compliance' => $this->calculateBudgetCompliance($organizationId),
        ];
        
        return [
            'scores' => $scores,
            'overall_score' => array_sum($scores) / count($scores),
            'recommendations' => $this->generateComplianceRecommendations($scores),
        ];
    }

    // ====================================================================
    // MÉTHODES PRIVÉES HELPER
    // ====================================================================

    private function groupByCategory(Collection $expenses): array
    {
        return $expenses->groupBy('expense_category')
            ->map(function ($group) {
                return [
                    'total' => $group->sum('total_ttc'),
                    'count' => $group->count(),
                    'average' => $group->avg('total_ttc'),
                ];
            })
            ->toArray();
    }

    private function calculateGrowth(float $current, float $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    private function getMonthlyTrend(int $organizationId, int $year): array
    {
        $trend = [];
        
        for ($month = 1; $month <= now()->month; $month++) {
            $total = VehicleExpense::where('organization_id', $organizationId)
                ->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->sum('total_ttc');
            
            $trend[] = [
                'month' => $month,
                'total' => $total,
            ];
        }
        
        return $trend;
    }

    private function calculateVehicleDistance(Vehicle $vehicle, int $year): int
    {
        // Calculer la distance parcourue basée sur les relevés kilométriques
        $readings = DB::table('vehicle_mileage_readings')
            ->where('vehicle_id', $vehicle->id)
            ->whereYear('recorded_at', $year)
            ->orderBy('recorded_at')
            ->get();
        
        if ($readings->count() < 2) return 0;
        
        return $readings->last()->mileage - $readings->first()->mileage;
    }

    private function calculateVehicleEfficiencyScore(Vehicle $vehicle, float $totalCost, int $distance): float
    {
        if ($distance == 0) return 0;
        
        // Score basé sur le coût par km (inversé pour que plus bas = meilleur)
        $costPerKm = $totalCost / $distance;
        
        // Score de 0 à 100 (0.50 DZD/km = 100, 2.00 DZD/km = 0)
        $score = max(0, min(100, 100 - (($costPerKm - 0.50) * 66.67)));
        
        return round($score, 2);
    }

    private function getCategoryLabel(string $category): string
    {
        $labels = [
            'carburant' => 'Carburant',
            'maintenance_preventive' => 'Maintenance Préventive',
            'reparation' => 'Réparations',
            'pieces_detachees' => 'Pièces Détachées',
            'assurance' => 'Assurance',
            'controle_technique' => 'Contrôle Technique',
            'vignette' => 'Vignette',
            'amendes' => 'Amendes',
            'peage' => 'Péage',
            'parking' => 'Parking',
            'lavage' => 'Lavage',
            'transport' => 'Transport',
            'formation_chauffeur' => 'Formation Chauffeur',
            'autre' => 'Autres',
        ];
        
        return $labels[$category] ?? ucfirst(str_replace('_', ' ', $category));
    }

    private function applyPeriodFilter($query, string $period, int $year): void
    {
        switch ($period) {
            case 'month':
                $query->whereMonth('expense_date', now()->month)
                      ->whereYear('expense_date', $year);
                break;
            case 'quarter':
                $quarter = ceil(now()->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                $query->whereBetween('expense_date', [
                    Carbon::create($year, $startMonth, 1),
                    Carbon::create($year, $endMonth)->endOfMonth()
                ]);
                break;
            case 'year':
                $query->whereYear('expense_date', $year);
                break;
        }
    }

    private function projectBudgetUsage(ExpenseGroup $group): array
    {
        // Projection simple basée sur le taux d'utilisation actuel
        $daysInPeriod = 365; // Pour l'année
        $daysPassed = now()->dayOfYear;
        $projectedUsage = ($group->budget_used / $daysPassed) * $daysInPeriod;
        
        return [
            'projected_total' => $projectedUsage,
            'projected_percentage' => $group->budget_allocated > 0 ? 
                ($projectedUsage / $group->budget_allocated) * 100 : 0,
            'will_exceed' => $projectedUsage > $group->budget_allocated,
        ];
    }

    // ... Autres méthodes helper privées selon les besoins ...
}
