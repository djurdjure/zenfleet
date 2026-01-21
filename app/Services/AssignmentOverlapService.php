<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Scopes\UserVehicleAccessScope;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

/**
 * 🚫 Service Anti-Chevauchement d'Affectations
 *
 * Gère la vérification et prévention des chevauchements d'affectations:
 * - Validation véhicule et chauffeur
 * - Gestion durées indéterminées (end_datetime = NULL)
 * - Suggestions de créneaux libres
 * - Messages d'erreur explicites
 *
 * @author ZenFleet Architecture Team
 */
class AssignmentOverlapService
{
    /**
     * Vérifie les chevauchements pour une affectation
     *
     * @param int $organizationId
     * @param int $vehicleId
     * @param int $driverId
     * @param Carbon $startDatetime
     * @param Carbon|null $endDatetime
     * @param int|null $excludeAssignmentId Pour édition
     * @return array{
     *     hasConflicts: bool,
     *     vehicleConflicts: Collection,
     *     driverConflicts: Collection,
     *     suggestedSlots: array,
     *     messages: array
     * }
     */
    public function checkOverlap(
        int $organizationId,
        int $vehicleId,
        int $driverId,
        Carbon $startDatetime,
        ?Carbon $endDatetime = null,
        ?int $excludeAssignmentId = null
    ): array {
        // Vérification véhicule
        $vehicleConflicts = $this->checkVehicleOverlap(
            $organizationId,
            $vehicleId,
            $startDatetime,
            $endDatetime,
            $excludeAssignmentId
        );

        // Vérification chauffeur
        $driverConflicts = $this->checkDriverOverlap(
            $organizationId,
            $driverId,
            $startDatetime,
            $endDatetime,
            $excludeAssignmentId
        );

        $hasConflicts = $vehicleConflicts->isNotEmpty() || $driverConflicts->isNotEmpty();

        $result = [
            'hasConflicts' => $hasConflicts,
            'vehicleConflicts' => $vehicleConflicts,
            'driverConflicts' => $driverConflicts,
            'suggestedSlots' => [],
            'messages' => []
        ];

        if ($hasConflicts) {
            $result['messages'] = $this->generateConflictMessages(
                $vehicleConflicts,
                $driverConflicts,
                $vehicleId,
                $driverId
            );

            // Générer suggestions de créneaux libres
            $result['suggestedSlots'] = $this->suggestAvailableSlots(
                $organizationId,
                $vehicleId,
                $driverId,
                $startDatetime,
                $endDatetime
            );
        }

        return $result;
    }

    /**
     * Vérifie les conflits pour un véhicule
     */
    private function checkVehicleOverlap(
        int $organizationId,
        int $vehicleId,
        Carbon $startDatetime,
        ?Carbon $endDatetime,
        ?int $excludeAssignmentId = null
    ): Collection {
        return Assignment::where('organization_id', $organizationId)
            ->where('vehicle_id', $vehicleId)
            ->when($excludeAssignmentId, fn($q) => $q->where('id', '!=', $excludeAssignmentId))
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where(function ($query) use ($startDatetime, $endDatetime) {
                $this->addOverlapConditions($query, $startDatetime, $endDatetime);
            })
            ->with(['vehicle', 'driver'])
            ->get();
    }

    /**
     * Vérifie les conflits pour un chauffeur
     */
    private function checkDriverOverlap(
        int $organizationId,
        int $driverId,
        Carbon $startDatetime,
        ?Carbon $endDatetime,
        ?int $excludeAssignmentId = null
    ): Collection {
        return Assignment::where('organization_id', $organizationId)
            ->where('driver_id', $driverId)
            ->when($excludeAssignmentId, fn($q) => $q->where('id', '!=', $excludeAssignmentId))
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where(function ($query) use ($startDatetime, $endDatetime) {
                $this->addOverlapConditions($query, $startDatetime, $endDatetime);
            })
            ->with(['vehicle', 'driver'])
            ->get();
    }

    /**
     * Ajoute les conditions d'intersection temporelle
     */
    private function addOverlapConditions(Builder $query, Carbon $start, ?Carbon $end): void
    {
        $query->where(function ($q) use ($start, $end) {
            if ($end) {
                // Période définie - intersection avec affectations existantes
                $q->where(function ($subQuery) use ($start, $end) {
                    // Cas 1: Affectations avec fin définie qui se chevauchent
                    $subQuery->whereNotNull('end_datetime')
                        ->where('start_datetime', '<', $end)
                        ->where('end_datetime', '>', $start);
                })->orWhere(function ($subQuery) use ($start) {
                    // Cas 2: Affectations sans fin qui commencent avant notre début
                    $subQuery->whereNull('end_datetime')
                        ->where('start_datetime', '<=', $start);
                });
            } else {
                // Durée indéterminée - conflit avec tout ce qui suit
                $q->where('start_datetime', '>=', $start);
            }
        });
    }

