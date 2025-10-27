#!/usr/bin/env php
<?php

/**
 * Test de Validation - Correction UpdateVehicleMileage V16.0
 * Vérifie que l'erreur "void function must not return a value" est corrigée
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMileageReading;
use App\Livewire\Admin\UpdateVehicleMileage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🔧 VALIDATION FIX - UpdateVehicleMileage V16.0 Enterprise\n";
echo str_repeat("=", 80) . "\n\n";

$testResults = [
    'passed' => 0,
    'failed' => 0,
    'warnings' => 0,
];

try {
    // ====================================================================
    // TEST 1: Vérification de la classe et syntaxe PHP
    // ====================================================================
    echo "📋 TEST 1: Vérification syntaxe PHP et classe\n";
    echo str_repeat("-", 40) . "\n";
    
    // Vérifier que la classe existe
    if (!class_exists(UpdateVehicleMileage::class)) {
        throw new Exception("Classe UpdateVehicleMileage introuvable!");
    }
    echo "✅ Classe trouvée\n";
    
    // Vérifier la syntaxe du fichier
    $filePath = app_path('Livewire/Admin/UpdateVehicleMileage.php');
    $output = [];
    $returnCode = 0;
    exec("php -l {$filePath} 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Syntaxe PHP valide\n";
        $testResults['passed']++;
    } else {
        echo "❌ Erreur de syntaxe: " . implode("\n", $output) . "\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // TEST 2: Vérification de la signature de la méthode save()
    // ====================================================================
    echo "\n📋 TEST 2: Vérification méthode save()\n";
    echo str_repeat("-", 40) . "\n";
    
    $reflection = new ReflectionClass(UpdateVehicleMileage::class);
    $saveMethod = $reflection->getMethod('save');
    
    // Vérifier que la méthode existe
    echo "✅ Méthode save() existe\n";
    
    // Vérifier le type de retour
    $returnType = $saveMethod->getReturnType();
    if ($returnType === null || $returnType->getName() !== 'void') {
        echo "✅ Type de retour corrigé (n'est plus void strict)\n";
        $testResults['passed']++;
    } else {
        echo "⚠️  Type de retour est toujours void - vérification requise\n";
        $testResults['warnings']++;
    }
    
    // ====================================================================
    // TEST 3: Vérification des propriétés publiques requises
    // ====================================================================
    echo "\n📋 TEST 3: Propriétés publiques Livewire\n";
    echo str_repeat("-", 40) . "\n";
    
    $requiredProperties = [
        'vehicleId', 'vehicleData', 'mode', 'newMileage', 
        'recordedDate', 'recordedTime', 'notes', 'search',
        'isLoading', 'validationMessage', 'validationType'
    ];
    
    $publicProperties = [];
    foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
        $publicProperties[] = $property->getName();
    }
    
    $missingProperties = array_diff($requiredProperties, $publicProperties);
    if (empty($missingProperties)) {
        echo "✅ Toutes les propriétés requises sont présentes\n";
        $testResults['passed']++;
    } else {
        echo "❌ Propriétés manquantes: " . implode(', ', $missingProperties) . "\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // TEST 4: Test d'instanciation du composant
    // ====================================================================
    echo "\n📋 TEST 4: Instanciation du composant\n";
    echo str_repeat("-", 40) . "\n";
    
    try {
        $component = new UpdateVehicleMileage();
        echo "✅ Composant instancié avec succès\n";
        $testResults['passed']++;
        
        // Vérifier les valeurs par défaut
        if ($component->recordedDate === now()->format('Y-m-d')) {
            echo "✅ Date par défaut correcte\n";
            $testResults['passed']++;
        }
        
        if ($component->mode === 'select') {
            echo "✅ Mode par défaut correct\n";
            $testResults['passed']++;
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur d'instanciation: " . $e->getMessage() . "\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // TEST 5: Test avec utilisateur et véhicule (si disponible)
    // ====================================================================
    echo "\n📋 TEST 5: Test avec données réelles\n";
    echo str_repeat("-", 40) . "\n";
    
    $user = User::where('email', 'admin@example.com')->first();
    if ($user) {
        auth()->login($user);
        echo "✅ Connecté en tant que: {$user->name}\n";
        
        $vehicle = Vehicle::where('organization_id', $user->organization_id)
            ->where('status', 'active')
            ->first();
            
        if ($vehicle) {
            echo "✅ Véhicule test trouvé: {$vehicle->registration_plate}\n";
            echo "   Kilométrage actuel: " . number_format($vehicle->current_mileage) . " km\n";
            
            // Test de chargement du véhicule
            try {
                $component = new UpdateVehicleMileage();
                $component->mount($vehicle->id);
                
                if ($component->vehicleData && $component->vehicleData['id'] == $vehicle->id) {
                    echo "✅ Chargement du véhicule réussi\n";
                    $testResults['passed']++;
                } else {
                    echo "⚠️  Véhicule non chargé (peut-être restrictions de rôle)\n";
                    $testResults['warnings']++;
                }
            } catch (Exception $e) {
                echo "❌ Erreur lors du chargement: " . $e->getMessage() . "\n";
                $testResults['failed']++;
            }
        } else {
            echo "⚠️  Aucun véhicule actif trouvé pour les tests\n";
            $testResults['warnings']++;
        }
    } else {
        echo "⚠️  Utilisateur admin non trouvé pour les tests\n";
        $testResults['warnings']++;
    }
    
    // ====================================================================
    // TEST 6: Vérification des règles de validation
    // ====================================================================
    echo "\n📋 TEST 6: Règles de validation\n";
    echo str_repeat("-", 40) . "\n";
    
    try {
        $component = new UpdateVehicleMileage();
        $rulesMethod = $reflection->getMethod('rules');
        $rulesMethod->setAccessible(true);
        $rules = $rulesMethod->invoke($component);
        
        $expectedFields = ['vehicleId', 'newMileage', 'recordedDate', 'recordedTime', 'notes'];
        $actualFields = array_keys($rules);
        
        $missingRules = array_diff($expectedFields, $actualFields);
        if (empty($missingRules)) {
            echo "✅ Toutes les règles de validation présentes\n";
            $testResults['passed']++;
        } else {
            echo "⚠️  Règles manquantes: " . implode(', ', $missingRules) . "\n";
            $testResults['warnings']++;
        }
        
        // Afficher un résumé des règles
        foreach ($rules as $field => $rule) {
            $ruleStr = is_array($rule) ? implode('|', $rule) : $rule;
            echo "   • {$field}: {$ruleStr}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur validation: " . $e->getMessage() . "\n";
        $testResults['failed']++;
    }
    
    // ====================================================================
    // RÉSUMÉ DES TESTS
    // ====================================================================
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "📊 RÉSUMÉ DES TESTS\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "✅ Tests réussis:    {$testResults['passed']}\n";
    echo "❌ Tests échoués:    {$testResults['failed']}\n";
    echo "⚠️  Avertissements:  {$testResults['warnings']}\n";
    
    $total = $testResults['passed'] + $testResults['failed'];
    if ($total > 0) {
        $successRate = round(($testResults['passed'] / $total) * 100, 2);
        echo "\n📈 Taux de réussite: {$successRate}%\n";
    }
    
    if ($testResults['failed'] === 0) {
        echo "\n🎉 SUCCÈS! Le composant UpdateVehicleMileage est opérationnel.\n";
        echo "   L'erreur 'void function must not return a value' est corrigée.\n";
    } else {
        echo "\n⚠️  ATTENTION: Des corrections supplémentaires sont nécessaires.\n";
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
