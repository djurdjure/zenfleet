#!/usr/bin/env php
<?php

/**
 * ====================================================================
 * 🧪 SCRIPT DE TEST - INTÉGRATION LIVEWIRE PHASE 3
 * ====================================================================
 * 
 * Vérifie que tous les composants Livewire sont correctement intégrés
 * - DriversImport component
 * - DriverSanctions component
 * - Routes configurées
 * - Vues wrapper créées
 * 
 * @version 1.0
 * @since 2025-01-19
 * ====================================================================
 */

echo "🧪 Test d'Intégration Livewire - Phase 3\n";
echo "==========================================\n\n";

// Charger Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$errors = [];
$warnings = [];
$success = [];

// ===============================================
// TEST 1: Vérifier que les composants Livewire existent
// ===============================================
echo "📦 Test 1: Composants Livewire\n";
echo "----------------------------\n";

$components = [
    'DriversImport' => 'app/Livewire/Admin/Drivers/DriversImport.php',
    'DriverSanctions' => 'app/Livewire/Admin/Drivers/DriverSanctions.php',
    'DriversTable' => 'app/Livewire/Admin/Drivers/DriversTable.php',
];

foreach ($components as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "  ✅ {$name}: OK\n";
        $success[] = "Composant {$name} existe";
    } else {
        echo "  ❌ {$name}: MANQUANT\n";
        $errors[] = "Composant {$name} manquant: {$path}";
    }
}
echo "\n";

// ===============================================
// TEST 2: Vérifier que les vues Livewire existent
// ===============================================
echo "📄 Test 2: Vues Livewire\n";
echo "----------------------------\n";

$views = [
    'drivers-import' => 'resources/views/livewire/admin/drivers/drivers-import.blade.php',
    'driver-sanctions' => 'resources/views/livewire/admin/drivers/driver-sanctions.blade.php',
    'drivers-table' => 'resources/views/livewire/admin/drivers/drivers-table.blade.php',
];

foreach ($views as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "  ✅ {$name}: OK\n";
        $success[] = "Vue {$name} existe";
    } else {
        echo "  ❌ {$name}: MANQUANT\n";
        $errors[] = "Vue {$name} manquante: {$path}";
    }
}
echo "\n";

// ===============================================
// TEST 3: Vérifier que les vues wrapper existent
// ===============================================
echo "🎁 Test 3: Vues Wrapper\n";
echo "----------------------------\n";

$wrappers = [
    'import-livewire' => 'resources/views/admin/drivers/import-livewire.blade.php',
    'sanctions-livewire' => 'resources/views/admin/drivers/sanctions-livewire.blade.php',
];

foreach ($wrappers as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "  ✅ {$name}: OK\n";
        $success[] = "Wrapper {$name} existe";
    } else {
        echo "  ❌ {$name}: MANQUANT\n";
        $errors[] = "Wrapper {$name} manquant: {$path}";
    }
}
echo "\n";

// ===============================================
// TEST 4: Vérifier les routes
// ===============================================
echo "🛣️  Test 4: Routes\n";
echo "----------------------------\n";

try {
    $routes = Route::getRoutes();
    
    $requiredRoutes = [
        'admin.drivers.import.show' => '/admin/drivers/import',
        'admin.drivers.sanctions.index' => '/admin/drivers/sanctions',
    ];
    
    foreach ($requiredRoutes as $routeName => $uri) {
        $route = $routes->getByName($routeName);
        if ($route) {
            echo "  ✅ {$routeName}: OK ({$route->uri()})\n";
            $success[] = "Route {$routeName} configurée";
        } else {
            echo "  ❌ {$routeName}: MANQUANT\n";
            $errors[] = "Route {$routeName} non trouvée";
        }
    }
} catch (Exception $e) {
    echo "  ⚠️  Erreur lors de la vérification des routes: " . $e->getMessage() . "\n";
    $warnings[] = "Erreur routes: " . $e->getMessage();
}
echo "\n";

// ===============================================
// TEST 5: Vérifier le modèle DriverSanction
// ===============================================
echo "📊 Test 5: Modèle DriverSanction\n";
echo "----------------------------\n";

try {
    $model = new App\Models\DriverSanction();
    $fillable = $model->getFillable();
    
    $requiredFields = ['driver_id', 'sanction_type', 'severity', 'reason', 'status', 'notes', 'supervisor_id'];
    
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!in_array($field, $fillable)) {
            $missingFields[] = $field;
        }
    }
    
    if (empty($missingFields)) {
        echo "  ✅ Tous les champs requis sont fillable\n";
        $success[] = "Modèle DriverSanction configuré correctement";
    } else {
        echo "  ⚠️  Champs manquants dans fillable: " . implode(', ', $missingFields) . "\n";
        $warnings[] = "Champs manquants dans fillable: " . implode(', ', $missingFields);
    }
    
} catch (Exception $e) {
    echo "  ❌ Erreur lors de la vérification du modèle: " . $e->getMessage() . "\n";
    $errors[] = "Erreur modèle: " . $e->getMessage();
}
echo "\n";

// ===============================================
// TEST 6: Vérifier la migration
// ===============================================
echo "🗄️  Test 6: Migration\n";
echo "----------------------------\n";

$migrationPath = 'database/migrations/2025_01_19_231500_add_enhanced_fields_to_driver_sanctions_table.php';
if (file_exists(__DIR__ . '/' . $migrationPath)) {
    echo "  ✅ Migration créée: OK\n";
    echo "  ⚠️  N'oubliez pas d'exécuter: php artisan migrate\n";
    $success[] = "Migration créée";
    $warnings[] = "Migration pas encore exécutée";
} else {
    echo "  ❌ Migration manquante\n";
    $errors[] = "Migration manquante: {$migrationPath}";
}
echo "\n";

// ===============================================
// RÉSUMÉ FINAL
// ===============================================
echo "\n";
echo "==========================================\n";
echo "📊 RÉSUMÉ DU TEST\n";
echo "==========================================\n\n";

echo "✅ Succès: " . count($success) . "\n";
echo "⚠️  Warnings: " . count($warnings) . "\n";
echo "❌ Erreurs: " . count($errors) . "\n\n";

if (!empty($errors)) {
    echo "🔴 ERREURS CRITIQUES:\n";
    foreach ($errors as $error) {
        echo "  • {$error}\n";
    }
    echo "\n";
}

if (!empty($warnings)) {
    echo "🟡 AVERTISSEMENTS:\n";
    foreach ($warnings as $warning) {
        echo "  • {$warning}\n";
    }
    echo "\n";
}

if (empty($errors)) {
    echo "🎉 TOUS LES TESTS CRITIQUES SONT PASSÉS !\n\n";
    echo "📝 PROCHAINES ÉTAPES:\n";
    echo "  1. Exécuter: php artisan migrate\n";
    echo "  2. Visiter: http://localhost/admin/drivers/import\n";
    echo "  3. Visiter: http://localhost/admin/drivers/sanctions\n";
    echo "  4. Tester l'importation de chauffeurs\n";
    echo "  5. Tester la création de sanctions\n\n";
} else {
    echo "❌ DES ERREURS ONT ÉTÉ DÉTECTÉES\n";
    echo "   Veuillez corriger les erreurs avant de continuer.\n\n";
    exit(1);
}

exit(0);
