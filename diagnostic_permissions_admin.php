<?php

/**
 * Script de diagnostic des permissions pour l'admin FADERCO
 * Usage: docker compose exec -u zenfleet_user php php diagnostic_permissions_admin.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DIAGNOSTIC DES PERMISSIONS ADMIN\n";
echo str_repeat("=", 70) . "\n\n";

// Récupérer l'admin
$admin = App\Models\User::where('email', 'admin@faderco.dz')->first();

if (!$admin) {
    echo "❌ Admin non trouvé\n";
    exit(1);
}

echo "✅ Admin trouvé: {$admin->email}\n";
echo "   Organisation: {$admin->organization->name}\n";
echo "   Rôles: " . $admin->getRoleNames()->implode(', ') . "\n\n";

// Lister toutes les permissions
echo "📋 PERMISSIONS DISPONIBLES:\n";
echo str_repeat("-", 70) . "\n";
$allPermissions = $admin->getAllPermissions();
foreach ($allPermissions as $perm) {
    echo "  ✓ {$perm->name}\n";
}
echo "\nTotal: {$allPermissions->count()} permissions\n\n";

// Tester les contrôleurs critiques
echo "🧪 TEST DES CONTRÔLEURS:\n";
echo str_repeat("-", 70) . "\n";

$controllersToTest = [
    'VehicleController' => [
        'class' => App\Http\Controllers\Admin\VehicleController::class,
        'permission' => 'view vehicles'
    ],
    'DriverController' => [
        'class' => App\Http\Controllers\Admin\DriverController::class,
        'permission' => 'view drivers'
    ],
    'SupplierController' => [
        'class' => App\Http\Controllers\Admin\SupplierController::class,
        'permission' => 'view suppliers'
    ],
    'AssignmentController' => [
        'class' => App\Http\Controllers\Admin\AssignmentController::class,
        'permission' => 'view assignments'
    ],
];

foreach ($controllersToTest as $name => $config) {
    echo "\n{$name}:\n";

    // Vérifier si la classe existe
    if (!class_exists($config['class'])) {
        echo "  ❌ Classe introuvable\n";
        continue;
    }

    // Vérifier la permission
    $hasPermission = $admin->can($config['permission']);
    echo "  Permission '{$config['permission']}': " . ($hasPermission ? "✅" : "❌") . "\n";

    // Vérifier le middleware via Reflection
    try {
        $reflection = new ReflectionClass($config['class']);
        $constructor = $reflection->getConstructor();

        if ($constructor) {
            $filePath = $reflection->getFileName();
            $fileContent = file_get_contents($filePath);

            // Chercher les middlewares role
            if (preg_match("/middleware\('role:([^']+)'\)/", $fileContent, $matches)) {
                $roles = $matches[1];
                echo "  Middleware role: {$roles}\n";

                $allowedRoles = array_map('trim', explode('|', $roles));
                $hasRole = false;
                foreach ($allowedRoles as $role) {
                    if ($admin->hasRole($role)) {
                        $hasRole = true;
                        break;
                    }
                }
                echo "  Accès role: " . ($hasRole ? "✅" : "❌") . "\n";
            } else {
                echo "  Middleware role: Aucun (OK)\n";
            }
        }
    } catch (Exception $e) {
        echo "  ⚠️  Erreur analyse: {$e->getMessage()}\n";
    }
}

// Vérifier les routes
echo "\n\n🛣️  TEST DES ROUTES:\n";
echo str_repeat("-", 70) . "\n";

$routesToTest = [
    'admin.vehicles.index' => '/admin/vehicles',
    'admin.drivers.index' => '/admin/drivers',
    'admin.suppliers.index' => '/admin/suppliers',
    'admin.assignments.index' => '/admin/assignments',
];

foreach ($routesToTest as $routeName => $path) {
    echo "\n{$routeName} ({$path}):\n";

    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            echo "  ✅ Route existe\n";

            // Vérifier les middlewares
            $middlewares = $route->gatherMiddleware();
            echo "  Middlewares: " . implode(', ', $middlewares) . "\n";
        } else {
            echo "  ❌ Route introuvable\n";
        }
    } catch (Exception $e) {
        echo "  ⚠️  Erreur: {$e->getMessage()}\n";
    }
}

echo "\n\n" . str_repeat("=", 70) . "\n";
echo "📊 RÉSUMÉ:\n";
echo str_repeat("=", 70) . "\n";
echo "Si vous voyez des ❌ dans 'Accès role', c'est le problème!\n";
echo "Les contrôleurs doivent accepter le rôle 'Admin' et pas seulement 'Super Admin'.\n\n";
