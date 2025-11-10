<?php

namespace App\Events;

use App\Models\Driver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement de changement de statut d'un chauffeur
 * 
 * @package App\Events
 * @version 1.0.0
 */
class DriverStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?Driver $driver;
    public string $newStatus;
    public array $metadata;

    public function __construct(?Driver $driver, string $newStatus, array $metadata = [])
    {
        $this->driver = $driver;
        $this->newStatus = $newStatus;
        $this->metadata = array_merge([
            'changed_at' => now()->toISOString(),
            'organization_id' => $driver?->organization_id
        ], $metadata);
    }

    public function broadcastOn(): array
    {
        if (!$this->driver) {
            return [];
        }

        return [
            new Channel('organization.' . $this->driver->organization_id),
            new Channel('drivers.' . $this->driver->organization_id),
            new Channel('driver.' . $this->driver->id)
        ];
    }

    public function broadcastAs(): string
    {
        return 'driver.status.changed';
    }

    public function broadcastWith(): array
    {
        if (!$this->driver) {
            return [];
        }

        return [
            'driver' => [
                'id' => $this->driver->id,
                'full_name' => $this->driver->full_name,
                'first_name' => $this->driver->first_name,
                'last_name' => $this->driver->last_name,
                'license_number' => $this->driver->license_number
            ],
            'new_status' => $this->newStatus,
            'is_available' => $this->newStatus === 'available',
            'metadata' => $this->metadata
        ];
    }
}
