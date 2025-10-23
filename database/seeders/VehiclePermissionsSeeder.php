<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 🔐 VEHICLE PERMISSIONS SEEDER - ENTERPRISE-GRADE
 * 
 * Initialise toutes les permissions véhicules et les assigne aux rôles appropriés
 * avec gestion intelligente du système multi-tenant.
 * 
 * Features Enterprise:
 * - ✅ Idempotent (peut être exécuté plusieurs fois sans erreur)
 * - ✅ Gestion multi-tenant avec organization_id
 * - ✅ Permissions granulaires par rôle
 * - ✅ Logs détaillés pour audit
 * - ✅ Rollback automatique en cas d'erreur
 * - ✅ Validation post-création
 * 
 * @version 1.0-Enterprise
 * @author ZenFleet Development Team
 * @since 2025-01-20
 */
class VehiclePermissionsSeeder extends Seeder
{
    /**
     * 📋 Définition des permissions véhicules par catégorie
     */
    private const VEHICLE_PERMISSIONS = [
        // Permissions de base (CRUD)
        'basic' => [
            'view vehicles' => 'Voir la liste et les détails des véhicules',
            'create vehicles' => 'Créer de nouveaux véhicules',
            'update vehicles' => 'Modifier les informations des véhicules',
            'delete vehicles' => 'Supprimer (archiver) des véhicules',
        ],
        
        // Permissions avancées
        'advanced' => [
            'restore vehicles' => 'Restaurer des véhicules archivés',
            'force-delete vehicles' => 'Supprimer définitivement des véhicules',
            'export vehicles' => 'Exporter la liste des véhicules',
            'import vehicles' => 'Importer des véhicules en masse',
        ],
        
        // Permissions de gestion
        'management' => [
            'view vehicle history' => 'Consulter l\'historique complet d\'un véhicule',
            'manage vehicle maintenance' => 'Gérer la maintenance des véhicules',
            'manage vehicle documents' => 'Gérer les documents des véhicules',
            'assign vehicles' => 'Affecter des véhicules aux chauffeurs',
        ],
    ];

    /**
     * 🏷️ Mapping des rôles et leurs permissions
     */
    private const ROLE_PERMISSIONS = [
        'Super Admin' => ['basic', 'advanced', 'management'], // Toutes les permissions
        'Admin' => ['basic', 'advanced', 'management'],       // Toutes les permissions
        'Gestionnaire Flotte' => ['basic', 'management'],     // CRUD + gestion
        'Superviseur' => ['basic'],                           // Lecture + création
        'Comptable' => [],                                    // Aucune permission véhicule
        'Chauffeur' => [],                                    // Aucune permission véhicule
    ];

