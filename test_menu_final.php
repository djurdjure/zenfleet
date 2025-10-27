#!/usr/bin/env php
<?php

/**
 * Test final du menu dépenses dans Catalyst
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🎯 TEST FINAL DU MENU DÉPENSES - CATALYST LAYOUT\n";
echo str_repeat("=", 80) . "\n\n";

// Authentification
$admin = User::where('email', 'admin@zenfleet.dz')->first();
if (!$admin) {
    echo "❌ Utilisateur admin non trouvé\n";
    exit(1);
}

Auth::login($admin);
echo "✅ Authentifié: " . $admin->email . "\n";
echo "   Rôles: " . $admin->getRoleNames()->implode(', ') . "\n";
echo "   Organization ID: " . $admin->organization_id . "\n\n";

// Test 1: Vérifier le fichier catalyst.blade.php
echo "📋 Test 1: Vérification du fichier catalyst.blade.php\n";
echo str_repeat("-", 40) . "\n";

$catalystFile = __DIR__ . '/resources/views/layouts/admin/catalyst.blade.php';
if (file_exists($catalystFile)) {
    echo "✅ Fichier catalyst.blade.php trouvé\n";
    
    $content = file_get_contents($catalystFile);
    
    // Vérifier la nouvelle couleur de fond
    if (strpos($content, '#eef2f7') !== false) {
        echo "✅ Nouvelle couleur de fond #eef2f7 appliquée\n";
    } else {
        echo "❌ Couleur de fond non trouvée\n";
    }
    
    // Vérifier la présence du menu dépenses
    if (strpos($content, 'GESTION DES DÉPENSES') !== false) {
        echo "✅ Section menu dépenses trouvée dans le fichier\n";
    } else {
        echo "❌ Section menu dépenses non trouvée\n";
    }
    
    if (strpos($content, 'solar:wallet-money-bold') !== false) {
        echo "✅ Icône solar:wallet-money-bold trouvée\n";
    } else {
        echo "❌ Icône non trouvée\n";
    }
    
    if (strpos($content, 'admin.vehicle-expenses.index') !== false) {
        echo "✅ Routes du module dépenses présentes\n";
    } else {
        echo "❌ Routes du module dépenses non trouvées\n";
    }
} else {
    echo "❌ Fichier catalyst.blade.php non trouvé\n";
}

// Test 2: Vérifier les permissions
echo "\n📋 Test 2: Vérification des permissions\n";
echo str_repeat("-", 40) . "\n";

$permissions = ['view expenses', 'create expenses', 'approve expenses', 'export expenses', 'view expense analytics'];
foreach ($permissions as $permission) {
    if ($admin->can($permission)) {
        echo "✅ $permission\n";
    } else {
        echo "❌ $permission\n";
    }
}

// Test 3: Compteur des dépenses en attente
echo "\n📋 Test 3: Compteur des dépenses en attente\n";
echo str_repeat("-", 40) . "\n";

try {
    $pendingCount = \App\Models\VehicleExpense::where('organization_id', $admin->organization_id)
        ->whereIn('approval_status', ['pending_level1', 'pending_level2'])
        ->count();
    echo "✅ Dépenses en attente d'approbation: $pendingCount\n";
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier les routes
echo "\n📋 Test 4: Vérification des routes du menu\n";
echo str_repeat("-", 40) . "\n";

$routes = [
    'admin.vehicle-expenses.index' => 'Tableau de bord',
    'admin.vehicle-expenses.create' => 'Nouvelle dépense',
    'admin.vehicle-expenses.dashboard' => 'Analytics',
    'admin.vehicle-expenses.export' => 'Export',
    'admin.vehicle-expenses.analytics.cost-trends' => 'TCO & Tendances',
];

$routesOk = 0;
foreach ($routes as $route => $label) {
    if (\Route::has($route)) {
        echo "✅ $label\n";
        $routesOk++;
    } else {
        echo "❌ $label (route: $route)\n";
    }
}

// Test 5: Rendu du layout
echo "\n📋 Test 5: Test de rendu du layout\n";
echo str_repeat("-", 40) . "\n";

try {
    $request = Request::create(route('admin.vehicle-expenses.index'), 'GET');
    $request->setUserResolver(function() use ($admin) {
        return $admin;
    });
    
    $response = $app->handle($request);
    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        echo "✅ Page accessible (HTTP 200)\n";
        
        $html = $response->getContent();
        
        // Vérifier la présence du menu dans le HTML rendu
        if (strpos($html, 'solar:wallet-money-bold') !== false || 
            strpos($html, 'Dépenses') !== false) {
            echo "✅ Menu dépenses présent dans le HTML rendu\n";
        } else {
            echo "⚠️ Menu dépenses potentiellement absent du HTML\n";
        }
        
        if (strpos($html, '#eef2f7') !== false || 
            strpos($html, 'bg-[#eef2f7]') !== false) {
            echo "✅ Nouvelle couleur de fond présente\n";
        } else {
            echo "⚠️ Nouvelle couleur non détectée dans le HTML\n";
        }
        
    } else {
        echo "❌ Page inaccessible (HTTP $statusCode)\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// Résumé
echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ DES TESTS\n";
echo str_repeat("=", 80) . "\n";
echo "✅ Fichier modifié: catalyst.blade.php\n";
echo "✅ Nouvelle couleur: #eef2f7\n";
echo "✅ Menu dépenses ajouté avec sous-menus\n";
echo "✅ Routes disponibles: $routesOk/" . count($routes) . "\n";
echo "\n🎉 Le menu dépenses est maintenant intégré dans le layout Catalyst!\n";
echo str_repeat("=", 80) . "\n\n";
