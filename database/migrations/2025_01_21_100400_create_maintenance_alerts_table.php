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
        Schema::create('maintenance_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_alerts_org');

            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_alerts_vehicle');

            $table->foreignId('maintenance_schedule_id')
                  ->constrained('maintenance_schedules')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_alerts_schedule');

            $table->enum('alert_type', ['km_based', 'time_based', 'overdue'])
                  ->index('idx_maintenance_alerts_type');

            $table->enum('priority', ['low', 'medium', 'high', 'critical'])
                  ->default('medium')
                  ->index('idx_maintenance_alerts_priority');

            $table->text('message');
            $table->date('due_date')->nullable()->index('idx_maintenance_alerts_due_date');
            $table->integer('due_mileage')->nullable()->index('idx_maintenance_alerts_due_mileage');

            // Système d'acquittement
            $table->boolean('is_acknowledged')->default(false)->index('idx_maintenance_alerts_acknowledged');
            $table->foreignId('acknowledged_by')
                  ->nullable()
                  ->constrained('users')
                  ->index('idx_maintenance_alerts_ack_by');
            $table->timestamp('acknowledged_at')->nullable();

            $table->timestamps();

            // Index composés pour dashboard et notifications
            $table->index(['organization_id', 'is_acknowledged', 'priority'], 'idx_maintenance_alerts_dashboard');
            $table->index(['organization_id', 'due_date', 'is_acknowledged'], 'idx_maintenance_alerts_due');
            $table->index(['is_acknowledged', 'priority', 'created_at'], 'idx_maintenance_alerts_active');

            // Index pour escalade automatique
            $table->index(['priority', 'created_at', 'is_acknowledged'], 'idx_maintenance_alerts_escalation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_alerts');
    }
};