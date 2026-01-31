<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * 🎯 SERVICE ENTERPRISE-GRADE : SYNCHRONISATION DES STATUTS DES RESSOURCES
 *
 * Ce service est la SEULE source de vérité pour la synchronisation entre :
 * - Statut Dynamique (is_available + assignment_status) [SOURCE PRIMAIRE]
 * - Statut Métier (status_id) [CHAMP DÉRIVÉ]
 *
 * PRINCIPE ARCHITECTURAL :
 * Le statut dynamique (is_available, assignment_status) est la source de vérité opérationnelle.
 * Le status_id est une propriété calculée/dérivée qui doit TOUJOURS refléter le statut dynamique.
 *
 * RÈGLES DE SYNCHRONISATION :
 * 1. Si is_available=true ET assignment_status='available' → status_id DOIT être celui de "disponible"
 * 2. Si is_available=false ET assignment_status='assigned' → status_id DOIT être celui de "en mission"
 * 3. Pour les autres cas (maintenance, congé, etc.) → Ne pas modifier automatiquement le status_id
 *
 * @version 3.0.0-Enterprise-Validated
 * @author ZenFleet Architecture Team
 * @date 2025-11-14
 */
class ResourceStatusSynchronizer
{
    /**
     * IDs des statuts dans la base de données
     * ⚠️ CES VALEURS DOIVENT CORRESPONDRE À VOTRE TABLE vehicle_statuses
     */
    const VEHICLE_STATUS_PARKING = 8;      // Véhicule disponible au parking
    const VEHICLE_STATUS_AFFECTE = 9;      // Véhicule en mission/affecté

    /**
     * IDs des statuts dans la base de données
     * ⚠️ CES VALEURS DOIVENT CORRESPONDRE À VOTRE TABLE driver_statuses
     */
    const DRIVER_STATUS_DISPONIBLE = 7;    // Chauffeur disponible pour affectation
    const DRIVER_STATUS_EN_MISSION = 8;    // Chauffeur en mission active

    /**
     * Mapping pour les statuts spéciaux des chauffeurs
     */
    const DRIVER_SPECIAL_STATUS_MAP = [
        'on_leave' => 3,     // En congé
        'training' => 4,     // En formation
        // Ajouter d'autres mappings si nécessaire
    ];

    /**
     * Mapping des statuts spéciaux vers enums (fallback si assignment_status != available/assigned)
     */
    private const DRIVER_SPECIAL_STATUS_ENUM_MAP = [
        'on_leave' => DriverStatusEnum::EN_CONGE,
        'training' => DriverStatusEnum::EN_FORMATION,
        'other' => DriverStatusEnum::AUTRE,
        'suspended' => DriverStatusEnum::AUTRE,
    ];

    /**
     * Cache en mémoire pour éviter les requêtes répétées
     */
    private array $vehicleStatusCache = [];
    private array $driverStatusCache = [];

    /**
     * Synchronise le status_id d'un véhicule selon son état de disponibilité
     *
     * SOURCE DE VÉRITÉ : is_available + assignment_status
     *
     * LOGIQUE :
     * - Si is_available=true ET assignment_status='available' → status_id = 8 (Parking)
     * - Si is_available=false ET assignment_status='assigned' → status_id = 9 (Affecté)
     * - Autres cas (maintenance, reserved, etc.) → Ne pas modifier le status_id
     *
     * @param Vehicle $vehicle Instance du véhicule à synchroniser
     * @return void
     */
    public function syncVehicleStatus(Vehicle $vehicle): void
    {
        $oldStatusId = $vehicle->status_id;
        $newStatusId = null;
        $organizationId = $vehicle->organization_id;

        // CAS 1 : Véhicule disponible pour affectation
        if ($vehicle->is_available === true && $vehicle->assignment_status === 'available') {
            $newStatusId = $this->resolveVehicleStatusIdForAvailable($organizationId);
        }
        // CAS 2 : Véhicule affecté en mission
        elseif ($vehicle->is_available === false && $vehicle->assignment_status === 'assigned') {
            $newStatusId = $this->resolveVehicleStatusIdForAssigned($organizationId);
        }
        // CAS 3 : Autres statuts (maintenance, reserved, etc.)
        // Le status_id a été défini manuellement, ne pas le modifier
        else {
            Log::debug('[ResourceStatusSynchronizer] Véhicule avec statut spécial, pas de synchronisation', [
                'vehicle_id' => $vehicle->id,
                'is_available' => $vehicle->is_available,
                'assignment_status' => $vehicle->assignment_status,
                'status_id' => $vehicle->status_id,
            ]);
            return;
        }

        // Mise à jour si nécessaire
        if ($newStatusId === null) {
            Log::warning('[ResourceStatusSynchronizer] Statut véhicule introuvable - synchronisation ignorée', [
                'vehicle_id' => $vehicle->id,
                'assignment_status' => $vehicle->assignment_status,
                'organization_id' => $organizationId,
            ]);
            return;
        }

        if ($vehicle->status_id !== $newStatusId) {
            $vehicle->update(['status_id' => $newStatusId]);

            Log::info('[ResourceStatusSynchronizer] 🔄 Véhicule synchronisé', [
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_plate,
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatusId,
                'is_available' => $vehicle->is_available,
                'assignment_status' => $vehicle->assignment_status,
            ]);
        }
    }

