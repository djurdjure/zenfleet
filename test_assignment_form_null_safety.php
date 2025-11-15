<?php

use App\Models\Assignment;
use App\Models\User;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n🧪 TEST DE NULL-SAFETY ASSIGNMENTFORM\n";
echo str_repeat("─", 70) . "\n";

// Créer une affectation de test avec dates null
$testAssignment = new Assignment([
    'vehicle_id' => 1,
    'driver_id' => 1,
    'start_datetime' => null, // NULL pour tester
    'end_datetime' => null,
    'reason' => 'Test',
    'organization_id' => 1
]);

// Tester la création d'un formulaire avec cette affectation
try {
    $user = User::whereHas('roles', function($q) {
        $q->whereIn('name', ['Super Admin', 'Admin']);
    })->first();
    
    if ($user) {
        auth()->login($user);
        
        // Simuler l'appel au composant
        $component = new \App\Livewire\AssignmentForm();
        
        // Appeler fillFromAssignment via reflection
        $reflection = new ReflectionClass($component);
        $method = $reflection->getMethod('fillFromAssignment');
        $method->setAccessible(true);
        
        // Test avec affectation ayant des dates null
        $method->invoke($component, $testAssignment);
        
        echo "  ✅ Test avec start_datetime=null: SUCCÈS\n";
        echo "     start_datetime défini à: " . $component->start_datetime . "\n";
        
        // Test avec dates valides
        $testAssignment->start_datetime = Carbon::now();
        $testAssignment->end_datetime = Carbon::now()->addHours(2);
        $method->invoke($component, $testAssignment);
        
        echo "  ✅ Test avec dates valides: SUCCÈS\n";
        echo "     start_datetime: " . $component->start_datetime . "\n";
        echo "     end_datetime: " . $component->end_datetime . "\n";
        
    } else {
        echo "  ⚠️  Aucun utilisateur admin trouvé pour les tests\n";
    }
    
} catch (\Exception $e) {
    echo "  ❌ Erreur durant le test: " . $e->getMessage() . "\n";
}

echo "\n✅ Tests terminés\n";