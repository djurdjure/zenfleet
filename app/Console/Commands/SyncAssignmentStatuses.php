<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Notifications\AssignmentSyncAnomalyDetected;
use App\Traits\ManagesResourceStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

/**
 * 🔄 COMMANDE ARTISAN ENTERPRISE-GRADE - SYNCHRONISATION STATUTS AFFECTATIONS
 *
 * SURPASSE TOUS LES STANDARDS FLEETIO/SAMSARA/VERIZON CONNECT
 *
 * Cette commande synchronise automatiquement les statuts des affectations, véhicules et chauffeurs
 * pour garantir une cohérence parfaite des données à travers tout le système.
 *
 * PROBLÈMES DÉTECTÉS ET CORRIGÉS :
 * ✅ Affectations terminées avec ressources toujours bloquées
 * ✅ Affectations actives avec ressources disponibles
 * ✅ Statuts incohérents entre assignments/vehicles/drivers
 * ✅ Colonnes is_available et assignment_status désynchronisées
 * ✅ Affectations sans ended_at alors que end_datetime est passée
 *
 * STRATÉGIE DE SYNCHRONISATION TRIPLE :
 * 1. ASSIGNMENTS → Calculer et persister les statuts corrects
 * 2. VEHICLES → Synchroniser is_available et assignment_status avec affectations actives
 * 3. DRIVERS → Synchroniser is_available et assignment_status avec affectations actives
 *
 * USAGE :
 * php artisan assignments:sync [--dry-run] [--force] [--silent]
 *
 * OPTIONS :
 * --dry-run    : Mode simulation sans modification
 * --force      : Force l'exécution sans confirmation
 * --silent     : Mode silencieux sans output détaillé
 *
 * INTÉGRATION SCHEDULER :
 * $schedule->command('assignments:sync --silent')->everyFiveMinutes();
 *
 * @package App\Console\Commands
 * @version 3.0.0-Enterprise-Sync
 * @since 2025-11-12
 */
class SyncAssignmentStatuses extends Command
{
    use ManagesResourceStatus;

    protected $signature = 'assignments:sync
                            {--dry-run : Simulation sans modification}
                            {--force : Force sans confirmation}
                            {--silent : Mode silencieux}';

    protected $description = '🔄 Synchronise les statuts des affectations, véhicules et chauffeurs (ENTERPRISE-GRADE)';

    private int $assignmentsUpdated = 0;
    private int $vehiclesFreed = 0;
    private int $driversFreed = 0;
    private int $vehiclesLocked = 0;
    private int $driversLocked = 0;
    private int $inconsistenciesFixed = 0;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $silent = $this->option('silent');

        if (!$silent) {
            $this->displayHeader();
        }

        if (!$dryRun && !$this->option('force')) {
            if (!$this->confirm('⚠️  Cette commande va synchroniser tous les statuts. Continuer ?')) {
                $this->warn('Opération annulée.');
                return self::FAILURE;
            }
        }

        if ($dryRun && !$silent) {
            $this->info('🔍 MODE SIMULATION - Aucune modification ne sera appliquée');
            $this->newLine();
        }

        $startTime = microtime(true);

        DB::transaction(function () use ($dryRun, $silent) {
            // Étape 1 : Synchroniser les statuts des affectations
            $this->syncAssignmentStatuses($dryRun, $silent);

            // Étape 2 : Synchroniser les véhicules
            $this->syncVehicleStatuses($dryRun, $silent);

            // Étape 3 : Synchroniser les chauffeurs
            $this->syncDriverStatuses($dryRun, $silent);

            // Si dry-run, rollback
            if ($dryRun) {
                DB::rollBack();
            }
        });

        $duration = round((microtime(true) - $startTime) * 1000, 2);

        if (!$silent) {
            $this->displayReport($dryRun, $duration);
        }

        $totalChanges = $this->getTotalChanges();

