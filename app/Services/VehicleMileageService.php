<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Models\MileageHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🎯 SERVICE ENTERPRISE-GRADE : GESTION DU KILOMÉTRAGE VÉHICULE
 *
 * Ce service centralise toute la logique de gestion du kilométrage des véhicules,
 * garantissant la cohérence des données entre toutes les tables concernées.
 *
 * RESPONSABILITÉS :
 * - Enregistrer les relevés kilométriques dans vehicle_mileage_readings
 * - Mettre à jour le current_mileage du véhicule de manière atomique
 * - Maintenir la compatibilité avec l'ancien système (mileage_histories)
 * - Valider la cohérence des données (pas de kilométrage décroissant)
 * - Gérer les relevés manuels et automatiques
 * - Traçabilité complète (audit trail)
 *
 * PRINCIPES :
 * - Transaction ACID garantie
 * - Single source of truth : vehicle_mileage_readings
 * - Validation stricte de la cohérence
 * - Rollback automatique en cas d'erreur
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Architecture Team
 * @date 2025-11-16
 */
class VehicleMileageService
{
    /**
     * 📝 Enregistre un relevé kilométrique de manière atomique
     *
     * Cette méthode est la SEULE façon recommandée d'enregistrer un kilométrage.
     * Elle garantit la cohérence entre toutes les tables concernées.
     *
     * FONCTIONNALITÉS ENTERPRISE :
     * - Validation de cohérence (kilométrage croissant)
     * - Mise à jour atomique du véhicule
     * - Double enregistrement (nouveau + ancien système)
     * - Support relevés manuels ET automatiques
     * - Traçabilité complète
     *
     * @param Vehicle $vehicle Véhicule concerné
     * @param int $mileage Valeur du kilométrage en km
     * @param string $type Type de relevé (assignment_start|assignment_end|manual|automatic)
     * @param array $context Contexte additionnel (driver_id, assignment_id, notes, etc.)
     * @return array Résultat de l'enregistrement
     * @throws \Exception Si validation échoue ou erreur technique
     */
    public function recordMileage(
        Vehicle $vehicle,
        int $mileage,
        string $type = 'manual',
        array $context = []
    ): array {
        // Validation initiale
        if ($mileage < 0) {
            throw new \InvalidArgumentException("Le kilométrage ne peut pas être négatif : {$mileage} km");
        }

        // Récupérer le dernier relevé pour validation
        $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        // Validation de cohérence : le kilométrage ne peut pas diminuer
        // (sauf pour les corrections manuelles explicites)
        $allowDecrease = $context['allow_decrease'] ?? false;
        
        if ($lastReading && $mileage < $lastReading->mileage && !$allowDecrease) {
            throw new \InvalidArgumentException(
                "Le kilométrage ({$mileage} km) ne peut pas être inférieur au dernier relevé ({$lastReading->mileage} km). " .
                "Dernier relevé enregistré le " . $lastReading->recorded_at->format('d/m/Y à H:i')
            );
        }

        // Vérifier également avec le current_mileage du véhicule
        if ($vehicle->current_mileage && $mileage < $vehicle->current_mileage && !$allowDecrease) {
            throw new \InvalidArgumentException(
                "Le kilométrage ({$mileage} km) ne peut pas être inférieur au kilométrage actuel du véhicule ({$vehicle->current_mileage} km)"
            );
        }

        Log::info('[VehicleMileageService] Début enregistrement kilométrage', [
            'vehicle_id' => $vehicle->id,
            'registration' => $vehicle->registration_plate,
            'previous_mileage' => $vehicle->current_mileage,
            'new_mileage' => $mileage,
            'difference' => $mileage - ($vehicle->current_mileage ?? 0),
            'type' => $type,
            'context' => $context,
        ]);

        // Transaction atomique
        return DB::transaction(function () use ($vehicle, $mileage, $type, $context, $lastReading) {
            $result = [
                'success' => false,
                'vehicle_id' => $vehicle->id,
                'previous_mileage' => $vehicle->current_mileage,
                'new_mileage' => $mileage,
                'difference' => $mileage - ($vehicle->current_mileage ?? 0),
                'actions' => [],
            ];

            $recordedAt = $context['recorded_at'] ?? now();
            $recordedBy = $context['recorded_by'] ?? auth()->id();
            $notes = $context['notes'] ?? null;
            $organizationId = $vehicle->organization_id ?? auth()->user()->organization_id;

            // Déterminer la méthode d'enregistrement
            $recordingMethod = in_array($type, ['assignment_start', 'assignment_end', 'automatic']) 
                ? 'automatic' 
                : 'manual';

            // 1. CRÉER L'ENTRÉE DANS vehicle_mileage_readings (système principal)
            $mileageReading = VehicleMileageReading::create([
                'organization_id' => $organizationId,
                'vehicle_id' => $vehicle->id,
                'recorded_at' => $recordedAt,
                'mileage' => $mileage,
                'recorded_by_id' => $recordingMethod === 'manual' ? $recordedBy : null,
                'recording_method' => $recordingMethod,
                'notes' => $notes ?? $this->generateNotes($type, $context),
            ]);

            $result['actions'][] = 'mileage_reading_created';
            $result['mileage_reading_id'] = $mileageReading->id;

            Log::info('[VehicleMileageService] Relevé créé dans vehicle_mileage_readings', [
                'mileage_reading_id' => $mileageReading->id,
                'vehicle_id' => $vehicle->id,
                'mileage' => $mileage,
            ]);

            // 2. METTRE À JOUR LE VÉHICULE (current_mileage)
            // Ne mettre à jour que si le nouveau kilométrage est supérieur
            if ($mileage > ($vehicle->current_mileage ?? 0)) {
                $vehicle->current_mileage = $mileage;
                $vehicle->save();

                $result['actions'][] = 'vehicle_mileage_updated';

                Log::info('[VehicleMileageService] Kilométrage véhicule mis à jour', [
                    'vehicle_id' => $vehicle->id,
                    'registration' => $vehicle->registration_plate,
                    'previous_mileage' => $result['previous_mileage'],
                    'new_mileage' => $mileage,
                    'difference' => $result['difference'],
                ]);
            } else {
                $result['actions'][] = 'vehicle_mileage_unchanged';
                Log::debug('[VehicleMileageService] Kilométrage véhicule inchangé (nouveau <= actuel)');
            }

            // 3. CRÉER L'ENTRÉE DANS mileage_histories (compatibilité ancien système)
            try {
                $mileageHistory = MileageHistory::create([
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $context['driver_id'] ?? null,
                    'assignment_id' => $context['assignment_id'] ?? null,
                    'mileage_value' => $mileage,
                    'recorded_at' => $recordedAt,
                    'type' => $type,
                    'notes' => $notes ?? $this->generateNotes($type, $context),
                    'created_by' => $recordedBy,
                    'organization_id' => $organizationId,
                ]);

                $result['actions'][] = 'mileage_history_created';
                $result['mileage_history_id'] = $mileageHistory->id;

                Log::info('[VehicleMileageService] Entrée créée dans mileage_histories (compatibilité)', [
                    'mileage_history_id' => $mileageHistory->id,
                ]);
            } catch (\Exception $e) {
                // Ne pas bloquer si la table mileage_histories n'existe pas encore
                Log::warning('[VehicleMileageService] Impossible de créer l\'entrée dans mileage_histories', [
                    'error' => $e->getMessage(),
                ]);
                $result['actions'][] = 'mileage_history_skipped';
            }

            $result['success'] = true;

            Log::info('[VehicleMileageService] Enregistrement kilométrage réussi', $result);

            return $result;
        });
    }

