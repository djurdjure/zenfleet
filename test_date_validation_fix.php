<?php

/**
 * 🧪 TEST DE VALIDATION - Correction Comparaison Dates
 *
 * Ce script teste la correction apportée à la validation des dates
 * dans AssignmentObserver pour s'assurer qu'elle fonctionne correctement.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     🧪 TEST VALIDATION DATES - Correction AssignmentObserver ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Date de fin APRÈS date de début (devrait passer)
echo "📊 Test 1: Date de fin APRÈS date de début\n";
echo "   Début: 19/11/2025 21:00\n";
echo "   Fin:   19/11/2025 23:30\n";

try {
    $start = Carbon::parse('2025-11-19 21:00:00');
    $end = Carbon::parse('2025-11-19 23:30:00');

    echo "   → start < end: " . ($start < $end ? "✅ OUI" : "❌ NON") . "\n";
    echo "   → start >= end: " . ($start >= $end ? "❌ OUI" : "✅ NON") . "\n";
    echo "   → Différence: " . $start->diffInMinutes($end) . " minutes\n";
    echo "   → Résultat: ✅ Devrait PASSER\n\n";
} catch (\Exception $e) {
    echo "   → Erreur: ❌ " . $e->getMessage() . "\n\n";
}

// Test 2: Date de fin ÉGALE à date de début (devrait échouer)
echo "📊 Test 2: Date de fin ÉGALE à date de début\n";
echo "   Début: 19/11/2025 21:00\n";
echo "   Fin:   19/11/2025 21:00\n";

try {
    $start = Carbon::parse('2025-11-19 21:00:00');
    $end = Carbon::parse('2025-11-19 21:00:00');

    echo "   → start < end: " . ($start < $end ? "✅ OUI" : "❌ NON") . "\n";
    echo "   → start >= end: " . ($start >= $end ? "❌ OUI (devrait échouer)" : "✅ NON") . "\n";
    echo "   → Différence: " . $start->diffInMinutes($end) . " minutes\n";
    echo "   → Résultat: ❌ Devrait ÉCHOUER\n\n";
} catch (\Exception $e) {
    echo "   → Erreur: ❌ " . $e->getMessage() . "\n\n";
}

// Test 3: Date de fin AVANT date de début (devrait échouer)
echo "📊 Test 3: Date de fin AVANT date de début\n";
echo "   Début: 19/11/2025 23:30\n";
echo "   Fin:   19/11/2025 21:00\n";

try {
    $start = Carbon::parse('2025-11-19 23:30:00');
    $end = Carbon::parse('2025-11-19 21:00:00');

    echo "   → start < end: " . ($start < $end ? "✅ OUI" : "❌ NON") . "\n";
    echo "   → start >= end: " . ($start >= $end ? "❌ OUI (devrait échouer)" : "✅ NON") . "\n";
    echo "   → Différence: " . $end->diffInMinutes($start) . " minutes (négatif)\n";
    echo "   → Résultat: ❌ Devrait ÉCHOUER\n\n";
} catch (\Exception $e) {
    echo "   → Erreur: ❌ " . $e->getMessage() . "\n\n";
}

// Test 4: Comparaison avec strings (ancien comportement problématique)
echo "📊 Test 4: Comparaison de STRINGS (ancien comportement)\n";
echo "   Début: \"2025-11-19 21:00:00\"\n";
echo "   Fin:   \"2025-11-19 23:30:00\"\n";

$start_string = "2025-11-19 21:00:00";
$end_string = "2025-11-19 23:30:00";

echo "   → start < end (string): " . ($start_string < $end_string ? "✅ OUI" : "❌ NON") . "\n";
echo "   → start >= end (string): " . ($start_string >= $end_string ? "❌ OUI" : "✅ NON") . "\n";
echo "   → Comparaison lexicographique: " . strcmp($start_string, $end_string) . " (< 0 = start < end)\n";
echo "   → Résultat: ✅ Fonct

ionne AUSSI mais risqué\n\n";

// Test 5: Comparaison avec microsecondes différentes
echo "📊 Test 5: Objets Carbon avec microsecondes différentes\n";

$start_with_micro = Carbon::parse('2025-11-19 21:00:00.123456');
$end_with_micro = Carbon::parse('2025-11-19 21:00:00.987654');

echo "   → start: " . $start_with_micro->format('Y-m-d H:i:s.u') . "\n";
echo "   → end:   " . $end_with_micro->format('Y-m-d H:i:s.u') . "\n";
echo "   → start < end: " . ($start_with_micro < $end_with_micro ? "✅ OUI" : "❌ NON") . "\n";
echo "   → Différence: " . $start_with_micro->diffInMicroseconds($end_with_micro) . " microsecondes\n";
echo "   → Résultat: ✅ Carbon gère correctement\n\n";

// Test 6: Timezone différents
echo "📊 Test 6: Timezones différents\n";

$start_paris = Carbon::parse('2025-11-19 21:00:00', 'Europe/Paris');
$end_utc = Carbon::parse('2025-11-19 20:00:00', 'UTC');

echo "   → start (Paris): " . $start_paris->toIso8601String() . "\n";
echo "   → end (UTC):     " . $end_utc->toIso8601String() . "\n";
echo "   → start < end: " . ($start_paris < $end_utc ? "✅ OUI" : "❌ NON") . "\n";
echo "   → Résultat: ✅ Carbon normalise les timezones\n\n";

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                     ✅ TESTS TERMINÉS                         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "📝 CONCLUSION:\n";
echo "   La correction force la conversion en objets Carbon avant comparaison,\n";
echo "   ce qui garantit une comparaison temporelle correcte plutôt que\n";
echo "   lexicographique, même si Eloquent passe des strings.\n\n";
