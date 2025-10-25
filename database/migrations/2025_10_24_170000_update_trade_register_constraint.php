<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mise à jour de la contrainte trade_register pour accepter le format réel algérien:
     * XX/XX-XXAXXXXXXX ou XX/XX-XXBXXXXXXX
     * (10 caractères alphanumériques après le tiret)
     */
    public function up(): void
    {
        // Supprimer l'ancienne contrainte
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_trade_register
        ");

        // Ajouter la nouvelle contrainte avec le format correct
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_trade_register CHECK (
                trade_register IS NULL OR
                trade_register ~ '^[0-9]{2}/[0-9]{2}-[0-9]{2}[A-Z][0-9]{7}$'
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la nouvelle contrainte
        DB::statement("
            ALTER TABLE suppliers
            DROP CONSTRAINT IF EXISTS valid_trade_register
        ");

        // Rétablir l'ancienne contrainte
        DB::statement("
            ALTER TABLE suppliers
            ADD CONSTRAINT valid_trade_register CHECK (
                trade_register IS NULL OR
                trade_register ~ '^[0-9]{2}/[0-9]{2}-[0-9]{7}$'
            )
        ");
    }
};
