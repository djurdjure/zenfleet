<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement de changement de statut d'un véhicule
 * 
 * @package App\Events
 * @version 1.0.0
 */
class VehicleStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?Vehicle $vehicle;
    public string $newStatus;
    public array $metadata;

    public function __construct(?Vehicle $vehicle, string $newStatus, array $metadata = [])
    {
        $this->vehicle = $vehicle;
        $this->newStatus = $newStatus;
        $this->metadata = array_merge([
            'changed_at' => now()->toISOString(),
            'organization_id' => $vehicle?->organization_id
        ], $metadata);
    }

    public function broadcastOn(): array
    {
        if (!$this->vehicle) {
            return [];
        }

        return [
            new Channel('organization.' . $this->vehicle->organization_id),
            new Channel('vehicles.' . $this->vehicle->organization_id),
            new Channel('vehicle.' . $this->vehicle->id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'vehicle.status.changed';
    }

    public function broadcastWith(): array
    {
        if (!$this->vehicle) {
            return [];
        }

        return [
            'vehicle' => [
                'id' => $this->vehicle->id,
                'registration_plate' => $this->vehicle->registration_plate,
                'brand' => $this->vehicle->brand,
                'model' => $this->vehicle->model
            ],
            'new_status' => $this->newStatus,
            'is_available' => $this->newStatus === 'available',
            'metadata' => $this->metadata
        ];
    }
}
