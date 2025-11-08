<?php

namespace App\Events;

use App\Models\Driver;
use App\Models\StatusHistory;
use App\Enums\DriverStatusEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 📡 DRIVER STATUS CHANGED EVENT
 *
 * Événement déclenché lors d'un changement de statut de chauffeur.
 * Permet de réagir aux transitions via Listeners ou Observers.
 *
 * Use Cases:
 * - Notifications RH automatiques
 * - Mise à jour planning temps réel
 * - Alertes responsables flotte
 * - Synchronisation systèmes paie/RH
 * - Analytics disponibilité chauffeurs
 *
 * @version 1.0-Enterprise
 */
class DriverStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Driver $driver;
    public ?DriverStatusEnum $fromStatus;
    public DriverStatusEnum $toStatus;
    public StatusHistory $statusHistory;
    public ?string $reason;
    public ?array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Driver $driver,
        ?DriverStatusEnum $fromStatus,
        DriverStatusEnum $toStatus,
        StatusHistory $statusHistory,
        ?string $reason = null,
        ?array $metadata = null
    ) {
        $this->driver = $driver;
        $this->fromStatus = $fromStatus;
        $this->toStatus = $toStatus;
        $this->statusHistory = $statusHistory;
        $this->reason = $reason;
        $this->metadata = $metadata;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.' . $this->driver->organization_id),
            new PrivateChannel('driver.' . $this->driver->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'driver.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'driver_id' => $this->driver->id,
            'driver_name' => $this->driver->full_name,
            'from_status' => $this->fromStatus?->value,
            'from_status_label' => $this->fromStatus?->label(),
            'to_status' => $this->toStatus->value,
            'to_status_label' => $this->toStatus->label(),
            'to_status_color' => $this->toStatus->hexColor(),
            'reason' => $this->reason,
            'changed_by' => $this->statusHistory->changedBy?->name,
            'changed_at' => $this->statusHistory->changed_at->toIso8601String(),
        ];
    }

    /**
     * Get the tags that should be assigned to the event.
     */
    public function tags(): array
    {
        return [
            'driver:' . $this->driver->id,
            'organization:' . $this->driver->organization_id,
            'status:' . $this->toStatus->value,
        ];
    }

    /**
     * Determine if this is a critical status change
     */
    public function isCritical(): bool
    {
        return in_array($this->toStatus, [
            DriverStatusEnum::AUTRE, // Peut inclure sanctions, maladie
        ]);
    }

    /**
     * Check if driver became available
     */
    public function becameAvailable(): bool
    {
        return $this->toStatus === DriverStatusEnum::DISPONIBLE &&
               ($this->fromStatus === null || $this->fromStatus !== DriverStatusEnum::DISPONIBLE);
    }

    /**
     * Check if driver became unavailable
     */
    public function becameUnavailable(): bool
    {
        return $this->toStatus !== DriverStatusEnum::DISPONIBLE &&
               ($this->fromStatus !== null && $this->fromStatus === DriverStatusEnum::DISPONIBLE);
    }

    /**
     * Check if driver is on leave
     */
    public function isOnLeave(): bool
    {
        return $this->toStatus === DriverStatusEnum::EN_CONGE;
    }
}
