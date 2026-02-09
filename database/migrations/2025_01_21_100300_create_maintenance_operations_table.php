<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('maintenance_operations')) {
            return;
        }

        $hasVehiclesTable = Schema::hasTable('vehicles');

        Schema::create('maintenance_operations', function (Blueprint $table) use ($hasVehiclesTable) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_operations_org');

            if ($hasVehiclesTable) {
                $table->foreignId('vehicle_id')
                      ->constrained('vehicles')
                      ->onDelete('cascade')
                      ->index('idx_maintenance_operations_vehicle');
            } else {
                // Legacy-safe bootstrap: add FK in a later migration once vehicles exists.
                $table->foreignId('vehicle_id')
                      ->index('idx_maintenance_operations_vehicle');
            }

            $table->foreignId('maintenance_type_id')
                  ->constrained('maintenance_types')
                  ->index('idx_maintenance_operations_type');

            $table->foreignId('maintenance_schedule_id')
                  ->nullable()
                  ->constrained('maintenance_schedules')
                  ->nullOnDelete()
                  ->index('idx_maintenance_operations_schedule');

            $table->foreignId('provider_id')
                  ->nullable()
                  ->constrained('maintenance_providers')
                  ->nullOnDelete()
                  ->index('idx_maintenance_operations_provider');

            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])
                  ->default('planned')
                  ->index('idx_maintenance_operations_status');

            $table->date('scheduled_date')->nullable()->index('idx_maintenance_operations_scheduled');
            $table->date('completed_date')->nullable()->index('idx_maintenance_operations_completed');
            $table->integer('mileage_at_maintenance')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // Audit trail complet
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->index('idx_maintenance_operations_created_by');
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->index('idx_maintenance_operations_updated_by');

            $table->timestamps();
            $table->softDeletes();

            // Index composés pour performance et rapports
            $table->index(['organization_id', 'status', 'scheduled_date'], 'idx_maintenance_operations_org_status');
            $table->index(['organization_id', 'vehicle_id', 'status'], 'idx_maintenance_operations_vehicle_status');
            $table->index(['scheduled_date', 'status'], 'idx_maintenance_operations_date_status');
            $table->index(['completed_date', 'status'], 'idx_maintenance_operations_completed_status');

            // Index pour reporting et analytics
            $table->index(['organization_id', 'completed_date', 'total_cost'], 'idx_maintenance_operations_reporting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_operations');
    }
};
