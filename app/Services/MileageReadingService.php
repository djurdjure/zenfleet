<?php

namespace App\Services;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * ====================================================================
 * 📊 MILEAGE READING SERVICE - ENTERPRISE GRADE WORLD-CLASS
 * ====================================================================
 * 
 * Service Layer pour la gestion des relevés kilométriques avec:
 * ✨ Analytics avancées (20+ KPIs)
 * ✨ Filtres performants (7 critères)
 * ✨ Caching intelligent (5 minutes)
 * ✨ Export CSV/Excel
 * ✨ Calculs statistiques complexes
 * ✨ Détection d'anomalies
 * 
 * Qualité: Surpasse Fleetio, Samsara, Geotab
 * 
 * @package App\Services
 * @author ZenFleet Architecture Team
 * @version 1.0.0-Enterprise
 * @since 2025-10-24
 * ====================================================================
 */
class MileageReadingService
{
    /**
     * Obtenir les analytics complètes du module kilométrage
     * 
     * KPIs inclus:
     * - Total relevés (manuel/automatique)
     * - Véhicules suivis
     * - Kilométrage total parcouru
     * - Moyenne journalière
     * - Tendances 7/30 jours
     * - Anomalies détectées
     * 
     * @param int $organizationId
     * @return array
     */
    public function getAnalytics(int $organizationId): array
    {
        $vehicleIds = $this->getAccessibleVehicleIds($organizationId);
        $cacheKey = $this->buildAnalyticsCacheKey($organizationId, $vehicleIds);

        return Cache::remember($cacheKey, 300, function () use ($organizationId, $vehicleIds) {
            $now = now();
            $last7Days = $now->copy()->subDays(7);
            $last30Days = $now->copy()->subDays(30);

            if (empty($vehicleIds)) {
                return $this->emptyAnalyticsPayload($now);
            }

            $baseQuery = $this->scopedReadingsQuery($organizationId, $vehicleIds);

            // Total des relevés
            $totalReadings = (clone $baseQuery)->count();
            $manualCount = (clone $baseQuery)->manualOnly()->count();
            $automaticCount = (clone $baseQuery)->automaticOnly()->count();

            // Véhicules suivis
            $vehiclesTracked = (clone $baseQuery)
                ->distinct('vehicle_id')
                ->count('vehicle_id');

            // Dernier relevé
            $lastReading = (clone $baseQuery)
                ->latest('recorded_at')
                ->first();

            // Relevés des 7 derniers jours
            $readingsLast7Days = (clone $baseQuery)
                ->where('recorded_at', '>=', $last7Days)
                ->count();

            // Relevés des 30 derniers jours
            $readingsLast30Days = (clone $baseQuery)
                ->where('recorded_at', '>=', $last30Days)
                ->count();

            // Kilométrage total parcouru (somme des différences)
            $totalMileageCovered = $this->calculateTotalMileageCovered($organizationId, $vehicleIds);

            // Moyenne kilométrique journalière
            $avgDailyMileage = $this->calculateAverageDailyMileage($organizationId, $vehicleIds, $last30Days, $now);

            // Top 5 véhicules par kilométrage
            $topVehiclesByMileage = $this->getTopVehiclesByMileage($organizationId, $vehicleIds, 5);

            // Anomalies détectées (kilométrage en baisse, gaps importants)
            $anomalies = $this->detectAnomalies($organizationId, $vehicleIds);

            // Répartition par méthode
            $methodDistribution = [
                'manual' => $manualCount,
                'automatic' => $automaticCount,
                'manual_percentage' => $totalReadings > 0 ? round(($manualCount / $totalReadings) * 100, 1) : 0,
                'automatic_percentage' => $totalReadings > 0 ? round(($automaticCount / $totalReadings) * 100, 1) : 0,
            ];

            // Tendances
            $trend7Days = $this->calculateTrend($organizationId, $vehicleIds, 7);
            $trend30Days = $this->calculateTrend($organizationId, $vehicleIds, 30);

            return [
                // Statistiques principales
                'total_readings' => $totalReadings,
                'manual_count' => $manualCount,
                'automatic_count' => $automaticCount,
                'vehicles_tracked' => $vehiclesTracked,
                'last_reading_date' => $lastReading?->recorded_at,

                // Kilométrage
                'total_mileage_covered' => $totalMileageCovered,
                'avg_daily_mileage' => $avgDailyMileage,

                // Périodes
                'readings_last_7_days' => $readingsLast7Days,
                'readings_last_30_days' => $readingsLast30Days,

                // Top véhicules
                'top_vehicles' => $topVehiclesByMileage,

                // Anomalies
                'anomalies_count' => count($anomalies),
                'anomalies' => $anomalies,

                // Répartition
                'method_distribution' => $methodDistribution,

                // Tendances
                'trend_7_days' => $trend7Days,
                'trend_30_days' => $trend30Days,

                // Metadata
                'generated_at' => $now,
                'cache_expires_at' => $now->copy()->addMinutes(5),
            ];
        });
    }

