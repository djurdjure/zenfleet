#!/usr/bin/env php
<?php

/**
 * Test direct de la page des dépenses
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\VehicleExpense;
use App\Services\VehicleExpenseService;
use App\Services\ExpenseAnalyticsService;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🔍 TEST DIRECT DE LA PAGE DES DÉPENSES\n";
echo str_repeat("=", 80) . "\n\n";

$admin = User::where('email', 'admin@zenfleet.dz')->first();
if (!$admin) {
    echo "❌ Utilisateur admin non trouvé\n";
    exit(1);
}

echo "✅ Utilisateur admin trouvé: " . $admin->email . "\n";
echo "   Organization ID: " . ($admin->organization_id ?? 'NULL') . "\n\n";

// Test 1: Vérifier les services
echo "📋 Test des services:\n";
echo str_repeat("-", 40) . "\n";

try {
    $expenseService = new VehicleExpenseService();
    echo "✅ VehicleExpenseService créé\n";
} catch (\Exception $e) {
    echo "❌ VehicleExpenseService: " . $e->getMessage() . "\n";
}

try {
    $analyticsService = new ExpenseAnalyticsService();
    echo "✅ ExpenseAnalyticsService créé\n";
} catch (\Exception $e) {
    echo "❌ ExpenseAnalyticsService: " . $e->getMessage() . "\n";
}

// Test 2: Appeler getDashboardStats
echo "\n📋 Test de getDashboardStats:\n";
echo str_repeat("-", 40) . "\n";

try {
    $stats = $analyticsService->getDashboardStats($admin->organization_id);
    echo "✅ Stats récupérées:\n";
    echo "   - current_month_total: " . ($stats['current_month_total'] ?? 'N/A') . "\n";
    echo "   - pending_count: " . ($stats['pending_count'] ?? 'N/A') . "\n";
    echo "   - approved_count: " . ($stats['approved_count'] ?? 'N/A') . "\n";
} catch (\Exception $e) {
    echo "❌ Erreur getDashboardStats: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

// Test 3: Appeler getBudgetAlerts
echo "\n📋 Test de getBudgetAlerts:\n";
echo str_repeat("-", 40) . "\n";

try {
    $budgetAlerts = $expenseService->getBudgetAlerts($admin->organization_id);
    echo "✅ Budget alerts récupérées: " . count($budgetAlerts) . " alertes\n";
} catch (\Exception $e) {
    echo "❌ Erreur getBudgetAlerts: " . $e->getMessage() . "\n";
}

// Test 4: Récupérer quelques dépenses
echo "\n📋 Test de récupération des dépenses:\n";
echo str_repeat("-", 40) . "\n";

try {
    $expenses = VehicleExpense::where('organization_id', $admin->organization_id)
        ->latest()
        ->limit(5)
        ->get();
    
    echo "✅ " . $expenses->count() . " dépense(s) trouvée(s)\n";
    
    foreach ($expenses as $expense) {
        echo "   - " . $expense->expense_date . " : " . 
             number_format($expense->total_ttc, 2) . " DZD (" . 
             $expense->expense_type . ")\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur récupération dépenses: " . $e->getMessage() . "\n";
}

// Test 5: Test de la vue
echo "\n📋 Test du rendu de la vue:\n";
echo str_repeat("-", 40) . "\n";

try {
    $viewData = [
        'stats' => [
            'current_month_total' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'avg_per_vehicle' => 0
        ],
        'budgetAlerts' => []
    ];
    
    $view = view('admin.vehicle-expenses.index_simple', $viewData);
    $content = $view->render();
    
    echo "✅ Vue rendue avec succès\n";
    echo "   Taille du contenu: " . strlen($content) . " octets\n";
    
    if (strpos($content, 'Gestion des Dépenses') !== false) {
        echo "✅ Titre trouvé dans la vue\n";
    } else {
        echo "⚠️ Titre non trouvé dans la vue\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur rendu vue: " . $e->getMessage() . "\n";
    echo "   Classe: " . get_class($e) . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "🎯 FIN DES TESTS\n";
echo str_repeat("=", 80) . "\n\n";
