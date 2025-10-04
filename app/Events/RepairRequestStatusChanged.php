<?php

namespace App\Events;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * RepairRequestStatusChanged - Event déclenché lors du changement de statut
 *
 * Déclenché automatiquement par:
 * - RepairRequestService->approveBySupervisor()
 * - RepairRequestService->rejectBySupervisor()
 * - RepairRequestService->approveByFleetManager()
 * - RepairRequestService->rejectByFleetManager()
 *
 * Écouté par:
 * - SendRepairRequestNotifications listener
 *
 * @version 1.0-Enterprise
 */
class RepairRequestStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Constructor.
     */
    public function __construct(
        public RepairRequest $repairRequest,
        public string $oldStatus,
        public string $newStatus,
        public User $triggeredBy
    ) {
    }
}
