<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

/**
 * 👑 MASTER PERMISSIONS SEEDER - ENTERPRISE-GRADE
 * 
 * Configure TOUTES les permissions pour TOUS les rôles de manière définitive.
 * 
 * Architecture:
 * - Super Admin : TOUTES les permissions (pas de restriction)
 * - Admin : Gestion complète de son organisation
 * - Gestionnaire Flotte : Gestion opérationnelle
 * - Superviseur : Consultation et opérations basiques
 * - Mécanicien : Maintenance uniquement
 * - Comptable : Finance et rapports
 * - Chauffeur : Consultation limitée
 * 
 * Features Enterprise:
 * - ✅ Assignation exhaustive des permissions
 * - ✅ Validation post-configuration
 * - ✅ Logs détaillés
 * - ✅ Idempotent (réexécutable)
 * - ✅ Transaction avec rollback
 * - ✅ Cache auto-nettoyé
 * 
 * @version 2.0-Master-Enterprise
 * @author ZenFleet Development Team
 * @since 2025-01-20
 */
class MasterPermissionsSeeder extends Seeder
{
    /**
     * 🎯 DÉFINITION COMPLÈTE DES PERMISSIONS PAR RÔLE
     * 
     * Super Admin : '*' = TOUTES les permissions
     * Autres rôles : Liste explicite des permissions
     */
    private const ROLE_PERMISSIONS_MAP = [
        'Super Admin' => '*', // TOUTES LES PERMISSIONS
        
        'Admin' => [
            // Organisation Management (COMPLET)
            'view organizations',
            'create organizations',
            'edit organizations',
            'delete organizations',
            'restore organizations',
            'export organizations',
            'manage organization settings',
            'view organization statistics',
            
            // Users Management (COMPLET)
            'view users',
            'create users',
            'edit users',
            'delete users',
            'restore users',
            'export users',
            'manage user roles',
            'reset user passwords',
            
            // Roles Management
            'view roles',
            'manage roles',
            
            // Vehicles (COMPLET)
            'view vehicles',
            'create vehicles',
            'update vehicles',
            'delete vehicles',
            'restore vehicles',
            'export vehicles',
            'import vehicles',
            'view vehicle history',
            'manage vehicle maintenance',
            'manage vehicle documents',
            'assign vehicles',
            'edit vehicles',
            'force-delete vehicles',
            
            // Drivers (COMPLET)
            'view drivers',
            'create drivers',
            'edit drivers',
            'delete drivers',
            'restore drivers',
            'export drivers',
            'import drivers',
            'view driver history',
            'assign drivers to vehicles',
            'manage driver licenses',
            
            // Assignments (COMPLET)
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'end assignments',
            'extend assignments',
            'export assignments',
            'view assignment calendar',
            'view assignment gantt',
            
            // Maintenance (COMPLET)
            'view maintenance',
            'manage maintenance plans',
            'create maintenance operations',
            'edit maintenance operations',
            'delete maintenance operations',
            'approve maintenance operations',
            'export maintenance reports',
            
            // Repair Requests (COMPLET)
            'view all repair requests',
            'create repair requests',
            'update own repair requests',
            'delete repair requests',
            'approve repair requests level 1',
            'approve repair requests level 2',
            'reject repair requests',
            'export repair requests',
            
            // Mileage (COMPLET)
            'view all mileage readings',
            'create mileage readings',
            'edit mileage readings',
            'delete mileage readings',
            'export mileage readings',
            
            // Suppliers (COMPLET)
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'restore suppliers',
            'export suppliers',
            'manage supplier contracts',
            
            // Expenses (COMPLET)
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',
            'export expenses',
            'view expense analytics',
            
            // Documents (COMPLET)
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'download documents',
            'approve documents',
            'export documents',
            
            // Analytics & Reports (COMPLET)
            'view analytics',
            'view performance metrics',
            'view roi metrics',
            'export analytics',
            
            // Alerts
            'view alerts',
            'create alerts',
            'edit alerts',
            'delete alerts',
            'mark alerts as read',
            'export alerts',
            
            // Audit
            'view audit logs',
            'export audit logs',
            'view security audit',
            'view user audit',
            'view organization audit',
            
            // Driver Sanctions (COMPLET)
            'view all driver sanctions',
            'create driver sanctions',
            'update any driver sanctions',
            'delete driver sanctions',
            'force delete driver sanctions',
            'restore driver sanctions',
            'archive driver sanctions',
            'unarchive driver sanctions',
            'export driver sanctions',
            'view driver sanction statistics',
            'view driver sanction history',
        ],
        
        'Gestionnaire Flotte' => [
            // Vehicles
            'view vehicles',
            'create vehicles',
            'update vehicles',
            'delete vehicles',
            'restore vehicles',
            'export vehicles',
            'import vehicles',
            'view vehicle history',
            'manage vehicle maintenance',
            'manage vehicle documents',
            'assign vehicles',
            'edit vehicles',
            
            // Drivers
            'view drivers',
            'create drivers',
            'edit drivers',
            'delete drivers',
            'restore drivers',
            'export drivers',
            'import drivers',
            'view driver history',
            'assign drivers to vehicles',
            'manage driver licenses',
            
            // Assignments
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'end assignments',
            'extend assignments',
            'export assignments',
            'view assignment calendar',
            'view assignment gantt',
            
            // Maintenance
            'view maintenance',
            'manage maintenance plans',
            'create maintenance operations',
            'edit maintenance operations',
            'export maintenance reports',
            
            // Repair Requests
            'view all repair requests',
            'create repair requests',
            'approve repair requests level 1',
            'export repair requests',
            
            // Mileage
            'view all mileage readings',
            'create mileage readings',
            'edit mileage readings',
            'export mileage readings',
            
            // Suppliers
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'export suppliers',
            
            // Documents
            'view documents',
            'create documents',
            'download documents',
            
            // Analytics
            'view analytics',
            'view performance metrics',
            'export analytics',
            
            // Alerts
            'view alerts',
            'mark alerts as read',
            
            // Driver Sanctions
            'view team driver sanctions',
            'create driver sanctions',
            'update own driver sanctions',
            'export driver sanctions',
        ],
        
        'Superviseur' => [
            // Vehicles
            'view vehicles',
            'view vehicle history',
            
            // Drivers
            'view drivers',
            'view driver history',
            
            // Assignments
            'view assignments',
            'create assignments',
            'end assignments',
            'view assignment calendar',
            
            // Maintenance
            'view maintenance',
            'create maintenance operations',
            
            // Repair Requests
            'view team repair requests',
            'create repair requests',
            
            // Mileage
            'view team mileage readings',
            'create mileage readings',
            
            // Documents
            'view documents',
            'download documents',
            
            // Alerts
            'view alerts',
            'mark alerts as read',
            
            // Driver Sanctions
            'view team driver sanctions',
            'create driver sanctions',
        ],
        
        'Mécanicien' => [
            // Vehicles
            'view vehicles',
            'view vehicle history',
            'manage vehicle maintenance',
            
            // Maintenance
            'view maintenance',
            'create maintenance operations',
            'edit maintenance operations',
            'export maintenance reports',
            
            // Repair Requests
            'view all repair requests',
            'create repair requests',
            'update own repair requests',
            
            // Mileage
            'view all mileage readings',
            'create mileage readings',
            
            // Documents
            'view documents',
            'create documents',
            'download documents',
        ],
        
        'Comptable' => [
            // Vehicles (lecture)
            'view vehicles',
            'export vehicles',
            
            // Drivers (lecture)
            'view drivers',
            'export drivers',
            
            // Assignments (lecture)
            'view assignments',
            'export assignments',
            
            // Expenses (COMPLET)
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'approve expenses',
            'export expenses',
            'view expense analytics',
            
            // Suppliers
            'view suppliers',
            'export suppliers',
            'manage supplier contracts',
            
            // Documents
            'view documents',
            'download documents',
            'export documents',
            
            // Analytics
            'view analytics',
            'view roi metrics',
            'export analytics',
            
            // Audit
            'view audit logs',
            'export audit logs',
        ],
        
        'Chauffeur' => [
            // Vehicles (limité)
            'view vehicles',
            
            // Drivers (son profil)
            'view drivers',
            'edit drivers', // Son propre profil
            
            // Assignments (ses affectations)
            'view assignments',
            
            // Repair Requests
            'view own repair requests',
            'create repair requests',
            'update own repair requests',
            
            // Mileage
            'view own mileage readings',
            'create mileage readings',
            
            // Documents
            'view documents',
            'download documents',
            
            // Alerts
            'view alerts',
            'mark alerts as read',
            
            // Driver Sanctions
            'view own driver sanctions',
        ],
    ];

