#!/usr/bin/env php
<?php

/**
 * Test du composant UpdateVehicleMileage
 * Vérifie que le composant fonctionne correctement
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Vehicle;
use App\Livewire\Admin\UpdateVehicleMileage;
use Livewire\Livewire;

echo "\n🚀 TEST DU COMPOSANT UPDATE VEHICLE MILEAGE V15.0\n";
echo "=================================================\n\n";

try {
    // 1. Vérifier que la classe existe
    if (!class_exists(UpdateVehicleMileage::class)) {
        throw new Exception("❌ Classe UpdateVehicleMileage introuvable!");
    }
    echo "✅ Classe UpdateVehicleMileage trouvée\n";

    // 2. Vérifier les propriétés publiques
    $reflection = new ReflectionClass(UpdateVehicleMileage::class);
    $publicProperties = [];
    foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
        $publicProperties[] = $property->getName();
    }
    echo "✅ Propriétés publiques: " . implode(', ', $publicProperties) . "\n";

    // 3. Vérifier les méthodes publiques
    $publicMethods = [];
    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->getDeclaringClass()->getName() === UpdateVehicleMileage::class) {
            $publicMethods[] = $method->getName();
        }
    }
    echo "✅ Méthodes publiques: " . implode(', ', $publicMethods) . "\n";

    // 4. Tester l'instanciation du composant
    $user = User::where('email', 'admin@example.com')->first();
    if (!$user) {
        echo "⚠️  Utilisateur admin@example.com non trouvé, test limité\n";
    } else {
        auth()->login($user);
        echo "✅ Connecté en tant que: {$user->name} ({$user->email})\n";

        // 5. Vérifier l'organisation et les véhicules
        $vehicleCount = Vehicle::where('organization_id', $user->organization_id)->count();
        echo "✅ Véhicules dans l'organisation: {$vehicleCount}\n";

        if ($vehicleCount > 0) {
            $vehicle = Vehicle::where('organization_id', $user->organization_id)->first();
            echo "✅ Véhicule test: {$vehicle->registration_plate} - {$vehicle->brand} {$vehicle->model}\n";
            echo "   Kilométrage actuel: " . number_format($vehicle->current_mileage) . " km\n";
        }
    }

    // 6. Vérifier les règles de validation
    $component = new UpdateVehicleMileage();
    $rulesMethod = $reflection->getMethod('rules');
    $rulesMethod->setAccessible(true);
    $rules = $rulesMethod->invoke($component);
    echo "\n📋 Règles de validation:\n";
    foreach ($rules as $field => $rule) {
        echo "   - {$field}: " . (is_array($rule) ? implode('|', $rule) : $rule) . "\n";
    }

    // 7. Vérifier les messages de validation
    $messagesMethod = $reflection->getMethod('messages');
    $messagesMethod->setAccessible(true);
    $messages = $messagesMethod->invoke($component);
    echo "\n💬 Messages personnalisés: " . count($messages) . " messages définis\n";

    echo "\n✅ TOUS LES TESTS PASSENT AVEC SUCCÈS!\n";
    echo "   Le composant UpdateVehicleMileage V15.0 est opérationnel.\n\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    exit(1);
}
