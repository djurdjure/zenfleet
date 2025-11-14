<?php

namespace App\Console\Commands;

use App\Jobs\AutoTerminateExpiredAssignmentsJob;
use Illuminate\Console\Command;

/**
 * 🤖 COMMANDE : LANCER LA TERMINAISON AUTOMATIQUE DES AFFECTATIONS EXPIRÉES
 *
 * Cette commande lance le job AutoTerminateExpiredAssignmentsJob
 * pour terminer les affectations dont la date de fin est dépassée.
 *
 * UTILISATION :
 * php artisan assignments:auto-terminate [--sync]
 *
 * OPTIONS :
 * --sync : Exécuter de manière synchrone (pour tests/debug)
 *
 * @version 1.0.0-Enterprise
 * @date 2025-11-14
 */
class AutoTerminateExpiredAssignmentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:auto-terminate
                            {--sync : Exécuter de manière synchrone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lance la terminaison automatique des affectations expirées';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("═══════════════════════════════════════════════════════════════");
        $this->info("🤖 TERMINAISON AUTOMATIQUE DES AFFECTATIONS EXPIRÉES");
        $this->info("═══════════════════════════════════════════════════════════════");
        $this->newLine();

        $sync = $this->option('sync');

        if ($sync) {
            $this->info("⚙️  Mode: Synchrone (dispatch now)");
            $this->newLine();

            // Dispatch synchrone
            AutoTerminateExpiredAssignmentsJob::dispatchSync();

            $this->newLine();
            $this->info("✅ Job exécuté avec succès");
            $this->info("Consultez les logs pour plus de détails");

        } else {
            $this->info("⚙️  Mode: Asynchrone (dispatch to queue)");
            $this->newLine();

            // Dispatch asynchrone
            AutoTerminateExpiredAssignmentsJob::dispatch();

            $this->info("✅ Job ajouté à la queue");
            $this->info("Le job sera traité par le worker de queue");
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
