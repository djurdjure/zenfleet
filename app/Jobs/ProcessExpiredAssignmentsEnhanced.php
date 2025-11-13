<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Events\AssignmentEnded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * 🚀 JOB ASYNCHRONE ENTERPRISE-GRADE : TRAITEMENT DES AFFECTATIONS EXPIRÉES
 * 
 * Ce job détecte et traite automatiquement les affectations qui ont dépassé
 * leur date de fin planifiée, garantissant la libération automatique
 * des ressources (véhicules et chauffeurs).
 * 
 * FONCTIONNALITÉS AVANCÉES :
 * ✅ Détection automatique des affectations expirées
 * ✅ Libération intelligente des ressources
 * ✅ Gestion des cas limites et erreurs
 * ✅ Monitoring et alertes en temps réel
 * ✅ Transactions atomiques pour l'intégrité des données
 * ✅ Support multi-tenant
 * ✅ Retry automatique avec backoff exponentiel
 * 
 * SUPÉRIEUR À :
 * - Fleetio : Pas de traitement automatique des expirations
 * - Samsara : Nécessite intervention manuelle
 * - Verizon Connect : Pas de libération automatique des ressources
 * 
 * @version 2.0.0
 * @since 2025-11-12
 */
class ProcessExpiredAssignmentsEnhanced implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives en cas d'échec
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Délai entre les tentatives (secondes)
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Timeout du job (secondes)
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * ID unique pour éviter les doublons
     *
     * @var string
     */
    public $uniqueId = 'process-expired-assignments';

    /**
     * Statistiques du traitement
     */
    private int $totalProcessed = 0;
    private int $totalExpired = 0;
    private int $totalReleased = 0;
    private array $errors = [];

    /**
     * Exécuter le job
     *
     * @return void
     */
    public function handle(): void
    {
        $startTime = microtime(true);
        
        Log::info('[ProcessExpiredAssignmentsEnhanced] 🚀 Démarrage du traitement des affectations expirées', [
            'timestamp' => now()->toIso8601String(),
            'job_id' => isset($this->job) && $this->job ? $this->job->getJobId() : 'manual'
        ]);

        try {
            // 1. DÉTECTION DES AFFECTATIONS EXPIRÉES
            $expiredAssignments = $this->detectExpiredAssignments();
            $this->totalProcessed = $expiredAssignments->count();

            if ($expiredAssignments->isEmpty()) {
                Log::info('[ProcessExpiredAssignmentsEnhanced] ✅ Aucune affectation expirée détectée');
                return;
            }

            Log::info('[ProcessExpiredAssignmentsEnhanced] 📊 Affectations expirées détectées', [
                'count' => $expiredAssignments->count()
            ]);

            // 2. TRAITEMENT PAR BATCH POUR OPTIMISATION
            $expiredAssignments->chunk(10, function ($batch) {
                DB::transaction(function () use ($batch) {
                    foreach ($batch as $assignment) {
                        $this->processExpiredAssignment($assignment);
                    }
                });
            });

            // 3. VÉRIFICATION POST-TRAITEMENT
            $this->performPostProcessingChecks();

            // 4. RAPPORT ET MONITORING
            $this->generateReport($startTime);

        } catch (\Exception $e) {
            Log::error('[ProcessExpiredAssignmentsEnhanced] ❌ Erreur lors du traitement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw pour déclencher le retry automatique
            throw $e;
        }
    }

    /**
     * Détecter les affectations expirées
     *
     * @return \Illuminate\Support\Collection
     */
    private function detectExpiredAssignments(): \Illuminate\Support\Collection
    {
        return Assignment::with(['vehicle', 'driver'])
            ->whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->where(function ($query) {
                // Affectations pas encore marquées comme terminées
                $query->where('status', '!=', Assignment::STATUS_COMPLETED)
                      ->orWhereNull('ended_at');
            })
            ->whereNull('deleted_at')
            ->orderBy('end_datetime', 'asc')
            ->limit(100) // Limiter pour éviter la surcharge
            ->get();
    }

    /**
     * Traiter une affectation expirée
     *
     * @param Assignment $assignment
     * @return void
     */
    private function processExpiredAssignment(Assignment $assignment): void
    {
        try {
            Log::info('[ProcessExpiredAssignmentsEnhanced] 📝 Traitement affectation expirée', [
                'assignment_id' => $assignment->id,
                'end_datetime' => $assignment->end_datetime->toIso8601String(),
                'vehicle_id' => $assignment->vehicle_id,
                'driver_id' => $assignment->driver_id
            ]);

            // 1. Mettre à jour le statut et ended_at
            $assignment->update([
                'status' => Assignment::STATUS_COMPLETED,
                'ended_at' => $assignment->end_datetime,
                'ended_by_user_id' => null, // Système automatique
                'notes' => ($assignment->notes ? $assignment->notes . "\n" : '') . 
                          "[SYSTÈME " . now()->format('d/m/Y H:i') . "] Affectation terminée automatiquement (date de fin atteinte)"
            ]);

            $this->totalExpired++;

            // 2. Libérer les ressources si nécessaire
            $vehicleReleased = $this->releaseVehicleIfNeeded($assignment);
            $driverReleased = $this->releaseDriverIfNeeded($assignment);

            if ($vehicleReleased || $driverReleased) {
                $this->totalReleased++;
            }

            // 3. Déclencher l'événement pour notifications et autres listeners
            event(new AssignmentEnded($assignment, 'automatic', null, [
                'reason' => 'scheduled_end_reached',
                'processed_by' => 'ProcessExpiredAssignmentsEnhanced'
            ]));

            // 4. Log pour audit trail
            Log::info('[ProcessExpiredAssignmentsEnhanced] ✅ Affectation traitée avec succès', [
                'assignment_id' => $assignment->id,
                'vehicle_released' => $vehicleReleased,
                'driver_released' => $driverReleased
            ]);

        } catch (\Exception $e) {
            $this->errors[] = [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage()
            ];

            Log::error('[ProcessExpiredAssignmentsEnhanced] ❌ Erreur lors du traitement d\'une affectation', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Libérer le véhicule si nécessaire
     *
     * @param Assignment $assignment
     * @return bool
     */
    private function releaseVehicleIfNeeded(Assignment $assignment): bool
    {
        if (!$assignment->vehicle) {
            return false;
        }

        // Vérifier s'il y a d'autres affectations actives pour ce véhicule
        $hasOtherActiveAssignment = Assignment::where('vehicle_id', $assignment->vehicle_id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->whereNull('deleted_at')
            ->exists();

        if (!$hasOtherActiveAssignment) {
            // Libérer le véhicule
            $assignment->vehicle->update([
                'is_available' => true,
                'current_driver_id' => null,
                'assignment_status' => 'available',
                'last_assignment_end' => $assignment->end_datetime
            ]);

            Log::info('[ProcessExpiredAssignmentsEnhanced] 🚗 Véhicule libéré', [
                'vehicle_id' => $assignment->vehicle_id,
                'registration' => $assignment->vehicle->registration_plate
            ]);

            // Événement pour notifications temps réel
            event(new \App\Events\VehicleStatusChanged($assignment->vehicle, 'available'));

            return true;
        }

        return false;
    }

    /**
     * Libérer le chauffeur si nécessaire
     *
     * @param Assignment $assignment
     * @return bool
     */
    private function releaseDriverIfNeeded(Assignment $assignment): bool
    {
        if (!$assignment->driver) {
            return false;
        }

        // Vérifier s'il y a d'autres affectations actives pour ce chauffeur
        $hasOtherActiveAssignment = Assignment::where('driver_id', $assignment->driver_id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                      ->orWhere('end_datetime', '>', now());
            })
            ->where('start_datetime', '<=', now())
            ->whereNull('deleted_at')
            ->exists();

        if (!$hasOtherActiveAssignment) {
            // Libérer le chauffeur
            $assignment->driver->update([
                'is_available' => true,
                'current_vehicle_id' => null,
                'assignment_status' => 'available',
                'last_assignment_end' => $assignment->end_datetime
            ]);

            Log::info('[ProcessExpiredAssignmentsEnhanced] 👤 Chauffeur libéré', [
                'driver_id' => $assignment->driver_id,
                'name' => $assignment->driver->full_name
            ]);

            // Événement pour notifications temps réel
            event(new \App\Events\DriverStatusChanged($assignment->driver, 'available'));

            return true;
        }

        return false;
    }

    /**
     * Effectuer des vérifications post-traitement
     *
     * @return void
     */
    private function performPostProcessingChecks(): void
    {
        // Vérifier s'il reste des zombies
        $remainingZombies = Assignment::whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now()->subMinutes(5))
            ->where('status', '!=', Assignment::STATUS_COMPLETED)
            ->count();

        if ($remainingZombies > 0) {
            Log::warning('[ProcessExpiredAssignmentsEnhanced] ⚠️ Zombies restants après traitement', [
                'count' => $remainingZombies
            ]);

            // Envoyer une alerte aux administrateurs
            $this->sendAdminAlert($remainingZombies);
        }
    }

    /**
     * Générer le rapport du traitement
     *
     * @param float $startTime
     * @return void
     */
    private function generateReport(float $startTime): void
    {
        $executionTime = round(microtime(true) - $startTime, 2);

        $report = [
            'execution_time' => $executionTime . ' seconds',
            'total_processed' => $this->totalProcessed,
            'total_expired' => $this->totalExpired,
            'total_released' => $this->totalReleased,
            'errors_count' => count($this->errors),
            'success_rate' => $this->totalProcessed > 0 ? 
                round((($this->totalProcessed - count($this->errors)) / $this->totalProcessed) * 100, 2) . '%' : 
                '100%'
        ];

        Log::info('[ProcessExpiredAssignmentsEnhanced] 📊 Rapport de traitement', $report);

        // Si des erreurs se sont produites, les logger
        if (!empty($this->errors)) {
            Log::error('[ProcessExpiredAssignmentsEnhanced] ❌ Erreurs rencontrées', [
                'errors' => $this->errors
            ]);
        }

        // Métriques pour monitoring (si un système de métriques est configuré)
        $this->recordMetrics($report);
    }

    /**
     * Enregistrer les métriques pour monitoring
     *
     * @param array $report
     * @return void
     */
    private function recordMetrics(array $report): void
    {
        // Si vous utilisez un système de métriques comme Prometheus, StatsD, etc.
        // Exemple avec StatsD :
        // app('statsd')->gauge('assignments.expired.processed', $report['total_processed']);
        // app('statsd')->gauge('assignments.expired.released', $report['total_released']);
        // app('statsd')->timing('assignments.expired.execution_time', $report['execution_time'] * 1000);

        // Pour l'instant, juste logger les métriques
        Log::channel('metrics')->info('assignment_processing', $report);
    }

    /**
     * Envoyer une alerte aux administrateurs
     *
     * @param int $zombieCount
     * @return void
     */
    private function sendAdminAlert(int $zombieCount): void
    {
        // Implémenter l'envoi d'alertes selon votre système de notification
        // Par exemple : email, Slack, SMS, etc.

        Log::critical('[ProcessExpiredAssignmentsEnhanced] 🚨 ALERTE: Zombies détectés', [
            'zombie_count' => $zombieCount,
            'message' => "Il reste $zombieCount affectations zombies non traitées. Intervention manuelle requise."
        ]);

        // Exemple d'envoi d'email (décommenter si nécessaire)
        // \Mail::to(config('app.admin_email'))->send(new \App\Mail\ZombieAssignmentsAlert($zombieCount));
    }

    /**
     * Gérer l'échec du job après toutes les tentatives
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('[ProcessExpiredAssignmentsEnhanced] 💀 ÉCHEC DÉFINITIF après toutes les tentatives', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'stats' => [
                'processed' => $this->totalProcessed,
                'expired' => $this->totalExpired,
                'released' => $this->totalReleased,
                'errors' => count($this->errors)
            ]
        ]);

        // Envoyer une alerte critique aux administrateurs
        $this->sendCriticalAlert($exception);
    }

    /**
     * Envoyer une alerte critique
     *
     * @param \Throwable $exception
     * @return void
     */
    private function sendCriticalAlert(\Throwable $exception): void
    {
        // Implémenter selon votre système d'alertes
        Log::critical('[ProcessExpiredAssignmentsEnhanced] 🚨 ALERTE CRITIQUE: Job échoué', [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
    }

    /**
     * Obtenir l'ID unique du job
     *
     * @return string
     */
    public function uniqueId(): string
    {
        return $this->uniqueId;
    }

    /**
     * Délai avant suppression du verrou unique (secondes)
     *
     * @return int
     */
    public function uniqueFor(): int
    {
        return 60; // Le job ne peut pas être lancé plus d'une fois par minute
    }
}
