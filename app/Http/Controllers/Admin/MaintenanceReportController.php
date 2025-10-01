<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use App\Models\MaintenanceType;
use App\Models\MaintenanceProvider;
use App\Models\Vehicle;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceReportExport;

/**
 * Contrôleur des rapports et analytiques du module Maintenance Enterprise-Grade
 * Génération de rapports avancés, KPIs et analytiques
 */
class MaintenanceReportController extends Controller
{
    /**
     * Index des rapports disponibles
     */
    public function index(): View
    {
        $organizationId = auth()->user()->organization_id;

        // Métriques générales pour la vue d'ensemble
        $overviewMetrics = [
            'total_operations' => MaintenanceOperation::where('organization_id', $organizationId)->count(),
            'operations_this_month' => MaintenanceOperation::where('organization_id', $organizationId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->count(),
            'total_cost_ytd' => MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereYear('completed_date', Carbon::now()->year)
                ->sum('total_cost') ?? 0,
            'avg_operation_duration' => MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->avg('duration_minutes') ?? 0,
        ];

        // Rapports disponibles
        $availableReports = [
            [
                'name' => 'Rapport de Performance',
                'description' => 'Analyse détaillée des performances de maintenance',
                'route' => 'maintenance.reports.performance',
                'icon' => 'chart-line',
                'color' => 'blue'
            ],
            [
                'name' => 'Analyse des Coûts',
                'description' => 'Évolution et répartition des coûts de maintenance',
                'route' => 'maintenance.reports.costs',
                'icon' => 'currency-dollar',
                'color' => 'green'
            ],
            [
                'name' => 'KPIs Maintenance',
                'description' => 'Indicateurs clés de performance',
                'route' => 'maintenance.reports.kpis',
                'icon' => 'chart-bar',
                'color' => 'purple'
            ],
            [
                'name' => 'Rapport de Conformité',
                'description' => 'Suivi de la conformité réglementaire',
                'route' => 'maintenance.reports.compliance',
                'icon' => 'shield-check',
                'color' => 'indigo'
            ],
            [
                'name' => 'Analyse des Fournisseurs',
                'description' => 'Performance et évaluation des fournisseurs',
                'route' => 'maintenance.reports.providers-analysis',
                'icon' => 'building-office',
                'color' => 'orange'
            ],
            [
                'name' => 'Rapport Personnalisé',
                'description' => 'Créer un rapport sur mesure',
                'route' => 'maintenance.reports.custom',
                'icon' => 'cog',
                'color' => 'gray'
            ]
        ];

        return view('admin.maintenance.reports.index', compact('overviewMetrics', 'availableReports'));
    }

    /**
     * Rapport de performance détaillé
     */
    public function performance(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;
        $period = $request->get('period', '12'); // Mois par défaut

        // Données de performance par mois
        $performanceData = $this->getPerformanceData($organizationId, $period);

        // Efficacité par type de maintenance
        $efficiencyByType = $this->getEfficiencyByType($organizationId, $period);

        // Temps de résolution moyen
        $resolutionTimes = $this->getResolutionTimes($organizationId, $period);

        // Taux de maintenance préventive vs corrective
        $preventiveRatio = $this->getPreventiveRatio($organizationId, $period);

        return view('admin.maintenance.reports.performance', compact(
            'performanceData',
            'efficiencyByType',
            'resolutionTimes',
            'preventiveRatio',
            'period'
        ));
    }

    /**
     * Analyse détaillée des coûts
     */
    public function costs(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;
        $period = $request->get('period', '12');

        // Évolution des coûts
        $costEvolution = $this->getCostEvolution($organizationId, $period);

        // Répartition des coûts par catégorie
        $costByCategory = $this->getCostByCategory($organizationId, $period);

        // Coût par véhicule
        $costPerVehicle = $this->getCostPerVehicle($organizationId, $period);

        // Prévisions de coûts
        $costForecasting = $this->getCostForecasting($organizationId);

        return view('admin.maintenance.reports.costs', compact(
            'costEvolution',
            'costByCategory',
            'costPerVehicle',
            'costForecasting',
            'period'
        ));
    }

    /**
     * KPIs de maintenance
     */
    public function kpis(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;
        $period = $request->get('period', '6');

        // KPIs principaux
        $kpis = [
            'mtbf' => $this->calculateMTBF($organizationId, $period), // Mean Time Between Failures
            'mttr' => $this->calculateMTTR($organizationId, $period), // Mean Time To Repair
            'availability' => $this->calculateAvailability($organizationId, $period),
            'compliance_rate' => $this->calculateComplianceRate($organizationId, $period),
            'cost_per_km' => $this->calculateCostPerKm($organizationId, $period),
            'preventive_ratio' => $this->calculatePreventiveRatio($organizationId, $period)
        ];

        // Tendances des KPIs
        $kpiTrends = $this->getKpiTrends($organizationId, $period);

        // Benchmarking (comparaison avec moyennes sectorielles)
        $benchmarks = $this->getBenchmarkData();

        return view('admin.maintenance.reports.kpis', compact('kpis', 'kpiTrends', 'benchmarks', 'period'));
    }

    /**
     * Rapport de conformité réglementaire
     */
    public function compliance(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;

        // Contrôles techniques
        $technicalInspections = $this->getTechnicalInspectionStatus($organizationId);

        // Maintenance obligatoire
        $mandatoryMaintenance = $this->getMandatoryMaintenanceStatus($organizationId);

        // Alertes de conformité
        $complianceAlerts = $this->getComplianceAlerts($organizationId);

        // Score de conformité global
        $complianceScore = $this->calculateComplianceScore($organizationId);

        return view('admin.maintenance.reports.compliance', compact(
            'technicalInspections',
            'mandatoryMaintenance',
            'complianceAlerts',
            'complianceScore'
        ));
    }

    /**
     * Analyse des fournisseurs
     */
    public function providersAnalysis(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;
        $period = $request->get('period', '12');

        // Performance des fournisseurs
        $providerPerformance = $this->getProviderPerformance($organizationId, $period);

        // Analyse des coûts par fournisseur
        $providerCosts = $this->getProviderCosts($organizationId, $period);

        // Délais de livraison/intervention
        $providerDeliveryTimes = $this->getProviderDeliveryTimes($organizationId, $period);

        // Recommandations
        $providerRecommendations = $this->getProviderRecommendations($organizationId);

        return view('admin.maintenance.reports.providers-analysis', compact(
            'providerPerformance',
            'providerCosts',
            'providerDeliveryTimes',
            'providerRecommendations',
            'period'
        ));
    }

    /**
     * Générateur de rapport personnalisé
     */
    public function custom(): View
    {
        $organizationId = auth()->user()->organization_id;

        // Options disponibles pour les rapports personnalisés
        $availableMetrics = [
            'operations_count' => 'Nombre d\'opérations',
            'total_cost' => 'Coût total',
            'avg_duration' => 'Durée moyenne',
            'vehicle_downtime' => 'Temps d\'immobilisation',
            'provider_performance' => 'Performance fournisseurs',
            'alert_frequency' => 'Fréquence des alertes'
        ];

        $availableFilters = [
            'date_range' => 'Période',
            'vehicle_type' => 'Type de véhicule',
            'maintenance_type' => 'Type de maintenance',
            'provider' => 'Fournisseur',
            'cost_range' => 'Fourchette de coût'
        ];

        $availableCharts = [
            'line' => 'Graphique linéaire',
            'bar' => 'Graphique en barres',
            'pie' => 'Graphique circulaire',
            'area' => 'Graphique en aires'
        ];

        return view('admin.maintenance.reports.custom', compact(
            'availableMetrics',
            'availableFilters',
            'availableCharts'
        ));
    }

    /**
     * Générer un rapport personnalisé
     */
    public function generateCustom(Request $request): JsonResponse
    {
        $request->validate([
            'metrics' => 'required|array',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:date_from',
            'chart_type' => 'required|in:line,bar,pie,area',
            'filters' => 'array'
        ]);

        $organizationId = auth()->user()->organization_id;
        $data = $this->buildCustomReport($organizationId, $request->all());

        return response()->json($data);
    }

    /**
     * API : Tendance des coûts
     */
    public function apiCostsTrend(Request $request): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;
        $period = $request->get('period', 6);

        $data = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $cost = MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereYear('completed_date', $month->year)
                ->whereMonth('completed_date', $month->month)
                ->sum('total_cost') ?? 0;

            $data[] = [
                'month' => $month->format('M Y'),
                'cost' => $cost
            ];
        }

