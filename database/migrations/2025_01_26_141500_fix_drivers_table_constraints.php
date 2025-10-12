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
        // Vérifier que les colonnes existent avant de les modifier
        $columns = DB::select("SELECT column_name, is_nullable FROM information_schema.columns WHERE table_name = 'drivers'");
        $columnNames = collect($columns)->pluck('column_name')->toArray();

        // Rendre nullable les colonnes qui existent et posent problème
        if (in_array('license_number', $columnNames)) {
            DB::statement('ALTER TABLE drivers ALTER COLUMN license_number DROP NOT NULL');
            DB::statement("UPDATE drivers SET license_number = NULL WHERE license_number = ''");
            echo "✅ Colonne license_number rendue nullable\n";
        }

        if (in_array('status_id', $columnNames)) {
            DB::statement('ALTER TABLE drivers ALTER COLUMN status_id DROP NOT NULL');
            echo "✅ Colonne status_id rendue nullable\n";
        }

        // Support pour anciennes colonnes si elles existent
        if (in_array('driver_license_number', $columnNames)) {
            DB::statement('ALTER TABLE drivers ALTER COLUMN driver_license_number DROP NOT NULL');
            DB::statement("UPDATE drivers SET driver_license_number = NULL WHERE driver_license_number = ''");
        }

        if (in_array('status', $columnNames)) {
            DB::statement('ALTER TABLE drivers ALTER COLUMN status DROP NOT NULL');
            DB::statement("UPDATE drivers SET status = 'active' WHERE status IS NULL");
        }
    }

    /**
     * 🔄 Rollback de la migration
     */
    public function down(): void
    {
        // Vérifier que les colonnes existent avant le rollback
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'drivers'");
        $columnNames = collect($columns)->pluck('column_name')->toArray();

        // Remettre les contraintes (attention: peut échouer si des NULL existent)
        if (in_array('license_number', $columnNames)) {
            DB::statement("UPDATE drivers SET license_number = 'N/A' WHERE license_number IS NULL");
            DB::statement('ALTER TABLE drivers ALTER COLUMN license_number SET NOT NULL');
        }

        if (in_array('status_id', $columnNames)) {
            DB::statement("UPDATE drivers SET status_id = (SELECT id FROM driver_statuses WHERE is_default = true LIMIT 1) WHERE status_id IS NULL");
            DB::statement('ALTER TABLE drivers ALTER COLUMN status_id SET NOT NULL');
        }

        // Support pour anciennes colonnes si elles existent
        if (in_array('driver_license_number', $columnNames)) {
            DB::statement("UPDATE drivers SET driver_license_number = 'N/A' WHERE driver_license_number IS NULL");
            DB::statement('ALTER TABLE drivers ALTER COLUMN driver_license_number SET NOT NULL');
        }

        if (in_array('status', $columnNames)) {
            DB::statement("UPDATE drivers SET status = 'active' WHERE status IS NULL");
            DB::statement('ALTER TABLE drivers ALTER COLUMN status SET NOT NULL');
        }
    }
};