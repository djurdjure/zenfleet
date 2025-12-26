<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the old constraint
        DB::statement("ALTER TABLE driver_sanction_histories DROP CONSTRAINT IF EXISTS driver_sanction_histories_action_check");

        // Add the new constraint with expanded values
        DB::statement("ALTER TABLE driver_sanction_histories ADD CONSTRAINT driver_sanction_histories_action_check CHECK (action::text = ANY (ARRAY['created'::text, 'updated'::text, 'archived'::text, 'unarchived'::text, 'deleted'::text, 'restored'::text, 'force_deleted'::text]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new constraint
        DB::statement("ALTER TABLE driver_sanction_histories DROP CONSTRAINT IF EXISTS driver_sanction_histories_action_check");

        // Restore the old constraint
        DB::statement("ALTER TABLE driver_sanction_histories ADD CONSTRAINT driver_sanction_histories_action_check CHECK (action::text = ANY (ARRAY['created'::text, 'updated'::text, 'archived'::text, 'unarchived'::text, 'deleted'::text]))");
    }
};