    /**
     * 🚀 Exécution du seeder master
     */
    public function run(): void
    {
        $this->command->info('👑 MASTER PERMISSIONS SEEDER - CONFIGURATION COMPLÈTE');
        $this->command->info(str_repeat('=', 70));
        $this->command->newLine();
        
        DB::beginTransaction();
        
        try {
            // Étape 1 : Récupérer toutes les permissions existantes
            $allPermissions = $this->getAllPermissions();
            $this->command->info("📋 Permissions totales dans le système: {$allPermissions->count()}");
            $this->command->newLine();
            
            // Étape 2 : Configurer chaque rôle
            $this->configureAllRoles($allPermissions);
            
            // Étape 3 : Assigner les rôles aux utilisateurs clés
            $this->assignRolesToKeyUsers();
            
            // Étape 4 : Validation finale
            $this->validateConfiguration();
            
            DB::commit();
            
            $this->command->info(str_repeat('=', 70));
            $this->command->info('✅ CONFIGURATION TERMINÉE AVEC SUCCÈS');
            $this->command->newLine();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->command->error('❌ ERREUR LORS DE LA CONFIGURATION');
            $this->command->error($e->getMessage());
            
            Log::error('MasterPermissionsSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * 📋 Récupère toutes les permissions du système
     */
    private function getAllPermissions(): \Illuminate\Support\Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * 🏷️ Configure tous les rôles avec leurs permissions
     */
    private function configureAllRoles($allPermissions): void
    {
        $this->command->info('🏷️  CONFIGURATION DES RÔLES:');
        $this->command->newLine();
        
        foreach (self::ROLE_PERMISSIONS_MAP as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                $this->command->warn("   ⚠️  Rôle '{$roleName}' introuvable, ignoré");
                continue;
            }
            
            if ($permissions === '*') {
                // Super Admin : TOUTES les permissions
                $this->assignAllPermissions($role, $allPermissions);
            } else {
                // Autres rôles : Permissions spécifiques
                $this->assignSpecificPermissions($role, $permissions);
            }
        }
    }

    /**
     * 👑 Assigne TOUTES les permissions au Super Admin
     */
    private function assignAllPermissions(Role $role, $allPermissions): void
    {
        $this->command->info("   👑 {$role->name}:");
        $this->command->line("      Mode: TOUTES LES PERMISSIONS");
        
        // Utiliser syncPermissions pour remplacer toutes les permissions existantes
        $role->syncPermissions($allPermissions->pluck('name')->toArray());
        
        $count = $allPermissions->count();
        $this->command->info("      ✅ {$count} permissions assignées (COMPLET)");
        $this->command->newLine();
    }

    /**
     * 🔗 Assigne des permissions spécifiques à un rôle
     */
    private function assignSpecificPermissions(Role $role, array $permissionNames): void
    {
        $this->command->info("   🏷️  {$role->name}:");
        
        // Filtrer les permissions qui existent réellement
        $existingPermissions = Permission::whereIn('name', $permissionNames)->pluck('name')->toArray();
        
        $missing = array_diff($permissionNames, $existingPermissions);
        if (!empty($missing)) {
            $this->command->warn("      ⚠️  " . count($missing) . " permissions introuvables (ignorées)");
        }
        
        // Sync permissions (remplace les existantes)
        $role->syncPermissions($existingPermissions);
        
        $this->command->info("      ✅ " . count($existingPermissions) . " permissions assignées");
        $this->command->newLine();
    }

    /**
     * 👤 Assigne les rôles aux utilisateurs clés
     */
    private function assignRolesToKeyUsers(): void
    {
        $this->command->info('👤 ASSIGNATION RÔLES AUX UTILISATEURS:');
        $this->command->newLine();
        
        $keyUsers = [
            'superadmin@zenfleet.dz' => 'Super Admin',
            'admin@zenfleet.dz' => 'Admin',
        ];
        
        foreach ($keyUsers as $email => $roleName) {
            $user = User::where('email', $email)->first();
            $role = Role::where('name', $roleName)->first();
            
            if (!$user || !$role) {
                $this->command->warn("   ⚠️  Utilisateur '{$email}' ou rôle '{$roleName}' introuvable");
                continue;
            }
            
            // Nettoyer les rôles existants
            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();
            
            // Assigner le nouveau rôle avec organization_id
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
                'organization_id' => $user->organization_id,
            ]);
            
            $this->command->info("   ✅ {$email} → {$roleName}");
        }
        
        $this->command->newLine();
    }

    /**
     * ✅ Validation de la configuration
     */
    private function validateConfiguration(): void
    {
        $this->command->info('✅ VALIDATION:');
        $this->command->newLine();
        
        // Vérifier Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $allPermissions = Permission::count();
        
        if ($superAdmin) {
            $superAdminPermissions = $superAdmin->permissions()->count();
            if ($superAdminPermissions === $allPermissions) {
                $this->command->info("   ✅ Super Admin: {$superAdminPermissions}/{$allPermissions} permissions");
            } else {
                $this->command->warn("   ⚠️  Super Admin: {$superAdminPermissions}/{$allPermissions} permissions (incomplet)");
            }
        }
        
        // Vérifier Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = $admin->permissions()->count();
            $this->command->info("   ✅ Admin: {$adminPermissions} permissions");
        }
        
        $this->command->newLine();
        $this->command->info('   🔄 Nettoyage du cache des permissions...');
        Artisan::call('permission:cache-reset');
        $this->command->info('   ✅ Cache nettoyé');
    }
}
