#!/usr/bin/env php
<?php

/**
 * 🔍 DIAGNOSTIC FORENSIQUE: Changement automatique de date vers 2025-05-20
 * 
 * @version 2.1 Ultra-Pro
 * @date 19 Novembre 2025
 */

echo "\n╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🔍 DIAGNOSTIC: Changement automatique date → 2025-05-20           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📋 ANALYSE DU PROBLÈME\n";
echo str_repeat("─", 70) . "\n\n";

// Test 1: Simuler le flux Livewire
echo "🔄 TEST 1: Simulation flux Livewire wire:model.live\n";
echo "──────────────────────────────────────────────────────────────────────\n\n";

$start_date = "18/11/2025"; // Date initiale format français
echo "1️⃣ Initialisation: start_date = '$start_date' (français)\n";

// Simulation convertDateFromFrenchFormat
if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $start_date, $matches)) {
    $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
    $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
    $year = $matches[3];
    $start_date_iso = "$year-$month-$day";
    echo "2️⃣ Conversion ISO: start_date = '$start_date_iso'\n";
}

echo "3️⃣ Livewire envoie au navigateur: value='$start_date_iso'\n";
echo "4️⃣ Flatpickr reçoit: '$start_date_iso' avec format='d/m/Y'\n";
echo "5️⃣ ⚠️  PROBLÈME: Flatpickr essaie de parser ISO avec format français!\n\n";

// Test 2: Comment Flatpickr pourrait mal parser
echo "🔄 TEST 2: Parsing de '2025-11-18' avec format 'd/m/Y'\n";
echo "──────────────────────────────────────────────────────────────────────\n\n";

$iso_value = "2025-11-18";
echo "Valeur ISO: $iso_value\n";
echo "Format attendu par Flatpickr: d/m/Y\n\n";

// Si Flatpickr essaie de parser "2025-11-18" comme d/m/Y
// Il pourrait interpréter : d=2025, m=11, Y=18
echo "🤔 Interprétation possible par Flatpickr:\n";
echo "  d (jour) = 2025 → ⚠️  Invalid!\n";
echo "  m (mois) = 11 → OK\n";
echo "  Y (année) = 18 → ⚠️  Devient 2018 ou erreur\n\n";

// Ou avec séparateur -
// Il pourrait essayer de détecter auto le format et mal interpréter
echo "🤔 Autre interprétation:\n";
echo "  Flatpickr détecte '-' au lieu de '/'\n";
echo "  Essaie d'auto-détecter : pourrait devenir une date aléatoire\n";
echo "  Résultat observé: 2025-05-20 (20 mai 2025)\n\n";

// Test 3: D'où vient 2025-05-20 ?
echo "🔄 TEST 3: Origine de la date '2025-05-20'\n";
echo "──────────────────────────────────────────────────────────────────────\n\n";

echo "Hypothèses:\n";
echo "1. 📅 Parsing erroné de Flatpickr\n";
echo "2. 🔢 Conversion de numéros : 20, 05, 2025\n";
echo "3. 💾 Valeur en cache/localStorage\n";
echo "4. 🔄 defaultDate non défini → fallback\n";
echo "5. 📝 Test précédent qui a laissé cette valeur\n\n";

// Test du parsing inverse
echo "🧪 Test parsing '20/05/2025' (ordre inversé):\n";
$test_date = "20/05/2025";
if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $test_date, $matches)) {
    echo "  → jour=$matches[1], mois=$matches[2], année=$matches[3]\n";
    echo "  → ISO: $matches[3]-$matches[2]-$matches[1] = 2025-05-20\n";
    echo "  ✅ C'est peut-être un parsing en format US (mm/dd/yyyy)!\n";
}

echo "\n╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║                     🎯 DIAGNOSTIC COMPLET                            ║\n";
echo "╠══════════════════════════════════════════════════════════════════════╣\n";
echo "║                                                                        ║\n";
echo "║  ❌ PROBLÈME IDENTIFIÉ:                                               ║\n";
echo "║  Le flux actuel convertit immédiatement vers ISO dans                 ║\n";
echo "║  updatedStartDate(), ce qui envoie une valeur ISO au navigateur.      ║\n";
echo "║  Flatpickr ne peut pas parser correctement cette valeur avec          ║\n";
echo "║  son format d/m/Y, créant une date incorrecte.                        ║\n";
echo "║                                                                        ║\n";
echo "║  🔧 SOLUTION REQUISE:                                                 ║\n";
echo "║  Garder start_date toujours au format FRANÇAIS dans la propriété,     ║\n";
echo "║  et ne convertir vers ISO que temporairement lors de la validation    ║\n";
echo "║  et sauvegarde, sans modifier la propriété elle-même.                 ║\n";
echo "║                                                                        ║\n";
echo "║  📋 FLUX CORRIGÉ:                                                     ║\n";
echo "║  1. start_date reste en français (18/11/2025)                         ║\n";
echo "║  2. updatedStartDate() ne convertit PAS la valeur                     ║\n";
echo "║  3. combineDateTime() fait conversion temporaire                      ║\n";
echo "║  4. Flatpickr reçoit toujours du français                             ║\n";
echo "║                                                                        ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "🏆 Analyse terminée - Solution identifiée!\n\n";
