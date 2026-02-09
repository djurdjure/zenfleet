<?php

namespace App\Console\Commands;

use App\Jobs\SyncResourceStatusesJob;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\AssignmentPresenceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * 🔧 COMMANDE DE SYNCHRONISATION ENTERPRISE-GRADE
 *
 * Synchronise la présence des ressources (is_available, assignment_status, current_*_id)
 * à partir des affectations comme source de vérité.
 *
 * Utilisation:
 * - php artisan assignments:sync-resource-status        (Mode diagnostic)
 * - php artisan assignments:sync-resource-status --dry  (Simulation)
 * - php artisan assignments:sync-resource-status --force (Exécution réelle)
 * - php artisan assignments:sync-resource-status --queue (Via queue)
 *
 * @version 2.0.0-Enterprise
 * @author ZenFleet Team
 */
class SyncResourceStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignments:sync-resource-status
                            {--dry : Mode simulation (affiche les changements sans les appliquer)}
                            {--force : Force l\'exécution immédiate}
                            {--queue : Exécute via la queue pour les grosses flottes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '🔄 Synchronise la présence des ressources avec les affectations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║  🚀 ZENFLEET - SYNCHRONISATION PRÉSENCE RESSOURCES         ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Mode queue
        if ($this->option('queue')) {
            $this->info('📤 Dispatching job vers la queue...');
            SyncResourceStatusesJob::dispatch();
            $this->info('✅ Job ajouté à la queue avec succès !');
            $this->info('   Surveillez les logs pour voir la progression.');
            return Command::SUCCESS;
        }

        // Analyse de l'état actuel
        $this->analyzeCurrentState();
        $this->newLine();

        // Mode dry-run
        if ($this->option('dry')) {
            $this->warn('🔍 MODE SIMULATION - Aucun changement ne sera appliqué');
            $this->simulateSynchronization();
            return Command::SUCCESS;
        }

        // Mode normal: demande confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Voulez-vous procéder à la synchronisation ?', true)) {
                $this->warn('❌ Synchronisation annulée');
                return Command::SUCCESS;
            }
        }

        // Exécution réelle
        $this->executeSynchronization();

        return Command::SUCCESS;
    }

    /**
     * Analyse l'état actuel des ressources
     */
    private function analyzeCurrentState(): void
    {
        $this->info('📊 ANALYSE DE L\'ÉTAT ACTUEL');
        $this->info('═══════════════════════════════════════');

        $now = now();
        $vehicleStats = $this->countVehiclePresenceMismatches($now);
        $driverStats = $this->countDriverPresenceMismatches($now);

        $this->line('Véhicules :');
        $this->line("  - Incohérences (devraient être affectés) : {$vehicleStats['assigned_mismatch']}");
        $this->line("  - Incohérences (devraient être disponibles) : {$vehicleStats['available_mismatch']}");

        $this->newLine();

        $this->line('Chauffeurs :');
        $this->line("  - Incohérences (devraient être affectés) : {$driverStats['assigned_mismatch']}");
        $this->line("  - Incohérences (devraient être disponibles) : {$driverStats['available_mismatch']}");
    }

    /**
     * Simulation de synchronisation
     */
    private function simulateSynchronization(): void
    {
        $now = now();
        $vehicleStats = $this->countVehiclePresenceMismatches($now);
        $driverStats = $this->countDriverPresenceMismatches($now);

        $total = $vehicleStats['assigned_mismatch'] + $vehicleStats['available_mismatch']
            + $driverStats['assigned_mismatch'] + $driverStats['available_mismatch'];

        if ($total === 0) {
            $this->info('✅ Aucune incohérence détectée.');
            return;
        }

        $this->warn("⚠️  {$total} incohérence(s) détectée(s). Exécutez sans --dry pour corriger.");
    }

    /**
     * Exécuter la synchronisation réelle
     */
    private function executeSynchronization(): void
    {
        $presence = app(AssignmentPresenceService::class);
        $result = $presence->syncAll();

        $this->info('✅ Synchronisation terminée');
        $this->line("  - Véhicules synchronisés : {$result['vehicles_synced']}");
        $this->line("  - Chauffeurs synchronisés : {$result['drivers_synced']}");
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
}
