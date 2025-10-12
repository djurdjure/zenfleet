<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne existe déjà avant de l'ajouter
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes(); // Ajoute la colonne 'deleted_at' nullable
            });
            echo "✅ Colonne deleted_at ajoutée à users\n";
        } else {
            echo "⚠️  Colonne deleted_at existe déjà dans users, skip\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Supprime la colonne 'deleted_at'
        });
    }
};
