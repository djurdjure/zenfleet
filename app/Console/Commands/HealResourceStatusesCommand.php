<?php

namespace App\Console\Commands;

use App\Services\ResourceStatusSynchronizer;
use Illuminate\Console\Command;

/**
 * 🔧 COMMANDE ARTISAN : DÉTECTION ET CORRECTION DES ZOMBIES
 *
 * Cette commande utilise le service ResourceStatusSynchronizer pour détecter
 * et corriger automatiquement toutes les incohérences de statuts.
 *
 * UTILISATION :
 * php artisan resources:heal-statuses                    # Correction réelle
 * php artisan resources:heal-statuses --dry-run          # Simulation (aucune modification)
 * php artisan resources:heal-statuses --verbose          # Avec détails
 *
 * PLANIFICATION :
 * Cette commande peut être planifiée dans app/Console/Kernel.php :
 * $schedule->command('resources:heal-statuses')->hourly();
 *
 * @version 1.0.0
 * @date 2025-11-14
 */
class HealResourceStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'resources:heal-statuses
                            {--dry-run : Afficher les modifications sans les appliquer}
                            {--details : Afficher les détails de chaque correction}';

    /**
     * The console command description.
     */
    protected $description = 'Détecte et corrige les incohérences de statuts des ressources (véhicules et chauffeurs)';

    /**
     * Execute the console command.
     */
    public function handle(ResourceStatusSynchronizer $synchronizer): int
    {
        $this->info('🔍 Détection des incohérences de statuts...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $verbose = $this->option('details');

        if ($dryRun) {
            $this->warn('⚠️ MODE SIMULATION : Aucune modification ne sera appliquée');
            $this->newLine();
        }

        // Exécuter le healing
        if ($dryRun) {
            // Mode simulation : compter sans corriger
            $vehicleStats = $this->simulateVehicleHealing();
            $driverStats = $this->simulateDriverHealing();
        } else {
            // Mode réel : détecter et corriger
            $vehicleStats = $synchronizer->healAllVehicleZombies();
            $driverStats = $synchronizer->healAllDriverZombies();
        }

        // Afficher les résultats
        $this->displayResults($vehicleStats, $driverStats, $verbose);

        // Message final
        $this->newLine();
        $totalHealed = ($vehicleStats['zombies_healed'] ?? 0) + ($driverStats['zombies_healed'] ?? 0);

        if ($totalHealed === 0) {
            $this->info('✅ Aucune incohérence détectée. Le système est parfaitement cohérent !');
        } else {
            if ($dryRun) {
                $this->warn("⚠️ {$totalHealed} zombie(s) détecté(s) en mode simulation");
                $this->info('💡 Exécutez sans --dry-run pour appliquer les corrections');
            } else {
                $this->info("✅ {$totalHealed} zombie(s) corrigé(s) avec succès !");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Simule le healing des véhicules (mode dry-run)
     */
    private function simulateVehicleHealing(): array
    {
        $zombiesAvailable = \App\Models\Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->where('status_id', '!=', 8)
            ->whereNull('deleted_at')
            ->get();

        $zombiesAssigned = \App\Models\Vehicle::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->where('status_id', '!=', 9)
            ->whereNull('deleted_at')
            ->get();

        return [
            'type' => 'vehicles',
            'zombies_found' => $zombiesAvailable->count() + $zombiesAssigned->count(),
            'zombies_healed' => $zombiesAvailable->count() + $zombiesAssigned->count(), // En dry-run, on considère qu'ils seraient guéris
            'details' => [
                'available_with_wrong_status' => $zombiesAvailable->count(),
                'assigned_with_wrong_status' => $zombiesAssigned->count(),
            ]
        ];
    }

    /**
     * Simule le healing des chauffeurs (mode dry-run)
     */
    private function simulateDriverHealing(): array
    {
        $zombiesAvailable = \App\Models\Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->where('status_id', '!=', 7)
            ->whereNull('deleted_at')
            ->get();

        $zombiesAssigned = \App\Models\Driver::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->where('status_id', '!=', 8)
            ->whereNull('deleted_at')
            ->get();

        return [
            'type' => 'drivers',
            'zombies_found' => $zombiesAvailable->count() + $zombiesAssigned->count(),
            'zombies_healed' => $zombiesAvailable->count() + $zombiesAssigned->count(),
            'details' => [
                'available_with_wrong_status' => $zombiesAvailable->count(),
                'assigned_with_wrong_status' => $zombiesAssigned->count(),
            ]
        ];
    }

    /**
     * Affiche les résultats de la détection/correction
     */
    private function displayResults(array $vehicleStats, array $driverStats, bool $verbose): void
    {
        // Résumé véhicules
        $this->info('1️⃣ Véhicules zombies :');
        $this->line("   Détectés : {$vehicleStats['zombies_found']}");
        $this->line("   Corrigés : {$vehicleStats['zombies_healed']}");

        if ($verbose && isset($vehicleStats['details'])) {
            $this->line("      - Disponibles avec mauvais status_id : {$vehicleStats['details']['available_with_wrong_status']}");
            $this->line("      - Affectés avec mauvais status_id : {$vehicleStats['details']['assigned_with_wrong_status']}");
        }

        $this->newLine();

        // Résumé chauffeurs
        $this->info('2️⃣ Chauffeurs zombies :');
        $this->line("   Détectés : {$driverStats['zombies_found']}");
        $this->line("   Corrigés : {$driverStats['zombies_healed']}");

        if ($verbose && isset($driverStats['details'])) {
            $this->line("      - Disponibles avec mauvais status_id : {$driverStats['details']['available_with_wrong_status']}");
            $this->line("      - Affectés avec mauvais status_id : {$driverStats['details']['assigned_with_wrong_status']}");
        }

        $this->newLine();

        // Tableau récapitulatif
        $this->table(
            ['Type', 'Détectés', 'Corrigés'],
            [
                ['Véhicules', $vehicleStats['zombies_found'], $vehicleStats['zombies_healed']],
                ['Chauffeurs', $driverStats['zombies_found'], $driverStats['zombies_healed']],
                ['TOTAL', $vehicleStats['zombies_found'] + $driverStats['zombies_found'], $vehicleStats['zombies_healed'] + $driverStats['zombies_healed']],
            ]
        );
    }
}
