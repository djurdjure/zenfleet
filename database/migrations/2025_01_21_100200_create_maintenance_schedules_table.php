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
        if (Schema::hasTable('maintenance_schedules')) {
            return;
        }

        $hasVehiclesTable = Schema::hasTable('vehicles');

        Schema::create('maintenance_schedules', function (Blueprint $table) use ($hasVehiclesTable) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_schedules_org');

            if ($hasVehiclesTable) {
                $table->foreignId('vehicle_id')
                      ->constrained('vehicles')
                      ->onDelete('cascade')
                      ->index('idx_maintenance_schedules_vehicle');
            } else {
                // Bootstrap-safe path: FK is added later once vehicles exists.
                $table->foreignId('vehicle_id')
                      ->index('idx_maintenance_schedules_vehicle');
            }

            $table->foreignId('maintenance_type_id')
                  ->constrained('maintenance_types')
                  ->index('idx_maintenance_schedules_type');

            $table->date('next_due_date')->nullable()->index('idx_maintenance_schedules_due_date');
            $table->integer('next_due_mileage')->nullable()->index('idx_maintenance_schedules_due_mileage');
            $table->integer('interval_km')->nullable();
            $table->integer('interval_days')->nullable();
            $table->integer('alert_km_before')->default(1000);
            $table->integer('alert_days_before')->default(7);
            $table->boolean('is_active')->default(true)->index('idx_maintenance_schedules_active');

            $table->timestamps();

            // Index composé pour alertes et recherches fréquentes
            $table->index(['organization_id', 'is_active', 'next_due_date'], 'idx_maintenance_schedules_alerts');
            $table->index(['organization_id', 'vehicle_id', 'is_active'], 'idx_maintenance_schedules_vehicle_active');
            $table->index(['next_due_date', 'is_active'], 'idx_maintenance_schedules_due_active');

            // Contrainte unique pour éviter les doublons de planification
            $table->unique(['vehicle_id', 'maintenance_type_id'], 'unq_maintenance_schedules_vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
