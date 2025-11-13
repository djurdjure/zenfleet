<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔄 JOB DE RÉCONCILIATION ENTERPRISE-GRADE
 *
 * Synchronise les status_id avec les champs is_available pour garantir
 * la cohérence totale entre les deux systèmes de gestion des statuts.
 *
 * Ce job corrige les incohérences existantes dans la base de données
 * où des ressources marquées comme disponibles (is_available=true)
 * ont encore un status_id incorrect (Affecté, En mission, etc.)
 *
 * @version 1.0.0-Enterprise
 * @author ZenFleet Team
 */
class SyncResourceStatusesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Timeout du job (10 minutes pour les grosses flottes)
     */
    public $timeout = 600;

    /**
     * Nombre de tentatives en cas d'échec
     */
    public $tries = 3;

    /**
     * Délai entre les tentatives (en secondes)
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🔄 Démarrage de la synchronisation des statuts de ressources');

        DB::transaction(function () {
            $this->syncVehicleStatuses();
            $this->syncDriverStatuses();
        });

        Log::info('✅ Synchronisation des statuts terminée avec succès');
    }

    /**
     * Synchronise les statuts des véhicules
     */
    private function syncVehicleStatuses(): void
    {
        // Récupérer les IDs de statuts
        $parkingStatus = VehicleStatus::where('name', 'Parking')->first();
        $affectedStatus = VehicleStatus::where('name', 'Affecté')->first();

        if (!$parkingStatus || !$affectedStatus) {
            Log::error('❌ Impossible de trouver les statuts véhicules requis');
            return;
        }

        // 1. Synchroniser les véhicules DISPONIBLES (is_available=true)
        // qui ont un status_id incorrect (pas "Parking")
        $availableVehicles = Vehicle::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_driver_id')
            ->where('status_id', '!=', $parkingStatus->id)
            ->get();

        $countAvailable = 0;
        foreach ($availableVehicles as $vehicle) {
            $vehicle->update(['status_id' => $parkingStatus->id]);
            $countAvailable++;

            Log::debug('Véhicule disponible synchronisé', [
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_plate,
                'old_status_id' => $vehicle->getOriginal('status_id'),
                'new_status_id' => $parkingStatus->id
            ]);
        }

        // 2. Synchroniser les véhicules AFFECTÉS (is_available=false)
        // qui ont un status_id incorrect (pas "Affecté")
        $assignedVehicles = Vehicle::where('is_available', false)
            ->where('assignment_status', 'assigned')
            ->whereNotNull('current_driver_id')
            ->where('status_id', '!=', $affectedStatus->id)
            ->get();

        $countAssigned = 0;
        foreach ($assignedVehicles as $vehicle) {
            $vehicle->update(['status_id' => $affectedStatus->id]);
            $countAssigned++;

            Log::debug('Véhicule affecté synchronisé', [
                'vehicle_id' => $vehicle->id,
                'registration' => $vehicle->registration_plate,
                'old_status_id' => $vehicle->getOriginal('status_id'),
                'new_status_id' => $affectedStatus->id
            ]);
        }

        Log::info('✅ Véhicules synchronisés', [
            'available_synced' => $countAvailable,
            'assigned_synced' => $countAssigned,
            'total' => $countAvailable + $countAssigned
        ]);
    }

    /**
     * Synchronise les statuts des chauffeurs
     */
    private function syncDriverStatuses(): void
    {
        // Récupérer les IDs de statuts
        $availableStatus = DriverStatus::where('slug', 'disponible')
            ->orWhere('name', 'ILIKE', '%disponible%')
            ->first();

        $onMissionStatus = DriverStatus::where('slug', 'en-mission')
            ->orWhere('name', 'ILIKE', '%mission%')
            ->first();

        if (!$availableStatus) {
            Log::error('❌ Impossible de trouver le statut chauffeur "Disponible"');
            return;
        }

        // 1. Synchroniser les chauffeurs DISPONIBLES (is_available=true)
        // qui ont un status_id incorrect (pas "Disponible")
        $availableDrivers = Driver::where('is_available', true)
            ->where('assignment_status', 'available')
            ->whereNull('current_vehicle_id')
            ->where('status_id', '!=', $availableStatus->id)
            ->get();

        $countAvailable = 0;
        foreach ($availableDrivers as $driver) {
            $driver->update(['status_id' => $availableStatus->id]);
            $countAvailable++;

            Log::debug('Chauffeur disponible synchronisé', [
                'driver_id' => $driver->id,
                'name' => $driver->full_name,
                'old_status_id' => $driver->getOriginal('status_id'),
                'new_status_id' => $availableStatus->id
            ]);
        }

        // 2. Synchroniser les chauffeurs EN MISSION (is_available=false)
        // si le statut "En mission" existe
        $countAssigned = 0;
        if ($onMissionStatus) {
            $assignedDrivers = Driver::where('is_available', false)
                ->where('assignment_status', 'assigned')
                ->whereNotNull('current_vehicle_id')
                ->where('status_id', '!=', $onMissionStatus->id)
                ->get();

            foreach ($assignedDrivers as $driver) {
                $driver->update(['status_id' => $onMissionStatus->id]);
                $countAssigned++;

                Log::debug('Chauffeur en mission synchronisé', [
                    'driver_id' => $driver->id,
                    'name' => $driver->full_name,
                    'old_status_id' => $driver->getOriginal('status_id'),
                    'new_status_id' => $onMissionStatus->id
                ]);
            }
        }

        Log::info('✅ Chauffeurs synchronisés', [
            'available_synced' => $countAvailable,
            'assigned_synced' => $countAssigned,
            'total' => $countAvailable + $countAssigned
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Échec de la synchronisation des statuts', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
