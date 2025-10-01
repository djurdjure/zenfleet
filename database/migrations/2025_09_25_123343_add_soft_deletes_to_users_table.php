<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Enterprise-grade User Table Enhancement
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ajouter SoftDeletes pour traçabilité enterprise
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
                echo "✅ Colonne deleted_at ajoutée à la table users\n";
            }

            // Ajouter des colonnes manquantes si nécessaires
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
                echo "✅ Colonne first_name ajoutée\n";
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
                echo "✅ Colonne last_name ajoutée\n";
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
                echo "✅ Colonne phone ajoutée\n";
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('phone');
                echo "✅ Colonne role ajoutée\n";
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
                echo "✅ Colonne status ajoutée\n";
            }
        });

        // Ajouter les index pour performance enterprise (séparément pour éviter les erreurs)
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['organization_id', 'status', 'deleted_at'], 'idx_users_org_status_deleted');
                $table->index(['email', 'deleted_at'], 'idx_users_email_deleted');
            });
            echo "🚀 Index de performance enterprise ajoutés\n";
        } catch (Exception $e) {
            echo "⚠️ Index déjà existants ou erreur: " . $e->getMessage() . "\n";
        }

        echo "🚀 Migration users table completed - Enterprise ready\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les index (si ils existent)
            try {
                $table->dropIndex('idx_users_org_status_deleted');
                $table->dropIndex('idx_users_email_deleted');
            } catch (Exception $e) {
                // Index n'existe pas, continuer
            }

            // Supprimer les colonnes ajoutées
            $columnsToDrop = [];
            if (Schema::hasColumn('users', 'deleted_at')) $columnsToDrop[] = 'deleted_at';
            if (Schema::hasColumn('users', 'first_name')) $columnsToDrop[] = 'first_name';
            if (Schema::hasColumn('users', 'last_name')) $columnsToDrop[] = 'last_name';
            if (Schema::hasColumn('users', 'phone')) $columnsToDrop[] = 'phone';
            if (Schema::hasColumn('users', 'role')) $columnsToDrop[] = 'role';
            if (Schema::hasColumn('users', 'status')) $columnsToDrop[] = 'status';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
