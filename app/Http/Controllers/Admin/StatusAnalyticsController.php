<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\StatusHistory;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Support\Analytics\ChartPayloadFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * 📊 STATUS ANALYTICS CONTROLLER - Enterprise Dashboard
 *
 * Contrôleur pour le tableau de bord analytics des transitions de statuts.
 * Fournit des métriques, graphiques et insights business.
 *
 * Features:
 * - Métriques KPI (temps moyen par statut, taux transitions)
 * - Graphiques ApexCharts (timeline, distribution, trends)
 * - Filtres avancés (date range, véhicule, type, organisation)
 * - Export CSV/PDF
 * - Comparaisons périodes (YoY, MoM)
 *
 * @version 1.0-Enterprise
 */
class StatusAnalyticsController extends Controller
{
    /**
     * Affiche le dashboard principal
     */
    public function index(Request $request)
    {
        // Récupérer les filtres
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());
        $vehicleId = $request->input('vehicle_id');
        $entityType = $request->input('entity_type', 'vehicle'); // vehicle ou driver

        // Convertir en Carbon si nécessaire
        $startDate = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $endDate = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        // Calculer les métriques KPI
        $metrics = $this->calculateMetrics($startDate, $endDate, $entityType);

        // Récupérer les données pour les graphiques
        $transitionStats = $this->getTransitionStats($startDate, $endDate, $entityType);
        $dailyChanges = $this->getDailyChanges($startDate, $endDate, $entityType);
        $statusDistribution = $this->getCurrentStatusDistribution($entityType);
        $topVehiclesChanges = $this->getTopVehiclesWithMostChanges($startDate, $endDate, 10);
        $chartPayloads = $this->buildChartPayloads($dailyChanges, $statusDistribution, $entityType, $startDate, $endDate);

        // Historique récent
        $recentChanges = $this->getRecentChanges($entityType, 20);

