<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Services\AssignmentTerminationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 🎯 COMMANDE ARTISAN : TERMINER UNE AFFECTATION
 *
 * Cette commande permet de terminer manuellement une affectation via CLI
 * en utilisant le service AssignmentTerminationService pour garantir
 * l'atomicité et la cohérence.
 *
 * UTILISATION :
 * php artisan assignment:terminate {id} [--end-time=...] [--mileage=...] [--notes=...]
 *
 * EXEMPLES :
 * php artisan assignment:terminate 25
 * php artisan assignment:terminate 25 --end-time="2025-11-14 18:00:00"
 * php artisan assignment:terminate 25 --mileage=150000 --notes="Terminaison manuelle"
 *
 * @version 1.0.0-Enterprise
 * @date 2025-11-14
 */
class TerminateAssignmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignment:terminate
                            {id : ID de l\'affectation à terminer}
                            {--end-time= : Date/heure de fin (format: Y-m-d H:i:s)}
                            {--mileage= : Kilométrage de fin}
                            {--notes= : Notes de terminaison}
                            {--force : Forcer la terminaison même si l\'affectation n\'est pas active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Termine manuellement une affectation avec synchronisation atomique des ressources';

    private AssignmentTerminationService $terminationService;

    /**
     * Create a new command instance.
     */
    public function __construct(AssignmentTerminationService $terminationService)
    {
        parent::__construct();
        $this->terminationService = $terminationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $assignmentId = $this->argument('id');
        $endTimeStr = $this->option('end-time');
        $mileage = $this->option('mileage');
        $notes = $this->option('notes');
        $force = $this->option('force');

        $this->info("═══════════════════════════════════════════════════════════════");
        $this->info("🎯 TERMINAISON D'AFFECTATION");
        $this->info("═══════════════════════════════════════════════════════════════");
        $this->newLine();

        // 1. CHARGER L'AFFECTATION
        $assignment = Assignment::with(['vehicle', 'driver', 'organization'])->find($assignmentId);

        if (!$assignment) {
            $this->error("❌ Affectation ID {$assignmentId} introuvable");
            return self::FAILURE;
        }

        // 2. AFFICHER L'ÉTAT ACTUEL
        $this->info("📋 Affectation #{$assignment->id}");
        $this->line("   Organisation: {$assignment->organization->name}");
        $this->line("   Status: {$assignment->status}");
        $this->line("   Début: {$assignment->start_datetime->format('d/m/Y H:i:s')}");
        $this->line("   Fin prévue: " . ($assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y H:i:s') : 'Indéterminée'));
        $this->line("   Terminée le: " . ($assignment->ended_at ? $assignment->ended_at->format('d/m/Y H:i:s') : 'Non terminée'));
        $this->newLine();

        if ($assignment->vehicle) {
            $this->line("🚗 Véhicule: {$assignment->vehicle->registration_plate}");
            $this->line("   Disponible: " . ($assignment->vehicle->is_available ? 'OUI' : 'NON'));
            $this->line("   Statut affectation: {$assignment->vehicle->assignment_status}");
            $this->line("   Status ID: {$assignment->vehicle->status_id}");
        } else {
            $this->warn("⚠️  Aucun véhicule associé");
        }
        $this->newLine();

        if ($assignment->driver) {
            $this->line("👨‍✈️ Chauffeur: {$assignment->driver->first_name} {$assignment->driver->last_name}");
            $this->line("   Disponible: " . ($assignment->driver->is_available ? 'OUI' : 'NON'));
            $this->line("   Statut affectation: {$assignment->driver->assignment_status}");
            $this->line("   Status ID: {$assignment->driver->status_id}");
        } else {
            $this->warn("⚠️  Aucun chauffeur associé");
        }
        $this->newLine();

        // 3. VÉRIFIER SI LA TERMINAISON EST POSSIBLE
        if (!$assignment->canBeEnded() && !$force) {
            $this->error("❌ Cette affectation ne peut pas être terminée dans son état actuel");
            $this->warn("   Status actuel: {$assignment->status}");
            $this->warn("   Utilisez --force pour forcer la terminaison");
            return self::FAILURE;
        }

        // 4. PARSER LA DATE DE FIN
        $endTime = null;
        if ($endTimeStr) {
            try {
                $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $endTimeStr);
                $this->line("📅 Date de fin: {$endTime->format('d/m/Y H:i:s')}");
            } catch (\Exception $e) {
                $this->error("❌ Format de date invalide. Utilisez: Y-m-d H:i:s (ex: 2025-11-14 18:00:00)");
                return self::FAILURE;
            }
        } else {
            $endTime = now();
            $this->line("📅 Date de fin: Maintenant ({$endTime->format('d/m/Y H:i:s')})");
        }
        $this->newLine();

        // 5. AFFICHER LES PARAMÈTRES
        if ($mileage) {
            $this->line("🛣️  Kilométrage de fin: {$mileage} km");
        }
        if ($notes) {
            $this->line("📝 Notes: {$notes}");
        }
        $this->newLine();

        // 6. DEMANDER CONFIRMATION
        if (!$this->confirm('Confirmer la terminaison de cette affectation ?', true)) {
            $this->warn('⚠️  Opération annulée par l\'utilisateur');
            return self::SUCCESS;
        }
        $this->newLine();

        // 7. TERMINER L'AFFECTATION
        $this->info("🔧 Terminaison en cours...");
        $this->newLine();

        try {
            $result = $this->terminationService->terminateAssignment(
                $assignment,
                $endTime,
                $mileage ? (int)$mileage : null,
                $notes,
                1 // CLI user ID
            );

            if ($result['success']) {
                $this->info("✅ TERMINAISON RÉUSSIE");
                $this->newLine();

                $this->info("📊 Actions effectuées:");
                foreach ($result['actions'] as $action) {
                    $this->line("   ✓ " . $this->formatAction($action));
                }
                $this->newLine();

                // Vérification finale
                $assignment->refresh();
                if ($assignment->vehicle) {
                    $assignment->vehicle->refresh();
                }
                if ($assignment->driver) {
                    $assignment->driver->refresh();
                }

                $this->info("═══════════════════════════════════════════════════════════════");
                $this->info("📊 ÉTAT FINAL");
                $this->info("═══════════════════════════════════════════════════════════════");
                $this->line("Affectation: {$assignment->status}");
                if ($assignment->vehicle) {
                    $this->line("Véhicule: " . ($assignment->vehicle->is_available ? 'Disponible' : 'Affecté') . " (status_id: {$assignment->vehicle->status_id})");
                }
                if ($assignment->driver) {
                    $this->line("Chauffeur: " . ($assignment->driver->is_available ? 'Disponible' : 'En mission') . " (status_id: {$assignment->driver->status_id})");
                }
                $this->newLine();

                return self::SUCCESS;
            } else {
                $this->error("❌ La terminaison a échoué");
                return self::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("❌ ERREUR LORS DE LA TERMINAISON");
            $this->error("   Message: {$e->getMessage()}");
            $this->error("   Fichier: {$e->getFile()}:{$e->getLine()}");
            $this->newLine();

            return self::FAILURE;
        }
    }

    /**
     * Formate les actions pour l'affichage
     */
    private function formatAction(string $action): string
    {
        $actionLabels = [
            'assignment_terminated' => 'Affectation terminée',
            'vehicle_released' => 'Véhicule libéré',
            'driver_released' => 'Chauffeur libéré',
            'vehicle_not_released_other_assignment' => 'Véhicule non libéré (autre affectation active)',
            'driver_not_released_other_assignment' => 'Chauffeur non libéré (autre affectation active)',
            'vehicle_mileage_updated' => 'Kilométrage véhicule mis à jour',
            'mileage_history_created' => 'Historique kilométrage créé',
            'events_dispatched' => 'Événements dispatchés',
        ];

        return $actionLabels[$action] ?? $action;
    }
}
