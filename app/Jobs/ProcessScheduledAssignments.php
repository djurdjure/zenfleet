<?php

namespace App\Jobs;

use App\Models\Assignment;
use App\Events\AssignmentActivated;
use App\Services\AssignmentPresenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🚀 JOB ENTERPRISE-GRADE : Gère la transition Scheduled -> Active en temps réel.
 * Exécuté toutes les minutes pour garantir une synchronisation quasi-instantanée.
 */
class ProcessScheduledAssignments implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 60;
    public $uniqueFor = 60;

    public function handle(): void
    {
        $now = now();
        $count = 0;

        // 1. Sélectionner les affectations qui DOIVENT être actives
        $assignmentsToActivate = Assignment::with(['vehicle', 'driver'])
            ->where('status', Assignment::STATUS_SCHEDULED)
            ->where('start_datetime', '<=', $now)
            ->whereNull('deleted_at')
            ->limit(50)
            ->get();

        if ($assignmentsToActivate->isEmpty()) {
            return;
        }

        Log::info('[ProcessScheduledAssignments] Affectations à activer détectées', ['count' => $assignmentsToActivate->count()]);

        foreach ($assignmentsToActivate as $assignment) {
            // Utiliser une transaction atomique pour garantir la cohérence
            DB::transaction(function () use ($assignment, &$count) {
                // 1. Mettre à jour le statut de l'affectation
                $assignment->update(['status' => Assignment::STATUS_ACTIVE]);

                // 2. Synchroniser la présence (source de vérité = assignments)
                $presence = app(AssignmentPresenceService::class);
                $presence->syncForAssignment($assignment, now());

                // 3. Déclencher l'événement
                event(new AssignmentActivated($assignment, 'automatic', null, [
                    'reason' => 'scheduled_start_reached',
                    'processed_by' => 'ProcessScheduledAssignments'
                ]));

                $count++;
            });
        }

        Log::info('[ProcessScheduledAssignments] Synchronisation terminée', ['activated_count' => $count]);
    }
}
