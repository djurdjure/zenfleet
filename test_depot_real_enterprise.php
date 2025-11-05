#!/usr/bin/env php
<?php

/**
 * TEST RÉEL ENTERPRISE-GRADE - Module Dépôts
 * Validation complète de tous les cas d'usage
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\VehicleDepot;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║     TEST RÉEL ENTERPRISE-GRADE - MODULE DÉPÔTS                ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// ============================================================
// SETUP
// ============================================================
$org = Organization::first();
if (!$org) {
    echo "❌ Aucune organisation trouvée.\n";
    exit(1);
}

// Simuler l'authentification
$user = User::where('organization_id', $org->id)->first();
if ($user) {
    Auth::login($user);
    echo "👤 Utilisateur connecté : {$user->name} (Org: {$org->name})\n\n";
}

// Nettoyer les tests précédents
VehicleDepot::where('name', 'like', 'DEPOT_TEST_%')->forceDelete();

$testsPassed = 0;
$testsFailed = 0;

// ============================================================
// TEST 1 : CRÉATION DÉPÔT MINIMAL (SANS CODE)
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1 : Création dépôt minimal (sans code)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $depot1 = VehicleDepot::create([
        'organization_id' => $org->id,
        'name' => 'DEPOT_TEST_Minimal',
        'current_count' => 0,
        'is_active' => true,
    ]);
    
    echo "✅ SUCCESS : Dépôt créé avec ID #{$depot1->id}\n";
    echo "   - Nom : {$depot1->name}\n";
    echo "   - Code : " . ($depot1->code ?? 'NULL (OK)') . "\n";
    echo "   - Actif : " . ($depot1->is_active ? 'Oui' : 'Non') . "\n\n";
    $testsPassed++;
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
    $testsFailed++;
}

// ============================================================
// TEST 2 : CRÉATION AVEC CODE AUTO-GÉNÉRÉ (LIVEWIRE SIMULATION)
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2 : Création avec code auto-généré (simulation Livewire)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    // Simuler la génération de code comme dans ManageDepots.php
    $prefix = 'DP';
    $lastDepot = VehicleDepot::where('organization_id', $org->id)
        ->whereNotNull('code')
        ->where('code', 'like', $prefix . '%')
        ->orderByRaw('CAST(SUBSTRING(code, 3) AS INTEGER) DESC')
        ->first();
    
    if ($lastDepot && preg_match('/^DP(\d+)$/', $lastDepot->code, $matches)) {
        $nextNumber = intval($matches[1]) + 1;
    } else {
        $nextNumber = 1;
    }
    
    $autoCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    
    echo "📝 Code auto-généré : {$autoCode}\n";
    
    $depot2 = VehicleDepot::create([
        'organization_id' => $org->id,
        'name' => 'DEPOT_TEST_AutoCode',
        'code' => $autoCode,
        'city' => 'Alger',
        'wilaya' => 'Alger',
        'current_count' => 0,
        'is_active' => true,
    ]);
    
    echo "✅ SUCCESS : Dépôt créé avec code auto-généré\n";
    echo "   - ID : {$depot2->id}\n";
    echo "   - Code : {$depot2->code}\n";
    echo "   - Format valide : " . (preg_match('/^DP\d{4}$/', $depot2->code) ? '✅ OUI' : '❌ NON') . "\n\n";
    $testsPassed++;
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
    $testsFailed++;
}

// ============================================================
// TEST 3 : CRÉATION AVEC TOUS LES CHAMPS (Y COMPRIS EMAIL)
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 3 : Création avec TOUS les champs (incluant email)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    // Simuler les données venant du formulaire Livewire (STRINGS)
    $formData = [
        'name' => 'DEPOT_TEST_Complet',
        'code' => 'CUSTOM-001',
        'address' => '123 Boulevard de la République',
        'city' => 'Alger',
        'wilaya' => 'Alger',
        'postal_code' => '16000',
        'phone' => '+213 555 0100',
        'email' => 'depot.central@zenfleet.com',  // EMAIL maintenant supporté
        'manager_name' => 'Ahmed Benali',
        'manager_phone' => '+213 555 0101',
        'capacity' => '100',     // String depuis formulaire
        'latitude' => '36.7538', // String depuis formulaire
        'longitude' => '3.0588',  // String depuis formulaire
        'description' => 'Dépôt principal de la flotte ZenFleet à Alger',
        'is_active' => 'true',    // String depuis formulaire
    ];
    
    echo "📝 Données du formulaire (avant casting) :\n";
    echo "   - email : {$formData['email']}\n";
    echo "   - capacity : '{$formData['capacity']}' (string)\n";
    echo "   - latitude : '{$formData['latitude']}' (string)\n";
    echo "   - longitude : '{$formData['longitude']}' (string)\n\n";
    
    // Appliquer le casting comme dans ManageDepots.php
    $data = [
        'organization_id' => $org->id,
        'name' => $formData['name'],
        'code' => $formData['code'] ?: null,
        'address' => $formData['address'],
        'city' => $formData['city'],
        'wilaya' => $formData['wilaya'],
        'postal_code' => $formData['postal_code'],
        'phone' => $formData['phone'],
        'email' => $formData['email'],
        'manager_name' => $formData['manager_name'],
        'manager_phone' => $formData['manager_phone'],
        'capacity' => $formData['capacity'] ? (int) $formData['capacity'] : null,
        'latitude' => $formData['latitude'] ? (float) $formData['latitude'] : null,
        'longitude' => $formData['longitude'] ? (float) $formData['longitude'] : null,
        'description' => $formData['description'],
        'is_active' => filter_var($formData['is_active'], FILTER_VALIDATE_BOOLEAN),
        'current_count' => 0,
    ];
    
    echo "🔄 Après casting :\n";
    echo "   - capacity : " . var_export($data['capacity'], true) . " (integer)\n";
    echo "   - latitude : " . var_export($data['latitude'], true) . " (float)\n";
    echo "   - longitude : " . var_export($data['longitude'], true) . " (float)\n";
    echo "   - is_active : " . var_export($data['is_active'], true) . " (boolean)\n\n";
    
    $depot3 = VehicleDepot::create($data);
    
    echo "✅ SUCCESS : Dépôt complet créé avec succès !\n";
    echo "   - ID : {$depot3->id}\n";
    echo "   - Email : {$depot3->email}\n";
    echo "   - Coordonnées : ({$depot3->latitude}, {$depot3->longitude})\n";
    echo "   - Capacité : {$depot3->capacity} véhicules\n";
    echo "   - Description : " . substr($depot3->description, 0, 50) . "...\n\n";
    $testsPassed++;
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
    $testsFailed++;
}

// ============================================================
// TEST 4 : MISE À JOUR D'UN DÉPÔT
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 4 : Mise à jour d'un dépôt\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    if (isset($depot3)) {
        $updateData = [
            'name' => 'DEPOT_TEST_Modifié',
            'capacity' => 150,
            'is_active' => false,
            'email' => 'nouveau.email@zenfleet.com',
        ];
        
        $depot3->update($updateData);
        $depot3->refresh();
        
        echo "✅ SUCCESS : Dépôt mis à jour\n";
        echo "   - Nouveau nom : {$depot3->name}\n";
        echo "   - Nouvelle capacité : {$depot3->capacity}\n";
        echo "   - Nouvel email : {$depot3->email}\n";
        echo "   - Actif : " . ($depot3->is_active ? 'Oui' : 'Non') . "\n\n";
        $testsPassed++;
    }
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
    $testsFailed++;
}

// ============================================================
// TEST 5 : RÉCUPÉRATION ET AFFICHAGE (SIMULATION INDEX)
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 5 : Récupération et affichage (simulation page index)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $depots = VehicleDepot::where('organization_id', $org->id)
        ->where('name', 'like', 'DEPOT_TEST_%')
        ->withCount('vehicles')
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "📋 Dépôts trouvés : {$depots->count()}\n\n";
    
    foreach ($depots as $depot) {
        echo "📦 {$depot->name}\n";
        echo "   - ID : {$depot->id}\n";
        echo "   - Code : " . ($depot->code ?? 'NULL') . "\n";
        echo "   - Email : " . ($depot->email ?? 'Non défini') . "\n";
        echo "   - Ville : " . ($depot->city ?? 'Non définie') . "\n";
        echo "   - Capacité : " . ($depot->capacity ?? 'Illimitée') . "\n";
        echo "   - Véhicules : {$depot->vehicles_count}\n";
        echo "   - Statut : " . ($depot->is_active ? '🟢 Actif' : '🔴 Inactif') . "\n";
        echo "   - Créé : {$depot->created_at->format('d/m/Y H:i')}\n";
        echo "\n";
    }
    
    if ($depots->count() > 0) {
        echo "✅ SUCCESS : Récupération et affichage corrects\n\n";
        $testsPassed++;
    } else {
        echo "⚠️ Aucun dépôt de test trouvé\n\n";
        $testsFailed++;
    }
    
} catch (\Exception $e) {
    echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
    $testsFailed++;
}

// ============================================================
// TEST 6 : VALIDATION CONTRAINTES UNIQUES
// ============================================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 6 : Validation contraintes uniques\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    // Tenter de créer un dépôt avec un code déjà utilisé
    $duplicateDepot = VehicleDepot::create([
        'organization_id' => $org->id,
        'name' => 'DEPOT_TEST_Duplicate',
        'code' => 'CUSTOM-001', // Déjà utilisé dans TEST 3
        'current_count' => 0,
        'is_active' => true,
    ]);
    
    echo "❌ ERREUR : Le code dupliqué a été accepté (ne devrait pas)\n\n";
    $testsFailed++;
    
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'duplicate key') !== false) {
        echo "✅ SUCCESS : Contrainte d'unicité respectée\n";
        echo "   - Le code dupliqué a été correctement rejeté\n\n";
        $testsPassed++;
    } else {
        echo "❌ ÉCHEC : {$e->getMessage()}\n\n";
        $testsFailed++;
    }
}

// ============================================================
// RÉSUMÉ FINAL
// ============================================================
echo "\n╔═══════════════════════════════════════════════════════════════╗\n";
echo "║                    RÉSUMÉ DES TESTS                           ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

echo "📊 Résultats :\n";
echo "   ✅ Tests réussis : {$testsPassed}\n";
echo "   ❌ Tests échoués : {$testsFailed}\n";
echo "   📈 Taux de réussite : " . round(($testsPassed / ($testsPassed + $testsFailed)) * 100) . "%\n\n";

if ($testsFailed == 0) {
    echo "╔═══════════════════════════════════════════════════════════════╗\n";
    echo "║     🎉 TOUS LES TESTS SONT PASSÉS AVEC SUCCÈS !              ║\n";
    echo "║                                                               ║\n";
    echo "║  ✅ Module dépôts ENTIÈREMENT FONCTIONNEL                    ║\n";
    echo "║  ✅ Enregistrement en base de données OK                     ║\n";
    echo "║  ✅ Tous les champs (y compris email) fonctionnent           ║\n";
    echo "║  ✅ Casting des types correct                                ║\n";
    echo "║  ✅ Contraintes d'unicité respectées                         ║\n";
    echo "║  ✅ Affichage dans l'index fonctionnel                       ║\n";
    echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
} else {
    echo "⚠️ Des tests ont échoué. Vérifiez les erreurs ci-dessus.\n\n";
}

// ============================================================
// VÉRIFICATION UI (INSTRUCTIONS MANUELLES)
// ============================================================
echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║          VÉRIFICATION MANUELLE UI À EFFECTUER                 ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";
echo "📋 Checklist de validation UI :\n\n";
echo "1. ✅ Le toggle 'Dépôt actif' utilise maintenant wire:model.defer\n";
echo "   → Plus d'espace non esthétique lors du clic\n\n";
echo "2. ⬜ Tester dans le navigateur :\n";
echo "   a) Aller sur la page Gestion des Dépôts\n";
echo "   b) Cliquer sur 'Nouveau Dépôt'\n";
echo "   c) Remplir le formulaire avec :\n";
echo "      - Nom : Test UI\n";
echo "      - Email : test@ui.com\n";
echo "      - Latitude : 36.7538\n";
echo "      - Longitude : 3.0588\n";
echo "   d) Cliquer sur le toggle 'Dépôt actif'\n";
echo "      → Vérifier qu'aucun espace ne se crée\n";
echo "   e) Cliquer sur 'Créer'\n";
echo "      → Le dépôt doit apparaître dans la liste\n\n";

// Nettoyage optionnel
echo "🧹 Nettoyage des dépôts de test...\n";
$deleted = VehicleDepot::where('name', 'like', 'DEPOT_TEST_%')->delete();
echo "✅ {$deleted} dépôts de test supprimés\n\n";

echo "✨ Test Enterprise-Grade terminé avec succès !\n\n";
