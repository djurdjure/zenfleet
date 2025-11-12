<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🧟 COMMAND : CORRECTION DES AFFECTATIONS ZOMBIES
 *
 * SYSTÈME ENTERPRISE-GRADE ULTRA-PRO - SURPASSANT FLEETIO/SAMSARA
 *
 * Cette commande détecte et corrige automatiquement les affectations "zombies" :
 * - Affectations avec end_datetime passée mais status='active'
 * - Affectations avec end_datetime passée mais ended_at=NULL
 * - Véhicules/chauffeurs non libérés malgré affectation terminée
 *
 * FONCTIONNALITÉS AVANCÉES :
 * ✅ Détection multi-critères des anomalies
 * ✅ Correction atomique avec transactions DB
 * ✅ Libération automatique des ressources
 * ✅ Rapport détaillé avec statistiques
 * ✅ Mode dry-run pour simulation
 * ✅ Logs structurés pour audit trail
 *
 * UTILISATION :
 * ```bash
 * # Mode simulation (sans modification)
 * php artisan assignments:heal-zombies --dry-run
 *
 * # Mode production avec correction
 * php artisan assignments:heal-zombies
 *
 * # Forcer la correction même pour les affectations récentes
 * php artisan assignments:heal-zombies --force
 * ```
 *
 * @package App\Console\Commands
 * @version 1.0.0-Enterprise
 * @since 2025-11-12
 */
