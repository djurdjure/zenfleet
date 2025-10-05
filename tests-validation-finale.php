#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\RepairRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════╗\n";
echo "║  VALIDATION FINALE - SYSTÈME PERMISSIONS       ║\n";
echo "║  ZenFleet Enterprise - Module Réparations      ║\n";
echo "╚════════════════════════════════════════════════╝\n\n";

$tests = [
    [
        'email' => 'superadmin@zenfleet.dz',
        'expected_role' => 'Super Admin',
        'should_see_menu' => true,
        'team_id_should_be_null' => true,
    ],
    [
        'email' => 'fleet@zenfleet.dz',
        'expected_role' => 'Gestionnaire Flotte',
        'should_see_menu' => true,
        'team_id_should_be_null' => false,
    ],
    [
        'email' => 'supervisor@zenfleet.dz',
        'expected_role' => 'Supervisor',
        'should_see_menu' => true,
        'team_id_should_be_null' => false,
    ],
    [
        'email' => 'driver@zenfleet.dz',
        'expected_role' => 'Chauffeur',
        'should_see_menu' => true,
        'team_id_should_be_null' => false,
    ],
];

$passed = 0;
$failed = 0;

foreach ($tests as $test) {
    $user = User::where('email', $test['email'])->first();

    if (!$user) {
        echo "❌ User not found: {$test['email']}\n";
        $failed++;
        continue;
    }

    Auth::login($user);

    echo "📝 Test: {$test['expected_role']}\n";

    // Test 1: Rôle correct
    $hasCorrectRole = $user->hasRole($test['expected_role']);
    echo "   1. Rôle assigné: " . ($hasCorrectRole ? "✅ PASS" : "❌ FAIL") . "\n";

    // Test 2: Menu visible
    $canSeeMenu = $user->can('view all repair requests')
        || $user->can('view team repair requests')
        || $user->can('view own repair requests');
    $menuTest = $canSeeMenu == $test['should_see_menu'];
    echo "   2. Menu visible: " . ($menuTest ? "✅ PASS" : "❌ FAIL") . "\n";

    // Test 3: Policy viewAny
    $canViewAny = $user->can('viewAny', RepairRequest::class);
    echo "   3. Policy viewAny: " . ($canViewAny ? "✅ PASS" : "❌ FAIL") . "\n";

    // Test 4: Team ID correct
    $roleAssignment = DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', 'App\Models\User')
        ->first();

    if ($test['team_id_should_be_null']) {
        $teamIdCorrect = $roleAssignment && $roleAssignment->organization_id === null;
        echo "   4. Team ID (NULL): " . ($teamIdCorrect ? "✅ PASS" : "❌ FAIL") . "\n";
    } else {
        $teamIdCorrect = $roleAssignment && $roleAssignment->organization_id == $user->organization_id;
        echo "   4. Team ID ({$user->organization_id}): " . ($teamIdCorrect ? "✅ PASS" : "❌ FAIL") . "\n";
    }

    $allPassed = $hasCorrectRole && $menuTest && $canViewAny && $teamIdCorrect;

    if ($allPassed) {
        echo "   ✅ TOUS LES TESTS PASSÉS\n";
        $passed++;
    } else {
        echo "   ❌ ÉCHEC\n";
        $failed++;
    }

    echo "\n";
    Auth::logout();
}

echo "═══════════════════════════════════════════════\n";
echo "RÉSULTAT FINAL:\n";
echo "  ✅ Passés: $passed\n";
echo "  ❌ Échoués: $failed\n\n";

if ($failed === 0) {
    echo "🎉 VALIDATION RÉUSSIE - SYSTÈME 100% FONCTIONNEL\n";
    exit(0);
} else {
    echo "⚠️  ATTENTION: Des tests ont échoué\n";
    exit(1);
}
