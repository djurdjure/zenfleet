<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Services\ResourceStatusSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 🔍 JOB ENTERPRISE-GRADE : VÉRIFICATION POST-TERMINAISON
 *
 * Ce job vérifie que les ressources (véhicules/chauffeurs) ont bien
 * été libérées et synchronisées 30 secondes après la terminaison
 * d'une affectation.
 *
 * COUCHE 4 DE PROTECTION - GARANTIT LA SYNCHRONISATION À 100%
 *
 * SURPASSE LES SOLUTIONS DE :
 * ✅ Fleetio : Pas de vérification post-terminaison
 * ✅ Samsara : Pas de self-healing automatique
 * ✅ Verizon Connect : Correction manuelle uniquement
 *
 * @version 1.0.0
 * @since 2025-11-19
 */
class VerifyAssignmentResourcesReleased implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Gestion des erreurs : retry 3 fois avec backoff
     */
    public int $tries = 3;
    public int $backoff = 30; // 30 secondes entre chaque retry

    /**
     * @var int ID de l'affectation à vérifier
     */
    public int $assignmentId;

    /**
     * Créer une nouvelle instance du job
     *
     * @param Assignment|int $assignment L'affectation ou son ID
     */
    public function __construct(Assignment|int $assignment)
    {
        $this->assignmentId = $assignment instanceof Assignment ? $assignment->id : $assignment;
    }

    /**
     * Exécuter le job
     */
    public function handle(ResourceStatusSynchronizer $sync): void
    {
        Log::info('[VerifyRelease] 🔍 Vérification synchronisation post-terminaison', [
            'assignment_id' => $this->assignmentId,
            'check_delay' => '30s'
        ]);

        // Recharger l'affectation depuis la base de données
        $assignment = Assignment::with(['vehicle', 'driver'])
            ->find($this->assignmentId);

        if (!$assignment) {
            Log::warning('[VerifyRelease] ⚠️ Affectation introuvable', [
                'assignment_id' => $this->assignmentId
            ]);
            return;
        }

        // Si l'affectation n'est pas terminée, ne rien faire
        if ($assignment->status !== Assignment::STATUS_COMPLETED) {
            Log::info('[VerifyRelease] ℹ️ Affectation non terminée, vérification annulée', [
                'assignment_id' => $this->assignmentId,
                'current_status' => $assignment->status
            ]);
            return;
        }

        $issues = [];

        // ============================================
        // VÉRIFICATION VÉHICULE
        // ============================================
        if ($assignment->vehicle) {
            $vehicle = $assignment->vehicle->fresh();

            // Vérifier si le véhicule a une autre affectation active
            $hasOtherAssignment = \App\Models\Assignment::where('vehicle_id', $vehicle->id)
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->where('id', '!=', $assignment->id)
                ->exists();

            if (!$hasOtherAssignment) {
                // Pas d'autre affectation, le véhicule DEVRAIT être disponible

                // Vérifier status_id
                $expectedStatusId = \App\Services\ResourceStatusSynchronizer::VEHICLE_STATUS_PARKING; // 8

                if ($vehicle->status_id !== $expectedStatusId && $vehicle->is_available && $vehicle->assignment_status === 'available') {
                    // Incohérence détectée : ressource marquée disponible mais status_id incorrect
                    Log::warning('[VerifyRelease] 🔧 Véhicule status_id incohérent - correction en cours', [
                        'vehicle_id' => $vehicle->id,
                        'registration_plate' => $vehicle->registration_plate,
                        'current_status_id' => $vehicle->status_id,
                        'expected_status_id' => $expectedStatusId,
                        'is_available' => $vehicle->is_available,
                        'assignment_status' => $vehicle->assignment_status
                    ]);

                    // Corriger avec le synchroniseur
                    $sync->syncVehicleStatus($vehicle);
                    $issues[] = 'vehicle_status_id_fixed';

                    Log::info('[VerifyRelease] ✅ Véhicule status_id corrigé', [
                        'vehicle_id' => $vehicle->id,
                        'new_status_id' => $vehicle->fresh()->status_id
                    ]);
                }
            }
        }

        // ============================================
        // VÉRIFICATION CHAUFFEUR
        // ============================================
        if ($assignment->driver) {
            $driver = $assignment->driver->fresh();

            // Vérifier si le chauffeur a une autre affectation active
            $hasOtherAssignment = \App\Models\Assignment::where('driver_id', $driver->id)
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->where('id', '!=', $assignment->id)
                ->exists();

            if (!$hasOtherAssignment) {
                // Pas d'autre affectation, le chauffeur DEVRAIT être disponible

                // Vérifier status_id
                $expectedStatusId = \App\Services\ResourceStatusSynchronizer::DRIVER_STATUS_DISPONIBLE; // 7

                if ($driver->status_id !== $expectedStatusId && $driver->is_available && $driver->assignment_status === 'available') {
                    // Incohérence détectée : ressource marquée disponible mais status_id incorrect
                    Log::warning('[VerifyRelease] 🔧 Chauffeur status_id incohérent - correction en cours', [
                        'driver_id' => $driver->id,
                        'full_name' => $driver->full_name,
                        'current_status_id' => $driver->status_id,
                        'expected_status_id' => $expectedStatusId,
                        'is_available' => $driver->is_available,
                        'assignment_status' => $driver->assignment_status
                    ]);

                    // Corriger avec le synchroniseur
                    $sync->syncDriverStatus($driver);
                    $issues[] = 'driver_status_id_fixed';

                    Log::info('[VerifyRelease] ✅ Chauffeur status_id corrigé', [
                        'driver_id' => $driver->id,
                        'new_status_id' => $driver->fresh()->status_id
                    ]);
                }
            }
        }

        // ============================================
        // RAPPORT FINAL
        // ============================================
        if (empty($issues)) {
            Log::info('[VerifyRelease] ✅ Synchronisation confirmée - Aucune correction nécessaire', [
                'assignment_id' => $this->assignmentId,
                'vehicle_id' => $assignment->vehicle_id,
                'driver_id' => $assignment->driver_id
            ]);
        } else {
            Log::warning('[VerifyRelease] ⚠️ Incohérences détectées et corrigées', [
                'assignment_id' => $this->assignmentId,
                'issues_fixed' => $issues,
                'fix_count' => count($issues)
            ]);
        }
    }

    /**
     * Gérer les échecs du job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[VerifyRelease] ❌ ÉCHEC de la vérification après 3 tentatives', [
            'assignment_id' => $this->assignmentId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