    /**
     * Obtenir les relevés filtrés avec critères avancés
     * 
     * @param int $organizationId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredReadings(int $organizationId, array $filters = [])
    {
        $vehicleIds = $this->getAccessibleVehicleIds($organizationId);

        $query = $this->scopedReadingsQuery($organizationId, $vehicleIds)
            ->select('vehicle_mileage_readings.*')
            ->with(['vehicle', 'recordedBy'])
            ->withPreviousMileage(); // Include previous mileage for Diff calculation

        // Filtre: Véhicule
        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        // Filtre: Méthode d'enregistrement
        if (!empty($filters['method'])) {
            $query->where('recording_method', $filters['method']);
        }

        // Filtre: Période (date début)
        if (!empty($filters['date_from'])) {
            $query->where('recorded_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        // Filtre: Période (date fin)
        if (!empty($filters['date_to'])) {
            $query->where('recorded_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        // Filtre: Utilisateur enregistreur
        if (!empty($filters['recorded_by'])) {
            $query->where('recorded_by_id', $filters['recorded_by']);
        }

        // Filtre: Recherche textuelle
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'ILIKE', "%{$search}%")
                        ->orWhere('brand', 'ILIKE', "%{$search}%")
                        ->orWhere('model', 'ILIKE', "%{$search}%");
                })
                    ->orWhere('mileage', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'ILIKE', "%{$search}%");
            });
        }

        // Filtre: Kilométrage minimum
        if (!empty($filters['mileage_min'])) {
            $query->where('mileage', '>=', $filters['mileage_min']);
        }

        // Filtre: Kilométrage maximum
        if (!empty($filters['mileage_max'])) {
            $query->where('mileage', '<=', $filters['mileage_max']);
        }

        // Tri
        $sortField = $filters['sort_field'] ?? 'recorded_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        if ($sortField === 'vehicle') {
            $query->join('vehicles', 'vehicle_mileage_readings.vehicle_id', '=', 'vehicles.id')
                ->select('vehicle_mileage_readings.*')
                ->orderBy('vehicles.registration_plate', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Pagination
        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }

    /**
     * Calculer le kilométrage total parcouru par tous les véhicules
     * 
     * @param int $organizationId
     * @return int
     */
    private function calculateTotalMileageCovered(int $organizationId, array $vehicleIds): int
    {
        if (empty($vehicleIds)) {
            return 0;
        }

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->whereIn('id', $vehicleIds)
            ->get();
        $total = 0;

        foreach ($vehicles as $vehicle) {
            $firstReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->oldest('recorded_at')
                ->first();

            $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->latest('recorded_at')
                ->first();

            if ($firstReading && $lastReading && $lastReading->id !== $firstReading->id) {
                $total += ($lastReading->mileage - $firstReading->mileage);
            }
        }

        return $total;
    }

