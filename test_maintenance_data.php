<?php

/**
 * Script de test pour vérifier le chargement des données de maintenance
 * À exécuter avec : docker compose exec -u zenfleet_user php php test_maintenance_data.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 TEST DU CHARGEMENT DES DONNÉES MAINTENANCE\n";
echo str_repeat("=", 60) . "\n\n";

// Simuler un utilisateur authentifié
$user = App\Models\User::with('organization')->first();

if (!$user) {
    echo "❌ Aucun utilisateur trouvé dans la base de données\n";
    exit(1);
}

echo "✅ Utilisateur trouvé : {$user->name} (ID: {$user->id})\n";
echo "✅ Organisation : {$user->organization->name} (ID: {$user->organization_id})\n\n";

// Test 1: Vérifier les véhicules
echo "📊 TEST 1: Chargement des véhicules\n";
echo str_repeat("-", 60) . "\n";

$vehicles = App\Models\Vehicle::where('organization_id', $user->organization_id)
    ->whereNull('deleted_at')
    ->orderBy('registration_plate')
    ->get(['id', 'registration_plate', 'brand', 'model', 'current_mileage']);

echo "Nombre de véhicules trouvés: " . $vehicles->count() . "\n";

if ($vehicles->count() > 0) {
    echo "✅ Véhicules disponibles:\n";
    foreach ($vehicles as $vehicle) {
        echo "   - ID {$vehicle->id}: {$vehicle->registration_plate} - {$vehicle->brand} {$vehicle->model}";
        if ($vehicle->current_mileage) {
            echo " (" . number_format($vehicle->current_mileage, 0, ',', ' ') . " km)";
        }
        echo "\n";
    }
} else {
    echo "⚠️  Aucun véhicule trouvé pour cette organisation\n";
}

echo "\n";

// Test 2: Vérifier les types de maintenance
echo "📊 TEST 2: Chargement des types de maintenance\n";
echo str_repeat("-", 60) . "\n";

$maintenanceTypes = App\Models\MaintenanceType::where('organization_id', $user->organization_id)
    ->where('is_active', true)
    ->orderBy('category')
    ->orderBy('name')
    ->get(['id', 'name', 'category', 'estimated_duration_minutes', 'estimated_cost']);

echo "Nombre de types trouvés: " . $maintenanceTypes->count() . "\n";

if ($maintenanceTypes->count() > 0) {
    echo "✅ Types de maintenance disponibles:\n";
    $currentCategory = null;
    foreach ($maintenanceTypes as $type) {
        if ($currentCategory !== $type->category) {
            $currentCategory = $type->category;
            echo "\n   Catégorie: " . strtoupper($type->category) . "\n";
        }
        echo "   - ID {$type->id}: {$type->name}";
        if ($type->estimated_cost) {
            echo " (≈ " . number_format($type->estimated_cost, 0, ',', ' ') . " DZD)";
        }
        echo "\n";
    }
} else {
    echo "⚠️  Aucun type de maintenance trouvé pour cette organisation\n";
    echo "💡 Solution: Exécuter le seeder avec:\n";
    echo "   docker compose exec -u zenfleet_user php php artisan db:seed --class=MaintenanceTypesSeeder\n";
}

echo "\n";

// Test 3: Vérifier les fournisseurs
echo "📊 TEST 3: Chargement des fournisseurs\n";
echo str_repeat("-", 60) . "\n";

$providers = App\Models\MaintenanceProvider::where('organization_id', $user->organization_id)
    ->where('is_active', true)
    ->orderBy('name')
    ->get(['id', 'name', 'phone', 'email']);

echo "Nombre de fournisseurs trouvés: " . $providers->count() . "\n";

if ($providers->count() > 0) {
    echo "✅ Fournisseurs disponibles:\n";
    foreach ($providers as $provider) {
        echo "   - ID {$provider->id}: {$provider->name}";
        if ($provider->phone) {
            echo " - {$provider->phone}";
        }
        echo "\n";
    }
} else {
    echo "⚠️  Aucun fournisseur trouvé pour cette organisation\n";
    echo "💡 Il est normal de ne pas avoir de fournisseurs au début\n";
}

echo "\n";

// Test 4: Simuler l'appel du contrôleur
echo "📊 TEST 4: Simulation de l'appel du contrôleur\n";
echo str_repeat("-", 60) . "\n";

try {
    // Simuler l'authentification
    Auth::login($user);

    $controller = new App\Http\Controllers\Admin\MaintenanceOperationController();

    // Utiliser la réflexion pour appeler la méthode create
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('create');

    echo "✅ Contrôleur MaintenanceOperationController instancié\n";
    echo "✅ Méthode create() existe\n";
    echo "✅ Utilisateur authentifié: {$user->name}\n";

} catch (\Exception $e) {
    echo "❌ Erreur lors du test du contrôleur: {$e->getMessage()}\n";
}

echo "\n";

// Résumé
echo str_repeat("=", 60) . "\n";
echo "📋 RÉSUMÉ DES TESTS\n";
echo str_repeat("=", 60) . "\n";
echo "Véhicules: " . ($vehicles->count() > 0 ? "✅ {$vehicles->count()} trouvé(s)" : "⚠️  Aucun") . "\n";
echo "Types maintenance: " . ($maintenanceTypes->count() > 0 ? "✅ {$maintenanceTypes->count()} trouvé(s)" : "⚠️  Aucun") . "\n";
echo "Fournisseurs: " . ($providers->count() > 0 ? "✅ {$providers->count()} trouvé(s)" : "⚠️  Aucun") . "\n";

echo "\n";

if ($vehicles->count() === 0 || $maintenanceTypes->count() === 0) {
    echo "⚠️  ATTENTION: Données manquantes détectées!\n\n";

    if ($vehicles->count() === 0) {
        echo "🔧 Pour ajouter des véhicules:\n";
        echo "   1. Accédez à http://localhost/admin/vehicles/create\n";
        echo "   2. Ou exécutez un seeder si disponible\n\n";
    }

    if ($maintenanceTypes->count() === 0) {
        echo "🔧 Pour ajouter des types de maintenance:\n";
        echo "   docker compose exec -u zenfleet_user php php artisan db:seed --class=MaintenanceTypesSeeder\n\n";
    }
} else {
    echo "✅ Toutes les données nécessaires sont présentes!\n";
    echo "Si les selects sont vides dans le formulaire, le problème vient de la vue.\n";
}

echo "\n";
