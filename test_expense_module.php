#!/usr/bin/env php
<?php

/**
 * Test du Module de Gestion des Dépenses
 * Vérifie que tous les composants sont correctement installés
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ExpenseGroup;
use App\Models\VehicleExpense;
use App\Models\ExpenseAuditLog;
use App\Services\VehicleExpenseService;
use App\Services\ExpenseAnalyticsService;
use App\Services\ExpenseApprovalService;
use App\Http\Controllers\Admin\VehicleExpenseController;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🧪 TEST DU MODULE DE GESTION DES DÉPENSES\n";
echo str_repeat("=", 80) . "\n\n";

$tests = [];

// ====================================================================
// TEST 1: Vérification des Modèles
// ====================================================================
echo "📋 TEST 1: Vérification des Modèles\n";
echo str_repeat("-", 40) . "\n";

// ExpenseGroup
if (class_exists(ExpenseGroup::class)) {
    echo "✅ Modèle ExpenseGroup trouvé\n";
    $tests['expense_group_model'] = true;
} else {
    echo "❌ Modèle ExpenseGroup non trouvé\n";
    $tests['expense_group_model'] = false;
}

// VehicleExpense (vérifier les nouvelles relations)
if (class_exists(VehicleExpense::class)) {
    $expense = new VehicleExpense();
    if (method_exists($expense, 'expenseGroup')) {
        echo "✅ Relation expenseGroup() dans VehicleExpense\n";
        $tests['expense_group_relation'] = true;
    } else {
        echo "❌ Relation expenseGroup() manquante\n";
        $tests['expense_group_relation'] = false;
    }
    
    if (method_exists($expense, 'requester')) {
        echo "✅ Relation requester() dans VehicleExpense\n";
        $tests['requester_relation'] = true;
    } else {
        echo "❌ Relation requester() manquante\n";
        $tests['requester_relation'] = false;
    }
}

// ExpenseAuditLog
if (class_exists(ExpenseAuditLog::class)) {
    echo "✅ Modèle ExpenseAuditLog trouvé\n";
    $tests['audit_log_model'] = true;
} else {
    echo "❌ Modèle ExpenseAuditLog non trouvé\n";
    $tests['audit_log_model'] = false;
}

// ====================================================================
// TEST 2: Vérification des Services
// ====================================================================
echo "\n📋 TEST 2: Vérification des Services\n";
echo str_repeat("-", 40) . "\n";

// VehicleExpenseService
if (class_exists(VehicleExpenseService::class)) {
    echo "✅ Service VehicleExpenseService trouvé\n";
    $service = new VehicleExpenseService();
    
    if (method_exists($service, 'getBudgetAlerts')) {
        echo "✅ Méthode getBudgetAlerts() disponible\n";
        $tests['budget_alerts_method'] = true;
    } else {
        echo "❌ Méthode getBudgetAlerts() manquante\n";
        $tests['budget_alerts_method'] = false;
    }
} else {
    echo "❌ Service VehicleExpenseService non trouvé\n";
    $tests['expense_service'] = false;
}

// ExpenseAnalyticsService
if (class_exists(ExpenseAnalyticsService::class)) {
    echo "✅ Service ExpenseAnalyticsService trouvé\n";
    $analytics = new ExpenseAnalyticsService();
    
    if (method_exists($analytics, 'calculateTCO')) {
        echo "✅ Méthode calculateTCO() disponible\n";
        $tests['tco_method'] = true;
    } else {
        echo "❌ Méthode calculateTCO() manquante\n";
        $tests['tco_method'] = false;
    }
} else {
    echo "❌ Service ExpenseAnalyticsService non trouvé\n";
    $tests['analytics_service'] = false;
}

// ExpenseApprovalService
if (class_exists(ExpenseApprovalService::class)) {
    echo "✅ Service ExpenseApprovalService trouvé\n";
    $approval = new ExpenseApprovalService();
    
    if (method_exists($approval, 'determineRequiredApprovalLevel')) {
        echo "✅ Méthode determineRequiredApprovalLevel() disponible\n";
        $tests['approval_level_method'] = true;
    } else {
        echo "❌ Méthode determineRequiredApprovalLevel() manquante\n";
        $tests['approval_level_method'] = false;
    }
} else {
    echo "❌ Service ExpenseApprovalService non trouvé\n";
    $tests['approval_service'] = false;
}

// ====================================================================
// TEST 3: Vérification du Contrôleur
// ====================================================================
echo "\n📋 TEST 3: Vérification du Contrôleur\n";
echo str_repeat("-", 40) . "\n";

if (class_exists(VehicleExpenseController::class)) {
    echo "✅ Contrôleur VehicleExpenseController trouvé\n";
    $controller = new \ReflectionClass(VehicleExpenseController::class);
    
    $methods = ['index', 'create', 'store', 'approve', 'reject', 'analytics', 'export'];
    foreach ($methods as $method) {
        if ($controller->hasMethod($method)) {
            echo "✅ Méthode {$method}() disponible\n";
            $tests["controller_{$method}"] = true;
        } else {
            echo "❌ Méthode {$method}() manquante\n";
            $tests["controller_{$method}"] = false;
        }
    }
} else {
    echo "❌ Contrôleur VehicleExpenseController non trouvé\n";
    $tests['controller'] = false;
}

// ====================================================================
// TEST 4: Vérification de la Base de Données
// ====================================================================
echo "\n📋 TEST 4: Vérification de la Base de Données\n";
echo str_repeat("-", 40) . "\n";

use Illuminate\Support\Facades\Schema;

// Table expense_groups
if (Schema::hasTable('expense_groups')) {
    echo "✅ Table expense_groups existe\n";
    $tests['expense_groups_table'] = true;
    
    // Vérifier quelques colonnes importantes
    $columns = ['budget_allocated', 'budget_used', 'budget_remaining'];
    foreach ($columns as $column) {
        if (Schema::hasColumn('expense_groups', $column)) {
            echo "  ✅ Colonne {$column} présente\n";
        } else {
            echo "  ❌ Colonne {$column} manquante\n";
        }
    }
} else {
    echo "❌ Table expense_groups n'existe pas\n";
    $tests['expense_groups_table'] = false;
}

// Table expense_audit_logs
if (Schema::hasTable('expense_audit_logs')) {
    echo "✅ Table expense_audit_logs existe\n";
    $tests['audit_logs_table'] = true;
} else {
    echo "❌ Table expense_audit_logs n'existe pas\n";
    $tests['audit_logs_table'] = false;
}

// Colonnes ajoutées à vehicle_expenses
if (Schema::hasTable('vehicle_expenses')) {
    $newColumns = ['expense_group_id', 'requester_id', 'level1_approved', 'level2_approved', 'approval_status'];
    $allColumnsPresent = true;
    
    foreach ($newColumns as $column) {
        if (Schema::hasColumn('vehicle_expenses', $column)) {
            echo "✅ Colonne vehicle_expenses.{$column} présente\n";
        } else {
            echo "❌ Colonne vehicle_expenses.{$column} manquante\n";
            $allColumnsPresent = false;
        }
    }
    
    $tests['vehicle_expenses_columns'] = $allColumnsPresent;
}

// ====================================================================
// TEST 5: Test Fonctionnel Simple
// ====================================================================
echo "\n📋 TEST 5: Test Fonctionnel Simple\n";
echo str_repeat("-", 40) . "\n";

try {
    // Tester la détermination du niveau d'approbation
    $approvalService = new ExpenseApprovalService();
    
    $level1 = $approvalService->determineRequiredApprovalLevel(50000); // 50K DZD
    echo "✅ Montant 50,000 DZD nécessite niveau: {$level1} (attendu: 1)\n";
    $tests['approval_level_50k'] = ($level1 == 1);
    
    $level2 = $approvalService->determineRequiredApprovalLevel(200000); // 200K DZD
    echo "✅ Montant 200,000 DZD nécessite niveau: {$level2} (attendu: 2)\n";
    $tests['approval_level_200k'] = ($level2 == 2);
    
    $level0 = $approvalService->determineRequiredApprovalLevel(5000); // 5K DZD
    echo "✅ Montant 5,000 DZD nécessite niveau: {$level0} (attendu: 0/auto)\n";
    $tests['approval_level_5k'] = ($level0 == 0);
    
} catch (\Exception $e) {
    echo "❌ Erreur test fonctionnel: " . $e->getMessage() . "\n";
    $tests['functional_test'] = false;
}

// ====================================================================
// RÉSUMÉ DES TESTS
// ====================================================================
echo "\n" . str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ DES TESTS\n";
echo str_repeat("=", 80) . "\n";

$totalTests = count($tests);
$passedTests = count(array_filter($tests));
$failedTests = $totalTests - $passedTests;

echo "✅ Tests réussis: {$passedTests}/{$totalTests}\n";
echo "❌ Tests échoués: {$failedTests}/{$totalTests}\n";

if ($failedTests === 0) {
    echo "\n🎉 SUCCÈS! Le module de gestion des dépenses est opérationnel.\n";
} else {
    echo "\n⚠️  ATTENTION: Certains composants manquent ou ne fonctionnent pas correctement.\n";
    echo "   Vérifiez que les migrations ont été exécutées: php artisan migrate\n";
}

echo "\n" . str_repeat("=", 80) . "\n\n";
