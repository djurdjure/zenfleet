<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleMileageReading;
use App\Models\MileageHistory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * 🚀 ENTERPRISE-GRADE Service de Gestion des Affectations Rétroactives
 * 
 * Ce service ultra-robuste gère les affectations dans le passé avec :
 * - Validation historique complète des disponibilités
 * - Vérification des statuts aux dates concernées
 * - Cohérence des kilométrages dans le temps
 * - Audit trail complet pour traçabilité
 * 
 * @version 2.1 Ultra-Pro
 * @author ZenFleet Engineering Team
 */
class RetroactiveAssignmentService
{
    private OverlapCheckService $overlapService;
    
    public function __construct(OverlapCheckService $overlapService)
    {
        $this->overlapService = $overlapService;
    }

    /**
     * 🔍 Valide la possibilité de créer une affectation rétroactive
     * 
     * @param int $vehicleId
     * @param int $driverId
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param int $organizationId
     * @return array ['is_valid' => bool, 'warnings' => array, 'errors' => array, 'historical_data' => array]
     */
    public function validateRetroactiveAssignment(
        int $vehicleId,
        int $driverId,
        Carbon $startDate,
        ?Carbon $endDate,
        int $organizationId
    ): array {
        $validation = [
            'is_valid' => true,
            'warnings' => [],
            'errors' => [],
            'historical_data' => [],
            'recommendations' => []
        ];

        // 1. Vérifier si c'est bien une date passée
        $isRetroactive = $startDate->isPast();
        if ($isRetroactive) {
            $validation['historical_data']['is_retroactive'] = true;
            $validation['historical_data']['days_in_past'] = $startDate->diffInDays(now());
            
            // Ajouter un warning pour les dates très anciennes
            if ($startDate->diffInDays(now()) > 90) {
                $validation['warnings'][] = [
                    'type' => 'old_date',
                    'message' => "Cette affectation date de plus de 90 jours. Assurez-vous d'avoir les justificatifs nécessaires.",
                    'severity' => 'medium'
                ];
            }
        }

        // 2. Vérifier les conflits existants avec la méthode standard
        $overlapCheck = $this->overlapService->checkOverlap(
            $vehicleId,
            $driverId,
            $startDate,
            $endDate,
            null,
            $organizationId
        );

        if ($overlapCheck['has_conflicts']) {
            $validation['is_valid'] = false;
            foreach ($overlapCheck['conflicts'] as $conflict) {
                $validation['errors'][] = [
                    'type' => 'overlap',
                    'message' => "Conflit détecté avec l'affectation #{$conflict['id']}: {$conflict['resource_label']} du {$conflict['period']['start']} au {$conflict['period']['end']}",
                    'conflict_id' => $conflict['id']
                ];
            }
        }

        // 3. Vérifier le statut historique du véhicule
        $vehicleHistoricalStatus = $this->checkVehicleHistoricalStatus($vehicleId, $startDate, $endDate);
        if (!$vehicleHistoricalStatus['was_available']) {
            $validation['warnings'][] = [
                'type' => 'vehicle_status',
                'message' => "Le véhicule n'était pas en statut 'Disponible' à cette période: {$vehicleHistoricalStatus['status_at_date']}",
                'severity' => 'low'
            ];
        }
        $validation['historical_data']['vehicle_status'] = $vehicleHistoricalStatus;

        // 4. Vérifier le statut historique du chauffeur
        $driverHistoricalStatus = $this->checkDriverHistoricalStatus($driverId, $startDate, $endDate);
        if (!$driverHistoricalStatus['was_available']) {
            $validation['warnings'][] = [
                'type' => 'driver_status',
                'message' => "Le chauffeur n'était pas en statut 'Disponible' à cette période: {$driverHistoricalStatus['status_at_date']}",
                'severity' => 'low'
            ];
        }
        $validation['historical_data']['driver_status'] = $driverHistoricalStatus;

        // 5. Vérifier la cohérence du kilométrage
        if ($isRetroactive) {
            $mileageCheck = $this->validateMileageCoherence($vehicleId, $startDate, $endDate);
            if (!$mileageCheck['is_coherent']) {
                $validation['warnings'][] = [
                    'type' => 'mileage',
                    'message' => $mileageCheck['message'],
                    'severity' => 'high'
                ];
                $validation['recommendations'][] = "Vérifiez et ajustez le kilométrage de départ si nécessaire";
            }
            $validation['historical_data']['mileage'] = $mileageCheck;
        }

        // 6. Vérifier les affectations futures qui pourraient être impactées
        if ($isRetroactive) {
            $futureImpact = $this->checkFutureAssignmentsImpact($vehicleId, $driverId, $startDate, $endDate);
            if ($futureImpact['has_impact']) {
                $validation['warnings'][] = [
                    'type' => 'future_impact',
                    'message' => "Cette affectation rétroactive pourrait impacter {$futureImpact['count']} affectation(s) future(s)",
                    'severity' => 'medium'
                ];
            }
            $validation['historical_data']['future_impact'] = $futureImpact;
        }

        // 7. Ajouter des recommandations basées sur l'analyse
        if ($isRetroactive) {
            $validation['recommendations'][] = "📝 Documentez la raison de cette saisie rétroactive dans le champ 'Notes'";
            $validation['recommendations'][] = "📊 Vérifiez les rapports mensuels déjà générés qui pourraient être impactés";
            
            if ($validation['historical_data']['days_in_past'] > 30) {
                $validation['recommendations'][] = "⚠️ Informez la comptabilité de cette modification rétroactive";
            }
        }

        // 8. Générer un score de confiance
        $confidenceScore = $this->calculateConfidenceScore($validation);
        $validation['confidence_score'] = $confidenceScore;

        return $validation;
    }

