<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\StatusHistory;
use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * 🔄 STATUS TRANSITION SERVICE - Enterprise-Grade State Machine
 *
 * Service centralisé pour gérer les transitions de statuts avec validation,
 * historisation et règles métier.
 *
 * Architecture:
 * - State Machine Pattern pour validation des transitions
 * - Transaction DB pour garantir la cohérence
 * - Event Sourcing pour historique complet
 * - Hooks pour actions personnalisées
 *
 * Fonctionnalités:
 * - Validation automatique des transitions autorisées
 * - Enregistrement automatique dans l'historique
 * - Support des métadonnées (reason, metadata JSON)
 * - Rollback automatique en cas d'erreur
 * - Extensible pour nouvelles entités
 *
 * @version 2.0-Enterprise
 */
class StatusTransitionService
{
    /**
     * Change le statut d'un véhicule avec validation et historisation
     *
     * @param Vehicle $vehicle
     * @param VehicleStatusEnum $newStatus
     * @param array $options Options: reason, metadata, change_type, user_id
     * @return bool
     * @throws InvalidArgumentException Si la transition est invalide
     */
    public function changeVehicleStatus(
        Vehicle $vehicle,
        VehicleStatusEnum $newStatus,
        array $options = []
    ): bool {
        // Récupérer le statut actuel (depuis la relation ou la colonne)
        $currentStatusEnum = $this->getCurrentVehicleStatus($vehicle);

        // Validation de la transition
        if (!$this->validateVehicleTransition($currentStatusEnum, $newStatus)) {
            $errorMessage = $currentStatusEnum
                ? $currentStatusEnum->getTransitionErrorMessage($newStatus)
                : "Impossible de définir le statut initial à '{$newStatus->label()}'.";

            throw new InvalidArgumentException($errorMessage);
        }

        // Exécuter la transition dans une transaction
        return DB::transaction(function () use ($vehicle, $currentStatusEnum, $newStatus, $options) {
            $previousStatus = $currentStatusEnum ? $currentStatusEnum->value : null;

            // Mettre à jour le statut du véhicule
            $statusUpdated = $this->updateVehicleStatusInDatabase($vehicle, $newStatus);

            if (!$statusUpdated) {
                throw new \RuntimeException("Échec de la mise à jour du statut du véhicule.");
            }

            // Enregistrer dans l'historique
            StatusHistory::recordChange(
                $vehicle,
                $previousStatus,
                $newStatus->value,
                array_merge($options, [
                    'change_type' => $options['change_type'] ?? 'manual',
                    'user_id' => $options['user_id'] ?? auth()->id(),
                ])
            );

            // Hook post-transition (peut être étendu)
            $this->executeVehiclePostTransitionHook($vehicle, $currentStatusEnum, $newStatus, $options);

            // Log l'événement
            Log::info("Vehicle status changed", [
                'vehicle_id' => $vehicle->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus->value,
                'user_id' => auth()->id(),
            ]);

            return true;
        });
    }

    /**
     * Change le statut d'un chauffeur avec validation et historisation
     *
     * @param Driver $driver
     * @param DriverStatusEnum $newStatus
     * @param array $options Options: reason, metadata, change_type, user_id
     * @return bool
     * @throws InvalidArgumentException Si la transition est invalide
     */
    public function changeDriverStatus(
        Driver $driver,
        DriverStatusEnum $newStatus,
        array $options = []
    ): bool {
        // Récupérer le statut actuel
        $currentStatusEnum = $this->getCurrentDriverStatus($driver);

        // Validation de la transition
        if (!$this->validateDriverTransition($currentStatusEnum, $newStatus)) {
            $errorMessage = $currentStatusEnum
                ? $currentStatusEnum->getTransitionErrorMessage($newStatus)
                : "Impossible de définir le statut initial à '{$newStatus->label()}'.";

            throw new InvalidArgumentException($errorMessage);
        }

        // Exécuter la transition dans une transaction
        return DB::transaction(function () use ($driver, $currentStatusEnum, $newStatus, $options) {
            $previousStatus = $currentStatusEnum ? $currentStatusEnum->value : null;

            // Mettre à jour le statut du chauffeur
            $statusUpdated = $this->updateDriverStatusInDatabase($driver, $newStatus);

            if (!$statusUpdated) {
                throw new \RuntimeException("Échec de la mise à jour du statut du chauffeur.");
            }

            // Enregistrer dans l'historique
            StatusHistory::recordChange(
                $driver,
                $previousStatus,
                $newStatus->value,
                array_merge($options, [
                    'change_type' => $options['change_type'] ?? 'manual',
                    'user_id' => $options['user_id'] ?? auth()->id(),
                ])
            );

            // Hook post-transition
            $this->executeDriverPostTransitionHook($driver, $currentStatusEnum, $newStatus, $options);

            // Log l'événement
            Log::info("Driver status changed", [
                'driver_id' => $driver->id,
                'from_status' => $previousStatus,
                'to_status' => $newStatus->value,
                'user_id' => auth()->id(),
            ]);

            return true;
        });
    }

