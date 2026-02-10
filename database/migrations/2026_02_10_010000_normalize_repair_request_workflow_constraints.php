<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('repair_requests')) {
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $hasStatus = Schema::hasColumn('repair_requests', 'status');
        $hasPriority = Schema::hasColumn('repair_requests', 'priority');
        $hasUrgency = Schema::hasColumn('repair_requests', 'urgency');
        $hasSupervisorDecision = Schema::hasColumn('repair_requests', 'supervisor_decision');
        $hasManagerDecision = Schema::hasColumn('repair_requests', 'manager_decision');
        $hasSupervisorStatus = Schema::hasColumn('repair_requests', 'supervisor_status');
        $hasFleetManagerStatus = Schema::hasColumn('repair_requests', 'fleet_manager_status');

        if ($hasUrgency && $hasPriority) {
            DB::statement("
                UPDATE repair_requests
                SET urgency = CASE
                    WHEN urgency IS NOT NULL AND urgency <> '' THEN urgency
                    WHEN priority = 'urgente' THEN 'critical'
                    WHEN priority = 'a_prevoir' THEN 'normal'
                    WHEN priority = 'non_urgente' THEN 'low'
                    ELSE 'normal'
                END
            ");
        }

        if ($hasUrgency) {
            DB::statement("
                UPDATE repair_requests
                SET urgency = 'normal'
                WHERE urgency IS NULL OR urgency = ''
            ");
        }

        if ($hasStatus) {
            $rejectMapping = $hasSupervisorDecision
                ? "WHEN status = 'refusee' THEN CASE WHEN supervisor_decision = 'refuse' THEN 'rejected_supervisor' ELSE 'rejected_final' END"
                : "WHEN status = 'refusee' THEN 'rejected_final'";

            DB::statement("
                UPDATE repair_requests
                SET status = CASE
                    WHEN status IN (
                        'pending_supervisor',
                        'approved_supervisor',
                        'rejected_supervisor',
                        'pending_fleet_manager',
                        'approved_final',
                        'rejected_final'
                    ) THEN status
                    WHEN status = 'en_attente' THEN 'pending_supervisor'
                    WHEN status = 'accord_initial' THEN 'pending_fleet_manager'
                    WHEN status = 'accordee' THEN 'approved_final'
                    {$rejectMapping}
                    WHEN status = 'en_cours' THEN 'approved_final'
                    WHEN status = 'terminee' THEN 'approved_final'
                    WHEN status = 'annulee' THEN 'rejected_final'
                    ELSE 'pending_supervisor'
                END
            ");
        }

        if ($hasSupervisorStatus && $hasSupervisorDecision) {
            DB::statement("
                UPDATE repair_requests
                SET supervisor_status = CASE
                    WHEN supervisor_status IS NOT NULL AND supervisor_status <> '' THEN supervisor_status
                    WHEN supervisor_decision = 'accepte' THEN 'approved'
                    WHEN supervisor_decision = 'refuse' THEN 'rejected'
                    ELSE NULL
                END
            ");
        }

        if ($hasFleetManagerStatus && $hasManagerDecision) {
            DB::statement("
                UPDATE repair_requests
                SET fleet_manager_status = CASE
                    WHEN fleet_manager_status IS NOT NULL AND fleet_manager_status <> '' THEN fleet_manager_status
                    WHEN manager_decision = 'valide' THEN 'approved'
                    WHEN manager_decision = 'refuse' THEN 'rejected'
                    ELSE NULL
                END
            ");
        }

        if ($hasStatus) {
            DB::statement("ALTER TABLE repair_requests ALTER COLUMN status SET DEFAULT 'pending_supervisor'");
        }

        if ($hasUrgency) {
            DB::statement("ALTER TABLE repair_requests ALTER COLUMN urgency SET DEFAULT 'normal'");
        }

        $legacyConstraints = [
            'repair_requests_status_check',
            'repair_requests_priority_check',
            'repair_requests_supervisor_decision_check',
            'repair_requests_manager_decision_check',
            'valid_workflow',
            'valid_completion',
            'valid_timing',
            'chk_repair_status',
            'chk_repair_urgency',
        ];

        foreach ($legacyConstraints as $constraint) {
            DB::statement("ALTER TABLE repair_requests DROP CONSTRAINT IF EXISTS {$constraint}");
        }

        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'chk_repair_status_modern'
                      AND conrelid = 'repair_requests'::regclass
                ) THEN
                    ALTER TABLE repair_requests
                    ADD CONSTRAINT chk_repair_status_modern CHECK (
                        status IN (
                            'pending_supervisor',
                            'approved_supervisor',
                            'rejected_supervisor',
                            'pending_fleet_manager',
                            'approved_final',
                            'rejected_final'
                        )
                    );
                END IF;
            END
            $$;
        ");

        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM pg_constraint
                    WHERE conname = 'chk_repair_urgency_modern'
                      AND conrelid = 'repair_requests'::regclass
                ) THEN
                    ALTER TABLE repair_requests
                    ADD CONSTRAINT chk_repair_urgency_modern CHECK (
                        urgency IN ('low', 'normal', 'high', 'critical')
                    );
                END IF;
            END
            $$;
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_repair_requests_status_org_modern
            ON repair_requests (organization_id, status, created_at DESC)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_repair_requests_driver_status_modern
            ON repair_requests (organization_id, driver_id, status)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_repair_requests_vehicle_status_modern
            ON repair_requests (organization_id, vehicle_id, status)
        ");
    }

    public function down(): void
    {
        if (!Schema::hasTable('repair_requests')) {
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE repair_requests DROP CONSTRAINT IF EXISTS chk_repair_status_modern");
        DB::statement("ALTER TABLE repair_requests DROP CONSTRAINT IF EXISTS chk_repair_urgency_modern");

        DB::statement("DROP INDEX IF EXISTS idx_repair_requests_status_org_modern");
        DB::statement("DROP INDEX IF EXISTS idx_repair_requests_driver_status_modern");
        DB::statement("DROP INDEX IF EXISTS idx_repair_requests_vehicle_status_modern");
    }
};

