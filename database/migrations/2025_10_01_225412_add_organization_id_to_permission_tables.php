<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 🏢 MIGRATION ENTERPRISE-GRADE: Multi-tenancy pour Permissions
     * Ajoute organization_id aux tables Spatie Permission pour scoping par organisation
     */
    public function up(): void
    {
        // 1. Table ROLES - Ajout organization_id
        if (!Schema::hasColumn('roles', 'organization_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('guard_name');
                $table->index('organization_id', 'roles_organization_id_index');

                // Clé étrangère pour intégrité référentielle
                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('cascade');
            });
            echo "✅ organization_id ajoutée à roles\n";
        } else {
            echo "⚠️  organization_id existe déjà dans roles\n";
        }

        // 2. Table MODEL_HAS_ROLES - Ajout organization_id
        if (!Schema::hasColumn('model_has_roles', 'organization_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('model_type');
                $table->index('organization_id', 'model_has_roles_organization_id_index');

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('cascade');
            });
            echo "✅ organization_id ajoutée à model_has_roles\n";
        } else {
            echo "⚠️  organization_id existe déjà dans model_has_roles\n";
        }

        // 3. Table MODEL_HAS_PERMISSIONS - Ajout organization_id
        if (!Schema::hasColumn('model_has_permissions', 'organization_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('model_type');
                $table->index('organization_id', 'model_has_permissions_organization_id_index');

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('cascade');
            });
            echo "✅ organization_id ajoutée à model_has_permissions\n";
        } else {
            echo "⚠️  organization_id existe déjà dans model_has_permissions\n";
        }

        // 4. Optionnel: Table PERMISSIONS - Si besoin de scoping des permissions par org
        // (Généralement les permissions restent globales, seuls les rôles sont scopés)
        if (!Schema::hasColumn('permissions', 'organization_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('organization_id')->nullable()->after('guard_name');
                $table->index('organization_id', 'permissions_organization_id_index');

                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('cascade');
            });
            echo "✅ organization_id ajoutée à permissions\n";
        } else {
            echo "⚠️  organization_id existe déjà dans permissions\n";
        }
    }

    /**
     * Rollback de la migration
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex('permissions_organization_id_index');
            $table->dropColumn('organization_id');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex('model_has_permissions_organization_id_index');
            $table->dropColumn('organization_id');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex('model_has_roles_organization_id_index');
            $table->dropColumn('organization_id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropIndex('roles_organization_id_index');
            $table->dropColumn('organization_id');
        });
    }
};
