<?php

namespace App\Listeners;

use App\Events\VehicleStatusChanged;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * 🔔 CRITICAL VEHICLE STATUS CHANGE LISTENER
 *
 * Listener exemple pour réagir aux changements critiques de statut véhicule.
 * Envoie des notifications aux gestionnaires de flotte.
 *
 * Use Cases:
 * - Véhicule EN_PANNE → Alert gestionnaire + mécaniciens
 * - Véhicule REFORME → Alert direction + comptabilité
 * - Véhicule non-opérationnel → Update planning automatique
 *
 * Configuration:
 * - Enregistrer dans EventServiceProvider
 * - Utilise queue pour performance (implements ShouldQueue)
 * - Retry automatique en cas d'échec
 *
 * @version 1.0-Enterprise
 */
class NotifyOnCriticalVehicleStatusChange implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public $backoff = [60, 180, 600];

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VehicleStatusChanged $event): void
    {
        // Vérifier si le changement est critique
        if (!$event->isCritical()) {
            return;
        }

        Log::info('Critical vehicle status change detected', [
            'vehicle_id' => $event->vehicle->id,
            'registration' => $event->vehicle->registration_plate,
            'from_status' => $event->fromStatus?->label(),
            'to_status' => $event->toStatus->label(),
            'reason' => $event->reason,
        ]);

        // Récupérer les gestionnaires à notifier
        $managers = $this->getManagers ToNotify($event);

        if ($managers->isEmpty()) {
            Log::warning('No managers found to notify for critical vehicle status change', [
                'vehicle_id' => $event->vehicle->id,
            ]);
            return;
        }

        // Préparer le message de notification
        $message = $this->buildNotificationMessage($event);

        // Envoyer les notifications
        foreach ($managers as $manager) {
            // TODO: Implémenter votre propre notification
            // Notification::send($manager, new CriticalVehicleStatusNotification($event, $message));

            Log::info('Manager notified of critical vehicle status', [
                'manager_id' => $manager->id,
                'vehicle_id' => $event->vehicle->id,
                'status' => $event->toStatus->value,
            ]);
        }
    }

    /**
     * Récupère les gestionnaires à notifier
     *
     * @param VehicleStatusChanged $event
     * @return \Illuminate\Support\Collection
     */
    protected function getManagersToNotify(VehicleStatusChanged $event)
    {
        // Récupérer les utilisateurs avec permission 'manage-fleet' ou rôle 'Fleet Manager'
        return User::where('organization_id', $event->vehicle->organization_id)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Fleet Manager', 'Gestionnaire Flotte']);
            })
            ->orWhereHas('permissions', function ($query) {
                $query->where('name', 'manage-fleet');
            })
            ->get();
    }

    /**
     * Construit le message de notification
     *
     * @param VehicleStatusChanged $event
     * @return string
     */
    protected function buildNotificationMessage(VehicleStatusChanged $event): string
    {
        $vehicleName = $event->vehicle->vehicle_name ?? $event->vehicle->registration_plate;
        $statusLabel = $event->toStatus->label();
        $reason = $event->reason ?? 'Non spécifiée';

        $message = "🚨 Alerte Critique - Véhicule {$vehicleName}\n\n";
        $message .= "Nouveau statut: {$statusLabel}\n";
        $message .= "Raison: {$reason}\n";

        if ($event->fromStatus) {
            $message .= "Ancien statut: {$event->fromStatus->label()}\n";
        }

        $message .= "\nDate: " . $event->statusHistory->changed_at->format('d/m/Y à H:i');

        if ($event->statusHistory->changedBy) {
            $message .= "\nPar: " . $event->statusHistory->changedBy->name;
        }

        return $message;
    }

    /**
     * Handle a job failure.
     */
    public function failed(VehicleStatusChanged $event, \Throwable $exception): void
    {
        Log::error('Failed to process critical vehicle status change notification', [
            'vehicle_id' => $event->vehicle->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
