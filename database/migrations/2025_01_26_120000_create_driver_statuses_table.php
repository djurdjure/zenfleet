<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🚀 Création de la table driver_statuses Enterprise-Grade
     * Architecture multi-tenant avec gestion avancée des statuts chauffeurs
     */
    public function up(): void
    {
        Schema::create('driver_statuses', function (Blueprint $table) {
            $table->id();

            // 📋 Informations de base du statut
            $table->string('name', 100)->index(); // Nom du statut (ex: "Disponible", "En mission")
            $table->string('slug', 100)->unique(); // Slug pour URL et références
            $table->text('description')->nullable(); // Description détaillée du statut

            // 🎨 Configuration visuelle enterprise
            $table->string('color', 20)->default('blue'); // Couleur pour l'interface (blue, green, red, yellow, etc.)
            $table->string('text_color', 20)->default('white'); // Couleur du texte
            $table->string('icon', 50)->nullable(); // Icône FontAwesome ou Heroicons

            // ⚡ Paramètres fonctionnels
            $table->boolean('is_active')->default(true)->index(); // Statut actif/inactif
            $table->boolean('is_default')->default(false)->index(); // Statut par défaut
            $table->boolean('allows_assignments')->default(true); // Permet les affectations
            $table->boolean('is_available_for_work')->default(true); // Disponible pour travail

            // 📊 Priorité et ordre d'affichage
            $table->integer('sort_order')->default(0)->index(); // Ordre d'affichage
            $table->tinyInteger('priority_level')->default(1); // Niveau de priorité (1=Normal, 2=Important, 3=Urgent)

            // 🏢 Multi-tenant support
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');

            // 🔧 Métadonnées enterprise
            $table->json('metadata')->nullable(); // Données additionnelles (notifications, règles business, etc.)
            $table->timestamp('valid_from')->nullable(); // Date de début de validité
            $table->timestamp('valid_until')->nullable(); // Date de fin de validité

            // 🕒 Horodatage standard
            $table->timestamps();
            $table->softDeletes(); // Suppression logique

            // 📑 Index composites pour performances
            $table->index(['organization_id', 'is_active', 'sort_order']);
            $table->index(['is_active', 'is_default']);
            $table->index(['allows_assignments', 'is_available_for_work']);

            // 🔒 Contraintes enterprise
            $table->unique(['name', 'organization_id']); // Nom unique par organisation
            $table->unique(['slug', 'organization_id']); // Slug unique par organisation
        });

        // 📝 Commentaire de table pour documentation
        DB::statement("COMMENT ON TABLE driver_statuses IS 'Statuts des chauffeurs - Architecture multi-tenant enterprise'");
        DB::statement("COMMENT ON COLUMN driver_statuses.allows_assignments IS 'Détermine si le chauffeur peut recevoir des affectations'");
        DB::statement("COMMENT ON COLUMN driver_statuses.is_available_for_work IS 'Détermine si le chauffeur est disponible pour travailler'");
        DB::statement("COMMENT ON COLUMN driver_statuses.priority_level IS '1=Normal, 2=Important, 3=Urgent'");
    }

    /**
     * 🗑️ Suppression de la table avec vérifications de sécurité
     */
    public function down(): void
    {
        // Vérification de sécurité avant suppression
        if (Schema::hasTable('drivers')) {
            $driversCount = DB::table('drivers')->whereNotNull('status_id')->count();

            if ($driversCount > 0) {
                throw new \Exception(
                    "Impossible de supprimer la table driver_statuses: {$driversCount} chauffeur(s) sont liés à des statuts. " .
                    "Veuillez d'abord migrer les données ou supprimer les liaisons."
                );
            }
        }

        Schema::dropIfExists('driver_statuses');
    }
};