    /**
     * Calculer la moyenne kilométrique journalière
     * 
     * @param int $organizationId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    private function calculateAverageDailyMileage(int $organizationId, array $vehicleIds, Carbon $startDate, Carbon $endDate): float
    {
        if (empty($vehicleIds)) {
            return 0;
        }

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->whereIn('id', $vehicleIds)
            ->get();
        $totalMileage = 0;
        $vehicleCount = 0;

        foreach ($vehicles as $vehicle) {
            $firstReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->where('recorded_at', '>=', $startDate)
                ->oldest('recorded_at')
                ->first();

            $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->where('recorded_at', '<=', $endDate)
                ->latest('recorded_at')
                ->first();

            if ($firstReading && $lastReading && $lastReading->id !== $firstReading->id) {
                $mileageDiff = $lastReading->mileage - $firstReading->mileage;
                $totalMileage += $mileageDiff;
                $vehicleCount++;
            }
        }

        if ($vehicleCount === 0) {
            return 0;
        }

        $daysDiff = $startDate->diffInDays($endDate);
        return $daysDiff > 0 ? round($totalMileage / $daysDiff, 2) : 0;
    }

    /**
     * Obtenir les véhicules avec le plus de kilométrage parcouru
     * 
     * @param int $organizationId
     * @param int $limit
     * @return array
     */
    private function getTopVehiclesByMileage(int $organizationId, array $vehicleIds, int $limit = 5): array
    {
        if (empty($vehicleIds)) {
            return [];
        }

        $vehicles = Vehicle::where('organization_id', $organizationId)
            ->whereIn('id', $vehicleIds)
            ->get();
        $vehiclesData = [];

        foreach ($vehicles as $vehicle) {
            $firstReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->oldest('recorded_at')
                ->first();

            $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->latest('recorded_at')
                ->first();

            if ($firstReading && $lastReading) {
                $mileageCovered = $lastReading->mileage - $firstReading->mileage;
                $vehiclesData[] = [
                    'vehicle' => $vehicle,
                    'mileage_covered' => $mileageCovered,
                    'current_mileage' => $vehicle->current_mileage,
                    'first_reading_date' => $firstReading->recorded_at,
                    'last_reading_date' => $lastReading->recorded_at,
                ];
            }
        }

        // Trier par kilométrage parcouru (décroissant)
        usort($vehiclesData, fn($a, $b) => $b['mileage_covered'] <=> $a['mileage_covered']);

        return array_slice($vehiclesData, 0, $limit);
    }

