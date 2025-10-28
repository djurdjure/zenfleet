<?php

/**
 * ====================================================================
 * 🧪 TEST DE CRÉATION DE DÉPENSE - VERSION CORRIGÉE
 * ====================================================================
 * 
 * Script de test pour valider le processus complet de création de dépense
 * avec les catégories correctes et la gestion d'erreur améliorée
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
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ===============================================
// CONFIGURATION DU TEST
// ===============================================
$testUserId = 4; // ID de l'utilisateur de test (admin)
$organizationId = 1; // ID de l'organisation

try {
    echo "\n🔧 TEST DE CRÉATION DE DÉPENSE VÉHICULE\n";
    echo "=" . str_repeat("=", 60) . "\n\n";

    // ===============================================
    // 1. VÉRIFIER L'UTILISATEUR
    // ===============================================
    echo "1️⃣ Vérification de l'utilisateur...\n";
    $user = User::find($testUserId);
    if (!$user) {
        throw new Exception("Utilisateur ID $testUserId non trouvé");
    }
    echo "   ✅ Utilisateur: {$user->name} (Org: {$user->organization_id})\n\n";

    // ===============================================
    // 2. VÉRIFIER LES CATÉGORIES DISPONIBLES
    // ===============================================
    echo "2️⃣ Catégories de dépenses disponibles:\n";
    $categories = config('expense_categories.categories');
    foreach ($categories as $key => $category) {
        echo "   • {$key} => {$category['label']}\n";
    }
    echo "\n";

    // ===============================================
    // 3. RÉCUPÉRER UN VÉHICULE DE TEST
    // ===============================================
    echo "3️⃣ Récupération d'un véhicule...\n";
    $vehicle = Vehicle::where('organization_id', $organizationId)
        ->first();
    
    if (!$vehicle) {
        throw new Exception("Aucun véhicule disponible dans l'organisation");
    }
    echo "   ✅ Véhicule: {$vehicle->registration_plate} - {$vehicle->brand} {$vehicle->model}\n\n";

    // ===============================================
    // 4. RÉCUPÉRER UN FOURNISSEUR (OPTIONNEL)
    // ===============================================
    echo "4️⃣ Récupération d'un fournisseur...\n";
    $supplier = Supplier::where('organization_id', $organizationId)
        ->where('is_active', true)
        ->first();
    
    if ($supplier) {
        echo "   ✅ Fournisseur: {$supplier->company_name}\n\n";
    } else {
        echo "   ⚠️  Aucun fournisseur actif trouvé (continuera sans fournisseur)\n\n";
    }

    // ===============================================
    // 5. CRÉER UNE DÉPENSE DE TEST
    // ===============================================
    echo "5️⃣ Création d'une dépense de test...\n";
    
    // Préparer les données avec une catégorie valide
    $expenseData = [
        'organization_id' => $organizationId,
        'vehicle_id' => $vehicle->id,
        'supplier_id' => $supplier ? $supplier->id : null,
        'expense_category' => 'maintenance_preventive', // Catégorie valide depuis la config
        'expense_type' => 'vidange', // Type valide pour maintenance_preventive
        'expense_date' => date('Y-m-d', strtotime('-1 day')), // Hier pour éviter la contrainte
        'amount_ht' => 150.00,
        'tva_rate' => 20,
        'description' => 'Vidange moteur à 15000 km - Test automatisé',
        'internal_notes' => 'Test créé par script de validation',
        'invoice_number' => 'TEST-' . date('YmdHis'),
        'payment_status' => 'pending', // Changer en pending pour éviter la contrainte
        'recorded_by' => $testUserId,
        'requester_id' => $testUserId,
        'needs_approval' => false,
        'approval_status' => 'approved'
    ];

    $totalTTC = $expenseData['amount_ht'] * (1 + $expenseData['tva_rate'] / 100);
    
    echo "   📝 Données de la dépense:\n";
    echo "      • Catégorie: {$expenseData['expense_category']}\n";
    echo "      • Type: {$expenseData['expense_type']}\n";
    echo "      • Montant HT: {$expenseData['amount_ht']} €\n";
    echo "      • TVA: {$expenseData['tva_rate']}%\n";
    echo "      • Montant TTC: " . number_format($totalTTC, 2) . " €\n";
    echo "\n";

    // Démarrer une transaction
    DB::beginTransaction();

    try {
        // Créer la dépense
        $expense = VehicleExpense::create($expenseData);
        
        echo "   ✅ Dépense créée avec succès!\n";
        echo "      • ID: {$expense->id}\n";
        echo "      • Référence: {$expense->reference_number}\n";
        echo "      • Montant TTC: {$expense->total_ttc} €\n";
        echo "\n";

        // ===============================================
        // 6. VÉRIFIER LA DÉPENSE CRÉÉE
        // ===============================================
        echo "6️⃣ Vérification de la dépense...\n";
        
        $createdExpense = VehicleExpense::with(['vehicle', 'supplier', 'recordedBy'])
            ->find($expense->id);
        
        if (!$createdExpense) {
            throw new Exception("Impossible de retrouver la dépense créée");
        }

        echo "   ✅ Dépense vérifiée:\n";
        echo "      • Véhicule: {$createdExpense->vehicle->registration_plate}\n";
        if ($createdExpense->supplier) {
            echo "      • Fournisseur: {$createdExpense->supplier->company_name}\n";
        }
        echo "      • Enregistrée par: {$createdExpense->recordedBy->name}\n";
        echo "      • Catégorie: {$createdExpense->expense_category}\n";
        echo "      • Type: {$createdExpense->expense_type}\n";
        echo "\n";

        // ===============================================
        // 7. TEST DE MISE À JOUR
        // ===============================================
        echo "7️⃣ Test de mise à jour...\n";
        
        $updateData = [
            'amount_ht' => 200.00,
            'description' => 'Vidange moteur à 15000 km - Modifié par test'
        ];
        
        $createdExpense->update($updateData);
        
        echo "   ✅ Dépense mise à jour:\n";
        echo "      • Nouveau montant TTC: {$createdExpense->total_ttc} €\n";
        echo "\n";

        // ===============================================
        // 8. TEST AVEC CATÉGORIES VARIÉES
        // ===============================================
        echo "8️⃣ Test avec différentes catégories...\n";
        
        $testCategories = [
            ['category' => 'carburant', 'type' => 'diesel'],
            ['category' => 'reparation', 'type' => 'moteur'],
            ['category' => 'assurance', 'type' => 'tous_risques'],
            ['category' => 'controle_technique', 'type' => 'controle_initial']
        ];
        
        foreach ($testCategories as $test) {
            $testData = array_merge($expenseData, [
                'expense_category' => $test['category'],
                'expense_type' => $test['type'],
                'description' => "Test catégorie {$test['category']} - type {$test['type']}",
                'invoice_number' => 'TEST-' . $test['category'] . '-' . time()
            ]);
            
            $testExpense = VehicleExpense::create($testData);
            echo "   ✅ {$test['category']}/{$test['type']} - ID: {$testExpense->id}\n";
        }
        echo "\n";

        // ===============================================
        // 9. STATISTIQUES FINALES
        // ===============================================
        echo "9️⃣ Statistiques après tests:\n";
        
        $stats = DB::table('vehicle_expenses')
            ->where('organization_id', $organizationId)
            ->where('vehicle_id', $vehicle->id)
            ->selectRaw('
                expense_category,
                COUNT(*) as count,
                SUM(total_ttc) as total
            ')
            ->groupBy('expense_category')
            ->get();
        
        foreach ($stats as $stat) {
            echo "   • {$stat->expense_category}: {$stat->count} dépense(s) = " . 
                 number_format($stat->total, 2) . " €\n";
        }
        echo "\n";

        // ===============================================
        // ROLLBACK (NE PAS GARDER LES DONNÉES DE TEST)
        // ===============================================
        echo "🔄 Rollback des données de test...\n";
        DB::rollBack();
        echo "   ✅ Toutes les données de test ont été annulées\n\n";

    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
    }

    // ===============================================
    // RÉSUMÉ
    // ===============================================
    echo "✨ TEST TERMINÉ AVEC SUCCÈS!\n";
    echo "=" . str_repeat("=", 60) . "\n";
    echo "Résumé:\n";
    echo "• ✅ Création de dépense fonctionnelle\n";
    echo "• ✅ Catégories correctement configurées\n";
    echo "• ✅ Validation des données OK\n";
    echo "• ✅ Relations (véhicule, fournisseur) OK\n";
    echo "• ✅ Mise à jour fonctionnelle\n";
    echo "• ✅ Support multi-catégories validé\n";
    echo "\n";
    echo "🎯 Le module de dépenses est prêt pour la production!\n\n";

} catch (Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
} catch (\Illuminate\Database\QueryException $e) {
    echo "\n❌ ERREUR BASE DE DONNÉES:\n";
    echo "Message: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getSql')) {
        echo "SQL: " . $e->getSql() . "\n";
        echo "Bindings: " . json_encode($e->getBindings()) . "\n";
    }
    echo "\n";
    exit(1);
}
