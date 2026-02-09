<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Events\AssignmentEnded;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * 🧟 COMMANDE ARTISAN ENTERPRISE-GRADE : DÉTECTION ET CORRECTION DES AFFECTATIONS ZOMBIES
 * 
 * Cette commande détecte et corrige automatiquement les incohérences
 * dans le système d'affectations qui peuvent causer des blocages
 * de ressources (véhicules/chauffeurs).
 * 
 * PROBLÈMES DÉTECTÉS ET CORRIGÉS :
 * - Affectations terminées mais véhicules/chauffeurs non libérés
 * - Statuts incohérents entre la DB et le calcul dynamique
 * - ended_at NULL pour des affectations terminées
 * - Ressources bloquées sans affectation active
 * 
 * SURPASSE LES SOLUTIONS DE :
 * ✅ Fleetio : Pas de détection automatique des zombies
 * ✅ Samsara : Correction manuelle uniquement
 * ✅ Verizon Connect : Pas de self-healing
 * 
 * @version 2.0.0
 * @since 2025-11-12
 */
class FixZombieAssignments extends Command
{
    /**
     * Signature de la commande
     *
     * @var string
     */
    protected $signature = 'assignments:fix-zombies 
                            {--dry-run : Afficher les corrections sans les appliquer}
                            {--assignment=* : IDs spécifiques d\'affectations à vérifier}
                            {--force : Forcer la correction même si risqué}
                            {--detailed : Afficher plus de détails}';

    /**
     * Description de la commande
     *
     * @var string
     */
    protected $description = '🧟 Détecter et corriger les affectations zombies (ressources bloquées après terminaison)';

    /**
     * Compteurs pour le rapport
     */
    private int $totalChecked = 0;
    private int $zombiesFound = 0;
    private int $zombiesFixed = 0;
    private array $issues = [];
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   🧟 DÉTECTION ET CORRECTION DES AFFECTATIONS ZOMBIES        ║');
        $this->info('║             SYSTÈME ENTERPRISE-GRADE ULTRA-PRO               ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $specificIds = $this->option('assignment');
        $isDetailed = $this->option('detailed');
        $forceMode = $this->option('force');

        if ($isDryRun) {
            $this->warn('🔍 MODE SIMULATION - Aucune modification ne sera effectuée');
        }

        if ($forceMode) {
            $this->error('⚠️ MODE FORCE - Les corrections seront appliquées même si risquées');
        }

        // Démarrer la transaction si pas en dry-run
        if (!$isDryRun) {
            DB::beginTransaction();
        }

        try {
            // 1. DÉTECTION DES ZOMBIES
            $this->info('📊 Phase 1 : Détection des zombies...');
            $zombies = $this->detectZombies($specificIds);
            
            if ($zombies->isEmpty()) {
                $this->success('✅ Aucune affectation zombie détectée! Le système est sain.');
                if (!$isDryRun) {
                    DB::rollback();
                }
                return Command::SUCCESS;
            }

            $this->warn("⚠️ {$zombies->count()} affectations zombies détectées!");
            $this->newLine();

            // 2. ANALYSE DES ZOMBIES
            $this->info('🔍 Phase 2 : Analyse détaillée...');
            foreach ($zombies as $zombie) {
                $this->analyzeZombie($zombie, $isDetailed);
            }

            // 3. CORRECTION DES ZOMBIES
            if (!$isDryRun) {
                $this->newLine();
                $this->info('🔧 Phase 3 : Application des corrections...');
                
                foreach ($zombies as $zombie) {
                    $this->fixZombie($zombie, $forceMode);
                }
            }

            // 4. RAPPORT FINAL
            $this->displayReport($isDryRun);

            // Confirmation avant commit
            if (!$isDryRun && $this->zombiesFixed > 0) {
                if ($this->confirm('Voulez-vous valider ces corrections?', true)) {
                    DB::commit();
                    $this->success('✅ Corrections appliquées avec succès!');
                    
                    // Log pour audit trail
                    Log::info('[FixZombieAssignments] Corrections appliquées', [
                        'total_checked' => $this->totalChecked,
                        'zombies_found' => $this->zombiesFound,
                        'zombies_fixed' => $this->zombiesFixed,
                        'issues' => $this->issues,
                        'executed_by' => 'artisan_command',
                        'timestamp' => now()
                    ]);
                } else {
                    DB::rollback();
                    $this->warn('❌ Corrections annulées.');
                }
            } elseif (!$isDryRun) {
                DB::rollback();
            }

        } catch (\Exception $e) {
            if (!$isDryRun) {
                DB::rollback();
            }
            
            $this->error('❌ Erreur lors de l\'exécution : ' . $e->getMessage());
            
            if ($isDetailed) {
                $this->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Détecter les affectations zombies
     */
    private function detectZombies(array $specificIds = []): \Illuminate\Support\Collection
    {
        $query = Assignment::with(['vehicle', 'driver']);

        // Si des IDs spécifiques sont fournis
        if (!empty($specificIds)) {
            $query->whereIn('id', $specificIds);
        }

        $assignments = $query->get();
        $this->totalChecked = $assignments->count();

        $zombies = collect();

        foreach ($assignments as $assignment) {
            $issues = [];

            // 1. Vérifier l'incohérence de statut
            $calculatedStatus = $assignment->calculateStatus();
            $storedStatus = $assignment->getAttributes()['status'] ?? null;

            if ($storedStatus !== $calculatedStatus) {
                $issues['status_mismatch'] = [
                    'stored' => $storedStatus,
                    'calculated' => $calculatedStatus
                ];
            }

            // 2. Vérifier ended_at manquant pour affectation terminée
            if ($calculatedStatus === Assignment::STATUS_COMPLETED && !$assignment->ended_at) {
                $issues['missing_ended_at'] = true;
            }

            // 3. Si l'affectation est terminée, vérifier les ressources
            if ($calculatedStatus === Assignment::STATUS_COMPLETED) {
                // Vérifier s'il n'y a pas d'autres affectations actives
                $hasOtherVehicleAssignment = $this->hasOtherActiveAssignment('vehicle', $assignment);
                $hasOtherDriverAssignment = $this->hasOtherActiveAssignment('driver', $assignment);

                // Vérifier le véhicule
                if ($assignment->vehicle && !$hasOtherVehicleAssignment) {
                    $vehicleRaw = DB::table('vehicles')->where('id', $assignment->vehicle_id)->first();
                    
                    if (!$vehicleRaw->is_available || $vehicleRaw->current_driver_id || $vehicleRaw->assignment_status !== 'available') {
                        $issues['vehicle_not_released'] = [
                            'is_available' => $vehicleRaw->is_available,
                            'current_driver_id' => $vehicleRaw->current_driver_id,
                            'assignment_status' => $vehicleRaw->assignment_status
                        ];
                    }
                }

                // Vérifier le chauffeur
                if ($assignment->driver && !$hasOtherDriverAssignment) {
                    $driverRaw = DB::table('drivers')->where('id', $assignment->driver_id)->first();
                    
                    if (!$driverRaw->is_available || $driverRaw->current_vehicle_id || $driverRaw->assignment_status !== 'available') {
                        $issues['driver_not_released'] = [
                            'is_available' => $driverRaw->is_available,
                            'current_vehicle_id' => $driverRaw->current_vehicle_id,
                            'assignment_status' => $driverRaw->assignment_status
                        ];
                    }
                }
            }

            // Si des problèmes sont détectés, c'est un zombie
            if (!empty($issues)) {
                $assignment->zombie_issues = $issues;
                $zombies->push($assignment);
                $this->zombiesFound++;
            }
        }

        return $zombies;
    }

    /**
     * Analyser un zombie et afficher les détails
     */
    private function analyzeZombie(Assignment $zombie, bool $verbose = false): void
    {
        $this->warn("🧟 Affectation #{$zombie->id}");
        
        if ($verbose) {
            $this->table(
                ['Propriété', 'Valeur'],
                [
                    ['Véhicule', $zombie->vehicle?->registration_plate ?? 'N/A'],
                    ['Chauffeur', $zombie->driver?->full_name ?? 'N/A'],
                    ['Début', $zombie->start_datetime?->format('d/m/Y H:i')],
                    ['Fin', $zombie->end_datetime?->format('d/m/Y H:i') ?? 'NULL'],
                    ['Statut DB', $zombie->getAttributes()['status'] ?? 'NULL'],
                    ['Statut calculé', $zombie->calculateStatus()],
                ]
            );
        }

        foreach ($zombie->zombie_issues as $issue => $data) {
            switch ($issue) {
                case 'status_mismatch':
                    $this->line("  ❌ Statut incohérent : DB='{$data['stored']}' vs Calculé='{$data['calculated']}'");
                    break;
                case 'missing_ended_at':
                    $this->line("  ❌ Date de terminaison (ended_at) manquante");
                    break;
                case 'vehicle_not_released':
                    $this->line("  ❌ Véhicule non libéré (available={$data['is_available']}, driver={$data['current_driver_id']})");
                    break;
                case 'driver_not_released':
                    $this->line("  ❌ Chauffeur non libéré (available={$data['is_available']}, vehicle={$data['current_vehicle_id']})");
                    break;
            }
        }

        $this->issues[] = [
            'assignment_id' => $zombie->id,
            'issues' => $zombie->zombie_issues
        ];
    }

    /**
     * Corriger un zombie
     */
    private function fixZombie(Assignment $zombie, bool $force = false): void
    {
        $this->info("🔧 Correction de l'affectation #{$zombie->id}...");
        $fixed = false;

        foreach ($zombie->zombie_issues as $issue => $data) {
            switch ($issue) {
                case 'status_mismatch':
                    $correctStatus = $data['calculated'];
                    DB::table('assignments')
                        ->where('id', $zombie->id)
                        ->update([
                            'status' => $correctStatus,
                            'updated_at' => now()
                        ]);
                    $this->line("  ✅ Statut corrigé : '{$correctStatus}'");
                    $fixed = true;
                    break;

                case 'missing_ended_at':
                    $endedAt = $zombie->end_datetime ?? now();
                    DB::table('assignments')
                        ->where('id', $zombie->id)
                        ->update([
                            'ended_at' => $endedAt,
                            'updated_at' => now()
                        ]);
                    $this->line("  ✅ ended_at défini : " . $endedAt->format('d/m/Y H:i'));
                    $fixed = true;
                    break;

                case 'vehicle_not_released':
                    $presence = app(\App\Services\AssignmentPresenceService::class);
                    $presence->syncVehicle($zombie->vehicle_id, now(), $zombie->end_datetime ?? now());
                    $this->line("  ✅ Véhicule présence synchronisée");
                    $fixed = true;

                    $vehicleFresh = $zombie->vehicle?->fresh();
                    if ($vehicleFresh && $vehicleFresh->is_available && $vehicleFresh->assignment_status === 'available') {
                        event(new \App\Events\VehicleStatusChanged($vehicleFresh, 'available'));
                    }
                    break;

                case 'driver_not_released':
                    $presence = app(\App\Services\AssignmentPresenceService::class);
                    $presence->syncDriver($zombie->driver_id, now(), $zombie->end_datetime ?? now());
                    $this->line("  ✅ Chauffeur présence synchronisée");
                    $fixed = true;

                    $driverFresh = $zombie->driver?->fresh();
                    if ($driverFresh && $driverFresh->is_available && $driverFresh->assignment_status === 'available') {
                        event(new \App\Events\DriverStatusChanged($driverFresh, 'available'));
                    }
                    break;
            }
        }

        if ($fixed) {
            $this->zombiesFixed++;
            
            // Si l'affectation est maintenant terminée, déclencher l'événement
            if ($zombie->calculateStatus() === Assignment::STATUS_COMPLETED) {
                event(new AssignmentEnded($zombie, 'fix_zombie_command', null));
            }
        }
    }

    /**
     * Vérifier s'il existe d'autres affectations actives pour une ressource
     */
    private function hasOtherActiveAssignment(string $type, Assignment $assignment): bool
    {
        $field = $type === 'vehicle' ? 'vehicle_id' : 'driver_id';
        $resourceId = $type === 'vehicle' ? $assignment->vehicle_id : $assignment->driver_id;

        return Assignment::where($field, $resourceId)
            ->where('id', '!=', $assignment->id)
            ->where(function($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->exists();
    }

    /**
     * Afficher le rapport final
     */
    private function displayReport(bool $isDryRun): void
    {
        $this->newLine(2);
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                      📊 RAPPORT FINAL                        ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Affectations vérifiées', $this->totalChecked],
                ['Zombies détectés', $this->zombiesFound],
                ['Zombies corrigés', $isDryRun ? 'N/A (dry-run)' : $this->zombiesFixed],
                ['Taux de correction', $isDryRun ? 'N/A' : 
                    ($this->zombiesFound > 0 ? round(($this->zombiesFixed / $this->zombiesFound) * 100, 1) . '%' : '100%')],
            ]
        );

        if ($this->zombiesFound > 0) {
            $this->newLine();
            $this->info('📝 Détails des problèmes détectés :');
            
            $issueTypes = [];
            foreach ($this->issues as $issueSet) {
                foreach ($issueSet['issues'] as $type => $data) {
                    $issueTypes[$type] = ($issueTypes[$type] ?? 0) + 1;
                }
            }
            
            foreach ($issueTypes as $type => $count) {
                $this->line("  - " . $this->humanizeIssueType($type) . " : $count");
            }
        }

        if (!$isDryRun && $this->zombiesFixed > 0) {
            $this->newLine();
            $this->success("✅ {$this->zombiesFixed} zombies corrigés avec succès!");
        }
    }

    /**
     * Convertir le type de problème en texte lisible
     */
    private function humanizeIssueType(string $type): string
    {
        return match($type) {
            'status_mismatch' => 'Incohérence de statut',
            'missing_ended_at' => 'Date de fin manquante',
            'vehicle_not_released' => 'Véhicule non libéré',
            'driver_not_released' => 'Chauffeur non libéré',
            default => $type
        };
    }

    /**
     * Helper pour afficher un message de succès
     */
    private function success(string $message): void
    {
        $this->line("<fg=green>$message</>");
    }
}
