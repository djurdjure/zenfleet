<?php

namespace App\Observers;

use App\Models\VehicleDepot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * VehicleDepotObserver
 *
 * Observer Enterprise-Grade pour gérer automatiquement l'invalidation du cache
 * des dépôts lorsqu'un dépôt est créé, modifié, activé, désactivé ou supprimé.
 *
 * Règles métier ENTERPRISE-GRADE:
 * - INVALIDATION CACHE: Invalide le cache des données de référence dès qu'un dépôt change
 * - SYNCHRONISATION TEMPS RÉEL: Les filtres se mettent à jour immédiatement
 * - AUDIT TRAIL: Log toutes les opérations pour traçabilité
 * - MULTI-TENANT SAFE: Invalide uniquement le cache de l'organisation concernée
 *
 * @version 1.0-Enterprise
 * @author ZenFleet Architecture Team
 * @since 2025-11-23
 */
class VehicleDepotObserver
{
    /**
     * Handle the VehicleDepot "created" event.
     *
     * ✅ INVALIDATION CACHE ENTERPRISE:
     * Invalide le cache des données de référence pour que le nouveau dépôt
     * apparaisse immédiatement dans les filtres.
     *
     * @param VehicleDepot $depot
     * @return void
     */
    public function created(VehicleDepot $depot): void
    {
        $this->invalidateCache($depot, 'created');

        Log::info('Dépôt créé - Cache invalidé', [
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
            'organization_id' => $depot->organization_id,
            'is_active' => $depot->is_active,
        ]);
    }

    /**
     * Handle the VehicleDepot "updated" event.
     *
     * ✅ INVALIDATION CACHE ENTERPRISE:
     * Invalide le cache si des attributs critiques ont changé (nom, statut actif, etc.)
     *
     * @param VehicleDepot $depot
     * @return void
     */
    public function updated(VehicleDepot $depot): void
    {
        // Vérifier si des attributs critiques ont changé
        $criticalAttributes = ['name', 'is_active', 'code', 'city', 'wilaya'];
        $hasChangedCriticalAttributes = false;

        foreach ($criticalAttributes as $attribute) {
            if ($depot->isDirty($attribute)) {
                $hasChangedCriticalAttributes = true;
                break;
            }
        }

        if ($hasChangedCriticalAttributes) {
            $this->invalidateCache($depot, 'updated');

            Log::info('Dépôt modifié - Cache invalidé', [
                'depot_id' => $depot->id,
                'depot_name' => $depot->name,
                'organization_id' => $depot->organization_id,
                'changed_attributes' => $depot->getDirty(),
            ]);
        }
    }

    /**
     * Handle the VehicleDepot "deleted" event.
     *
     * ✅ INVALIDATION CACHE ENTERPRISE:
     * Invalide le cache pour que le dépôt supprimé disparaisse immédiatement des filtres.
     *
     * @param VehicleDepot $depot
     * @return void
     */
    public function deleted(VehicleDepot $depot): void
    {
        $this->invalidateCache($depot, 'deleted');

        Log::info('Dépôt supprimé (soft delete) - Cache invalidé', [
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
            'organization_id' => $depot->organization_id,
        ]);
    }

    /**
     * Handle the VehicleDepot "restored" event.
     *
     * ✅ INVALIDATION CACHE ENTERPRISE:
     * Invalide le cache pour que le dépôt restauré réapparaisse immédiatement dans les filtres.
     *
     * @param VehicleDepot $depot
     * @return void
     */
    public function restored(VehicleDepot $depot): void
    {
        $this->invalidateCache($depot, 'restored');

        Log::info('Dépôt restauré - Cache invalidé', [
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
            'organization_id' => $depot->organization_id,
            'is_active' => $depot->is_active,
        ]);
    }

    /**
     * Handle the VehicleDepot "force deleted" event.
     *
     * ✅ INVALIDATION CACHE ENTERPRISE:
     * Invalide le cache pour suppression définitive.
     *
     * @param VehicleDepot $depot
     * @return void
     */
    public function forceDeleted(VehicleDepot $depot): void
    {
        $this->invalidateCache($depot, 'force_deleted');

        Log::warning('Dépôt supprimé définitivement - Cache invalidé', [
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
            'organization_id' => $depot->organization_id,
        ]);
    }

    /**
     * Invalide le cache des données de référence pour l'organisation du dépôt.
     *
     * ✅ STRATÉGIE ENTERPRISE V2.0:
     * - Invalide le cache spécifique des dépôts (vehicle_depots_{org_id})
     * - Invalide le cache statique des données de référence (vehicle_static_reference_data_{org_id})
     * - Invalide également les tags de cache pour une invalidation granulaire
     * - Supporte le multi-tenant: chaque organisation a son propre cache
     *
     * @param VehicleDepot $depot
     * @param string $action Action déclenchant l'invalidation (pour logging)
     * @return void
     */
    protected function invalidateCache(VehicleDepot $depot, string $action): void
    {
        $organizationId = $depot->organization_id;

        // 1. Invalider le cache spécifique des dépôts (priorité car plus volatile)
        $depotsCacheKey = "vehicle_depots_{$organizationId}";
        Cache::forget($depotsCacheKey);

        // 2. Invalider le cache statique des données de référence (par sécurité)
        $staticCacheKey = "vehicle_static_reference_data_{$organizationId}";
        Cache::forget($staticCacheKey);

        // 3. Invalider l'ancien cache global (rétrocompatibilité)
        $legacyCacheKey = "vehicle_reference_data_{$organizationId}";
        Cache::forget($legacyCacheKey);

        // 4. Invalider les tags de cache pour une granularité maximale
        try {
            Cache::tags(['vehicles', 'depots', "org_{$organizationId}"])->flush();
        } catch (\Exception $e) {
            // Fallback si le driver de cache ne supporte pas les tags
            Log::debug('Cache tags not supported, using key-based invalidation only', [
                'error' => $e->getMessage()
            ]);
        }

        Log::debug('Cache invalidé avec succès', [
            'action' => $action,
            'organization_id' => $organizationId,
            'depot_cache_key' => $depotsCacheKey,
            'static_cache_key' => $staticCacheKey,
            'legacy_cache_key' => $legacyCacheKey,
            'depot_id' => $depot->id,
            'depot_name' => $depot->name,
        ]);
    }
}