    /**
     * Synchronise le status_id d'un chauffeur selon son état de disponibilité
     *
     * SOURCE DE VÉRITÉ : is_available + assignment_status
     *
     * LOGIQUE :
     * - Si is_available=true ET assignment_status='available' → status_id = 7 (Disponible)
     * - Si is_available=false ET assignment_status='assigned' → status_id = 8 (En mission)
     * - Si assignment_status est un statut spécial (on_leave, training) → Utiliser le mapping
     * - Autres cas → Ne pas modifier le status_id
     *
     * @param Driver $driver Instance du chauffeur à synchroniser
     * @return void
     */
    public function syncDriverStatus(Driver $driver): void
    {
        $oldStatusId = $driver->status_id;
        $newStatusId = null;
        $organizationId = $driver->organization_id;

        // CAS 1 : Chauffeur disponible pour affectation
        if ($driver->is_available === true && $driver->assignment_status === 'available') {
            $newStatusId = $this->resolveDriverStatusIdForAvailable($organizationId);
        }
        // CAS 2 : Chauffeur en mission
        elseif ($driver->is_available === false && $driver->assignment_status === 'assigned') {
            $newStatusId = $this->resolveDriverStatusIdForAssigned($organizationId);
        }
        // CAS 3 : Statuts spéciaux (on_leave, training, etc.)
        elseif (isset(self::DRIVER_SPECIAL_STATUS_ENUM_MAP[$driver->assignment_status])) {
            $enum = self::DRIVER_SPECIAL_STATUS_ENUM_MAP[$driver->assignment_status];
            $newStatusId = $this->resolveDriverStatusIdFromEnum($enum, $organizationId);
        } elseif ($driver->assignment_status) {
            $normalizedStatus = str_replace('-', '_', $driver->assignment_status);
            $enum = DriverStatusEnum::tryFrom($normalizedStatus);
            if ($enum) {
                $newStatusId = $this->resolveDriverStatusIdFromEnum($enum, $organizationId);
            }
        }
        // CAS 4 : Autres cas (suspended, inactive, etc.)
        // Le status_id a été défini manuellement, ne pas le modifier
        else {
            Log::debug('[ResourceStatusSynchronizer] Chauffeur avec statut spécial, pas de synchronisation', [
                'driver_id' => $driver->id,
                'is_available' => $driver->is_available,
                'assignment_status' => $driver->assignment_status,
                'status_id' => $driver->status_id,
            ]);
            return;
        }

        // Mise à jour si nécessaire
        if ($newStatusId === null) {
            Log::warning('[ResourceStatusSynchronizer] Statut chauffeur introuvable - synchronisation ignorée', [
                'driver_id' => $driver->id,
                'assignment_status' => $driver->assignment_status,
                'organization_id' => $organizationId,
            ]);
            return;
        }

        if ($driver->status_id !== $newStatusId) {
            $driver->update(['status_id' => $newStatusId]);

            Log::info('[ResourceStatusSynchronizer] 🔄 Chauffeur synchronisé', [
                'driver_id' => $driver->id,
                'name' => $driver->first_name . ' ' . $driver->last_name,
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatusId,
                'is_available' => $driver->is_available,
                'assignment_status' => $driver->assignment_status,
            ]);
        }
    }

