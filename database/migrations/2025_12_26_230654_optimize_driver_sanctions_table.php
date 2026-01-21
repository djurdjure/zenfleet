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
        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        // 1. Data Migration: Copy archived_at to deleted_at for existing records
        // Check if archived_at column exists before trying to use it to avoid errors if re-running
        if (Schema::hasColumn('driver_sanctions', 'archived_at')) {
            DB::statement("UPDATE driver_sanctions SET deleted_at = archived_at WHERE deleted_at IS NULL AND archived_at IS NOT NULL");

            if (in_array($driver, ['pgsql', 'sqlite'], true)) {
                DB::statement('DROP INDEX IF EXISTS idx_sanctions_archived');
                DB::statement('DROP INDEX IF EXISTS idx_sanctions_org_archived');
            }
        }

        // 2. Schema Optimization
        Schema::table('driver_sanctions', function (Blueprint $table) {
            // Remove redundant column
            if (Schema::hasColumn('driver_sanctions', 'archived_at')) {
                $table->dropColumn('archived_at');
            }

            // Add strategic indexes for Enterprise performance
            // Check if indexes exist first to avoid errors? Laravel usually handles this but let's be safe or just standard
            // Standard Laravel way:
            $table->index('deleted_at');
            $table->index('driver_id');
            $table->index('status');

            // Compound index for common filtered queries
            $table->index(['driver_id', 'status', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('driver_sanctions')) {
            return;
        }

        Schema::table('driver_sanctions', function (Blueprint $table) {
            if (!Schema::hasColumn('driver_sanctions', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }

            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['driver_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['driver_id', 'status', 'deleted_at']);

            $table->index('archived_at', 'idx_sanctions_archived');
            $table->index(['organization_id', 'archived_at'], 'idx_sanctions_org_archived');
        });

        // Restore data
        DB::statement("UPDATE driver_sanctions SET archived_at = deleted_at WHERE archived_at IS NULL AND deleted_at IS NOT NULL");
    }
};
