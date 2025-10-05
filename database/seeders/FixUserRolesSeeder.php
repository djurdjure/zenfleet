<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * FixUserRolesSeeder - Correction des assignations de rôles
 *
 * Ce seeder garantit que:
 * 1. Super Admin a organization_id = NULL dans model_has_roles (global)
 * 2. Autres utilisateurs ont organization_id = leur user.organization_id
 * 3. Aucun doublon de rôles
 *
 * @version 1.0-Enterprise
 */
class FixUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Correction des assignations de rôles...');

        // 1. Nettoyer les doublons dans model_has_roles
        $this->removeDuplicateRoles();

        // 2. Fixer le Super Admin
        $this->fixSuperAdminRole();

        // 3. Fixer les autres utilisateurs
        $this->fixRegularUserRoles();

        $this->command->info('');
        $this->command->info('✅ Assignations de rôles corrigées avec succès!');
    }

    /**
     * Supprimer les doublons dans model_has_roles
     */
    protected function removeDuplicateRoles(): void
    {
        $this->command->info('  📋 Suppression des doublons...');

        // Récupérer tous les enregistrements avec leurs IDs
        $records = DB::table('model_has_roles')
            ->select('*')
            ->orderBy('role_id')
            ->orderBy('model_id')
            ->get();

        $seen = [];
        $duplicates = 0;

        foreach ($records as $record) {
            $key = "{$record->role_id}_{$record->model_id}_{$record->model_type}";

            if (isset($seen[$key])) {
                // C'est un doublon, le supprimer
                DB::table('model_has_roles')
                    ->where('role_id', $record->role_id)
                    ->where('model_id', $record->model_id)
                    ->where('model_type', $record->model_type)
                    ->where('organization_id', $record->organization_id)
                    ->delete();
                $duplicates++;
            } else {
                $seen[$key] = true;
            }
        }

        $this->command->info("     ✓ {$duplicates} doublons supprimés");
    }

    /**
     * Fixer le rôle Super Admin
     */
    protected function fixSuperAdminRole(): void
    {
        $this->command->info('  👑 Correction Super Admin...');

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if (!$superAdminRole) {
            $this->command->warn('     ⚠️  Rôle Super Admin introuvable');
            return;
        }

        // Trouver tous les utilisateurs qui devraient être Super Admin
        $superAdmins = User::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin');
        })->get();

        foreach ($superAdmins as $admin) {
            // Supprimer l'ancienne assignation
            DB::table('model_has_roles')
                ->where('model_id', $admin->id)
                ->where('model_type', 'App\\Models\\User')
                ->where('role_id', $superAdminRole->id)
                ->delete();

            // Réassigner avec organization_id = NULL (global)
            DB::table('model_has_roles')->insert([
                'role_id' => $superAdminRole->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $admin->id,
                'organization_id' => null, // Super Admin est global
            ]);

            $this->command->info("     ✓ {$admin->email} → Super Admin (global)");
        }
    }

    /**
     * Fixer les rôles des utilisateurs réguliers
     */
    protected function fixRegularUserRoles(): void
    {
        $this->command->info('  👥 Correction utilisateurs réguliers...');

        $fixed = 0;

        // Récupérer tous les enregistrements où organization_id est NULL mais pas Super Admin
        $records = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereNull('model_has_roles.organization_id')
            ->where('roles.name', '!=', 'Super Admin')
            ->select('model_has_roles.*', 'roles.name as role_name')
            ->get();

        foreach ($records as $record) {
            $user = User::find($record->model_id);

            if ($user && $user->organization_id) {
                // Mettre à jour avec l'organization_id de l'utilisateur
                DB::table('model_has_roles')
                    ->where('role_id', $record->role_id)
                    ->where('model_id', $record->model_id)
                    ->where('model_type', $record->model_type)
                    ->update(['organization_id' => $user->organization_id]);

                $fixed++;
            }
        }

        $this->command->info("     ✓ {$fixed} assignations corrigées");
    }
}
