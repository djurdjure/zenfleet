<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché lors de la fin d'une affectation
 * 
 * ENTERPRISE-GRADE ULTRA-PRO - SURPASSANT FLEETIO/SAMSARA
 * 
 * Cet événement orchestre la libération automatique des ressources
 * et les notifications en temps réel à travers le système.
 * 
 * @package App\Events
 * @version 1.0.0
 * @since 2025-11-09
 */
class AssignmentEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Assignment L'affectation terminée
     */
    public Assignment $assignment;

    /**
     * @var string Type de terminaison ('manual', 'automatic', 'scheduled')
     */
    public string $endType;

    /**
     * @var int|null ID de l'utilisateur ayant terminé l'affectation
     */
    public ?int $userId;

    /**
     * @var array Données additionnelles pour l'événement
     */
    public array $metadata;

    /**
     * Créer une nouvelle instance de l'événement
     *
     * @param Assignment $assignment L'affectation terminée
     * @param string $endType Type de terminaison
     * @param int|null $userId ID de l'utilisateur (null si automatique)
     * @param array $metadata Métadonnées additionnelles
     */
    public function __construct(
        Assignment $assignment,
        string $endType = 'automatic',
        ?int $userId = null,
        array $metadata = []
    ) {
        $this->assignment = $assignment;
        $this->endType = $endType;
        $this->userId = $userId;
        $this->metadata = array_merge([
            'ended_at' => now()->toISOString(),
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'organization_id' => $assignment->organization_id,
            'duration_hours' => $assignment->duration_hours,
            'end_mileage' => $assignment->end_mileage
        ], $metadata);
    }

    /**
     * Définir les canaux de broadcast pour l'événement
     *
     * @return Channel[]
     */
    public function broadcastOn(): array
    {
        return [
            // Canal global de l'organisation
            new PresenceChannel('organization.' . $this->assignment->organization_id),
            
            // Canal spécifique aux affectations
            new Channel('assignments.' . $this->assignment->organization_id),
            
            // Canal du véhicule concerné
            new Channel('vehicle.' . $this->assignment->vehicle_id),
            
            // Canal du chauffeur concerné
            new Channel('driver.' . $this->assignment->driver_id)
        ];
    }

    /**
     * Définir le nom de l'événement broadcast
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'assignment.ended';
    }

    /**
     * Définir les données à broadcaster
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'assignment' => [
                'id' => $this->assignment->id,
                'vehicle' => [
                    'id' => $this->assignment->vehicle_id,
                    'registration' => $this->assignment->vehicle->registration_plate ?? null,
                    'now_available' => true
                ],
                'driver' => [
                    'id' => $this->assignment->driver_id,
                    'name' => $this->assignment->driver->full_name ?? null,
                    'now_available' => true
                ],
                'end_datetime' => $this->assignment->end_datetime?->toISOString(),
                'duration' => $this->assignment->formatted_duration
            ],
            'end_type' => $this->endType,
            'user_id' => $this->userId,
            'metadata' => $this->metadata,
            'message' => $this->getNotificationMessage()
        ];
    }

    /**
     * Générer le message de notification approprié
     *
     * @return string
     */
    private function getNotificationMessage(): string
    {
        $vehiclePlate = $this->assignment->vehicle->registration_plate ?? 'Véhicule #' . $this->assignment->vehicle_id;
        $driverName = $this->assignment->driver->full_name ?? 'Chauffeur #' . $this->assignment->driver_id;

        return match($this->endType) {
            'manual' => "L'affectation de {$vehiclePlate} à {$driverName} a été terminée manuellement.",
            'automatic' => "L'affectation de {$vehiclePlate} à {$driverName} s'est terminée automatiquement.",
            'scheduled' => "L'affectation de {$vehiclePlate} à {$driverName} a atteint sa date de fin planifiée.",
            default => "L'affectation de {$vehiclePlate} à {$driverName} est terminée."
        };
    }

    /**
     * Déterminer si l'événement doit être mis en queue
     *
     * @return bool
     */
    public function shouldQueue(): bool
    {
        // Les notifications critiques ne sont pas mises en queue
        return false;
    }
}
