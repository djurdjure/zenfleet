<?php

namespace App\Console\Commands;

use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * 🤖 Command Artisan - Archivage Automatique des Chauffeurs Inactifs
 * 
 * Usage:
 *   php artisan drivers:auto-archive --inactive-months=12
 *   php artisan drivers:auto-archive --inactive-months=6 --dry-run
 * 
 * Fonctionnalités:
 * - Archive automatiquement les chauffeurs inactifs depuis X mois
 * - Mode dry-run pour tester sans archiver
 * - Logs complets de l'opération
 * - Statistiques détaillées
 */
class ArchiveInactiveDrivers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'drivers:auto-archive
                            {--inactive-months=12 : Nombre de mois d\'inactivité avant archivage}
                            {--dry-run : Mode test sans archivage réel}
                            {--force : Forcer l\'archivage sans confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Archive automatiquement les chauffeurs inactifs depuis X mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inactiveMonths = $this->option('inactive-months');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('═══════════════════════════════════════════════════════');
        $this->info('🤖 Archivage Automatique des Chauffeurs Inactifs');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Date limite d'inactivité
        $inactiveSince = Carbon::now()->subMonths($inactiveMonths);
        
        $this->info("📅 Critère d'inactivité : Aucune affectation depuis " . $inactiveSince->format('d/m/Y'));
        $this->info("🔍 Mode : " . ($dryRun ? 'TEST (dry-run)' : 'RÉEL'));
        $this->newLine();

        // Recherche des chauffeurs inactifs
        $inactiveDrivers = Driver::query()
            ->whereDoesntHave('assignments', function ($query) use ($inactiveSince) {
                $query->where('start_datetime', '>=', $inactiveSince);
            })
            ->orWhereHas('assignments', function ($query) use ($inactiveSince) {
                $query->where('end_datetime', '<', $inactiveSince);
            })
            ->where('created_at', '<', $inactiveSince) // Ne pas archiver les nouveaux
            ->get();

        $totalFound = $inactiveDrivers->count();

        if ($totalFound === 0) {
            $this->info('✅ Aucun chauffeur inactif trouvé.');
            $this->info('Tous vos chauffeurs sont actifs ou ont été récemment assignés.');
            return Command::SUCCESS;
        }

        $this->warn("⚠️  {$totalFound} chauffeur(s) inactif(s) trouvé(s)");
        $this->newLine();

        // Afficher la liste des chauffeurs
        $this->table(
            ['ID', 'Matricule', 'Nom Complet', 'Dernière Affectation', 'Jours d\'inactivité'],
            $inactiveDrivers->map(function ($driver) use ($inactiveSince) {
                $lastAssignment = $driver->assignments()->orderBy('end_datetime', 'desc')->first();
                $lastDate = $lastAssignment 
                    ? $lastAssignment->end_datetime ?? $lastAssignment->start_datetime
                    : $driver->created_at;
                
                return [
                    $driver->id,
                    $driver->employee_number ?? 'N/A',
                    $driver->first_name . ' ' . $driver->last_name,
                    $lastDate ? $lastDate->format('d/m/Y') : 'Jamais',
                    $lastDate ? $lastDate->diffInDays(now()) : 'N/A',
                ];
            })
        );

        $this->newLine();

        // Confirmation si mode réel et pas de --force
        if (!$dryRun && !$force) {
            if (!$this->confirm('Voulez-vous vraiment archiver ces chauffeurs ?')) {
                $this->info('❌ Opération annulée.');
                return Command::SUCCESS;
            }
        }

        // Archivage
        $archived = 0;
        $errors = 0;

        if ($dryRun) {
            $this->info('🧪 MODE TEST : Aucun archivage réel ne sera effectué.');
            $archived = $totalFound;
        } else {
            $this->info('🚀 Démarrage de l\'archivage...');
            $this->newLine();

            $progressBar = $this->output->createProgressBar($totalFound);
            $progressBar->start();

            foreach ($inactiveDrivers as $driver) {
                try {
                    // Soft delete
                    $driver->delete();
                    
                    // Log
                    Log::info('Driver auto-archived', [
                        'driver_id' => $driver->id,
                        'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                        'employee_number' => $driver->employee_number,
                        'inactive_months' => $inactiveMonths,
                        'archived_by' => 'Auto-Archive Command',
                        'timestamp' => now()->toISOString(),
                    ]);

                    $archived++;
                } catch (\Exception $e) {
                    $this->error("Erreur pour le chauffeur {$driver->id}: " . $e->getMessage());
                    Log::error('Driver auto-archive error', [
                        'driver_id' => $driver->id,
                        'error' => $e->getMessage(),
                    ]);
                    $errors++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);
        }

        // Statistiques finales
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('📊 RAPPORT FINAL');
        $this->info('═══════════════════════════════════════════════════════');
        $this->info("✅ Chauffeurs archivés : {$archived}");
        if ($errors > 0) {
            $this->error("❌ Erreurs : {$errors}");
        }
        $this->info('═══════════════════════════════════════════════════════');

        // Log final
        Log::info('Auto-archive command completed', [
            'total_found' => $totalFound,
            'archived' => $archived,
            'errors' => $errors,
            'inactive_months' => $inactiveMonths,
            'dry_run' => $dryRun,
        ]);

        return Command::SUCCESS;
    }
}
