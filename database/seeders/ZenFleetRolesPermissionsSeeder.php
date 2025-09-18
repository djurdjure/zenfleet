<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ZenFleetRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $this->createPermissions();

        // Créer les rôles et assigner les permissions
        $this->createRoles();

        $this->command->info('🔐 Rôles et permissions ZenFleet créés avec succès!');
    }

    private function createPermissions(): void
    {
        $permissions = [
            // 🏢 Gestion des organisations
            'organizations.view',
            'organizations.create',
            'organizations.edit',
            'organizations.delete',
            'organizations.manage_settings',
            'organizations.manage_subscription',
            'organizations.view_statistics',

            // 👥 Gestion des utilisateurs
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage_roles',
            'users.reset_password',
            'users.impersonate',
            'users.export',

            // 🚗 Gestion des véhicules
            'vehicles.view',
            'vehicles.create',
            'vehicles.edit',
            'vehicles.delete',
            'vehicles.assign_driver',
            'vehicles.manage_maintenance',
            'vehicles.view_location',
            'vehicles.export',

            // 🚚 Gestion des chauffeurs
            'drivers.view',
            'drivers.create',
            'drivers.edit',
            'drivers.delete',
            'drivers.assign_vehicle',
            'drivers.manage_documents',
            'drivers.view_performance',
            'drivers.export',

            // 📊 Rapports et analytiques
            'reports.view',
            'reports.create',
            'reports.export',
            'reports.financial',
            'reports.operational',
            'reports.compliance',

            // 🔧 Maintenance
            'maintenance.view',
            'maintenance.create',
            'maintenance.edit',
            'maintenance.delete',
            'maintenance.schedule',
            'maintenance.approve',
            'maintenance.reports',

            // ⛽ Carburant
            'fuel.view',
            'fuel.create',
            'fuel.edit',
            'fuel.delete',
            'fuel.reports',
            'fuel.manage_cards',

            // 🚚 Trajets et missions
            'trips.view',
            'trips.create',
            'trips.edit',
            'trips.delete',
            'trips.assign',
            'trips.track',
            'trips.reports',

            // 🏪 Fournisseurs
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',
            'suppliers.manage_contracts',

            // 📄 Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',
            'documents.approve',
            'documents.download',

            // ⚙️ Paramètres système
            'settings.view',
            'settings.edit',
            'settings.system',
            'settings.notifications',
            'settings.integrations',

            // 🔍 Audit et logs
            'audit.view',
            'audit.export',
            'audit.delete',

            // 💰 Facturation
            'billing.view',
            'billing.create',
            'billing.edit',
            'billing.export',

            // 🌐 API et intégrations
            'api.access',
            'api.manage_keys',
            'integrations.view',
            'integrations.manage',

            // 🚨 Alertes et notifications
            'alerts.view',
            'alerts.create',
            'alerts.edit',
            'alerts.delete',
            'notifications.send',

            // 🗺️ Géolocalisation
            'tracking.view',
            'tracking.real_time',
            'tracking.history',
            'tracking.geofences',

            // ⚠️ Permissions super admin
            'system.access',
            'system.manage_organizations',
            'system.manage_global_settings',
            'system.view_system_logs',
            'system.backup_restore',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $this->command->info('📋 ' . count($permissions) . ' permissions créées');
    }

    private function createRoles(): void
    {
        // 🦸‍♂️ SUPER ADMIN - Accès total au système
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(Permission::all()); // Toutes les permissions

        // 👑 ADMIN ORGANISATION - Gestionnaire principal d'une organisation
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);
        $adminPermissions = [
            // Organisation
            'organizations.view', 'organizations.edit', 'organizations.manage_settings',
            'organizations.view_statistics',

            // Utilisateurs
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'users.manage_roles', 'users.reset_password', 'users.export',

            // Véhicules
            'vehicles.view', 'vehicles.create', 'vehicles.edit', 'vehicles.delete',
            'vehicles.assign_driver', 'vehicles.manage_maintenance', 'vehicles.view_location',
            'vehicles.export',

            // Chauffeurs
            'drivers.view', 'drivers.create', 'drivers.edit', 'drivers.delete',
            'drivers.assign_vehicle', 'drivers.manage_documents', 'drivers.view_performance',
            'drivers.export',

            // Rapports
            'reports.view', 'reports.create', 'reports.export', 'reports.financial',
            'reports.operational', 'reports.compliance',

            // Maintenance
            'maintenance.view', 'maintenance.create', 'maintenance.edit', 'maintenance.delete',
            'maintenance.schedule', 'maintenance.approve', 'maintenance.reports',

            // Carburant
            'fuel.view', 'fuel.create', 'fuel.edit', 'fuel.delete',
            'fuel.reports', 'fuel.manage_cards',

            // Trajets
            'trips.view', 'trips.create', 'trips.edit', 'trips.delete',
            'trips.assign', 'trips.track', 'trips.reports',

            // Fournisseurs
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete',
            'suppliers.manage_contracts',

            // Documents
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            'documents.approve', 'documents.download',

            // Paramètres
            'settings.view', 'settings.edit', 'settings.notifications', 'settings.integrations',

            // Audit
            'audit.view', 'audit.export',

            // Facturation
            'billing.view', 'billing.create', 'billing.edit', 'billing.export',

            // Alertes
            'alerts.view', 'alerts.create', 'alerts.edit', 'alerts.delete',
            'notifications.send',

            // Géolocalisation
            'tracking.view', 'tracking.real_time', 'tracking.history', 'tracking.geofences',
        ];
        $admin->givePermissionTo($adminPermissions);

        // 🚛 GESTIONNAIRE FLOTTE - Gestion opérationnelle de la flotte
        $fleetManager = Role::firstOrCreate([
            'name' => 'Gestionnaire Flotte',
            'guard_name' => 'web'
        ]);
        $fleetManagerPermissions = [
            // Véhicules
            'vehicles.view', 'vehicles.create', 'vehicles.edit',
            'vehicles.assign_driver', 'vehicles.manage_maintenance', 'vehicles.view_location',
            'vehicles.export',

            // Chauffeurs
            'drivers.view', 'drivers.create', 'drivers.edit',
            'drivers.assign_vehicle', 'drivers.manage_documents', 'drivers.view_performance',
            'drivers.export',

            // Maintenance
            'maintenance.view', 'maintenance.create', 'maintenance.edit',
            'maintenance.schedule', 'maintenance.reports',

            // Carburant
            'fuel.view', 'fuel.create', 'fuel.edit', 'fuel.reports',

            // Trajets
            'trips.view', 'trips.create', 'trips.edit', 'trips.assign',
            'trips.track', 'trips.reports',

            // Fournisseurs
            'suppliers.view', 'suppliers.create', 'suppliers.edit',

            // Documents
            'documents.view', 'documents.create', 'documents.edit', 'documents.download',

            // Rapports
            'reports.view', 'reports.create', 'reports.export', 'reports.operational',

            // Alertes
            'alerts.view', 'alerts.create', 'alerts.edit', 'notifications.send',

            // Géolocalisation
            'tracking.view', 'tracking.real_time', 'tracking.history', 'tracking.geofences',
        ];
        $fleetManager->givePermissionTo($fleetManagerPermissions);

        // 👁️ SUPERVISEUR - Supervision et contrôle
        $supervisor = Role::firstOrCreate([
            'name' => 'Superviseur',
            'guard_name' => 'web'
        ]);
        $supervisorPermissions = [
            // Véhicules
            'vehicles.view', 'vehicles.view_location',

            // Chauffeurs
            'drivers.view', 'drivers.view_performance',

            // Trajets
            'trips.view', 'trips.track', 'trips.reports',

            // Maintenance
            'maintenance.view', 'maintenance.reports',

            // Carburant
            'fuel.view', 'fuel.reports',

            // Documents
            'documents.view', 'documents.download',

            // Rapports
            'reports.view', 'reports.operational',

            // Alertes
            'alerts.view', 'notifications.send',

            // Géolocalisation
            'tracking.view', 'tracking.real_time', 'tracking.history',
        ];
        $supervisor->givePermissionTo($supervisorPermissions);

        // 🚚 CHAUFFEUR - Accès limité aux fonctionnalités de conduite
        $driver = Role::firstOrCreate([
            'name' => 'Chauffeur',
            'guard_name' => 'web'
        ]);
        $driverPermissions = [
            // Véhicules (lecture seule de leur véhicule assigné)
            'vehicles.view',

            // Trajets (leurs trajets uniquement)
            'trips.view',

            // Maintenance (signaler des problèmes)
            'maintenance.view', 'maintenance.create',

            // Carburant (enregistrer les pleins)
            'fuel.view', 'fuel.create',

            // Documents (accès à leurs documents)
            'documents.view', 'documents.download',

            // Alertes (recevoir et consulter)
            'alerts.view',
        ];
        $driver->givePermissionTo($driverPermissions);

        // 💼 COMPTABLE - Gestion financière et facturation
        $accountant = Role::firstOrCreate([
            'name' => 'Comptable',
            'guard_name' => 'web'
        ]);
        $accountantPermissions = [
            // Rapports financiers
            'reports.view', 'reports.export', 'reports.financial',

            // Facturation
            'billing.view', 'billing.create', 'billing.edit', 'billing.export',

            // Carburant (coûts)
            'fuel.view', 'fuel.reports',

            // Maintenance (coûts)
            'maintenance.view', 'maintenance.reports',

            // Fournisseurs
            'suppliers.view', 'suppliers.manage_contracts',

            // Documents financiers
            'documents.view', 'documents.create', 'documents.download',
        ];
        $accountant->givePermissionTo($accountantPermissions);

        // 🔧 MÉCANICIEN - Gestion de la maintenance
        $mechanic = Role::firstOrCreate([
            'name' => 'Mécanicien',
            'guard_name' => 'web'
        ]);
        $mechanicPermissions = [
            // Véhicules (état technique)
            'vehicles.view', 'vehicles.edit',

            // Maintenance
            'maintenance.view', 'maintenance.create', 'maintenance.edit', 'maintenance.reports',

            // Fournisseurs (pièces détachées)
            'suppliers.view',

            // Documents techniques
            'documents.view', 'documents.create', 'documents.download',

            // Alertes techniques
            'alerts.view',
        ];
        $mechanic->givePermissionTo($mechanicPermissions);

        // 📊 ANALYSTE - Analyse et reporting
        $analyst = Role::firstOrCreate([
            'name' => 'Analyste',
            'guard_name' => 'web'
        ]);
        $analystPermissions = [
            // Rapports complets
            'reports.view', 'reports.create', 'reports.export',
            'reports.operational', 'reports.compliance',

            // Vue d'ensemble
            'vehicles.view', 'drivers.view', 'trips.view',
            'maintenance.view', 'fuel.view',

            // Géolocalisation
            'tracking.view', 'tracking.history',

            // Documents
            'documents.view', 'documents.download',

            // Audit
            'audit.view', 'audit.export',
        ];
        $analyst->givePermissionTo($analystPermissions);

        $this->command->info('👥 8 rôles créés avec leurs permissions assignées');
    }
}