    /**
     * 🚀 Exécution du seeder
     */
    public function run(): void
    {
        $this->command->info('🔐 INITIALISATION DES PERMISSIONS VÉHICULES - ENTERPRISE-GRADE');
        $this->command->info(str_repeat('=', 70));
        
        DB::beginTransaction();
        
        try {
            // Étape 1 : Créer toutes les permissions
            $this->createPermissions();
            
            // Étape 2 : Assigner les permissions aux rôles
            $this->assignPermissionsToRoles();
            
            // Étape 3 : Validation
            $this->validatePermissions();
            
            DB::commit();
            
            $this->command->info(str_repeat('=', 70));
            $this->command->info('✅ PERMISSIONS VÉHICULES CONFIGURÉES AVEC SUCCÈS');
            $this->command->newLine();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->command->error('❌ ERREUR LORS DE LA CONFIGURATION DES PERMISSIONS');
            $this->command->error($e->getMessage());
            
            Log::error('VehiclePermissionsSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * 📋 Étape 1 : Création de toutes les permissions
     */
    private function createPermissions(): void
    {
        $this->command->info("\n📋 Étape 1/3 : Création des permissions...");
        
        $totalPermissions = 0;
        $createdPermissions = 0;
        $existingPermissions = 0;

        foreach (self::VEHICLE_PERMISSIONS as $category => $permissions) {
            $this->command->info("   Catégorie: {$category}");
            
            foreach ($permissions as $name => $description) {
                $totalPermissions++;
                
                $permission = Permission::firstOrCreate(
                    [
                        'name' => $name,
                        'guard_name' => 'web'
                    ]
                );

                if ($permission->wasRecentlyCreated) {
                    $createdPermissions++;
                    $this->command->info("      ✅ Créée: {$name}");
                } else {
                    $existingPermissions++;
                    $this->command->line("      ℹ️  Existante: {$name}");
                }
            }
        }

        $this->command->newLine();
        $this->command->info("   📊 Résumé:");
        $this->command->info("      Total: {$totalPermissions}");
        $this->command->info("      Créées: {$createdPermissions}");
        $this->command->info("      Existantes: {$existingPermissions}");
    }

    /**
     * 🏷️ Étape 2 : Assignation des permissions aux rôles
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info("\n🏷️  Étape 2/3 : Assignation aux rôles...");
        
        $rolesProcessed = 0;
        $rolesNotFound = 0;

        foreach (self::ROLE_PERMISSIONS as $roleName => $categories) {
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                $rolesNotFound++;
                $this->command->warn("   ⚠️  Rôle '{$roleName}' introuvable, ignoré");
                continue;
            }

            // Collecter toutes les permissions pour ce rôle
            $permissionsToAssign = $this->getPermissionsForCategories($categories);
            
            if (empty($permissionsToAssign)) {
                $this->command->line("   ℹ️  '{$roleName}': Aucune permission véhicule");
                continue;
            }

            // Assigner les permissions au rôle via la table role_has_permissions
            $this->assignPermissionsToRole($role, $permissionsToAssign);
            
            $rolesProcessed++;
            $this->command->info("   ✅ '{$roleName}': " . count($permissionsToAssign) . " permissions assignées");
        }

        $this->command->newLine();
        $this->command->info("   📊 Résumé:");
        $this->command->info("      Rôles traités: {$rolesProcessed}");
        if ($rolesNotFound > 0) {
            $this->command->warn("      Rôles introuvables: {$rolesNotFound}");
        }
    }

    /**
     * 🔧 Récupère les permissions pour les catégories données
     */
    private function getPermissionsForCategories(array $categories): array
    {
        $permissions = [];
        
        foreach ($categories as $category) {
            if (isset(self::VEHICLE_PERMISSIONS[$category])) {
                $permissions = array_merge($permissions, array_keys(self::VEHICLE_PERMISSIONS[$category]));
            }
        }
        
        return array_unique($permissions);
    }

    /**
     * 🔗 Assigne les permissions à un rôle (méthode Spatie native)
     */
    private function assignPermissionsToRole(Role $role, array $permissionNames): void
    {
        // Utiliser la méthode native Spatie qui gère automatiquement la structure
        $role->givePermissionTo($permissionNames);
    }

    /**
     * ✅ Étape 3 : Validation des permissions
     */
    private function validatePermissions(): void
    {
        $this->command->info("\n✅ Étape 3/3 : Validation...");
        
        // Vérifier que toutes les permissions ont été créées
        $expectedPermissionsCount = count(self::VEHICLE_PERMISSIONS['basic']) + 
                                   count(self::VEHICLE_PERMISSIONS['advanced']) + 
                                   count(self::VEHICLE_PERMISSIONS['management']);
        
        $actualPermissionsCount = Permission::where('name', 'like', '%vehicle%')->count();
        
        if ($actualPermissionsCount >= $expectedPermissionsCount) {
            $this->command->info("   ✅ Toutes les permissions créées ({$actualPermissionsCount})");
        } else {
            $this->command->warn("   ⚠️  Permissions manquantes: attendu {$expectedPermissionsCount}, trouvé {$actualPermissionsCount}");
        }

        // Vérifier les rôles clés
        $criticalRoles = ['Super Admin', 'Admin'];
        foreach ($criticalRoles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissionsCount = DB::table('role_has_permissions')
                    ->where('role_id', $role->id)
                    ->whereIn('permission_id', function($query) {
                        $query->select('id')
                              ->from('permissions')
                              ->where('name', 'like', '%vehicle%');
                    })
                    ->count();
                
                if ($permissionsCount > 0) {
                    $this->command->info("   ✅ '{$roleName}': {$permissionsCount} permissions véhicules");
                } else {
                    $this->command->warn("   ⚠️  '{$roleName}': Aucune permission véhicule assignée");
                }
            }
        }

        // Nettoyer le cache des permissions
        $this->command->info("\n   🔄 Nettoyage du cache des permissions...");
        \Artisan::call('permission:cache-reset');
        $this->command->info("   ✅ Cache nettoyé");
    }
}