    /**
     * 🕐 Vérifie le statut historique d'un véhicule
     * 
     * ENTERPRISE-GRADE: Logique optimiste intelligente
     * Si pas d'historique ET véhicule disponible actuellement ET pas de conflit
     * → Considérer comme disponible historiquement (déduction raisonnable)
     */
    private function checkVehicleHistoricalStatus(int $vehicleId, Carbon $startDate, ?Carbon $endDate): array
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            return ['was_available' => false, 'status_at_date' => 'Véhicule introuvable'];
        }

        // Chercher dans l'historique des statuts si la table existe
        try {
            if (DB::getSchemaBuilder()->hasTable('vehicle_status_history')) {
                $statusHistory = DB::table('vehicle_status_history')
                    ->where('vehicle_id', $vehicleId)
                    ->where('changed_at', '<=', $startDate)
                    ->orderBy('changed_at', 'desc')
                    ->first();

                if ($statusHistory) {
                    $wasAvailable = in_array($statusHistory->status_id, [8, 1]); // Parking ou Disponible
                    return [
                        'was_available' => $wasAvailable,
                        'status_at_date' => $statusHistory->status_name ?? "Status ID: {$statusHistory->status_id}",
                        'status_id' => $statusHistory->status_id,
                        'changed_at' => $statusHistory->changed_at
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('[RetroactiveAssignment] Historique statuts véhicule indisponible', [
                'vehicle_id' => $vehicleId,
                'error' => $e->getMessage()
            ]);
        }

        // ✅ LOGIQUE INTELLIGENTE ENTERPRISE-GRADE
        // Si pas d'historique: Vérifier affectations durant cette période
        $hadAssignmentsDuringPeriod = Assignment::where('vehicle_id', $vehicleId)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_datetime', [$startDate, $endDate ?? Carbon::now()])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate ?? Carbon::now()]);
            })
            ->exists();

        // Si aucune affectation durant période ET véhicule disponible actuellement
        // → Déduction raisonnable: était probablement disponible
        $currentlyAvailable = $vehicle->status_id == 8 || $vehicle->is_available;
        $wasLikelyAvailable = !$hadAssignmentsDuringPeriod && $currentlyAvailable;

        return [
            'was_available' => $wasLikelyAvailable,
            'status_at_date' => $wasLikelyAvailable 
                ? 'Disponible (déduit: pas d\'affectation durant période)'
                : ($vehicle->status_label ?? 'Statut actuel'),
            'status_id' => $vehicle->status_id,
            'inference' => 'Statut déduit en l\'absence d\'historique (méthode enterprise-grade)',
            'had_assignments' => $hadAssignmentsDuringPeriod
        ];
    }

    /**
     * 👤 Vérifie le statut historique d'un chauffeur
     * 
     * ENTERPRISE-GRADE: Logique optimiste intelligente
     * Si pas d'historique ET chauffeur disponible actuellement ET pas de conflit
     * → Considérer comme disponible historiquement (déduction raisonnable)
     */
    private function checkDriverHistoricalStatus(int $driverId, Carbon $startDate, ?Carbon $endDate): array
    {
        $driver = Driver::find($driverId);
        if (!$driver) {
            return ['was_available' => false, 'status_at_date' => 'Chauffeur introuvable'];
        }

        // Chercher dans l'historique des statuts si la table existe
        try {
            if (DB::getSchemaBuilder()->hasTable('driver_status_history')) {
                $statusHistory = DB::table('driver_status_history')
                    ->where('driver_id', $driverId)
                    ->where('changed_at', '<=', $startDate)
                    ->orderBy('changed_at', 'desc')
                    ->first();

                if ($statusHistory) {
                    $wasAvailable = in_array($statusHistory->status_id, [9, 1]); // Available ou Actif
                    return [
                        'was_available' => $wasAvailable,
                        'status_at_date' => $statusHistory->status_name ?? "Status ID: {$statusHistory->status_id}",
                        'status_id' => $statusHistory->status_id,
                        'changed_at' => $statusHistory->changed_at
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('[RetroactiveAssignment] Historique statuts chauffeur indisponible', [
                'driver_id' => $driverId,
                'error' => $e->getMessage()
            ]);
        }

        // ✅ LOGIQUE INTELLIGENTE ENTERPRISE-GRADE
        // Si pas d'historique: Vérifier affectations durant cette période
        $hadAssignmentsDuringPeriod = Assignment::where('driver_id', $driverId)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_datetime', [$startDate, $endDate ?? Carbon::now()])
                  ->orWhereBetween('end_datetime', [$startDate, $endDate ?? Carbon::now()]);
            })
            ->exists();

        // Si aucune affectation durant période ET chauffeur disponible actuellement
        // → Déduction raisonnable: était probablement disponible
        $currentlyAvailable = $driver->status_id == 9 || $driver->is_available;
        $wasLikelyAvailable = !$hadAssignmentsDuringPeriod && $currentlyAvailable;

        return [
            'was_available' => $wasLikelyAvailable,
            'status_at_date' => $wasLikelyAvailable 
                ? 'Disponible (déduit: pas d\'affectation durant période)'
                : ($driver->status_label ?? 'Statut actuel'),
            'status_id' => $driver->status_id,
            'inference' => 'Statut déduit en l\'absence d\'historique (méthode enterprise-grade)',
            'had_assignments' => $hadAssignmentsDuringPeriod
        ];
    }

    /**
     * 📏 Valide la cohérence du kilométrage dans le temps
     */
    private function validateMileageCoherence(int $vehicleId, Carbon $startDate, ?Carbon $endDate): array
    {
        // Récupérer le kilométrage actuel du véhicule
        $vehicle = Vehicle::find($vehicleId);
        $currentMileage = $vehicle->current_mileage ?? 0;

        // Chercher les entrées de kilométrage autour de cette date
        // Utiliser MileageHistory qui contient l'historique des relevés
        $mileageBefore = MileageHistory::where('vehicle_id', $vehicleId)
            ->where('recorded_at', '<=', $startDate)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $mileageAfter = MileageHistory::where('vehicle_id', $vehicleId)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->first();

        $result = [
            'is_coherent' => true,
            'message' => 'Kilométrage cohérent',
            'mileage_before' => $mileageBefore?->mileage,
            'mileage_after' => $mileageAfter?->mileage,
            'current_mileage' => $currentMileage
        ];

        // Si on a un kilométrage après la date de début
        if ($mileageAfter && $mileageBefore) {
            if ($mileageBefore->mileage > $mileageAfter->mileage) {
                $result['is_coherent'] = false;
                $result['message'] = "Incohérence détectée: le kilométrage diminue entre " . 
                    $mileageBefore->recorded_at->format('d/m/Y') . " ({$mileageBefore->mileage} km) et " .
                    $mileageAfter->recorded_at->format('d/m/Y') . " ({$mileageAfter->mileage} km)";
            }
        }

        // Suggérer un kilométrage de départ basé sur l'historique
        if ($mileageBefore) {
            $result['suggested_start_mileage'] = $mileageBefore->mileage;
        } elseif ($mileageAfter) {
            // Estimation basée sur le kilométrage suivant
            $daysUntilNext = $startDate->diffInDays($mileageAfter->recorded_at);
            $estimatedDailyKm = 100; // Estimation moyenne
            $result['suggested_start_mileage'] = max(0, $mileageAfter->mileage - ($daysUntilNext * $estimatedDailyKm));
        }

        return $result;
    }

    /**
     * 🔮 Vérifie l'impact sur les affectations futures
     */
    private function checkFutureAssignmentsImpact(
        int $vehicleId, 
        int $driverId, 
        Carbon $startDate, 
        ?Carbon $endDate
    ): array {
        $query = Assignment::where(function($q) use ($vehicleId, $driverId) {
                $q->where('vehicle_id', $vehicleId)
                  ->orWhere('driver_id', $driverId);
            })
            ->where('start_datetime', '>', $endDate ?? $startDate);

        $futureAssignments = $query->get();

        return [
            'has_impact' => $futureAssignments->isNotEmpty(),
            'count' => $futureAssignments->count(),
            'assignments' => $futureAssignments->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'start' => $assignment->start_datetime->format('d/m/Y H:i'),
                    'vehicle' => $assignment->vehicle->registration_number ?? 'N/A',
                    'driver' => $assignment->driver->full_name ?? 'N/A'
                ];
            })->toArray()
        ];
    }

    /**
     * 📊 Calcule un score de confiance pour l'affectation rétroactive
     */
    private function calculateConfidenceScore(array $validation): array
    {
        $score = 100;
        $factors = [];

        // Pénalités basées sur les erreurs et warnings
        $errorCount = count($validation['errors']);
        $warningCount = count($validation['warnings']);
        
        $score -= $errorCount * 25; // -25 points par erreur
        $score -= $warningCount * 10; // -10 points par warning

        if ($errorCount > 0) {
            $factors[] = "-{$errorCount} erreur(s) critique(s)";
        }
        if ($warningCount > 0) {
            $factors[] = "-{$warningCount} avertissement(s)";
        }

        // Bonus pour données historiques complètes
        if (isset($validation['historical_data']['vehicle_status']) && 
            !isset($validation['historical_data']['vehicle_status']['warning'])) {
            $score += 5;
            $factors[] = "+Historique véhicule disponible";
        }

        if (isset($validation['historical_data']['driver_status']) && 
            !isset($validation['historical_data']['driver_status']['warning'])) {
            $score += 5;
            $factors[] = "+Historique chauffeur disponible";
        }

        // Pénalité pour dates très anciennes
        if (isset($validation['historical_data']['days_in_past'])) {
            $daysInPast = $validation['historical_data']['days_in_past'];
            if ($daysInPast > 180) {
                $score -= 20;
                $factors[] = "-Date très ancienne (>6 mois)";
            } elseif ($daysInPast > 90) {
                $score -= 10;
                $factors[] = "-Date ancienne (>3 mois)";
            }
        }

        return [
            'score' => max(0, min(100, $score)),
            'level' => $this->getConfidenceLevel($score),
            'factors' => $factors
        ];
    }

    /**
     * 🎯 Détermine le niveau de confiance
     */
    private function getConfidenceLevel(int $score): string
    {
        if ($score >= 90) return '🟢 Excellent';
        if ($score >= 70) return '🟡 Bon';
        if ($score >= 50) return '🟠 Moyen';
        if ($score >= 30) return '🔴 Faible';
        return '⛔ Très faible';
    }

    /**
     * 📝 Enregistre une affectation rétroactive avec audit trail complet
     */
    public function createRetroactiveAssignment(array $data, array $validationResult): Assignment
    {
        return DB::transaction(function() use ($data, $validationResult) {
            // Créer l'affectation
            $assignment = Assignment::create($data);

            // Enregistrer l'audit trail
            DB::table('retroactive_assignment_logs')->insert([
                'assignment_id' => $assignment->id,
                'created_by' => auth()->id(),
                'created_at' => now(),
                'days_in_past' => $validationResult['historical_data']['days_in_past'] ?? 0,
                'confidence_score' => $validationResult['confidence_score']['score'] ?? 0,
                'warnings' => json_encode($validationResult['warnings']),
                'historical_data' => json_encode($validationResult['historical_data']),
                'justification' => $data['notes'] ?? null
            ]);

            // Logger l'événement
            Log::info('[RetroactiveAssignment] Création affectation rétroactive', [
                'assignment_id' => $assignment->id,
                'start_date' => $data['start_datetime'],
                'days_in_past' => $validationResult['historical_data']['days_in_past'] ?? 0,
                'user_id' => auth()->id()
            ]);

            return $assignment;
        });
    }
}
