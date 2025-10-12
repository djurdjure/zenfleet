<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Create enhanced driver statuses table
     */
    public function up(): void
    {
        echo "🚛 Vérification de la table driver_statuses enterprise-grade...\n";

        // Si la table existe déjà, vérifier et ajouter les colonnes manquantes
        if (Schema::hasTable('driver_statuses')) {
            echo "   ⚠️  Table driver_statuses existe déjà, mise à jour de la structure...\n";

            Schema::table('driver_statuses', function (Blueprint $table) {
                // Ajouter les colonnes manquantes si nécessaire
                if (!Schema::hasColumn('driver_statuses', 'can_drive')) {
                    $table->boolean('can_drive')->default(true)->after('sort_order');
                }
                if (!Schema::hasColumn('driver_statuses', 'can_assign')) {
                    $table->boolean('can_assign')->default(true)->after('can_drive');
                }
                if (!Schema::hasColumn('driver_statuses', 'requires_validation')) {
                    $table->boolean('requires_validation')->default(false)->after('can_assign');
                }
            });

            // Ajouter les index manquants
            try {
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_slug_idx ON driver_statuses (slug)');
                DB::statement('CREATE INDEX IF NOT EXISTS driver_statuses_sort_order_idx ON driver_statuses (sort_order)');
            } catch (\Exception $e) {
                echo "   ⚠️  Certains index existent déjà\n";
            }

            echo "   ✅ Structure driver_statuses mise à jour\n";

            // Insérer les statuts par défaut s'ils n'existent pas déjà
            $this->insertDefaultDriverStatuses();

            return;
        }

        // Créer la nouvelle table avec structure complète si elle n'existe pas
        Schema::create('driver_statuses', function (Blueprint $table) {
            $table->id();

            // Informations de base
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6b7280'); // Couleur hex pour l'interface
            $table->string('icon', 50)->default('fa-user'); // Icône FontAwesome

            // Statut et ordre
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            // Permissions et règles
            $table->boolean('can_drive')->default(true); // Peut conduire
            $table->boolean('can_assign')->default(true); // Peut être assigné
            $table->boolean('requires_validation')->default(false); // Nécessite validation

            // Multi-tenant
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');

            // Timestamps
            $table->timestamps();

            // Index pour performance
            $table->index(['organization_id', 'is_active']);
            $table->index(['slug']);
            $table->index(['sort_order']);
        });

        echo "   ✅ Table driver_statuses créée avec structure enterprise\n";

        // Insérer les statuts par défaut
        $this->insertDefaultDriverStatuses();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_statuses');
    }

    /**
     * Insert default driver statuses
     */
    private function insertDefaultDriverStatuses(): void
    {
        echo "   📋 Vérification et insertion des statuts de chauffeurs par défaut...\n";

        // Compter les statuts existants
        $existingCount = \DB::table('driver_statuses')->count();
        if ($existingCount > 0) {
            echo "   ⚠️  {$existingCount} statuts existent déjà, skip insertion par défaut\n";
            return;
        }

        $defaultStatuses = [
            [
                'name' => 'Actif',
                'slug' => 'active',
                'description' => 'Chauffeur actif et disponible pour les affectations',
                'color' => '#10b981', // Vert
                'icon' => 'fa-check-circle',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => true,
                'requires_validation' => false,
                'sort_order' => 1,
                'organization_id' => null, // Global
            ],
            [
                'name' => 'En service',
                'slug' => 'in-service',
                'description' => 'Chauffeur actuellement en mission',
                'color' => '#3b82f6', // Bleu
                'icon' => 'fa-road',
                'is_active' => true,
                'can_drive' => true,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 2,
                'organization_id' => null,
            ],
            [
                'name' => 'En congé',
                'slug' => 'on-leave',
                'description' => 'Chauffeur en congé temporaire',
                'color' => '#f59e0b', // Orange
                'icon' => 'fa-calendar-times',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 3,
                'organization_id' => null,
            ],
            [
                'name' => 'En formation',
                'slug' => 'in-training',
                'description' => 'Chauffeur en cours de formation',
                'color' => '#8b5cf6', // Violet
                'icon' => 'fa-graduation-cap',
                'is_active' => true,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 4,
                'organization_id' => null,
            ],
            [
                'name' => 'Suspendu',
                'slug' => 'suspended',
                'description' => 'Chauffeur suspendu temporairement',
                'color' => '#ef4444', // Rouge
                'icon' => 'fa-ban',
                'is_active' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => true,
                'sort_order' => 5,
                'organization_id' => null,
            ],
            [
                'name' => 'Inactif',
                'slug' => 'inactive',
                'description' => 'Chauffeur inactif ou non disponible',
                'color' => '#6b7280', // Gris
                'icon' => 'fa-user-slash',
                'is_active' => false,
                'can_drive' => false,
                'can_assign' => false,
                'requires_validation' => false,
                'sort_order' => 6,
                'organization_id' => null,
            ],
        ];

        foreach ($defaultStatuses as $status) {
            \DB::table('driver_statuses')->insert(array_merge($status, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        echo "   ✅ " . count($defaultStatuses) . " statuts de chauffeurs insérés\n";

        // Statistiques
        $totalStatuses = \DB::table('driver_statuses')->count();
        $activeStatuses = \DB::table('driver_statuses')->where('is_active', true)->count();

        echo "   📊 Statistiques finales:\n";
        echo "      - Total statuts: {$totalStatuses}\n";
        echo "      - Statuts actifs: {$activeStatuses}\n";
        echo "      - Statuts inactifs: " . ($totalStatuses - $activeStatuses) . "\n";
    }
};