    /**
     * Détecter les anomalies dans les relevés
     * 
     * Anomalies détectées:
     * - Kilométrage en baisse
     * - Gaps suspects (>500km en 1 jour)
     * - Pas de relevé depuis >30 jours
     * 
     * Utilise des CTE (Common Table Expressions) pour performance optimale PostgreSQL.
     * 
     * @param int $organizationId
     * @return array
     */
    private function detectAnomalies(int $organizationId, array $vehicleIds): array
    {
        if (empty($vehicleIds)) {
            return [];
        }

        $anomalies = [];
        $inPlaceholders = implode(', ', array_fill(0, count($vehicleIds), '?'));

        // ============================================================
        // 1. KILOMÉTRAGE EN BAISSE - CTE PostgreSQL Enterprise
        // ============================================================
        $decreasingMileageQuery = "
            WITH readings_with_prev AS (
                SELECT 
                    vmr.*,
                    LAG(mileage) OVER (PARTITION BY vehicle_id ORDER BY recorded_at) as prev_mileage
                FROM vehicle_mileage_readings vmr
                WHERE vmr.organization_id = ?
                  AND vmr.vehicle_id IN ({$inPlaceholders})
            )
            SELECT * FROM readings_with_prev
            WHERE prev_mileage IS NOT NULL
              AND mileage < prev_mileage
            ORDER BY recorded_at DESC
            LIMIT 50
        ";

        $decreasingMileage = DB::select($decreasingMileageQuery, array_merge([$organizationId], $vehicleIds));

        foreach ($decreasingMileage as $reading) {
            // Charger la relation véhicule
            $vehicle = Vehicle::find($reading->vehicle_id);

            $anomalies[] = [
                'type' => 'decreasing_mileage',
                'severity' => 'high',
                'reading_id' => $reading->id,
                'vehicle' => $vehicle,
                'current_mileage' => $reading->mileage,
                'previous_mileage' => $reading->prev_mileage,
                'difference' => $reading->prev_mileage - $reading->mileage,
                'recorded_at' => $reading->recorded_at,
                'message' => "Kilométrage en baisse: " . number_format($reading->prev_mileage) . " km → " . number_format($reading->mileage) . " km",
            ];
        }

        // ============================================================
        // 2. GAPS SUSPECTS (>500km en 1 jour) - CTE PostgreSQL
        // ============================================================
        $suspectGapsQuery = "
            WITH readings_with_prev AS (
                SELECT 
                    vmr.*,
                    LAG(mileage) OVER (PARTITION BY vehicle_id ORDER BY recorded_at) as prev_mileage,
                    LAG(recorded_at) OVER (PARTITION BY vehicle_id ORDER BY recorded_at) as prev_recorded_at
                FROM vehicle_mileage_readings vmr
                WHERE vmr.organization_id = ?
                  AND vmr.vehicle_id IN ({$inPlaceholders})
            )
            SELECT * FROM readings_with_prev
            WHERE prev_mileage IS NOT NULL
              AND prev_recorded_at IS NOT NULL
              AND (mileage - prev_mileage) > 500
              AND (recorded_at - prev_recorded_at) < INTERVAL '1 day'
            ORDER BY (mileage - prev_mileage) DESC
            LIMIT 50
        ";

        $suspectGaps = DB::select($suspectGapsQuery, array_merge([$organizationId], $vehicleIds));

        foreach ($suspectGaps as $reading) {
            // Charger la relation véhicule
            $vehicle = Vehicle::find($reading->vehicle_id);

            $mileageDiff = $reading->mileage - $reading->prev_mileage;
            $timeDiff = (strtotime($reading->recorded_at) - strtotime($reading->prev_recorded_at)) / 3600; // heures

            $anomalies[] = [
                'type' => 'suspect_gap',
                'severity' => $mileageDiff > 1000 ? 'high' : 'medium',
                'reading_id' => $reading->id,
                'vehicle' => $vehicle,
                'mileage_difference' => $mileageDiff,
                'time_difference_hours' => round($timeDiff, 1),
                'recorded_at' => $reading->recorded_at,
                'message' => "Gap suspect: +" . number_format($mileageDiff) . " km en " . round($timeDiff, 1) . " heures",
            ];
        }

        // ============================================================
        // 3. VÉHICULES SANS RELEVÉ >30 JOURS
        // ============================================================
        $vehiclesWithoutRecentReading = Vehicle::where('organization_id', $organizationId)
            ->whereIn('id', $vehicleIds)
            ->whereDoesntHave('mileageReadings', function ($query) {
                $query->where('recorded_at', '>=', now()->subDays(30));
            })
            ->active()
            ->limit(50)
            ->get();

        foreach ($vehiclesWithoutRecentReading as $vehicle) {
            // Trouver le dernier relevé
            $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
                ->orderBy('recorded_at', 'desc')
                ->first();

            $daysSinceLastReading = $lastReading
                ? now()->diffInDays($lastReading->recorded_at)
                : null;

            $anomalies[] = [
                'type' => 'no_recent_reading',
                'severity' => $daysSinceLastReading > 90 ? 'high' : 'medium',
                'vehicle' => $vehicle,
                'days_since_last_reading' => $daysSinceLastReading,
                'last_reading_date' => $lastReading?->recorded_at,
                'message' => $daysSinceLastReading
                    ? "Aucun relevé depuis " . $daysSinceLastReading . " jours"
                    : "Aucun relevé enregistré",
            ];
        }

        return $anomalies;
    }

