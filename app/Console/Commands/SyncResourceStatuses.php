<?php

namespace App\Console\Commands;

use App\Jobs\SyncResourceStatusesJob;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use Illuminate\Console\Command;

/**
 * 🔧 COMMANDE DE SYNCHRONISATION ENTERPRISE-GRADE
 *
 * Synchronise les status_id avec les champs is_available pour garantir
 * la cohérence totale entre les deux systèmes de gestion des statuts.
 *
 * Utilisation:
 * - php artisan assignments:sync-resource-status        (Mode diagnostic)
 * - php artisan assignments:sync-resource-status --dry  (Simulation)
 * - php artisan assignments:sync-resource-status --force (Exécution réelle)
 * - php artisan assignments:sync-resource-status --queue (Via queue)
 *
 * @version 1.0.0-Enterprise
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
    protected $description = '🔄 Synchronise les status_id avec is_available pour garantir la cohérence des statuts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║  🚀 ZENFLEET - SYNCHRONISATION DES STATUTS DE RESSOURCES   ║');
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

        // Récupérer les statuts
        $parkingStatus = VehicleStatus::where('name', 'Parking')->first();
        $affectedStatus = VehicleStatus::where('name', 'Affecté')->first();
        $availableDriverStatus = DriverStatus::where('slug', 'disponible')
            ->orWhere('name', 'ILIKE', '%disponible%')
            ->first();

        // Analyser les véhicules
        $vehiclesAvailableButWrongStatus = Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->when($parkingStatus, fn($q) => $q->where('status_id', '!=', $parkingStatus->id))
            ->count();

        $vehiclesAssignedButWrongStatus = Vehicle::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->whereNotNull('current_driver_id')
            ->when($affectedStatus, fn($q) => $q->where('status_id', '!=', $affectedStatus->id))
            ->count();

        // Analyser les chauffeurs
        $driversAvailableButWrongStatus = Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->when($availableDriverStatus, fn($q) => $q->where('status_id', '!=', $availableDriverStatus->id))
            ->count();

        $driversAssignedButWrongStatus = Driver::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->whereNotNull('current_vehicle_id')
            ->count();

        // Afficher les résultats
        $this->table(
            ['Ressource', 'État', 'Incohérences'],
            [
                ['Véhicules', 'Disponibles (is_available=true)', $vehiclesAvailableButWrongStatus],
                ['Véhicules', 'Affectés (is_available=false)', $vehiclesAssignedButWrongStatus],
                ['Chauffeurs', 'Disponibles (is_available=true)', $driversAvailableButWrongStatus],
                ['Chauffeurs', 'En mission (is_available=false)', $driversAssignedButWrongStatus],
            ]
        );

        $totalInconsistencies = $vehiclesAvailableButWrongStatus + $vehiclesAssignedButWrongStatus +
                               $driversAvailableButWrongStatus + $driversAssignedButWrongStatus;

        if ($totalInconsistencies === 0) {
            $this->info('✅ Aucune incohérence détectée ! Tous les statuts sont synchronisés.');
        } else {
            $this->warn("⚠️  Total d'incohérences à corriger: {$totalInconsistencies}");
        }
    }

    /**
     * Simule la synchronisation sans appliquer les changements
     */
    private function simulateSynchronization(): void
    {
        $this->newLine();
        $this->info('🔍 SIMULATION DES CHANGEMENTS');
        $this->info('═══════════════════════════════════════');

        $parkingStatus = VehicleStatus::where('name', 'Parking')->first();
        $availableDriverStatus = DriverStatus::where('slug', 'disponible')
            ->orWhere('name', 'ILIKE', '%disponible%')
            ->first();

        // Véhicules à modifier
        $vehiclesToFix = Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->when($parkingStatus, fn($q) => $q->where('status_id', '!=', $parkingStatus->id))
            ->with('vehicleStatus')
            ->limit(10)
            ->get();

        if ($vehiclesToFix->count() > 0) {
            $this->info("\n📦 Véhicules qui seraient mis à jour:");
            foreach ($vehiclesToFix as $vehicle) {
                $this->line(sprintf(
                    "  • %s: %s → Parking",
                    $vehicle->registration_plate,
                    $vehicle->vehicleStatus->name ?? 'N/A'
                ));
            }
            if ($vehiclesToFix->count() === 10) {
                $this->line('  ... et plus');
            }
        }

        // Chauffeurs à modifier
        $driversToFix = Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->when($availableDriverStatus, fn($q) => $q->where('status_id', '!=', $availableDriverStatus->id))
            ->with('driverStatus')
            ->limit(10)
            ->get();

        if ($driversToFix->count() > 0) {
            $this->info("\n👤 Chauffeurs qui seraient mis à jour:");
            foreach ($driversToFix as $driver) {
                $this->line(sprintf(
                    "  • %s: %s → Disponible",
                    $driver->full_name,
                    $driver->driverStatus->name ?? 'N/A'
                ));
            }
            if ($driversToFix->count() === 10) {
                $this->line('  ... et plus');
            }
        }

        $this->newLine();
        $this->info('💡 Relancez avec --force pour appliquer ces changements');
    }

    /**
     * Exécute la synchronisation réelle
     */
    private function executeSynchronization(): void
    {
        $this->newLine();
        $this->info('⚙️  EXÉCUTION DE LA SYNCHRONISATION');
        $this->info('═══════════════════════════════════════');

        $progressBar = $this->output->createProgressBar(4);
        $progressBar->setFormat('verbose');

        // Étape 1: Véhicules disponibles
        $progressBar->setMessage('Synchronisation des véhicules disponibles...');
        $progressBar->advance();

        $parkingStatus = VehicleStatus::where('name', 'Parking')->first();
        $countVehiclesAvailable = 0;

        if ($parkingStatus) {
            $countVehiclesAvailable = Vehicle::where('is_available', true)
                ->where('assignment_status', 'available')
                ->whereNull('current_driver_id')
                ->where('status_id', '!=', $parkingStatus->id)
                ->update(['status_id' => $parkingStatus->id]);
        }

        // Étape 2: Véhicules affectés
        $progressBar->setMessage('Synchronisation des véhicules affectés...');
        $progressBar->advance();

        $affectedStatus = VehicleStatus::where('name', 'Affecté')->first();
        $countVehiclesAssigned = 0;

        if ($affectedStatus) {
            $countVehiclesAssigned = Vehicle::where('is_available', false)
                ->where('assignment_status', 'assigned')
                ->whereNotNull('current_driver_id')
                ->where('status_id', '!=', $affectedStatus->id)
                ->update(['status_id' => $affectedStatus->id]);
        }

        // Étape 3: Chauffeurs disponibles
        $progressBar->setMessage('Synchronisation des chauffeurs disponibles...');
        $progressBar->advance();

        $availableDriverStatus = DriverStatus::where('slug', 'disponible')
            ->orWhere('name', 'ILIKE', '%disponible%')
            ->first();
        $countDriversAvailable = 0;

        if ($availableDriverStatus) {
            $countDriversAvailable = Driver::where('is_available', true)
                ->where('assignment_status', 'available')
                ->whereNull('current_vehicle_id')
                ->where('status_id', '!=', $availableDriverStatus->id)
                ->update(['status_id' => $availableDriverStatus->id]);
        }

        // Étape 4: Chauffeurs en mission
        $progressBar->setMessage('Synchronisation des chauffeurs en mission...');
        $progressBar->advance();

        $onMissionStatus = DriverStatus::where('slug', 'en-mission')
            ->orWhere('name', 'ILIKE', '%mission%')
            ->first();
        $countDriversAssigned = 0;

        if ($onMissionStatus) {
            $countDriversAssigned = Driver::where('is_available', false)
                ->where('assignment_status', 'assigned')
                ->whereNotNull('current_vehicle_id')
                ->where('status_id', '!=', $onMissionStatus->id)
                ->update(['status_id' => $onMissionStatus->id]);
        }

        $progressBar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('✅ SYNCHRONISATION TERMINÉE AVEC SUCCÈS !');
        $this->newLine();

        $this->table(
            ['Type', 'Nombre de mises à jour'],
            [
                ['Véhicules disponibles', $countVehiclesAvailable],
                ['Véhicules affectés', $countVehiclesAssigned],
                ['Chauffeurs disponibles', $countDriversAvailable],
                ['Chauffeurs en mission', $countDriversAssigned],
                ['─────────────────────', '─────────────────'],
                ['TOTAL', $countVehiclesAvailable + $countVehiclesAssigned + $countDriversAvailable + $countDriversAssigned],
            ]
        );

        $this->newLine();
        $this->info('📝 Les logs détaillés sont disponibles dans storage/logs/laravel.log');
    }
}
