#!/usr/bin/env php
<?php

/**
 * 🧪 TEST DE SYNTAXE - Vérification rapide
 *
 * Teste la syntaxe PHP des scripts de correction
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  🧪 TEST DE SYNTAXE PHP - SCRIPTS DE CORRECTION           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$scripts = [
    'fix_driver_statuses_v2.php',
    'test_permissions.php',
    'validate_fixes.php',
];

$allOk = true;

foreach ($scripts as $script) {
    echo "📝 Test: {$script}... ";

    if (!file_exists($script)) {
        echo "❌ Fichier introuvable\n";
        $allOk = false;
        continue;
    }

    // Vérification de la syntaxe avec php -l
    $output = [];
    $returnCode = 0;
    exec("php -l {$script} 2>&1", $output, $returnCode);

    if ($returnCode === 0) {
        echo "✅ Syntaxe correcte\n";
    } else {
        echo "❌ Erreur de syntaxe\n";
        echo "   " . implode("\n   ", $output) . "\n";
        $allOk = false;
    }
}

echo "\n";

if ($allOk) {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ✅ TOUS LES SCRIPTS ONT UNE SYNTAXE VALIDE               ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "🚀 Vous pouvez maintenant exécuter ./fix_all.sh --auto\n";
    echo "\n";
    exit(0);
} else {
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║  ❌ ERREURS DE SYNTAXE DÉTECTÉES                           ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "⚠️  Corrigez les erreurs ci-dessus avant de continuer\n";
    echo "\n";
    exit(1);
}
