<?php

namespace App\Console\Commands;

use App\Jobs\ProcessExpiredAssignments;
use App\Models\Assignment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande de traitement des affectations expirées
 * 
 * SYSTÈME ENTERPRISE-GRADE ULTRA-PRO
 * 
 * Cette commande orchestre le traitement automatique des affectations
 * qui ont atteint leur date de fin planifiée.
 * 
 * @package App\Console\Commands
 * @version 2.0.0
 * @since 2025-11-09
 */
class ProcessAssignmentsCommand extends Command
{
    /**
     * Signature de la commande
     *
     * @var string
     */
    protected $signature = 'assignments:process-expired
                            {--organization= : ID de l\'organisation à traiter}
                            {--mode=automatic : Mode de traitement (automatic|forced)}
                            {--dry-run : Simulation sans modifications}
                            {--verbose-log : Logs détaillés}';

    /**
     * Description de la commande
     *
     * @var string
     */
    protected $description = '🚀 Traiter automatiquement les affectations expirées et libérer les ressources';

    /**
     * Exécuter la commande
     *
     * @return int
     */
    public function handle(): int
    {
        $organizationId = $this->option('organization');
        $mode = $this->option('mode');
        $isDryRun = $this->option('dry-run');
        $verboseLog = $this->option('verbose-log');

        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║  TRAITEMENT DES AFFECTATIONS EXPIRÉES - ZENFLEET     ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        // Afficher les paramètres
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Organisation', $organizationId ?: 'Toutes'],
                ['Mode', ucfirst($mode)],
                ['Dry Run', $isDryRun ? 'Oui' : 'Non'],
                ['Logs détaillés', $verboseLog ? 'Oui' : 'Non'],
                ['Démarré à', now()->format('d/m/Y H:i:s')]
            ]
        );

        if ($isDryRun) {
            $this->warn('⚠️  MODE DRY-RUN : Aucune modification ne sera effectuée');
            $this->newLine();
            return $this->performDryRun($organizationId);
        }

        try {
            // Logger le début
            Log::info('Commande assignments:process-expired démarrée', [
                'organization_id' => $organizationId,
                'mode' => $mode,
                'user' => auth()->user()?->email ?? 'système'
            ]);

            // Dispatch le job
            $this->info('🔄 Dispatch du job de traitement...');
            
            ProcessExpiredAssignments::dispatch($organizationId, $mode);
            
            $this->newLine();
            $this->info('✅ Job dispatché avec succès !');
            $this->info('   Le traitement s\'exécute en arrière-plan.');
            
            if ($verboseLog) {
                $this->info('   Consultez les logs pour suivre la progression.');
            }

            // Afficher les statistiques actuelles
            $this->displayStatistics($organizationId);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du dispatch du job');
            $this->error('   ' . $e->getMessage());
            
            if ($verboseLog) {
                $this->error('Trace:');
                $this->line($e->getTraceAsString());
            }

            Log::error('Erreur dans la commande assignments:process-expired', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Effectuer une simulation (dry-run)
     *
     * @param int|null $organizationId
     * @return int
     */
    private function performDryRun(?int $organizationId): int
    {
        $this->info('🔍 Analyse des affectations expirées...');
        $this->newLine();

        $query = Assignment::query()
            ->with(['vehicle', 'driver'])
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at');

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $expiredAssignments = $query->get();

        if ($expiredAssignments->isEmpty()) {
            $this->info('✅ Aucune affectation expirée à traiter');
            return Command::SUCCESS;
        }

        $this->warn("📊 {$expiredAssignments->count()} affectation(s) expirée(s) trouvée(s)");
        $this->newLine();

        // Tableau des affectations à traiter
        $rows = $expiredAssignments->map(function ($assignment) {
            return [
                $assignment->id,
                $assignment->vehicle?->registration_plate ?? 'N/A',
                $assignment->driver?->full_name ?? 'N/A',
                $assignment->start_datetime->format('d/m/Y H:i'),
                $assignment->end_datetime->format('d/m/Y H:i'),
                now()->diffForHumans($assignment->end_datetime, true) . ' en retard'
            ];
        })->toArray();

        $this->table(
            ['ID', 'Véhicule', 'Chauffeur', 'Début', 'Fin', 'Retard'],
            $rows
        );

        $this->newLine();
        $this->info('Ces affectations seraient terminées automatiquement.');
        $this->info('Les véhicules et chauffeurs seraient libérés.');

        return Command::SUCCESS;
    }

    /**
     * Afficher les statistiques
     *
     * @param int|null $organizationId
     * @return void
     */
    private function displayStatistics(?int $organizationId): void
    {
        $this->newLine();
        $this->info('📊 STATISTIQUES ACTUELLES');
        $this->line('─────────────────────────');

        $query = Assignment::query();
        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        // Statistiques
        $stats = [
            'Total affectations' => $query->count(),
            'Actives' => (clone $query)->whereNull('end_datetime')
                ->where('start_datetime', '<=', now())
                ->count(),
            'Planifiées' => (clone $query)->where('start_datetime', '>', now())->count(),
            'Expirées non traitées' => (clone $query)->whereNotNull('end_datetime')
                ->where('end_datetime', '<=', now())
                ->whereNull('ended_at')
                ->count(),
            'Terminées aujourd\'hui' => (clone $query)->whereNotNull('ended_at')
                ->whereDate('ended_at', today())
                ->count()
        ];

        foreach ($stats as $label => $value) {
            $this->line(sprintf('  • %-25s : %d', $label, $value));
        }
    }
}
