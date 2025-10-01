<?php

namespace App\Services;

use App\Models\Assignment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * 🔍 Service de vérification de chevauchement d'affectations
 *
 * Implémente les règles enterprise-grade:
 * - Un véhicule ne peut avoir 2 affectations qui s'intersectent
 * - Un chauffeur ne peut être affecté à 2 véhicules sur des périodes qui s'intersectent
 * - end_datetime = NULL est traité comme +∞
 * - Frontières: deux créneaux consécutifs sont autorisés si le premier se termine exactement quand le second démarre
 */
class OverlapCheckService
{
    /**
     * Vérifie les chevauchements pour une affectation
     *
     * @param int $vehicleId
     * @param int $driverId
     * @param Carbon $start
     * @param Carbon|null $end NULL = durée indéterminée
     * @param int|null $excludeId ID d'affectation à exclure (pour édition)
     * @return array ['conflicts' => [...], 'suggestions' => [...]]
     */
    public function checkOverlap(
        int $vehicleId,
        int $driverId,
        Carbon $start,
        ?Carbon $end = null,
        ?int $excludeId = null,
        ?int $organizationId = null
    ): array {
        $organizationId = $organizationId ?? auth()->user()->organization_id;

        // Vérifier les conflits véhicule
        $vehicleConflicts = $this->findConflictsForResource(
            'vehicle_id',
            $vehicleId,
            $start,
            $end,
            $organizationId,
            $excludeId
        );

        // Vérifier les conflits chauffeur
        $driverConflicts = $this->findConflictsForResource(
            'driver_id',
            $driverId,
            $start,
            $end,
            $organizationId,
            $excludeId
        );

        $allConflicts = $vehicleConflicts->merge($driverConflicts);

        return [
            'has_conflicts' => $allConflicts->isNotEmpty(),
            'conflicts' => $this->formatConflicts($allConflicts),
            'suggestions' => $this->generateSuggestions($vehicleId, $driverId, $start, $end, $organizationId, $excludeId)
        ];
    }

