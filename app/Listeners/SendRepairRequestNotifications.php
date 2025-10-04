<?php

namespace App\Listeners;

use App\Events\RepairRequestStatusChanged;
use App\Models\RepairRequest;
use App\Models\User;
use App\Notifications\RepairRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * SendRepairRequestNotifications - Listener pour notifications automatiques
 *
 * Écoute: RepairRequestStatusChanged
 *
 * Logique de notification:
 * - pending_supervisor → Notifier superviseur
 * - approved_supervisor → Notifier chauffeur
 * - pending_fleet_manager → Notifier tous les gestionnaires de flotte
 * - approved_final → Notifier chauffeur + superviseur
 * - rejected_supervisor → Notifier chauffeur
 * - rejected_final → Notifier chauffeur + superviseur
 *
 * @version 1.0-Enterprise
 */
class SendRepairRequestNotifications implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RepairRequestStatusChanged $event): void
    {
        $repairRequest = $event->repairRequest;
        $newStatus = $event->newStatus;

        try {
            match ($newStatus) {
                RepairRequest::STATUS_PENDING_SUPERVISOR => $this->notifySupervisor($repairRequest),
                RepairRequest::STATUS_APPROVED_SUPERVISOR,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER => $this->handleSupervisorApproval($repairRequest),
                RepairRequest::STATUS_APPROVED_FINAL => $this->handleFinalApproval($repairRequest),
                RepairRequest::STATUS_REJECTED_SUPERVISOR => $this->handleSupervisorRejection($repairRequest),
                RepairRequest::STATUS_REJECTED_FINAL => $this->handleFinalRejection($repairRequest),
                default => null,
            };
        } catch (\Exception $e) {
            Log::error('SendRepairRequestNotifications: Error sending notifications', [
                'repair_request_id' => $repairRequest->id,
                'status' => $newStatus,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 📢 NOTIFIER LE SUPERVISEUR (Nouvelle demande)
     */
    protected function notifySupervisor(RepairRequest $repairRequest): void
    {
        if (!$repairRequest->supervisor_id) {
            return;
        }

        $supervisor = User::find($repairRequest->supervisor_id);

        if ($supervisor) {
            $supervisor->notify(
                new RepairRequestNotification(
                    $repairRequest,
                    RepairRequestNotification::TYPE_NEW_REQUEST
                )
            );

            Log::info('Notification sent to supervisor', [
                'repair_request_id' => $repairRequest->id,
                'supervisor_id' => $supervisor->id,
            ]);
        }
    }

    /**
     * ✅ APPROBATION SUPERVISEUR
     * - Notifier chauffeur (approbation niveau 1)
     * - Notifier gestionnaires de flotte (en attente validation)
     */
    protected function handleSupervisorApproval(RepairRequest $repairRequest): void
    {
        // 1. Notifier le chauffeur
        $this->notifyDriver(
            $repairRequest,
            RepairRequestNotification::TYPE_APPROVED_LEVEL_1
        );

        // 2. Notifier tous les gestionnaires de flotte de l'organisation
        $this->notifyFleetManagers($repairRequest);
    }

    /**
     * ✅ APPROBATION FINALE
     * - Notifier chauffeur (approbation finale)
     * - Notifier superviseur (pour info)
     */
    protected function handleFinalApproval(RepairRequest $repairRequest): void
    {
        // 1. Notifier le chauffeur
        $this->notifyDriver(
            $repairRequest,
            RepairRequestNotification::TYPE_APPROVED_FINAL
        );

        // 2. Notifier le superviseur
        if ($repairRequest->supervisor_id) {
            $supervisor = User::find($repairRequest->supervisor_id);

            if ($supervisor) {
                $supervisor->notify(
                    new RepairRequestNotification(
                        $repairRequest,
                        RepairRequestNotification::TYPE_APPROVED_FINAL
                    )
                );
            }
        }
    }

    /**
     * ❌ REJET SUPERVISEUR
     * - Notifier chauffeur uniquement
     */
    protected function handleSupervisorRejection(RepairRequest $repairRequest): void
    {
        $this->notifyDriver(
            $repairRequest,
            RepairRequestNotification::TYPE_REJECTED
        );
    }

    /**
     * ❌ REJET FINAL
     * - Notifier chauffeur
     * - Notifier superviseur
     */
    protected function handleFinalRejection(RepairRequest $repairRequest): void
    {
        // 1. Notifier le chauffeur
        $this->notifyDriver(
            $repairRequest,
            RepairRequestNotification::TYPE_REJECTED
        );

        // 2. Notifier le superviseur
        if ($repairRequest->supervisor_id) {
            $supervisor = User::find($repairRequest->supervisor_id);

            if ($supervisor) {
                $supervisor->notify(
                    new RepairRequestNotification(
                        $repairRequest,
                        RepairRequestNotification::TYPE_REJECTED
                    )
                );
            }
        }
    }

    /**
     * 👤 NOTIFIER LE CHAUFFEUR
     */
    protected function notifyDriver(RepairRequest $repairRequest, string $type): void
    {
        $driver = $repairRequest->driver;

        if (!$driver || !$driver->user_id) {
            return;
        }

        $user = User::find($driver->user_id);

        if ($user) {
            $user->notify(
                new RepairRequestNotification($repairRequest, $type)
            );

            Log::info('Notification sent to driver', [
                'repair_request_id' => $repairRequest->id,
                'driver_id' => $driver->id,
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }
    }

    /**
     * 👥 NOTIFIER TOUS LES GESTIONNAIRES DE FLOTTE
     */
    protected function notifyFleetManagers(RepairRequest $repairRequest): void
    {
        $fleetManagers = User::where('organization_id', $repairRequest->organization_id)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Gestionnaire Flotte', 'Admin', 'Super Admin']);
            })
            ->get();

        foreach ($fleetManagers as $fleetManager) {
            $fleetManager->notify(
                new RepairRequestNotification(
                    $repairRequest,
                    RepairRequestNotification::TYPE_PENDING_APPROVAL_L2
                )
            );
        }

        Log::info('Notifications sent to fleet managers', [
            'repair_request_id' => $repairRequest->id,
            'count' => $fleetManagers->count(),
        ]);
    }
}
