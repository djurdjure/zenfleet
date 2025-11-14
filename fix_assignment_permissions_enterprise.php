<?php

/**
 * ⚡ FIX ENTERPRISE PERMISSIONS POUR LES AFFECTATIONS
 * 
 * Ce script corrige et optimise les permissions du module d'affectations
 * selon les standards entreprise-grade.
 *
 * @author ZenFleet Architecture Team
 * @version 2.0.0
 */

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🚀 FIX ENTERPRISE PERMISSIONS - MODULE AFFECTATIONS                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";

// Début de transaction pour rollback en cas d'erreur
DB::beginTransaction();

try {
    // 1️⃣ DÉFINIR LA MATRICE COMPLÈTE DES PERMISSIONS ENTERPRISE
    $permissionMatrix = [
        // Permissions de base CRUD
        'assignments.view' => [
            'display_name' => 'Voir les affectations',
            'description' => 'Permet de consulter la liste et le détail des affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.create' => [
            'display_name' => 'Créer des affectations',
            'description' => 'Permet de créer de nouvelles affectations de véhicules',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.edit' => [
            'display_name' => 'Modifier les affectations',
            'description' => 'Permet de modifier les affectations existantes',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.delete' => [
            'display_name' => 'Supprimer les affectations',
            'description' => 'Permet de supprimer les affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        
        // Permissions avancées
        'assignments.end' => [
            'display_name' => 'Terminer les affectations',
            'description' => 'Permet de terminer une affectation en cours',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.extend' => [
            'display_name' => 'Prolonger les affectations',
            'description' => 'Permet d\'étendre la durée d\'une affectation',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.export' => [
            'display_name' => 'Exporter les affectations',
            'description' => 'Permet d\'exporter les données d\'affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        
        // Permissions de visualisation avancée
        'assignments.view.calendar' => [
            'display_name' => 'Voir le calendrier des affectations',
            'description' => 'Accès à la vue calendrier des affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.view.gantt' => [
            'display_name' => 'Voir le diagramme Gantt',
            'description' => 'Accès à la vue Gantt des affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.view.statistics' => [
            'display_name' => 'Voir les statistiques',
            'description' => 'Accès aux statistiques et analytics des affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.view.conflicts' => [
            'display_name' => 'Voir les conflits',
            'description' => 'Voir les conflits et chevauchements d\'affectations',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        
        // Permissions batch/bulk
        'assignments.bulk.create' => [
            'display_name' => 'Créer des affectations en lot',
            'description' => 'Créer plusieurs affectations simultanément',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.bulk.update' => [
            'display_name' => 'Modifier des affectations en lot',
            'description' => 'Modifier plusieurs affectations simultanément',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.bulk.delete' => [
            'display_name' => 'Supprimer des affectations en lot',
            'description' => 'Supprimer plusieurs affectations simultanément',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        
        // Permissions spéciales
        'assignments.force-delete' => [
            'display_name' => 'Suppression forcée',
            'description' => 'Supprimer définitivement les affectations (sans soft delete)',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.restore' => [
            'display_name' => 'Restaurer les affectations',
            'description' => 'Restaurer les affectations supprimées',
            'category' => 'assignments',
            'module' => 'fleet'
        ],
        'assignments.manage-all' => [
            'display_name' => 'Gérer toutes les affectations',
            'description' => 'Accès complet à toutes les affectations de l\'organisation',
            'category' => 'assignments',
            'module' => 'fleet'
        ]
    ];
    
    // Mapping des anciennes permissions vers les nouvelles
    $permissionMapping = [
        'view assignments' => 'assignments.view',
        'create assignments' => 'assignments.create',
        'edit assignments' => 'assignments.edit',
        'delete assignments' => 'assignments.delete',
        'end assignments' => 'assignments.end',
        'extend assignments' => 'assignments.extend',
        'export assignments' => 'assignments.export',
        'view assignment calendar' => 'assignments.view.calendar',
        'view assignment gantt' => 'assignments.view.gantt',
        'view assignment statistics' => 'assignments.view.statistics'
    ];
    
    echo "\n📦 CRÉATION/MISE À JOUR DES PERMISSIONS\n";
    echo str_repeat("─", 70) . "\n";
    
    // Créer ou mettre à jour les nouvelles permissions
    foreach ($permissionMatrix as $name => $config) {
        $permission = Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            $config
        );
        
        // Mettre à jour les metadata si la permission existait déjà
        if (!$permission->wasRecentlyCreated) {
            $permission->update($config);
            echo "  ♻️  Mise à jour: {$name}\n";
        } else {
            echo "  ✅ Créée: {$name}\n";
        }
    }
    
    // Garder les anciennes permissions pour compatibilité
    echo "\n📋 MAINTIEN DES PERMISSIONS LEGACY (compatibilité)\n";
    echo str_repeat("─", 70) . "\n";
    
    foreach ($permissionMapping as $oldName => $newName) {
        $oldPerm = Permission::firstOrCreate(
            ['name' => $oldName, 'guard_name' => 'web']
        );
        echo "  ✅ Legacy: {$oldName} -> {$newName}\n";
    }
    
    // 2️⃣ MATRICE DES RÔLES ET PERMISSIONS
    $rolePermissions = [
        'Super Admin' => [
            // Toutes les permissions du module
            'assignments.view', 'assignments.create', 'assignments.edit', 'assignments.delete',
            'assignments.end', 'assignments.extend', 'assignments.export',
            'assignments.view.calendar', 'assignments.view.gantt', 'assignments.view.statistics',
            'assignments.view.conflicts', 'assignments.bulk.create', 'assignments.bulk.update',
            'assignments.bulk.delete', 'assignments.force-delete', 'assignments.restore',
            'assignments.manage-all',
            // Legacy
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'end assignments', 'extend assignments', 'export assignments',
            'view assignment calendar', 'view assignment gantt', 'view assignment statistics'
        ],
        
        'Admin' => [
            // Permissions standard admin
            'assignments.view', 'assignments.create', 'assignments.edit', 'assignments.delete',
            'assignments.end', 'assignments.extend', 'assignments.export',
            'assignments.view.calendar', 'assignments.view.gantt', 'assignments.view.statistics',
            'assignments.view.conflicts', 'assignments.bulk.create', 'assignments.bulk.update',
            'assignments.restore',
            // Legacy
            'view assignments', 'create assignments', 'edit assignments', 'delete assignments',
            'end assignments', 'extend assignments', 'export assignments',
            'view assignment calendar', 'view assignment gantt', 'view assignment statistics'
        ],
        
        'Gestionnaire Flotte' => [
            'assignments.view', 'assignments.create', 'assignments.edit',
            'assignments.end', 'assignments.extend', 'assignments.export',
            'assignments.view.calendar', 'assignments.view.gantt', 'assignments.view.statistics',
            'assignments.view.conflicts', 'assignments.bulk.create',
            // Legacy
            'view assignments', 'create assignments', 'edit assignments',
            'end assignments', 'extend assignments', 'export assignments',
            'view assignment calendar', 'view assignment gantt'
        ],
        
        'Superviseur' => [
            'assignments.view', 'assignments.create', 'assignments.edit',
            'assignments.end', 'assignments.view.calendar',
            // Legacy
            'view assignments', 'create assignments', 'edit assignments',
            'end assignments', 'view assignment calendar'
        ],
        
        'Comptable' => [
            'assignments.view', 'assignments.export',
            'assignments.view.statistics',
            // Legacy
            'view assignments', 'export assignments'
        ],
        
        'Analyste' => [
            'assignments.view', 'assignments.export',
            'assignments.view.statistics', 'assignments.view.gantt',
            // Legacy
            'view assignments', 'export assignments'
        ],
        
        'Chauffeur' => [
            'assignments.view',
            // Legacy
            'view assignments'
        ]
    ];
    
    echo "\n👥 ATTRIBUTION DES PERMISSIONS AUX RÔLES\n";
    echo str_repeat("─", 70) . "\n";
    
    foreach ($rolePermissions as $roleName => $permissions) {
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        
        // Récupérer toutes les permissions existantes du rôle
        $existingPermissions = $role->permissions->pluck('name')->toArray();
        
        // Ajouter les nouvelles permissions sans supprimer les anciennes
        $allPermissions = array_unique(array_merge($existingPermissions, $permissions));
        
        // Filtrer uniquement les permissions qui existent
        $validPermissions = Permission::whereIn('name', $allPermissions)->pluck('name')->toArray();
        
        // Synchroniser les permissions
        $role->syncPermissions($validPermissions);
        
        $assignmentPermsCount = collect($validPermissions)->filter(function($p) {
            return str_contains($p, 'assignment');
        })->count();
        
        echo "  ✅ {$roleName}: {$assignmentPermsCount} permissions affectations\n";
    }
    
    // 3️⃣ FIX SPÉCIFIQUE POUR L'ADMIN PRINCIPAL
    echo "\n🔧 FIX ADMIN PRINCIPAL\n";
    echo str_repeat("─", 70) . "\n";
    
    $adminUser = User::whereEmail('admin@zenfleet.dz')
        ->orWhere('email', 'admin@zenfleet.com')
        ->first();
    
    if ($adminUser) {
        // S'assurer que l'admin a le rôle Admin
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
            echo "  ✅ Rôle Admin assigné à {$adminUser->email}\n";
        }
        
        // Vérifier les permissions critiques
        $criticalPerms = [
            'assignments.create', 'create assignments',
            'assignments.end', 'end assignments',
            'assignments.view.statistics', 'view assignment statistics'
        ];
        
        foreach ($criticalPerms as $perm) {
            if (!$adminUser->can($perm)) {
                echo "  ⚠️  Permission manquante détectée: {$perm}\n";
                $adminUser->givePermissionTo($perm);
                echo "  ✅ Permission ajoutée: {$perm}\n";
            }
        }
    }
    
    // 4️⃣ NETTOYER LE CACHE DES PERMISSIONS
    echo "\n🧹 NETTOYAGE DU CACHE\n";
    echo str_repeat("─", 70) . "\n";
    
    Cache::forget('spatie.permission.cache');
    Cache::forget('spatie.role.cache');
    app()['cache']->forget('spatie.permission.cache');
    app()['cache']->forget('spatie.role.cache');
    
    echo "  ✅ Cache des permissions nettoyé\n";
    
    // 5️⃣ VÉRIFICATION FINALE
    echo "\n✅ VÉRIFICATION FINALE\n";
    echo str_repeat("─", 70) . "\n";
    
    if ($adminUser) {
        $adminUser->refresh();
        
        $testPerms = [
            'view assignments',
            'create assignments',
            'edit assignments',
            'end assignments',
            'delete assignments'
        ];
        
        echo "  Permissions de {$adminUser->email}:\n";
        foreach ($testPerms as $perm) {
            $hasIt = $adminUser->can($perm);
            $icon = $hasIt ? '✅' : '❌';
            echo "    {$icon} {$perm}: " . ($hasIt ? 'OUI' : 'NON') . "\n";
        }
    }
    
    // Commit de la transaction
    DB::commit();
    
    echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
    echo "║   ✅ FIX TERMINÉ AVEC SUCCÈS !                                       ║\n";
    echo "║                                                                       ║\n";
    echo "║   L'utilisateur admin peut maintenant:                               ║\n";
    echo "║   • Créer des affectations                                           ║\n";
    echo "║   • Terminer des affectations                                        ║\n";
    echo "║   • Voir les statistiques                                            ║\n";
    echo "║   • Gérer toutes les fonctionnalités du module                       ║\n";
    echo "╚═══════════════════════════════════════════════════════════════════════╝\n";
    
    // Log de l'opération pour audit
    Log::info('Permissions affectations mises à jour avec succès', [
        'user_id' => $adminUser?->id,
        'permissions_created' => count($permissionMatrix),
        'roles_updated' => count($rolePermissions)
    ]);
    
} catch (\Exception $e) {
    DB::rollback();
    
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "   Rollback effectué, aucune modification appliquée.\n";
    
    Log::error('Erreur lors de la mise à jour des permissions', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    exit(1);
}

echo "\n";
