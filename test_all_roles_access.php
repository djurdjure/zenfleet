<?php

/**
 * Script de test complet pour vérifier l'accès de chaque rôle
 * Usage: docker compose exec -u zenfleet_user php php test_all_roles_access.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST COMPLET DES ACCÈS PAR RÔLE\n";
echo str_repeat("=", 80) . "\n\n";

$roles = ['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Superviseur', 'Chauffeur'];

$pagesACritiques = [
    'Véhicules' => 'view vehicles',
    'Chauffeurs' => 'view drivers',
    'Affectations' => 'view assignments',
    'Fournisseurs' => 'view suppliers',
    'Tableau de bord' => 'view dashboard',
    'Rapports' => 'view reports',
    'Utilisateurs' => 'view users',
];

foreach ($roles as $roleName) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();

    if (!$role) {
        echo "❌ Rôle '{$roleName}' introuvable\n\n";
        continue;
    }

    echo "👤 RÔLE: {$roleName}\n";
    echo str_repeat("-", 80) . "\n";
    echo "Total permissions: {$role->permissions->count()}\n";
    echo "Accès aux pages:\n";

    foreach ($pagesACritiques as $page => $permission) {
        $hasAccess = $role->hasPermissionTo($permission);
        $status = $hasAccess ? '✅' : '❌';
        echo "  {$status} {$page} ({$permission})\n";
    }

    echo "\nPermissions spécifiques:\n";
    $groupedPerms = [
        'Création' => [],
        'Modification' => [],
        'Suppression' => [],
        'Import/Export' => [],
        'Autres' => [],
    ];

    foreach ($role->permissions as $perm) {
        if (str_starts_with($perm->name, 'create')) {
            $groupedPerms['Création'][] = $perm->name;
        } elseif (str_starts_with($perm->name, 'edit')) {
            $groupedPerms['Modification'][] = $perm->name;
        } elseif (str_starts_with($perm->name, 'delete')) {
            $groupedPerms['Suppression'][] = $perm->name;
        } elseif (str_contains($perm->name, 'import') || str_contains($perm->name, 'export')) {
            $groupedPerms['Import/Export'][] = $perm->name;
        } else {
            $groupedPerms['Autres'][] = $perm->name;
        }
    }

    foreach ($groupedPerms as $category => $perms) {
        if (!empty($perms)) {
            echo "  {$category}: " . count($perms) . " permission(s)\n";
        }
    }

    echo "\n" . str_repeat("=", 80) . "\n\n";
}

// Test avec un utilisateur réel
echo "🧪 TEST AVEC UTILISATEUR RÉEL: admin@faderco.dz\n";
echo str_repeat("=", 80) . "\n";

$admin = App\Models\User::where('email', 'admin@faderco.dz')->first();

if ($admin) {
    echo "✅ Utilisateur trouvé: {$admin->email}\n";
    echo "   Organisation: {$admin->organization->name}\n";
    echo "   Rôles: " . $admin->getRoleNames()->implode(', ') . "\n\n";

    echo "Test d'accès aux pages critiques:\n";
    foreach ($pagesACritiques as $page => $permission) {
        $canAccess = $admin->can($permission);
        $status = $canAccess ? '✅' : '❌';
        echo "  {$status} {$page}\n";
    }

    echo "\n📋 Routes accessibles:\n";
    $routes = [
        '/admin/vehicles' => 'admin.vehicles.index',
        '/admin/drivers' => 'admin.drivers.index',
        '/admin/assignments' => 'admin.assignments.index',
        '/admin/suppliers' => 'admin.suppliers.index',
        '/admin/dashboard' => 'admin.dashboard',
    ];

    foreach ($routes as $path => $routeName) {
        try {
            $route = Route::getRoutes()->getByName($routeName);
            if ($route) {
                $middlewares = $route->gatherMiddleware();
                $hasRoleMiddleware = false;
                $requiredRoles = [];

                foreach ($middlewares as $middleware) {
                    if (str_starts_with($middleware, 'role:')) {
                        $hasRoleMiddleware = true;
                        $requiredRoles = explode('|', str_replace('role:', '', $middleware));
                        break;
                    }
                }

                if ($hasRoleMiddleware) {
                    $hasRole = false;
                    foreach ($requiredRoles as $role) {
                        if ($admin->hasRole($role)) {
                            $hasRole = true;
                            break;
                        }
                    }
                    $status = $hasRole ? '✅' : '❌';
                    echo "  {$status} {$path} (requis: " . implode('|', $requiredRoles) . ")\n";
                } else {
                    echo "  ✅ {$path} (pas de restriction de rôle)\n";
                }
            }
        } catch (Exception $e) {
            echo "  ⚠️  {$path} - Erreur: {$e->getMessage()}\n";
        }
    }
} else {
    echo "❌ Utilisateur admin@faderco.dz introuvable\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "✨ TEST TERMINÉ\n\n";