    /**
     * Trouve les conflits pour une ressource (véhicule ou chauffeur)
     */
    private function findConflictsForResource(
        string $resourceColumn,
        int $resourceId,
        Carbon $start,
        ?Carbon $end,
        int $organizationId,
        ?int $excludeId = null
    ): Collection {
        $query = Assignment::where('organization_id', $organizationId)
            ->where($resourceColumn, $resourceId)
            ->with(['vehicle', 'driver']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get()->filter(function ($assignment) use ($start, $end) {
            return $this->intervalsOverlap(
                $start,
                $end,
                $assignment->start_datetime,
                $assignment->end_datetime
            );
        });
    }

    /**
     * Vérifie si deux intervalles se chevauchent
     * Selon spécifications: frontières exactes sont autorisées
     */
    private function intervalsOverlap(
        Carbon $start1,
        ?Carbon $end1,
        Carbon $start2,
        ?Carbon $end2
    ): bool {
        // Traiter NULL comme +∞
        $end1Effective = $end1 ?? Carbon::create(2099, 12, 31);
        $end2Effective = $end2 ?? Carbon::create(2099, 12, 31);

        // Pas de chevauchement si l'un se termine exactement quand l'autre commence
        if ($end1Effective->equalTo($start2) || $end2Effective->equalTo($start1)) {
            return false;
        }

        // Chevauchement si les intervalles s'intersectent
        return $start1->lt($end2Effective) && $start2->lt($end1Effective);
    }

    /**
     * Formate les conflits pour l'API
     */
    private function formatConflicts(Collection $conflicts): array
    {
        return $conflicts->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'resource_type' => $assignment->vehicle_id ? 'vehicle' : 'driver',
                'resource_label' => $assignment->vehicle_display . ' / ' . $assignment->driver_display,
                'period' => [
                    'start' => $assignment->start_datetime->format('d/m/Y H:i'),
                    'end' => $assignment->end_datetime?->format('d/m/Y H:i') ?? 'Indéterminé'
                ],
                'status' => $assignment->status_label,
                'reason' => $assignment->reason
            ];
        })->toArray();
    }

    /**
     * Génère des suggestions de créneaux libres
     */
    private function generateSuggestions(
        int $vehicleId,
        int $driverId,
        Carbon $start,
        ?Carbon $end,
        int $organizationId,
        ?int $excludeId = null
    ): array {
        $suggestions = [];
        $requestedDuration = $end ? $start->diffInHours($end) : 24; // Durée par défaut si indéterminée

        // Recherche des créneaux libres dans les 7 prochains jours
        $searchStart = now();
        $searchEnd = now()->addDays(7);

        $existingAssignments = Assignment::where('organization_id', $organizationId)
            ->where(function ($query) use ($vehicleId, $driverId) {
                $query->where('vehicle_id', $vehicleId)
                      ->orWhere('driver_id', $driverId);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereBetween('start_datetime', [$searchStart, $searchEnd])
            ->orderBy('start_datetime')
            ->get();

        // Trouver les créneaux libres
        $currentTime = $searchStart->copy();
        foreach ($existingAssignments as $assignment) {
            if ($currentTime->addHours($requestedDuration)->lte($assignment->start_datetime)) {
                $suggestions[] = [
                    'start' => $currentTime->format('Y-m-d\TH:i'),
                    'end' => $currentTime->copy()->addHours($requestedDuration)->format('Y-m-d\TH:i'),
                    'description' => 'Disponible à partir du ' . $currentTime->format('d/m/Y H:i')
                ];

                if (count($suggestions) >= 3) break; // Limiter à 3 suggestions
            }
            $currentTime = $assignment->end_datetime ?? $assignment->start_datetime->copy()->addHours(24);
        }

        return $suggestions;
    }

    /**
     * Trouve le prochain créneau libre de durée donnée
     */
    public function findNextAvailableSlot(
        int $vehicleId,
        int $driverId,
        int $durationHours = 24,
        ?int $organizationId = null
    ): ?array {
        $organizationId = $organizationId ?? auth()->user()->organization_id;

        // Recherche dans les 30 prochains jours
        $searchStart = now();
        $searchEnd = now()->addDays(30);

        $assignments = Assignment::where('organization_id', $organizationId)
            ->where(function ($query) use ($vehicleId, $driverId) {
                $query->where('vehicle_id', $vehicleId)
                      ->orWhere('driver_id', $driverId);
            })
            ->where('start_datetime', '>=', $searchStart)
            ->orderBy('start_datetime')
            ->get();

        $currentSlot = $searchStart->copy();

        foreach ($assignments as $assignment) {
            $proposedEnd = $currentSlot->copy()->addHours($durationHours);

            if ($proposedEnd->lte($assignment->start_datetime)) {
                return [
                    'start' => $currentSlot->format('Y-m-d\TH:i'),
                    'end' => $proposedEnd->format('Y-m-d\TH:i'),
                    'start_formatted' => $currentSlot->format('d/m/Y H:i'),
                    'end_formatted' => $proposedEnd->format('d/m/Y H:i')
                ];
            }

            // Passer au slot suivant après cette affectation
            $currentSlot = $assignment->end_datetime ?? $assignment->start_datetime->copy()->addDay();
        }

        // Si aucun conflit trouvé, retourner le slot immédiat
        return [
            'start' => $currentSlot->format('Y-m-d\TH:i'),
            'end' => $currentSlot->copy()->addHours($durationHours)->format('Y-m-d\TH:i'),
            'start_formatted' => $currentSlot->format('d/m/Y H:i'),
            'end_formatted' => $currentSlot->copy()->addHours($durationHours)->format('d/m/Y H:i')
        ];
    }

    /**
     * Valide une affectation complète avec messages d'erreur explicites
     */
    public function validateAssignment(
        int $vehicleId,
        int $driverId,
        Carbon $start,
        ?Carbon $end = null,
        ?int $excludeId = null,
        ?int $organizationId = null
    ): array {
        // Validation des règles métier de base
        $errors = [];

        if ($end && $start->gte($end)) {
            $errors[] = 'La date de début doit être antérieure à la date de fin.';
        }

        if ($start->lt(now()->subHour())) {
            $errors[] = 'Les affectations ne peuvent pas commencer dans le passé.';
        }

        // Vérification des chevauchements
        $overlapCheck = $this->checkOverlap($vehicleId, $driverId, $start, $end, $excludeId, $organizationId);

        if ($overlapCheck['has_conflicts']) {
            foreach ($overlapCheck['conflicts'] as $conflict) {
                $errors[] = sprintf(
                    'Conflit détecté: %s déjà affecté du %s au %s (%s)',
                    $conflict['resource_label'],
                    $conflict['period']['start'],
                    $conflict['period']['end'],
                    $conflict['status']
                );
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'conflicts' => $overlapCheck['conflicts'],
            'suggestions' => $overlapCheck['suggestions']
        ];
    }
}