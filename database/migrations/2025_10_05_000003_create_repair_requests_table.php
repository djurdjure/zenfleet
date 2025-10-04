<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();

            // Informations principales de la demande
            $table->string('status', 50)->default('pending_supervisor');
            $table->string('title', 255);
            $table->text('description');
            $table->string('urgency', 20)->default('normal');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->integer('current_mileage')->nullable();
            $table->string('current_location', 255)->nullable();

            // Validation Superviseur
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('supervisor_status', 30)->nullable();
            $table->text('supervisor_comment')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();

            // Validation Fleet Manager
            $table->foreignId('fleet_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('fleet_manager_status', 30)->nullable();
            $table->text('fleet_manager_comment')->nullable();
            $table->timestamp('fleet_manager_approved_at')->nullable();

            // Gestion des rejets
            $table->text('rejection_reason')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();

            // Validation finale
            $table->foreignId('final_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('final_approved_at')->nullable();

            // Lien avec maintenance_operations si disponible
            $table->foreignId('maintenance_operation_id')->nullable()->constrained('maintenance_operations')->nullOnDelete();

            // Pièces jointes (JSON pour stockage flexible)
            $table->json('photos')->nullable();
            $table->json('attachments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index de performance
            $table->index(['status', 'organization_id'], 'idx_repair_requests_status_org');
            $table->index(['driver_id', 'status'], 'idx_repair_requests_driver_status');
            $table->index(['vehicle_id', 'created_at'], 'idx_repair_requests_vehicle_date');
            $table->index(['supervisor_id', 'supervisor_status'], 'idx_repair_requests_supervisor');
            $table->index(['fleet_manager_id', 'fleet_manager_status'], 'idx_repair_requests_fleet_mgr');
        });

        // CHECK constraints pour valeurs ENUM
        DB::statement("ALTER TABLE repair_requests ADD CONSTRAINT chk_repair_status CHECK (status IN ('pending_supervisor', 'approved_supervisor', 'rejected_supervisor', 'pending_fleet_manager', 'approved_final', 'rejected_final'))");
        DB::statement("ALTER TABLE repair_requests ADD CONSTRAINT chk_repair_urgency CHECK (urgency IN ('low', 'normal', 'high', 'critical'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_requests');
    }
};
