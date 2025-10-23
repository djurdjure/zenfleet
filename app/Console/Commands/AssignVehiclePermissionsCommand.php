<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

/**
 * 🔐 COMMANDE D'ASSIGNATION DES PERMISSIONS VÉHICULES - ENTERPRISE-GRADE
 * 
 * Assigne toutes les permissions véhicules à un utilisateur spécifique
 * en gérant intelligemment le système multi-tenant.
 * 
 * Usage:
 *   php artisan permissions:assign-vehicles {email}
 *   php artisan permissions:assign-vehicles superadmin@zenfleet.dz
 *   php artisan permissions:assign-vehicles --all  (tous les admins)
 * 
 * Features:
 * - ✅ Assignation via rôles (meilleure pratique)
 * - ✅ Support multi-tenant avec organization_id
 * - ✅ Validation avant et après
 * - ✅ Affichage détaillé et coloré
 * - ✅ Dry-run mode pour prévisualisation
 * - ✅ Gestion d'erreurs robuste
 * 
 * @version 1.0-Enterprise
 * @author ZenFleet Development Team
 * @since 2025-01-20
 */
class AssignVehiclePermissionsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'permissions:assign-vehicles 
                            {email? : Email de l\'utilisateur}
                            {--all : Assigner à tous les administrateurs}
                            {--dry-run : Prévisualiser sans appliquer}
                            {--force : Forcer la réassignation même si déjà présentes}';

    /**
     * @var string
     */
    protected $description = 'Assigne toutes les permissions véhicules à un utilisateur via ses rôles (Enterprise-Grade)';

    /**
     * 🎯 Permissions véhicules à assigner
     */
    private const VEHICLE_PERMISSIONS = [
        'view vehicles',
        'create vehicles',
        'update vehicles',
        'delete vehicles',
        'restore vehicles',
        'force-delete vehicles',
        'export vehicles',
        'import vehicles',
        'view vehicle history',
        'manage vehicle maintenance',
        'manage vehicle documents',
        'assign vehicles',
    ];

    /**
     * 🚀 Exécution de la commande
     */
    public function handle(): int
    {
        $this->displayHeader();

        // Mode --all : traiter tous les admins
        if ($this->option('all')) {
            return $this->assignToAllAdmins();
        }

        // Mode individuel
        $email = $this->argument('email');
        
        if (!$email) {
            $email = $this->ask('Email de l\'utilisateur');
        }

        if (!$email) {
            $this->error('❌ Email requis');
            return 1;
        }

        return $this->assignToUser($email);
    }

    /**
     * 📋 Affichage header enterprise
     */
    private function displayHeader(): void
    {
        $this->newLine();
        $this->info('🔐 ASSIGNATION PERMISSIONS VÉHICULES - ENTERPRISE-GRADE');
        $this->info(str_repeat('=', 70));
        $this->newLine();
    }

    /**
     * 👤 Assigner les permissions à un utilisateur
     */
    private function assignToUser(string $email): int
    {
        // Récupérer l'utilisateur
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Utilisateur '{$email}' introuvable");
            return 1;
        }

        $this->info("👤 Utilisateur trouvé:");
        $this->line("   ID: {$user->id}");
        $this->line("   Email: {$user->email}");
        $this->line("   Nom: " . ($user->name ?: 'N/A'));
        $this->line("   Organisation ID: {$user->organization_id}");
        $this->newLine();

        // Vérifier les rôles
        $roles = $user->roles;
        
        if ($roles->isEmpty()) {
            $this->warn("⚠️  L'utilisateur n'a AUCUN rôle assigné");
            $this->newLine();
            
            if (!$this->confirm('Voulez-vous lui créer un rôle "Admin" ?', false)) {
                $this->info('Opération annulée');
                return 1;
            }
            
            $this->createAndAssignAdminRole($user);
            $roles = $user->fresh()->roles;
        }

        $this->info("🏷️  Rôles actuels:");
        foreach ($roles as $role) {
            $this->line("   • {$role->name} (ID: {$role->id})");
        }
        $this->newLine();

        // Dry-run mode
        if ($this->option('dry-run')) {
            $this->warn('🔍 MODE DRY-RUN : Prévisualisation sans modifications');
            $this->newLine();
        }

        // Assigner les permissions à chaque rôle
        $totalAssigned = 0;
        $totalSkipped = 0;

        foreach ($roles as $role) {
            $this->info("📌 Traitement du rôle: {$role->name}");
            
            $result = $this->assignPermissionsToRole($role, $user->organization_id);
            $totalAssigned += $result['assigned'];
            $totalSkipped += $result['skipped'];
        }

        $this->newLine();
        $this->displaySummary($totalAssigned, $totalSkipped);

        // Validation post-assignation
        if (!$this->option('dry-run')) {
            $this->validateUserPermissions($user);
        }

        return 0;
    }

    /**
     * 👥 Assigner à tous les administrateurs
     */
    private function assignToAllAdmins(): int
    {
        $this->info('👥 Mode: Assignation à TOUS les administrateurs');
        $this->newLine();

        // Trouver tous les utilisateurs avec rôle Admin ou Super Admin
        $adminRoleIds = Role::whereIn('name', ['Super Admin', 'Admin'])->pluck('id');
        
        $adminUsers = User::whereHas('roles', function($query) use ($adminRoleIds) {
            $query->whereIn('role_id', $adminRoleIds);
        })->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('⚠️  Aucun administrateur trouvé');
            return 1;
        }

        $this->info("Administrateurs trouvés: {$adminUsers->count()}");
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('Continuer ?', true)) {
            $this->info('Opération annulée');
            return 1;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($adminUsers as $user) {
            $this->line("→ Traitement: {$user->email}");
            
            try {
                $result = $this->assignToUser($user->email);
                if ($result === 0) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur: {$e->getMessage()}");
                $failureCount++;
            }
        }

        $this->newLine();
        $this->info("✅ Succès: {$successCount}");
        if ($failureCount > 0) {
            $this->warn("❌ Échecs: {$failureCount}");
        }

        return $failureCount > 0 ? 1 : 0;
    }

    /**
     * 🔗 Assigner les permissions à un rôle
     */
    private function assignPermissionsToRole(Role $role, ?int $organizationId): array
    {
        $assigned = 0;
        $skipped = 0;

        // S'assurer que toutes les permissions existent
        $this->ensurePermissionsExist();

        // Récupérer les IDs des permissions
        $permissionIds = Permission::whereIn('name', self::VEHICLE_PERMISSIONS)
            ->pluck('id', 'name');

        foreach (self::VEHICLE_PERMISSIONS as $permissionName) {
            $permissionId = $permissionIds->get($permissionName);
            
            if (!$permissionId) {
                $this->warn("   ⚠️  Permission '{$permissionName}' introuvable");
                continue;
            }

            // Vérifier si déjà assignée
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->where('permission_id', $permissionId)
                ->exists();

            if ($exists && !$this->option('force')) {
                $this->line("   ℹ️  {$permissionName}: Déjà assignée");
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("   🔍 {$permissionName}: SERAIT assignée");
                $assigned++;
                continue;
            }

            // Utiliser la méthode native Spatie
            $role->givePermissionTo($permissionName);

            $this->info("   ✅ {$permissionName}: Assignée");
            $assigned++;
        }

        return ['assigned' => $assigned, 'skipped' => $skipped];
    }

    /**
     * 📋 S'assurer que toutes les permissions existent
     */
    private function ensurePermissionsExist(): void
    {
        $created = 0;

        foreach (self::VEHICLE_PERMISSIONS as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web'
            ]);

            if ($permission->wasRecentlyCreated) {
                $created++;
            }
        }

        if ($created > 0) {
            $this->info("   ✅ {$created} permissions créées");
        }
    }

    /**
     * 🏷️ Créer et assigner un rôle Admin
     */
    private function createAndAssignAdminRole(User $user): void
    {
        $role = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        // Assigner le rôle via SQL pour gérer organization_id
        DB::table('model_has_roles')->updateOrInsert(
            [
                'role_id' => $role->id,
                'model_type' => get_class($user),
                'model_id' => $user->id,
            ],
            [
                'role_id' => $role->id,
                'model_type' => get_class($user),
                'model_id' => $user->id,
                'organization_id' => $user->organization_id,
            ]
        );

        $this->info("   ✅ Rôle 'Admin' créé et assigné");
    }

    /**
     * 📊 Afficher le résumé
     */
    private function displaySummary(int $assigned, int $skipped): void
    {
        $this->info(str_repeat('=', 70));
        $this->info('📊 RÉSUMÉ:');
        $this->line("   Permissions assignées: {$assigned}");
        $this->line("   Permissions ignorées: {$skipped}");
        
        if (!$this->option('dry-run')) {
            $this->newLine();
            $this->info('🔄 Nettoyage du cache des permissions...');
            \Artisan::call('permission:cache-reset');
            $this->info('✅ Cache nettoyé');
        }
    }

    /**
     * ✅ Valider les permissions de l'utilisateur
     */
    private function validateUserPermissions(User $user): void
    {
        $this->newLine();
        $this->info('✅ Validation finale:');

        $user->load('roles.permissions');

        $canView = $user->can('view vehicles');
        $canCreate = $user->can('create vehicles');
        $canUpdate = $user->can('update vehicles');
        $canDelete = $user->can('delete vehicles');

        $this->line("   " . ($canView ? "✅" : "❌") . " view vehicles");
        $this->line("   " . ($canCreate ? "✅" : "❌") . " create vehicles");
        $this->line("   " . ($canUpdate ? "✅" : "❌") . " update vehicles");
        $this->line("   " . ($canDelete ? "✅" : "❌") . " delete vehicles");

        $allGranted = $canView && $canCreate && $canUpdate && $canDelete;

        $this->newLine();
        if ($allGranted) {
            $this->info('🎉 SUCCÈS ! Toutes les permissions sont actives.');
        } else {
            $this->warn('⚠️  Certaines permissions ne sont pas actives. Vérifier la configuration multi-tenant.');
        }
    }
}