        return response()->json($data);
    }

    /**
     * API : Statut des opérations
     */
    public function apiOperationsStatus(): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $statusData = MaintenanceOperation::where('organization_id', $organizationId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return response()->json($statusData);
    }

    /**
     * API : Préventif vs Correctif
     */
    public function apiPreventiveVsCorrective(): JsonResponse
    {
        $organizationId = auth()->user()->organization_id;

        $data = MaintenanceOperation::where('maintenance_operations.organization_id', $organizationId)
            ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
            ->selectRaw('maintenance_types.category, COUNT(*) as count')
            ->groupBy('maintenance_types.category')
            ->pluck('count', 'category')
            ->toArray();

        return response()->json($data);
    }

    /**
     * Export rapport de performance
     */
    public function exportPerformance(Request $request): Response
    {
        $period = $request->get('period', '12');
        return Excel::download(new MaintenanceReportExport('performance', $period), 'rapport-performance-maintenance.xlsx');
    }

    /**
     * Export analyse des coûts
     */
    public function exportCosts(Request $request): Response
    {
        $period = $request->get('period', '12');
        return Excel::download(new MaintenanceReportExport('costs', $period), 'analyse-couts-maintenance.xlsx');
    }

    /**
     * Export KPIs
     */
    public function exportKpis(Request $request): Response
    {
        $period = $request->get('period', '6');
        return Excel::download(new MaintenanceReportExport('kpis', $period), 'kpis-maintenance.xlsx');
    }

    /**
     * Export rapport de conformité
     */
    public function exportCompliance(): Response
    {
        return Excel::download(new MaintenanceReportExport('compliance'), 'rapport-conformite-maintenance.xlsx');
    }

    /**
     * Export analyse des fournisseurs
     */
    public function exportProvidersAnalysis(Request $request): Response
    {
        $period = $request->get('period', '12');
        return Excel::download(new MaintenanceReportExport('providers', $period), 'analyse-fournisseurs-maintenance.xlsx');
    }

    // Méthodes privées pour les calculs et analyses

    private function getPerformanceData(int $organizationId, int $period): array
    {
        $data = [];
        for ($i = $period - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $operations = MaintenanceOperation::where('organization_id', $organizationId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            $data[] = [
                'month' => $month->format('M Y'),
                'total_operations' => $operations->count(),
                'completed_operations' => $operations->where('status', 'completed')->count(),
                'avg_duration' => $operations->where('status', 'completed')->avg('duration_minutes') ?? 0,
                'success_rate' => $operations->count() > 0 ?
                    ($operations->where('status', 'completed')->count() / $operations->count()) * 100 : 0
            ];
        }

        return $data;
    }

    private function getEfficiencyByType(int $organizationId, int $period): array
    {
        return MaintenanceType::where('organization_id', $organizationId)
            ->withCount(['operations as operations_count' => function ($query) use ($period) {
                $query->where('created_at', '>=', Carbon::now()->subMonths($period));
            }])
            ->withAvg(['operations as avg_duration' => function ($query) use ($period) {
                $query->where('created_at', '>=', Carbon::now()->subMonths($period))
                      ->where('status', 'completed');
            }], 'duration_minutes')
            ->get()
            ->map(function ($type) {
                return [
                    'name' => $type->name,
                    'category' => $type->category,
                    'operations_count' => $type->operations_count,
                    'avg_duration' => $type->avg_duration ?? 0,
                    'efficiency_score' => $this->calculateEfficiencyScore($type)
                ];
            })
            ->toArray();
    }

    private function calculateEfficiencyScore($type): float
    {
        // Logique de calcul du score d'efficacité basé sur durée vs estimation
        $avgDuration = $type->avg_duration ?? 0;
        $estimatedDuration = $type->estimated_duration_minutes ?? 1;

        if ($avgDuration <= $estimatedDuration) {
            return 100;
        }

        return max(0, 100 - (($avgDuration - $estimatedDuration) / $estimatedDuration * 100));
    }

    private function getResolutionTimes(int $organizationId, int $period): array
    {
        return MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths($period))
            ->selectRaw('
                AVG(duration_minutes) as avg_duration,
                MIN(duration_minutes) as min_duration,
                MAX(duration_minutes) as max_duration,
                PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY duration_minutes) as median_duration
            ')
            ->first()
            ->toArray();
    }

    private function calculateMTBF(int $organizationId, int $period): float
    {
        // Mean Time Between Failures - temps moyen entre pannes
        $correctiveOperations = MaintenanceOperation::where('maintenance_operations.organization_id', $organizationId)
            ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
            ->where('maintenance_types.category', 'corrective')
            ->where('maintenance_operations.created_at', '>=', Carbon::now()->subMonths($period))
            ->count();

        $totalVehicles = Vehicle::where('organization_id', $organizationId)->count();
        $totalDays = $period * 30;

        return $correctiveOperations > 0 ? ($totalVehicles * $totalDays) / $correctiveOperations : 0;
    }

    private function calculateMTTR(int $organizationId, int $period): float
    {
        // Mean Time To Repair - temps moyen de réparation
        return MaintenanceOperation::where('maintenance_operations.organization_id', $organizationId)
            ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
            ->where('maintenance_types.category', 'corrective')
            ->where('maintenance_operations.status', 'completed')
            ->where('maintenance_operations.created_at', '>=', Carbon::now()->subMonths($period))
            ->avg('maintenance_operations.duration_minutes') ?? 0;
    }

    private function calculateAvailability(int $organizationId, int $period): float
    {
        // Calcul du taux de disponibilité de la flotte
        $totalDowntime = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths($period))
            ->sum('duration_minutes');

        $totalVehicles = Vehicle::where('organization_id', $organizationId)->count();
        $totalMinutesInPeriod = $period * 30 * 24 * 60 * $totalVehicles;

        return $totalMinutesInPeriod > 0 ?
            (($totalMinutesInPeriod - $totalDowntime) / $totalMinutesInPeriod) * 100 : 100;
    }

    private function buildCustomReport(int $organizationId, array $params): array
    {
        // Logique de construction de rapport personnalisé
        $query = MaintenanceOperation::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$params['date_from'], $params['date_to']]);

        // Appliquer les filtres
        if (isset($params['filters']['vehicle_type'])) {
            $query->whereHas('vehicle', function ($q) use ($params) {
                $q->where('vehicle_type', $params['filters']['vehicle_type']);
            });
        }

        // Calculer les métriques demandées
        $results = [];
        foreach ($params['metrics'] as $metric) {
            $results[$metric] = $this->calculateMetric($query, $metric);
        }

        return $results;
    }

    private function calculateMetric($query, string $metric): mixed
    {
        return match($metric) {
            'operations_count' => $query->count(),
            'total_cost' => $query->sum('total_cost'),
            'avg_duration' => $query->avg('duration_minutes'),
            default => 0
        };
    }
}