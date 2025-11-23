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
 * Règles métier ENTERPRISE-GRADE V2.1:
 * - VALIDATION STRICTE: Le kilométrage doit être >= au kilométrage actuel du véhicule
 * - VALIDATION TEMPORELLE STRICTE: La date/heure du relevé doit être STRICTEMENT APRÈS le relevé le plus récent
 * - PROTECTION CONCURRENCE: Lock pessimiste pour éviter les race conditions
 * - COHÉRENCE RÉTROACTIVE: Validation complète pour les insertions rétroactives
 * - Met à jour current_mileage du véhicule UNIQUEMENT si le nouveau relevé
 *   est supérieur au kilométrage actuel du véhicule
 * - Journalise toutes les mises à jour pour l'audit
 * - Gère les cas edge (premier relevé, corrections manuelles)
 *
 * @version 2.1-Enterprise
 */
class VehicleMileageReadingObserver
{
    /**
     * Handle the VehicleMileageReading "creating" event.
     *
     * ✅ VALIDATION STRICTE ENTERPRISE V2.1:
     * Vérifie que le kilométrage ET la date/heure sont valides AVANT création.
     *
     * Validations effectuées:
     * 1. Kilométrage >= current_mileage du véhicule (sauf premier relevé)
     * 2. Date/heure STRICTEMENT APRÈS le relevé le plus récent (pas d'égalité)
     * 3. Pour insertions rétroactives: cohérence avec relevés précédents ET suivants
     * 4. Lock pessimiste pour éviter les race conditions
     *
     * @param VehicleMileageReading $reading
     * @return bool False pour annuler la création
     * @throws \Exception Si validation échoue
     */
    public function creating(VehicleMileageReading $reading): bool
    {
        // ✅ VALIDATION ENTERPRISE: Vérifier le kilométrage avec LOCK
        // Utilise lockForUpdate() pour éviter les race conditions
        $vehicle = Vehicle::where('id', $reading->vehicle_id)
            ->lockForUpdate()
            ->first();

        if (!$vehicle) {
            Log::error('Tentative de création relevé pour véhicule inexistant', [
                'vehicle_id' => $reading->vehicle_id,
                'mileage' => $reading->mileage,
                'organization_id' => $reading->organization_id,
            ]);
            throw new \Exception("Le véhicule n'existe pas.");
        }

        $currentMileage = $vehicle->current_mileage ?? 0;
        $newMileage = $reading->mileage;

        // ✅ RÈGLE MÉTIER STRICTE: Le nouveau kilométrage doit être >= au kilométrage actuel
        // Exception: Autoriser si c'est le premier relevé (current_mileage = 0 ou NULL)
        if ($currentMileage > 0 && $newMileage < $currentMileage) {
            Log::warning('Tentative de création relevé avec kilométrage invalide', [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate,
                'current_mileage' => $currentMileage,
                'attempted_mileage' => $newMileage,
                'difference' => $newMileage - $currentMileage,
                'recorded_by' => $reading->recorded_by_id,
                'organization_id' => $reading->organization_id,
            ]);

            throw new \Exception(sprintf(
                "Le kilométrage saisi (%s km) est inférieur au kilométrage actuel du véhicule %s (%s km). " .
                "Un relevé kilométrique doit toujours être égal ou supérieur au kilométrage précédent.",
                number_format($newMileage, 0, ',', ' '),
                $vehicle->registration_plate,
                number_format($currentMileage, 0, ',', ' ')
            ));
        }

        // ✅ VALIDATION TEMPORELLE STRICTE V2.1: La date/heure du nouveau relevé doit être STRICTEMENT APRÈS le relevé le plus récent
        $mostRecentReading = VehicleMileageReading::where('vehicle_id', $reading->vehicle_id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if ($mostRecentReading) {
            // La date/heure doit être STRICTEMENT supérieure (pas égale)
            if ($reading->recorded_at <= $mostRecentReading->recorded_at) {
                Log::warning('Tentative de création relevé avec date/heure non chronologique', [
                    'vehicle_id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'attempted_datetime' => $reading->recorded_at,
                    'latest_datetime' => $mostRecentReading->recorded_at,
                    'attempted_mileage' => $newMileage,
                    'latest_mileage' => $mostRecentReading->mileage,
                ]);

                throw new \Exception(sprintf(
                    "La date et l'heure du relevé (%s) doivent être strictement postérieures au relevé le plus récent du véhicule %s (%s). " .
                    "Veuillez saisir une date et heure plus récentes.",
                    $reading->recorded_at->format('d/m/Y à H:i'),
                    $vehicle->registration_plate,
                    $mostRecentReading->recorded_at->format('d/m/Y à H:i')
                ));
            }
        }

        // ✅ VALIDATION COHÉRENCE RÉTROACTIVE: Si insertion rétroactive, vérifier la cohérence chronologique du kilométrage
        $futureReadings = VehicleMileageReading::where('vehicle_id', $reading->vehicle_id)
            ->where('recorded_at', '>', $reading->recorded_at)
            ->orderBy('recorded_at', 'asc')
            ->get();

        if ($futureReadings->isNotEmpty()) {
            // Vérifier que le kilométrage saisi est cohérent avec les relevés futurs
            $nextReading = $futureReadings->first();

            if ($newMileage > $nextReading->mileage) {
                Log::warning('Tentative de création relevé rétroactif avec kilométrage incohérent', [
                    'vehicle_id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'attempted_mileage' => $newMileage,
                    'attempted_datetime' => $reading->recorded_at,
                    'next_reading_mileage' => $nextReading->mileage,
                    'next_reading_datetime' => $nextReading->recorded_at,
                ]);

                throw new \Exception(sprintf(
                    "Un relevé kilométrique ultérieur existe déjà avec %s km le %s. " .
                    "Le kilométrage saisi (%s km) est incohérent avec l'historique.",
                    number_format($nextReading->mileage, 0, ',', ' '),
                    $nextReading->recorded_at->format('d/m/Y à H:i'),
                    number_format($newMileage, 0, ',', ' ')
                ));
            }

            // Vérifier également avec le relevé précédent pour s'assurer que le kilométrage est entre les deux
            $previousReading = VehicleMileageReading::where('vehicle_id', $reading->vehicle_id)
                ->where('recorded_at', '<', $reading->recorded_at)
                ->orderBy('recorded_at', 'desc')
                ->first();

            if ($previousReading && $newMileage < $previousReading->mileage) {
                Log::warning('Tentative de création relevé rétroactif inférieur au relevé précédent', [
                    'vehicle_id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'attempted_mileage' => $newMileage,
                    'attempted_datetime' => $reading->recorded_at,
                    'previous_reading_mileage' => $previousReading->mileage,
                    'previous_reading_datetime' => $previousReading->recorded_at,
                ]);

                throw new \Exception(sprintf(
                    "Un relevé kilométrique antérieur existe déjà avec %s km le %s. " .
                    "Le kilométrage saisi (%s km) ne peut pas être inférieur.",
                    number_format($previousReading->mileage, 0, ',', ' '),
                    $previousReading->recorded_at->format('d/m/Y à H:i'),
                    number_format($newMileage, 0, ',', ' ')
                ));
            }
        }

        Log::info('Validation relevé kilométrique réussie', [
            'vehicle_id' => $vehicle->id,
            'registration_plate' => $vehicle->registration_plate,
            'current_mileage' => $currentMileage,
            'new_mileage' => $newMileage,
            'increase' => $newMileage - $currentMileage,
            'recorded_at' => $reading->recorded_at,
        ]);

        return true;
    }

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
