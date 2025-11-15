<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 TEST GATE AVEC AUTH() - DEBUG FINAL                    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$user = User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur introuvable\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->name} ({$user->email})\n";
echo "👑 Rôle: " . $user->roles->pluck('name')->implode(', ') . "\n\n";

// Simuler l'authentification
Auth::login($user);
echo "✅ Utilisateur connecté via Auth::login()\n\n";

// Test 1: Via auth()->user()
echo "TEST 1: Via auth()->user()\n";
echo "  • auth()->check(): " . (auth()->check() ? '✅' : '❌') . "\n";
echo "  • auth()->id(): " . (auth()->id() ?? 'NULL') . "\n";
echo "  • auth()->user()->can('assignments.create'): " . (auth()->user()->can('assignments.create') ? '✅' : '❌') . "\n\n";

// Test 2: Via Gate directement
echo "TEST 2: Via Gate::allows()\n";
$result1 = Gate::allows('create', Assignment::class);
echo "  • Gate::allows('create', Assignment::class): " . ($result1 ? '✅' : '❌') . "\n\n";

// Test 3: Via Gate::forUser()
echo "TEST 3: Via Gate::forUser()\n";
$result2 = Gate::forUser($user)->allows('create', Assignment::class);
echo "  • Gate::forUser(\$user)->allows('create', Assignment::class): " . ($result2 ? '✅' : '❌') . "\n\n";

// Test 4: Vérifier quel Policy method est appelée
echo "TEST 4: Inspection de la Policy resolution\n";
try {
    $policy = Gate::getPolicyFor(Assignment::class);
    echo "  • Policy trouvée: " . ($policy ? get_class($policy) : 'NULL') . "\n";

    if ($policy) {
        $reflection = new ReflectionMethod($policy, 'create');
        $file = $reflection->getFileName();
        $line = $reflection->getStartLine();
        echo "  • Fichier: {$file}\n";
        echo "  • Ligne: {$line}\n";

        // Lire le code
        $lines = file($file);
        $methodCode = '';
        for ($i = $line - 1; $i < $line + 5 && $i < count($lines); $i++) {
            $methodCode .= ($i + 1) . ': ' . $lines[$i];
        }
        echo "  • Code:\n" . $methodCode . "\n";
    }
} catch (Exception $e) {
    echo "  ❌ Erreur: {$e->getMessage()}\n";
}

// Test 5: Appeler directement la méthode authorize comme Livewire
echo "\nTEST 5: Simulation \$this->authorize() Livewire\n";
try {
    // C'est EXACTEMENT ce que Livewire fait
    app(\Illuminate\Contracts\Auth\Access\Gate::class)->authorize('create', Assignment::class);
    echo "  ✅ AUTORISÉ - Pas d'exception\n";
} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "  ❌ REFUSÉ - Exception: {$e->getMessage()}\n";
}

echo "\n";