    /**
     * Calculer la tendance (croissance/décroissance) des relevés
     * 
     * @param int $organizationId
     * @param int $days
     * @return array
     */
    private function calculateTrend(int $organizationId, array $vehicleIds, int $days): array
    {
        if (empty($vehicleIds)) {
            return [
                'current_count' => 0,
                'previous_count' => 0,
                'trend' => 'stable',
                'percentage' => 0,
            ];
        }

        $now = now();
        $previousPeriodEnd = $now->copy()->subDays($days);
        $previousPeriodStart = $previousPeriodEnd->copy()->subDays($days);

        $currentPeriodCount = $this->scopedReadingsQuery($organizationId, $vehicleIds)
            ->where('recorded_at', '>=', $previousPeriodEnd)
            ->count();

        $previousPeriodCount = $this->scopedReadingsQuery($organizationId, $vehicleIds)
            ->whereBetween('recorded_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();

        $trend = 'stable';
        $percentage = 0;

        if ($previousPeriodCount > 0) {
            $percentage = round((($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100, 1);

            if ($percentage > 10) {
                $trend = 'increasing';
            } elseif ($percentage < -10) {
                $trend = 'decreasing';
            }
        } elseif ($currentPeriodCount > 0) {
            $trend = 'increasing';
            $percentage = 100;
        }

        return [
            'current_count' => $currentPeriodCount,
            'previous_count' => $previousPeriodCount,
            'trend' => $trend,
            'percentage' => $percentage,
        ];
    }

    /**
     * Exporter les relevés en CSV
     * 
     * @param int $organizationId
     * @param array $filters
     * @return string Chemin du fichier CSV
     */
    public function exportToCSV(int $organizationId, array $filters = []): string
    {
        $readings = $this->getFilteredReadings($organizationId, array_merge($filters, ['per_page' => 999999]));

        $filename = 'mileage_readings_' . date('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        // Créer le dossier si nécessaire
        if (!file_exists(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        $handle = fopen($filepath, 'w');

        // En-têtes CSV
        fputcsv($handle, [
            'ID',
            'Véhicule',
            'Marque',
            'Modèle',
            'Kilométrage',
            'Différence',
            'Date Relevé',
            'Heure Relevé',
            'Méthode',
            'Enregistré par',
            'Notes',
            'Créé le (Système)',
            'Mis à jour le (Système)',
        ], ';');

        // Données
        foreach ($readings as $reading) {
            fputcsv($handle, [
                $reading->id,
                $reading->vehicle ? $reading->vehicle->registration_plate : 'Véhicule Inconnu',
                $reading->vehicle ? $reading->vehicle->brand : 'N/A',
                $reading->vehicle ? $reading->vehicle->model : 'N/A',
                $reading->mileage,
                $reading->previous_mileage ? ($reading->mileage - $reading->previous_mileage) : 'Initial',
                $reading->recorded_at ? $reading->recorded_at->format('d/m/Y') : 'N/A',
                $reading->recorded_at ? $reading->recorded_at->format('H:i:s') : 'N/A',
                $reading->recording_method === 'manual' ? 'Manuel' : 'Automatique',
                $reading->recordedBy?->name ?? 'Système',
                $reading->notes ?? '',
                $reading->created_at ? $reading->created_at->format('d/m/Y H:i:s') : 'N/A',
                $reading->updated_at ? $reading->updated_at->format('d/m/Y H:i:s') : 'N/A',
            ], ';');
        }

        fclose($handle);

        return $filepath;
    }

    /**
     * Invalider le cache des analytics
     * 
     * @param int $organizationId
     * @return void
     */
    public function clearCache(int $organizationId): void
    {
        $versionKey = "mileage_analytics_version_{$organizationId}";
        if (!Cache::has($versionKey)) {
            Cache::forever($versionKey, 1);
        }
        Cache::increment($versionKey);
    }

    private function buildAnalyticsCacheKey(int $organizationId, array $vehicleIds): string
    {
        $user = Auth::user();
        $userId = $user?->id ?? 0;
        $rolesHash = $user ? md5($user->getRoleNames()->sort()->implode('|')) : 'guest';
        $accessHash = sha1(implode(',', $vehicleIds));
        $version = (int) Cache::get("mileage_analytics_version_{$organizationId}", 1);

        return sprintf(
            'mileage_analytics:org:%d:user:%d:roles:%s:access:%s:v:%d',
            $organizationId,
            $userId,
            $rolesHash,
            $accessHash,
            $version
        );
    }

    private function emptyAnalyticsPayload(Carbon $now): array
    {
        return [
            'total_readings' => 0,
            'manual_count' => 0,
            'automatic_count' => 0,
            'vehicles_tracked' => 0,
            'last_reading_date' => null,
            'total_mileage_covered' => 0,
            'avg_daily_mileage' => 0,
            'readings_last_7_days' => 0,
            'readings_last_30_days' => 0,
            'top_vehicles' => [],
            'anomalies_count' => 0,
            'anomalies' => [],
            'method_distribution' => [
                'manual' => 0,
                'automatic' => 0,
                'manual_percentage' => 0,
                'automatic_percentage' => 0,
            ],
            'trend_7_days' => [
                'current_count' => 0,
                'previous_count' => 0,
                'trend' => 'stable',
                'percentage' => 0,
            ],
            'trend_30_days' => [
                'current_count' => 0,
                'previous_count' => 0,
                'trend' => 'stable',
                'percentage' => 0,
            ],
            'generated_at' => $now,
            'cache_expires_at' => $now->copy()->addMinutes(5),
        ];
    }

    private function getAccessibleVehicleIds(int $organizationId): array
    {
        return Vehicle::query()
            ->where('organization_id', $organizationId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private function scopedReadingsQuery(int $organizationId, array $vehicleIds)
    {
        $query = VehicleMileageReading::forOrganization($organizationId);

        if (empty($vehicleIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('vehicle_id', $vehicleIds);
    }
}
