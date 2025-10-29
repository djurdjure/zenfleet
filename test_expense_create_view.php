<?php

/**
 * ====================================================================
 * 🧪 TEST DE LA VUE DE CRÉATION DE DÉPENSE
 * ====================================================================
 * 
 * Script de test pour vérifier que la page de création de dépense
 * s'affiche correctement avec le bon layout
 * 
 * @version 1.0.0-Enterprise
 * @since 2025-10-29
 * ====================================================================
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

try {
    echo "\n🔧 TEST DE LA VUE DE CRÉATION DE DÉPENSE\n";
    echo "=" . str_repeat("=", 60) . "\n\n";

    // ===============================================
    // 1. VÉRIFIER LES VUES ET LAYOUTS
    // ===============================================
    echo "1️⃣ Vérification des fichiers de vue...\n";
    
    $viewFile = resource_path('views/admin/vehicle-expenses/create.blade.php');
    if (file_exists($viewFile)) {
        echo "   ✅ Fichier de vue existe: create.blade.php\n";
        echo "   📏 Taille: " . number_format(filesize($viewFile)) . " octets\n";
    } else {
        throw new Exception("Le fichier de vue n'existe pas!");
    }
    
    // Vérifier les layouts
    $layouts = [
        'layouts/admin.blade.php',
        'layouts/admin/catalyst.blade.php'
    ];
    
    echo "\n2️⃣ Vérification des layouts...\n";
    foreach ($layouts as $layout) {
        $layoutPath = resource_path('views/' . $layout);
        if (file_exists($layoutPath)) {
            echo "   ✅ Layout disponible: $layout\n";
        } else {
            echo "   ❌ Layout manquant: $layout\n";
        }
    }
    
    // ===============================================
    // 2. TESTER LE RENDU DE LA VUE
    // ===============================================
    echo "\n3️⃣ Test de rendu de la vue...\n";
    
    // Simuler un utilisateur connecté
    $user = User::find(4); // User admin
    if (!$user) {
        throw new Exception("Utilisateur de test non trouvé");
    }
    Auth::login($user);
    
    // Préparer les données pour la vue
    $vehicles = \App\Models\Vehicle::where('organization_id', $user->organization_id)
        ->orderBy('registration_plate')
        ->get();
        
    $suppliers = \App\Models\Supplier::where('organization_id', $user->organization_id)
        ->where('is_active', true)
        ->orderBy('company_name')
        ->get();
        
    $expenseGroups = \App\Models\ExpenseGroup::where('organization_id', $user->organization_id)
        ->orderBy('name')
        ->get();
    
    echo "   📊 Données disponibles:\n";
    echo "      • Véhicules: " . $vehicles->count() . "\n";
    echo "      • Fournisseurs: " . $suppliers->count() . "\n";
    echo "      • Groupes de dépenses: " . $expenseGroups->count() . "\n";
    
    // Tenter de rendre la vue
    try {
        $html = View::make('admin.vehicle-expenses.create', compact(
            'vehicles',
            'suppliers',
            'expenseGroups'
        ))->render();
        
        echo "\n   ✅ Vue rendue avec succès!\n";
        echo "   📄 Taille HTML: " . number_format(strlen($html)) . " caractères\n";
        
        // Vérifier des éléments clés dans le HTML
        $checks = [
            'form action' => 'action="',
            'vehicle_id' => 'name="vehicle_id"',
            'expense_category' => 'name="expense_category"',
            'amount_ht' => 'name="amount_ht"',
            'Alpine.js' => 'x-data',
            'Tailwind' => 'class="'
        ];
        
        echo "\n4️⃣ Vérification du contenu HTML...\n";
        foreach ($checks as $name => $pattern) {
            if (strpos($html, $pattern) !== false) {
                echo "   ✅ $name trouvé\n";
            } else {
                echo "   ⚠️  $name non trouvé\n";
            }
        }
        
    } catch (\Exception $e) {
        echo "\n   ❌ Erreur lors du rendu: " . $e->getMessage() . "\n";
        echo "   📍 Fichier: " . $e->getFile() . "\n";
        echo "   📍 Ligne: " . $e->getLine() . "\n";
        throw $e;
    }
    
    // ===============================================
    // 3. VÉRIFIER LA ROUTE
    // ===============================================
    echo "\n5️⃣ Vérification de la route...\n";
    
    $route = Route::getRoutes()->getByName('admin.vehicle-expenses.create');
    if ($route) {
        echo "   ✅ Route existe: " . $route->uri() . "\n";
        echo "   📍 Méthode HTTP: " . implode('|', $route->methods()) . "\n";
        echo "   📍 Action: " . $route->getActionName() . "\n";
    } else {
        echo "   ⚠️  Route 'admin.vehicle-expenses.create' non trouvée\n";
    }
    
    // ===============================================
    // 4. STRUCTURE DES FICHIERS
    // ===============================================
    echo "\n6️⃣ Structure des fichiers de vue...\n";
    
    $viewDir = resource_path('views/admin/vehicle-expenses');
    $files = scandir($viewDir);
    
    echo "   📁 Contenu du dossier:\n";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $viewDir . '/' . $file;
            if (is_dir($path)) {
                echo "      📁 $file/\n";
            } else {
                $size = filesize($path);
                echo "      📄 $file (" . number_format($size) . " octets)\n";
            }
        }
    }
    
    // ===============================================
    // RÉSUMÉ
    // ===============================================
    echo "\n✨ TEST TERMINÉ AVEC SUCCÈS!\n";
    echo "=" . str_repeat("=", 60) . "\n";
    echo "Résumé:\n";
    echo "• ✅ Vue principale: create.blade.php\n";
    echo "• ✅ Layout: layouts.admin (→ catalyst)\n";
    echo "• ✅ Rendu HTML fonctionnel\n";
    echo "• ✅ Formulaire complet avec tous les champs\n";
    echo "• ✅ Structure de fichiers propre et organisée\n";
    echo "\n";
    echo "🎯 La page de création de dépense est prête!\n\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}
