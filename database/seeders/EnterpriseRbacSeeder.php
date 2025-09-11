<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EnterpriseRbacSeeder extends Seeder
{
    /**
     * 🚀 SEEDER RBAC ENTERPRISE ZENFLEET - STANDARD INTERNATIONAL
     * Version: 2.0 - Ultra-Professionnel
     * Compatible: Laravel 9+ & Spatie Permission v5+
     */
    public function run(): void
    {
        $this->command->info('🚀 ZENFLEET ENTERPRISE RBAC SYSTEM - INITIALISATION');
        $this->command->info('=========================================================');
        
        // Reset du cache des permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        // 1. Créer toutes les permissions
        $this->createEnterprisePermissions();
        
        // 2. Créer les rôles hiérarchiques 
        $this->createEnterpriseRoles();
        
        // 3. Créer les utilisateurs de test
        $this->createTestUsers();
        
        // 4. Affichage final
        $this->displayFinalSummary();
        
        $this->command->info('✅ SYSTÈME RBAC ENTERPRISE INITIALISÉ AVEC SUCCÈS !');
    }

    /**
     * 🛡️ CRÉATION DES PERMISSIONS ENTERPRISE - COMPLÈTES
     */
    private function createEnterprisePermissions(): void
    {
        $this->command->info('🛡️ Création des permissions enterprise...');
        
        $permissions = [
            // 🌐 PERMISSIONS SYSTÈME (Super Admin uniquement)
            'manage system',
            'view system dashboard',
            'view all organizations',
            'create organizations',
            'edit organizations', 
            'delete organizations',
            'suspend organizations',
            'activate organizations',
            'manage global settings',
            'view system logs',
            'create global admins',
            'manage global roles',
            'view system metrics',
            'manage system backups',
            
            // 🏢 PERMISSIONS ORGANISATION
            'view organizations',
            'edit organizations',
            'manage organization settings',
            'manage organization billing',
            'view organization analytics',
            'export organization data',
            
            // 👥 PERMISSIONS UTILISATEURS ET RÔLES
            'view users',
            'create users',
            'edit users',
            'delete users',
            'restore users',
            'assign user roles',
            'view user profiles',
            'manage roles',
            'view roles',
            'edit roles',
            'assign permissions',
            
            // 🚗 PERMISSIONS VÉHICULES
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'restore vehicles',
            'force delete vehicles',
            'assign vehicles',
            'track vehicles',
            'import vehicles',
            'export vehicles',
            'view vehicle history',
            'manage vehicle status',
            
            // 👨‍💼 PERMISSIONS CHAUFFEURS
            'view drivers',
            'create drivers',
            'edit drivers', 
            'delete drivers',
            'restore drivers',
            'force delete drivers',
            'assign drivers',
            'view driver profiles',
            'manage driver licenses',
            'import drivers',
            'export drivers',
            'view driver history',
            
            // 📋 PERMISSIONS AFFECTATIONS
            'view assignments',
            'create assignments',
            'edit assignments',
            'end assignments',
            'extend assignments',
            'view assignment history',
            'manage assignment status',
            
            // 🔧 PERMISSIONS MAINTENANCE
            'view maintenance',
            'manage maintenance plans',
            'log maintenance',
            'schedule maintenance',
            'view maintenance history',
            'manage maintenance alerts',
            'approve maintenance',
            'view maintenance costs',
            
            // 📋 PERMISSIONS FICHES DE REMISE
            'create handovers',
            'view handovers',
            'edit handovers',
            'delete handovers',
            'upload signed handovers',
            'download handovers',
            'approve handovers',
            
            // 🏭 PERMISSIONS FOURNISSEURS
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'manage supplier contracts',
            'view supplier performance',
            
            // 📄 PERMISSIONS DOCUMENTS
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'manage document categories',
            'upload documents',
            'download documents',
            'archive documents',
            
            // 📊 PERMISSIONS RAPPORTS ET ANALYTICS
            'view reports',
            'create reports',
            'edit reports',
            'delete reports',
            'export reports',
            'schedule reports',
            'view basic analytics',
            'view advanced analytics',
            'export analytics',
            'manage dashboards',
            
            // 🔍 PERMISSIONS AUDIT ET SÉCURITÉ
            'view audit logs',
            'export audit logs',
            'manage security settings',
            'view login attempts',
            'manage user sessions',
            'view system security',
            
            // 🔑 PERMISSIONS API ET INTÉGRATIONS
            'view api settings',
            'manage api keys',
            'view api logs',
            'manage webhooks',
            'test integrations',
            
            // 💰 PERMISSIONS FINANCIÈRES
            'view costs',
            'manage budgets',
            'view financial reports',
            'export financial data',
            'manage invoicing',
            
            // 🎛️ PERMISSIONS CONFIGURATION
            'manage settings',
            'view system configuration',
            'manage notifications',
            'configure alerts',
            'manage email templates',
            
            // 📱 PERMISSIONS SPÉCIALES CHAUFFEURS
            'view own vehicles',
            'view own assignments',
            'update assignment status',
            'view own handovers',
            'sign handovers',
            'view own maintenance',
            'report vehicle issues',
            'log trip data',
            'view own profile',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $this->command->info("   ✅ " . count($permissions) . " permissions créées/vérifiées");
    }

    /**
     * 👑 CRÉATION DES RÔLES HIÉRARCHIQUES ENTERPRISE
     */
    private function createEnterpriseRoles(): void
    {
        $this->command->info('👑 Création des rôles hiérarchiques enterprise...');
        
        // 🌐 SUPER ADMIN - Niveau Système Global
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);
        
        $superAdminPermissions = Permission::all()->pluck('name')->toArray();
        $superAdminRole->syncPermissions($superAdminPermissions);
        $this->command->info("   ✅ Super Admin: " . count($superAdminPermissions) . " permissions (TOUTES)");
        
        // 🏢 ADMIN - Niveau Organisation
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin', 
            'guard_name' => 'web'
        ]);
        
        $adminPermissions = [
            // Organisation (sans création)
            'view organizations', 'edit organizations', 'manage organization settings',
            'manage organization billing', 'view organization analytics', 'export organization data',
            
            // Utilisateurs complets
            'view users', 'create users', 'edit users', 'delete users', 'restore users',
            'assign user roles', 'view user profiles', 'manage roles', 'view roles', 'edit roles',
            
            // Véhicules complets
            'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'restore vehicles',
            'assign vehicles', 'track vehicles', 'import vehicles', 'export vehicles', 'view vehicle history',
            
            // Chauffeurs complets  
            'view drivers', 'create drivers', 'edit drivers', 'delete drivers', 'restore drivers',
            'assign drivers', 'view driver profiles', 'manage driver licenses', 'import drivers', 'export drivers',
            
            // Opérations
            'view assignments', 'create assignments', 'edit assignments', 'end assignments',
            'view maintenance', 'manage maintenance plans', 'log maintenance', 'schedule maintenance',
            'create handovers', 'view handovers', 'edit handovers', 'delete handovers', 'upload signed handovers',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',
            'view documents', 'create documents', 'edit documents', 'delete documents', 'manage document categories',
            
            // Rapports et Analytics
            'view reports', 'create reports', 'export reports', 'view basic analytics', 'view advanced analytics',
            'export analytics', 'view audit logs', 'export audit logs',
        ];
        $adminRole->syncPermissions($adminPermissions);
        $this->command->info("   ✅ Admin: " . count($adminPermissions) . " permissions");
        
        // 🚗 GESTIONNAIRE FLOTTE - Niveau Opérationnel
        $fleetManagerRole = Role::firstOrCreate([
            'name' => 'Gestionnaire Flotte',
            'guard_name' => 'web'
        ]);
        
        $fleetManagerPermissions = [
            // Véhicules (gestion complète)
            'view vehicles', 'create vehicles', 'edit vehicles', 'assign vehicles', 'track vehicles',
            'import vehicles', 'export vehicles', 'view vehicle history', 'manage vehicle status',
            
            // Chauffeurs (gestion complète)
            'view drivers', 'create drivers', 'edit drivers', 'assign drivers', 'view driver profiles',
            'manage driver licenses', 'import drivers', 'export drivers', 'view driver history',
            
            // Affectations
            'view assignments', 'create assignments', 'edit assignments', 'end assignments', 'extend assignments',
            
            // Maintenance
            'view maintenance', 'manage maintenance plans', 'log maintenance', 'schedule maintenance',
            'view maintenance history', 'manage maintenance alerts',
            
            // Handovers
            'create handovers', 'view handovers', 'edit handovers', 'upload signed handovers', 'approve handovers',
            
            // Fournisseurs
            'view suppliers', 'create suppliers', 'edit suppliers', 'manage supplier contracts',
            
            // Documents
            'view documents', 'create documents', 'edit documents', 'manage document categories',
            
            // Rapports
            'view reports', 'create reports', 'export reports', 'view basic analytics', 'view advanced analytics',
        ];
        $fleetManagerRole->syncPermissions($fleetManagerPermissions);
        $this->command->info("   ✅ Gestionnaire Flotte: " . count($fleetManagerPermissions) . " permissions");
        
        // 👥 SUPERVISOR - Niveau Supervision
        $supervisorRole = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web'
        ]);
        
        $supervisorPermissions = [
            // Consultation
            'view vehicles', 'view vehicle history', 'track vehicles',
            'view drivers', 'view driver profiles', 'view driver history',
            'view assignments', 'view assignment history',
            'view maintenance', 'view maintenance history',
            'view handovers', 'view suppliers',
            'view documents', 'view reports', 'view basic analytics',
            
            // Actions limitées
            'create assignments', 'edit assignments', 'log maintenance',
            'create handovers', 'edit handovers', 'create reports',
        ];
        $supervisorRole->syncPermissions($supervisorPermissions);
        $this->command->info("   ✅ supervisor: " . count($supervisorPermissions) . " permissions");
        
        // 🚙 CHAUFFEUR - Niveau Utilisateur Final
        $chauffeurRole = Role::firstOrCreate([
            'name' => 'Chauffeur',
            'guard_name' => 'web'
        ]);
        
        $chauffeurPermissions = [
            'view own vehicles',
            'view own assignments', 
            'update assignment status',
            'view own handovers',
            'sign handovers',
            'view own maintenance',
            'report vehicle issues',
            'log trip data',
            'view own profile',
        ];
        $chauffeurRole->syncPermissions($chauffeurPermissions);
        $this->command->info("   ✅ Chauffeur: " . count($chauffeurPermissions) . " permissions");
    }

    /**
     * 👤 CRÉATION DES UTILISATEURS DE TEST
     */
    private function createTestUsers(): void
    {
        $this->command->info('👤 Création des utilisateurs de test...');
        
        // Récupérer une organisation active ou créer une organisation de test
        $organization = Organization::where('status', 'active')->first();
        if (!$organization) {
            $organization = Organization::create([
                'name' => 'Organisation de Test',
                'slug' => 'organisation-test',
                'legal_name' => 'Test Organization SAS',
                'organization_type' => 'enterprise',
                'email' => 'contact@test-org.com',
                'address' => '123 Rue de Test',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'FR',
                'timezone' => 'Europe/Paris',
                'currency' => 'EUR',
                'language' => 'fr',
                'status' => 'active',
                'subscription_plan' => 'enterprise',
                'subscription_expires_at' => now()->addYear(),
                'max_vehicles' => 1000,
                'max_drivers' => 500,
                'max_users' => 100,
            ]);
            $this->command->info("   📋 Organisation de test créée: {$organization->name}");
        }

        $testUsers = [
            [
                'email' => 'superadmin@zenfleet.app',
                'name' => 'Super Administrateur',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'role' => 'Super Admin',
                'password' => 'SuperAdmin2025!',
                'organization_id' => null, // Global
                'job_title' => 'Super Administrateur Système',
                'department' => 'IT Système'
            ],
            [
                'email' => 'admin@zenfleet.app',
                'name' => 'Administrateur',
                'first_name' => 'Admin',
                'last_name' => 'Organisation',
                'role' => 'Admin',
                'password' => 'AdminZen2025!',
                'organization_id' => $organization->id,
                'job_title' => 'Administrateur',
                'department' => 'Direction'
            ],
            [
                'email' => 'fleet@zenfleet.app',
                'name' => 'Gestionnaire de Flotte',
                'first_name' => 'Gestionnaire',
                'last_name' => 'Flotte',
                'role' => 'Gestionnaire Flotte',
                'password' => 'FleetManager2025!',
                'organization_id' => $organization->id,
                'job_title' => 'Gestionnaire de Flotte',
                'department' => 'Opérations'
            ],
            [
                'email' => 'supervisor@zenfleet.app',
                'name' => 'Superviseur Équipe',
                'first_name' => 'Superviseur',
                'last_name' => 'Équipe',
                'role' => 'supervisor',
                'password' => 'Supervisor2025!',
                'organization_id' => $organization->id,
                'job_title' => 'Superviseur',
                'department' => 'Opérations'
            ],
            [
                'email' => 'driver@zenfleet.app',
                'name' => 'Chauffeur Professionnel',
                'first_name' => 'Chauffeur',
                'last_name' => 'Pro',
                'role' => 'Chauffeur',
                'password' => 'Driver2025!',
                'organization_id' => $organization->id,
                'job_title' => 'Chauffeur Professionnel',
                'department' => 'Transport'
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'job_title' => $userData['job_title'],
                    'department' => $userData['department'],
                    'organization_id' => $userData['organization_id'],
                    'is_super_admin' => $userData['role'] === 'Super Admin',
                    'is_active' => true,
                    'user_status' => 'active',
                    'email_verified_at' => now(),
                    'password' => Hash::make($userData['password']),
                    'remember_token' => Str::random(10),
                    'timezone' => 'Europe/Paris',
                    'language' => 'fr',
                ]
            );

            // Assigner le rôle
            $user->syncRoles([$userData['role']]);
            
            $this->command->info("   ✅ {$userData['role']}: {$userData['email']}");
        }
    }

    /**
     * 📊 AFFICHAGE DU RÉSUMÉ FINAL
     */
    private function displayFinalSummary(): void
    {
        $this->command->info('');
        $this->command->info('🏆 RÉSUMÉ FINAL - SYSTÈME RBAC ENTERPRISE ZENFLEET');
        $this->command->info('=========================================================');
        
        // Statistiques
        $permissionsCount = Permission::count();
        $rolesCount = Role::count();
        $usersCount = User::count();
        
        $this->command->info("📊 STATISTIQUES:");
        $this->command->info("   🛡️ Permissions créées: {$permissionsCount}");
        $this->command->info("   👑 Rôles configurés: {$rolesCount}");
        $this->command->info("   👤 Utilisateurs de test: {$usersCount}");
        $this->command->info('');
        
        // Hiérarchie des rôles
        $this->command->info("🏗️ HIÉRARCHIE DES RÔLES:");
        $this->command->info("   1️⃣ Super Admin (superadmin@zenfleet.app) - Système Global");
        $this->command->info("   2️⃣ Admin (admin@zenfleet.app) - Organisation");
        $this->command->info("   3️⃣ Gestionnaire Flotte (fleet@zenfleet.app) - Opérationnel");
        $this->command->info("   4️⃣ supervisor (supervisor@zenfleet.app) - Supervision");
        $this->command->info("   5️⃣ Chauffeur (driver@zenfleet.app) - Utilisateur Final");
        $this->command->info('');
        
        // Comptes de test
        $this->command->info("🔐 COMPTES DE TEST:");
        $this->command->info("   🌐 superadmin@zenfleet.app / SuperAdmin2025!");
        $this->command->info("   🏢 admin@zenfleet.app / AdminZen2025!");
        $this->command->info("   🚗 fleet@zenfleet.app / FleetManager2025!");
        $this->command->info("   👥 supervisor@zenfleet.app / Supervisor2025!");
        $this->command->info("   🚙 driver@zenfleet.app / Driver2025!");
        $this->command->info('');
        
        // Instructions finales
        $this->command->info("🎯 PROCHAINES ÉTAPES:");
        $this->command->info("   1. Testez la connexion avec les comptes ci-dessus");
        $this->command->info("   2. Vérifiez les permissions pour chaque rôle");
        $this->command->info("   3. Configurez les organisations selon vos besoins");
        $this->command->info("   4. Personnalisez les rôles si nécessaire");
        $this->command->info('');
    }
}