    /**
     * 🚀 Enregistre le kilométrage de début d'affectation
     *
     * Appelé lors de la création d'une affectation pour tracer le kilométrage initial.
     *
     * @param Vehicle $vehicle
     * @param int $mileage
     * @param int $driverId
     * @param int $assignmentId
     * @param Carbon|null $recordedAt
     * @return array
     */
    public function recordAssignmentStart(
        Vehicle $vehicle,
        int $mileage,
        int $driverId,
        int $assignmentId,
        ?Carbon $recordedAt = null
    ): array {
        return $this->recordMileage($vehicle, $mileage, 'assignment_start', [
            'driver_id' => $driverId,
            'assignment_id' => $assignmentId,
            'recorded_at' => $recordedAt ?? now(),
            'recorded_by' => auth()->id() ?? 1,
            'notes' => "Kilométrage de début d'affectation #{$assignmentId}",
        ]);
    }

    /**
     * 🏁 Enregistre le kilométrage de fin d'affectation
     *
     * Appelé lors de la terminaison d'une affectation pour tracer le kilométrage final.
     *
     * @param Vehicle $vehicle
     * @param int $mileage
     * @param int $driverId
     * @param int $assignmentId
     * @param Carbon|null $recordedAt
     * @return array
     */
    public function recordAssignmentEnd(
        Vehicle $vehicle,
        int $mileage,
        int $driverId,
        int $assignmentId,
        ?Carbon $recordedAt = null
    ): array {
        return $this->recordMileage($vehicle, $mileage, 'assignment_end', [
            'driver_id' => $driverId,
            'assignment_id' => $assignmentId,
            'recorded_at' => $recordedAt ?? now(),
            'recorded_by' => auth()->id() ?? 1,
            'notes' => "Kilométrage de fin d'affectation #{$assignmentId}",
        ]);
    }

