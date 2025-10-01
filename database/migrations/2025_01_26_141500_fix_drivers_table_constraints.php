<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🔧 Correction des contraintes NOT NULL problématiques
     */
    public function up(): void
    {
        // Rendre nullable les colonnes qui posent problème
        DB::statement('ALTER TABLE drivers ALTER COLUMN driver_license_number DROP NOT NULL');
        DB::statement('ALTER TABLE drivers ALTER COLUMN status DROP NOT NULL');

        // Mettre à jour les valeurs existantes si nécessaire
        DB::statement("UPDATE drivers SET driver_license_number = NULL WHERE driver_license_number = ''");
        DB::statement("UPDATE drivers SET status = 'active' WHERE status IS NULL");
    }

    /**
     * 🔄 Rollback de la migration
     */
    public function down(): void
    {
        // Remettre les contraintes (attention: peut échouer si des NULL existent)
        DB::statement("UPDATE drivers SET driver_license_number = 'N/A' WHERE driver_license_number IS NULL");
        DB::statement("UPDATE drivers SET status = 'active' WHERE status IS NULL");

        DB::statement('ALTER TABLE drivers ALTER COLUMN driver_license_number SET NOT NULL');
        DB::statement('ALTER TABLE drivers ALTER COLUMN status SET NOT NULL');
    }
};