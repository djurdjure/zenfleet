<?php

/**
 * Test d'accès à la création d'affectations
 */

use App\Models\User;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n🧪 TEST D'ACCÈS À LA CRÉATION D'AFFECTATIONS\n";
echo str_repeat("=", 60) . "\n";

// Récupérer l'utilisateur admin
$admin = User::whereEmail('admin@zenfleet.dz')->first();

if (!$admin) {
    echo "❌ Utilisateur admin@zenfleet.dz non trouvé !\n";
    exit(1);
}

echo "👤 Utilisateur: {$admin->name} ({$admin->email})\n";
echo "🏢 Organisation: {$admin->organization_id}\n\n";

// Simuler la connexion de l'utilisateur
auth()->login($admin);

echo "📋 TEST DES PERMISSIONS\n";
echo str_repeat("-", 60) . "\n";

// Test des permissions critiques
$permissions = [
    'view assignments',
    'create assignments',
    'edit assignments',
    'end assignments',
    'delete assignments',
    'assignments.create',
    'assignments.view',
    'assignments.end'
];

$allPass = true;

foreach ($permissions as $permission) {
    $hasPermission = $admin->can($permission);
    $icon = $hasPermission ? '✅' : '❌';
    echo "  {$icon} {$permission}: " . ($hasPermission ? 'OUI' : 'NON') . "\n";
    
    if (!$hasPermission && in_array($permission, ['create assignments', 'assignments.create'])) {
        $allPass = false;
    }
}

echo "\n📋 TEST DES POLICIES\n";
echo str_repeat("-", 60) . "\n";

// Test de la policy AssignmentPolicy
$assignment = new \App\Models\Assignment();
$assignment->organization_id = $admin->organization_id;

// Test create via policy
$canCreate = $admin->can('create', \App\Models\Assignment::class);
$icon = $canCreate ? '✅' : '❌';
echo "  {$icon} Policy create(): " . ($canCreate ? 'OUI' : 'NON') . "\n";

// Test viewAny via policy
$canViewAny = $admin->can('viewAny', \App\Models\Assignment::class);
$icon = $canViewAny ? '✅' : '❌';
echo "  {$icon} Policy viewAny(): " . ($canViewAny ? 'OUI' : 'NON') . "\n";

if (!$canCreate) {
    $allPass = false;
}

echo "\n📋 SIMULATION D'ACCÈS AU CONTRÔLEUR\n";
echo str_repeat("-", 60) . "\n";

try {
    // Créer une instance du contrôleur
    $controller = app(\App\Http\Controllers\Admin\AssignmentController::class);
    
    // Créer une fausse requête
    $request = Request::create('/admin/assignments/create', 'GET');
    $request->setUserResolver(function () use ($admin) {
        return $admin;
    });
    
    // Définir la requête dans l'application
    app()->instance('request', $request);
    
    echo "  ✅ Le contrôleur peut être instancié\n";
    
    // Tester si l'utilisateur peut accéder à la méthode create
    $authorized = true;
    try {
        $response = $controller->create();
        echo "  ✅ Accès à la méthode create() autorisé\n";
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        echo "  ❌ Accès refusé: " . $e->getMessage() . "\n";
        $authorized = false;
        $allPass = false;
    }
    
} catch (\Exception $e) {
    echo "  ❌ Erreur: " . $e->getMessage() . "\n";
    $allPass = false;
}

echo "\n" . str_repeat("=", 60) . "\n";

if ($allPass) {
    echo "✅ SUCCÈS: L'utilisateur admin peut créer des affectations !\n";
    echo "\n🎉 Le problème de permission est RÉSOLU !\n";
    echo "L'utilisateur peut maintenant accéder à:\n";
    echo "  • http://localhost/admin/assignments/create\n";
    echo "  • Toutes les fonctionnalités du module affectations\n";
} else {
    echo "❌ PROBLÈME: Certaines permissions sont manquantes\n";
    echo "\n⚠️  Actions recommandées:\n";
    echo "  1. Exécuter: php fix_assignment_permissions_enterprise.php\n";
    echo "  2. Vider le cache: php artisan cache:clear\n";
    echo "  3. Redémarrer les services Docker\n";
}

echo "\n";
