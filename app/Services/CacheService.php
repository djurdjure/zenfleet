<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 heure
    private const LONG_TTL = 86400;   // 24 heures
    private const SHORT_TTL = 300;    // 5 minutes

    /**
     * Cache avec tags pour invalidation granulaire
     */
    public function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL, array $tags = []): mixed
    {
        try {
            $cache = Cache::tags($tags);
            return $cache->remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning('Cache miss due to error', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        }
    }

    /**
     * Cache pour les données d'organisation
     */
    public function rememberOrganizationData(int $organizationId, string $dataType, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        $key = "org_{$organizationId}_{$dataType}";
        $tags = ["organization_{$organizationId}", "organization_data", $dataType];
        
        return $this->remember($key, $callback, $ttl, $tags);
    }

    /**
     * Cache pour les statistiques de maintenance
     */
    public function rememberMaintenanceStats(int $organizationId, callable $callback): mixed
    {
        $key = "maintenance_stats_{$organizationId}";
        $tags = ["organization_{$organizationId}", "maintenance", "statistics"];
        
        return $this->remember($key, $callback, self::SHORT_TTL, $tags);
    }

    /**
     * Cache pour les données de véhicules
     */
    public function rememberVehicleData(int $organizationId, callable $callback): mixed
    {
        $key = "vehicles_data_{$organizationId}";
        $tags = ["organization_{$organizationId}", "vehicles"];
        
        return $this->remember($key, $callback, self::DEFAULT_TTL, $tags);
    }

    /**
     * Invalidation par tags
     */
    public function flushByTags(array $tags): void
    {
        try {
            Cache::tags($tags)->flush();
            Log::info('Cache flushed by tags', ['tags' => $tags]);
        } catch (\Exception $e) {
            Log::error('Cache flush failed', [
                'tags' => $tags,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Invalidation pour une organisation
     */
    public function flushOrganizationCache(int $organizationId): void
    {
        $this->flushByTags(["organization_{$organizationId}"]);
    }

    /**
     * Warm up du cache avec les données essentielles
     */
    public function warmUpOrganizationCache(int $organizationId): void
    {
        try {
            // Pre-cache des données critiques
            $this->rememberOrganizationData($organizationId, 'users', function () use ($organizationId) {
                return \App\Models\User::where('organization_id', $organizationId)
                    ->where('is_active', true)
                    ->select(['id', 'name', 'email', 'last_activity_at'])
                    ->get();
            });

            $this->rememberVehicleData($organizationId, function () use ($organizationId) {
                return \App\Models\Vehicle::where('organization_id', $organizationId)
                    ->with(['vehicleType', 'fuelType', 'vehicleStatus'])
                    ->select(['id', 'registration_plate', 'brand', 'model', 'status_id', 'vehicle_type_id', 'fuel_type_id'])
                    ->get();
            });

            Log::info('Cache warmed up successfully', ['organization_id' => $organizationId]);
        } catch (\Exception $e) {
            Log::error('Cache warm up failed', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Statistiques de performance du cache
     */
    public function getCacheStats(): array
    {
        try {
            if (config('cache.default') === 'redis') {
                $redis = Redis::connection();
                $info = $redis->info('memory');
                
                return [
                    'driver' => 'redis',
                    'memory_used' => $info['used_memory_human'] ?? 'N/A',
                    'memory_peak' => $info['used_memory_peak_human'] ?? 'N/A',
                    'connected_clients' => $redis->info('clients')['connected_clients'] ?? 0,
                    'keyspace_hits' => $redis->info('stats')['keyspace_hits'] ?? 0,
                    'keyspace_misses' => $redis->info('stats')['keyspace_misses'] ?? 0,
                ];
            }
            
            return ['driver' => config('cache.default'), 'stats' => 'not_available'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
