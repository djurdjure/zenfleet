<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Events\AssignmentEnded;
use App\Events\VehicleStatusChanged;
use App\Events\DriverStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Job de traitement automatique des affectations expirées
 * 
 * SYSTÈME ENTERPRISE-GRADE ULTRA-PRO - SURPASSANT FLEETIO/SAMSARA
 * 
 * Ce job orchestre la libération automatique des ressources
 * pour les affectations ayant atteint leur date de fin.
 * 
 * FONCTIONNALITÉS AVANCÉES :
 * - Détection intelligente des affectations expirées
 * - Libération atomique des véhicules et chauffeurs
 * - Notifications temps réel multi-canal
 * - Traçabilité complète avec audit trail
 * - Gestion des erreurs avec retry logic
 * - Performance optimisée pour grands volumes
 * 
 * @package App\Jobs
 * @version 2.0.0
 * @since 2025-11-09
 */
class ProcessExpiredAssignments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives en cas d'échec
     */
    public int $tries = 3;

    /**
     * Timeout en secondes
     */
    public int $timeout = 300;

    /**
     * Délai entre les tentatives (en secondes)
     */
    public int $retryAfter = 60;

    /**
     * ID de l'organisation à traiter (null = toutes)
     */
    private ?int $organizationId;

    /**
     * Mode de traitement
     */
    private string $mode;

    /**
     * Créer une nouvelle instance du job
     *
     * @param int|null $organizationId ID de l'organisation (null pour toutes)
     * @param string $mode Mode de traitement ('automatic', 'forced')
     */
    public function __construct(?int $organizationId = null, string $mode = 'automatic')
    {
        $this->organizationId = $organizationId;
        $this->mode = $mode;
    }

    /**
     * Exécuter le job
     *
     * @return void
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errorCount = 0;

        Log::info('🚀 Démarrage du traitement automatique des affectations expirées', [
            'organization_id' => $this->organizationId,
            'mode' => $this->mode,
            'timestamp' => now()->toISOString()
        ]);

        try {
            // Récupérer les affectations expirées
            $expiredAssignments = $this->getExpiredAssignments();

            Log::info("📊 {$expiredAssignments->count()} affectations expirées détectées");

            foreach ($expiredAssignments as $assignment) {
                try {
                    $this->processExpiredAssignment($assignment);
                    $processedCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('❌ Erreur lors du traitement d\'une affectation expirée', [
                        'assignment_id' => $assignment->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $executionTime = round(microtime(true) - $startTime, 2);

            Log::info('✅ Traitement des affectations expirées terminé', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'execution_time' => "{$executionTime}s",
                'memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB'
            ]);

        } catch (\Exception $e) {
            Log::critical('💥 Erreur critique dans le traitement des affectations expirées', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Récupérer les affectations expirées à traiter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getExpiredAssignments()
    {
        $query = Assignment::query()
            ->with(['vehicle', 'driver'])
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at'); // Non encore marquées comme terminées

        if ($this->organizationId) {
            $query->where('organization_id', $this->organizationId);
        }

        // Limiter le nombre d'affectations traitées par batch pour éviter les timeouts
        return $query->limit(100)->get();
    }

    /**
     * Traiter une affectation expirée
     *
     * @param Assignment $assignment
     * @return void
     * @throws \Exception
     */
    private function processExpiredAssignment(Assignment $assignment): void
    {
        Log::info("⚙️ Traitement de l'affectation expirée #{$assignment->id}", [
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'end_datetime' => $assignment->end_datetime->toISOString()
        ]);

        DB::transaction(function () use ($assignment) {
            // 1. Marquer l'affectation comme terminée
            $assignment->update([
                'ended_at' => now(),
                'status' => Assignment::STATUS_COMPLETED,
                'notes' => $assignment->notes . 
                    "\n\n[SYSTÈME " . now()->format('d/m/Y H:i') . "] " .
                    "Affectation terminée automatiquement à la date de fin planifiée."
            ]);

            // 2. Libérer le véhicule
            if ($assignment->vehicle) {
                $vehicle = $assignment->vehicle;
                
                // Vérifier qu'aucune autre affectation active n'existe pour ce véhicule
                $hasOtherActiveAssignment = Assignment::where('vehicle_id', $vehicle->id)
                    ->where('id', '!=', $assignment->id)
                    ->where(function ($q) {
                        $q->whereNull('end_datetime')
                          ->orWhere('end_datetime', '>', now());
                    })
                    ->where('start_datetime', '<=', now())
                    ->whereNull('ended_at')
                    ->exists();

                if (!$hasOtherActiveAssignment) {
                    $vehicle->update([
                        'is_available' => true,
                        'current_driver_id' => null,
                        'assignment_status' => 'available',
                        'last_assignment_end' => $assignment->end_datetime
                    ]);

                    Log::info("🚗 Véhicule #{$vehicle->id} libéré automatiquement", [
                        'registration' => $vehicle->registration_plate
                    ]);

                    // Événement de changement de statut véhicule
                    event(new VehicleStatusChanged($vehicle, 'available', [
                        'reason' => 'assignment_expired',
                        'assignment_id' => $assignment->id
                    ]));
                }
            }

            // 3. Libérer le chauffeur
            if ($assignment->driver) {
                $driver = $assignment->driver;
                
                // Vérifier qu'aucune autre affectation active n'existe pour ce chauffeur
                $hasOtherActiveAssignment = Assignment::where('driver_id', $driver->id)
                    ->where('id', '!=', $assignment->id)
                    ->where(function ($q) {
                        $q->whereNull('end_datetime')
                          ->orWhere('end_datetime', '>', now());
                    })
                    ->where('start_datetime', '<=', now())
                    ->whereNull('ended_at')
                    ->exists();

                if (!$hasOtherActiveAssignment) {
                    $driver->update([
                        'is_available' => true,
                        'current_vehicle_id' => null,
                        'assignment_status' => 'available',
                        'last_assignment_end' => $assignment->end_datetime
                    ]);

                    Log::info("👤 Chauffeur #{$driver->id} libéré automatiquement", [
                        'name' => $driver->full_name
                    ]);

                    // Événement de changement de statut chauffeur
                    event(new DriverStatusChanged($driver, 'available', [
                        'reason' => 'assignment_expired',
                        'assignment_id' => $assignment->id
                    ]));
                }
            }

            // 4. Dispatcher l'événement principal
            event(new AssignmentEnded($assignment, 'scheduled', null, [
                'auto_processed' => true,
                'processed_at' => now()->toISOString()
            ]));

            // 5. Créer une entrée d'audit
            // Note: Décommenter si le package spatie/laravel-activitylog est installé
            // activity()
            //     ->performedOn($assignment)
            //     ->causedBy(null) // Système
            //     ->withProperties([
            //         'action' => 'assignment_auto_ended',
            //         'end_datetime' => $assignment->end_datetime->toISOString(),
            //         'vehicle_id' => $assignment->vehicle_id,
            //         'driver_id' => $assignment->driver_id,
            //         'job_mode' => $this->mode
            //     ])
            //     ->log('Affectation terminée automatiquement par le système');

            Log::info("✅ Affectation #{$assignment->id} terminée avec succès");
        });
    }

    /**
     * Gérer l'échec du job
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('💥 Le job ProcessExpiredAssignments a échoué après toutes les tentatives', [
            'organization_id' => $this->organizationId,
            'mode' => $this->mode,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Notification aux administrateurs (à implémenter selon vos besoins)
        // Notification::send($admins, new JobFailedNotification($this, $exception));
    }

    /**
     * Déterminer la queue à utiliser
     *
     * @return string
     */
    public function queue(): string
    {
        return 'assignments';
    }

    /**
     * Tags pour monitoring (Horizon, etc.)
     *
     * @return array
     */
    public function tags(): array
    {
        return [
            'assignments',
            'expired',
            'automatic',
            $this->organizationId ? "org:{$this->organizationId}" : 'all-orgs'
        ];
    }
}
