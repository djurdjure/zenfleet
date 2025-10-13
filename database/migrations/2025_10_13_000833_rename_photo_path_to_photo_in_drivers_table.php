<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🔧 CORRECTION ENTERPRISE: Renommer photo_path en photo
     *
     * PROBLÈME IDENTIFIÉ:
     * - La colonne en BDD s'appelle "photo_path"
     * - Le code (modèle, service, formulaires) utilise "photo"
     * - Résultat: SQLSTATE[42703] Undefined column: column "photo" does not exist
     *
     * SOLUTION:
     * Renommer la colonne "photo_path" en "photo" pour harmoniser avec le code
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Vérifier si la colonne photo_path existe et si photo n'existe pas
            if (Schema::hasColumn('drivers', 'photo_path') && !Schema::hasColumn('drivers', 'photo')) {
                $table->renameColumn('photo_path', 'photo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'photo') && !Schema::hasColumn('drivers', 'photo_path')) {
                $table->renameColumn('photo', 'photo_path');
            }
        });
    }
};
