<?php

namespace App\Events;

use App\Models\Vehicle;
use App\Models\StatusHistory;
use App\Enums\VehicleStatusEnum;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 📡 VEHICLE STATUS CHANGED EVENT
 *
 * Événement déclenché lors d'un changement de statut de véhicule.
 * Permet de réagir aux transitions via Listeners ou Observers.
 *
 * Use Cases:
 * - Notifications automatiques aux gestionnaires
 * - Mise à jour dashboards temps réel (broadcasting)
 * - Déclenchement workflows automatiques
 * - Analytics et reporting
 * - Intégrations externes (webhook, API)
 *
 * @version 1.0-Enterprise
 */
class VehicleStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Vehicle $vehicle;
    public ?VehicleStatusEnum $fromStatus;
    public VehicleStatusEnum $toStatus;
    public StatusHistory $statusHistory;
    public ?string $reason;
    public ?array $metadata;

    /**
     * Create a new event instance.
     */
    public function __construct(
        Vehicle $vehicle,
        ?VehicleStatusEnum $fromStatus,
        VehicleStatusEnum $toStatus,
        StatusHistory $statusHistory,
        ?string $reason = null,
        ?array $metadata = null
    ) {
        $this->vehicle = $vehicle;
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
            new PrivateChannel('organization.' . $this->vehicle->organization_id),
            new PrivateChannel('vehicle.' . $this->vehicle->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'vehicle.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'vehicle_name' => $this->vehicle->vehicle_name ?? $this->vehicle->registration_plate,
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
            'vehicle:' . $this->vehicle->id,
            'organization:' . $this->vehicle->organization_id,
            'status:' . $this->toStatus->value,
        ];
    }

    /**
     * Determine if this is a critical status change (requires attention)
     */
    public function isCritical(): bool
    {
        return in_array($this->toStatus, [
            VehicleStatusEnum::EN_PANNE,
            VehicleStatusEnum::REFORME,
        ]);
    }

    /**
     * Check if vehicle became operational
     */
    public function becameOperational(): bool
    {
        return $this->toStatus->isOperational() &&
               ($this->fromStatus === null || !$this->fromStatus->isOperational());
    }

    /**
     * Check if vehicle became non-operational
     */
    public function becameNonOperational(): bool
    {
        return !$this->toStatus->isOperational() &&
               ($this->fromStatus !== null && $this->fromStatus->isOperational());
    }
}
