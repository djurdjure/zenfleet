<?php

/**
 * 🌐 Validation Web - Changement de Statut Ultra-Pro
 * 
 * Script de validation finale pour vérifier que tout est bien configuré
 * pour l'interface web.
 * 
 * @version 1.0-Final
 * @since 2025-11-12
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "=========================================\n";
echo "🌐 VALIDATION WEB - STATUS BADGE ULTRA-PRO\n";
echo "=========================================\n\n";

// 1. Vérifier les composants Livewire
echo "📦 VÉRIFICATION DES COMPOSANTS\n";
echo "================================\n\n";

$checks = [
    'Composant PHP' => app_path('Livewire/Admin/VehicleStatusBadgeUltraPro.php'),
    'Vue Blade' => resource_path('views/livewire/admin/vehicle-status-badge-ultra-pro.blade.php'),
    'Toast System' => resource_path('views/components/toast-notifications.blade.php'),
    'Page Index' => resource_path('views/admin/vehicles/index.blade.php'),
];

$allGood = true;
foreach ($checks as $name => $path) {
    if (file_exists($path)) {
        echo "✅ {$name}: OK\n";
        
        // Vérifier que le composant est bien utilisé dans index.blade.php
        if ($name === 'Page Index') {
            $content = file_get_contents($path);
            if (strpos($content, 'vehicle-status-badge-ultra-pro') !== false) {
                echo "   ✅ Composant Ultra-Pro intégré dans la page\n";
            } else {
                echo "   ⚠️ Composant Ultra-Pro non trouvé dans la page\n";
                $allGood = false;
            }
        }
    } else {
        echo "❌ {$name}: MANQUANT\n";
        $allGood = false;
    }
}

echo "\n";
echo "📊 ÉTAT DE LA BASE DE DONNÉES\n";
echo "==============================\n\n";

// Vérifier les statuts disponibles
$statuses = VehicleStatus::all();
echo "Statuts disponibles ({$statuses->count()}):\n";
foreach ($statuses as $status) {
    $slug = \Str::slug($status->name);
    echo "   - {$status->name} (ID: {$status->id}, Slug: {$slug})\n";
}

// Vérifier quelques véhicules
echo "\n";
$vehicles = Vehicle::with('vehicleStatus')
    ->where('is_archived', false)
    ->limit(5)
    ->get();

echo "Véhicules échantillon ({$vehicles->count()}):\n";
foreach ($vehicles as $vehicle) {
    $status = $vehicle->vehicleStatus ? $vehicle->vehicleStatus->name : 'Non défini';
    echo "   - {$vehicle->registration_plate}: {$status}\n";
}

// Vérifier l'historique récent
echo "\n";
$recentHistory = DB::table('status_history')
    ->where('statusable_type', 'App\Models\Vehicle')
    ->orderBy('changed_at', 'desc')
    ->limit(3)
    ->get();

if ($recentHistory->count() > 0) {
    echo "Historique récent des changements:\n";
    foreach ($recentHistory as $history) {
        $date = \Carbon\Carbon::parse($history->changed_at)->format('Y-m-d H:i');
        echo "   - {$date}: {$history->from_status} → {$history->to_status}\n";
    }
} else {
    echo "Aucun historique de changement trouvé\n";
}

echo "\n";
echo "🔧 CONFIGURATION LIVEWIRE\n";
echo "==========================\n\n";

// Vérifier que Livewire est bien configuré
$livewireConfig = config('livewire');
if ($livewireConfig) {
    echo "✅ Livewire configuré\n";
    echo "   - Asset URL: " . ($livewireConfig['asset_url'] ?? 'default') . "\n";
    echo "   - App URL: " . config('app.url') . "\n";
} else {
    echo "❌ Configuration Livewire manquante\n";
    $allGood = false;
}

echo "\n";
echo "=========================================\n";
if ($allGood) {
    echo "✅ VALIDATION COMPLÈTE RÉUSSIE!\n";
    echo "=========================================\n\n";
    
    echo "🎯 PROCHAINES ÉTAPES:\n";
    echo "1. Accédez à: " . config('app.url') . "/admin/vehicles\n";
    echo "2. Connectez-vous avec un compte admin\n";
    echo "3. Cliquez sur un badge de statut de véhicule\n";
    echo "4. Testez le changement avec confirmation\n";
    echo "5. Vérifiez les notifications toast\n\n";
    
    echo "📋 POINTS DE VALIDATION:\n";
    echo "✓ Badge cliquable avec animation hover\n";
    echo "✓ Dropdown avec statuts autorisés\n";
    echo "✓ Modal de confirmation avec détails\n";
    echo "✓ Messages contextuels intelligents\n";
    echo "✓ Notifications toast de succès/erreur\n";
    echo "✓ Historisation automatique\n";
    echo "✓ Validation State Machine\n";
} else {
    echo "⚠️ VALIDATION INCOMPLÈTE\n";
    echo "=========================================\n";
    echo "Certains éléments sont manquants ou mal configurés.\n";
    echo "Veuillez vérifier les erreurs ci-dessus.\n";
}

echo "\n";
