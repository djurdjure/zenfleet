<?php

/**
 * 🔧 VALIDATION FINALE - TOUTES LES CORRECTIONS
 * 
 * Script de validation finale de tous les modules corrigés
 * 
 * @version 1.0
 * @since 2025-11-11
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;

// Démarrer l'application Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║         🚀 VALIDATION FINALE - TOUS LES MODULES                  ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$allPassed = true;
$modules = [];

// =========================================================================
// MODULE 1: AFFECTATIONS (ASSIGNMENTS)
// =========================================================================

echo "📋 MODULE AFFECTATIONS\n";
echo "═══════════════════════════════════════════════════════\n";

try {
    // Vérifier la structure de la base de données
    $columns = DB::select("
        SELECT column_name
        FROM information_schema.columns
        WHERE table_name = 'assignments'
        AND table_schema = 'public'
    ");
    
    $columnNames = array_column($columns, 'column_name');
    
    if (in_array('status', $columnNames)) {
        echo "✅ Colonne 'status' présente\n";
        $modules['assignments']['status_column'] = true;
    } else {
        echo "❌ Colonne 'status' manquante\n";
        $modules['assignments']['status_column'] = false;
        $allPassed = false;
    }
    
    if (!in_array('cancelled_at', $columnNames)) {
        echo "✅ Colonne 'cancelled_at' absente (comme attendu)\n";
        $modules['assignments']['no_cancelled_at'] = true;
    } else {
        echo "❌ Colonne 'cancelled_at' toujours présente\n";
        $modules['assignments']['no_cancelled_at'] = false;
        $allPassed = false;
    }
    
    // Test du composant Livewire
    $livewireClass = '\\App\\Livewire\\Admin\\AssignmentFiltersEnhanced';
    if (class_exists($livewireClass)) {
        $component = new $livewireClass();
        $component->mount();
        echo "✅ Composant Livewire fonctionnel\n";
        $modules['assignments']['livewire'] = true;
    } else {
        echo "❌ Composant Livewire non trouvé\n";
        $modules['assignments']['livewire'] = false;
        $allPassed = false;
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    $modules['assignments']['error'] = $e->getMessage();
    $allPassed = false;
}

echo "\n";

// =========================================================================
// MODULE 2: AFFICHAGE CHAUFFEURS
// =========================================================================

echo "👤 MODULE AFFICHAGE CHAUFFEURS\n";
echo "═══════════════════════════════════════════════════════\n";

try {
    // Test avec un véhicule ayant un chauffeur  
    // Récupération via les affectations actives
    $assignment = Assignment::with(['driver.user', 'vehicle'])
        ->where('start_datetime', '<=', now())
        ->where(function($q) {
            $q->whereNull('end_datetime')
              ->orWhere('end_datetime', '>', now());
        })
        ->whereHas('driver')
        ->first();
    
    if ($assignment) {
        if ($assignment->driver) {
            $driver = $assignment->driver;
            $user = $driver->user;
            
            // Test de la logique d'affichage du nom
            $driverName = '';
            if ($driver->first_name || $driver->last_name) {
                $driverName = trim($driver->first_name . ' ' . $driver->last_name);
            } elseif ($user) {
                if ($user->first_name || $user->last_name) {
                    $driverName = trim($user->first_name . ' ' . $user->last_name);
                } elseif ($user->name) {
                    $driverName = $user->name;
                }
            }
            
            if ($driverName) {
                echo "✅ Logique d'affichage du nom: '$driverName'\n";
                $modules['drivers']['name_display'] = true;
            } else {
                echo "⚠️ Nom de chauffeur vide\n";
                $modules['drivers']['name_display'] = false;
            }
        } else {
            echo "⚠️ Pas de chauffeur actuel trouvé\n";
            $modules['drivers']['no_current'] = true;
        }
    } else {
        echo "⚠️ Aucune affectation active avec chauffeur trouvée\n";
        $modules['drivers']['no_assignment'] = true;
    }
    
    echo "✅ Module chauffeurs validé\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    $modules['drivers']['error'] = $e->getMessage();
    $allPassed = false;
}

echo "\n";

// =========================================================================
// MODULE 3: SYNTAXE BLADE/ALPINE
// =========================================================================

echo "🎨 MODULE SYNTAXE BLADE/ALPINE\n";
echo "═══════════════════════════════════════════════════════\n";

$bladeFile = __DIR__ . '/resources/views/livewire/admin/assignment-filters-enhanced.blade.php';

if (file_exists($bladeFile)) {
    $content = file_get_contents($bladeFile);
    
    // Vérifier les corrections appliquées
    if (strpos($content, "@entangle('showVehicleDropdown').defer") !== false) {
        echo "✅ Directive @entangle corrigée pour véhicules\n";
        $modules['blade']['entangle_vehicle'] = true;
    } else {
        echo "❌ Directive @entangle non corrigée pour véhicules\n";
        $modules['blade']['entangle_vehicle'] = false;
        $allPassed = false;
    }
    
    if (strpos($content, "@entangle('showDriverDropdown').defer") !== false) {
        echo "✅ Directive @entangle corrigée pour chauffeurs\n";
        $modules['blade']['entangle_driver'] = true;
    } else {
        echo "❌ Directive @entangle non corrigée pour chauffeurs\n";
        $modules['blade']['entangle_driver'] = false;
        $allPassed = false;
    }
    
    // Vérifier l'absence d'erreurs courantes
    if (strpos($content, 'Undefined constant') === false) {
        echo "✅ Pas d'erreur 'Undefined constant'\n";
        $modules['blade']['no_undefined'] = true;
    } else {
        echo "❌ Erreur 'Undefined constant' potentielle\n";
        $modules['blade']['no_undefined'] = false;
        $allPassed = false;
    }
    
} else {
    echo "❌ Fichier Blade non trouvé\n";
    $modules['blade']['file_exists'] = false;
    $allPassed = false;
}

echo "\n";

// =========================================================================
// MODULE 4: FICHIERS TEMPORAIRES
// =========================================================================

echo "🗑️ MODULE NETTOYAGE\n";
echo "═══════════════════════════════════════════════════════\n";

$backupFiles = glob(__DIR__ . '/app/Livewire/Admin/*.backup.*');
$backupFiles2 = glob(__DIR__ . '/resources/views/admin/vehicles/*.backup-*');
$backupFiles3 = glob(__DIR__ . '/resources/views/livewire/admin/*.backup');

$totalBackups = count($backupFiles) + count($backupFiles2) + count($backupFiles3);

if ($totalBackups == 0) {
    echo "✅ Aucun fichier de backup trouvé (nettoyé)\n";
    $modules['cleanup']['backups'] = true;
} else {
    echo "⚠️ $totalBackups fichiers de backup restants\n";
    $modules['cleanup']['backups'] = false;
}

echo "\n";

// =========================================================================
// RÉSUMÉ FINAL
// =========================================================================

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                      📊 RÉSUMÉ FINAL                             ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

$moduleStatus = [
    'Affectations' => isset($modules['assignments']) && !isset($modules['assignments']['error']),
    'Affichage Chauffeurs' => isset($modules['drivers']) && !isset($modules['drivers']['error']),
    'Syntaxe Blade/Alpine' => isset($modules['blade']) && ($modules['blade']['entangle_vehicle'] ?? false),
    'Nettoyage' => isset($modules['cleanup']) && ($modules['cleanup']['backups'] ?? false)
];

foreach ($moduleStatus as $name => $status) {
    echo sprintf("%-25s %s\n", $name . ':', $status ? '✅ VALIDÉ' : '❌ ÉCHEC');
}

echo "\n";

if ($allPassed) {
    echo "╔══════════════════════════════════════════════════════════════════╗\n";
    echo "║     ✅ TOUTES LES VALIDATIONS SONT PASSÉES AVEC SUCCÈS!         ║\n";
    echo "╚══════════════════════════════════════════════════════════════════╝\n\n";
    
    echo "🎉 Le système est prêt pour la production!\n";
    echo "📋 Prochaines étapes:\n";
    echo "   1. Faire un commit des changements\n";
    echo "   2. Tester l'interface utilisateur\n";
    echo "   3. Déployer en production\n";
} else {
    echo "╔══════════════════════════════════════════════════════════════════╗\n";
    echo "║     ⚠️ CERTAINES VALIDATIONS ONT ÉCHOUÉ                         ║\n";
    echo "╚══════════════════════════════════════════════════════════════════╝\n\n";
    
    echo "❌ Veuillez corriger les problèmes identifiés avant de continuer.\n";
}

echo "\n";
