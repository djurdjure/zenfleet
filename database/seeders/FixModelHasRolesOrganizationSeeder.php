<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * FixModelHasRolesOrganizationSeeder - Correction organization_id dans model_has_roles
 *
 * Ce seeder corrige le problème où organization_id dans model_has_roles
 * ne correspond pas à l'organization_id de l'utilisateur.
 *
 * Problème résolu:
 * - SQLSTATE[23505]: Unique violation duplicate key (role_id, model_id, model_type)
 * - organization_id incorrect causant des conflits lors de syncRoles()
 *
 * @version 1.0-Enterprise
 */
class FixModelHasRolesOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Correction organization_id dans model_has_roles...');
        $this->command->info('');

        // 1. Identifier les enregistrements problématiques
        $problematic = $this->findProblematicRecords();

        if (empty($problematic)) {
            $this->command->info('✅ Aucun enregistrement problématique trouvé');
            return;
        }

        $this->command->warn("⚠️  " . count($problematic) . " utilisateurs avec organization_id incorrect");
        $this->command->info('');

        // 2. Corriger chaque utilisateur
        $fixed = 0;
        $failed = 0;

        foreach ($problematic as $record) {
            try {
                $this->fixUserRoleAssignment($record);
                $fixed++;
                $this->command->info("  ✓ User ID {$record->user_id}: {$record->user_email}");
            } catch (\Exception $e) {
                $failed++;
                $this->command->error("  ✗ User ID {$record->user_id}: {$e->getMessage()}");
            }
        }

        // 3. Résumé
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info("✅ Corrigés: {$fixed}");

        if ($failed > 0) {
            $this->command->error("❌ Échoués: {$failed}");
        }

        $this->command->info('═══════════════════════════════════════════════');

        // 4. Invalider le cache des permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->command->info('🔄 Cache des permissions invalidé');
    }

    /**
     * Trouver les enregistrements avec organization_id incorrect
     */
    protected function findProblematicRecords()
    {
        return DB::select("
            SELECT
                users.id as user_id,
                users.email as user_email,
                users.organization_id as user_org,
                model_has_roles.organization_id as role_org,
                model_has_roles.role_id,
                roles.name as role_name
            FROM model_has_roles
            JOIN users ON model_has_roles.model_id = users.id
            JOIN roles ON model_has_roles.role_id = roles.id
            WHERE model_has_roles.model_type = 'App\\Models\\User'
              AND (
                  -- Cas 1: organization_id différent (sauf Super Admin qui doit être NULL)
                  (roles.name != 'Super Admin' AND model_has_roles.organization_id != users.organization_id)
                  OR
                  -- Cas 2: Super Admin avec organization_id non NULL
                  (roles.name = 'Super Admin' AND model_has_roles.organization_id IS NOT NULL)
              )
            ORDER BY users.id
        ");
    }

    /**
     * Corriger l'assignation de rôle pour un utilisateur
     */
    protected function fixUserRoleAssignment($record): void
    {
        $user = User::find($record->user_id);
        $role = Role::find($record->role_id);

        if (!$user || !$role) {
            throw new \Exception("User ou Role introuvable");
        }

        // Supprimer l'ancienne assignation (incorrecte)
        DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\Models\User')
            ->where('role_id', $role->id)
            ->delete();

        // Déterminer le bon organization_id
        $organizationId = ($role->name === 'Super Admin') ? null : $user->organization_id;

        // Créer la nouvelle assignation (correcte)
        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'organization_id' => $organizationId,
        ]);
    }
}
