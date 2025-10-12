<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * 🔧 FIX PERMISSIONS SEEDER - Correction Format Enterprise
 *
 * Corrige le format des permissions pour correspondre au middleware EnterprisePermissionMiddleware
 * Format Spatie standard: "action resource" (ex: "view vehicles", "create drivers")
 *
 * @version 1.0-FIX
 * @author ZenFleet Security Team
 */
class FixPermissionsSeeder extends Seeder
{
    /**
     * 📋 DÉFINITION DES PERMISSIONS - FORMAT SPATIE STANDARD
     *
     * Structure: 'action resource' où action = view|create|edit|delete|restore|manage|approve|export|import
     */
    private function getPermissionsDefinition(): array
    {
        return [
            // 🏢 ORGANISATIONS (Super Admin uniquement)
            'organizations' => [
                'view organizations',
                'create organizations',
                'edit organizations',
                'delete organizations',
                'restore organizations',
                'export organizations',
                'manage organization settings',
                'view organization statistics',
            ],

            // 👥 UTILISATEURS
            'users' => [
                'view users',
                'create users',
                'edit users',
                'delete users',
                'restore users',
                'export users',
                'manage user roles',
                'reset user passwords',
                'impersonate users',
            ],

            // 🎭 RÔLES ET PERMISSIONS
            'roles' => [
                'view roles',
                'manage roles',  // Includes create, edit, delete roles + assign permissions
            ],

            // 🚗 VÉHICULES
            'vehicles' => [
                'view vehicles',
                'create vehicles',
                'edit vehicles',
                'delete vehicles',
                'restore vehicles',
                'export vehicles',
                'import vehicles',
                'view vehicle history',
                'manage vehicle maintenance',
                'manage vehicle documents',
            ],

            // 👨‍💼 CHAUFFEURS
            'drivers' => [
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
            ],

            // 📋 AFFECTATIONS
            'assignments' => [
                'view assignments',
                'create assignments',
                'edit assignments',
                'delete assignments',
                'end assignments',
                'extend assignments',
                'export assignments',
                'view assignment calendar',
                'view assignment gantt',
            ],

            // 🔧 MAINTENANCE
            'maintenance' => [
                'view maintenance',
                'manage maintenance plans',
                'create maintenance operations',
                'edit maintenance operations',
                'delete maintenance operations',
                'approve maintenance operations',
                'export maintenance reports',
            ],

            // 🛠️ DEMANDES DE RÉPARATION
            'repair_requests' => [
                'view own repair requests',
                'view team repair requests',
                'view all repair requests',
                'create repair requests',
                'update own repair requests',
                'delete repair requests',
                'approve repair requests level 1',  // Superviseur
                'approve repair requests level 2',  // Fleet Manager
                'reject repair requests',
                'export repair requests',
            ],

            // 📊 RELEVÉS KILOMÉTRIQUES
            'mileage_readings' => [
                'view own mileage readings',
                'view team mileage readings',
                'view all mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'delete mileage readings',
                'export mileage readings',
            ],

            // 🏪 FOURNISSEURS
            'suppliers' => [
                'view suppliers',
                'create suppliers',
                'edit suppliers',
                'delete suppliers',
                'restore suppliers',
                'export suppliers',
                'manage supplier contracts',
            ],

            // 💰 DÉPENSES
            'expenses' => [
                'view expenses',
                'create expenses',
                'edit expenses',
                'delete expenses',
                'approve expenses',
                'export expenses',
                'view expense analytics',
            ],

            // 📄 DOCUMENTS
            'documents' => [
                'view documents',
                'create documents',
                'edit documents',
                'delete documents',
                'download documents',
                'approve documents',
                'export documents',
            ],

            // 📊 RAPPORTS ET ANALYTICS
            'analytics' => [
                'view analytics',
                'view performance metrics',
                'view roi metrics',
                'export analytics',
            ],

            // 🔔 ALERTES
            'alerts' => [
                'view alerts',
                'create alerts',
                'edit alerts',
                'delete alerts',
                'mark alerts as read',
                'export alerts',
            ],

            // 🔍 AUDIT
            'audit' => [
                'view audit logs',
                'export audit logs',
                'view security audit',
                'view user audit',
                'view organization audit',
            ],
        ];
    }

