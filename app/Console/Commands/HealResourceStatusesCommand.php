<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\AssignmentPresenceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 🔧 COMMANDE ARTISAN : DÉTECTION ET CORRECTION DES ZOMBIES DE PRÉSENCE
 *
 * Cette commande détecte et corrige les incohérences de présence
 * (is_available, assignment_status, current_*_id) à partir des affectations.
 *
 * UTILISATION :
 * php artisan resources:heal-statuses                    # Correction réelle
 * php artisan resources:heal-statuses --dry-run          # Simulation (aucune modification)
 * php artisan resources:heal-statuses --details          # Avec détails
 *
 * @version 2.0.0
 * @date 2026-02-07
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
    protected $description = 'Détecte et corrige les incohérences de présence des ressources (véhicules et chauffeurs)';

    /**
     * Execute the console command.
     */
    public function handle(AssignmentPresenceService $presence): int
    {
        $this->info('🔍 Détection des incohérences de présence...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $verbose = $this->option('details');

        if ($dryRun) {
            $this->warn('⚠️ MODE SIMULATION : Aucune modification ne sera appliquée');
            $this->newLine();
        }

        $now = now();
        $vehicleStats = $this->countVehiclePresenceMismatches($now);
        $driverStats = $this->countDriverPresenceMismatches($now);

        if (!$dryRun) {
            $presence->syncAll();
            $vehicleStatsAfter = $this->countVehiclePresenceMismatches(now());
            $driverStatsAfter = $this->countDriverPresenceMismatches(now());
        } else {
            $vehicleStatsAfter = $vehicleStats;
            $driverStatsAfter = $driverStats;
        }

        $this->displayResults($vehicleStats, $driverStats, $vehicleStatsAfter, $driverStatsAfter, $verbose, $dryRun);

        return self::SUCCESS;
    }

    private function countVehiclePresenceMismatches(Carbon $now): array
    {
        $activeVehicleIds = Assignment::query()
            ->select('vehicle_id')
            ->whereNotNull('vehicle_id')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where('start_datetime', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', $now);
            })
            ->groupBy('vehicle_id');

        $assignedMismatch = Vehicle::query()
            ->whereIn('id', $activeVehicleIds)
            ->where(function ($q) {
                $q->where('is_available', true)
                    ->orWhere('assignment_status', '!=', 'assigned')
                    ->orWhereNull('current_driver_id');
            })
            ->count();

        $availableMismatch = Vehicle::query()
            ->whereNotIn('id', $activeVehicleIds)
            ->where(function ($q) {
                $q->where('is_available', false)
                    ->orWhere('assignment_status', '!=', 'available')
                    ->orWhereNotNull('current_driver_id');
            })
            ->count();

        return [
            'assigned_mismatch' => $assignedMismatch,
            'available_mismatch' => $availableMismatch,
        ];
    }

    private function countDriverPresenceMismatches(Carbon $now): array
    {
        $activeDriverIds = Assignment::query()
            ->select('driver_id')
            ->whereNotNull('driver_id')
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', Assignment::STATUS_CANCELLED);
            })
            ->where('start_datetime', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>', $now);
            })
            ->groupBy('driver_id');

        $assignedMismatch = Driver::query()
            ->whereIn('id', $activeDriverIds)
            ->where(function ($q) {
                $q->where('is_available', true)
                    ->orWhere('assignment_status', '!=', 'assigned')
                    ->orWhereNull('current_vehicle_id');
            })
            ->count();

        $availableMismatch = Driver::query()
            ->whereNotIn('id', $activeDriverIds)
            ->where(function ($q) {
                $q->where('is_available', false)
                    ->orWhere('assignment_status', '!=', 'available')
                    ->orWhereNotNull('current_vehicle_id');
            })
            ->count();

        return [
            'assigned_mismatch' => $assignedMismatch,
            'available_mismatch' => $availableMismatch,
        ];
    }

    /**
     * Affiche les résultats de la détection/correction
     */
    private function displayResults(array $vehicleBefore, array $driverBefore, array $vehicleAfter, array $driverAfter, bool $verbose, bool $dryRun): void
    {
        $this->info('1️⃣ Véhicules :');
        $this->line("   Détectés (doivent être affectés) : {$vehicleBefore['assigned_mismatch']}");
        $this->line("   Détectés (doivent être disponibles) : {$vehicleBefore['available_mismatch']}");

        $this->newLine();

        $this->info('2️⃣ Chauffeurs :');
        $this->line("   Détectés (doivent être affectés) : {$driverBefore['assigned_mismatch']}");
        $this->line("   Détectés (doivent être disponibles) : {$driverBefore['available_mismatch']}");

        $this->newLine();

        if (!$dryRun) {
            $this->info('✅ Résultats après correction :');
            $this->line("   Véhicules restants : " . ($vehicleAfter['assigned_mismatch'] + $vehicleAfter['available_mismatch']));
            $this->line("   Chauffeurs restants : " . ($driverAfter['assigned_mismatch'] + $driverAfter['available_mismatch']));
        } elseif ($verbose) {
            $this->line('ℹ️ Mode simulation : aucun changement appliqué.');
        }
    }
}
