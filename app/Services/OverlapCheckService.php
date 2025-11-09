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
     * Génère des suggestions de créneaux libres - ENTERPRISE-GRADE
     *
     * Algorithme robuste qui :
     * - Vérifie les conflits pour véhicule ET chauffeur séparément
     * - Gère correctement les affectations indéterminées (end_datetime = NULL)
     * - Détecte les affectations actives qui ont commencé dans le passé
     * - Trouve les créneaux réellement libres sans faux positifs
     */
    private function generateSuggestions(
        int $vehicleId,
        int $driverId,
        Carbon $start,
        ?Carbon $end,
        int $organizationId,
        ?int $excludeId = null
    ): array {
        $requestedDuration = $end ? $start->diffInHours($end) : 24;
        $searchStart = now();
        $searchEnd = now()->addDays(7);

        // Récupérer TOUTES les affectations actives ou futures pour véhicule ET chauffeur
        // (y compris celles qui ont commencé avant searchStart mais sont toujours actives)
        $vehicleAssignments = Assignment::where('organization_id', $organizationId)
            ->where('vehicle_id', $vehicleId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($searchStart) {
                $q->whereNull('end_datetime') // Affectations indéterminées
                  ->orWhere('end_datetime', '>=', $searchStart); // Affectations qui se terminent après maintenant
            })
            ->orderBy('start_datetime')
            ->get();

        $driverAssignments = Assignment::where('organization_id', $organizationId)
            ->where('driver_id', $driverId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($searchStart) {
                $q->whereNull('end_datetime')
                  ->orWhere('end_datetime', '>=', $searchStart);
            })
            ->orderBy('start_datetime')
            ->get();

        // Fusionner et trier par date de début
        $allAssignments = $vehicleAssignments->merge($driverAssignments)
            ->unique('id')
            ->sortBy('start_datetime')
            ->values();

        // Trouver les créneaux libres
        $suggestions = [];
        $currentSlot = $searchStart->copy();

        foreach ($allAssignments as $assignment) {
            $assignmentStart = $assignment->start_datetime;
            $assignmentEnd = $assignment->end_datetime ?? Carbon::create(2099, 12, 31);

            // Si le créneau actuel est avant le début de cette affectation
            if ($currentSlot->lt($assignmentStart)) {
                $proposedEnd = $currentSlot->copy()->addHours($requestedDuration);

                // Vérifier si la durée demandée rentre avant cette affectation
                if ($proposedEnd->lte($assignmentStart)) {
                    $suggestions[] = [
                        'start' => $currentSlot->format('Y-m-d\TH:i'),
                        'end' => $proposedEnd->format('Y-m-d\TH:i'),
                        'description' => 'Disponible du ' . $currentSlot->format('d/m/Y H:i') . ' au ' . $proposedEnd->format('d/m/Y H:i')
                    ];

                    if (count($suggestions) >= 3) break;
                }
            }

            // Passer au prochain créneau possible après cette affectation
            if ($assignmentEnd->gt($currentSlot)) {
                $currentSlot = $assignmentEnd->copy();
            }
        }

        // Si on n'a pas encore 3 suggestions, proposer après la dernière affectation
        if (count($suggestions) < 3 && $currentSlot->lte($searchEnd)) {
            $proposedEnd = $currentSlot->copy()->addHours($requestedDuration);
            if ($proposedEnd->lte($searchEnd)) {
                $suggestions[] = [
                    'start' => $currentSlot->format('Y-m-d\TH:i'),
                    'end' => $proposedEnd->format('Y-m-d\TH:i'),
                    'description' => 'Disponible du ' . $currentSlot->format('d/m/Y H:i') . ' au ' . $proposedEnd->format('d/m/Y H:i')
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Trouve le prochain créneau libre de durée donnée - ENTERPRISE-GRADE
     *
     * Algorithme robuste similaire à generateSuggestions() mais :
     * - Retourne le PREMIER créneau disponible uniquement
     * - Recherche sur 30 jours (plus large que les suggestions)
     * - Gère correctement les affectations indéterminées
     */
    public function findNextAvailableSlot(
        int $vehicleId,
        int $driverId,
        int $durationHours = 24,
        ?int $organizationId = null
    ): ?array {
        $organizationId = $organizationId ?? auth()->user()->organization_id;

        $searchStart = now();
        $searchEnd = now()->addDays(30);

        // Récupérer toutes les affectations actives ou futures
        $vehicleAssignments = Assignment::where('organization_id', $organizationId)
            ->where('vehicle_id', $vehicleId)
            ->where(function ($q) use ($searchStart) {
                $q->whereNull('end_datetime')
                  ->orWhere('end_datetime', '>=', $searchStart);
            })
            ->orderBy('start_datetime')
            ->get();

        $driverAssignments = Assignment::where('organization_id', $organizationId)
            ->where('driver_id', $driverId)
            ->where(function ($q) use ($searchStart) {
                $q->whereNull('end_datetime')
                  ->orWhere('end_datetime', '>=', $searchStart);
            })
            ->orderBy('start_datetime')
            ->get();

        // Fusionner et trier
        $allAssignments = $vehicleAssignments->merge($driverAssignments)
            ->unique('id')
            ->sortBy('start_datetime')
            ->values();

        $currentSlot = $searchStart->copy();

        // Trouver le premier créneau disponible
        foreach ($allAssignments as $assignment) {
            $assignmentStart = $assignment->start_datetime;
            $assignmentEnd = $assignment->end_datetime ?? Carbon::create(2099, 12, 31);

            // Vérifier si la durée demandée rentre avant cette affectation
            if ($currentSlot->lt($assignmentStart)) {
                $proposedEnd = $currentSlot->copy()->addHours($durationHours);

                if ($proposedEnd->lte($assignmentStart)) {
                    return [
                        'start' => $currentSlot->format('Y-m-d\TH:i'),
                        'end' => $proposedEnd->format('Y-m-d\TH:i'),
                        'start_formatted' => $currentSlot->format('d/m/Y H:i'),
                        'end_formatted' => $proposedEnd->format('d/m/Y H:i')
                    ];
                }
            }

            // Avancer au prochain créneau possible
            if ($assignmentEnd->gt($currentSlot)) {
                $currentSlot = $assignmentEnd->copy();
            }
        }

        // Si aucun conflit, retourner le créneau immédiat
        if ($currentSlot->lte($searchEnd)) {
            $proposedEnd = $currentSlot->copy()->addHours($durationHours);
            return [
                'start' => $currentSlot->format('Y-m-d\TH:i'),
                'end' => $proposedEnd->format('Y-m-d\TH:i'),
                'start_formatted' => $currentSlot->format('d/m/Y H:i'),
                'end_formatted' => $proposedEnd->format('d/m/Y H:i')
            ];
        }

        // Aucun créneau disponible dans les 30 prochains jours
        return null;
    }

    /**
     * ⚡ MÉTHODE POUR LIVEWIRE - Vérifie les conflits avec format simplifié
     *
     * Cette méthode est spécifiquement conçue pour AssignmentWizard Livewire.
     * Elle accepte des strings datetime et retourne un tableau simple de conflits.
     *
     * @param int $vehicleId ID du véhicule
     * @param int $driverId ID du chauffeur
     * @param string $startDatetime Date/heure début (format: 'Y-m-d\TH:i')
     * @param string|null $endDatetime Date/heure fin ou null si indéterminée
     * @param int|null $excludeId ID affectation à exclure (pour édition)
     * @return array Tableau de conflits formatés
     */
    public function checkConflicts(
        int $vehicleId,
        int $driverId,
        string $startDatetime,
        ?string $endDatetime = null,
        ?int $excludeId = null
    ): array {
        // Conversion strings → Carbon avec gestion multi-tenant
        $organizationId = auth()->check() ? auth()->user()->organization_id : null;

        if (!$organizationId) {
            throw new \RuntimeException('User must be authenticated to check conflicts');
        }

        try {
            $start = Carbon::parse($startDatetime);
            $end = $endDatetime ? Carbon::parse($endDatetime) : null;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid datetime format: ' . $e->getMessage());
        }

        // Utiliser la méthode principale checkOverlap
        $result = $this->checkOverlap(
            $vehicleId,
            $driverId,
            $start,
            $end,
            $excludeId,
            $organizationId
        );

        // Retourner seulement les conflits (pas suggestions) pour Livewire
        return $result['conflicts'];
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