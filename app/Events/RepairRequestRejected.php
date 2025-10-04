<?php

namespace App\Events;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * RepairRequestRejected - Event déclenché lors du rejet
 *
 * Déclenché par:
 * - RepairRequestService->rejectBySupervisor() [level: supervisor]
 * - RepairRequestService->rejectByFleetManager() [level: fleet_manager]
 *
 * Utilisé pour:
 * - Tracking des rejets
 * - Analytics (taux de rejet par superviseur/gestionnaire)
 * - Notifications avec raison
 *
 * @version 1.0-Enterprise
 */
class RepairRequestRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Constructor.
     */
    public function __construct(
        public RepairRequest $repairRequest,
        public User $rejector,
        public string $level, // 'supervisor' or 'fleet_manager'
        public string $reason
    ) {
    }
}
