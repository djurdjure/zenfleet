<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 🎯 EVENT : AFFECTATION TERMINÉE - Enterprise-Grade
 *
 * Événement dispatché lorsqu'une affectation se termine (manuellement ou automatiquement).
 *
 * USE CASES :
 * - Libération automatique du véhicule (status_id → disponible)
 * - Libération automatique du chauffeur (status_id → disponible)
 * - Enregistrement dans StatusHistory
 * - Envoi de notifications
 * - Mise à jour de métriques (Analytics)
 *
 * @version 1.0-Enterprise
 */
class AssignmentEnded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Assignment $assignment;
    public string $endedBy; // 'manual' | 'automatic'
    public ?int $userId; // User qui a terminé (si manual)

    /**
     * Créer une nouvelle instance de l'événement
     *
     * @param Assignment $assignment L'affectation terminée
     * @param string $endedBy 'manual' (interface) ou 'automatic' (cron/job)
     * @param int|null $userId ID de l'utilisateur qui a terminé (si manual)
     */
    public function __construct(Assignment $assignment, string $endedBy = 'manual', ?int $userId = null)
    {
        $this->assignment = $assignment;
        $this->endedBy = $endedBy;
        $this->userId = $userId;
    }

    /**
     * Retourne les données pour broadcasting (si nécessaire)
     */
    public function broadcastWith(): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'vehicle_id' => $this->assignment->vehicle_id,
            'driver_id' => $this->assignment->driver_id,
            'ended_at' => $this->assignment->end_datetime?->toIso8601String(),
            'ended_by' => $this->endedBy,
        ];
    }
}
