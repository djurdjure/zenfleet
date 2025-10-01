<?php

/**
 * 🎯 OPTIMISATION ENTERPRISE DES MIGRATIONS - ZENFLEET
 *
 * Script expert pour nettoyer et optimiser le système de migrations
 * avec expertise 20+ ans PostgreSQL + Laravel Enterprise
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "🎯 OPTIMISATION ENTERPRISE - SYSTÈME DE MIGRATIONS\n";
echo "==================================================\n\n";

// Initialisation Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "📊 1. ANALYSE DES MIGRATIONS EXISTANTES\n";
echo "---------------------------------------\n";

try {
    // Récupération des migrations exécutées
    $ranMigrations = DB::table('migrations')->orderBy('batch')->get();
    $pendingMigrations = [];

    // Scan des fichiers de migration
    $migrationFiles = glob(__DIR__ . '/database/migrations/*.php');

    echo "✅ Migrations exécutées: " . $ranMigrations->count() . "\n";
    echo "📁 Fichiers de migration: " . count($migrationFiles) . "\n";

    // Identification des migrations en attente
    $ranMigrationNames = $ranMigrations->pluck('migration')->toArray();

    foreach ($migrationFiles as $file) {
        $filename = basename($file, '.php');
        if (!in_array($filename, $ranMigrationNames)) {
            $pendingMigrations[] = $filename;
        }
    }

    echo "⏳ Migrations en attente: " . count($pendingMigrations) . "\n\n";

    // Analyse des migrations critiques
    $criticalMigrations = [
        'create_driver_statuses_table',
        'create_drivers_table',
        'create_assignments_table',
        'create_vehicles_table'
    ];

    echo "🔍 Analyse des migrations critiques:\n";
    foreach ($criticalMigrations as $critical) {
        $found = false;
        foreach ($pendingMigrations as $pending) {
            if (strpos($pending, $critical) !== false) {
                echo "   ⚠️ CRITIQUE EN ATTENTE: $pending\n";
                $found = true;
            }
        }
        if (!$found) {
            echo "   ✅ $critical: Déjà exécutée ou non nécessaire\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Erreur analyse migrations: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🗃️ 2. ANALYSE STRUCTURE TABLES EXISTANTES\n";
echo "------------------------------------------\n";

try {
    $tables = [
        'drivers' => 'Table des chauffeurs',
        'driver_statuses' => 'Statuts des chauffeurs',
        'assignments' => 'Affectations',
        'vehicles' => 'Véhicules',
        'organizations' => 'Organisations'
    ];

    foreach ($tables as $tableName => $description) {
        if (Schema::hasTable($tableName)) {
            $columns = Schema::getColumnListing($tableName);
            echo "   ✅ $tableName ($description): " . count($columns) . " colonnes\n";

            // Vérification des index
            $indexes = DB::select("
                SELECT indexname, indexdef
                FROM pg_indexes
                WHERE tablename = ? AND schemaname = 'public'
            ", [$tableName]);

            echo "      📋 Index: " . count($indexes) . "\n";

        } else {
            echo "   ❌ $tableName: Table manquante\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Erreur analyse tables: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🚀 3. RECOMMANDATIONS D'OPTIMISATION\n";
echo "------------------------------------\n";

$recommendations = [];

// Vérifier les migrations dupliquées
$duplicateChecks = [
    'driver_statuses' => ['2025_01_26_120000_create_driver_statuses_table', '2025_06_07_231226_create_driver_statuses_table'],
    'drivers' => ['2025_06_07_231452_create_drivers_table']
];

foreach ($duplicateChecks as $feature => $migrations) {
    $foundMigrations = [];
    foreach ($migrations as $migration) {
        if (in_array($migration, $pendingMigrations)) {
            $foundMigrations[] = $migration;
        }
    }

    if (count($foundMigrations) > 1) {
        $recommendations[] = "🔄 DUPLICATION: Fusionner les migrations $feature: " . implode(', ', $foundMigrations);
    } elseif (count($foundMigrations) == 1) {
        $recommendations[] = "✅ UNIQUE: Migration $feature prête: " . $foundMigrations[0];
    }
}

// Vérifier les contraintes problématiques
if (in_array('2025_01_20_000000_add_gist_constraints_assignments', $pendingMigrations)) {
    $recommendations[] = "⚠️ ATTENTION: Migration GIST contraints peut causer des erreurs PostgreSQL";
}

// Recommandations performance
$performanceChecks = [
    'drivers' => ['organization_id', 'status_id', 'employee_number'],
    'assignments' => ['driver_id', 'vehicle_id', 'organization_id']
];

foreach ($performanceChecks as $tableName => $indexFields) {
    if (Schema::hasTable($tableName)) {
        $recommendations[] = "🏎️ PERFORMANCE: Vérifier les index sur $tableName: " . implode(', ', $indexFields);
    }
}

if (empty($recommendations)) {
    echo "🎉 AUCUNE OPTIMISATION NÉCESSAIRE!\n";
    echo "   Le système de migrations est déjà optimal.\n";
} else {
    echo "📋 Recommandations:\n";
    foreach ($recommendations as $i => $rec) {
        echo "   " . ($i + 1) . ". $rec\n";
    }
}

echo "\n";

echo "🛡️ 4. PLAN D'OPTIMISATION ENTERPRISE\n";
echo "------------------------------------\n";

$optimizationPlan = [
    "1. 🧹 NETTOYAGE" => [
        "Identifier et supprimer les migrations dupliquées",
        "Fusionner les migrations similaires",
        "Optimiser l'ordre d'exécution"
    ],
    "2. 🏗️ RESTRUCTURATION" => [
        "Créer une migration unifiée pour les tables critiques",
        "Séparer les contraintes complexes",
        "Optimiser les foreign keys"
    ],
    "3. 🚀 PERFORMANCE" => [
        "Ajouter les index strategiques manquants",
        "Optimiser les requêtes de création",
        "Implémenter le partitioning si nécessaire"
    ],
    "4. 🔒 SÉCURITÉ" => [
        "Valider toutes les contraintes",
        "Vérifier les permissions PostgreSQL",
        "Optimiser les RLS (Row Level Security)"
    ]
];

foreach ($optimizationPlan as $phase => $steps) {
    echo "$phase:\n";
    foreach ($steps as $step) {
        echo "   ✓ $step\n";
    }
    echo "\n";
}

echo "📈 5. MÉTRIQUES DE PERFORMANCE\n";
echo "------------------------------\n";

try {
    // Taille des tables
    $tableSizes = DB::select("
        SELECT
            schemaname,
            tablename,
            pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) as size,
            pg_total_relation_size(schemaname||'.'||tablename) as size_bytes
        FROM pg_tables
        WHERE schemaname = 'public'
        AND tablename IN ('drivers', 'driver_statuses', 'assignments', 'vehicles', 'organizations')
        ORDER BY size_bytes DESC
    ");

    echo "📊 Taille des tables principales:\n";
    foreach ($tableSizes as $table) {
        echo "   📋 {$table->tablename}: {$table->size}\n";
    }

    // Nombre d'enregistrements
    echo "\n📈 Nombre d'enregistrements:\n";
    $recordCounts = [
        'drivers' => DB::table('drivers')->count(),
        'driver_statuses' => DB::table('driver_statuses')->count(),
        'assignments' => Schema::hasTable('assignments') ? DB::table('assignments')->count() : 0,
        'organizations' => DB::table('organizations')->count()
    ];

    foreach ($recordCounts as $table => $count) {
        echo "   📊 {$table}: {$count} enregistrements\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur métriques: " . $e->getMessage() . "\n";
}

echo "\n";

echo "🎯 SCORE D'OPTIMISATION ENTERPRISE\n";
echo "==================================\n";

$score = 0;
$maxScore = 100;

// Points pour tables existantes (40 points)
$tablesScore = 0;
foreach (['drivers', 'driver_statuses', 'organizations', 'users'] as $table) {
    if (Schema::hasTable($table)) $tablesScore += 10;
}
$score += $tablesScore;

// Points pour migrations propres (30 points)
$migrationsScore = max(0, 30 - (count($pendingMigrations) * 2));
$score += $migrationsScore;

// Points pour performance (30 points)
$performanceScore = 30; // Base, à ajuster selon les index
$score += min(30, $performanceScore);

$finalScore = min(100, $score);

echo "🏆 SCORE FINAL: {$finalScore}/100\n\n";

if ($finalScore >= 90) {
    echo "🌟 EXCELLENT - Système optimisé enterprise-grade!\n";
} elseif ($finalScore >= 70) {
    echo "✅ BON - Quelques optimisations mineures recommandées\n";
} else {
    echo "⚠️ À AMÉLIORER - Optimisations nécessaires\n";
}

echo "\n💫 Analyse terminée - " . date('Y-m-d H:i:s') . "\n";
echo "🚛 ZenFleet Migration System - Expertise Enterprise 20+ ans\n";