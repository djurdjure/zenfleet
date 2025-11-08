<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚙 MIGRATION: Mise à jour des types de véhicules - Enterprise Grade
 *
 * Objectif: Ajouter les nouveaux types requis pour le système ZenFleet
 *
 * Nouveaux types:
 * - voiture        : Voiture de tourisme ou utilitaire léger
 * - camion         : Camion ou poids lourd
 * - moto           : Moto, scooter ou deux-roues
 * - engin          : Engin spécialisé (construction, BTP, agriculture)
 * - fourgonnette   : Fourgonnette ou van utilitaire
 * - bus            : Bus ou minibus
 * - vul            : Véhicule utilitaire léger (VUL)
 * - semi_remorque  : Semi-remorque ou camion avec remorque
 * - autre          : Autre type non catégorisé
 *
 * Cette migration:
 * 1. Ajoute les colonnes de métadonnées si elles n'existent pas
 * 2. Insère les nouveaux types s'ils n'existent pas
 * 3. Préserve les données existantes
 * 4. Est idempotente (peut être exécutée plusieurs fois sans effet de bord)
 *
 * @version 2.0-Enterprise
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo "🚙 Mise à jour de la table vehicle_types - Enterprise Grade\n";

        // =====================================================================
        // ÉTAPE 1: Ajout des colonnes de métadonnées
        // =====================================================================

        if (!Schema::hasColumn('vehicle_types', 'slug')) {
            Schema::table('vehicle_types', function (Blueprint $table) {
                $table->string('slug', 100)->nullable()->after('name');
                $table->text('description')->nullable()->after('slug');
                $table->string('color', 7)->default('#6b7280')->after('description');
                $table->string('icon', 50)->default('fa-car')->after('color');
                $table->boolean('is_active')->default(true)->after('icon');
                $table->integer('sort_order')->default(0)->after('is_active');

                // Colonnes de règles métier
                $table->boolean('requires_special_license')->default(false)->after('sort_order')
                      ->comment('Nécessite un permis spécial ?');
                $table->string('required_license_category', 20)->nullable()->after('requires_special_license')
                      ->comment('Catégorie de permis requise (B, C, D, etc.)');
                $table->integer('maintenance_cost_level')->default(3)->after('required_license_category')
                      ->comment('Niveau de coût de maintenance (1-5)');
                $table->decimal('average_capacity_tons', 8, 2)->nullable()->after('maintenance_cost_level')
                      ->comment('Capacité moyenne de transport en tonnes');

                // Multi-tenant
                $table->foreignId('organization_id')->nullable()->after('average_capacity_tons')
                      ->constrained()->onDelete('cascade');

                // Timestamps
                $table->timestamps();

                // Index pour performance
                $table->index(['organization_id', 'is_active']);
                $table->index(['slug']);
                $table->index(['sort_order']);
            });

            echo "   ✅ Colonnes de métadonnées ajoutées\n";
        } else {
            echo "   ℹ️  Colonnes de métadonnées déjà présentes\n";
        }

        // =====================================================================
        // ÉTAPE 2: Mise à jour de la colonne slug pour les types existants
        // =====================================================================

        $this->generateSlugsForExistingTypes();

        // =====================================================================
        // ÉTAPE 3: Insertion des nouveaux types
        // =====================================================================

        $this->insertNewVehicleTypes();

        // =====================================================================
        // ÉTAPE 4: Statistiques finales
        // =====================================================================

        $totalTypes = DB::table('vehicle_types')->count();
        $activeTypes = DB::table('vehicle_types')->where('is_active', true)->count();

        echo "   📊 Statistiques finales:\n";
        echo "      - Total types: {$totalTypes}\n";
        echo "      - Types actifs: {$activeTypes}\n";
        echo "      - Types inactifs: " . ($totalTypes - $activeTypes) . "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne supprime pas les colonnes pour éviter la perte de données
        // On se contente de marquer les nouveaux types comme inactifs

        DB::table('vehicle_types')
            ->whereIn('slug', ['voiture', 'camion', 'moto', 'engin', 'fourgonnette', 'bus', 'vul', 'semi_remorque', 'autre'])
            ->update(['is_active' => false]);

        echo "   ⚠️  Les nouveaux types ont été désactivés (non supprimés pour préserver les données)\n";
    }

    /**
     * Génère les slugs pour les types existants
     */
    private function generateSlugsForExistingTypes(): void
    {
        $types = DB::table('vehicle_types')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        if ($types->isEmpty()) {
            echo "   ℹ️  Tous les types ont déjà un slug\n";
            return;
        }

        foreach ($types as $type) {
            $slug = \Str::slug($type->name);

            DB::table('vehicle_types')
                ->where('id', $type->id)
                ->update([
                    'slug' => $slug,
                    'updated_at' => now(),
                ]);
        }

        echo "   ✅ Slugs générés pour " . $types->count() . " types existants\n";
    }

    /**
     * Insère les nouveaux types de véhicules
     */
    private function insertNewVehicleTypes(): void
    {
        $newTypes = [
            [
                'name' => 'Voiture',
                'slug' => 'voiture',
                'description' => 'Voiture de tourisme ou véhicule utilitaire léger',
                'color' => '#3b82f6', // Bleu
                'icon' => 'fa-car',
                'is_active' => true,
                'sort_order' => 1,
                'requires_special_license' => false,
                'required_license_category' => 'B',
                'maintenance_cost_level' => 2,
                'average_capacity_tons' => 0.5,
                'organization_id' => null,
            ],
            [
                'name' => 'Camion',
                'slug' => 'camion',
                'description' => 'Camion ou poids lourd pour transport de marchandises',
                'color' => '#8b5cf6', // Violet
                'icon' => 'fa-truck',
                'is_active' => true,
                'sort_order' => 5,
                'requires_special_license' => true,
                'required_license_category' => 'C',
                'maintenance_cost_level' => 4,
                'average_capacity_tons' => 12.0,
                'organization_id' => null,
            ],
            [
                'name' => 'Moto',
                'slug' => 'moto',
                'description' => 'Moto, scooter ou deux-roues motorisé',
                'color' => '#10b981', // Vert
                'icon' => 'fa-motorcycle',
                'is_active' => true,
                'sort_order' => 2,
                'requires_special_license' => false,
                'required_license_category' => 'A',
                'maintenance_cost_level' => 2,
                'average_capacity_tons' => 0.2,
                'organization_id' => null,
            ],
            [
                'name' => 'Engin',
                'slug' => 'engin',
                'description' => 'Engin spécialisé : construction, BTP, agriculture',
                'color' => '#f59e0b', // Jaune/Orange
                'icon' => 'fa-tractor',
                'is_active' => true,
                'sort_order' => 8,
                'requires_special_license' => true,
                'required_license_category' => 'CACES',
                'maintenance_cost_level' => 5,
                'average_capacity_tons' => null,
                'organization_id' => null,
            ],
            [
                'name' => 'Fourgonnette',
                'slug' => 'fourgonnette',
                'description' => 'Fourgonnette ou van utilitaire',
                'color' => '#6366f1', // Indigo
                'icon' => 'fa-shuttle-van',
                'is_active' => true,
                'sort_order' => 4,
                'requires_special_license' => false,
                'required_license_category' => 'B',
                'maintenance_cost_level' => 3,
                'average_capacity_tons' => 2.0,
                'organization_id' => null,
            ],
            [
                'name' => 'Bus',
                'slug' => 'bus',
                'description' => 'Bus ou minibus pour transport collectif de personnes',
                'color' => '#ec4899', // Rose
                'icon' => 'fa-bus',
                'is_active' => true,
                'sort_order' => 7,
                'requires_special_license' => true,
                'required_license_category' => 'D',
                'maintenance_cost_level' => 4,
                'average_capacity_tons' => null,
                'organization_id' => null,
            ],
            [
                'name' => 'VUL',
                'slug' => 'vul',
                'description' => 'Véhicule utilitaire léger (VUL)',
                'color' => '#06b6d4', // Cyan
                'icon' => 'fa-truck-moving',
                'is_active' => true,
                'sort_order' => 3,
                'requires_special_license' => false,
                'required_license_category' => 'B',
                'maintenance_cost_level' => 3,
                'average_capacity_tons' => 1.5,
                'organization_id' => null,
            ],
            [
                'name' => 'Semi-remorque',
                'slug' => 'semi_remorque',
                'description' => 'Semi-remorque ou camion avec remorque',
                'color' => '#f97316', // Orange
                'icon' => 'fa-trailer',
                'is_active' => true,
                'sort_order' => 6,
                'requires_special_license' => true,
                'required_license_category' => 'CE',
                'maintenance_cost_level' => 5,
                'average_capacity_tons' => 24.0,
                'organization_id' => null,
            ],
            [
                'name' => 'Autre',
                'slug' => 'autre',
                'description' => 'Autre type de véhicule non catégorisé',
                'color' => '#6b7280', // Gris
                'icon' => 'fa-question-circle',
                'is_active' => true,
                'sort_order' => 9,
                'requires_special_license' => false,
                'required_license_category' => null,
                'maintenance_cost_level' => 3,
                'average_capacity_tons' => null,
                'organization_id' => null,
            ],
        ];

        $inserted = 0;
        $updated = 0;

        foreach ($newTypes as $typeData) {
            // Vérifier si le type existe déjà par nom (clé unique existante)
            $existingType = DB::table('vehicle_types')
                ->where('name', $typeData['name'])
                ->first();

            if (!$existingType) {
                // Nouveau type : insertion
                DB::table('vehicle_types')->insert(array_merge($typeData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $inserted++;
            } else {
                // Type existant : mise à jour des métadonnées
                DB::table('vehicle_types')
                    ->where('id', $existingType->id)
                    ->update([
                        'slug' => $typeData['slug'],
                        'description' => $typeData['description'],
                        'color' => $typeData['color'],
                        'icon' => $typeData['icon'],
                        'sort_order' => $typeData['sort_order'],
                        'requires_special_license' => $typeData['requires_special_license'],
                        'required_license_category' => $typeData['required_license_category'],
                        'maintenance_cost_level' => $typeData['maintenance_cost_level'],
                        'average_capacity_tons' => $typeData['average_capacity_tons'],
                        'updated_at' => now(),
                    ]);
                $updated++;
            }
        }

        if ($inserted > 0) {
            echo "   ✅ {$inserted} nouveaux types insérés\n";
        }
        if ($updated > 0) {
            echo "   🔄 {$updated} types existants mis à jour avec métadonnées\n";
        }
    }
};
