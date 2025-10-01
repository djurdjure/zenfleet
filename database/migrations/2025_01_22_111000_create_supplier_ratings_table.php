<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('repair_request_id')->nullable(); // Lié à une demande de réparation
            $table->unsignedBigInteger('rated_by'); // Utilisateur qui évalue

            // Critères d'évaluation
            $table->decimal('quality_rating', 3, 2); // Qualité du travail (1-10)
            $table->decimal('timeliness_rating', 3, 2); // Respect des délais (1-10)
            $table->decimal('communication_rating', 3, 2); // Communication (1-10)
            $table->decimal('pricing_rating', 3, 2); // Tarification (1-10)
            $table->decimal('overall_rating', 3, 2); // Note globale (1-10)

            // Commentaires
            $table->text('positive_feedback')->nullable();
            $table->text('negative_feedback')->nullable();
            $table->text('suggestions')->nullable();

            // Métadonnées
            $table->boolean('would_recommend')->default(true);
            $table->json('service_categories_rated')->nullable(); // Catégories de service évaluées

            $table->timestamps();

            // Index pour performance
            $table->index(['supplier_id', 'overall_rating']);
            $table->index(['organization_id', 'supplier_id']);
            $table->index('repair_request_id');

            // Contraintes foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('repair_request_id')->references('id')->on('repair_requests')->onDelete('set null');
            $table->foreign('rated_by')->references('id')->on('users')->onDelete('cascade');

            // Contrainte unicité : un seul rating par demande de réparation
            $table->unique(['repair_request_id']);
        });

        // Contraintes business pour les notes
        DB::statement("
            ALTER TABLE supplier_ratings
            ADD CONSTRAINT valid_ratings CHECK (
                quality_rating BETWEEN 1 AND 10 AND
                timeliness_rating BETWEEN 1 AND 10 AND
                communication_rating BETWEEN 1 AND 10 AND
                pricing_rating BETWEEN 1 AND 10 AND
                overall_rating BETWEEN 1 AND 10
            )
        ");
    }

    public function down()
    {
        Schema::dropIfExists('supplier_ratings');
    }
};