    // =========================================================================
    // VALIDATION METHODS
    // =========================================================================

    /**
     * Valide une transition de statut pour un véhicule
     */
    protected function validateVehicleTransition(
        ?VehicleStatusEnum $currentStatus,
        VehicleStatusEnum $newStatus
    ): bool {
        // Si pas de statut actuel (création initiale), autoriser tous les statuts sauf réformé
        if ($currentStatus === null) {
            return $newStatus !== VehicleStatusEnum::REFORME;
        }

        // Utiliser la logique métier de l'Enum
        return $currentStatus->canTransitionTo($newStatus);
    }

    /**
     * Valide une transition de statut pour un chauffeur
     */
    protected function validateDriverTransition(
        ?DriverStatusEnum $currentStatus,
        DriverStatusEnum $newStatus
    ): bool {
        // Si pas de statut actuel (création initiale), autoriser tous sauf EN_MISSION
        if ($currentStatus === null) {
            return $newStatus !== DriverStatusEnum::EN_MISSION;
        }

        // Utiliser la logique métier de l'Enum
        return $currentStatus->canTransitionTo($newStatus);
    }

    // =========================================================================
    // STATUS RETRIEVAL METHODS
    // =========================================================================

    /**
     * Récupère le statut actuel d'un véhicule sous forme d'Enum
     */
    protected function getCurrentVehicleStatus(Vehicle $vehicle): ?VehicleStatusEnum
    {
        // Si le modèle utilise déjà un Enum cast, le récupérer directement
        if ($vehicle->status instanceof VehicleStatusEnum) {
            return $vehicle->status;
        }

        // Sinon, récupérer depuis la relation status_id
        if ($vehicle->status_id && $vehicle->vehicleStatus) {
            $statusSlug = \Str::slug($vehicle->vehicleStatus->name);
            return VehicleStatusEnum::tryFrom($statusSlug);
        }

        return null;
    }

    /**
     * Récupère le statut actuel d'un chauffeur sous forme d'Enum
     */
    protected function getCurrentDriverStatus(Driver $driver): ?DriverStatusEnum
    {
        // Si le modèle utilise déjà un Enum cast, le récupérer directement
        if ($driver->status instanceof DriverStatusEnum) {
            return $driver->status;
        }

        // Sinon, récupérer depuis la relation status_id
        if ($driver->status_id && $driver->driverStatus) {
            $statusSlug = \Str::slug($driver->driverStatus->name);
            return DriverStatusEnum::tryFrom($statusSlug);
        }

        return null;
    }

    // =========================================================================
    // DATABASE UPDATE METHODS
    // =========================================================================

    /**
     * Met à jour le statut du véhicule en base de données
     */
    protected function updateVehicleStatusInDatabase(
        Vehicle $vehicle,
        VehicleStatusEnum $newStatus
    ): bool {
        // Trouver le status_id correspondant au slug de l'enum
        $vehicleStatus = \App\Models\VehicleStatus::where('slug', $newStatus->value)->first();

        if (!$vehicleStatus) {
            throw new \RuntimeException("Statut '{$newStatus->value}' non trouvé en base de données.");
        }

        $vehicle->status_id = $vehicleStatus->id;
        return $vehicle->save();
    }

