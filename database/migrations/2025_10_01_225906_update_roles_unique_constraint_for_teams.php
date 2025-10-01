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
        // Supprimer l'ancienne contrainte unique (name, guard_name)
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_unique');
        });

        // Ajouter la nouvelle contrainte unique (name, guard_name, organization_id)
        DB::statement('
            CREATE UNIQUE INDEX roles_name_guard_organization_unique
            ON roles (name, guard_name, organization_id)
        ');

        // Pour les rôles globaux (Super Admin), organization_id est NULL
        // On doit aussi créer un index partiel pour ces cas
        DB::statement('
            CREATE UNIQUE INDEX roles_name_guard_null_organization_unique
            ON roles (name, guard_name)
            WHERE organization_id IS NULL
        ');
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
