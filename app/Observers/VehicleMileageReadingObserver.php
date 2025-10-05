<?php

namespace App\Observers;

use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;

/**
 * VehicleMileageReadingObserver
 *
 * Observer pour gérer automatiquement la mise à jour du kilométrage actuel
 * du véhicule lorsqu'un nouveau relevé est créé ou mis à jour.
 *
 * Règles métier:
 * - Met à jour current_mileage du véhicule UNIQUEMENT si le nouveau relevé
 *   est supérieur au kilométrage actuel du véhicule
 * - Journalise toutes les mises à jour pour l'audit
 * - Gère les cas edge (premier relevé, corrections manuelles)
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReadingObserver
{
    /**
     * Handle the VehicleMileageReading "created" event.
     *
     * Mise à jour automatique du current_mileage lors de la création d'un relevé.
     */
    public function created(VehicleMileageReading $reading): void
    {
        $this->updateVehicleMileage($reading, 'created');
    }

    /**
     * Handle the VehicleMileageReading "updated" event.
     *
     * Mise à jour automatique du current_mileage lors de la modification d'un relevé.
     */
    public function updated(VehicleMileageReading $reading): void
    {
        // Vérifier si le kilométrage a changé
        if ($reading->isDirty('mileage')) {
            $this->updateVehicleMileage($reading, 'updated');
        }
    }

    /**
     * Handle the VehicleMileageReading "deleted" event.
     *
     * Recalculer le current_mileage si le relevé supprimé était le plus récent.
     */
    public function deleted(VehicleMileageReading $reading): void
    {
        $this->recalculateVehicleMileage($reading->vehicle_id);
    }

    /**
     * Handle the VehicleMileageReading "restored" event.
     *
     * Re-vérifier le current_mileage après restauration.
     */
    public function restored(VehicleMileageReading $reading): void
    {
        $this->updateVehicleMileage($reading, 'restored');
    }

    /**
     * Mettre à jour le kilométrage actuel du véhicule.
     *
     * RÈGLE MÉTIER: Met à jour UNIQUEMENT si le nouveau kilométrage est supérieur.
     *
     * @param VehicleMileageReading $reading Le relevé kilométrique
     * @param string $action L'action déclenchant la mise à jour (pour logging)
     */
    protected function updateVehicleMileage(VehicleMileageReading $reading, string $action): void
    {
        $vehicle = Vehicle::find($reading->vehicle_id);

        if (!$vehicle) {
            Log::warning("Vehicle not found for mileage reading", [
                'reading_id' => $reading->id,
                'vehicle_id' => $reading->vehicle_id,
            ]);
            return;
        }

        $currentMileage = $vehicle->current_mileage ?? 0;
        $newMileage = $reading->mileage;

        // RÈGLE: Mettre à jour UNIQUEMENT si le nouveau kilométrage est supérieur
        if ($newMileage > $currentMileage) {
            $oldMileage = $vehicle->current_mileage;

            // Utilise la méthode dédiée du modèle Vehicle pour synchroniser le kilométrage
            $vehicle->syncCurrentMileageFromReading($newMileage);

            // Audit log
            Log::info("Vehicle mileage updated", [
                'action' => $action,
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_number,
                'old_mileage' => $oldMileage,
                'new_mileage' => $newMileage,
                'reading_id' => $reading->id,
                'reading_method' => $reading->recording_method,
                'recorded_at' => $reading->recorded_at->toIso8601String(),
                'organization_id' => $reading->organization_id,
            ]);
        } else {
            // Log pour diagnostic (pas une erreur, c'est normal pour les corrections manuelles)
            Log::debug("Vehicle mileage not updated (new reading not greater)", [
                'action' => $action,
                'vehicle_id' => $vehicle->id,
                'current_vehicle_mileage' => $currentMileage,
                'reading_mileage' => $newMileage,
                'reading_id' => $reading->id,
                'is_manual' => $reading->is_manual,
            ]);
        }
    }

    /**
     * Recalculer le kilométrage actuel du véhicule.
     *
     * Utilisé après suppression d'un relevé pour s'assurer que
     * current_mileage reflète toujours le relevé le plus récent.
     *
     * @param int $vehicleId ID du véhicule
     */
    protected function recalculateVehicleMileage(int $vehicleId): void
    {
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            return;
        }

        // Trouver le relevé le plus récent restant
        $latestReading = VehicleMileageReading::where('vehicle_id', $vehicleId)
            ->latest('recorded_at')
            ->first();

        $newMileage = $latestReading ? $latestReading->mileage : 0;

        if ($vehicle->current_mileage != $newMileage) {
            $oldMileage = $vehicle->current_mileage;

            // Utilise la méthode dédiée du modèle Vehicle pour synchroniser le kilométrage
            $vehicle->syncCurrentMileageFromReading($newMileage);

            Log::info("Vehicle mileage recalculated after deletion", [
                'vehicle_id' => $vehicle->id,
                'old_mileage' => $oldMileage,
                'new_mileage' => $newMileage,
                'latest_reading_id' => $latestReading?->id,
            ]);
        }
    }
}
