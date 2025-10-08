<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * 📊 Seeder des Permissions Kilométrage - Enterprise Grade
 *
 * Crée toutes les permissions nécessaires pour le module kilométrage
 * et les assigne automatiquement aux rôles appropriés.
 *
 * Permissions créées:
 * - view own mileage readings: Chauffeur peut voir ses propres relevés
 * - view team mileage readings: Superviseur peut voir les relevés de son équipe
 * - view all mileage readings: Admin/Gestionnaire peut voir tous les relevés
 * - create mileage readings: Créer de nouveaux relevés
 * - edit mileage readings: Modifier des relevés existants
 * - delete mileage readings: Supprimer des relevés
 * - export mileage readings: Exporter les données
 *
 * @package Database\Seeders
 * @version 1.0-Enterprise
 * @author ZenFleet Development Team
 */
class MileagePermissionsSeeder extends Seeder
{
    /**
     * Exécute le seeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            Log::info('🚀 Démarrage du seeder MileagePermissionsSeeder');

            // 1. Créer les permissions pour le kilométrage
            $this->createMileagePermissions();

            // 2. Assigner les permissions aux rôles
            $this->assignPermissionsToRoles();

            DB::commit();

            $this->command->info('✅ Permissions kilométrage créées et assignées avec succès');
            Log::info('✅ MileagePermissionsSeeder terminé avec succès');

        } catch (\Exception $e) {
            DB::rollBack();

            $errorMessage = 'Erreur lors de la création des permissions kilométrage: ' . $e->getMessage();
            $this->command->error('❌ ' . $errorMessage);
            Log::error($errorMessage, [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Crée toutes les permissions pour le module kilométrage
     */
    private function createMileagePermissions(): void
    {
        $permissions = [
            // Permissions de lecture
            'view own mileage readings',
            'view team mileage readings',
            'view all mileage readings',

            // Permissions d'écriture
            'create mileage readings',
            'edit mileage readings',
            'delete mileage readings',

            // Permissions avancées
            'export mileage readings',
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                [
                    'name' => $permissionName,
                    'guard_name' => 'web'
                ]
            );

            $this->command->info("  ✓ Permission créée: {$permission->name}");
            Log::info("Permission kilométrage créée", [
                'permission' => $permission->name
            ]);
        }
    }

    /**
     * Assigne les permissions aux rôles appropriés
     */
    private function assignPermissionsToRoles(): void
    {
        // Mapping des rôles et leurs permissions
        $rolePermissions = [
            'Super Admin' => [
                'view all mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'delete mileage readings',
                'export mileage readings',
            ],
            'Admin' => [
                'view all mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'delete mileage readings',
                'export mileage readings',
            ],
            'Gestionnaire Flotte' => [
                'view all mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'delete mileage readings',
                'export mileage readings',
            ],
            'Supervisor' => [
                'view team mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'export mileage readings',
            ],
            'Chef de Parc' => [
                'view team mileage readings',
                'create mileage readings',
                'edit mileage readings',
                'export mileage readings',
            ],
            'Chauffeur' => [
                'view own mileage readings',
                'create mileage readings',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                foreach ($permissions as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();

                    if ($permission && !$role->hasPermissionTo($permission)) {
                        $role->givePermissionTo($permission);
                        $this->command->info("  ✓ Permission '{$permissionName}' assignée au rôle '{$roleName}'");
                    }
                }

                Log::info("Permissions assignées au rôle", [
                    'role' => $roleName,
                    'permissions_count' => count($permissions)
                ]);
            } else {
                $this->command->warn("  ⚠ Rôle '{$roleName}' non trouvé");
                Log::warning("Rôle non trouvé lors de l'assignation des permissions", ['role' => $roleName]);
            }
        }
    }
}
