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
        Schema::create('maintenance_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                  ->constrained('organizations')
                  ->onDelete('cascade')
                  ->index('idx_maintenance_providers_org');

            $table->string('name', 255);
            $table->string('company_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->json('specialties')->nullable(); // ['brake', 'engine', 'electrical', etc.]
            $table->decimal('rating', 2, 1)->nullable(); // 0.0 - 5.0
            $table->boolean('is_active')->default(true)->index('idx_maintenance_providers_active');

            $table->timestamps();

            // Index composé pour recherche et performance
            $table->index(['organization_id', 'is_active'], 'idx_maintenance_providers_org_active');
            $table->index(['organization_id', 'city'], 'idx_maintenance_providers_org_city');

            // Index pour recherche par email et téléphone
            $table->index('email', 'idx_maintenance_providers_email');
            $table->index('phone', 'idx_maintenance_providers_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_providers');
    }
};