    /**
     * 📝 Enregistre un relevé manuel
     *
     * Utilisé pour les relevés kilométriques saisis manuellement par les utilisateurs.
     *
     * @param Vehicle $vehicle
     * @param int $mileage
     * @param string|null $notes
     * @param Carbon|null $recordedAt
     * @return array
     */
    public function recordManualReading(
        Vehicle $vehicle,
        int $mileage,
        ?string $notes = null,
        ?Carbon $recordedAt = null
    ): array {
        return $this->recordMileage($vehicle, $mileage, 'manual', [
            'recorded_at' => $recordedAt ?? now(),
            'recorded_by' => auth()->id(),
            'notes' => $notes ?? 'Relevé manuel',
        ]);
    }

    /**
     * 🔄 Synchronise le kilométrage du véhicule avec le dernier relevé
     *
     * Utilisé pour corriger les incohérences ou après une migration de données.
     *
     * @param Vehicle $vehicle
     * @return array
     */
    public function syncVehicleMileage(Vehicle $vehicle): array
    {
        $lastReading = VehicleMileageReading::where('vehicle_id', $vehicle->id)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (!$lastReading) {
            return [
                'success' => false,
                'message' => 'Aucun relevé trouvé pour ce véhicule',
                'vehicle_id' => $vehicle->id,
            ];
        }

        $previousMileage = $vehicle->current_mileage;

        if ($lastReading->mileage !== $vehicle->current_mileage) {
            $vehicle->current_mileage = $lastReading->mileage;
            $vehicle->save();

            Log::info('[VehicleMileageService] Kilométrage véhicule synchronisé', [
                'vehicle_id' => $vehicle->id,
                'previous_mileage' => $previousMileage,
                'new_mileage' => $lastReading->mileage,
                'last_reading_date' => $lastReading->recorded_at->toDateTimeString(),
            ]);

            return [
                'success' => true,
                'message' => 'Kilométrage synchronisé avec succès',
                'vehicle_id' => $vehicle->id,
                'previous_mileage' => $previousMileage,
                'new_mileage' => $lastReading->mileage,
                'difference' => $lastReading->mileage - $previousMileage,
            ];
        }

        return [
            'success' => true,
            'message' => 'Kilométrage déjà synchronisé',
            'vehicle_id' => $vehicle->id,
            'current_mileage' => $vehicle->current_mileage,
        ];
    }

    /**
     * 📊 Obtient l'historique des relevés pour un véhicule
     *
     * @param Vehicle $vehicle
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMileageHistory(Vehicle $vehicle, int $limit = 50)
    {
        return VehicleMileageReading::where('vehicle_id', $vehicle->id)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->with(['recordedBy'])
            ->get();
    }

    /**
     * 🔍 Détecte les incohérences de kilométrage
     *
     * Identifie les véhicules dont le current_mileage ne correspond pas
     * au dernier relevé enregistré.
     *
     * @param int|null $organizationId
     * @return \Illuminate\Support\Collection
     */
    public function detectInconsistencies(?int $organizationId = null)
    {
        $query = Vehicle::query();

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $vehicles = $query->with(['mileageReadings' => function ($q) {
            $q->orderBy('recorded_at', 'desc')->limit(1);
        }])->get();

        $inconsistencies = collect();

        foreach ($vehicles as $vehicle) {
            $lastReading = $vehicle->mileageReadings->first();

            if ($lastReading && $lastReading->mileage !== $vehicle->current_mileage) {
                $inconsistencies->push([
                    'vehicle_id' => $vehicle->id,
                    'registration_plate' => $vehicle->registration_plate,
                    'current_mileage' => $vehicle->current_mileage,
                    'last_reading_mileage' => $lastReading->mileage,
                    'difference' => abs($lastReading->mileage - $vehicle->current_mileage),
                    'last_reading_date' => $lastReading->recorded_at,
                ]);
            }
        }

        return $inconsistencies;
    }

    /**
     * Génère des notes automatiques selon le type de relevé
     *
     * @param string $type
     * @param array $context
     * @return string
     */
    protected function generateNotes(string $type, array $context): string
    {
        $assignmentId = $context['assignment_id'] ?? null;
        $driverId = $context['driver_id'] ?? null;

        return match ($type) {
            'assignment_start' => "Kilométrage de début d'affectation" . ($assignmentId ? " #{$assignmentId}" : ""),
            'assignment_end' => "Kilométrage de fin d'affectation" . ($assignmentId ? " #{$assignmentId}" : ""),
            'manual' => 'Relevé manuel',
            'automatic' => 'Relevé automatique',
            default => 'Relevé kilométrique',
        };
    }
}