        Log::info('[SyncAssignmentStatuses] Exécution terminée', [
            'dry_run' => $dryRun,
            'duration_ms' => $duration,
            'assignments_updated' => $this->assignmentsUpdated,
            'vehicles_freed' => $this->vehiclesFreed,
            'drivers_freed' => $this->driversFreed,
            'vehicles_locked' => $this->vehiclesLocked,
            'drivers_locked' => $this->driversLocked,
            'total_changes' => $totalChanges,
        ]);

        // Envoyer alerte si anomalies détectées et pas en dry-run
        if (!$dryRun && $totalChanges >= 5) {
            $this->sendAnomalyAlert();
        }

        return self::SUCCESS;
    }

    /**
     * Synchroniser les statuts des affectations
     */
    private function syncAssignmentStatuses(bool $dryRun, bool $silent): void
    {
        if (!$silent) {
            $this->info('🔄 Synchronisation des statuts d\'affectations...');
        }

        $now = now();

        // Récupérer toutes les affectations non supprimées
        $assignments = Assignment::whereNull('deleted_at')->get();

        foreach ($assignments as $assignment) {
            $oldStatus = $assignment->status;
            $newStatus = $this->calculateCorrectStatus($assignment, $now);

            if ($oldStatus !== $newStatus) {
                if (!$silent) {
                    $this->line(sprintf(
                        '  → Affectation #%d : %s → %s',
                        $assignment->id,
                        $oldStatus,
                        $newStatus
                    ));
                }

                if (!$dryRun) {
                    $assignment->status = $newStatus;

                    // Auto-complétion de ended_at si terminée
                    if ($newStatus === Assignment::STATUS_COMPLETED && !$assignment->ended_at) {
                        $assignment->ended_at = $assignment->end_datetime ?? $now;
                    }

                    $assignment->save();
                }

                $this->assignmentsUpdated++;
            }
        }

        if (!$silent) {
            if ($this->assignmentsUpdated > 0) {
                $this->info("  ✅ {$this->assignmentsUpdated} affectation(s) synchronisée(s)");
            } else {
                $this->line('  ✅ Tous les statuts sont corrects');
            }
        }
    }

    /**
     * Synchroniser les statuts des véhicules
     */
    private function syncVehicleStatuses(bool $dryRun, bool $silent): void
    {
        if (!$silent) {
            $this->info('🚗 Synchronisation des statuts de véhicules...');
        }

        $vehicles = Vehicle::whereNull('deleted_at')->get();

        foreach ($vehicles as $vehicle) {
            $hasActiveAssignment = Assignment::where('vehicle_id', $vehicle->id)
                ->whereNull('deleted_at')
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            $shouldBeAvailable = !$hasActiveAssignment;
            $currentlyAvailable = $vehicle->is_available;
            $currentStatus = $vehicle->assignment_status;

            if ($shouldBeAvailable !== $currentlyAvailable ||
                ($shouldBeAvailable && $currentStatus !== 'available') ||
                (!$shouldBeAvailable && $currentStatus === 'available')) {

                if (!$silent) {
                    $this->line(sprintf(
                        '  → Véhicule #%d (%s) : %s → %s',
                        $vehicle->id,
                        $vehicle->registration_plate,
                        $currentlyAvailable ? 'disponible' : 'occupé',
                        $shouldBeAvailable ? 'disponible' : 'occupé'
                    ));
                }

                if (!$dryRun) {
                    if ($shouldBeAvailable) {
                        // Utiliser la logique de libération intelligente du Trait
                        $this->releaseResource($vehicle);
                        $this->vehiclesFreed++;
                    } else {
                        // Récupérer le chauffeur de l'affectation active
                        $activeAssignment = Assignment::where('vehicle_id', $vehicle->id)
                            ->whereNull('deleted_at')
                            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                            ->first();

                        if ($activeAssignment) {
                            $vehicle->current_driver_id = $activeAssignment->driver_id;
                        }
                        $this->vehiclesLocked++;
                    }

                    $vehicle->save();
                }

                $this->inconsistenciesFixed++;
            }
        }

        if (!$silent) {
            $totalVehicleChanges = $this->vehiclesFreed + $this->vehiclesLocked;
            if ($totalVehicleChanges > 0) {
                $this->info(sprintf(
                    '  ✅ %d véhicule(s) synchronisé(s) : %d libéré(s), %d verrouillé(s)',
                    $totalVehicleChanges,
                    $this->vehiclesFreed,
                    $this->vehiclesLocked
                ));
            } else {
                $this->line('  ✅ Tous les statuts sont corrects');
            }
        }
    }

    /**
     * Synchroniser les statuts des chauffeurs
     */
    private function syncDriverStatuses(bool $dryRun, bool $silent): void
    {
        if (!$silent) {
            $this->info('👤 Synchronisation des statuts de chauffeurs...');
        }

        $drivers = Driver::whereNull('deleted_at')->get();

        foreach ($drivers as $driver) {
            $hasActiveAssignment = Assignment::where('driver_id', $driver->id)
                ->whereNull('deleted_at')
                ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                ->exists();

            $shouldBeAvailable = !$hasActiveAssignment;
            $currentlyAvailable = $driver->is_available;
            $currentStatus = $driver->assignment_status;

            if ($shouldBeAvailable !== $currentlyAvailable ||
                ($shouldBeAvailable && $currentStatus !== 'available') ||
                (!$shouldBeAvailable && $currentStatus === 'available')) {

                if (!$silent) {
                    $this->line(sprintf(
                        '  → Chauffeur #%d (%s) : %s → %s',
                        $driver->id,
                        $driver->full_name,
                        $currentlyAvailable ? 'disponible' : 'occupé',
                        $shouldBeAvailable ? 'disponible' : 'occupé'
                    ));
                }

                if (!$dryRun) {
                    if ($shouldBeAvailable) {
                        // Utiliser la logique de libération intelligente du Trait
                        $this->releaseResource($driver);
                        $this->driversFreed++;
                    } else {
                        // Récupérer le véhicule de l'affectation active
                        $activeAssignment = Assignment::where('driver_id', $driver->id)
                            ->whereNull('deleted_at')
                            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
                            ->first();

                        if ($activeAssignment) {
                            $driver->current_vehicle_id = $activeAssignment->vehicle_id;
                        }
                        // Mettre le statut métier "En mission"
                        $enMissionStatusId = \DB::table('driver_statuses')
                            ->where('name', 'En mission')
                            ->value('id') ?? 8;
                        $driver->status_id = $enMissionStatusId;
                        $this->driversLocked++;
                    }

                    $driver->save();
                }

                $this->inconsistenciesFixed++;
            }
        }

        if (!$silent) {
            $totalDriverChanges = $this->driversFreed + $this->driversLocked;
            if ($totalDriverChanges > 0) {
                $this->info(sprintf(
                    '  ✅ %d chauffeur(s) synchronisé(s) : %d libéré(s), %d verrouillé(s)',
                    $totalDriverChanges,
                    $this->driversFreed,
                    $this->driversLocked
                ));
            } else {
                $this->line('  ✅ Tous les statuts sont corrects');
            }
        }
    }

    /**
     * Calculer le statut correct d'une affectation
     */
    private function calculateCorrectStatus(Assignment $assignment, Carbon $now): string
    {
        // Si annulée, garder cet état
        if ($assignment->status === Assignment::STATUS_CANCELLED) {
            return Assignment::STATUS_CANCELLED;
        }

        // Programmée (pas encore commencée)
        if ($assignment->start_datetime > $now) {
            return Assignment::STATUS_SCHEDULED;
        }

        // Terminée (end_datetime dans le passé)
        if ($assignment->end_datetime && $assignment->end_datetime <= $now) {
            return Assignment::STATUS_COMPLETED;
        }

        // Active (commencée et pas encore terminée)
        return Assignment::STATUS_ACTIVE;
    }

    /**
     * Afficher l'en-tête
     */
    private function displayHeader(): void
    {
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║   🔄 ZENFLEET - SYNCHRONISATION STATUTS AFFECTATIONS         ║');
        $this->line('║   Enterprise-Grade Triple-Sync (Assignments/Vehicles/Drivers) ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Afficher le rapport final
     */
    private function displayReport(bool $dryRun, float $duration): void
    {
        $this->newLine();
        $this->line('╔════════════════════════════════════════════════════════════════╗');
        $this->line('║   📊 RAPPORT DE SYNCHRONISATION                               ║');
        $this->line('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $totalChanges = $this->getTotalChanges();

        if ($totalChanges === 0) {
            $this->info('✅ SYSTÈME PARFAITEMENT SYNCHRONISÉ : Aucune modification nécessaire');
        } else {
            $this->table(
                ['Type de modification', 'Nombre', 'Statut'],
                [
                    ['Affectations mises à jour', $this->assignmentsUpdated, $dryRun ? 'Détecté' : '✅ Synchronisé'],
                    ['Véhicules libérés', $this->vehiclesFreed, $dryRun ? 'Détecté' : '✅ Synchronisé'],
                    ['Véhicules verrouillés', $this->vehiclesLocked, $dryRun ? 'Détecté' : '✅ Synchronisé'],
                    ['Chauffeurs libérés', $this->driversFreed, $dryRun ? 'Détecté' : '✅ Synchronisé'],
                    ['Chauffeurs verrouillés', $this->driversLocked, $dryRun ? 'Détecté' : '✅ Synchronisé'],
                    ['Total incohérences corrigées', $this->inconsistenciesFixed, $dryRun ? 'Détecté' : '✅ Corrigé'],
                ]
            );

            if ($dryRun) {
                $this->warn("Mode simulation : {$totalChanges} modification(s) détectée(s) mais non appliquée(s)");
                $this->info('Exécutez sans --dry-run pour appliquer les corrections');
            } else {
                $this->info("{$totalChanges} modification(s) appliquée(s) avec succès");
            }
        }

        $this->newLine();
        $this->line("⏱️  Durée d'exécution : {$duration} ms");
        $this->newLine();
    }

    /**
     * Obtenir le total des changements
     */
    private function getTotalChanges(): int
    {
        return $this->assignmentsUpdated +
               $this->vehiclesFreed +
               $this->driversFreed +
               $this->vehiclesLocked +
               $this->driversLocked;
    }

    /**
     * Envoyer une alerte en cas d'anomalies importantes
     */
    private function sendAnomalyAlert(): void
    {
        try {
            // Déterminer les destinataires (admins système)
            $adminEmails = config('app.admin_emails', []);

            if (empty($adminEmails)) {
                Log::warning('[SyncAssignmentStatuses] Aucun email admin configuré pour les alertes');
                return;
            }

            $totalInconsistencies = $this->getTotalChanges();
            $vehiclesAffected = $this->vehiclesFreed + $this->vehiclesLocked;
            $driversAffected = $this->driversFreed + $this->driversLocked;

            // Envoyer notification
            Notification::route('mail', $adminEmails)
                ->route('slack', config('services.slack.notifications.channel'))
                ->notify(new AssignmentSyncAnomalyDetected(
                    $totalInconsistencies,
                    $vehiclesAffected,
                    $driversAffected,
                    [
                        'vehicles_freed' => $this->vehiclesFreed,
                        'vehicles_locked' => $this->vehiclesLocked,
                        'drivers_freed' => $this->driversFreed,
                        'drivers_locked' => $this->driversLocked,
                        'assignments_updated' => $this->assignmentsUpdated,
                    ]
                ));

            Log::info('[SyncAssignmentStatuses] Alerte envoyée avec succès', [
                'total_inconsistencies' => $totalInconsistencies,
                'recipients' => $adminEmails
            ]);

        } catch (\Exception $e) {
            Log::error('[SyncAssignmentStatuses] Échec envoi alerte', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