    /**
     * Génère les messages d'erreur explicites
     */
    private function generateConflictMessages(
        Collection $vehicleConflicts,
        Collection $driverConflicts,
        int $vehicleId,
        int $driverId
    ): array {
        $messages = [];

        if ($vehicleConflicts->isNotEmpty()) {
            $vehicle = Vehicle::find($vehicleId);
            $vehicleName = $vehicle ? $vehicle->registration_plate ?? $vehicle->brand . ' ' . $vehicle->model : "Véhicule #$vehicleId";

            foreach ($vehicleConflicts as $conflict) {
                $period = $this->formatConflictPeriod($conflict);
                $messages[] = "Conflit véhicule: {$vehicleName} est déjà affecté à {$conflict->driver_display} {$period}";
            }
        }

        if ($driverConflicts->isNotEmpty()) {
            $driver = Driver::find($driverId);
            $driverName = $driver ? $driver->first_name . ' ' . $driver->last_name : "Chauffeur #$driverId";

            foreach ($driverConflicts as $conflict) {
                $period = $this->formatConflictPeriod($conflict);
                $messages[] = "Conflit chauffeur: {$driverName} est déjà affecté au véhicule {$conflict->vehicle_display} {$period}";
            }
        }

        return $messages;
    }

    /**
     * Formate la période de conflit
     */
    private function formatConflictPeriod(Assignment $assignment): string
    {
        $start = $assignment->start_datetime->format('d/m/Y H:i');

        if ($assignment->end_datetime) {
            $end = $assignment->end_datetime->format('d/m/Y H:i');
            return "du {$start} au {$end}";
        }

        return "depuis le {$start} (durée indéterminée)";
    }

    /**
     * Suggère des créneaux libres
     */
    public function suggestAvailableSlots(
        int $organizationId,
        int $vehicleId,
        int $driverId,
        Carbon $requestedStart,
        ?Carbon $requestedEnd = null,
        int $maxSuggestions = 3
    ): array {
        $suggestions = [];
        $searchStart = $requestedStart->copy()->subDays(7); // Chercher 7 jours avant
        $searchEnd = $requestedStart->copy()->addDays(14);  // Jusqu'à 14 jours après

        // Récupérer toutes les affectations du véhicule et chauffeur dans la période
        $vehicleAssignments = $this->getAssignmentsInPeriod($organizationId, $vehicleId, null, $searchStart, $searchEnd);
        $driverAssignments = $this->getAssignmentsInPeriod($organizationId, null, $driverId, $searchStart, $searchEnd);

        // Merger et trier par date
        $allAssignments = $vehicleAssignments->merge($driverAssignments)
            ->sortBy('start_datetime')
            ->unique('id');

        $requestedDuration = $requestedEnd ?
            $requestedStart->diffInHours($requestedEnd) :
            24; // Durée par défaut pour suggestions

        // Chercher créneaux libres
        $currentTime = $searchStart->copy();

        foreach ($allAssignments as $assignment) {
            $slotEnd = $assignment->start_datetime;

            // Vérifier si on a assez de temps avant cette affectation
            if ($currentTime->diffInHours($slotEnd) >= $requestedDuration) {
                $suggestions[] = [
                    'start' => $currentTime->copy(),
                    'end' => $requestedEnd ? $currentTime->copy()->addHours($requestedDuration) : null,
                    'duration_hours' => $requestedDuration,
                    'reason' => 'Créneau libre avant affectation existante'
                ];

                if (count($suggestions) >= $maxSuggestions) {
                    break;
                }
            }

            // Avancer après cette affectation
            $currentTime = $assignment->end_datetime ?? $assignment->start_datetime->copy()->addHours(24);
        }

        // Suggestion après toutes les affectations si pas assez trouvé
        if (count($suggestions) < $maxSuggestions) {
            $suggestions[] = [
                'start' => $currentTime->copy(),
                'end' => $requestedEnd ? $currentTime->copy()->addHours($requestedDuration) : null,
                'duration_hours' => $requestedDuration,
                'reason' => 'Créneau libre après affectations existantes'
            ];
        }

        return array_map(function ($suggestion) {
            return [
                'start' => $suggestion['start']->format('Y-m-d\TH:i'),
                'end' => $suggestion['end']?->format('Y-m-d\TH:i'),
                'start_formatted' => $suggestion['start']->format('d/m/Y H:i'),
                'end_formatted' => $suggestion['end']?->format('d/m/Y H:i'),
                'duration_hours' => $suggestion['duration_hours'],
                'reason' => $suggestion['reason'],
                'description' => $suggestion['reason'],
            ];
        }, $suggestions);
    }