    /**
     * Met à jour le statut du chauffeur en base de données
     */
    protected function updateDriverStatusInDatabase(
        Driver $driver,
        DriverStatusEnum $newStatus
    ): bool {
        // Trouver le status_id correspondant au slug de l'enum
        $driverStatus = \App\Models\DriverStatus::where('slug', $newStatus->value)->first();

        if (!$driverStatus) {
            throw new \RuntimeException("Statut '{$newStatus->value}' non trouvé en base de données.");
        }

        $driver->status_id = $driverStatus->id;
        return $driver->save();
    }

    // =========================================================================
    // POST-TRANSITION HOOKS - EXTENSIBLE
    // =========================================================================

    /**
     * Hook exécuté après une transition de statut de véhicule
     *
     * Permet d'ajouter des actions automatiques selon les transitions.
     *
     * Exemples:
     * - PARKING → AFFECTÉ : Créer une notification
     * - EN_PANNE → EN_MAINTENANCE : Créer une opération de maintenance
     * - EN_MAINTENANCE → REFORMÉ : Archiver le véhicule
     */
    protected function executeVehiclePostTransitionHook(
        Vehicle $vehicle,
        ?VehicleStatusEnum $fromStatus,
        VehicleStatusEnum $toStatus,
        array $options
    ): void {
        // Hook EN_PANNE → EN_MAINTENANCE : Vérifier qu'une maintenance est planifiée
        if ($fromStatus === VehicleStatusEnum::EN_PANNE && $toStatus === VehicleStatusEnum::EN_MAINTENANCE) {
            // Ici, on pourrait automatiquement créer une MaintenanceOperation si elle n'existe pas
            // Exemple: MaintenanceOperation::createFromBreakdown($vehicle, $options['metadata'] ?? []);
        }

        // Hook EN_MAINTENANCE → PARKING : Mise à jour de current_value (dépréciation)
        if ($fromStatus === VehicleStatusEnum::EN_MAINTENANCE && $toStatus === VehicleStatusEnum::PARKING) {
            // Logique de dépréciation ou mise à jour de valeur
        }

        // Hook → REFORMÉ : Désaffecter automatiquement si affecté
        if ($toStatus === VehicleStatusEnum::REFORME) {
            if ($vehicle->isCurrentlyAssigned()) {
                // Terminer les affectations actives
                $vehicle->assignments()->whereNull('end_datetime')->update([
                    'end_datetime' => now(),
                    'status' => 'terminated',
                ]);
            }
        }

        // Fire Laravel Event (si implémenté)
        // event(new VehicleStatusChanged($vehicle, $fromStatus, $toStatus, $options));
    }

    /**
     * Hook exécuté après une transition de statut de chauffeur
     */
    protected function executeDriverPostTransitionHook(
        Driver $driver,
        ?DriverStatusEnum $fromStatus,
        DriverStatusEnum $toStatus,
        array $options
    ): void {
        // Hook → EN_CONGE : Créer une entrée dans le système de gestion des congés
        if ($toStatus === DriverStatusEnum::EN_CONGE) {
            // Exemple: LeaveRequest::createFromStatusChange($driver, $options);
        }

        // Hook EN_MISSION → DISPONIBLE : Terminer l'affectation active
        if ($fromStatus === DriverStatusEnum::EN_MISSION && $toStatus === DriverStatusEnum::DISPONIBLE) {
            // Terminer les affectations actives
            $driver->assignments()->whereNull('end_datetime')->update([
                'end_datetime' => now(),
                'status' => 'completed',
            ]);
        }

        // Fire Laravel Event
        // event(new DriverStatusChanged($driver, $fromStatus, $toStatus, $options));
    }

    // =========================================================================
    // BULK OPERATIONS
    // =========================================================================

    /**
     * Change le statut de plusieurs véhicules en masse
     *
     * @param array $vehicleIds
     * @param VehicleStatusEnum $newStatus
     * @param array $options
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function bulkChangeVehicleStatus(
        array $vehicleIds,
        VehicleStatusEnum $newStatus,
        array $options = []
    ): array {
        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($vehicleIds as $vehicleId) {
            try {
                $vehicle = Vehicle::findOrFail($vehicleId);
                $this->changeVehicleStatus($vehicle, $newStatus, $options);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[$vehicleId] = $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }
}
