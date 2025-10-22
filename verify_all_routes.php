#!/usr/bin/env php
<?php

/**
 * ====================================================================
 * 🔍 SCRIPT DE VÉRIFICATION DES ROUTES - ULTRA PROFESSIONNEL
 * ====================================================================
 * 
 * Vérifie que toutes les routes utilisées dans le layout existent
 * et détecte les routes manquantes avant qu'elles ne causent des erreurs
 * 
 * @version 1.0
 * @since 2025-01-19
 * ====================================================================
 */

echo "🔍 Vérification des Routes - Ultra Professionnel\n";
echo "================================================\n\n";

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$errors = [];
$warnings = [];
$success = [];

// Lire le fichier layout
$layoutPath = __DIR__ . '/resources/views/layouts/admin/catalyst.blade.php';
$layoutContent = file_get_contents($layoutPath);

// Extraire toutes les routes avec route()
preg_match_all("/route\('([^']+)'\)/", $layoutContent, $matches);
$routesInLayout = array_unique($matches[1]);

echo "📋 Routes trouvées dans le layout: " . count($routesInLayout) . "\n\n";

// Récupérer toutes les routes définies
$allRoutes = Route::getRoutes();
$definedRoutes = [];
foreach ($allRoutes as $route) {
    if ($route->getName()) {
        $definedRoutes[] = $route->getName();
    }
}

echo "✅ Routes définies dans l'application: " . count($definedRoutes) . "\n\n";

// Vérifier chaque route du layout
echo "🔍 Vérification des routes du layout:\n";
echo "=====================================\n\n";

$missingCount = 0;
$existingCount = 0;

foreach ($routesInLayout as $routeName) {
    if (in_array($routeName, $definedRoutes)) {
        echo "  ✅ {$routeName}\n";
        $existingCount++;
        $success[] = $routeName;
    } else {
        echo "  ❌ {$routeName} - MANQUANTE!\n";
        $missingCount++;
        $errors[] = $routeName;
    }
}

echo "\n";
echo "================================================\n";
echo "📊 RÉSUMÉ\n";
echo "================================================\n\n";

echo "✅ Routes existantes: {$existingCount}\n";
echo "❌ Routes manquantes: {$missingCount}\n\n";

if (!empty($errors)) {
    echo "🔴 ROUTES MANQUANTES (À CORRIGER):\n";
    echo "===================================\n";
    foreach ($errors as $error) {
        echo "  • {$error}\n";
        
        // Suggérer des alternatives
        $suggestions = [];
        foreach ($definedRoutes as $defined) {
            similar_text($error, $defined, $percent);
            if ($percent > 70) {
                $suggestions[] = $defined;
            }
        }
        
        if (!empty($suggestions)) {
            echo "    💡 Suggestions: " . implode(', ', array_slice($suggestions, 0, 3)) . "\n";
        }
    }
    echo "\n";
}

// Vérifier les routes critiques pour l'application
echo "🎯 Vérification des routes critiques:\n";
echo "=====================================\n\n";

$criticalRoutes = [
    'admin.dashboard',
    'admin.drivers.index',
    'admin.drivers.sanctions.index',
    'admin.drivers.import.show',
    'admin.vehicles.index',
    'admin.assignments.index',
    'admin.maintenance.overview',
    'admin.maintenance.operations.index',
    'admin.repair-requests.index',
];

$allCriticalExist = true;

foreach ($criticalRoutes as $critical) {
    if (in_array($critical, $definedRoutes)) {
        echo "  ✅ {$critical}\n";
    } else {
        echo "  ❌ {$critical} - CRITIQUE!\n";
        $allCriticalExist = false;
    }
}

echo "\n";

if ($allCriticalExist) {
    echo "✅ Toutes les routes critiques existent!\n\n";
} else {
    echo "❌ Certaines routes critiques sont manquantes!\n\n";
}

// Vérifier les nouvelles routes Livewire Phase 3
echo "🚀 Vérification des routes Phase 3 (Livewire):\n";
echo "==============================================\n\n";

$phase3Routes = [
    'admin.drivers.import.show' => 'Import de chauffeurs',
    'admin.drivers.sanctions.index' => 'Sanctions des chauffeurs',
];

$allPhase3Exist = true;

foreach ($phase3Routes as $route => $description) {
    if (in_array($route, $definedRoutes)) {
        echo "  ✅ {$route} - {$description}\n";
    } else {
        echo "  ❌ {$route} - {$description} - MANQUANTE!\n";
        $allPhase3Exist = false;
    }
}

echo "\n";

if ($allPhase3Exist) {
    echo "✅ Toutes les routes Phase 3 sont configurées!\n\n";
} else {
    echo "❌ Certaines routes Phase 3 sont manquantes!\n\n";
}

// Résultat final
echo "================================================\n";
echo "🎯 RÉSULTAT FINAL\n";
echo "================================================\n\n";

if (empty($errors)) {
    echo "✅ SUCCÈS! Toutes les routes du layout existent.\n";
    echo "✅ L'application devrait fonctionner sans erreur de route.\n\n";
    echo "📝 Prochaines étapes:\n";
    echo "  1. Tester l'application dans le navigateur\n";
    echo "  2. Vérifier tous les liens de navigation\n";
    echo "  3. Tester les pages critiques\n\n";
    exit(0);
} else {
    echo "❌ ATTENTION! {$missingCount} route(s) manquante(s) détectée(s).\n";
    echo "❌ Corrigez les routes avant de tester l'application.\n\n";
    echo "📝 Actions à prendre:\n";
    echo "  1. Corriger les routes manquantes dans le layout\n";
    echo "  2. Ou créer les routes manquantes dans routes/web.php\n";
    echo "  3. Vider les caches: php artisan view:clear && php artisan route:clear\n";
    echo "  4. Relancer ce script pour vérifier\n\n";
    exit(1);
}