        return view('admin.analytics.status-dashboard', compact(
            'metrics',
            'transitionStats',
            'dailyChanges',
            'statusDistribution',
            'topVehiclesChanges',
            'chartPayloads',
            'recentChanges',
            'startDate',
            'endDate',
            'entityType'
        ));
    }

    /**
     * Calcule les métriques KPI
     */
    protected function calculateMetrics(Carbon $startDate, Carbon $endDate, string $entityType): array
    {
        $entityClass = $entityType === 'vehicle' ? Vehicle::class : Driver::class;

        // Total changements période
        $totalChanges = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->count();

        // Changements manuels vs automatiques
        $manualChanges = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->where('change_type', 'manual')
            ->count();

        $automaticChanges = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->where('change_type', 'automatic')
            ->count();

        // Entités uniques avec changements
        $uniqueEntities = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->distinct('statusable_id')
            ->count('statusable_id');

        // Moyenne changements par entité
        $avgChangesPerEntity = $uniqueEntities > 0 ? round($totalChanges / $uniqueEntities, 2) : 0;

        // Temps moyen dans statut "en_panne" ou "en_maintenance" (véhicules)
        $avgDowntime = null;
        if ($entityType === 'vehicle') {
            $avgDowntime = $this->calculateAverageDowntime($startDate, $endDate);
        }

        // Comparaison période précédente (croissance)
        $previousPeriod = $this->calculatePreviousPeriodGrowth($startDate, $endDate, $entityClass);

        return [
            'total_changes' => $totalChanges,
            'manual_changes' => $manualChanges,
            'automatic_changes' => $automaticChanges,
            'unique_entities' => $uniqueEntities,
            'avg_changes_per_entity' => $avgChangesPerEntity,
            'avg_downtime_hours' => $avgDowntime,
            'growth_percentage' => $previousPeriod['growth'],
            'period_days' => $startDate->diffInDays($endDate),
        ];
    }

    /**
     * Récupère les statistiques de transitions
     */
    protected function getTransitionStats(Carbon $startDate, Carbon $endDate, string $entityType): array
    {
        $entityClass = $entityType === 'vehicle' ? Vehicle::class : Driver::class;

        $stats = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->whereNotNull('from_status')
            ->select('from_status', 'to_status', DB::raw('count(*) as count'))
            ->groupBy('from_status', 'to_status')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return $stats->map(function ($item) {
            return [
                'from' => ucfirst(str_replace('_', ' ', $item->from_status)),
                'to' => ucfirst(str_replace('_', ' ', $item->to_status)),
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Récupère les changements quotidiens
     */
    protected function getDailyChanges(Carbon $startDate, Carbon $endDate, string $entityType): array
    {
        $entityClass = $entityType === 'vehicle' ? Vehicle::class : Driver::class;

        $dailyData = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(changed_at) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $dailyData->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('Y-m-d'),
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Distribution actuelle des statuts
     */
    protected function getCurrentStatusDistribution(string $entityType): array
    {
        if ($entityType === 'vehicle') {
            $distribution = Vehicle::with('vehicleStatus')
                ->select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get();

            return $distribution->map(function ($item) {
                return [
                    'status' => $item->vehicleStatus->name ?? 'Inconnu',
                    'count' => $item->count,
                ];
            })->toArray();
        } else {
            $distribution = Driver::with('driverStatus')
                ->select('status_id', DB::raw('count(*) as count'))
                ->groupBy('status_id')
                ->get();

            return $distribution->map(function ($item) {
                return [
                    'status' => $item->driverStatus->name ?? 'Inconnu',
                    'count' => $item->count,
                ];
            })->toArray();
        }
    }

    /**
     * Top véhicules avec le plus de changements
     */
    protected function getTopVehiclesWithMostChanges(Carbon $startDate, Carbon $endDate, int $limit = 10): array
    {
        $topVehicles = StatusHistory::where('statusable_type', Vehicle::class)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->select('statusable_id', DB::raw('count(*) as changes_count'))
            ->groupBy('statusable_id')
            ->orderByDesc('changes_count')
            ->limit($limit)
            ->get();

        return $topVehicles->map(function ($item) {
            $vehicle = Vehicle::find($item->statusable_id);
            return [
                'vehicle_id' => $item->statusable_id,
                'vehicle_name' => $vehicle ? ($vehicle->vehicle_name ?? $vehicle->registration_plate) : 'Inconnu',
                'changes_count' => $item->changes_count,
            ];
        })->toArray();
    }

    /**
     * Récupère les changements récents
     */
    protected function getRecentChanges(string $entityType, int $limit = 20): array
    {
        $entityClass = $entityType === 'vehicle' ? Vehicle::class : Driver::class;

        $changes = StatusHistory::where('statusable_type', $entityClass)
            ->with(['statusable', 'changedBy'])
            ->orderByDesc('changed_at')
            ->limit($limit)
            ->get();

        return $changes->map(function ($change) use ($entityType) {
            $entity = $change->statusable;
            $entityName = $entityType === 'vehicle'
                ? ($entity->vehicle_name ?? $entity->registration_plate ?? 'Inconnu')
                : ($entity->full_name ?? 'Inconnu');

            return [
                'id' => $change->id,
                'entity_name' => $entityName,
                'from_status' => $change->from_status ? ucfirst(str_replace('_', ' ', $change->from_status)) : 'Initial',
                'to_status' => ucfirst(str_replace('_', ' ', $change->to_status)),
                'reason' => $change->reason,
                'changed_by' => $change->changedBy->name ?? 'Système',
                'changed_at' => $change->changed_at->format('d/m/Y H:i'),
                'change_type' => $change->change_type,
            ];
        })->toArray();
    }

    /**
     * Calcule le temps moyen d'immobilisation (downtime)
     */
    protected function calculateAverageDowntime(Carbon $startDate, Carbon $endDate): ?float
    {
        // Récupérer les transitions vers statuts non-opérationnels
        $downtimeTransitions = StatusHistory::where('statusable_type', Vehicle::class)
            ->whereIn('to_status', ['en_panne', 'en_maintenance'])
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->get();

        if ($downtimeTransitions->isEmpty()) {
            return null;
        }

        $totalDowntimeSeconds = 0;
        $count = 0;

        foreach ($downtimeTransitions as $transition) {
            // Trouver la prochaine transition vers statut opérationnel
            $nextTransition = StatusHistory::where('statusable_type', Vehicle::class)
                ->where('statusable_id', $transition->statusable_id)
                ->where('changed_at', '>', $transition->changed_at)
                ->whereNotIn('to_status', ['en_panne', 'en_maintenance', 'reforme'])
                ->orderBy('changed_at')
                ->first();

            if ($nextTransition) {
                $downtime = $transition->changed_at->diffInSeconds($nextTransition->changed_at);
                $totalDowntimeSeconds += $downtime;
                $count++;
            }
        }

        if ($count === 0) {
            return null;
        }

        // Retourner en heures
        return round($totalDowntimeSeconds / 3600 / $count, 2);
    }

    /**
     * Calcule la croissance par rapport à la période précédente
     */
    protected function calculatePreviousPeriodGrowth(Carbon $startDate, Carbon $endDate, string $entityClass): array
    {
        $currentPeriodDays = $startDate->diffInDays($endDate);

        $previousStartDate = $startDate->copy()->subDays($currentPeriodDays);
        $previousEndDate = $startDate->copy();

        $currentCount = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$startDate, $endDate])
            ->count();

        $previousCount = StatusHistory::where('statusable_type', $entityClass)
            ->whereBetween('changed_at', [$previousStartDate, $previousEndDate])
            ->count();

        $growth = $previousCount > 0
            ? round((($currentCount - $previousCount) / $previousCount) * 100, 1)
            : 0;

        return [
            'current_count' => $currentCount,
            'previous_count' => $previousCount,
            'growth' => $growth,
        ];
    }

    /**
     * API JSON pour alimenter le chart quotidien en mode AJAX.
     */
    public function getDailyStatsApi(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => ['nullable', 'in:vehicle,driver'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $entityType = $validated['entity_type'] ?? 'vehicle';
        $startDate = isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : now()->subDays(30);
        $endDate = isset($validated['end_date']) ? Carbon::parse($validated['end_date']) : now();

        $organizationId = auth()->user()->organization_id ?? null;
        $cacheKey = sprintf(
            'status_analytics_daily:%s:%s:%s:%s',
            $organizationId ?? 'global',
            $entityType,
            $startDate->format('Ymd'),
            $endDate->format('Ymd')
        );

        $payload = Cache::remember($cacheKey, config('analytics.cache.ttl.realtime', 300), function () use ($entityType, $startDate, $endDate, $organizationId) {
            $dailyChanges = $this->getDailyChanges($startDate, $endDate, $entityType);

            return ChartPayloadFactory::make(
                chartId: 'status-daily-changes-api',
                type: 'area',
                labels: collect($dailyChanges)->pluck('date')->values()->all(),
                series: [[
                    'name' => 'Changements',
                    'data' => collect($dailyChanges)->pluck('count')->values()->all(),
                ]],
                options: [
                    'stroke' => ['curve' => 'smooth', 'width' => 2],
                    'fill' => [
                        'type' => 'gradient',
                        'gradient' => [
                            'shadeIntensity' => 1,
                            'opacityFrom' => 0.7,
                            'opacityTo' => 0.3,
                        ],
                    ],
                    'legend' => ['show' => false],
                ],
                meta: [
                    'source' => 'status.analytics.api.daily',
                    'tenant_id' => $organizationId,
                    'period' => $startDate->format('Y-m-d') . '|' . $endDate->format('Y-m-d'),
                    'filters' => ['entity_type' => $entityType],
                    'timezone' => $this->resolveTimezone(),
                    'currency' => $this->resolveCurrency($organizationId),
                ],
                height: 300,
                ariaLabel: 'Evolution quotidienne des changements de statuts'
            );
        });

        return response()->json([
            'success' => true,
            'payload' => $payload,
        ]);
    }

    /**
     * Build normalized payload contract for dashboard charts.
     */
    protected function buildChartPayloads(array $dailyChanges, array $statusDistribution, string $entityType, Carbon $startDate, Carbon $endDate): array
    {
        $organizationId = auth()->user()->organization_id ?? null;
        $period = $startDate->format('Y-m-d') . '|' . $endDate->format('Y-m-d');
        $meta = [
            'source' => 'status.analytics.dashboard',
            'tenant_id' => $organizationId,
            'period' => $period,
            'filters' => ['entity_type' => $entityType],
            'timezone' => $this->resolveTimezone(),
            'currency' => $this->resolveCurrency($organizationId),
        ];

        return [
            'daily_changes' => ChartPayloadFactory::make(
                chartId: 'status-daily-changes',
                type: 'area',
                labels: collect($dailyChanges)->pluck('date')->values()->all(),
                series: [[
                    'name' => 'Changements',
                    'data' => collect($dailyChanges)->pluck('count')->values()->all(),
                ]],
                options: [
                    'stroke' => ['curve' => 'smooth', 'width' => 2],
                    'fill' => [
                        'type' => 'gradient',
                        'gradient' => [
                            'shadeIntensity' => 1,
                            'opacityFrom' => 0.7,
                            'opacityTo' => 0.3,
                        ],
                    ],
                    'xaxis' => ['labels' => ['rotate' => -45]],
                    'colors' => ['#3b82f6'],
                    'legend' => ['show' => false],
                ],
                meta: $meta,
                height: 300,
                ariaLabel: 'Evolution quotidienne des changements de statuts'
            ),
            'status_distribution' => ChartPayloadFactory::make(
                chartId: 'status-distribution',
                type: 'donut',
                labels: collect($statusDistribution)->pluck('status')->values()->all(),
                series: collect($statusDistribution)->pluck('count')->values()->all(),
                options: [
                    'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'],
                    'legend' => ['position' => 'bottom'],
                ],
                meta: $meta,
                height: 300,
                ariaLabel: 'Distribution actuelle des statuts'
            ),
        ];
    }

    protected function resolveTimezone(): string
    {
        return auth()->user()->timezone
            ?? config('app.timezone');
    }

    protected function resolveCurrency(?int $organizationId): string
    {
        if (!$organizationId) {
            return config('algeria.currency.code', 'DZD');
        }

        $organization = Organization::query()->find($organizationId);

        return $organization?->currency
            ?? config('algeria.currency.code', 'DZD');
    }
}
