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
        Schema::create('repair_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            // Informations de base
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('slug', 150)->unique();

            // Personnalisation visuelle
            $table->string('icon', 50)->nullable(); // Font Awesome icon name
            $table->string('color', 20)->nullable()->default('blue'); // Couleur du badge

            // Gestion de l'affichage
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            // Métadonnées flexibles (JSON pour extension future)
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index de performance
            $table->index(['organization_id', 'is_active'], 'idx_repair_categories_org_active');
            $table->index('sort_order', 'idx_repair_categories_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_categories');
    }
};
