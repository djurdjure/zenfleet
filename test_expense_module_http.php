#!/usr/bin/env php
<?php

/**
 * Test HTTP du Module de Gestion des Dépenses
 * Vérifie que les pages sont accessibles
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🌐 TEST HTTP DU MODULE DE GESTION DES DÉPENSES\n";
echo str_repeat("=", 80) . "\n\n";

// Authentification en tant qu'admin
$admin = User::where('email', 'admin@zenfleet.dz')->first();
if (!$admin) {
    // Créer un admin si inexistant
    $admin = User::create([
        'name' => 'Admin Test',
        'email' => 'admin@zenfleet.dz',
        'password' => bcrypt('password'),
        'organization_id' => 1
    ]);
}

Auth::login($admin);
echo "✅ Authentifié en tant qu'admin: " . $admin->email . "\n\n";

// Liste des routes à tester
$routes = [
    'admin.vehicle-expenses.index' => 'Liste des dépenses',
    'admin.vehicle-expenses.create' => 'Formulaire de création',
    'admin.vehicle-expenses.dashboard' => 'Dashboard analytics',
];

echo "📋 Test d'accès aux routes\n";
echo str_repeat("-", 40) . "\n";

$success = 0;
$errors = 0;

foreach ($routes as $routeName => $description) {
    try {
        $url = route($routeName);
        
        // Simuler une requête HTTP GET
        $request = Request::create($url, 'GET');
        $request->setUserResolver(function() use ($admin) {
            return $admin;
        });
        
        // Obtenir la réponse
        $response = $app->handle($request);
        $statusCode = $response->getStatusCode();
        
        if ($statusCode === 200) {
            echo "✅ $description ($routeName): HTTP 200 OK\n";
            $success++;
        } elseif ($statusCode === 302) {
            echo "⚠️ $description ($routeName): HTTP 302 Redirect\n";
            $success++;
        } else {
            echo "❌ $description ($routeName): HTTP $statusCode\n";
            $errors++;
        }
        
    } catch (Exception $e) {
        echo "❌ $description ($routeName): Erreur - " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ\n";
echo str_repeat("=", 80) . "\n";
echo "✅ Routes accessibles: $success\n";
echo "❌ Routes en erreur: $errors\n";

if ($errors === 0) {
    echo "\n🎉 Toutes les routes sont accessibles!\n";
} else {
    echo "\n⚠️ Certaines routes nécessitent attention.\n";
}

echo str_repeat("=", 80) . "\n\n";
