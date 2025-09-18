<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🏢 ZenFleet Organizations Table - Mise à jour Structure
 *
 * Migration pour compléter la structure de la table organizations
 * avec les champs manquants selon la spécification finale.
 *
 * @version 1.0
 * @compatible Laravel 12.x, PHP 8.2+
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Ajouter les champs manquants pour le représentant légal
            if (!Schema::hasColumn('organizations', 'manager_first_name')) {
                $table->string('manager_first_name')->nullable()->after('wilaya');
            }
            if (!Schema::hasColumn('organizations', 'manager_last_name')) {
                $table->string('manager_last_name')->nullable()->after('manager_first_name');
            }
            if (!Schema::hasColumn('organizations', 'manager_address')) {
                $table->string('manager_address')->nullable()->after('manager_nin');
            }
            if (!Schema::hasColumn('organizations', 'manager_dob')) {
                $table->date('manager_dob')->nullable()->after('manager_address');
            }
            if (!Schema::hasColumn('organizations', 'manager_pob')) {
                $table->string('manager_pob')->nullable()->after('manager_dob');
            }
            if (!Schema::hasColumn('organizations', 'manager_phone_number')) {
                $table->string('manager_phone_number')->nullable()->after('manager_pob');
            }
            if (!Schema::hasColumn('organizations', 'manager_id_scan_path')) {
                $table->string('manager_id_scan_path')->nullable()->after('manager_phone_number');
            }

            // Renommer les champs existants si nécessaire
            if (Schema::hasColumn('organizations', 'postal_code') && !Schema::hasColumn('organizations', 'zip_code')) {
                $table->renameColumn('postal_code', 'zip_code');
            }

            if (Schema::hasColumn('organizations', 'phone') && !Schema::hasColumn('organizations', 'phone_number')) {
                $table->renameColumn('phone', 'phone_number');
            }

            // Ajouter des index pour optimisation si pas déjà présents
            $table->index(['status'], 'idx_organizations_status');
            $table->index(['organization_type'], 'idx_organizations_type');
            $table->index(['city', 'wilaya'], 'idx_organizations_location');
            $table->index(['name'], 'idx_organizations_name');
        });

        echo "✅ Table organizations mise à jour avec la structure finale\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Supprimer les champs ajoutés
            $columns = [
                'manager_first_name',
                'manager_last_name',
                'manager_address',
                'manager_dob',
                'manager_pob',
                'manager_phone_number',
                'manager_id_scan_path'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('organizations', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Restaurer les noms de colonnes originaux
            if (Schema::hasColumn('organizations', 'zip_code')) {
                $table->renameColumn('zip_code', 'postal_code');
            }

            if (Schema::hasColumn('organizations', 'phone_number')) {
                $table->renameColumn('phone_number', 'phone');
            }

            // Supprimer les index
            $table->dropIndex('idx_organizations_status');
            $table->dropIndex('idx_organizations_type');
            $table->dropIndex('idx_organizations_location');
            $table->dropIndex('idx_organizations_name');
        });

        echo "🔄 Modifications de la table organizations annulées\n";
    }
};