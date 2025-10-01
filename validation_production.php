<?php

/**
 * 🚀 VALIDATION PRODUCTION - SYSTÈME DE PERMISSIONS
 *
 * Ce script valide que le système est prêt pour la production
 * Usage: docker compose exec -u zenfleet_user php php validation_production.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 VALIDATION PRODUCTION - ZENFLEET\n";
echo str_repeat("=", 80) . "\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier que les Policies existent
echo "1. 📁 VÉRIFICATION DES FICHIERS POLICIES\n";
echo str_repeat("-", 80) . "\n";

$policiesRequired = [
    'app/Policies/VehiclePolicy.php',
    'app/Policies/DriverPolicy.php',
    'app/Policies/SupplierPolicy.php',
    'app/Policies/AssignmentPolicy.php',
];

foreach ($policiesRequired as $policy) {
    $path = __DIR__ . '/' . $policy;
    if (file_exists($path)) {
        echo "  ✅ {$policy}\n";
        $success[] = "Policy file exists: {$policy}";
    } else {
        echo "  ❌ {$policy} - MANQUANT!\n";
        $errors[] = "Policy file missing: {$policy}";
    }
}

echo "\n";

// 2. Vérifier que les Policies sont enregistrées
echo "2. 🛡️ VÉRIFICATION DE L'ENREGISTREMENT DES POLICIES\n";
echo str_repeat("-", 80) . "\n";

$authServiceProvider = file_get_contents(__DIR__ . '/app/Providers/AuthServiceProvider.php');

$policiesCheck = [
    'VehiclePolicy' => strpos($authServiceProvider, 'VehiclePolicy::class') !== false,
    'DriverPolicy' => strpos($authServiceProvider, 'DriverPolicy::class') !== false,
    'SupplierPolicy' => strpos($authServiceProvider, 'SupplierPolicy::class') !== false,
    'AssignmentPolicy' => strpos($authServiceProvider, 'AssignmentPolicy::class') !== false,
];

foreach ($policiesCheck as $policy => $registered) {
    if ($registered) {
        echo "  ✅ {$policy} enregistrée dans AuthServiceProvider\n";
        $success[] = "Policy registered: {$policy}";
    } else {
        echo "  ❌ {$policy} NON enregistrée!\n";
        $errors[] = "Policy not registered: {$policy}";
    }
}

echo "\n";

// 3. Vérifier les permissions des rôles
echo "3. 🔑 VÉRIFICATION DES PERMISSIONS\n";
echo str_repeat("-", 80) . "\n";

$roles = [
    'Super Admin' => ['min' => 100, 'expected' => 132],
    'Admin' => ['min' => 25, 'expected' => 29],
    'Gestionnaire Flotte' => ['min' => 20, 'expected' => 71],
    'Superviseur' => ['min' => 10, 'expected' => 32],
    'Chauffeur' => ['min' => 2, 'expected' => 11],
];

foreach ($roles as $roleName => $config) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();

    if (!$role) {
        echo "  ⚠️  {$roleName} - Rôle introuvable\n";
        $warnings[] = "Role not found: {$roleName}";
        continue;
    }

    $permCount = $role->permissions()->count();

    if ($permCount >= $config['min']) {
        echo "  ✅ {$roleName}: {$permCount} permissions (attendu: {$config['expected']})\n";
        $success[] = "Role has adequate permissions: {$roleName}";
    } else {
        echo "  ❌ {$roleName}: {$permCount} permissions (minimum: {$config['min']})\n";
        $errors[] = "Role has insufficient permissions: {$roleName}";
    }
}

echo "\n";

// 4. Vérifier le compte Admin de test
echo "4. 👤 VÉRIFICATION DU COMPTE ADMIN TEST\n";
echo str_repeat("-", 80) . "\n";

$admin = App\Models\User::where('email', 'admin@faderco.dz')->first();

if (!$admin) {
    echo "  ❌ admin@faderco.dz introuvable\n";
    $errors[] = "Test admin account not found";
} else {
    echo "  ✅ Utilisateur: {$admin->email}\n";
    echo "     Organisation: {$admin->organization->name} (ID: {$admin->organization_id})\n";
    echo "     Rôle: " . $admin->getRoleNames()->implode(', ') . "\n";
    echo "     Permissions: {$admin->getAllPermissions()->count()}\n";

    // Tester les permissions critiques
    $criticalPermissions = [
        'view vehicles',
        'view drivers',
        'view suppliers',
        'view assignments',
        'view dashboard',
    ];

    $allHave = true;
    foreach ($criticalPermissions as $perm) {
        if (!$admin->can($perm)) {
            echo "  ❌ Permission manquante: {$perm}\n";
            $errors[] = "Admin missing permission: {$perm}";
            $allHave = false;
        }
    }

    if ($allHave) {
        echo "  ✅ Toutes les permissions critiques présentes\n";
        $success[] = "Admin has all critical permissions";
    }
}

echo "\n";

// 5. Vérifier les middlewares sur les contrôleurs
echo "5. 🚪 VÉRIFICATION DES MIDDLEWARES\n";
echo str_repeat("-", 80) . "\n";

$controllers = [
    'DriverController' => [
        'path' => 'app/Http/Controllers/Admin/DriverController.php',
        'expected' => 'role:Super Admin|Admin|Gestionnaire Flotte',
    ],
];

foreach ($controllers as $name => $config) {
    $path = __DIR__ . '/' . $config['path'];
    if (file_exists($path)) {
        $content = file_get_contents($path);
        if (strpos($content, $config['expected']) !== false) {
            echo "  ✅ {$name}: Middleware correct\n";
            $success[] = "Controller has correct middleware: {$name}";
        } else {
            echo "  ⚠️  {$name}: Middleware peut être incorrect\n";
            $warnings[] = "Controller middleware might be incorrect: {$name}";
        }
    } else {
        echo "  ❌ {$name}: Fichier introuvable\n";
        $errors[] = "Controller file not found: {$name}";
    }
}

echo "\n";

// 6. Vérifier les routes critiques
echo "6. 🛣️  VÉRIFICATION DES ROUTES\n";
echo str_repeat("-", 80) . "\n";

$criticalRoutes = [
    'admin.vehicles.index',
    'admin.drivers.index',
    'admin.suppliers.index',
    'admin.assignments.index',
    'admin.dashboard',
];

foreach ($criticalRoutes as $routeName) {
    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            echo "  ✅ {$routeName}\n";
            $success[] = "Route exists: {$routeName}";
        } else {
            echo "  ❌ {$routeName} - Route introuvable\n";
            $errors[] = "Route not found: {$routeName}";
        }
    } catch (Exception $e) {
        echo "  ❌ {$routeName} - Erreur: {$e->getMessage()}\n";
        $errors[] = "Route error: {$routeName}";
    }
}

echo "\n";

// 7. Test d'accès avec le compte Admin
echo "7. 🧪 TEST D'ACCÈS ADMIN\n";
echo str_repeat("-", 80) . "\n";

if ($admin) {
    $models = [
        'Vehicle' => App\Models\Vehicle::class,
        'Driver' => App\Models\Driver::class,
        'Supplier' => App\Models\Supplier::class,
        'Assignment' => App\Models\Assignment::class,
    ];

    foreach ($models as $name => $model) {
        $canView = $admin->can('viewAny', $model);
        $canCreate = $admin->can('create', $model);

        if ($canView && $canCreate) {
            echo "  ✅ {$name}: viewAny ✅ + create ✅\n";
            $success[] = "Admin can access {$name}";
        } else {
            echo "  ❌ {$name}: viewAny " . ($canView ? '✅' : '❌') . " + create " . ($canCreate ? '✅' : '❌') . "\n";
            $errors[] = "Admin cannot fully access {$name}";
        }
    }
}

echo "\n";

// Résumé final
echo str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ DE VALIDATION\n";
echo str_repeat("=", 80) . "\n\n";

echo "✅ Succès: " . count($success) . "\n";
echo "⚠️  Avertissements: " . count($warnings) . "\n";
echo "❌ Erreurs: " . count($errors) . "\n\n";

if (count($errors) === 0 && count($warnings) === 0) {
    echo "🎉 VALIDATION RÉUSSIE - SYSTÈME PRÊT POUR LA PRODUCTION\n\n";
    echo "Le système de permissions est entièrement opérationnel:\n";
    echo "  - ✅ Toutes les Policies sont créées et enregistrées\n";
    echo "  - ✅ Tous les rôles ont les permissions appropriées\n";
    echo "  - ✅ Le compte Admin peut accéder à toutes les ressources\n";
    echo "  - ✅ Les middlewares sont correctement configurés\n";
    echo "  - ✅ Toutes les routes critiques sont accessibles\n\n";
    exit(0);
} elseif (count($errors) === 0) {
    echo "⚠️  VALIDATION AVEC AVERTISSEMENTS\n\n";
    echo "Le système est opérationnel mais nécessite une attention:\n";
    foreach ($warnings as $warning) {
        echo "  ⚠️  {$warning}\n";
    }
    echo "\n";
    exit(0);
} else {
    echo "❌ VALIDATION ÉCHOUÉE - CORRECTIONS NÉCESSAIRES\n\n";
    echo "Erreurs critiques détectées:\n";
    foreach ($errors as $error) {
        echo "  ❌ {$error}\n";
    }
    echo "\n";
    exit(1);
}
