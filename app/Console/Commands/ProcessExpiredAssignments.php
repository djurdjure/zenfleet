<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Events\AssignmentEnded;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * ⏰ COMMAND : TRAITEMENT AUTOMATIQUE AFFECTATIONS EXPIRÉES
 *
 * Cette commande s'exécute toutes les 5 minutes via le scheduler Laravel.
 *
 * FONCTIONNEMENT :
 * 1. Trouve toutes les affectations avec end_datetime <= now() ET status != 'completed'
 * 2. Met à jour leur statut en 'completed'
 * 3. Dispatch AssignmentEnded pour libérer véhicule/chauffeur
 * 4. Logs structurés pour monitoring
 *
 * CRON CONFIGURATION (app/Console/Kernel.php) :
 * ```php
 * $schedule->command('assignments:process-expired')->everyFiveMinutes();
 * ```
 *
 * MONITORING :
 * - Logs dans storage/logs/laravel.log
 * - Métriques Prometheus (si activé)
 * - Alertes si > 100 affectations expirées à la fois (anomalie)
 *
 * @version 1.0-Enterprise
 */
class ProcessExpiredAssignments extends Command
{
    /**
     * Signature de la commande
     */
    protected $signature = 'assignments:process-expired
                            {--dry-run : Exécuter en mode simulation sans mise à jour}
                            {--limit=100 : Nombre maximum d\'affectations à traiter par run}';

    /**
     * Description de la commande
     */
    protected $description = '🔄 Traite automatiquement les affectations expirées et libère les ressources';

    /**
     * Exécuter la commande
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info('🚀 Démarrage du traitement des affectations expirées...');
        $this->info('Mode: ' . ($dryRun ? '🧪 DRY-RUN (simulation)' : '✅ PRODUCTION'));

        $startTime = microtime(true);

        // Trouver les affectations expirées
        // Note : On filtre d'abord par end_datetime, puis on exclut les 'completed' côté PHP
        // car le statut peut être NULL en base et calculé dynamiquement
        $expiredAssignments = Assignment::query()
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->where(function($query) {
                $query->whereNull('status')
                      ->orWhere('status', '!=', Assignment::STATUS_COMPLETED);
            })
            ->limit($limit)
            ->get()
            ->filter(function($assignment) {
                // Filtrer côté PHP pour utiliser l'accessor calculé
                return $assignment->status !== Assignment::STATUS_COMPLETED;
            });

        $count = $expiredAssignments->count();

        if ($count === 0) {
            $this->info('✅ Aucune affectation expirée à traiter.');
            return Command::SUCCESS;
        }

        $this->info("📊 {$count} affectation(s) expirée(s) trouvée(s)");

        // Alerte si trop d'affectations expirées (anomalie)
        if ($count >= 100) {
            Log::warning('[ProcessExpiredAssignments] ALERTE : Nombre anormal d\'affectations expirées', [
                'count' => $count,
                'limit' => $limit,
            ]);
            $this->warn("⚠️  ALERTE : {$count} affectations expirées détectées (limite: {$limit})");
        }

        $processed = 0;
        $errors = 0;

        // Progress bar
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($expiredAssignments as $assignment) {
            $progressBar->setMessage("Traitement Assignment #{$assignment->id}");

            try {
                if (!$dryRun) {
                    // Mettre à jour le statut (force l'écriture en DB)
                    $assignment->update(['status' => Assignment::STATUS_COMPLETED]);

                    // Dispatcher l'événement pour libérer véhicule/chauffeur
                    AssignmentEnded::dispatch($assignment, 'automatic', null);
                }

                $processed++;
                $progressBar->advance();

                Log::info('[ProcessExpiredAssignments] Affectation traitée', [
                    'assignment_id' => $assignment->id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'driver_id' => $assignment->driver_id,
                    'end_datetime' => $assignment->end_datetime->toIso8601String(),
                    'dry_run' => $dryRun,
                ]);

            } catch (\Throwable $e) {
                $errors++;

                Log::error('[ProcessExpiredAssignments] ERREUR lors du traitement', [
                    'assignment_id' => $assignment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $this->error("\n❌ Erreur Assignment #{$assignment->id}: {$e->getMessage()}");
            }
        }

        $progressBar->setMessage('Terminé');
        $progressBar->finish();
        $this->newLine(2);

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Résumé
        $this->info("✅ Traitement terminé en {$duration}ms");
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Affectations trouvées', $count],
                ['Traitées avec succès', $processed],
                ['Erreurs', $errors],
                ['Durée (ms)', $duration],
                ['Mode', $dryRun ? 'DRY-RUN' : 'PRODUCTION'],
            ]
        );

        Log::info('[ProcessExpiredAssignments] Exécution terminée', [
            'total' => $count,
            'processed' => $processed,
            'errors' => $errors,
            'duration_ms' => $duration,
            'dry_run' => $dryRun,
        ]);

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
