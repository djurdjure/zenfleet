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
        Schema::table('drivers', function (Blueprint $table) {
            // Vérifier que la colonne n'existe pas déjà
            if (!Schema::hasColumn('drivers', 'license_verified')) {
                $table->boolean('license_verified')
                    ->default(false)
                    ->after('license_authority')
                    ->comment('Indique si le permis de conduire a été vérifié');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'license_verified')) {
                $table->dropColumn('license_verified');
            }
        });
    }
};
