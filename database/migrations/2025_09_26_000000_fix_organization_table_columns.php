<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Fix Organization table columns and ensure data consistency
     */
    public function up(): void
    {
        echo "🔧 Vérification et correction des colonnes pour OrganizationTable...\n";

        // 1. S'assurer que la colonne status existe dans users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
                echo "   ✅ Colonne status ajoutée à users\n";
            }
        });

        // 2. S'assurer que la colonne status existe dans organizations
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'status')) {
                $table->string('status')->default('active')->after('description');
                echo "   ✅ Colonne status ajoutée à organizations\n";
            }
        });

        // 3. Mettre à jour les utilisateurs sans status défini
        try {
            $usersUpdated = \DB::table('users')
                ->whereNull('status')
                ->orWhere('status', '')
                ->update(['status' => 'active']);

            if ($usersUpdated > 0) {
                echo "   ✅ {$usersUpdated} utilisateurs mis à jour avec status=active\n";
            }
        } catch (Exception $e) {
            echo "   ⚠️ Erreur lors de la mise à jour des utilisateurs: " . $e->getMessage() . "\n";
        }

        // 4. Mettre à jour les organisations sans status défini
        try {
            $orgsUpdated = \DB::table('organizations')
                ->whereNull('status')
                ->orWhere('status', '')
                ->update(['status' => 'active']);

            if ($orgsUpdated > 0) {
                echo "   ✅ {$orgsUpdated} organisations mises à jour avec status=active\n";
            }
        } catch (Exception $e) {
            echo "   ⚠️ Erreur lors de la mise à jour des organisations: " . $e->getMessage() . "\n";
        }

        // 5. S'assurer que les colonnes slug existent si nécessaire
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('name');
                echo "   ✅ Colonne slug ajoutée à organizations\n";
            }
        });

        // 6. Générer les slugs pour les organisations sans slug
        try {
            $organizations = \DB::table('organizations')->whereNull('slug')->get();
            foreach ($organizations as $org) {
                $slug = \Illuminate\Support\Str::slug($org->name);
                $counter = 1;
                $originalSlug = $slug;

                // Vérifier l'unicité du slug
                while (\DB::table('organizations')->where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                \DB::table('organizations')
                    ->where('id', $org->id)
                    ->update(['slug' => $slug]);
            }

            if (count($organizations) > 0) {
                echo "   ✅ Slugs générés pour " . count($organizations) . " organisations\n";
            }
        } catch (Exception $e) {
            echo "   ⚠️ Erreur lors de la génération des slugs: " . $e->getMessage() . "\n";
        }

        // 7. Vérification de la structure
        echo "\n📊 Vérification de la structure finale:\n";

        $userColumns = Schema::getColumnListing('users');
        $orgColumns = Schema::getColumnListing('organizations');

        echo "   - Users.status: " . (in_array('status', $userColumns) ? "✅ Existe" : "❌ Manquant") . "\n";
        echo "   - Organizations.status: " . (in_array('status', $orgColumns) ? "✅ Existe" : "❌ Manquant") . "\n";
        echo "   - Organizations.slug: " . (in_array('slug', $orgColumns) ? "✅ Existe" : "❌ Manquant") . "\n";

        // 8. Statistiques finales
        $activeUsers = \DB::table('users')->where('status', 'active')->whereNull('deleted_at')->count();
        $activeOrgs = \DB::table('organizations')->where('status', 'active')->whereNull('deleted_at')->count();
        $totalVehicles = \DB::table('vehicles')->whereNull('deleted_at')->count();
        $totalDrivers = \DB::table('drivers')->whereNull('deleted_at')->count();

        echo "\n📈 Statistiques système:\n";
        echo "   - Utilisateurs actifs: {$activeUsers}\n";
        echo "   - Organisations actives: {$activeOrgs}\n";
        echo "   - Véhicules: {$totalVehicles}\n";
        echo "   - Chauffeurs: {$totalDrivers}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Ne pas supprimer les colonnes status car elles sont critiques
        // Juste nettoyer les slugs si nécessaire
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};