<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * 🏢 MIGRATION: Mise à jour contrainte unique pour multi-tenancy
     *
     * Modifie la contrainte unique sur roles pour permettre
     * le même nom de rôle dans différentes organisations
     */
    public function up(): void
    {
        // Vérifier et supprimer l'ancienne contrainte unique si elle existe
        $constraintExists = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'roles'
            AND constraint_name = 'roles_name_guard_name_unique'
        ");

        if (!empty($constraintExists)) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_guard_name_unique');
            });
            echo "✅ Ancienne contrainte roles_name_guard_name_unique supprimée\n";
        } else {
            echo "⚠️  Contrainte roles_name_guard_name_unique n'existe pas, skip\n";
        }

        // Ajouter la nouvelle contrainte unique (name, guard_name, organization_id) si elle n'existe pas
        $indexExists = DB::select("
            SELECT indexname
            FROM pg_indexes
            WHERE tablename = 'roles'
            AND indexname = 'roles_name_guard_organization_unique'
        ");

        if (empty($indexExists)) {
            DB::statement('
                CREATE UNIQUE INDEX roles_name_guard_organization_unique
                ON roles (name, guard_name, organization_id)
            ');
            echo "✅ Index roles_name_guard_organization_unique créé\n";
        } else {
            echo "⚠️  Index roles_name_guard_organization_unique existe déjà\n";
        }

        // Pour les rôles globaux (Super Admin), organization_id est NULL
        $partialIndexExists = DB::select("
            SELECT indexname
            FROM pg_indexes
            WHERE tablename = 'roles'
            AND indexname = 'roles_name_guard_null_organization_unique'
        ");

        if (empty($partialIndexExists)) {
            DB::statement('
                CREATE UNIQUE INDEX roles_name_guard_null_organization_unique
                ON roles (name, guard_name)
                WHERE organization_id IS NULL
            ');
            echo "✅ Index roles_name_guard_null_organization_unique créé\n";
        } else {
            echo "⚠️  Index roles_name_guard_null_organization_unique existe déjà\n";
        }
    }

    /**
     * Rollback de la migration
     */
    public function down(): void
    {
        // Supprimer les nouvelles contraintes
        DB::statement('DROP INDEX IF EXISTS roles_name_guard_organization_unique');
        DB::statement('DROP INDEX IF EXISTS roles_name_guard_null_organization_unique');

        // Restaurer l'ancienne contrainte
        Schema::table('roles', function (Blueprint $table) {
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};
