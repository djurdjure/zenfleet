<?php

namespace App\Services;

use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\VehicleMileageService;

/**
 * 🎯 SERVICE ENTERPRISE-GRADE : TERMINAISON D'AFFECTATION
 *
 * Ce service garantit l'atomicité et la cohérence de la terminaison d'affectation
 * en orchestrant toutes les opérations nécessaires dans une transaction unique.
 *
 * RESPONSABILITÉS :
 * - Vérifier que l'affectation peut être terminée
 * - Terminer l'affectation (set end_datetime, ended_at, ended_by)
 * - Libérer les ressources (véhicule et chauffeur)
 * - Synchroniser les statuts métier (status_id)
 * - Dispatcher les événements
 * - Logger pour audit trail
 *
 * PRINCIPE :
 * - Transaction ACID garantie
 * - Aucune libération partielle possible
 * - Rollback automatique en cas d'erreur
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-14
 */
class AssignmentTerminationService
{
    private ResourceStatusSynchronizer $statusSync;
    private VehicleMileageService $mileageService;

    public function __construct(
        ResourceStatusSynchronizer $statusSync,
        VehicleMileageService $mileageService
    ) {
        $this->statusSync = $statusSync;
        $this->mileageService = $mileageService;
    }

    /**
     * Termine une affectation de manière atomique et cohérente
     *
     * @param Assignment $assignment Affectation à terminer
     * @param Carbon|null $endTime Date/heure de fin (défaut: maintenant)
     * @param int|null $endMileage Kilométrage de fin (optionnel)
     * @param string|null $notes Notes de terminaison (optionnel)
     * @param int|null $userId ID de l'utilisateur terminant l'affectation
     * @return array Résultat de la terminaison
     * @throws \Exception Si la terminaison échoue
     */
    public function terminateAssignment(
        Assignment $assignment,
        ?Carbon $endTime = null,
        ?int $endMileage = null,
        ?string $notes = null,
        ?int $userId = null
    ): array {
        // 1. VALIDATION PRÉ-TERMINAISON
        if (!$assignment->canBeEnded()) {
            throw new \Exception("L'affectation #{$assignment->id} ne peut pas être terminée dans son état actuel");
        }

        $endTime = $endTime ?? now();
        $userId = $userId ?? auth()->id() ?? 1;

        Log::info('[AssignmentTermination] Début de terminaison', [
            'assignment_id' => $assignment->id,
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'end_time' => $endTime->toISOString(),
            'user_id' => $userId,
        ]);

        // 2. TRANSACTION ATOMIQUE
        return DB::transaction(function () use ($assignment, $endTime, $endMileage, $notes, $userId) {
            $result = [
                'success' => false,
                'assignment_id' => $assignment->id,
                'vehicle_id' => $assignment->vehicle_id,
                'driver_id' => $assignment->driver_id,
                'actions' => [],
            ];

            // 2.1. TERMINER L'AFFECTATION
            $assignment->end_datetime = $endTime;
            $assignment->ended_at = now();
            $assignment->ended_by_user_id = $userId;

            if ($endMileage) {
                $assignment->end_mileage = $endMileage;
            }

            if ($notes) {
                $assignment->notes = $assignment->notes
                    ? $assignment->notes . "\n\n[" . now()->format('d/m/Y H:i') . "] Terminaison: " . $notes
                    : "[" . now()->format('d/m/Y H:i') . "] Terminaison: " . $notes;
            }

            $assignment->save();
            $result['actions'][] = 'assignment_terminated';

            Log::info('[AssignmentTermination] Affectation terminée', [
                'assignment_id' => $assignment->id,
                'ended_at' => $assignment->ended_at->toISOString(),
            ]);

            // 2.2. VÉRIFIER S'IL Y A D'AUTRES AFFECTATIONS ACTIVES
            $hasOtherVehicleAssignment = Assignment::where('vehicle_id', $assignment->vehicle_id)
                ->where('id', '!=', $assignment->id)
                ->whereNull('deleted_at')
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            $hasOtherDriverAssignment = Assignment::where('driver_id', $assignment->driver_id)
                ->where('id', '!=', $assignment->id)
                ->whereNull('deleted_at')
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            // 2.3. LIBÉRER LE VÉHICULE SI AUCUNE AUTRE AFFECTATION
            if (!$hasOtherVehicleAssignment && $assignment->vehicle) {
                $assignment->vehicle->update([
                    'is_available' => true,
                    'current_driver_id' => null,
                    'assignment_status' => 'available',
                    'last_assignment_end' => $endTime,
                ]);

                // Synchroniser le status_id
                $this->statusSync->syncVehicleStatus($assignment->vehicle->fresh());

                $result['actions'][] = 'vehicle_released';

                Log::info('[AssignmentTermination] Véhicule libéré', [
                    'vehicle_id' => $assignment->vehicle_id,
                    'registration' => $assignment->vehicle->registration_plate,
                ]);
            } else {
                $result['actions'][] = 'vehicle_not_released_other_assignment';
                Log::info('[AssignmentTermination] Véhicule NON libéré (autre affectation active)', [
                    'vehicle_id' => $assignment->vehicle_id,
                ]);
            }

            // 2.4. LIBÉRER LE CHAUFFEUR SI AUCUNE AUTRE AFFECTATION
            if (!$hasOtherDriverAssignment && $assignment->driver) {
                $assignment->driver->update([
                    'is_available' => true,
                    'current_vehicle_id' => null,
                    'assignment_status' => 'available',
                    'last_assignment_end' => $endTime,
                ]);

                // Synchroniser le status_id
                $this->statusSync->syncDriverStatus($assignment->driver->fresh());

                $result['actions'][] = 'driver_released';

                Log::info('[AssignmentTermination] Chauffeur libéré', [
                    'driver_id' => $assignment->driver_id,
                    'name' => $assignment->driver->first_name . ' ' . $assignment->driver->last_name,
                ]);
            } else {
                $result['actions'][] = 'driver_not_released_other_assignment';
                Log::info('[AssignmentTermination] Chauffeur NON libéré (autre affectation active)', [
                    'driver_id' => $assignment->driver_id,
                ]);
            }

            // 2.5. METTRE À JOUR LE KILOMÉTRAGE VÉHICULE SI FOURNI
            // 🎯 ENTERPRISE UPGRADE: Utilisation du VehicleMileageService pour traçabilité complète
            if ($endMileage && $assignment->vehicle) {
                try {
                    $mileageResult = $this->mileageService->recordAssignmentEnd(
                        $assignment->vehicle,
                        $endMileage,
                        $assignment->driver_id,
                        $assignment->id,
                        $endTime
                    );

                    $result['actions'] = array_merge($result['actions'], $mileageResult['actions']);
                    $result['mileage_update'] = $mileageResult;

                    Log::info('[AssignmentTermination] Kilométrage de fin enregistré via VehicleMileageService', [
                        'assignment_id' => $assignment->id,
                        'end_mileage' => $endMileage,
                        'mileage_result' => $mileageResult,
                    ]);
                } catch (\Exception $e) {
                    // Logger l'erreur mais ne pas bloquer la terminaison
                    Log::error('[AssignmentTermination] Erreur lors de l\'enregistrement du kilométrage de fin', [
                        'assignment_id' => $assignment->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    
                    // Fallback : mise à jour directe (pour compatibilité)
                    $assignment->vehicle->current_mileage = $endMileage;
                    $assignment->vehicle->save();
                    $result['actions'][] = 'vehicle_mileage_updated_fallback';
                }
            }

            // 2.6. DISPATCHER LES ÉVÉNEMENTS
            try {
                if (class_exists('\App\Events\AssignmentEnded')) {
                    event(new \App\Events\AssignmentEnded($assignment, 'manual', $userId));
                }
                if (class_exists('\App\Events\VehicleStatusChanged') && $assignment->vehicle && in_array('vehicle_released', $result['actions'])) {
                    event(new \App\Events\VehicleStatusChanged($assignment->vehicle, 'available'));
                }
                if (class_exists('\App\Events\DriverStatusChanged') && $assignment->driver && in_array('driver_released', $result['actions'])) {
                    event(new \App\Events\DriverStatusChanged($assignment->driver, 'available'));
                }
                $result['actions'][] = 'events_dispatched';
            } catch (\Exception $e) {
                // Les événements n'existent peut-être pas encore
                Log::debug('[AssignmentTermination] Impossible de dispatcher les événements', [
                    'error' => $e->getMessage(),
                ]);
            }

            $result['success'] = true;

            Log::info('[AssignmentTermination] Terminaison réussie', $result);

            return $result;
        });
    }

    /**
     * Force la libération des ressources d'une affectation
     * (utilisé pour corriger les zombies)
     *
     * @param Assignment $assignment
     * @return array
     */
    public function forceReleaseResources(Assignment $assignment): array
    {
        return DB::transaction(function () use ($assignment) {
            $result = ['actions' => []];

            if ($assignment->vehicle) {
                $assignment->vehicle->update([
                    'is_available' => true,
                    'current_driver_id' => null,
                    'assignment_status' => 'available',
                ]);

                $this->statusSync->syncVehicleStatus($assignment->vehicle->fresh());
                $result['actions'][] = 'vehicle_released';

                Log::info('[AssignmentTermination] Force release - Véhicule libéré', [
                    'vehicle_id' => $assignment->vehicle_id,
                ]);
            }

            if ($assignment->driver) {
                $assignment->driver->update([
                    'is_available' => true,
                    'current_vehicle_id' => null,
                    'assignment_status' => 'available',
                ]);

                $this->statusSync->syncDriverStatus($assignment->driver->fresh());
                $result['actions'][] = 'driver_released';

                Log::info('[AssignmentTermination] Force release - Chauffeur libéré', [
                    'driver_id' => $assignment->driver_id,
                ]);
            }

            return $result;
        });
    }

    /**
     * Détecte les affectations zombies (état incohérent)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function detectZombieAssignments()
    {
        return Assignment::where('status', Assignment::STATUS_ACTIVE)
            ->where(function ($query) {
                $query->whereHas('vehicle', fn($q) => $q->where('is_available', true))
                    ->orWhereHas('driver', fn($q) => $q->where('is_available', true));
            })
            ->with(['vehicle', 'driver'])
            ->get();
    }

    /**
     * Détecte les affectations expirées qui n'ont pas été terminées
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function detectExpiredAssignments()
    {
        return Assignment::whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at')
            ->whereNotIn('status', [Assignment::STATUS_COMPLETED, Assignment::STATUS_CANCELLED])
            ->with(['vehicle', 'driver'])
            ->get();
    }
}
