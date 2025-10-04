<?php

namespace App\Events;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * RepairRequestApproved - Event déclenché lors de l'approbation
 *
 * Déclenché par:
 * - RepairRequestService->approveBySupervisor() [level: supervisor]
 * - RepairRequestService->approveByFleetManager() [level: fleet_manager]
 *
 * Utilisé pour:
 * - Tracking des approbations
 * - Analytics
 * - Webhooks externes
 *
 * @version 1.0-Enterprise
 */
class RepairRequestApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Constructor.
     */
    public function __construct(
        public RepairRequest $repairRequest,
        public User $approver,
        public string $level // 'supervisor' or 'fleet_manager'
    ) {
    }
}
