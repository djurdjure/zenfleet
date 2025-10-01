<?php

namespace App\Jobs\Maintenance;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceAlert;
use App\Models\Organization;
use App\Notifications\Maintenance\MaintenanceAlertNotification;
use App\Notifications\Maintenance\MaintenanceEscalationNotification;
use Carbon\Carbon;

/**
 * Job pour vérifier quotidiennement les planifications de maintenance
 * et créer les alertes nécessaires
 */
class CheckMaintenanceSchedulesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // 5 minutes
    public int $tries = 3;

    protected ?int $organizationId;
    protected bool $sendNotifications;

    /**
     * Constructeur du job
     */
    public function __construct(?int $organizationId = null, bool $sendNotifications = true)
    {
        $this->organizationId = $organizationId;
        $this->sendNotifications = $sendNotifications;
    }

    /**
     * Exécution du job
     */
    public function handle(): void
    {
        Log::info('Starting maintenance schedules check', [
            'organization_id' => $this->organizationId,
            'send_notifications' => $this->sendNotifications,
        ]);

        $organizations = $this->organizationId
            ? Organization::where('id', $this->organizationId)->get()
            : Organization::active()->get();

        $totalAlertsCreated = 0;
        $totalEscalationsNeeded = 0;

        foreach ($organizations as $organization) {
            try {
                $result = $this->processOrganization($organization);
                $totalAlertsCreated += $result['alerts_created'];
                $totalEscalationsNeeded += $result['escalations_needed'];
            } catch (\Exception $e) {
                Log::error('Error processing organization maintenance schedules', [
                    'organization_id' => $organization->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info('Completed maintenance schedules check', [
            'organizations_processed' => $organizations->count(),
            'total_alerts_created' => $totalAlertsCreated,
            'total_escalations_needed' => $totalEscalationsNeeded,
        ]);
    }

    /**
     * Traiter une organisation
     */
    protected function processOrganization(Organization $organization): array
    {
        $alertsCreated = 0;
        $escalationsNeeded = 0;

        // Obtenir toutes les planifications actives de l'organisation
        $schedules = MaintenanceSchedule::where('organization_id', $organization->id)
            ->active()
            ->with(['vehicle', 'maintenanceType'])
            ->get();

        foreach ($schedules as $schedule) {
            try {
                // Vérifier si une alerte est nécessaire
                if ($this->shouldCreateAlert($schedule)) {
                    $alert = $schedule->createAlertIfNeeded();

                    if ($alert) {
                        $alertsCreated++;

                        // Envoyer notification si configuré
                        if ($this->sendNotifications) {
                            $this->sendAlertNotification($alert);
                        }
                    }
                }

                // Vérifier les escalades nécessaires
                $existingAlerts = $schedule->alerts()
                    ->unacknowledged()
                    ->get();

                foreach ($existingAlerts as $existingAlert) {
                    if ($existingAlert->needsEscalation()) {
                        $escalationsNeeded++;

                        if ($this->sendNotifications) {
                            $this->sendEscalationNotification($existingAlert);
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::error('Error processing maintenance schedule', [
                    'schedule_id' => $schedule->id,
                    'organization_id' => $organization->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Nettoyer les anciennes alertes acquittées (plus de 30 jours)
        $this->cleanupOldAlerts($organization);

        return [
            'alerts_created' => $alertsCreated,
            'escalations_needed' => $escalationsNeeded,
        ];
    }

    /**
     * Déterminer si une alerte doit être créée
     */
    protected function shouldCreateAlert(MaintenanceSchedule $schedule): bool
    {
        $today = Carbon::today();
        $currentMileage = $schedule->vehicle?->current_mileage ?? 0;

        // Vérifier si déjà en retard
        if (($schedule->next_due_date && $schedule->next_due_date->lt($today)) ||
            ($schedule->next_due_mileage && $schedule->next_due_mileage < $currentMileage)) {
            return true;
        }

        // Vérifier si dans la période d'alerte
        $alertDate = $today->copy()->addDays($schedule->alert_days_before);
        $alertMileage = $currentMileage + $schedule->alert_km_before;

        if (($schedule->next_due_date && $schedule->next_due_date->lte($alertDate)) ||
            ($schedule->next_due_mileage && $schedule->next_due_mileage <= $alertMileage)) {

            // Vérifier qu'il n'y a pas déjà une alerte non acquittée récente
            $existingAlert = $schedule->alerts()
                ->unacknowledged()
                ->where('created_at', '>=', $today->subDays(1))
                ->first();

            return !$existingAlert;
        }

        return false;
    }

    /**
     * Envoyer une notification d'alerte
     */
    protected function sendAlertNotification(MaintenanceAlert $alert): void
    {
        try {
            // Obtenir les utilisateurs à notifier selon la priorité
            $users = $this->getUsersToNotify($alert);

            foreach ($users as $user) {
                $user->notify(new MaintenanceAlertNotification($alert));
            }

            Log::info('Maintenance alert notification sent', [
                'alert_id' => $alert->id,
                'priority' => $alert->priority,
                'users_notified' => $users->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending maintenance alert notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envoyer une notification d'escalade
     */
    protected function sendEscalationNotification(MaintenanceAlert $alert): void
    {
        try {
            // Obtenir les managers/superviseurs pour escalade
            $managers = $this->getManagersToNotify($alert);

            foreach ($managers as $manager) {
                $manager->notify(new MaintenanceEscalationNotification($alert));
            }

            Log::warning('Maintenance alert escalation sent', [
                'alert_id' => $alert->id,
                'priority' => $alert->priority,
                'age_hours' => $alert->age_in_hours,
                'managers_notified' => $managers->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending maintenance escalation notification', [
                'alert_id' => $alert->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtenir les utilisateurs à notifier selon la priorité
     */
    protected function getUsersToNotify(MaintenanceAlert $alert): \Illuminate\Database\Eloquent\Collection
    {
        $organizationId = $alert->organization_id;

        switch ($alert->priority) {
            case 'critical':
                // Notifier tous les gestionnaires et superviseurs
                return \App\Models\User::where('organization_id', $organizationId)
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['admin', 'fleet_manager', 'supervisor']);
                    })
                    ->where('is_active', true)
                    ->get();

            case 'high':
                // Notifier les gestionnaires de flotte
                return \App\Models\User::where('organization_id', $organizationId)
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['admin', 'fleet_manager']);
                    })
                    ->where('is_active', true)
                    ->get();

            case 'medium':
                // Notifier les responsables maintenance
                return \App\Models\User::where('organization_id', $organizationId)
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['admin', 'fleet_manager', 'maintenance_manager']);
                    })
                    ->where('is_active', true)
                    ->get();

            default:
                // Notifier uniquement les responsables maintenance
                return \App\Models\User::where('organization_id', $organizationId)
                    ->whereHas('roles', function ($query) {
                        $query->whereIn('name', ['maintenance_manager']);
                    })
                    ->where('is_active', true)
                    ->get();
        }
    }

    /**
     * Obtenir les managers pour escalade
     */
    protected function getManagersToNotify(MaintenanceAlert $alert): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\User::where('organization_id', $alert->organization_id)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['admin', 'fleet_manager']);
            })
            ->where('is_active', true)
            ->get();
    }

    /**
     * Nettoyer les anciennes alertes acquittées
     */
    protected function cleanupOldAlerts(Organization $organization): void
    {
        $cutoffDate = Carbon::now()->subDays(30);

        $deletedCount = MaintenanceAlert::where('organization_id', $organization->id)
            ->acknowledged()
            ->where('acknowledged_at', '<', $cutoffDate)
            ->delete();

        if ($deletedCount > 0) {
            Log::info('Cleaned up old maintenance alerts', [
                'organization_id' => $organization->id,
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate,
            ]);
        }
    }

    /**
     * Gestion des échecs du job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Maintenance schedules check job failed', [
            'organization_id' => $this->organizationId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // Notifier les administrateurs système en cas d'échec critique
        try {
            $systemAdmins = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'system_admin');
            })->get();

            foreach ($systemAdmins as $admin) {
                $admin->notify(new \App\Notifications\System\JobFailedNotification(
                    'Maintenance Schedules Check',
                    $exception->getMessage()
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify system admins of job failure', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}