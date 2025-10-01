<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * 🏢 MIGRATION ENTERPRISE-GRADE: Rôles vers Multi-tenancy
 *
 * Ce seeder migre les rôles existants vers le système multi-tenant
 * en créant des rôles scopés par organisation
 */
class MigrateRolesToTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('🚀 Début de la migration vers multi-tenancy...');

        // ÉTAPE 1: Supprimer les anciens rôles (sauf Super Admin)
        $this->command->info('📋 Étape 1: Nettoyage des anciens rôles...');

        // Détacher tous les utilisateurs de leurs rôles actuels (sauf Super Admin)
        DB::table('model_has_roles')
            ->whereIn('role_id', function($query) {
                $query->select('id')
                    ->from('roles')
                    ->where('name', '!=', 'Super Admin');
            })
            ->delete();

        // Supprimer les anciens rôles (sauf Super Admin)
        Role::where('name', '!=', 'Super Admin')->delete();

        $this->command->info('✅ Anciens rôles nettoyés');

        // ÉTAPE 2: Créer les rôles pour chaque organisation
        $this->command->info('📋 Étape 2: Création des rôles par organisation...');

        $organizations = Organization::all();
        $roleNames = ['Admin', 'Gestionnaire Flotte', 'Supervisor', 'Chauffeur'];

        foreach ($organizations as $org) {
            $this->command->info("  → Création des rôles pour: {$org->name}");

            foreach ($roleNames as $roleName) {
                $role = Role::create([
                    'name' => $roleName,
                    'organization_id' => $org->id,
                    'guard_name' => 'web'
                ]);

                // Assigner les permissions selon le rôle
                $this->assignPermissionsToRole($role);
            }
        }

        $this->command->info('✅ Rôles créés pour toutes les organisations');

        // ÉTAPE 3: S'assurer que Super Admin est global
        $this->command->info('📋 Étape 3: Configuration du rôle Super Admin...');

        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'organization_id' => null // Super Admin est GLOBAL
        ]);

        // Super Admin a toutes les permissions
        $superAdminRole->syncPermissions(Permission::all());

        $this->command->info('✅ Super Admin configuré (global)');

        // ÉTAPE 4: Réassigner les utilisateurs à leurs rôles scopés
        $this->command->info('📋 Étape 4: Réassignation des utilisateurs...');

        $users = User::whereNotNull('organization_id')->get();

        foreach ($users as $user) {
            // Par défaut, assigner le rôle "Chauffeur" pour tous les utilisateurs
            $defaultRole = Role::where('name', 'Chauffeur')
                ->where('organization_id', $user->organization_id)
                ->first();

            if ($defaultRole) {
                $user->assignRole($defaultRole);
                $this->command->info("  → {$user->name}: Chauffeur (org #{$user->organization_id})");
            }
        }

        $this->command->info('✅ Utilisateurs réassignés');

        // ÉTAPE 5: Créer un utilisateur Admin par défaut pour chaque organisation
        $this->command->info('📋 Étape 5: Vérification des admins...');

        foreach ($organizations as $org) {
            // Vérifier si l'organisation a au moins un Admin
            $hasAdmin = User::where('users.organization_id', $org->id)
                ->whereHas('roles', function($query) use ($org) {
                    $query->where('roles.name', 'Admin')
                        ->where('roles.organization_id', $org->id);
                })
                ->exists();

            if (!$hasAdmin) {
                $this->command->warn("  ⚠️  L'organisation '{$org->name}' n'a pas d'Admin.");
                $this->command->info("     Vous devrez manuellement promouvoir un utilisateur.");
            }
        }

        $this->command->info('✅ Vérification terminée');

        $this->command->info('🎉 Migration vers multi-tenancy terminée avec succès!');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Vous devez maintenant assigner manuellement les rôles Admin');
        $this->command->warn('   aux utilisateurs appropriés pour chaque organisation.');
    }

    /**
     * Assigne les permissions à un rôle selon son nom
     */
    private function assignPermissionsToRole(Role $role): void
    {
        switch ($role->name) {
            case 'Admin':
                // Admin a tout sauf la gestion des organisations
                $permissions = Permission::where('name', 'not like', '%organizations%')->get();
                $role->syncPermissions($permissions);
                break;

            case 'Gestionnaire Flotte':
                $role->syncPermissions([
                    'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles',
                    'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers',
                    'view assignments', 'create assignments', 'edit assignments', 'end assignments',
                    'view maintenance', 'manage maintenance plans', 'log maintenance',
                    'create handovers', 'view handovers', 'edit handovers',
                    'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
                    'view documents', 'create documents', 'edit documents', 'delete documents',
                    'manage document_categories',
                ]);
                break;

            case 'Supervisor':
                $role->syncPermissions([
                    'view vehicles', 'view drivers', 'view assignments',
                    'view maintenance', 'view handovers', 'view documents',
                ]);
                break;

            case 'Chauffeur':
                $role->syncPermissions([
                    'view vehicles',
                    'view assignments',
                ]);
                break;
        }
    }
}