    /**
     * Détecte et corrige tous les véhicules zombies
     *
     * DÉFINITION D'UN ZOMBIE :
     * - Véhicule avec is_available=true ET assignment_status='available' MAIS status_id != 8
     * - Véhicule avec is_available=false ET assignment_status='assigned' MAIS status_id != 9
     *
     * @return array Statistiques de correction
     */
    public function healAllVehicleZombies(): array
    {
        $parkingStatusId = $this->resolveVehicleStatusIdForAvailable();
        $assignedStatusId = $this->resolveVehicleStatusIdForAssigned();

        $zombiesAvailable = collect();
        $zombiesAssigned = collect();

        if ($parkingStatusId) {
            $zombiesAvailable = Vehicle::where('is_available', true)
                ->where('assignment_status', 'available')
                ->where('status_id', '!=', $parkingStatusId)
                ->whereNull('deleted_at')
                ->get();
        } else {
            Log::warning('[ResourceStatusSynchronizer] Statut PARKING introuvable - healAllVehicleZombies partiel');
        }

        if ($assignedStatusId) {
            $zombiesAssigned = Vehicle::where('is_available', false)
                ->where('assignment_status', 'assigned')
                ->where('status_id', '!=', $assignedStatusId)
                ->whereNull('deleted_at')
                ->get();
        } else {
            Log::warning('[ResourceStatusSynchronizer] Statut AFFECTE introuvable - healAllVehicleZombies partiel');
        }

        $zombies = $zombiesAvailable->merge($zombiesAssigned);
        $healed = 0;

        foreach ($zombies as $zombie) {
            $this->syncVehicleStatus($zombie);
            $healed++;
        }

        return [
            'type' => 'vehicles',
            'zombies_found' => $zombies->count(),
            'zombies_healed' => $healed,
            'details' => [
                'available_with_wrong_status' => $zombiesAvailable->count(),
                'assigned_with_wrong_status' => $zombiesAssigned->count(),
            ]
        ];
    }

    /**
     * Détecte et corrige tous les chauffeurs zombies
     *
     * DÉFINITION D'UN ZOMBIE :
     * - Chauffeur avec is_available=true ET assignment_status='available' MAIS status_id != 7
     * - Chauffeur avec is_available=false ET assignment_status='assigned' MAIS status_id != 8
     *
     * @return array Statistiques de correction
     */
    public function healAllDriverZombies(): array
    {
        $availableStatusId = $this->resolveDriverStatusIdForAvailable();
        $assignedStatusId = $this->resolveDriverStatusIdForAssigned();

        $zombiesAvailable = collect();
        $zombiesAssigned = collect();

        if ($availableStatusId) {
            $zombiesAvailable = Driver::where('is_available', true)
                ->where('assignment_status', 'available')
                ->where('status_id', '!=', $availableStatusId)
                ->whereNull('deleted_at')
                ->get();
        } else {
            Log::warning('[ResourceStatusSynchronizer] Statut DISPONIBLE introuvable - healAllDriverZombies partiel');
        }

        if ($assignedStatusId) {
            $zombiesAssigned = Driver::where('is_available', false)
                ->where('assignment_status', 'assigned')
                ->where('status_id', '!=', $assignedStatusId)
                ->whereNull('deleted_at')
                ->get();
        } else {
            Log::warning('[ResourceStatusSynchronizer] Statut EN_MISSION introuvable - healAllDriverZombies partiel');
        }

        $zombies = $zombiesAvailable->merge($zombiesAssigned);
        $healed = 0;

        foreach ($zombies as $zombie) {
            $this->syncDriverStatus($zombie);
            $healed++;
        }

        return [
            'type' => 'drivers',
            'zombies_found' => $zombies->count(),
            'zombies_healed' => $healed,
            'details' => [
                'available_with_wrong_status' => $zombiesAvailable->count(),
                'assigned_with_wrong_status' => $zombiesAssigned->count(),
            ]
        ];
    }

