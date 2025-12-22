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
        Schema::create('handover_checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->onDelete('cascade');
            $table->jsonb('template_json');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Unique constraint: one template per vehicle type per organization
            $table->unique(['organization_id', 'vehicle_type_id'], 'unique_org_vehicle_type_template');

            // Index for efficient queries
            $table->index(['organization_id', 'is_default'], 'idx_org_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('handover_checklist_templates');
    }
};
