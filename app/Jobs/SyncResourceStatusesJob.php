<?php

namespace App\Jobs;

use App\Services\AssignmentPresenceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * 🔄 JOB DE RÉCONCILIATION ENTERPRISE-GRADE
 *
 * Synchronise les champs de présence (is_available, assignment_status, current_*_id)
 * à partir des affectations comme source de vérité.
 *
 * Ce job corrige les incohérences existantes dans la base de données
 * où des ressources ont une présence désynchronisée.
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
    public function handle(AssignmentPresenceService $presence): void
    {
        Log::info('🔄 Démarrage de la synchronisation de présence des ressources');

        $result = $presence->syncAll();

        Log::info('✅ Synchronisation de présence terminée avec succès', $result);
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
