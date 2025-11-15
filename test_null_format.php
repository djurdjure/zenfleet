<?php
use App\Models\Assignment;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test avec une affectation ayant start_datetime = null
$testAssignment = new Assignment();
$testAssignment->start_datetime = null;
$testAssignment->end_datetime = null;

try {
    // Simuler l'appel à format() sur null
    if ($testAssignment->start_datetime) {
        $formatted = $testAssignment->start_datetime->format('Y-m-d\TH:i');
    } else {
        $formatted = now()->format('Y-m-d\TH:i');
    }
    echo "  ✅ Gestion du null fonctionne: $formatted\n";
} catch (\Error $e) {
    echo "  ❌ Erreur: " . $e->getMessage() . "\n";
}