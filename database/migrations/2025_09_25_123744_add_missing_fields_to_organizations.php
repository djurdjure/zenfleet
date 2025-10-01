<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add missing fields to organizations
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'slug')) {
                $table->string('slug')->unique()->after('name');
                echo "✅ Colonne slug ajoutée\n";
            }

            if (!Schema::hasColumn('organizations', 'email')) {
                $table->string('email')->nullable()->after('slug');
                echo "✅ Colonne email ajoutée\n";
            }
        });

        // Générer des slugs pour les organisations existantes
        try {
            $organizations = \DB::table('organizations')->whereNull('slug')->get();
            foreach ($organizations as $org) {
                $slug = \Illuminate\Support\Str::slug($org->name);
                \DB::table('organizations')->where('id', $org->id)->update(['slug' => $slug]);
            }
            if (count($organizations) > 0) {
                echo "✅ Slugs générés pour " . count($organizations) . " organisations\n";
            }
        } catch (Exception $e) {
            echo "⚠️ Erreur lors de la génération des slugs: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('organizations', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
