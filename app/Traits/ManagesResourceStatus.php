<?php

namespace App\Traits;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Trait Enterprise-Grade pour la gestion intelligente des statuts de ressources.
 * Assure qu'une ressource n'est libérée que si elle n'a plus aucune affectation active ou planifiée.
 */
trait ManagesResourceStatus
{
    /**
     * Libère la ressource (Driver ou Vehicle) si elle n'a plus d'affectation active/planifiée.
     */
    protected function releaseResource(Model $resource): void
    {
        // Déterminer la clé étrangère (driver_id ou vehicle_id)
        $foreignKey = ($resource instanceof Vehicle) ? 'vehicle_id' : 'driver_id';

        // Vérifier s'il existe une autre affectation active ou planifiée pour cette ressource
        $hasOtherAssignment = Assignment::where($foreignKey, $resource->id)
            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
            ->whereNull('deleted_at')
            ->exists();

        if (!$hasOtherAssignment) {
            // Libération effective
            $updateData = [
                'is_available' => true,
                'assignment_status' => 'available',
            ];

            if ($resource instanceof Vehicle) {
                // Statut métier "Parking" pour le véhicule
                $statusSync = app(\App\Services\ResourceStatusSynchronizer::class);
                $parkingStatusId = $statusSync->resolveVehicleStatusIdForAvailable($resource->organization_id);
                if ($parkingStatusId) {
                    $updateData['status_id'] = $parkingStatusId;
                } else {
                    Log::warning('[ManagesResourceStatus] Statut PARKING introuvable - status_id non modifié', [
                        'vehicle_id' => $resource->id,
                        'organization_id' => $resource->organization_id,
                    ]);
                }
                $updateData['current_driver_id'] = null;
            } elseif ($resource instanceof Driver) {
                // Statut métier "Disponible" pour le chauffeur
                $statusSync = app(\App\Services\ResourceStatusSynchronizer::class);
                $availableStatusId = $statusSync->resolveDriverStatusIdForAvailable($resource->organization_id);
                if ($availableStatusId) {
                    $updateData['status_id'] = $availableStatusId;
                } else {
                    Log::warning('[ManagesResourceStatus] Statut DISPONIBLE introuvable - status_id non modifié', [
                        'driver_id' => $resource->id,
                        'organization_id' => $resource->organization_id,
                    ]);
                }
                $updateData['current_vehicle_id'] = null;
            }

            $resource->update($updateData);
        }
    }
}
