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
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_types_org');

            $table->string('name', 255)->index('idx_maintenance_types_name');
            $table->text('description')->nullable();
            $table->enum('category', ['preventive', 'corrective', 'inspection', 'revision'])
                  ->index('idx_maintenance_types_category');
            $table->boolean('is_recurring')->default(false);
            $table->integer('default_interval_km')->nullable();
            $table->integer('default_interval_days')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index('idx_maintenance_types_active');

            $table->timestamps();

            // Index composé pour performance multi-tenant
            $table->index(['organization_id', 'is_active', 'category'], 'idx_maintenance_types_composite');

            // Contrainte unique pour éviter les doublons par organisation
            $table->unique(['organization_id', 'name'], 'unq_maintenance_types_org_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_types');
    }
};