    /**
     * Récupère les affectations dans une période
     */
    private function getAssignmentsInPeriod(
        int $organizationId,
        ?int $vehicleId,
        ?int $driverId,
        Carbon $start,
        Carbon $end
    ): Collection {
        return Assignment::where('organization_id', $organizationId)
            ->when($vehicleId, fn($q) => $q->where('vehicle_id', $vehicleId))
            ->when($driverId, fn($q) => $q->where('driver_id', $driverId))
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    // Affectations avec fin définie qui intersectent
                    $q->whereNotNull('end_datetime')
                        ->where('start_datetime', '<', $end)
                        ->where('end_datetime', '>', $start);
                })->orWhere(function ($q) use ($start) {
                    // Affectations sans fin qui commencent avant la fin recherchée
                    $q->whereNull('end_datetime')
                        ->where('start_datetime', '<=', $start);
                });
            })
            ->orderBy('start_datetime')
            ->get();
    }

    /**
     * Vérifie la disponibilité d'un véhicule
     */
    public function isVehicleAvailable(
        int $organizationId,
        int $vehicleId,
        Carbon $start,
        ?Carbon $end = null,
        ?int $excludeAssignmentId = null
    ): bool {
        $conflicts = $this->checkVehicleOverlap(
            $organizationId,
            $vehicleId,
            $start,
            $end,
            $excludeAssignmentId
        );

        return $conflicts->isEmpty();
    }

    /**
     * Vérifie la disponibilité d'un chauffeur
     */
    public function isDriverAvailable(
        int $organizationId,
        int $driverId,
        Carbon $start,
        ?Carbon $end = null,
        ?int $excludeAssignmentId = null
    ): bool {
        $conflicts = $this->checkDriverOverlap(
            $organizationId,
            $driverId,
            $start,
            $end,
            $excludeAssignmentId
        );

        return $conflicts->isEmpty();
    }

    /**
     * Trouve le prochain créneau libre commun
     */
    public function findNextAvailableSlot(
        int $organizationId,
        int $vehicleId,
        int $driverId,
        Carbon $fromDate,
        int $durationHours = 24,
        int $maxDaysSearch = 30
    ): ?array {
        $searchEnd = $fromDate->copy()->addDays($maxDaysSearch);
        $currentStart = $fromDate->copy();

        while ($currentStart->lt($searchEnd)) {
            $currentEnd = $currentStart->copy()->addHours($durationHours);

            // Vérifier disponibilité véhicule et chauffeur
            if ($this->isVehicleAvailable($organizationId, $vehicleId, $currentStart, $currentEnd) &&
                $this->isDriverAvailable($organizationId, $driverId, $currentStart, $currentEnd)) {

                return [
                    'start' => $currentStart->format('Y-m-d\TH:i'),
                    'end' => $currentEnd->format('Y-m-d\TH:i'),
                    'start_formatted' => $currentStart->format('d/m/Y H:i'),
                    'end_formatted' => $currentEnd->format('d/m/Y H:i'),
                    'duration_hours' => $durationHours
                ];
            }

            // Avancer par tranche d'une heure
            $currentStart->addHour();
        }

        return null;
    }

    /**
     * Valide une affectation avant sauvegarde
     */
    public function validateAssignment(
        int $organizationId,
        int $vehicleId,
        int $driverId,
        Carbon $startDatetime,
        ?Carbon $endDatetime = null,
        ?int $excludeAssignmentId = null
    ): array {
        // Validation des dates
        $validationErrors = [];

        $vehicleExists = Vehicle::withoutGlobalScope(UserVehicleAccessScope::class)
            ->where('id', $vehicleId)
            ->where('organization_id', $organizationId)
            ->exists();

        if (! $vehicleExists) {
            $validationErrors[] = 'Véhicule introuvable pour cette organisation.';
        }

        $driverExists = Driver::where('id', $driverId)
            ->where('organization_id', $organizationId)
            ->exists();

        if (! $driverExists) {
            $validationErrors[] = 'Chauffeur introuvable pour cette organisation.';
        }

        if ($endDatetime && $endDatetime->lte($startDatetime)) {
            $validationErrors[] = 'La date de fin doit être postérieure à la date de début';
        }

        if ($startDatetime->lt(now()->subHour())) {
            $validationErrors[] = 'Impossible de créer une affectation dans le passé (plus d\'une heure)';
        }

        if ($startDatetime->gt(now()->addYear())) {
            $validationErrors[] = 'Impossible de programmer une affectation plus d\'un an à l\'avance';
        }

        // Vérification chevauchements
        $overlapCheck = $this->checkOverlap(
            $organizationId,
            $vehicleId,
            $driverId,
            $startDatetime,
            $endDatetime,
            $excludeAssignmentId
        );

        return [
            'isValid' => empty($validationErrors) && !$overlapCheck['hasConflicts'],
            'validationErrors' => $validationErrors,
            'overlapErrors' => $overlapCheck['messages'],
            'suggestedSlots' => $overlapCheck['suggestedSlots'],
            'conflicts' => [
                'vehicle' => $overlapCheck['vehicleConflicts'],
                'driver' => $overlapCheck['driverConflicts']
            ]
        ];
    }
}