    /**
     * Détecte et corrige TOUS les zombies (véhicules + chauffeurs)
     *
     * @return array Statistiques globales
     */
    public function healAllZombies(): array
    {
        $vehicleStats = [];
        $driverStats = [];

        DB::transaction(function () use (&$vehicleStats, &$driverStats) {
            $vehicleStats = $this->healAllVehicleZombies();
            $driverStats = $this->healAllDriverZombies();
        });

        return [
            'vehicles' => $vehicleStats,
            'drivers' => $driverStats,
            'total_healed' => $vehicleStats['zombies_healed'] + $driverStats['zombies_healed'],
            'total_found' => $vehicleStats['zombies_found'] + $driverStats['zombies_found'],
        ];
    }

    /**
     * Vérifie si un véhicule est en état zombie
     *
     * @param Vehicle $vehicle
     * @return bool
     */
    public function isVehicleZombie(Vehicle $vehicle): bool
    {
        $organizationId = $vehicle->organization_id;
        $parkingStatusId = $this->resolveVehicleStatusIdForAvailable($organizationId);
        $assignedStatusId = $this->resolveVehicleStatusIdForAssigned($organizationId);

        // Zombie si disponible mais avec mauvais status_id
        if ($parkingStatusId && $vehicle->is_available === true &&
            $vehicle->assignment_status === 'available' &&
            $vehicle->status_id !== $parkingStatusId) {
            return true;
        }

        // Zombie si affecté mais avec mauvais status_id
        if ($assignedStatusId && $vehicle->is_available === false &&
            $vehicle->assignment_status === 'assigned' &&
            $vehicle->status_id !== $assignedStatusId) {
            return true;
        }

        return false;
    }

    /**
     * Vérifie si un chauffeur est en état zombie
     *
     * @param Driver $driver
     * @return bool
     */
    public function isDriverZombie(Driver $driver): bool
    {
        $organizationId = $driver->organization_id;
        $availableStatusId = $this->resolveDriverStatusIdForAvailable($organizationId);
        $assignedStatusId = $this->resolveDriverStatusIdForAssigned($organizationId);

        // Zombie si disponible mais avec mauvais status_id
        if ($availableStatusId && $driver->is_available === true &&
            $driver->assignment_status === 'available' &&
            $driver->status_id !== $availableStatusId) {
            return true;
        }

        // Zombie si affecté mais avec mauvais status_id
        if ($assignedStatusId && $driver->is_available === false &&
            $driver->assignment_status === 'assigned' &&
            $driver->status_id !== $assignedStatusId) {
            return true;
        }

        return false;
    }

    /**
     * Résout l'ID du statut véhicule "parking" (disponible)
     */
    public function resolveVehicleStatusIdForAvailable(?int $organizationId = null): ?int
    {
        return $this->resolveVehicleStatusId([
            VehicleStatusEnum::PARKING->value,
            'available',
            'disponible'
        ], [
            VehicleStatusEnum::PARKING->label(),
            'Disponible'
        ], $organizationId, self::VEHICLE_STATUS_PARKING);
    }

    /**
     * Résout l'ID du statut véhicule "affecté" (assigné)
     */
    public function resolveVehicleStatusIdForAssigned(?int $organizationId = null): ?int
    {
        return $this->resolveVehicleStatusId([
            VehicleStatusEnum::AFFECTE->value,
            'en_mission',
            'assigned'
        ], [
            VehicleStatusEnum::AFFECTE->label(),
            'En mission'
        ], $organizationId, self::VEHICLE_STATUS_AFFECTE);
    }

    /**
     * Résout l'ID du statut chauffeur "disponible"
     */
    public function resolveDriverStatusIdForAvailable(?int $organizationId = null): ?int
    {
        return $this->resolveDriverStatusId([
            DriverStatusEnum::DISPONIBLE->value,
            'available'
        ], [
            DriverStatusEnum::DISPONIBLE->label(),
            'Available'
        ], $organizationId, self::DRIVER_STATUS_DISPONIBLE);
    }

    /**
     * Résout l'ID du statut chauffeur "en mission"
     */
    public function resolveDriverStatusIdForAssigned(?int $organizationId = null): ?int
    {
        return $this->resolveDriverStatusId([
            DriverStatusEnum::EN_MISSION->value,
            'assigned'
        ], [
            DriverStatusEnum::EN_MISSION->label(),
            'En mission'
        ], $organizationId, self::DRIVER_STATUS_EN_MISSION);
    }

    private function resolveDriverStatusIdFromEnum(DriverStatusEnum $enum, ?int $organizationId = null): ?int
    {
        return $this->resolveDriverStatusId([
            $enum->value
        ], [
            $enum->label()
        ], $organizationId);
    }

