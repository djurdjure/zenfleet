#!/usr/bin/env php
<?php

/**
 * 🔍 SCRIPT DE VALIDATION - Corrections Appliquées
 *
 * Valide que toutes les corrections ont été appliquées correctement
 * Compatible Docker et CLI standard
 *
 * @version 2.0-Enterprise
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 VALIDATION DES CORRECTIONS - ENTERPRISE v2.0           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$success = [];

// ============================================================
// TEST 1: VehicleController - Permissions d'import
// ============================================================
echo "📝 Test 1: Vérification des permissions d'import de véhicules...\n";

$vehicleControllerPath = __DIR__ . '/app/Http/Controllers/Admin/VehicleController.php';
$vehicleControllerContent = file_get_contents($vehicleControllerPath);

// Vérifier qu'il n'y a plus de 'import_vehicles'
$importVehiclesCount = substr_count($vehicleControllerContent, "authorize('import_vehicles')");

if ($importVehiclesCount > 0) {
    $errors[] = "❌ VehicleController contient encore {$importVehiclesCount} occurrence(s) de 'import_vehicles'";
} else {
    $success[] = "✅ Toutes les autorisations utilisent 'create vehicles'";
}

// Vérifier qu'il y a bien 'create vehicles'
$createVehiclesCount = substr_count($vehicleControllerContent, "authorize('create vehicles')");

if ($createVehiclesCount >= 5) {
    $success[] = "✅ {$createVehiclesCount} méthodes d'import utilisent correctement 'create vehicles'";
} else {
    $warnings[] = "⚠️  Seulement {$createVehiclesCount} autorisations 'create vehicles' trouvées (attendu: 5+)";
}

// ============================================================
// TEST 2: DriverStatusSeeder - Statuts complets
// ============================================================
echo "📝 Test 2: Vérification du seeder de statuts chauffeurs...\n";

$seederPath = __DIR__ . '/database/seeders/DriverStatusSeeder.php';
$seederContent = file_get_contents($seederPath);

// Vérifier la présence des champs essentiels
$requiredFields = ['color', 'icon', 'description', 'can_drive', 'can_assign', 'sort_order'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (strpos($seederContent, "'{$field}'") === false) {
        $missingFields[] = $field;
    }
}

if (empty($missingFields)) {
    $success[] = "✅ Tous les champs requis sont présents dans le seeder";
} else {
    $errors[] = "❌ Champs manquants dans le seeder: " . implode(', ', $missingFields);
}

// Vérifier le nombre de statuts
$statusCount = substr_count($seederContent, "'name' =>");
if ($statusCount >= 8) {
    $success[] = "✅ {$statusCount} statuts définis dans le seeder";
} else {
    $warnings[] = "⚠️  Seulement {$statusCount} statuts définis (recommandé: 8+)";
}

// ============================================================
// TEST 3: Base de données - Statuts créés
// ============================================================
echo "📝 Test 3: Vérification des statuts en base de données...\n";

try {
    $statusesInDb = \App\Models\DriverStatus::count();

    if ($statusesInDb >= 8) {
        $success[] = "✅ {$statusesInDb} statuts présents en base de données";

        // Vérifier les champs
        $statusesWithColor = \App\Models\DriverStatus::whereNotNull('color')->count();
        $statusesWithIcon = \App\Models\DriverStatus::whereNotNull('icon')->count();

        if ($statusesWithColor === $statusesInDb) {
            $success[] = "✅ Tous les statuts ont une couleur définie";
        } else {
            $warnings[] = "⚠️  {$statusesWithColor}/{$statusesInDb} statuts ont une couleur";
        }

        if ($statusesWithIcon === $statusesInDb) {
            $success[] = "✅ Tous les statuts ont une icône définie";
        } else {
            $warnings[] = "⚠️  {$statusesWithIcon}/{$statusesInDb} statuts ont une icône";
        }

    } else {
        $warnings[] = "⚠️  Seulement {$statusesInDb} statuts en base (recommandé: 8+)";
        $warnings[] = "💡 Exécutez: php fix_driver_statuses.php";
    }
} catch (\Exception $e) {
    $errors[] = "❌ Erreur de connexion à la base de données: " . $e->getMessage();
}

// ============================================================
// TEST 4: EnterprisePermissionMiddleware - Mapping
// ============================================================
echo "📝 Test 4: Vérification du middleware de permissions...\n";

$middlewarePath = __DIR__ . '/app/Http/Middleware/EnterprisePermissionMiddleware.php';
$middlewareContent = file_get_contents($middlewarePath);

// Vérifier le mapping des routes d'import
if (strpos($middlewareContent, "'admin.vehicles.import.*' => 'create vehicles'") !== false) {
    $success[] = "✅ Mapping des routes d'import véhicules correct";
} else {
    $warnings[] = "⚠️  Vérifier le mapping 'admin.vehicles.import.*' dans le middleware";
}

if (strpos($middlewareContent, "'admin.drivers.import.*' => 'create drivers'") !== false) {
    $success[] = "✅ Mapping des routes d'import chauffeurs correct";
} else {
    $warnings[] = "⚠️  Vérifier le mapping 'admin.drivers.import.*' dans le middleware";
}

// ============================================================
// TEST 5: Permissions utilisateur admin
// ============================================================
echo "📝 Test 5: Vérification des permissions de l'utilisateur admin...\n";

try {
    $admin = \App\Models\User::where('email', 'admin@faderco.dz')->first();

    if ($admin) {
        $hasCreateVehicles = $admin->can('create vehicles');
        $hasCreateDrivers = $admin->can('create drivers');

        if ($hasCreateVehicles) {
            $success[] = "✅ L'admin a la permission 'create vehicles'";
        } else {
            $errors[] = "❌ L'admin n'a PAS la permission 'create vehicles'";
            $errors[] = "💡 Solution: Assigner la permission via le panneau d'administration";
        }

        if ($hasCreateDrivers) {
            $success[] = "✅ L'admin a la permission 'create drivers'";
        } else {
            $warnings[] = "⚠️  L'admin n'a pas la permission 'create drivers'";
        }

        // Afficher toutes les permissions
        $allPermissions = $admin->getAllPermissions()->pluck('name')->toArray();
        echo "   📋 Permissions de l'admin: " . count($allPermissions) . " permissions\n";

    } else {
        $warnings[] = "⚠️  Utilisateur admin@faderco.dz non trouvé";
    }
} catch (\Exception $e) {
    $errors[] = "❌ Erreur lors de la vérification des permissions: " . $e->getMessage();
}

// ============================================================
// RAPPORT FINAL
// ============================================================
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RAPPORT DE VALIDATION\n";
echo str_repeat("=", 60) . "\n\n";

if (!empty($success)) {
    echo "✅ SUCCÈS (" . count($success) . "):\n";
    foreach ($success as $msg) {
        echo "   {$msg}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "⚠️  AVERTISSEMENTS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "   {$msg}\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "❌ ERREURS (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "   {$msg}\n";
    }
    echo "\n";
}

// ============================================================
// CONCLUSION
// ============================================================
if (empty($errors) && empty($warnings)) {
    echo "🎉 TOUTES LES VALIDATIONS SONT RÉUSSIES!\n";
    echo "   Vous pouvez maintenant tester l'application.\n";
    exit(0);
} elseif (empty($errors)) {
    echo "✅ Corrections appliquées avec succès (quelques avertissements mineurs)\n";
    echo "   L'application devrait fonctionner correctement.\n";
    exit(0);
} else {
    echo "❌ Des erreurs critiques ont été détectées\n";
    echo "   Veuillez corriger ces problèmes avant de continuer.\n";
    exit(1);
}
