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
        // ⏰ Traiter les affectations expirées toutes les 5 minutes
        // Cette tâche libère automatiquement les véhicules et chauffeurs
        $schedule->command('assignments:process-expired')
            ->everyFiveMinutes()
            ->withoutOverlapping(10) // Timeout 10 min si bloqué
            ->runInBackground()
            ->onSuccess(function () {
                \Log::info('[Scheduler] assignments:process-expired SUCCÈS');
            })
            ->onFailure(function () {
                \Log::error('[Scheduler] assignments:process-expired ÉCHEC');
            });

        // 🧹 Nettoyage logs anciens (tous les jours à 2h du matin)
        $schedule->command('queue:prune-batches --hours=48')
            ->daily()
            ->at('02:00')
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
