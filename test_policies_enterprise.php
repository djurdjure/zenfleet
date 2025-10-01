<?php

/**
 * 🔐 TEST COMPLET DES POLICIES ENTERPRISE-GRADE
 *
 * Ce script teste l'accès réel de l'Admin FADERCO avec les nouvelles Policies
 * Usage: docker compose exec -u zenfleet_user php php test_policies_enterprise.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔐 TEST DES POLICIES ENTERPRISE - ZENFLEET\n";
echo str_repeat("=", 80) . "\n\n";

// Récupérer l'admin FADERCO
$admin = App\Models\User::where('email', 'admin@faderco.dz')->first();

if (!$admin) {
    echo "❌ Admin admin@faderco.dz introuvable\n";
    exit(1);
}

echo "✅ Utilisateur: {$admin->email}\n";
echo "   Organisation: {$admin->organization->name} (ID: {$admin->organization_id})\n";
echo "   Rôle: " . $admin->getRoleNames()->implode(', ') . "\n\n";

// Test des Policies
echo "🧪 TEST DES POLICIES\n";
echo str_repeat("=", 80) . "\n\n";

// 1. Test VehiclePolicy
echo "1. 🚗 VEHICLE POLICY\n";
echo str_repeat("-", 80) . "\n";

$canViewVehicles = $admin->can('viewAny', App\Models\Vehicle::class);
echo "  viewAny (voir tous les véhicules): " . ($canViewVehicles ? "✅" : "❌") . "\n";

$canCreateVehicles = $admin->can('create', App\Models\Vehicle::class);
echo "  create (créer un véhicule): " . ($canCreateVehicles ? "✅" : "❌") . "\n";

// Test avec un véhicule de la même organisation
$ownVehicle = App\Models\Vehicle::where('organization_id', $admin->organization_id)->first();
if ($ownVehicle) {
    $canViewOwn = $admin->can('view', $ownVehicle);
    echo "  view (voir véhicule de SON organisation): " . ($canViewOwn ? "✅" : "❌") . "\n";

    $canUpdateOwn = $admin->can('update', $ownVehicle);
    echo "  update (modifier véhicule de SON organisation): " . ($canUpdateOwn ? "✅" : "❌") . "\n";

    $canDeleteOwn = $admin->can('delete', $ownVehicle);
    echo "  delete (supprimer véhicule de SON organisation): " . ($canDeleteOwn ? "✅" : "❌") . "\n";
}

// Test avec un véhicule d'une autre organisation
$otherVehicle = App\Models\Vehicle::where('organization_id', '!=', $admin->organization_id)->first();
if ($otherVehicle) {
    $canViewOther = $admin->can('view', $otherVehicle);
    echo "  view (voir véhicule d'AUTRE organisation): " . ($canViewOther ? "❌ (correct)" : "✅ (problème!)") . "\n";

    $canUpdateOther = $admin->can('update', $otherVehicle);
    echo "  update (modifier véhicule d'AUTRE organisation): " . ($canUpdateOther ? "❌ (correct)" : "✅ (problème!)") . "\n";
}

echo "\n";

// 2. Test DriverPolicy
echo "2. 👤 DRIVER POLICY\n";
echo str_repeat("-", 80) . "\n";

$canViewDrivers = $admin->can('viewAny', App\Models\Driver::class);
echo "  viewAny (voir tous les chauffeurs): " . ($canViewDrivers ? "✅" : "❌") . "\n";

$canCreateDrivers = $admin->can('create', App\Models\Driver::class);
echo "  create (créer un chauffeur): " . ($canCreateDrivers ? "✅" : "❌") . "\n";

// Test avec un chauffeur de la même organisation
$ownDriver = App\Models\Driver::where('organization_id', $admin->organization_id)->first();
if ($ownDriver) {
    $canViewOwn = $admin->can('view', $ownDriver);
    echo "  view (voir chauffeur de SON organisation): " . ($canViewOwn ? "✅" : "❌") . "\n";

    $canUpdateOwn = $admin->can('update', $ownDriver);
    echo "  update (modifier chauffeur de SON organisation): " . ($canUpdateOwn ? "✅" : "❌") . "\n";

    $canDeleteOwn = $admin->can('delete', $ownDriver);
    echo "  delete (supprimer chauffeur de SON organisation): " . ($canDeleteOwn ? "✅" : "❌") . "\n";
}

// Test isolation multi-tenant
$otherDriver = App\Models\Driver::where('organization_id', '!=', $admin->organization_id)->first();
if ($otherDriver) {
    $canViewOther = $admin->can('view', $otherDriver);
    echo "  view (voir chauffeur d'AUTRE organisation): " . ($canViewOther ? "❌ (correct)" : "✅ (problème!)") . "\n";
}

echo "\n";

// 3. Test SupplierPolicy
echo "3. 🏢 SUPPLIER POLICY\n";
echo str_repeat("-", 80) . "\n";

$canViewSuppliers = $admin->can('viewAny', App\Models\Supplier::class);
echo "  viewAny (voir tous les fournisseurs): " . ($canViewSuppliers ? "✅" : "❌") . "\n";

$canCreateSuppliers = $admin->can('create', App\Models\Supplier::class);
echo "  create (créer un fournisseur): " . ($canCreateSuppliers ? "✅" : "❌") . "\n";

// Test avec un fournisseur de la même organisation
$ownSupplier = App\Models\Supplier::where('organization_id', $admin->organization_id)->first();
if ($ownSupplier) {
    $canViewOwn = $admin->can('view', $ownSupplier);
    echo "  view (voir fournisseur de SON organisation): " . ($canViewOwn ? "✅" : "❌") . "\n";

    $canUpdateOwn = $admin->can('update', $ownSupplier);
    echo "  update (modifier fournisseur de SON organisation): " . ($canUpdateOwn ? "✅" : "❌") . "\n";
}

echo "\n";

// 4. Test AssignmentPolicy
echo "4. 📋 ASSIGNMENT POLICY\n";
echo str_repeat("-", 80) . "\n";

$canViewAssignments = $admin->can('viewAny', App\Models\Assignment::class);
echo "  viewAny (voir toutes les affectations): " . ($canViewAssignments ? "✅" : "❌") . "\n";

$canCreateAssignments = $admin->can('create', App\Models\Assignment::class);
echo "  create (créer une affectation): " . ($canCreateAssignments ? "✅" : "❌") . "\n";

// Test avec une affectation de la même organisation
$ownAssignment = App\Models\Assignment::where('organization_id', $admin->organization_id)->first();
if ($ownAssignment) {
    $canViewOwn = $admin->can('view', $ownAssignment);
    echo "  view (voir affectation de SON organisation): " . ($canViewOwn ? "✅" : "❌") . "\n";

    $canUpdateOwn = $admin->can('update', $ownAssignment);
    echo "  update (modifier affectation de SON organisation): " . ($canUpdateOwn ? "✅" : "❌") . "\n";

    $canEndOwn = $admin->can('end', $ownAssignment);
    echo "  end (terminer affectation de SON organisation): " . ($canEndOwn ? "✅" : "❌") . "\n";
}

echo "\n";

// Résumé final
echo str_repeat("=", 80) . "\n";
echo "📊 RÉSUMÉ FINAL\n";
echo str_repeat("=", 80) . "\n\n";

$testResults = [
    'Vehicle' => $canViewVehicles && $canCreateVehicles,
    'Driver' => $canViewDrivers && $canCreateDrivers,
    'Supplier' => $canViewSuppliers && $canCreateSuppliers,
    'Assignment' => $canViewAssignments && $canCreateAssignments,
];

$allPassed = true;
foreach ($testResults as $model => $passed) {
    echo "  " . ($passed ? "✅" : "❌") . " {$model}Policy: " . ($passed ? "OPÉRATIONNEL" : "ÉCHOUÉ") . "\n";
    if (!$passed) {
        $allPassed = false;
    }
}

echo "\n";

if ($allPassed) {
    echo "✨ TOUS LES TESTS RÉUSSIS! ✨\n";
    echo "Le système de permissions Enterprise-Grade est opérationnel.\n";
    echo "L'Admin peut maintenant accéder à toutes les pages de son organisation.\n\n";
    exit(0);
} else {
    echo "⚠️  CERTAINS TESTS ONT ÉCHOUÉ\n";
    echo "Vérifiez les permissions et les policies ci-dessus.\n\n";
    exit(1);
}
