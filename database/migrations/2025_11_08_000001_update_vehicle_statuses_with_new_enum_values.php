<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * 🚗 MIGRATION: Mise à jour des statuts de véhicules - Enterprise Grade
 *
 * Objectif: Ajouter les nouveaux statuts requis pour le système ZenFleet
 *
 * Nouveaux statuts:
 * - parking      : Véhicule disponible au parking, non affecté
 * - affecté      : Véhicule affecté à un chauffeur
 * - en_panne     : Véhicule en panne, nécessite intervention
 * - en_maintenance : Véhicule chez le réparateur
 * - reformé      : Véhicule réformé, hors service définitif
 *
 * Cette migration:
 * 1. Ajoute les colonnes de métadonnées si elles n'existent pas
 * 2. Insère les nouveaux statuts s'ils n'existent pas
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
        echo "🚗 Mise à jour de la table vehicle_statuses - Enterprise Grade\n";

        // =====================================================================
        // ÉTAPE 1: Ajout des colonnes de métadonnées
        // =====================================================================

        if (!Schema::hasColumn('vehicle_statuses', 'slug')) {
            Schema::table('vehicle_statuses', function (Blueprint $table) {
                $table->string('slug', 100)->nullable()->after('name');
                $table->text('description')->nullable()->after('slug');
                $table->string('color', 7)->default('#6b7280')->after('description');
                $table->string('icon', 50)->default('fa-car')->after('color');
                $table->boolean('is_active')->default(true)->after('icon');
                $table->integer('sort_order')->default(0)->after('is_active');

                // Colonnes de règles métier
                $table->boolean('can_be_assigned')->default(false)->after('sort_order')
                      ->comment('Le véhicule peut-il être affecté à un chauffeur ?');
                $table->boolean('is_operational')->default(true)->after('can_be_assigned')
                      ->comment('Le véhicule est-il opérationnel ?');
                $table->boolean('requires_maintenance')->default(false)->after('is_operational')
                      ->comment('Le véhicule nécessite-t-il une intervention ?');

                // Multi-tenant
                $table->foreignId('organization_id')->nullable()->after('requires_maintenance')
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
        // ÉTAPE 2: Mise à jour de la colonne slug pour les statuts existants
        // =====================================================================

        $this->generateSlugsForExistingStatuses();

        // =====================================================================
        // ÉTAPE 3: Insertion des nouveaux statuts
        // =====================================================================

        $this->insertNewVehicleStatuses();

        // =====================================================================
        // ÉTAPE 4: Statistiques finales
        // =====================================================================

        $totalStatuses = DB::table('vehicle_statuses')->count();
        $activeStatuses = DB::table('vehicle_statuses')->where('is_active', true)->count();

        echo "   📊 Statistiques finales:\n";
        echo "      - Total statuts: {$totalStatuses}\n";
        echo "      - Statuts actifs: {$activeStatuses}\n";
        echo "      - Statuts inactifs: " . ($totalStatuses - $activeStatuses) . "\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne supprime pas les colonnes pour éviter la perte de données
        // On se contente de marquer les nouveaux statuts comme inactifs

        DB::table('vehicle_statuses')
            ->whereIn('slug', ['parking', 'affecte', 'en_panne', 'en_maintenance', 'reforme'])
            ->update(['is_active' => false]);

        echo "   ⚠️  Les nouveaux statuts ont été désactivés (non supprimés pour préserver les données)\n";
    }

    /**
     * Génère les slugs pour les statuts existants
     */
    private function generateSlugsForExistingStatuses(): void
    {
        $statuses = DB::table('vehicle_statuses')
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->get();

        if ($statuses->isEmpty()) {
            echo "   ℹ️  Tous les statuts ont déjà un slug\n";
            return;
        }

        foreach ($statuses as $status) {
            $slug = \Str::slug($status->name);

            DB::table('vehicle_statuses')
                ->where('id', $status->id)
                ->update([
                    'slug' => $slug,
                    'updated_at' => now(),
                ]);
        }

        echo "   ✅ Slugs générés pour " . $statuses->count() . " statuts existants\n";
    }

    /**
     * Insère les nouveaux statuts de véhicules
     */
    private function insertNewVehicleStatuses(): void
    {
        $newStatuses = [
            [
                'name' => 'Parking',
                'slug' => 'parking',
                'description' => 'Véhicule disponible au parking, prêt pour affectation',
                'color' => '#3b82f6', // Bleu
                'icon' => 'fa-parking',
                'is_active' => true,
                'sort_order' => 1,
                'can_be_assigned' => true,
                'is_operational' => true,
                'requires_maintenance' => false,
                'organization_id' => null,
            ],
            [
                'name' => 'Affecté',
                'slug' => 'affecte',
                'description' => 'Véhicule affecté à un chauffeur, en service',
                'color' => '#10b981', // Vert
                'icon' => 'fa-user-check',
                'is_active' => true,
                'sort_order' => 2,
                'can_be_assigned' => false,
                'is_operational' => true,
                'requires_maintenance' => false,
                'organization_id' => null,
            ],
            [
                'name' => 'En panne',
                'slug' => 'en_panne',
                'description' => 'Véhicule en panne, nécessite intervention technique',
                'color' => '#ef4444', // Rouge
                'icon' => 'fa-exclamation-triangle',
                'is_active' => true,
                'sort_order' => 3,
                'can_be_assigned' => false,
                'is_operational' => false,
                'requires_maintenance' => true,
                'organization_id' => null,
            ],
            [
                'name' => 'En maintenance',
                'slug' => 'en_maintenance',
                'description' => 'Véhicule en cours de réparation chez le réparateur',
                'color' => '#f59e0b', // Orange
                'icon' => 'fa-wrench',
                'is_active' => true,
                'sort_order' => 4,
                'can_be_assigned' => false,
                'is_operational' => false,
                'requires_maintenance' => true,
                'organization_id' => null,
            ],
            [
                'name' => 'Réformé',
                'slug' => 'reforme',
                'description' => 'Véhicule réformé, hors service définitif',
                'color' => '#6b7280', // Gris
                'icon' => 'fa-archive',
                'is_active' => false,
                'sort_order' => 5,
                'can_be_assigned' => false,
                'is_operational' => false,
                'requires_maintenance' => false,
                'organization_id' => null,
            ],
        ];

        $inserted = 0;
        $skipped = 0;
        $updated = 0;

        foreach ($newStatuses as $statusData) {
            // Vérifier si le statut existe déjà par nom (clé unique existante)
            $existingStatus = DB::table('vehicle_statuses')
                ->where('name', $statusData['name'])
                ->first();

            if (!$existingStatus) {
                // Nouveau statut : insertion
                DB::table('vehicle_statuses')->insert(array_merge($statusData, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
                $inserted++;
            } else {
                // Statut existant : mise à jour des métadonnées
                DB::table('vehicle_statuses')
                    ->where('id', $existingStatus->id)
                    ->update([
                        'slug' => $statusData['slug'],
                        'description' => $statusData['description'],
                        'color' => $statusData['color'],
                        'icon' => $statusData['icon'],
                        'sort_order' => $statusData['sort_order'],
                        'can_be_assigned' => $statusData['can_be_assigned'],
                        'is_operational' => $statusData['is_operational'],
                        'requires_maintenance' => $statusData['requires_maintenance'],
                        'updated_at' => now(),
                    ]);
                $updated++;
            }
        }

        if ($inserted > 0) {
            echo "   ✅ {$inserted} nouveaux statuts insérés\n";
        }
        if ($updated > 0) {
            echo "   🔄 {$updated} statuts existants mis à jour avec métadonnées\n";
        }
    }
};
