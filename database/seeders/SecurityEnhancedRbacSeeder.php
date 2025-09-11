<?php

namespace Database\Seeders;

use Database\Seeders\EnterpriseRbacSeeder;

class SecurityEnhancedRbacSeeder extends EnterpriseRbacSeeder
{
    public function run(): void
    {
        $this->command->info('🛡️ INITIALISATION RBAC AVEC SÉCURITÉ RENFORCÉE');
        
        parent::run();
        
        // Ajouter les permissions de sécurité spécifiques
        $this->createSecurityPermissions();
        
        // Appliquer les restrictions de sécurité
        $this->applySecurityRestrictions();
    }
    
    private function createSecurityPermissions(): void
    {
        $this->command->info('🔐 Création des permissions de sécurité...');
        
        $securityPermissions = [
            'promote to super admin',   // Exclusif Super Admin
            'demote super admin',       // Exclusif Super Admin
            'manage standard roles',    // Admin peut gérer rôles non-critiques
            'assign standard roles',    // Admin peut assigner rôles <= Admin
            'view security logs',       // Monitoring des actions sensibles
            'manage security settings', // Configuration sécurité
        ];
        
        foreach ($securityPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
        
        // Assigner exclusivement au Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $superAdminRole->givePermissionTo($securityPermissions);
        
        // Retirer les permissions dangereuses de l'Admin
        $adminRole = Role::where('name', 'Admin')->first();
        $dangerousPermissions = [
            'manage roles',  // Trop large
            'create organizations',
            'manage system',
        ];
        
        foreach ($dangerousPermissions as $permission) {
            if ($adminRole->hasPermissionTo($permission)) {
                $adminRole->revokePermissionTo($permission);
            }
        }
        
        // Ajouter les permissions sécurisées à l'Admin
        $adminRole->givePermissionTo(['manage standard roles', 'assign standard roles']);
        
        $this->command->info('   ✅ Permissions de sécurité appliquées');
        $this->command->info('   🚫 Permissions dangereuses retirées de Admin');
    }
    
    private function applySecurityRestrictions(): void
    {
        $this->command->info('🔒 Application des restrictions de sécurité...');
        
        // S'assurer qu'il y a au moins un Super Admin
        $superAdminCount = User::role('Super Admin')->count();
        if ($superAdminCount === 0) {
            $this->command->error('⚠️ ALERTE: Aucun Super Admin trouvé!');
        }
        
        // Vérifier que l'Admin ne peut pas s'auto-promouvoir
        $adminUsers = User::role('Admin')->get();
        foreach ($adminUsers as $admin) {
            if ($admin->can('promote to super admin')) {
                $this->command->error("⚠️ FAILLE: {$admin->email} peut se promouvoir Super Admin!");
            }
        }
        
        $this->command->info('   ✅ Restrictions de sécurité vérifiées');
    }
}

