<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🚀 Mise à jour de la table driver_statuses vers version Enterprise-Grade
     * Architecture multi-tenant avec gestion avancée des statuts chauffeurs
     */
    public function up(): void
    {
        // Si la table existe déjà, on ajoute les colonnes manquantes
        if (Schema::hasTable('driver_statuses')) {
            echo "⚠️  Table driver_statuses existe déjà, ajout des colonnes manquantes\n";

            Schema::table('driver_statuses', function (Blueprint $table) {
                // Vérifier et ajouter chaque colonne individuellement
                if (!Schema::hasColumn('driver_statuses', 'slug')) {
                    $table->string('slug', 100)->nullable()->after('name');
                }
                if (!Schema::hasColumn('driver_statuses', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('driver_statuses', 'color')) {
                    $table->string('color', 20)->default('blue')->after('description');
                }
                if (!Schema::hasColumn('driver_statuses', 'text_color')) {
                    $table->string('text_color', 20)->default('white')->after('color');
                }
                if (!Schema::hasColumn('driver_statuses', 'icon')) {
                    $table->string('icon', 50)->nullable()->after('text_color');
                }
                if (!Schema::hasColumn('driver_statuses', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('icon');
                }
                if (!Schema::hasColumn('driver_statuses', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('is_active');
                }
                if (!Schema::hasColumn('driver_statuses', 'allows_assignments')) {
                    $table->boolean('allows_assignments')->default(true)->after('is_default');
                }
                if (!Schema::hasColumn('driver_statuses', 'is_available_for_work')) {
                    $table->boolean('is_available_for_work')->default(true)->after('allows_assignments');
                }
                if (!Schema::hasColumn('driver_statuses', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('is_available_for_work');
                }
                if (!Schema::hasColumn('driver_statuses', 'priority_level')) {
                    $table->tinyInteger('priority_level')->default(1)->after('sort_order');
                }
                if (!Schema::hasColumn('driver_statuses', 'organization_id')) {
                    $table->unsignedBigInteger('organization_id')->nullable()->after('priority_level');
                    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
                }
                if (!Schema::hasColumn('driver_statuses', 'metadata')) {
                    $table->json('metadata')->nullable()->after('organization_id');
                }
                if (!Schema::hasColumn('driver_statuses', 'valid_from')) {
                    $table->timestamp('valid_from')->nullable()->after('metadata');
                }
                if (!Schema::hasColumn('driver_statuses', 'valid_until')) {
                    $table->timestamp('valid_until')->nullable()->after('valid_from');
                }
                if (!Schema::hasColumn('driver_statuses', 'created_at')) {
                    $table->timestamps();
                }
                if (!Schema::hasColumn('driver_statuses', 'deleted_at')) {
                    $table->softDeletes();
                }
            });

            // Ajouter les index
            try {
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_is_active_index ON driver_statuses (is_active)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_is_default_index ON driver_statuses (is_default)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_sort_order_index ON driver_statuses (sort_order)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_organization_id_index ON driver_statuses (organization_id)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_org_active_sort ON driver_statuses (organization_id, is_active, sort_order)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_active_default ON driver_statuses (is_active, is_default)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_assignments_available ON driver_statuses (allows_assignments, is_available_for_work)');
            } catch (\Exception $e) {
                echo "⚠️  Certains index existent déjà\n";
            }

            // Mettre à jour la contrainte unique sur name
            try {
                DB::statement('ALTER TABLE driver_statuses DROP CONSTRAINT IF EXISTS driver_statuses_name_unique');
                DB::statement('ALTER TABLE driver_statuses ADD CONSTRAINT driver_statuses_name_org_unique UNIQUE (name, organization_id)');
                DB::statement('ALTER TABLE driver_statuses ADD CONSTRAINT driver_statuses_slug_org_unique UNIQUE (slug, organization_id)');
            } catch (\Exception $e) {
                echo "⚠️  Contraintes unique déjà en place\n";
            }

            // Générer les slugs manquants
            DB::statement("UPDATE driver_statuses SET slug = LOWER(REGEXP_REPLACE(name, '[^a-zA-Z0-9]+', '-', 'g')) WHERE slug IS NULL");

            // Rendre slug non-nullable après avoir rempli les valeurs
            DB::statement('ALTER TABLE driver_statuses ALTER COLUMN slug SET NOT NULL');

            return;
        }

        // Sinon créer la table complète
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