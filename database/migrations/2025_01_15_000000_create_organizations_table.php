<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🏢 ZenFleet Organizations Table - Structure Finale
 *
 * Migration définitive pour la table organizations avec la structure
 * exacte requise pour le système ZenFleet.
 *
 * @version 4.0 (Structure Finale)
 * @compatible Laravel 12.x, PHP 8.2+
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create if organizations table doesn't exist
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
            // ===== INFORMATIONS GÉNÉRALES =====
            $table->id(); // Clé primaire (BIGINT UNSIGNED AUTO_INCREMENT)
            $table->uuid('uuid')->unique(); // UUID unique
            $table->string('name'); // Nom sous lequel l'organisation sera affichée
            $table->string('legal_name')->nullable(); // Raison sociale
            $table->string('organization_type')->nullable(); // Type d'organisation
            $table->string('industry')->nullable(); // Secteur d'activité
            $table->text('description')->nullable(); // Description
            $table->string('website')->nullable(); // Site web
            $table->string('phone_number')->nullable(); // Numéro de téléphone
            $table->string('logo_path')->nullable(); // Chemin du logo
            $table->string('status')->default('active'); // Statut (active, inactive, suspended)

            // ===== INFORMATIONS LÉGALES =====
            $table->string('trade_register')->nullable(); // Registre de commerce
            $table->string('nif')->nullable(); // Numéro d'identification fiscale
            $table->string('ai')->nullable(); // Article d'imposition
            $table->string('nis')->nullable(); // Numéro d'identification statistique
            $table->string('address'); // Adresse du siège social
            $table->string('city'); // Commune du siège social
            $table->string('zip_code')->nullable(); // Code postal
            $table->string('wilaya'); // Wilaya du siège social
            $table->string('scan_nif_path')->nullable(); // Scan du NIF
            $table->string('scan_ai_path')->nullable(); // Scan de l'AI
            $table->string('scan_nis_path')->nullable(); // Scan du NIS

            // ===== REPRÉSENTANT LÉGAL =====
            $table->string('manager_first_name')->nullable(); // Prénom du gérant
            $table->string('manager_last_name')->nullable(); // Nom du gérant
            $table->string('manager_nin')->nullable(); // NIN du gérant
            $table->string('manager_address')->nullable(); // Adresse du gérant
            $table->date('manager_dob')->nullable(); // Date de naissance du gérant
            $table->string('manager_pob')->nullable(); // Lieu de naissance du gérant
            $table->string('manager_phone_number')->nullable(); // Téléphone du gérant
            $table->string('manager_id_scan_path')->nullable(); // Scan pièce d'identité du gérant

            // ===== HORODATAGE =====
            $table->timestamps(); // created_at et updated_at

            // ===== INDEX POUR OPTIMISATION =====
            $table->index(['status']);
            $table->index(['organization_type']);
            $table->index(['city', 'wilaya']);
            $table->index(['name']);
        });
        }

        // Add organization_id to related tables
        $this->addOrganizationIdToTables();

        echo "✅ Table organizations créée avec la structure finale\n";
    }

    /**
     * Add organization_id foreign key to related tables
     */
    private function addOrganizationIdToTables(): void
    {
        $tables = [
            'users',
            'vehicles',
            'drivers',
            'assignments',
            'maintenance_plans',
            'maintenance_logs',
            'vehicle_handover_forms'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'organization_id')) {
                        $table->foreignId('organization_id')
                              ->nullable()
                              ->constrained('organizations')
                              ->onDelete('cascade');
                    }
                });
                echo "✅ organization_id ajouté à la table {$tableName}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove organization_id from related tables first
        $tables = [
            'users',
            'vehicles',
            'drivers',
            'assignments',
            'maintenance_plans',
            'maintenance_logs',
            'vehicle_handover_forms'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['organization_id']);
                    $table->dropColumn('organization_id');
                });
            }
        }

        Schema::dropIfExists('organizations');
        echo "🔄 Table organizations et relations supprimées\n";
    }
};