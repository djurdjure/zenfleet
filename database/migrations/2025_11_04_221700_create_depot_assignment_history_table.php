<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Drop existing indexes if any (cleanup from failed migration)
        DB::statement('DROP INDEX IF EXISTS depot_assignment_history_action_index CASCADE');
        DB::statement('DROP INDEX IF EXISTS depot_assignment_history_vehicle_id_assigned_at_index CASCADE');
        DB::statement('DROP INDEX IF EXISTS depot_assignment_history_depot_id_assigned_at_index CASCADE');
        DB::statement('DROP INDEX IF EXISTS depot_assignment_history_organization_id_assigned_at_index CASCADE');

        Schema::create('depot_assignment_history', function (Blueprint $table) {
            $table->id();

            // Vehicle being assigned
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->onDelete('cascade');

            // Depot assignment (nullable for unassignment events)
            $table->foreignId('depot_id')
                  ->nullable()
                  ->constrained('vehicle_depots')
                  ->onDelete('set null');

            // Organization for multi-tenant
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade');

            // Previous depot (for tracking changes)
            $table->foreignId('previous_depot_id')
                  ->nullable()
                  ->constrained('vehicle_depots')
                  ->onDelete('set null');

            // Action type: 'assigned', 'unassigned', 'transferred'
            $table->string('action', 20);

            // User who performed the action
            $table->foreignId('assigned_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Optional notes/reason for assignment
            $table->text('notes')->nullable();

            // Timestamp of the action
            $table->timestamp('assigned_at')->useCurrent();

            // Standard timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['vehicle_id', 'assigned_at'], 'idx_dah_vehicle_assigned');
            $table->index(['depot_id', 'assigned_at'], 'idx_dah_depot_assigned');
            $table->index(['organization_id', 'assigned_at'], 'idx_dah_org_assigned');
            $table->index('action', 'idx_dah_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('depot_assignment_history');
    }
};
