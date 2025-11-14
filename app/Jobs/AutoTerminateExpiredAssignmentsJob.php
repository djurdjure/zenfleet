<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Services\AssignmentTerminationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 🤖 JOB : TERMINAISON AUTOMATIQUE DES AFFECTATIONS EXPIRÉES
 *
 * Ce job détecte et termine automatiquement les affectations dont
 * la date de fin est dépassée mais qui sont toujours marquées comme actives.
 *
 * FONCTIONNEMENT :
 * - Détecte les affectations avec end_datetime <= maintenant
 * - Status actif (active ou scheduled)
 * - ended_at est NULL
 * - Termine chaque affectation via AssignmentTerminationService
 * - Gère les erreurs individuellement (une erreur ne bloque pas les autres)
 *
 * PLANIFICATION RECOMMANDÉE :
 * - Toutes les 15 minutes : $schedule->job(new AutoTerminateExpiredAssignmentsJob)->everyFifteenMinutes();
 * - Toutes les heures : $schedule->job(new AutoTerminateExpiredAssignmentsJob)->hourly();
 *
 * @version 1.0.0-Enterprise
 * @date 2025-11-14
 */
class AutoTerminateExpiredAssignmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives maximum
     */
    public int $tries = 3;

    /**
     * Timeout du job (5 minutes)
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AssignmentTerminationService $terminationService): void
    {
        Log::info('[AutoTerminateExpiredAssignments] Démarrage du job de terminaison automatique');

        $startTime = now();

        // 1. DÉTECTER LES AFFECTATIONS EXPIRÉES
        $expiredAssignments = Assignment::whereNotNull('end_datetime')
            ->where('end_datetime', '<=', now())
            ->whereNull('ended_at')
            ->whereIn('status', [Assignment::STATUS_ACTIVE, Assignment::STATUS_SCHEDULED])
            ->with(['vehicle', 'driver', 'organization'])
            ->get();

        $totalFound = $expiredAssignments->count();

        if ($totalFound === 0) {
            Log::info('[AutoTerminateExpiredAssignments] Aucune affectation expirée détectée');
            return;
        }

        Log::info('[AutoTerminateExpiredAssignments] Affectations expirées détectées', [
            'count' => $totalFound,
        ]);

        $stats = [
            'found' => $totalFound,
            'terminated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        // 2. TERMINER CHAQUE AFFECTATION
        foreach ($expiredAssignments as $assignment) {
            try {
                Log::info('[AutoTerminateExpiredAssignments] Terminaison de l\'affectation', [
                    'assignment_id' => $assignment->id,
                    'organization_id' => $assignment->organization_id,
                    'vehicle_id' => $assignment->vehicle_id,
                    'driver_id' => $assignment->driver_id,
                    'end_datetime' => $assignment->end_datetime->toISOString(),
                    'hours_overdue' => now()->diffInHours($assignment->end_datetime),
                ]);

                // Utiliser le service de terminaison atomique
                $result = $terminationService->terminateAssignment(
                    $assignment,
                    $assignment->end_datetime, // Utiliser la date de fin prévue
                    null, // Pas de kilométrage
                    'Terminaison automatique (affectation expirée)', // Notes
                    null // Système
                );

                if ($result['success']) {
                    $stats['terminated']++;

                    Log::info('[AutoTerminateExpiredAssignments] Affectation terminée avec succès', [
                        'assignment_id' => $assignment->id,
                        'actions' => $result['actions'],
                    ]);

                    // Notification optionnelle
                    try {
                        if (class_exists('\App\Notifications\AssignmentAutoTerminatedNotification')) {
                            // Notifier l'organisation
                            if ($assignment->organization && $assignment->organization->admin_users) {
                                foreach ($assignment->organization->admin_users as $admin) {
                                    $admin->notify(new \App\Notifications\AssignmentAutoTerminatedNotification($assignment));
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        // Ne pas bloquer si la notification échoue
                        Log::debug('[AutoTerminateExpiredAssignments] Impossible d\'envoyer la notification', [
                            'assignment_id' => $assignment->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    $stats['failed']++;
                    $stats['errors'][] = [
                        'assignment_id' => $assignment->id,
                        'error' => 'Terminaison échouée (result[success] = false)',
                    ];

                    Log::warning('[AutoTerminateExpiredAssignments] Échec de la terminaison', [
                        'assignment_id' => $assignment->id,
                        'result' => $result,
                    ]);
                }

            } catch (\Exception $e) {
                $stats['failed']++;
                $stats['errors'][] = [
                    'assignment_id' => $assignment->id,
                    'error' => $e->getMessage(),
                ];

                Log::error('[AutoTerminateExpiredAssignments] Erreur lors de la terminaison', [
                    'assignment_id' => $assignment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Continuer avec les autres affectations
                continue;
            }
        }

        $duration = now()->diffInSeconds($startTime);

        Log::info('[AutoTerminateExpiredAssignments] Job terminé', [
            'duration_seconds' => $duration,
            'stats' => $stats,
        ]);

        // 3. ALERTER SI BEAUCOUP D'ÉCHECS
        if ($stats['failed'] > 0 && $stats['failed'] >= ($stats['found'] * 0.5)) {
            Log::critical('[AutoTerminateExpiredAssignments] Taux d\'échec élevé détecté', [
                'failed' => $stats['failed'],
                'total' => $stats['found'],
                'failure_rate' => round(($stats['failed'] / $stats['found']) * 100, 2) . '%',
                'errors' => $stats['errors'],
            ]);

            // Optionnel : Envoyer une alerte Slack/Email
            try {
                if (class_exists('\App\Notifications\HighFailureRateAlert')) {
                    // Notifier les administrateurs système
                    // notification()->send(new HighFailureRateAlert($stats));
                }
            } catch (\Exception $e) {
                // Ne pas bloquer
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('[AutoTerminateExpiredAssignments] Le job a échoué complètement', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
