#!/usr/bin/env php
<?php

/**
 * Test de l'affichage du menu des dépenses
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🎯 TEST DU MENU DES DÉPENSES\n";
echo str_repeat("=", 80) . "\n\n";

// Authentification
$admin = User::where('email', 'admin@zenfleet.dz')->first();
if ($admin) {
    Auth::login($admin);
    echo "✅ Authentifié: " . $admin->email . "\n\n";
}

// Vérifier les routes principales
echo "📋 Vérification des routes du menu:\n";
echo str_repeat("-", 40) . "\n";

$routes = [
    'admin.vehicle-expenses.index' => 'Vue d\'ensemble',
    'admin.vehicle-expenses.create' => 'Nouvelle dépense',
    'admin.vehicle-expenses.dashboard' => 'Analytics & Rapports',
    'admin.vehicle-expenses.export' => 'Export',
    'admin.vehicle-expenses.analytics.cost-trends' => 'TCO & Tendances',
];

$success = 0;
$errors = 0;

foreach ($routes as $routeName => $description) {
    try {
        if (\Route::has($routeName)) {
            $url = route($routeName);
            echo "✅ $description: $url\n";
            $success++;
        } else {
            echo "❌ $description: Route non trouvée\n";
            $errors++;
        }
    } catch (\Exception $e) {
        echo "❌ $description: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// Vérifier les permissions
echo "\n📋 Vérification des permissions:\n";
echo str_repeat("-", 40) . "\n";

$permissions = [
    'view expenses' => 'Voir les dépenses',
    'create expenses' => 'Créer des dépenses',
    'approve expenses' => 'Approuver les dépenses',
    'view expense analytics' => 'Voir les analytics',
];

foreach ($permissions as $permission => $description) {
    if ($admin && $admin->can($permission)) {
        echo "✅ $description\n";
    } else {
        echo "❌ $description\n";
    }
}

// Test d'accès à la page principale
echo "\n📋 Test d'accès HTTP:\n";
echo str_repeat("-", 40) . "\n";

try {
    $request = Request::create(route('admin.vehicle-expenses.index'), 'GET');
    $request->setUserResolver(function() use ($admin) {
        return $admin;
    });
    
    $response = $app->handle($request);
    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        echo "✅ Page d'accueil des dépenses accessible (HTTP 200)\n";
        
        // Vérifier la présence du menu dans le HTML
        $content = $response->getContent();
        
        if (strpos($content, 'Gestion des Dépenses') !== false) {
            echo "✅ Menu 'Gestion des Dépenses' présent dans la page\n";
        } else {
            echo "⚠️ Menu 'Gestion des Dépenses' non trouvé dans la page\n";
        }
        
        if (strpos($content, 'tabler:moneybag') !== false) {
            echo "✅ Icône du menu trouvée\n";
        } else {
            echo "⚠️ Icône du menu non trouvée\n";
        }
        
    } else {
        echo "❌ Page d'accueil des dépenses: HTTP $statusCode\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur d'accès: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ\n";
echo str_repeat("=", 80) . "\n";
echo "✅ Routes disponibles: $success\n";
echo "❌ Routes manquantes: $errors\n";

if ($errors === 0) {
    echo "\n🎉 Le menu des dépenses est correctement configuré!\n";
} else {
    echo "\n⚠️ Certaines routes nécessitent configuration.\n";
}

echo str_repeat("=", 80) . "\n\n";
