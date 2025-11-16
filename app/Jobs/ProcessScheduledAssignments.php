<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Events\AssignmentActivated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🚀 JOB ENTERPRISE-GRADE : Gère la transition Scheduled -> Active en temps réel.
 * Exécuté toutes les minutes pour garantir une synchronisation quasi-instantanée.
 */
class ProcessScheduledAssignments implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;
    public $uniqueFor = 60;

    public function handle(): void
    {
        $now = now();
        $count = 0;

        // 1. Sélectionner les affectations qui DOIVENT être actives
        $assignmentsToActivate = Assignment::with(['vehicle', 'driver'])
            ->where('status', Assignment::STATUS_SCHEDULED)
            ->where('start_datetime', '<=', $now)
            ->whereNull('deleted_at')
            ->limit(50)
            ->get();

        if ($assignmentsToActivate->isEmpty()) {
            return;
        }

        Log::info('[ProcessScheduledAssignments] Affectations à activer détectées', ['count' => $assignmentsToActivate->count()]);

        foreach ($assignmentsToActivate as $assignment) {
            // Utiliser une transaction atomique pour garantir la cohérence
            DB::transaction(function () use ($assignment, &$count) {
                // 1. Mettre à jour le statut de l'affectation
                $assignment->update(['status' => Assignment::STATUS_ACTIVE]);

                // 2. Mettre à jour le statut du véhicule
                $this->updateVehicleStatus($assignment->vehicle, $assignment->driver_id);

                // 3. Mettre à jour le statut du chauffeur
                $this->updateDriverStatus($assignment->driver, $assignment->vehicle_id);

                // 4. Déclencher l'événement
                event(new AssignmentActivated($assignment, 'automatic', null, [
                    'reason' => 'scheduled_start_reached',
                    'processed_by' => 'ProcessScheduledAssignments'
                ]));

                $count++;
            });
        }

        Log::info('[ProcessScheduledAssignments] Synchronisation terminée', ['activated_count' => $count]);
    }

    /**
     * Met à jour le statut du véhicule à "assigned" (En mission).
     */
    private function updateVehicleStatus(Vehicle $vehicle, int $driverId): void
    {
        // Récupérer l'ID du statut métier "En mission" (à adapter si l'ID 8 n'est pas garanti)
        $assignedStatusId = DB::table('vehicle_statuses')->where('name', 'En mission')->value('id') ?? 8;

        $vehicle->update([
            'status_id' => $assignedStatusId,
            'is_available' => false,
            'assignment_status' => 'assigned',
            'current_driver_id' => $driverId, // Lien direct pour cohérence immédiate
        ]);
    }

    /**
     * Met à jour le statut du chauffeur à "assigned" (En mission).
     */
    private function updateDriverStatus(Driver $driver, int $vehicleId): void
    {
        // Récupérer l'ID du statut métier "En mission" (à adapter si l'ID 8 n'est pas garanti)
        $assignedStatusId = DB::table('driver_statuses')->where('name', 'En mission')->value('id') ?? 8;

        $driver->update([
            'status_id' => $assignedStatusId,
            'is_available' => false,
            'assignment_status' => 'assigned',
            'current_vehicle_id' => $vehicleId, // Lien direct pour cohérence immédiate
        ]);
    }
}