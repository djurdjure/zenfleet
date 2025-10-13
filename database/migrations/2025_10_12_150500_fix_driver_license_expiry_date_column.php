<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 🔧 MIGRATION: Correction du nom de colonne license_expiry_date
 *
 * PROBLÈME IDENTIFIÉ:
 * - Le modèle Driver.php utilise 'driver_license_expiry_date' dans $fillable
 * - La table PostgreSQL a 'license_expiry_date' (sans préfixe driver_)
 * - Résultat: SQLSTATE[42703] Undefined column lors de la création
 *
 * SOLUTION ENTERPRISE:
 * - On garde le nom actuel dans la DB: 'license_expiry_date'
 * - On corrige le modèle Driver.php pour utiliser le bon nom
 * - Pas besoin de renommer la colonne (évite downtime)
 *
 * @version 1.0-HOTFIX
 * @author ZenFleet DevOps Team
 * @date 2025-10-12
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cette migration est une DOCUMENTATION du problème résolu.
     * Aucune modification de schéma n'est nécessaire.
     */
    public function up(): void
    {
        // ✅ VÉRIFICATION: La colonne license_expiry_date existe déjà
        if (!Schema::hasColumn('drivers', 'license_expiry_date')) {
            // Si elle n'existe pas, on la crée (cas rare)
            Schema::table('drivers', function (Blueprint $table) {
                $table->date('license_expiry_date')->nullable()
                      ->comment('Date d\'expiration du permis de conduire (calculée auto depuis license_issue_date)')
                      ->after('license_authority');
            });
        }

        // ✅ VÉRIFICATION: S'assurer qu'il n'y a pas de colonne avec l'ancien nom
        if (Schema::hasColumn('drivers', 'driver_license_expiry_date')) {
            // Si l'ancienne colonne existe, la supprimer
            Schema::table('drivers', function (Blueprint $table) {
                $table->dropColumn('driver_license_expiry_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback nécessaire - on garde license_expiry_date
    }
};
