<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Renommer l'ancienne colonne si elle existe
            if (Schema::hasColumn('drivers', 'license_category')) {
                $table->renameColumn('license_category', 'license_category_old');
            }
            
            // Ajouter la nouvelle colonne pour stocker plusieurs catégories
            $table->json('license_categories')
                ->nullable()
                ->after('license_number')
                ->comment('Catégories de permis (JSON array: ["B", "C", "D", etc.])');
        });
        
        // Migrer les données existantes
        $drivers = DB::table('drivers')->whereNotNull('license_category_old')->get();
        foreach ($drivers as $driver) {
            DB::table('drivers')
                ->where('id', $driver->id)
                ->update([
                    'license_categories' => json_encode([$driver->license_category_old])
                ]);
        }
        
        // Supprimer l'ancienne colonne
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'license_category_old')) {
                $table->dropColumn('license_category_old');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Recréer l'ancienne colonne
            $table->string('license_category', 10)->nullable()->after('license_number');
            
            // Supprimer la nouvelle colonne
            if (Schema::hasColumn('drivers', 'license_categories')) {
                $table->dropColumn('license_categories');
            }
        });
    }
};
