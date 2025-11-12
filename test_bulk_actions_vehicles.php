<?php

/**
 * TEST DU SYSTÈME D'ACTIONS BULK POUR VÉHICULES
 */

require_once __DIR__ . '/vendor/autoload.php';

// Démarrer l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "\n╔══════════════════════════════════════════════════════════╗\n";
echo "║   🚀 TEST SYSTÈME D'ACTIONS BULK - VÉHICULES            ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

// Test 1: Vérification du composant Livewire
echo "📋 TEST 1: COMPOSANT LIVEWIRE\n";
echo "════════════════════════════════════\n";

$componentClass = '\\App\\Livewire\\Admin\\VehicleBulkActions';
if (class_exists($componentClass)) {
    echo "✅ Composant VehicleBulkActions trouvé\n";
} else {
    echo "❌ Composant VehicleBulkActions non trouvé\n";
}

// Test 2: Vérification de la vue
echo "\n📋 TEST 2: VUE BLADE\n";
echo "════════════════════════════════════\n";

$bladeFile = __DIR__ . '/resources/views/livewire/admin/vehicle-bulk-actions.blade.php';
if (file_exists($bladeFile)) {
    echo "✅ Vue vehicle-bulk-actions.blade.php trouvée\n";
} else {
    echo "❌ Vue vehicle-bulk-actions.blade.php non trouvée\n";
}

// Test 3: Vérification de l'intégration
echo "\n📋 TEST 3: INTÉGRATION\n";
echo "════════════════════════════════════\n";

$indexFile = __DIR__ . '/resources/views/admin/vehicles/index.blade.php';
if (file_exists($indexFile)) {
    $content = file_get_contents($indexFile);
    if (strpos($content, '@livewire(\'admin.vehicle-bulk-actions\')') !== false) {
        echo "✅ Composant intégré dans index.blade.php\n";
    } else {
        echo "❌ Composant non intégré\n";
    }
} else {
    echo "❌ Fichier index.blade.php non trouvé\n";
}

echo "\n✨ Test terminé!\n\n";
