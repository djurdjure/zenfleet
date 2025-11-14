<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Driver;
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

        // CAS 1 : Véhicule disponible pour affectation
        if ($vehicle->is_available === true && $vehicle->assignment_status === 'available') {
            $newStatusId = self::VEHICLE_STATUS_PARKING;
        }
        // CAS 2 : Véhicule affecté en mission
        elseif ($vehicle->is_available === false && $vehicle->assignment_status === 'assigned') {
            $newStatusId = self::VEHICLE_STATUS_AFFECTE;
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
        if ($newStatusId && $vehicle->status_id !== $newStatusId) {
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

        // CAS 1 : Chauffeur disponible pour affectation
        if ($driver->is_available === true && $driver->assignment_status === 'available') {
            $newStatusId = self::DRIVER_STATUS_DISPONIBLE;
        }
        // CAS 2 : Chauffeur en mission
        elseif ($driver->is_available === false && $driver->assignment_status === 'assigned') {
            $newStatusId = self::DRIVER_STATUS_EN_MISSION;
        }
        // CAS 3 : Statuts spéciaux (on_leave, training, etc.)
        elseif (isset(self::DRIVER_SPECIAL_STATUS_MAP[$driver->assignment_status])) {
            $newStatusId = self::DRIVER_SPECIAL_STATUS_MAP[$driver->assignment_status];
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
        if ($newStatusId && $driver->status_id !== $newStatusId) {
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
        $zombiesAvailable = Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->where('status_id', '!=', self::VEHICLE_STATUS_PARKING)
            ->whereNull('deleted_at')
            ->get();

        $zombiesAssigned = Vehicle::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->where('status_id', '!=', self::VEHICLE_STATUS_AFFECTE)
            ->whereNull('deleted_at')
            ->get();

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
        $zombiesAvailable = Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->where('status_id', '!=', self::DRIVER_STATUS_DISPONIBLE)
            ->whereNull('deleted_at')
            ->get();

        $zombiesAssigned = Driver::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->where('status_id', '!=', self::DRIVER_STATUS_EN_MISSION)
            ->whereNull('deleted_at')
            ->get();

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
        // Zombie si disponible mais avec mauvais status_id
        if ($vehicle->is_available === true &&
            $vehicle->assignment_status === 'available' &&
            $vehicle->status_id !== self::VEHICLE_STATUS_PARKING) {
            return true;
        }

        // Zombie si affecté mais avec mauvais status_id
        if ($vehicle->is_available === false &&
            $vehicle->assignment_status === 'assigned' &&
            $vehicle->status_id !== self::VEHICLE_STATUS_AFFECTE) {
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
        // Zombie si disponible mais avec mauvais status_id
        if ($driver->is_available === true &&
            $driver->assignment_status === 'available' &&
            $driver->status_id !== self::DRIVER_STATUS_DISPONIBLE) {
            return true;
        }

        // Zombie si affecté mais avec mauvais status_id
        if ($driver->is_available === false &&
            $driver->assignment_status === 'assigned' &&
            $driver->status_id !== self::DRIVER_STATUS_EN_MISSION) {
            return true;
        }

        return false;
    }
}
