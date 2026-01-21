<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * 🧪 Test Compatibility Permissions Seeder
 * 
 * Ce seeder ajoute les permissions dans les deux formats (espaces et dot notation)
 * pour garantir la compatibilité des tests avec les différentes conventions de nommage.
 * 
 * IMPORTANT: Ce seeder doit être exécuté APRÈS ZenFleetRolesPermissionsSeeder
 */
class TestCompatibilityPermissionsSeeder extends Seeder
{
    /**
     * Permissions au format "espaces" utilisées par les tests legacy
     */
    private array $legacyPermissions = [
        // Assignments (format legacy avec espaces)
        'view assignments',
        'create assignments',
        'edit assignments',
        'delete assignments',
        'end assignments',
        
        // Vehicles (format legacy avec espaces)
        'view vehicles',
        'create vehicles',
        'edit vehicles',
        'delete vehicles',
        'restore vehicles',
        'force delete vehicles',
        
        // Drivers (format legacy avec espaces)
        'view drivers',
        'create drivers',
        'edit drivers',
        'delete drivers',
        'restore drivers',
        'force delete drivers',
        
        // Users (format legacy avec espaces)
        'view users',
        'create users',
        'edit users',
        'delete users',
        
        // Roles (format legacy)
        'manage roles',
        
        // Maintenance (format legacy)
        'view maintenance',
        'manage maintenance plans',
        'log maintenance',
        
        // Handovers (format legacy)
        'view handovers',
        'create handovers',
        'edit handovers',
        'delete handovers',
        'upload signed handovers',
        
        // Suppliers (format legacy)
        'view suppliers',
        'create suppliers',
        'edit suppliers',
        'delete suppliers',
        
        // Documents (format legacy)
        'view documents',
        'create documents',
        'edit documents',
        'delete documents',
        'manage document_categories',
        
        // Depots (format legacy)
        'view depots',
        'create depots',
        'edit depots',
        'delete depots',
        'restore depots',
        
        // Organizations (format legacy)
        'view organizations',
        'create organizations',
        'edit organizations',
        'delete organizations',
    ];

    public function run(): void
    {
        $this->command->info('🧪 Création des permissions de compatibilité pour les tests...');
        
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        $created = 0;
        foreach ($this->legacyPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
            $created++;
        }
        
        $this->command->info("✅ {$created} permissions de compatibilité créées/vérifiées");
        
        // Assigner les permissions legacy aux rôles existants
        $this->assignLegacyPermissionsToRoles();
        
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
    
    private function assignLegacyPermissionsToRoles(): void
    {
        // Super Admin reçoit toutes les permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
            $this->command->line('  ↳ Super Admin: toutes les permissions legacy attribuées');
        }
        
        // Admin reçoit presque toutes les permissions (sauf organizations management)
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = array_filter($this->legacyPermissions, function ($p) {
                return !str_contains($p, 'organizations') || $p === 'view organizations';
            });
            foreach ($adminPermissions as $permission) {
                try {
                    $admin->givePermissionTo($permission);
                } catch (\Exception $e) {
                    // Ignore si déjà attribué
                }
            }
            $this->command->line('  ↳ Admin: permissions legacy attribuées');
        }
        
        // Gestionnaire Flotte
        $fleetManager = Role::where('name', 'Gestionnaire Flotte')->first();
        if ($fleetManager) {
            $fleetPermissions = [
                'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles',
                'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers',
                'view assignments', 'create assignments', 'edit assignments', 'end assignments',
                'view maintenance', 'manage maintenance plans', 'log maintenance',
                'view handovers', 'create handovers', 'edit handovers',
                'view documents', 'create documents', 'edit documents', 'delete documents',
                'manage document_categories',
                'view depots', 'create depots', 'edit depots',
            ];
            foreach ($fleetPermissions as $permission) {
                try {
                    $fleetManager->givePermissionTo($permission);
                } catch (\Exception $e) {
                    // Ignore
                }
            }
            $this->command->line('  ↳ Gestionnaire Flotte: permissions legacy attribuées');
        }
        
        // Chauffeur
        $driver = Role::where('name', 'Chauffeur')->first();
        if ($driver) {
            $driverPermissions = [
                'view vehicles',
                'view assignments',
            ];
            foreach ($driverPermissions as $permission) {
                try {
                    $driver->givePermissionTo($permission);
                } catch (\Exception $e) {
                    // Ignore
                }
            }
            $this->command->line('  ↳ Chauffeur: permissions legacy attribuées');
        }
        
        $this->command->info('🔐 Permissions legacy attribuées aux rôles');
    }
}
