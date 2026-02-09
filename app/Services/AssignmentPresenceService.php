<?php

namespace App\Services;

use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 SERVICE ENTERPRISE-GRADE : ASSIGNMENT PRESENCE
 *
 * Objectif :
 * - Faire de l'affectation (assignments) la source de vérité.
 * - Synchroniser les champs dérivés (is_available, assignment_status, current_*_id).
 * - Ne jamais modifier les status_id (statuts opérationnels).
 *
 * @version 1.0.0-Enterprise
 * @since 2026-02-07
 */
class AssignmentPresenceService
{
    /**
     * Synchronise la présence pour un véhicule et un chauffeur liés à une affectation.
     */
    public function syncForAssignment(Assignment $assignment, ?Carbon $referenceTime = null, ?Carbon $lastAssignmentEnd = null): void
    {
        $this->syncVehicle($assignment->vehicle_id, $referenceTime, $lastAssignmentEnd);
        $this->syncDriver($assignment->driver_id, $referenceTime, $lastAssignmentEnd);
    }

    /**
     * Synchronise la présence d'un véhicule à un instant donné.
     */
    public function syncVehicle(?int $vehicleId, ?Carbon $referenceTime = null, ?Carbon $lastAssignmentEnd = null): void
    {
        if (!$vehicleId) {
            return;
        }

        $referenceTime = $referenceTime ?? now();
        $active = $this->findActiveAssignmentForVehicle($vehicleId, $referenceTime);

        if ($active) {
            $this->updateVehiclePresence($vehicleId, [
                'is_available' => false,
                'assignment_status' => 'assigned',
                'current_driver_id' => $active->driver_id,
                'updated_at' => now(),
            ]);
            return;
        }

        $update = [
            'is_available' => true,
            'assignment_status' => 'available',
            'current_driver_id' => null,
            'updated_at' => now(),
        ];
        if ($lastAssignmentEnd) {
            $update['last_assignment_end'] = $lastAssignmentEnd;
        }

        $this->updateVehiclePresence($vehicleId, $update);
    }

    /**
     * Synchronise la présence d'un chauffeur à un instant donné.
     */
    public function syncDriver(?int $driverId, ?Carbon $referenceTime = null, ?Carbon $lastAssignmentEnd = null): void
    {
        if (!$driverId) {
            return;
        }

        $referenceTime = $referenceTime ?? now();
        $active = $this->findActiveAssignmentForDriver($driverId, $referenceTime);

        if ($active) {
            $this->updateDriverPresence($driverId, [
                'is_available' => false,
                'assignment_status' => 'assigned',
                'current_vehicle_id' => $active->vehicle_id,
                'updated_at' => now(),
            ]);
            return;
        }

        $update = [
            'is_available' => true,
            'assignment_status' => 'available',
            'current_vehicle_id' => null,
            'updated_at' => now(),
        ];
        if ($lastAssignmentEnd) {
            $update['last_assignment_end'] = $lastAssignmentEnd;
        }

        $this->updateDriverPresence($driverId, $update);
    }

    /**
     * Synchronise toutes les ressources (utile pour jobs/commandes).
     */
    public function syncAll(?int $organizationId = null): array
    {
        $referenceTime = now();
        $vehicleQuery = DB::table('vehicles')->select('id');
        $driverQuery = DB::table('drivers')->select('id');

        if ($organizationId) {
            $vehicleQuery->where('organization_id', $organizationId);
            $driverQuery->where('organization_id', $organizationId);
        }

        $vehicleIds = $vehicleQuery->pluck('id');
        $driverIds = $driverQuery->pluck('id');

        foreach ($vehicleIds as $vehicleId) {
            $this->syncVehicle($vehicleId, $referenceTime);
        }

        foreach ($driverIds as $driverId) {
            $this->syncDriver($driverId, $referenceTime);
        }

        return [
            'vehicles_synced' => $vehicleIds->count(),
            'drivers_synced' => $driverIds->count(),
        ];
    }

    private function findActiveAssignmentForVehicle(int $vehicleId, Carbon $referenceTime): ?object
    {
        return DB::table('assignments')
            ->select('id', 'driver_id')
            ->where('vehicle_id', $vehicleId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where('start_datetime', '<=', $referenceTime)
            ->where(function ($q) use ($referenceTime) {
                $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', $referenceTime);
            })
            ->orderByDesc('start_datetime')
            ->orderByDesc('id')
            ->first();
    }

    private function findActiveAssignmentForDriver(int $driverId, Carbon $referenceTime): ?object
    {
        return DB::table('assignments')
            ->select('id', 'vehicle_id')
            ->where('driver_id', $driverId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where('start_datetime', '<=', $referenceTime)
            ->where(function ($q) use ($referenceTime) {
                $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', $referenceTime);
            })
            ->orderByDesc('start_datetime')
            ->orderByDesc('id')
            ->first();
    }

    private function updateVehiclePresence(int $vehicleId, array $data): void
    {
        DB::table('vehicles')->where('id', $vehicleId)->update($data);
    }

    private function updateDriverPresence(int $driverId, array $data): void
    {
        DB::table('drivers')->where('id', $driverId)->update($data);
    }
}
