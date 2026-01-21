<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 🔄 MIGRATION ENTERPRISE-GRADE: Unification Fournisseurs
     * 
     * Objectif: Supprimer la duplication entre suppliers et maintenance_providers
     * Architecture: Une seule table suppliers pour tous les fournisseurs
     * 
     * Étapes:
     * 1. Migrer les fournisseurs maintenance_providers manquants vers suppliers
     * 2. Créer table de mapping pour mise à jour des IDs
     * 3. Mettre à jour les FK dans maintenance_operations
     * 4. Supprimer la FK vers maintenance_providers
     * 5. Créer FK vers suppliers
     * 6. Supprimer la table maintenance_providers
     * 
     * @version 1.0
     * @author ZenFleet Architecture Team - Expert Système Senior
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        DB::transaction(function () {
            echo "🔄 DÉBUT MIGRATION: maintenance_providers → suppliers\n";
            echo "====================================================\n\n";

            // ÉTAPE 1: Créer les fournisseurs manquants dans suppliers
            echo "1️⃣  Migration des fournisseurs manquants...\n";
            
            $providersToMigrate = DB::table('maintenance_providers')->get();
            $mapping = [];

            foreach ($providersToMigrate as $provider) {
                // Chercher si existe déjà dans suppliers
                $existing = DB::table('suppliers')
                    ->where('company_name', 'LIKE', '%' . $provider->name . '%')
                    ->orWhere('company_name', 'LIKE', '%' . $provider->company_name . '%')
                    ->first();

                if ($existing) {
                    echo "   ✅ '{$provider->name}' existe déjà (suppliers.id={$existing->id})\n";
                    $mapping[$provider->id] = $existing->id;
                } else {
                    // Créer dans suppliers avec toutes les colonnes obligatoires
                    $supplierId = DB::table('suppliers')->insertGetId([
                        'organization_id' => $provider->organization_id,
                        'company_name' => $provider->company_name ?? $provider->name,
                        'supplier_type' => 'mecanicien', // Type par défaut pour maintenance
                        
                        // Colonnes contact (NOT NULL) - extraire du nom si disponible
                        'contact_first_name' => $provider->name ?? 'Contact',
                        'contact_last_name' => 'Maintenance',
                        'contact_phone' => $provider->phone ?? '0000000000',
                        
                        // Colonnes optionnelles
                        'contact_email' => $provider->email,
                        'phone' => $provider->phone ?? '0000000000',
                        
                        // Adresse (NOT NULL)
                        'address' => $provider->address ?? 'Adresse non renseignée',
                        'city' => $provider->city ?? 'Non spécifié',
                        'wilaya' => '16', // Alger par défaut
                        'postal_code' => $provider->postal_code,
                        
                        // Autres champs avec defaults
                        'specialties' => json_encode([]),
                        'certifications' => json_encode([]),
                        'service_areas' => json_encode([]),
                        'documents' => json_encode([]),
                        
                        // Rating si disponible
                        'rating' => $provider->rating ?? 0,
                        
                        // Métadonnées
                        'is_active' => $provider->is_active,
                        'is_preferred' => false,
                        'is_certified' => false,
                        'blacklisted' => false,
                        'auto_score_enabled' => true,
                        
                        // Stats avec defaults
                        'total_orders' => 0,
                        'total_amount_spent' => 0,
                        'avg_order_value' => 0,
                        'completed_orders' => 0,
                        'on_time_deliveries' => 0,
                        'customer_complaints' => 0,
                        
                        // Dates
                        'created_at' => $provider->created_at,
                        'updated_at' => now(),
                    ]);
                    
                    echo "   ✅ '{$provider->name}' créé (suppliers.id={$supplierId})\n";
                    $mapping[$provider->id] = $supplierId;
                }
            }

            echo "\n2️⃣  Table de mapping créée:\n";
            foreach ($mapping as $oldId => $newId) {
                echo "   • maintenance_providers.id={$oldId} → suppliers.id={$newId}\n";
            }

            // ÉTAPE 2: Supprimer la FK vers maintenance_providers AVANT de modifier les IDs
            echo "\n3️⃣  Suppression FK vers maintenance_providers...\n";
            
            Schema::table('maintenance_operations', function (Blueprint $table) {
                $table->dropForeign('idx_maintenance_operations_provider');
            });
            
            echo "   ✅ FK supprimée\n";

            // ÉTAPE 3: Mettre à jour les provider_id dans maintenance_operations
            echo "\n4️⃣  Mise à jour des opérations...\n";
            
            $operations = DB::table('maintenance_operations')
                ->whereNotNull('provider_id')
                ->get();

            foreach ($operations as $operation) {
                if (isset($mapping[$operation->provider_id])) {
                    $newProviderId = $mapping[$operation->provider_id];
                    DB::table('maintenance_operations')
                        ->where('id', $operation->id)
                        ->update(['provider_id' => $newProviderId]);
                    
                    echo "   ✅ Opération #{$operation->id}: provider_id {$operation->provider_id} → {$newProviderId}\n";
                }
            }

            // ÉTAPE 4: Créer la FK vers suppliers
            echo "\n5️⃣  Création FK vers suppliers...\n";
            
            Schema::table('maintenance_operations', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('suppliers')
                    ->onDelete('set null')
                    ->name('fk_maintenance_operations_supplier');
            });
            
            echo "   ✅ FK créée: maintenance_operations.provider_id → suppliers.id\n";

            // ÉTAPE 5: Supprimer la table maintenance_providers
            echo "\n6️⃣  Suppression table maintenance_providers...\n";
            
            Schema::dropIfExists('maintenance_providers');
            
            echo "   ✅ Table supprimée\n";

            echo "\n====================================================\n";
            echo "✅ MIGRATION RÉUSSIE!\n";
            echo "====================================================\n";
            echo "\nRÉSUMÉ:\n";
            echo "• " . count($mapping) . " fournisseurs migrés/mappés\n";
            echo "• " . $operations->count() . " opérations mises à jour\n";
            echo "• FK redirigée vers suppliers\n";
            echo "• Table maintenance_providers supprimée\n";
            echo "\n💡 Prochaine étape: Mettre à jour le code pour utiliser Supplier\n";
        });
    }

    /**
     * Rollback de la migration
     * 
     * ATTENTION: Cette migration est destructive (supprime maintenance_providers)
     * Le rollback ne peut pas restaurer les données originales.
     * Un backup de la base est FORTEMENT recommandé avant exécution.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        DB::transaction(function () {
            echo "⚠️  ROLLBACK: Impossible de restaurer maintenance_providers sans backup\n";
            echo "La table a été supprimée et les données migrées vers suppliers.\n";
            echo "Restaurez depuis un backup si nécessaire.\n";

            // On peut seulement recréer la structure vide
            Schema::create('maintenance_providers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organization_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('company_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('postal_code')->nullable();
                $table->json('specialties')->nullable();
                $table->decimal('rating', 3, 1)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Supprimer la FK vers suppliers
            Schema::table('maintenance_operations', function (Blueprint $table) {
                $table->dropForeign('fk_maintenance_operations_supplier');
            });

            // Recréer FK vers maintenance_providers (vide)
            Schema::table('maintenance_operations', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('maintenance_providers')
                    ->onDelete('set null')
                    ->name('idx_maintenance_operations_provider');
            });

            echo "⚠️  Table maintenance_providers recréée VIDE\n";
            echo "⚠️  Restaurez les données depuis un backup\n";
        });
    }
};
