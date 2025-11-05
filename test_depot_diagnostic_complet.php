#!/usr/bin/env php
<?php

/**
 * Diagnostic COMPLET - Module Dépôts
 * Test et correction des problèmes persistants
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VehicleDepot;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║      DIAGNOSTIC COMPLET MODULE DÉPÔTS - ENTERPRISE GRADE      ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// ============================================================
// 1. VÉRIFICATION STRUCTURE BASE DE DONNÉES
// ============================================================
echo "🔍 VÉRIFICATION STRUCTURE BASE DE DONNÉES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $columns = Schema::getColumnListing('vehicle_depots');
    echo "✅ Table 'vehicle_depots' existe\n";
    echo "📋 Colonnes : " . implode(', ', array_slice($columns, 0, 10)) . "...\n\n";
    
    // Vérifier contraintes sur 'code'
    $codeColumn = DB::select("
        SELECT column_name, is_nullable, data_type, character_maximum_length
        FROM information_schema.columns 
        WHERE table_name = 'vehicle_depots' 
        AND column_name = 'code'
    ");
    
    if (!empty($codeColumn)) {
        $col = $codeColumn[0];
        echo "📊 Colonne 'code' :\n";
        echo "   - Type : {$col->data_type}({$col->character_maximum_length})\n";
        echo "   - Nullable : " . ($col->is_nullable === 'YES' ? '✅ OUI' : '❌ NON') . "\n\n";
        
        if ($col->is_nullable === 'NO') {
            echo "⚠️ PROBLÈME DÉTECTÉ : La colonne 'code' n'accepte pas NULL !\n";
            echo "   Cela peut causer des échecs d'enregistrement.\n\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur : {$e->getMessage()}\n\n";
}

// ============================================================
// 2. TEST ENREGISTREMENT SIMPLE
// ============================================================
echo "🧪 TEST ENREGISTREMENT SIMPLE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $org = Organization::first();
    if (!$org) {
        echo "❌ Aucune organisation trouvée.\n";
        exit(1);
    }
    
    // Nettoyer les tests précédents
    VehicleDepot::where('name', 'like', 'TEST_%')->forceDelete();
    
    echo "📝 Test 1 : Création dépôt minimaliste...\n";
    
    $depot = VehicleDepot::create([
        'organization_id' => $org->id,
        'name' => 'TEST_' . uniqid(),
        'current_count' => 0,
        'is_active' => true,
    ]);
    
    echo "✅ SUCCESS : Dépôt créé avec ID #{$depot->id}\n";
    echo "   - Nom : {$depot->name}\n";
    echo "   - Code : " . ($depot->code ?? 'NULL') . "\n\n";
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n";
    echo "📍 Trace : " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    
    // Analyser l'erreur SQL
    if (strpos($e->getMessage(), 'null value in column "code"') !== false) {
        echo "⚠️ DIAGNOSTIC : La colonne 'code' n'accepte pas NULL\n";
        echo "   SOLUTION : Exécuter la migration pour rendre 'code' nullable\n\n";
    }
}

// ============================================================
// 3. TEST AVEC TOUS LES CHAMPS
// ============================================================
echo "🧪 TEST AVEC TOUS LES CHAMPS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $fullData = [
        'organization_id' => $org->id,
        'name' => 'TEST_FULL_' . uniqid(),
        'code' => 'TEST-' . rand(1000, 9999),
        'address' => '123 Rue Test',
        'city' => 'Alger',
        'wilaya' => 'Alger',
        'postal_code' => '16000',
        'phone' => '+213 555 0001',
        'email' => 'test@depot.com',
        'manager_name' => 'Manager Test',
        'manager_phone' => '+213 555 0002',
        'capacity' => 50,
        'latitude' => 36.7538,
        'longitude' => 3.0588,
        'description' => 'Description test',
        'current_count' => 0,
        'is_active' => true,
    ];
    
    echo "📝 Données à insérer :\n";
    foreach (['name', 'code', 'latitude', 'longitude', 'capacity'] as $key) {
        $value = $fullData[$key] ?? 'NULL';
        $type = gettype($value);
        echo "   - {$key}: {$value} ({$type})\n";
    }
    echo "\n";
    
    $depot2 = VehicleDepot::create($fullData);
    
    echo "✅ SUCCESS : Dépôt complet créé avec ID #{$depot2->id}\n";
    echo "   - Coordonnées : ({$depot2->latitude}, {$depot2->longitude})\n\n";
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
}

// ============================================================
// 4. ANALYSE PROBLÈME TOGGLE
// ============================================================
echo "🎨 ANALYSE PROBLÈME TOGGLE UI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$bladeContent = file_get_contents(__DIR__ . '/resources/views/livewire/depots/manage-depots.blade.php');

// Vérifier wire:model du toggle
if (preg_match('/wire:model\.(live|defer)="is_active"/', $bladeContent, $matches)) {
    $mode = $matches[1];
    if ($mode === 'live') {
        echo "⚠️ PROBLÈME DÉTECTÉ : Toggle utilise wire:model.live\n";
        echo "   Cela cause un re-render à chaque clic → espace non esthétique\n";
        echo "   SOLUTION : Remplacer par wire:model.defer\n\n";
    } else {
        echo "✅ Toggle utilise wire:model.defer (correct)\n\n";
    }
} else {
    echo "❓ Impossible de déterminer le mode du toggle\n\n";
}

// Vérifier position du toggle
if (strpos($bladeContent, '<div class="md:col-span-2 flex items-center">') !== false) {
    echo "✅ Toggle est dans la grille (position correcte)\n\n";
} else {
    echo "⚠️ Toggle pourrait être hors de la grille\n\n";
}

// ============================================================
// 5. SIMULATION COMPOSANT LIVEWIRE
// ============================================================
echo "🔧 SIMULATION COMPOSANT LIVEWIRE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Simuler les données venant du formulaire (comme Livewire)
$formData = [
    'name' => 'TEST_LIVEWIRE_' . uniqid(),
    'code' => '',  // Vide comme dans le formulaire
    'latitude' => '36.7538',  // String depuis le formulaire
    'longitude' => '3.0588',   // String depuis le formulaire
    'capacity' => '50',        // String depuis le formulaire
    'is_active' => 'true',     // String depuis le formulaire
];

echo "📝 Données simulées depuis formulaire :\n";
foreach ($formData as $key => $value) {
    $display = $value === '' ? '(vide)' : $value;
    echo "   - {$key}: '{$display}' (string)\n";
}
echo "\n";

// Appliquer le casting comme dans ManageDepots.php
$castedData = [
    'organization_id' => $org->id,
    'name' => $formData['name'],
    'code' => $formData['code'] ?: null,
    'capacity' => $formData['capacity'] ? (int) $formData['capacity'] : null,
    'latitude' => $formData['latitude'] ? (float) $formData['latitude'] : null,
    'longitude' => $formData['longitude'] ? (float) $formData['longitude'] : null,
    'is_active' => filter_var($formData['is_active'], FILTER_VALIDATE_BOOLEAN),
    'current_count' => 0,
];

echo "🔄 Après casting :\n";
foreach (['code', 'capacity', 'latitude', 'longitude', 'is_active'] as $key) {
    $value = $castedData[$key];
    $type = gettype($value);
    $display = $value === null ? 'NULL' : var_export($value, true);
    echo "   - {$key}: {$display} ({$type})\n";
}
echo "\n";

try {
    $depot3 = VehicleDepot::create($castedData);
    echo "✅ SUCCESS : Dépôt créé depuis données Livewire\n";
    echo "   - ID : {$depot3->id}\n";
    echo "   - Code : " . ($depot3->code ?? 'NULL') . "\n\n";
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
}

// ============================================================
// 6. VÉRIFICATION LOGS
// ============================================================
echo "📜 VÉRIFICATION LOGS RÉCENTS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $lines = explode("\n", $logs);
    $recentErrors = [];
    
    foreach (array_slice($lines, -50) as $line) {
        if (strpos($line, 'Erreur enregistrement dépôt') !== false ||
            strpos($line, 'vehicle_depots') !== false) {
            $recentErrors[] = $line;
        }
    }
    
    if (!empty($recentErrors)) {
        echo "⚠️ Erreurs récentes trouvées :\n";
        foreach (array_slice($recentErrors, -3) as $error) {
            echo "   " . substr($error, 0, 100) . "...\n";
        }
        echo "\n";
    } else {
        echo "✅ Aucune erreur récente liée aux dépôts\n\n";
    }
}

// ============================================================
// RÉSUMÉ ET RECOMMANDATIONS
// ============================================================
echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                  RÉSUMÉ DU DIAGNOSTIC                         ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

$issues = [];
$solutions = [];

// Analyser les résultats
if (isset($col) && $col->is_nullable === 'NO') {
    $issues[] = "❌ La colonne 'code' n'accepte pas NULL";
    $solutions[] = "Exécuter: docker exec zenfleet_php php artisan migrate";
}

if (isset($mode) && $mode === 'live') {
    $issues[] = "❌ Toggle utilise wire:model.live (cause l'espace)";
    $solutions[] = "Remplacer par wire:model.defer dans le blade";
}

if (empty($issues)) {
    echo "✅ AUCUN PROBLÈME DÉTECTÉ\n";
    echo "   Le module devrait fonctionner correctement.\n\n";
} else {
    echo "⚠️ PROBLÈMES DÉTECTÉS :\n";
    foreach ($issues as $issue) {
        echo "   {$issue}\n";
    }
    echo "\n";
    
    echo "💡 SOLUTIONS RECOMMANDÉES :\n";
    foreach ($solutions as $i => $solution) {
        echo "   " . ($i + 1) . ". {$solution}\n";
    }
    echo "\n";
}

// Nettoyage
echo "🧹 Nettoyage des dépôts de test...\n";
VehicleDepot::where('name', 'like', 'TEST_%')->forceDelete();
echo "✅ Nettoyage terminé\n\n";

echo "📊 Diagnostic terminé !\n\n";
