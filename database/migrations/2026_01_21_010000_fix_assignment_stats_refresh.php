<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        if (!Schema::hasTable('assignments')) {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS assignment_stats_refresh ON assignments;');
        DB::statement('DROP FUNCTION IF EXISTS refresh_assignment_stats();');

        DB::statement("
            CREATE OR REPLACE FUNCTION assignment_computed_status(start_dt timestamp, end_dt timestamp)
            RETURNS text
            LANGUAGE plpgsql
            STABLE
            AS \$\$
            BEGIN
                IF start_dt > NOW() THEN
                    RETURN 'scheduled';
                ELSIF end_dt IS NULL OR end_dt > NOW() THEN
                    RETURN 'active';
                ELSE
                    RETURN 'completed';
                END IF;
            END;
            \$\$;
        ");
    }

    public function down(): void
    {
        // Intentionally left empty: the removed trigger relied on REFRESH CONCURRENTLY,
        // which is not safe inside a transaction.
    }
};