    /**
     * 🎭 DÉFINITION DES RÔLES ET LEURS PERMISSIONS
     */
    private function getRolesDefinition(): array
    {
        return [
            'Super Admin' => [
                'description' => 'Accès total au système, toutes organisations',
                'permissions' => 'all',  // Toutes les permissions
                'organization_id' => null,  // Cross-tenant
            ],

            'Admin' => [
                'description' => 'Administrateur d\'une organisation',
                'permissions' => [
                    // Gestion utilisateurs
                    'view users', 'create users', 'edit users', 'delete users',
                    'manage user roles', 'reset user passwords',

                    // Gestion véhicules
                    'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
                    'export vehicles', 'import vehicles', 'view vehicle history',
                    'manage vehicle maintenance', 'manage vehicle documents',

                    // Gestion chauffeurs
                    'view drivers', 'create drivers', 'edit drivers', 'delete drivers',
                    'export drivers', 'import drivers', 'assign drivers to vehicles',
                    'manage driver licenses',

                    // Affectations
                    'view assignments', 'create assignments', 'edit assignments',
                    'end assignments', 'extend assignments', 'export assignments',
                    'view assignment calendar', 'view assignment gantt',

                    // Maintenance et réparations
                    'view maintenance', 'manage maintenance plans',
                    'view all repair requests', 'approve repair requests level 2',

                    // Finances
                    'view expenses', 'approve expenses', 'view expense analytics',
                    'view suppliers', 'create suppliers', 'edit suppliers',

                    // Documents
                    'view documents', 'create documents', 'edit documents', 'approve documents',

                    // Relevés kilométriques
                    'view all mileage readings', 'create mileage readings',
                    'edit mileage readings', 'export mileage readings',

                    // Analytics et audit
                    'view analytics', 'view performance metrics', 'view roi metrics',
                    'view audit logs',

                    // Alertes
                    'view alerts', 'create alerts', 'edit alerts',
                ],
                'organization_id' => null,  // Will be set per organization
            ],

            'Gestionnaire Flotte' => [
                'description' => 'Gestionnaire de flotte opérationnel',
                'permissions' => [
                    // Véhicules
                    'view vehicles', 'create vehicles', 'edit vehicles',
                    'export vehicles', 'view vehicle history', 'manage vehicle maintenance',

                    // Chauffeurs
                    'view drivers', 'create drivers', 'edit drivers',
                    'assign drivers to vehicles',

                    // Affectations
                    'view assignments', 'create assignments', 'edit assignments',
                    'end assignments', 'view assignment calendar',

                    // Maintenance
                    'view maintenance', 'manage maintenance plans',
                    'create maintenance operations', 'edit maintenance operations',

                    // Réparations
                    'view all repair requests', 'create repair requests',
                    'approve repair requests level 2',

                    // Relevés kilométriques
                    'view all mileage readings', 'create mileage readings',
                    'edit mileage readings',

                    // Fournisseurs
                    'view suppliers', 'create suppliers', 'edit suppliers',

                    // Documents
                    'view documents', 'create documents', 'edit documents',

                    // Analytics
                    'view analytics', 'view performance metrics',
                ],
                'organization_id' => null,
            ],

            'Superviseur' => [
                'description' => 'Superviseur d\'équipe chauffeurs',
                'permissions' => [
                    // Véhicules (lecture)
                    'view vehicles', 'view vehicle history',

                    // Chauffeurs de son équipe
                    'view drivers',

                    // Affectations
                    'view assignments', 'create assignments', 'end assignments',

                    // Réparations (niveau 1)
                    'view team repair requests', 'create repair requests',
                    'approve repair requests level 1',

                    // Relevés kilométriques
                    'view team mileage readings', 'create mileage readings',

                    // Documents
                    'view documents', 'create documents',
                ],
                'organization_id' => null,
            ],

            'Chauffeur' => [
                'description' => 'Chauffeur de véhicule',
                'permissions' => [
                    // Véhicules (lecture uniquement de son véhicule)
                    'view vehicles',

                    // Ses propres données
                    'view own repair requests', 'create repair requests',
                    'update own repair requests',

                    // Relevés kilométriques
                    'view own mileage readings', 'create mileage readings',

                    // Documents
                    'view documents',
                ],
                'organization_id' => null,
            ],

            'Comptable' => [
                'description' => 'Comptable et gestion financière',
                'permissions' => [
                    // Finances
                    'view expenses', 'create expenses', 'edit expenses',
                    'approve expenses', 'export expenses', 'view expense analytics',

                    // Fournisseurs
                    'view suppliers', 'create suppliers', 'edit suppliers',
                    'manage supplier contracts',

                    // Documents financiers
                    'view documents', 'create documents', 'approve documents',

                    // Véhicules et maintenance (lecture pour facturation)
                    'view vehicles', 'view maintenance',

                    // Analytics financiers
                    'view analytics', 'view roi metrics',
                ],
                'organization_id' => null,
            ],

            'Mécanicien' => [
                'description' => 'Mécanicien/Technicien maintenance',
                'permissions' => [
                    // Maintenance
                    'view maintenance', 'create maintenance operations',
                    'edit maintenance operations',

                    // Réparations
                    'view all repair requests', 'update own repair requests',

                    // Véhicules (diagnostic)
                    'view vehicles', 'view vehicle history',

                    // Relevés kilométriques
                    'view all mileage readings', 'create mileage readings',

                    // Documents
                    'view documents', 'create documents',
                ],
                'organization_id' => null,
            ],

            'Analyste' => [
                'description' => 'Analyste données et reporting',
                'permissions' => [
                    // Analytics
                    'view analytics', 'view performance metrics', 'view roi metrics',
                    'export analytics',

                    // Audit (lecture seule)
                    'view audit logs',

                    // Toutes les vues pour reporting
                    'view vehicles', 'view drivers', 'view assignments',
                    'view maintenance', 'view all repair requests',
                    'view all mileage readings', 'view expenses',

                    // Exports
                    'export vehicles', 'export drivers', 'export assignments',
                    'export mileage readings', 'export expenses',
                ],
                'organization_id' => null,
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔧 CORRECTION DES PERMISSIONS - FORMAT SPATIE STANDARD\n";
        echo str_repeat('=', 80) . "\n\n";

        // Désactiver les contraintes FK temporairement
        DB::statement('SET CONSTRAINTS ALL DEFERRED');

        try {
            // 1. Purger les anciennes permissions et associations
            $this->purgeOldPermissions();

            // 2. Créer les nouvelles permissions
            $this->createPermissions();

            // 3. Réassigner les permissions aux rôles
            $this->assignPermissionsToRoles();

            // 4. Vider le cache des permissions
            $this->clearPermissionsCache();

            echo "\n✅ CORRECTION TERMINÉE AVEC SUCCÈS!\n";
            echo str_repeat('=', 80) . "\n";

        } catch (\Exception $e) {
            echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * Purger les anciennes permissions
     */
    private function purgeOldPermissions(): void
    {
        echo "1️⃣  Purge des anciennes permissions...\n";

        // Supprimer les associations role_has_permissions
        DB::table('role_has_permissions')->delete();
        echo "   ✅ Associations rôle-permission supprimées\n";

        // Supprimer les associations model_has_permissions
        DB::table('model_has_permissions')->delete();
        echo "   ✅ Associations modèle-permission supprimées\n";

        // Supprimer toutes les permissions
        DB::table('permissions')->delete();
        echo "   ✅ Permissions supprimées\n\n";
    }

    /**
     * Créer les nouvelles permissions
     */
    private function createPermissions(): void
    {
        echo "2️⃣  Création des nouvelles permissions...\n";

        $permissions = $this->getPermissionsDefinition();
        $totalCreated = 0;

        foreach ($permissions as $resource => $permissionList) {
            echo "   📦 {$resource}:\n";

            foreach ($permissionList as $permissionName) {
                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);

                $totalCreated++;
                echo "      ✅ {$permissionName}\n";
            }
        }

        echo "\n   📊 Total permissions créées: {$totalCreated}\n\n";
    }

    /**
     * Assigner les permissions aux rôles
     */
    private function assignPermissionsToRoles(): void
    {
        echo "3️⃣  Assignment des permissions aux rôles...\n";

        $rolesDefinition = $this->getRolesDefinition();

        foreach ($rolesDefinition as $roleName => $roleConfig) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if (!$role) {
                echo "   ⚠️  Rôle '{$roleName}' non trouvé, skip\n";
                continue;
            }

            echo "   🎭 {$roleName}:\n";

            // Retirer toutes les permissions existantes
            $role->syncPermissions([]);

            // Assigner les nouvelles permissions
            if ($roleConfig['permissions'] === 'all') {
                // Super Admin = toutes les permissions
                $allPermissions = Permission::all();
                $role->givePermissionTo($allPermissions);
                echo "      ✅ Toutes les permissions assignées ({$allPermissions->count()})\n";
            } else {
                // Assigner les permissions spécifiques
                $assigned = 0;
                foreach ($roleConfig['permissions'] as $permissionName) {
                    try {
                        $role->givePermissionTo($permissionName);
                        $assigned++;
                    } catch (\Exception $e) {
                        echo "      ⚠️  Permission '{$permissionName}' non trouvée\n";
                    }
                }
                echo "      ✅ {$assigned} permissions assignées\n";
            }
        }

        echo "\n";
    }

    /**
     * Vider le cache des permissions
     */
    private function clearPermissionsCache(): void
    {
        echo "4️⃣  Vidage du cache des permissions...\n";

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('spatie.permission.cache');

        echo "   ✅ Cache invalidé\n\n";
    }
}
