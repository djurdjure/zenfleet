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
                            {--organization= : ID de l\'organisation à traiter}
                            {--mode=automatic : Mode de traitement (automatic, forced)}
                            {--verbose : Afficher les logs détaillés}
                            {--stats : Afficher les statistiques détaillées}';

    /**
     * Description de la commande
     */
    protected $description = '🔄 Traite automatiquement les affectations expirées et libère les ressources';

    /**
     * Exécuter la commande - VERSION ENTERPRISE-GRADE ULTRA-PRO
     *
     * AMÉLIORATIONS PAR RAPPORT À L'ANCIENNE VERSION :
     * ✅ Dispatch du Job au lieu de l'Event (correction critique)
     * ✅ Détection des affectations zombies (ended_at IS NULL)
     * ✅ Statistiques détaillées en temps réel
     * ✅ Logs structurés pour monitoring
     * ✅ Support multi-organisation
     */
    public function handle(): int
    {
        $organizationId = $this->option('organization');
        $mode = $this->option('mode');
        $verbose = $this->option('verbose');
        $showStats = $this->option('stats');

        $this->displayHeader();

        $this->displayConfig($organizationId, $mode, $verbose);

        $startTime = microtime(true);

        // Dispatcher le Job vers la queue au lieu de traiter directement
        // C'est la correction CRITIQUE qui manquait !
        $job = new \App\Jobs\ProcessExpiredAssignments(
            $organizationId ? (int) $organizationId : null,
            $mode
        );

        dispatch($job);

        $this->newLine();
        $this->info('🔄 Dispatch du job de traitement...');
        $this->newLine();
        $this->info('✅ Job dispatché avec succès !');
        $this->info('   Le traitement s\'exécute en arrière-plan.');

        // Afficher les statistiques actuelles si demandé
        if ($showStats) {
            $this->newLine();
            $this->displayStatistics();
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        Log::info('[ProcessExpiredAssignments Command] Job dispatché', [
            'organization_id' => $organizationId,
            'mode' => $mode,
            'duration_ms' => $duration,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Afficher l'en-tête de la commande
     */
    private function displayHeader(): void
    {
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║  <fg=cyan>TRAITEMENT DES AFFECTATIONS EXPIRÉES - ZENFLEET</>     ║');
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Afficher la configuration
     */
    private function displayConfig(?string $organizationId, string $mode, bool $verbose): void
    {
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Organisation', $organizationId ?? 'Toutes'],
                ['Mode', ucfirst($mode)],
                ['Dry Run', 'Non'],
                ['Logs détaillés', $verbose ? 'Oui' : 'Non'],
                ['Démarré à', now()->format('d/m/Y H:i:s')],
            ]
        );
    }

    /**
     * Afficher les statistiques actuelles
     */
    private function displayStatistics(): void
    {
        $totalAssignments = Assignment::count();
        $activeAssignments = Assignment::where('status', Assignment::STATUS_ACTIVE)->count();
        $scheduledAssignments = Assignment::where('status', Assignment::STATUS_SCHEDULED)->count();

        // Affectations expirées non traitées (ZOMBIES)
        $expiredUnprocessed = Assignment::query()
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at')
            ->count();

        // Affectations terminées aujourd'hui
        $completedToday = Assignment::query()
            ->where('status', Assignment::STATUS_COMPLETED)
            ->whereDate('ended_at', today())
            ->count();

        $this->info('<fg=cyan>📊 STATISTIQUES ACTUELLES</>');
        $this->line('─────────────────────────');
        $this->line("  • Total affectations        : <fg=white>{$totalAssignments}</>");
        $this->line("  • Actives                   : <fg=green>{$activeAssignments}</>");
        $this->line("  • Planifiées               : <fg=blue>{$scheduledAssignments}</>");
        $this->line("  • Expirées non traitées   : <fg=" . ($expiredUnprocessed > 0 ? 'red' : 'green') . ">{$expiredUnprocessed}</>");
        $this->line("  • Terminées aujourd'hui    : <fg=white>{$completedToday}</>");

        if ($expiredUnprocessed > 10) {
            $this->newLine();
            $this->warn("⚠️  ALERTE : {$expiredUnprocessed} affectations zombies détectées !");
        }
    }
}
