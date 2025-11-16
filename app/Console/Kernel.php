<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * 🚀 ENTERPRISE-GRADE SCHEDULED TASKS
     */
    protected function schedule(Schedule $schedule): void
    {
        // =================================================================
        // 🔄 SYNCHRONISATION AUTOMATIQUE DES AFFECTATIONS (ULTRA-PRO)
        // =================================================================

        // ⚡ Traitement des affectations expirées toutes les 5 minutes
        // Libération automatique des véhicules et chauffeurs
        $schedule->job(new \App\Jobs\ProcessExpiredAssignmentsEnhanced())
            ->everyFiveMinutes()
            ->name('process-expired-assignments')
            ->withoutOverlapping(5)
            ->onSuccess(function () {
                \Log::info('[Scheduler] ✅ Traitement affectations expirées: SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] ❌ Traitement affectations expirées: ÉCHEC');
            });

        // =================================================================
        // 🚀 TRANSITION SCHEDULED -> ACTIVE (INSTANTANÉE)
        // =================================================================
        // Exécuté toutes les minutes pour une synchronisation immédiate des ressources
        $schedule->job(new \App\Jobs\ProcessScheduledAssignments())
            ->everyMinute()
            ->name('process-scheduled-assignments')
            ->withoutOverlapping(1) // Très important pour l'exécution fréquente
            ->onSuccess(function () {
                \Log::info('[Scheduler] ✅ Transition Scheduled->Active: SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] ❌ Transition Scheduled->Active: ÉCHEC');
            });

        // 🧟 Détection et correction des zombies toutes les 30 minutes
        // Correction des incohérences de statut et ressources bloquées
        $schedule->command('assignments:fix-zombies --force')
            ->everyThirtyMinutes()
            ->name('fix-zombie-assignments')
            ->withoutOverlapping(10)
            ->runInBackground()
            ->onSuccess(function () {
                \Log::info('[Scheduler] 🧟 Correction zombies: SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] 🧟 Correction zombies: ÉCHEC');
            });

        // ⚡ Synchronisation temps réel toutes les 5 minutes
        // Garantit la cohérence parfaite entre assignments/vehicles/drivers
        $schedule->command('assignments:sync --silent')
            ->everyFiveMinutes()
            ->withoutOverlapping(5)
            ->runInBackground()
            ->onSuccess(function () {
                \Log::info('[Scheduler] 🔄 Synchronisation affectations: SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] 🔄 Synchronisation affectations: ÉCHEC - Investigation requise');
                // TODO: Envoyer alerte Slack/Email
            });

        // 🏥 Healing quotidien approfondi des zombies à 2h du matin
        // Analyse complète et correction de toutes les anomalies
        $schedule->command('assignments:fix-zombies --force --verbose')
            ->dailyAt('02:00')
            ->name('daily-deep-zombie-fix')
            ->withoutOverlapping(30)
            ->runInBackground()
            ->onSuccess(function () {
                \Log::info('[Scheduler] 🏥 Healing zombies quotidien approfondi: SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] 🏥 Healing zombies quotidien approfondi: ÉCHEC');
            });

        // 📊 Rapport quotidien des affectations à 6h du matin
        $schedule->command('assignments:daily-report')
            ->dailyAt('06:00')
            ->name('daily-assignments-report')
            ->runInBackground();

        // =================================================================
        // 🧹 MAINTENANCE SYSTÈME
        // =================================================================

        // Nettoyage logs anciens (tous les jours à 3h du matin)
        $schedule->command('queue:prune-batches --hours=48')
            ->dailyAt('03:00')
            ->runInBackground();

        // Nettoyage cache expired (toutes les heures)
        $schedule->command('cache:prune-stale-tags')
            ->hourly()
            ->runInBackground();

        // Optimisation des tables de base de données (hebdomadaire)
        $schedule->command('db:optimize')
            ->weekly()
            ->sundays()
            ->at('04:00')
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
