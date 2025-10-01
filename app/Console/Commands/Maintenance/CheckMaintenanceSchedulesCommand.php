<?php

namespace App\Console\Commands\Maintenance;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\Maintenance\CheckMaintenanceSchedulesJob;
use App\Models\Organization;

/**
 * Commande Artisan pour vérifier les planifications de maintenance
 * Utilisée pour les tâches cron et l'exécution manuelle
 */
class CheckMaintenanceSchedulesCommand extends Command
{
    /**
     * Signature de la commande
     */
    protected $signature = 'maintenance:check-schedules
                            {--organization= : ID de l\'organisation à traiter (optionnel)}
                            {--no-notifications : Ne pas envoyer de notifications}
                            {--force : Forcer l\'exécution même si une autre instance est en cours}
                            {--dry-run : Afficher ce qui serait fait sans exécuter}';

    /**
     * Description de la commande
     */
    protected $description = 'Vérifier les planifications de maintenance et créer les alertes nécessaires';

    /**
     * Exécution de la commande
     */
    public function handle(): int
    {
        $this->info('🔧 Démarrage de la vérification des planifications de maintenance...');

        $organizationId = $this->option('organization');
        $sendNotifications = !$this->option('no-notifications');
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Vérifier si une autre instance est en cours (sauf si --force)
        if (!$force && $this->isAnotherInstanceRunning()) {
            $this->warn('Une autre instance de cette commande est déjà en cours d\'exécution.');
            $this->line('Utilisez --force pour forcer l\'exécution.');
            return self::FAILURE;
        }

        try {
            if ($isDryRun) {
                return $this->dryRun($organizationId);
            }

            $this->executeChecks($organizationId, $sendNotifications);
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la vérification des maintenances : ' . $e->getMessage());
            Log::error('Maintenance check command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Exécuter les vérifications
     */
    protected function executeChecks(?int $organizationId, bool $sendNotifications): void
    {
        if ($organizationId) {
            $organization = Organization::findOrFail($organizationId);
            $this->info("Traitement de l'organisation : {$organization->name}");
        } else {
            $organizationCount = Organization::active()->count();
            $this->info("Traitement de {$organizationCount} organisation(s)");
        }

        $startTime = microtime(true);

        // Dispatcher le job
        CheckMaintenanceSchedulesJob::dispatch($organizationId, $sendNotifications);

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        $this->info("✅ Vérification terminée en {$executionTime}ms");

        if (!$sendNotifications) {
            $this->warn('ℹ️  Les notifications ont été désactivées pour cette exécution');
        }

        // Afficher un résumé
        $this->showSummary($organizationId);
    }

    /**
     * Mode dry-run - afficher ce qui serait fait
     */
    protected function dryRun(?int $organizationId): int
    {
        $this->warn('🧪 MODE DRY-RUN - Aucune action ne sera effectuée');

        $organizations = $organizationId
            ? Organization::where('id', $organizationId)->get()
            : Organization::active()->get();

        $this->table(
            ['Organisation', 'Planifications actives', 'Alertes non acquittées', 'Actions à effectuer'],
            $organizations->map(function ($org) {
                $activeSchedules = \App\Models\MaintenanceSchedule::where('organization_id', $org->id)
                    ->active()
                    ->count();

                $unacknowledgedAlerts = \App\Models\MaintenanceAlert::where('organization_id', $org->id)
                    ->unacknowledged()
                    ->count();

                $actions = $this->getActionsForOrganization($org);

                return [
                    $org->name,
                    $activeSchedules,
                    $unacknowledgedAlerts,
                    implode(', ', $actions),
                ];
            })->toArray()
        );

        return self::SUCCESS;
    }

    /**
     * Obtenir les actions qui seraient effectuées pour une organisation
     */
    protected function getActionsForOrganization(Organization $organization): array
    {
        $actions = [];

        // Vérifier les planifications nécessitant des alertes
        $schedulesNeedingAlerts = \App\Models\MaintenanceSchedule::where('organization_id', $organization->id)
            ->active()
            ->where(function ($query) {
                $today = \Carbon\Carbon::today();
                $query->where(function ($q) use ($today) {
                    // En retard
                    $q->where('next_due_date', '<', $today)
                      ->orWhereRaw('next_due_mileage < (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id)');
                })->orWhere(function ($q) use ($today) {
                    // Dans la période d'alerte
                    $q->whereRaw('next_due_date <= ? + INTERVAL \'1 day\' * alert_days_before', [$today])
                      ->orWhereRaw('next_due_mileage <= (SELECT current_mileage FROM vehicles WHERE vehicles.id = maintenance_schedules.vehicle_id) + alert_km_before');
                });
            })
            ->count();

        if ($schedulesNeedingAlerts > 0) {
            $actions[] = "Créer {$schedulesNeedingAlerts} alerte(s)";
        }

        // Vérifier les escalades nécessaires
        $escalationsNeeded = \App\Models\MaintenanceAlert::where('organization_id', $organization->id)
            ->unacknowledged()
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('priority', 'critical')
                      ->where('created_at', '<=', now()->subHours(2));
                })->orWhere(function ($q) {
                    $q->where('priority', 'high')
                      ->where('created_at', '<=', now()->subHours(8));
                });
            })
            ->count();

        if ($escalationsNeeded > 0) {
            $actions[] = "Escalader {$escalationsNeeded} alerte(s)";
        }

        return $actions ?: ['Aucune action nécessaire'];
    }

    /**
     * Afficher un résumé des actions effectuées
     */
    protected function showSummary(?int $organizationId): void
    {
        $this->newLine();
        $this->info('📊 RÉSUMÉ');

        $query = \App\Models\MaintenanceAlert::query();
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $todayAlerts = $query->whereDate('created_at', today())->count();
        $unacknowledged = $query->unacknowledged()->count();
        $critical = $query->unacknowledged()->where('priority', 'critical')->count();

        $this->line("• Alertes créées aujourd'hui : {$todayAlerts}");
        $this->line("• Alertes non acquittées : {$unacknowledged}");

        if ($critical > 0) {
            $this->error("• ⚠️  Alertes critiques : {$critical}");
        } else {
            $this->line("• Alertes critiques : {$critical}");
        }

        // Recommandations
        if ($unacknowledged > 10) {
            $this->warn('💡 Recommandation : Réviser les processus d\'acquittement des alertes');
        }

        if ($critical > 0) {
            $this->error('🚨 Action requise : Des alertes critiques nécessitent une attention immédiate');
        }
    }

    /**
     * Vérifier si une autre instance de la commande est en cours
     */
    protected function isAnotherInstanceRunning(): bool
    {
        $lockFile = storage_path('app/locks/maintenance-check.lock');

        if (!file_exists($lockFile)) {
            return false;
        }

        $lockTime = filectime($lockFile);
        $maxAge = 3600; // 1 heure

        // Si le fichier de verrouillage est trop ancien, le supprimer
        if (time() - $lockTime > $maxAge) {
            unlink($lockFile);
            return false;
        }

        return true;
    }

    /**
     * Créer un fichier de verrouillage
     */
    protected function createLockFile(): void
    {
        $lockDir = storage_path('app/locks');

        if (!is_dir($lockDir)) {
            mkdir($lockDir, 0755, true);
        }

        touch(storage_path('app/locks/maintenance-check.lock'));
    }

    /**
     * Supprimer le fichier de verrouillage
     */
    protected function removeLockFile(): void
    {
        $lockFile = storage_path('app/locks/maintenance-check.lock');

        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
}