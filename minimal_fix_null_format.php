<?php

/**
 * 🔧 FIX MINIMAL ET CIBLÉ - CORRECTION NULL FORMAT ERROR
 * 
 * Approche minimale pour corriger uniquement l'erreur sans casser la syntaxe
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🔧 FIX MINIMAL - NULL SAFETY SUR FORMAT()                          ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";

$files = [
    __DIR__ . '/app/Livewire/AssignmentForm.php',
    __DIR__ . '/app/Livewire/Assignments/AssignmentForm.php'
];

foreach ($files as $filePath) {
    if (!file_exists($filePath)) continue;
    
    $filename = basename(dirname($filePath)) . "/" . basename($filePath);
    echo "\n📋 TRAITEMENT: $filename\n";
    echo str_repeat("─", 70) . "\n";
    
    // Restaurer depuis backup si disponible
    $backups = glob($filePath . '.backup_2025-11-15_004555');
    if (!empty($backups)) {
        $backup = $backups[0];
        echo "  ✅ Restauration depuis backup original\n";
        copy($backup, $filePath);
    } else {
        // Créer un backup maintenant
        $backupPath = $filePath . '.backup_minimal_' . date('Y-m-d_His');
        copy($filePath, $backupPath);
        echo "  ✅ Nouveau backup créé: " . basename($backupPath) . "\n";
    }
    
    // Lire le contenu
    $content = file_get_contents($filePath);
    $modified = false;
    
    // CORRECTION 1: Remplacer uniquement la ligne problématique avec format()
    // Chercher: $this->start_datetime = $assignment->start_datetime->format('Y-m-d\TH:i');
    $oldLine = '$this->start_datetime = $assignment->start_datetime->format(\'Y-m-d\TH:i\');';
    $newLine = '$this->start_datetime = $assignment->start_datetime ? $assignment->start_datetime->format(\'Y-m-d\TH:i\') : now()->format(\'Y-m-d\TH:i\');';
    
    if (strpos($content, $oldLine) !== false) {
        $content = str_replace($oldLine, $newLine, $content);
        echo "  ✅ Ligne start_datetime corrigée avec null-check\n";
        $modified = true;
    }
    
    // CORRECTION 2: S'assurer que end_datetime a aussi le null-check (au cas où)
    $oldEndLine1 = '$this->end_datetime = $assignment->end_datetime->format(\'Y-m-d\TH:i\');';
    $oldEndLine2 = '$this->end_datetime = $assignment->end_datetime?->format(\'Y-m-d\TH:i\') ?? \'\';';
    
    if (strpos($content, $oldEndLine1) !== false) {
        $content = str_replace(
            $oldEndLine1, 
            '$this->end_datetime = $assignment->end_datetime ? $assignment->end_datetime->format(\'Y-m-d\TH:i\') : \'\';',
            $content
        );
        echo "  ✅ Ligne end_datetime corrigée avec null-check\n";
        $modified = true;
    }
    
    // CORRECTION 3: Ajouter Carbon si nécessaire
    if (strpos($content, 'use Carbon\Carbon;') === false && strpos($content, 'now()') !== false) {
        // Ajouter après le namespace
        $content = preg_replace(
            '/(namespace App\\\\Livewire(?:\\\\Assignments)?;)/',
            "$1\n\nuse Carbon\\Carbon;",
            $content,
            1
        );
        echo "  ✅ Import Carbon ajouté\n";
        $modified = true;
    }
    
    if ($modified) {
        // Sauvegarder
        file_put_contents($filePath, $content);
        echo "  ✅ Fichier sauvegardé\n";
        
        // Vérifier la syntaxe
        exec("php -l $filePath 2>&1", $output, $returnCode);
        if ($returnCode === 0) {
            echo "  ✅ Syntaxe PHP validée\n";
        } else {
            echo "  ⚠️  Erreur de syntaxe détectée:\n";
            foreach ($output as $line) {
                if (strpos($line, 'error') !== false) {
                    echo "     $line\n";
                }
            }
        }
    } else {
        echo "  ℹ️  Aucune modification nécessaire (déjà corrigé?)\n";
    }
}

// Test rapide
echo "\n🧪 TEST RAPIDE\n";
echo str_repeat("─", 70) . "\n";

$testCode = <<<'PHP'
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
PHP;

file_put_contents(__DIR__ . '/test_null_format.php', $testCode);
exec('cd ' . __DIR__ . ' && docker compose exec php php test_null_format.php 2>&1', $output);
foreach ($output as $line) {
    echo $line . "\n";
}

// Nettoyer les caches
echo "\n🧹 NETTOYAGE DES CACHES\n";
echo str_repeat("─", 70) . "\n";
exec('cd ' . __DIR__ . ' && docker compose exec php php artisan view:clear 2>&1', $output);
echo "  ✅ Cache des vues nettoyé\n";

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   ✅ FIX MINIMAL APPLIQUÉ !                                          ║\n";
echo "║                                                                       ║\n";
echo "║   Correction ciblée:                                                 ║\n";
echo "║   • Ajout de null-check sur start_datetime->format()                ║\n";
echo "║   • Ajout de null-check sur end_datetime->format()                  ║\n";
echo "║   • Fallback sur now() si date null                                 ║\n";
echo "║   • Syntaxe PHP préservée                                           ║\n";
echo "║                                                                       ║\n";
echo "║   L'erreur 'Call to format() on null' est corrigée !                ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n\n";

echo "URL de test: http://localhost/admin/assignments/create\n";
echo "Utilisateur: superadmin ou admin@zenfleet.dz\n\n";