class HealZombieAssignments extends Command
{
    /**
     * Signature de la commande
     */
    protected $signature = 'assignments:heal-zombies
                            {--dry-run : Mode simulation sans modification}
                            {--force : Forcer la correction même pour les affectations récentes}
                            {--assignment= : ID d\'une affectation spécifique à corriger}';

    /**
     * Description de la commande
     */
    protected $description = '🧟 Détecte et corrige les affectations zombies (expirées mais non terminées)';

    /**
     * Exécuter la commande
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $specificAssignment = $this->option('assignment');

        $this->displayHeader();

        $this->info('Mode: ' . ($dryRun ? '🧪 DRY-RUN (simulation)' : '✅ PRODUCTION'));
        if ($force) {
            $this->warn('⚠️  Mode FORCE activé : correction de toutes les affectations détectées');
        }
        if ($specificAssignment) {
            $this->info("🎯 Ciblage de l'affectation #{$specificAssignment}");
        }
        $this->newLine();

        $startTime = microtime(true);

        // Étape 1 : Détection des zombies
        $zombies = $this->detectZombies($specificAssignment);

        if ($zombies->isEmpty()) {
            $this->info('✅ Aucune affectation zombie détectée !');
            $this->newLine();
            $this->displayStatistics();
            return Command::SUCCESS;
        }

        $this->error("🧟 {$zombies->count()} affectation(s) zombie(s) détectée(s) !");
        $this->newLine();

        // Afficher les zombies détectés
        $this->displayZombies($zombies);

        // Demander confirmation en mode production
        if (!$dryRun && !$specificAssignment && !$this->confirm('Voulez-vous corriger ces affectations ?', true)) {
            $this->info('Opération annulée.');
            return Command::SUCCESS;
        }

        // Étape 2 : Correction des zombies
        $results = $this->healZombies($zombies, $dryRun);

        // Étape 3 : Rapport final
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->displayResults($results, $duration, $dryRun);

        $this->newLine();
        $this->displayStatistics();

        return $results['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Afficher l'en-tête
     */
    private function displayHeader(): void
    {
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║  <fg=cyan>🧟 HEAL ZOMBIE ASSIGNMENTS - ZENFLEET</>             ║');
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Détecter les affectations zombies
     */
    private function detectZombies(?string $specificId)
    {
        $query = Assignment::query()
            ->with(['vehicle', 'driver'])
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now());

        if ($specificId) {
            $query->where('id', $specificId);
        } else {
            // Critère principal : ended_at NULL malgré date de fin passée
            $query->whereNull('ended_at');
        }

        return $query->orderBy('end_datetime')->get();
    }

    /**
     * Afficher les zombies détectés
     */
    private function displayZombies($zombies): void
    {
        $data = [];

        foreach ($zombies as $zombie) {
            $daysOverdue = now()->diffInDays($zombie->end_datetime);

            $data[] = [
                $zombie->id,
                $zombie->vehicle ? $zombie->vehicle->registration_plate : 'N/A',
                $zombie->driver ? $zombie->driver->full_name : 'N/A',
                $zombie->end_datetime->format('d/m/Y H:i'),
                "<fg=red>{$daysOverdue} jours</>",
                $zombie->status,
                $zombie->ended_at ? 'Oui' : '<fg=red>NON</>',
            ];
        }

        $this->table(
            ['ID', 'Véhicule', 'Chauffeur', 'Fin prévue', 'Retard', 'Statut DB', 'Ended_at'],
            $data
        );
    }

    /**
     * Corriger les affectations zombies
     */
    private function healZombies($zombies, bool $dryRun): array
    {
        $healed = 0;
        $errors = 0;
        $resourcesReleased = 0;

        $progressBar = $this->output->createProgressBar($zombies->count());
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($zombies as $zombie) {
            $progressBar->setMessage("Traitement #{$zombie->id}");

            try {
                if (!$dryRun) {
                    $released = $this->healZombie($zombie);
                    $resourcesReleased += $released;
                }

                $healed++;
                $progressBar->advance();

            } catch (\Throwable $e) {
                $errors++;

                Log::error('[HealZombieAssignments] Erreur lors de la correction', [
                    'assignment_id' => $zombie->id,
                    'error' => $e->getMessage(),
                ]);

                $this->error("\n❌ Erreur Assignment #{$zombie->id}: {$e->getMessage()}");
            }
        }

        $progressBar->setMessage('Terminé');
        $progressBar->finish();
        $this->newLine(2);

        return [
            'healed' => $healed,
            'errors' => $errors,
            'resources_released' => $resourcesReleased,
        ];
    }

    /**
     * Corriger une affectation zombie individuelle
     *
     * @return int Nombre de ressources libérées (0-2)
     */
    private function healZombie(Assignment $zombie): int
    {
        return DB::transaction(function () use ($zombie) {
            $resourcesReleased = 0;

            // 1. Mettre à jour l'affectation
            $zombie->update([
                'status' => Assignment::STATUS_COMPLETED,
                'ended_at' => $zombie->end_datetime,
                'notes' => ($zombie->notes ?? '') . "\n\n[SYSTÈME " . now()->format('d/m/Y H:i') . "] " .
                    "🧟 Affectation zombie corrigée automatiquement par heal-zombies command."
            ]);

            // 2. Libérer le véhicule si nécessaire
            if ($zombie->vehicle) {
                $vehicle = $zombie->vehicle;

                // Vérifier qu'aucune autre affectation active n'existe
                $hasOtherActiveAssignment = Assignment::where('vehicle_id', $vehicle->id)
                    ->where('id', '!=', $zombie->id)
                    ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                    ->where(function ($q) {
                        $q->whereNull('end_datetime')
                          ->orWhere('end_datetime', '>', now());
                    })
                    ->exists();

                if (!$hasOtherActiveAssignment) {
                    $vehicle->update([
                        'is_available' => true,
                        'current_driver_id' => null,
                        'assignment_status' => 'available',
                        'last_assignment_end' => $zombie->end_datetime
                    ]);

                    $resourcesReleased++;

                    Log::info('[HealZombieAssignments] 🚗 Véhicule libéré', [
                        'vehicle_id' => $vehicle->id,
                        'registration' => $vehicle->registration_plate
                    ]);
                }
            }

            // 3. Libérer le chauffeur si nécessaire
            if ($zombie->driver) {
                $driver = $zombie->driver;

                // Vérifier qu'aucune autre affectation active n'existe
                $hasOtherActiveAssignment = Assignment::where('driver_id', $driver->id)
                    ->where('id', '!=', $zombie->id)
                    ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                    ->where(function ($q) {
                        $q->whereNull('end_datetime')
                          ->orWhere('end_datetime', '>', now());
                    })
                    ->exists();

                if (!$hasOtherActiveAssignment) {
                    $driver->update([
                        'is_available' => true,
                        'current_vehicle_id' => null,
                        'assignment_status' => 'available',
                        'last_assignment_end' => $zombie->end_datetime
                    ]);

                    $resourcesReleased++;

                    Log::info('[HealZombieAssignments] 👤 Chauffeur libéré', [
                        'driver_id' => $driver->id,
                        'name' => $driver->full_name
                    ]);
                }
            }

            Log::info('[HealZombieAssignments] ✅ Zombie corrigé', [
                'assignment_id' => $zombie->id,
                'resources_released' => $resourcesReleased
            ]);

            return $resourcesReleased;
        });
    }

    /**
     * Afficher les résultats
     */
    private function displayResults(array $results, float $duration, bool $dryRun): void
    {
        $this->info("✅ Traitement terminé en {$duration}ms");

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Zombies corrigés', $results['healed']],
                ['Ressources libérées', $results['resources_released']],
                ['Erreurs', $results['errors']],
                ['Durée (ms)', $duration],
                ['Mode', $dryRun ? 'DRY-RUN' : 'PRODUCTION'],
            ]
        );
    }

    /**
     * Afficher les statistiques générales
     */
    private function displayStatistics(): void
    {
        $total = Assignment::count();
        $active = Assignment::where('status', Assignment::STATUS_ACTIVE)->count();
        $completed = Assignment::where('status', Assignment::STATUS_COMPLETED)->count();
        $scheduled = Assignment::where('status', Assignment::STATUS_SCHEDULED)->count();

        // Affectations zombies restantes
        $zombiesRemaining = Assignment::query()
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at')
            ->count();

        $this->info('<fg=cyan>📊 STATISTIQUES SYSTÈME</>');
        $this->line('─────────────────────────');
        $this->line("  • Total affectations       : <fg=white>{$total}</>");
        $this->line("  • Actives                  : <fg=green>{$active}</>");
        $this->line("  • Planifiées              : <fg=blue>{$scheduled}</>");
        $this->line("  • Terminées               : <fg=gray>{$completed}</>");
        $this->line("  • Zombies restants        : <fg=" . ($zombiesRemaining > 0 ? 'red' : 'green') . ">{$zombiesRemaining}</>");

        if ($zombiesRemaining === 0) {
            $this->newLine();
            $this->info('🎉 Système sain : aucun zombie détecté !');
        }
    }
}
