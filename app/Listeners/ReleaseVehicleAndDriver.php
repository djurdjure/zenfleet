<?php

namespace App\Listeners;

use App\Events\AssignmentEnded;
use App\Models\StatusHistory;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔄 LISTENER : LIBÉRATION VÉHICULE + CHAUFFEUR - Enterprise-Grade
 *
 * Lorsqu'une affectation se termine, ce listener :
 * 1. Met le véhicule en statut "Disponible" (si aucune autre affectation active)
 * 2. Met le chauffeur en statut "Disponible" (si aucune autre affectation active)
 * 3. Enregistre les transitions dans StatusHistory
 * 4. Logs structurés pour observabilité
 *
 * IMPORTANT : Vérifie qu'il n'y a pas d'autre affectation active avant de libérer
 *
 * @version 1.0-Enterprise
 */
class ReleaseVehicleAndDriver implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Gestion des erreurs : retry 3 fois avec backoff
     */
    public int $tries = 3;
    public int $backoff = 60; // 1 minute entre chaque retry

    /**
     * Traite l'événement
     *
     * @param AssignmentEnded $event
     * @return void
     */
    public function handle(AssignmentEnded $event): void
    {
        $assignment = $event->assignment;

        Log::info('[ReleaseVehicleAndDriver] Traitement affectation terminée', [
            'assignment_id' => $assignment->id,
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'ended_by' => $event->endedBy,
        ]);

        DB::transaction(function () use ($assignment, $event) {
            $this->releaseVehicleIfAvailable($assignment);
            $this->releaseDriverIfAvailable($assignment);
        });
    }

    /**
     * 🚗 Libère le véhicule si aucune autre affectation active
     */
    protected function releaseVehicleIfAvailable(Assignment $assignment): void
    {
        $vehicle = Vehicle::find($assignment->vehicle_id);

        if (!$vehicle) {
            Log::warning('[ReleaseVehicleAndDriver] Véhicule introuvable', [
                'vehicle_id' => $assignment->vehicle_id,
            ]);
            return;
        }

        // Vérifier s'il existe une autre affectation ACTIVE pour ce véhicule
        $hasActiveAssignment = \App\Models\Assignment::where('vehicle_id', $vehicle->id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->exists();

        if ($hasActiveAssignment) {
            Log::info('[ReleaseVehicleAndDriver] Véhicule a une autre affectation active', [
                'vehicle_id' => $vehicle->id,
            ]);
            return;
        }

        // Récupérer l'ID du statut "Disponible"
        $disponibleStatus = \App\Models\VehicleStatus::where('slug', 'disponible')
            ->orWhere('name', 'Disponible')
            ->first();

        if (!$disponibleStatus) {
            Log::error('[ReleaseVehicleAndDriver] Statut "Disponible" introuvable pour véhicules');
            return;
        }

        $oldStatusId = $vehicle->status_id;

        // Mise à jour du statut
        $vehicle->update([
            'status_id' => $disponibleStatus->id,
            'status_reason' => 'Affectation terminée automatiquement',
        ]);

        // Enregistrer dans l'historique
        StatusHistory::create([
            'entity_type' => 'vehicle',
            'entity_id' => $vehicle->id,
            'from_status_id' => $oldStatusId,
            'to_status_id' => $disponibleStatus->id,
            'changed_by' => null, // Système automatique
            'reason' => "Affectation #{$assignment->id} terminée",
            'organization_id' => $vehicle->organization_id,
        ]);

        Log::info('[ReleaseVehicleAndDriver] Véhicule libéré', [
            'vehicle_id' => $vehicle->id,
            'new_status' => 'Disponible',
        ]);
    }

    /**
     * 👤 Libère le chauffeur si aucune autre affectation active
     */
    protected function releaseDriverIfAvailable(Assignment $assignment): void
    {
        $driver = Driver::find($assignment->driver_id);

        if (!$driver) {
            Log::warning('[ReleaseVehicleAndDriver] Chauffeur introuvable', [
                'driver_id' => $assignment->driver_id,
            ]);
            return;
        }

        // Vérifier s'il existe une autre affectation ACTIVE pour ce chauffeur
        $hasActiveAssignment = \App\Models\Assignment::where('driver_id', $driver->id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->exists();

        if ($hasActiveAssignment) {
            Log::info('[ReleaseVehicleAndDriver] Chauffeur a une autre affectation active', [
                'driver_id' => $driver->id,
            ]);
            return;
        }

        // Récupérer l'ID du statut "Disponible"
        $disponibleStatus = \App\Models\DriverStatus::where('slug', 'disponible')
            ->orWhere('name', 'Disponible')
            ->first();

        if (!$disponibleStatus) {
            Log::error('[ReleaseVehicleAndDriver] Statut "Disponible" introuvable pour chauffeurs');
            return;
        }

        $oldStatusId = $driver->status_id;

        // Mise à jour du statut
        $driver->update([
            'status_id' => $disponibleStatus->id,
            'status_reason' => 'Affectation terminée automatiquement',
        ]);

        // Enregistrer dans l'historique
        StatusHistory::create([
            'entity_type' => 'driver',
            'entity_id' => $driver->id,
            'from_status_id' => $oldStatusId,
            'to_status_id' => $disponibleStatus->id,
            'changed_by' => null, // Système automatique
            'reason' => "Affectation #{$assignment->id} terminée",
            'organization_id' => $driver->organization_id,
        ]);

        Log::info('[ReleaseVehicleAndDriver] Chauffeur libéré', [
            'driver_id' => $driver->id,
            'new_status' => 'Disponible',
        ]);
    }

    /**
     * Gérer les échecs du job
     */
    public function failed(AssignmentEnded $event, \Throwable $exception): void
    {
        Log::error('[ReleaseVehicleAndDriver] ÉCHEC après 3 tentatives', [
            'assignment_id' => $event->assignment->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