    /**
     * Résout un ID de statut véhicule de manière robuste et multi-tenant
     */
    private function resolveVehicleStatusId(array $slugCandidates, array $nameCandidates, ?int $organizationId, ?int $fallbackId = null): ?int
    {
        $slugCandidates = $this->expandSlugCandidates($slugCandidates);
        $cacheKey = ($organizationId ?? 'global') . ':vehicle:' . implode('|', $slugCandidates) . ':' . implode('|', $nameCandidates) . ':' . ($fallbackId ?? 'none');

        if (array_key_exists($cacheKey, $this->vehicleStatusCache)) {
            return $this->vehicleStatusCache[$cacheKey];
        }

        $query = VehicleStatus::query()
            ->where(function ($q) use ($slugCandidates, $nameCandidates) {
                if (!empty($slugCandidates)) {
                    $q->whereIn('slug', $slugCandidates);
                }
                if (!empty($nameCandidates)) {
                    $q->orWhereIn('name', $nameCandidates);
                }
            });

        if ($organizationId !== null) {
            $query->where(function ($q) use ($organizationId) {
                $q->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationId);
            })
            ->orderByRaw('organization_id is null');
        } else {
            $query->whereNull('organization_id');
        }

        $status = $query->orderBy('sort_order')->orderBy('id')->first();

        if (!$status && $fallbackId) {
            $status = VehicleStatus::whereKey($fallbackId)->first();
        }

        if (!$status) {
            Log::warning('[ResourceStatusSynchronizer] Statut véhicule non trouvé', [
                'slugs' => $slugCandidates,
                'names' => $nameCandidates,
                'organization_id' => $organizationId,
                'fallback_id' => $fallbackId,
            ]);
        }

        return $this->vehicleStatusCache[$cacheKey] = $status?->id;
    }

    /**
     * Résout un ID de statut chauffeur de manière robuste et multi-tenant
     */
    private function resolveDriverStatusId(array $slugCandidates, array $nameCandidates, ?int $organizationId, ?int $fallbackId = null): ?int
    {
        $slugCandidates = $this->expandSlugCandidates($slugCandidates);
        $cacheKey = ($organizationId ?? 'global') . ':driver:' . implode('|', $slugCandidates) . ':' . implode('|', $nameCandidates) . ':' . ($fallbackId ?? 'none');

        if (array_key_exists($cacheKey, $this->driverStatusCache)) {
            return $this->driverStatusCache[$cacheKey];
        }

        $query = DriverStatus::query()
            ->where(function ($q) use ($slugCandidates, $nameCandidates) {
                if (!empty($slugCandidates)) {
                    $q->whereIn('slug', $slugCandidates);
                }
                if (!empty($nameCandidates)) {
                    $q->orWhereIn('name', $nameCandidates);
                }
            });

        if ($organizationId !== null) {
            $query->where(function ($q) use ($organizationId) {
                $q->whereNull('organization_id')
                    ->orWhere('organization_id', $organizationId);
            })
            ->orderByRaw('organization_id is null');
        } else {
            $query->whereNull('organization_id');
        }

        $status = $query->orderBy('sort_order')->orderBy('id')->first();

        if (!$status && $fallbackId) {
            $status = DriverStatus::whereKey($fallbackId)->first();
        }

        if (!$status) {
            Log::warning('[ResourceStatusSynchronizer] Statut chauffeur non trouvé', [
                'slugs' => $slugCandidates,
                'names' => $nameCandidates,
                'organization_id' => $organizationId,
                'fallback_id' => $fallbackId,
            ]);
        }

        return $this->driverStatusCache[$cacheKey] = $status?->id;
    }

    /**
     * Ajoute les variantes slug avec tirets/underscores
     */
    private function expandSlugCandidates(array $slugs): array
    {
        $expanded = [];

        foreach ($slugs as $slug) {
            if (!is_string($slug) || $slug === '') {
                continue;
            }
            $expanded[] = $slug;
            if (str_contains($slug, '_')) {
                $expanded[] = str_replace('_', '-', $slug);
            }
            if (str_contains($slug, '-')) {
                $expanded[] = str_replace('-', '_', $slug);
            }
        }

        return array_values(array_unique($expanded));
    }
}
