<?php

/**
 * 🔍 DEBUG ENTERPRISE - PROBLÈME D'AUTORISATION 403
 * 
 * Diagnostic approfondi du problème d'autorisation sur /admin/assignments/create
 */

use App\Models\User;
use App\Models\Assignment;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔═══════════════════════════════════════════════════════════════════════╗\n";
echo "║   🔍 DEBUG ENTERPRISE - DIAGNOSTIC ERREUR 403                         ║\n";
echo "╚═══════════════════════════════════════════════════════════════════════╝\n";

// 1. RÉCUPÉRER L'UTILISATEUR ADMIN
$admin = User::whereEmail('admin@zenfleet.dz')->first();
if (!$admin) {
    $admin = User::whereHas('roles', function($q) {
        $q->where('name', 'Admin');
    })->first();
}

if (!$admin) {
    die("❌ Aucun utilisateur admin trouvé!\n");
}

echo "\n👤 UTILISATEUR TESTÉ: {$admin->name} ({$admin->email})\n";
echo "🏢 Organisation ID: {$admin->organization_id}\n";

// 2. ANALYSER LES RÔLES ET PERMISSIONS
echo "\n📋 ANALYSE DES RÔLES\n";
echo str_repeat("─", 70) . "\n";

$roles = $admin->roles;
foreach ($roles as $role) {
    echo "  • Rôle: {$role->name} (ID: {$role->id})\n";
    
    // Permissions du rôle liées aux affectations
    $rolePerms = $role->permissions->filter(function($p) {
        return str_contains(strtolower($p->name), 'assignment');
    });
    
    if ($rolePerms->isNotEmpty()) {
        echo "    Permissions affectations du rôle:\n";
        foreach ($rolePerms as $perm) {
            echo "      - {$perm->name}\n";
        }
    }
}

// 3. VÉRIFIER LES PERMISSIONS SPÉCIFIQUES
echo "\n🔐 TEST DES PERMISSIONS CRITIQUES\n";
echo str_repeat("─", 70) . "\n";

$criticalPermissions = [
    'create assignments',
    'assignments.create',
    'create_assignments',
    'assignment.create',
    'assignments:create'
];

foreach ($criticalPermissions as $perm) {
    // Vérifier si la permission existe
    $exists = Permission::where('name', $perm)->exists();
    $hasIt = $admin->can($perm);
    
    $existsIcon = $exists ? '✓' : '✗';
    $hasIcon = $hasIt ? '✅' : '❌';
    
    echo sprintf("  %s DB | %s User | %s\n", $existsIcon, $hasIcon, $perm);
}

// 4. SIMULER L'AUTORISATION VIA POLICY
echo "\n🛡️ TEST DE LA POLICY AssignmentPolicy\n";
echo str_repeat("─", 70) . "\n";

Auth::login($admin);

// Test via la Policy directement
$policy = app(\App\Policies\AssignmentPolicy::class);

// Test create
$canCreateViaPolicy = false;
try {
    $canCreateViaPolicy = $policy->create($admin);
    $icon = $canCreateViaPolicy ? '✅' : '❌';
    echo "  {$icon} Policy->create(): " . ($canCreateViaPolicy ? 'AUTORISÉ' : 'REFUSÉ') . "\n";
} catch (\Exception $e) {
    echo "  ❌ Erreur Policy->create(): " . $e->getMessage() . "\n";
}

// Test viewAny
try {
    $canViewAny = $policy->viewAny($admin);
    $icon = $canViewAny ? '✅' : '❌';
    echo "  {$icon} Policy->viewAny(): " . ($canViewAny ? 'AUTORISÉ' : 'REFUSÉ') . "\n";
} catch (\Exception $e) {
    echo "  ❌ Erreur Policy->viewAny(): " . $e->getMessage() . "\n";
}

// Test via Gate
$canCreateViaGate = false;
try {
    $canCreateViaGate = $admin->can('create', Assignment::class);
    $icon = $canCreateViaGate ? '✅' : '❌';
    echo "  {$icon} Gate create Assignment: " . ($canCreateViaGate ? 'AUTORISÉ' : 'REFUSÉ') . "\n";
} catch (\Exception $e) {
    echo "  ❌ Erreur Gate: " . $e->getMessage() . "\n";
}

// 5. SIMULER L'ACCÈS AU CONTRÔLEUR
echo "\n🎮 SIMULATION D'ACCÈS AU CONTRÔLEUR\n";
echo str_repeat("─", 70) . "\n";

// Créer une requête simulée
$request = Request::create('/admin/assignments/create', 'GET');
$request->setUserResolver(function () use ($admin) {
    return $admin;
});

app()->instance('request', $request);

// Tester l'autorisation directement
try {
    // Test 1: Via authorize helper
    $authorized1 = false;
    try {
        app(\Illuminate\Auth\Access\Gate::class)->authorize('create assignments');
        $authorized1 = true;
        echo "  ✅ authorize('create assignments'): AUTORISÉ\n";
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        echo "  ❌ authorize('create assignments'): " . $e->getMessage() . "\n";
    }
    
    // Test 2: Via Policy
    try {
        app(\Illuminate\Auth\Access\Gate::class)->authorize('create', Assignment::class);
        echo "  ✅ authorize('create', Assignment::class): AUTORISÉ\n";
    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        echo "  ❌ authorize('create', Assignment::class): " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "  ❌ Erreur générale: " . $e->getMessage() . "\n";
}

// 6. ANALYSER LE CONTRÔLEUR
echo "\n📁 ANALYSE DU CONTRÔLEUR\n";
echo str_repeat("─", 70) . "\n";

$controllerFile = file_get_contents(__DIR__ . '/app/Http/Controllers/Admin/AssignmentController.php');

// Vérifier authorizeResource
if (strpos($controllerFile, 'authorizeResource') !== false) {
    echo "  ⚠️  Le contrôleur utilise authorizeResource() dans __construct\n";
    echo "     Cela peut créer un conflit avec les autorisations manuelles\n";
}

// Vérifier les authorize() dans create()
preg_match_all('/\$this->authorize\([\'"]([^\'"]+)[\'"]\)/', $controllerFile, $matches);
if (!empty($matches[1])) {
    echo "  📌 Autorisations trouvées dans le contrôleur:\n";
    foreach (array_unique($matches[1]) as $auth) {
        echo "     - {$auth}\n";
    }
}

// 7. DIAGNOSTIC ET SOLUTION
echo "\n💡 DIAGNOSTIC\n";
echo str_repeat("═", 70) . "\n";

$problems = [];

// Problème 1: Double autorisation
if (strpos($controllerFile, 'authorizeResource') !== false && strpos($controllerFile, '$this->authorize(\'create assignments\')') !== false) {
    $problems[] = "Double vérification d'autorisation (authorizeResource + authorize manuel)";
}

// Problème 2: Permission manquante
if (!$admin->can('create assignments')) {
    $problems[] = "L'utilisateur n'a pas la permission 'create assignments'";
}

// Problème 3: Policy incorrect
if (!$canCreateViaPolicy) {
    $problems[] = "La Policy refuse l'accès via la méthode create()";
}

// Problème 4: Gate incorrect
if (!$canCreateViaGate) {
    $problems[] = "Le Gate refuse l'accès pour créer un Assignment";
}

if (empty($problems)) {
    echo "  ✅ Aucun problème détecté dans la configuration\n";
} else {
    echo "  ❌ PROBLÈMES DÉTECTÉS:\n";
    foreach ($problems as $idx => $problem) {
        echo "     " . ($idx + 1) . ". {$problem}\n";
    }
}

// 8. SOLUTION PROPOSÉE
echo "\n🔧 SOLUTION ENTERPRISE\n";
echo str_repeat("═", 70) . "\n";
echo "  Le problème vient probablement de la double vérification:\n";
echo "  1. authorizeResource() dans __construct mappe automatiquement\n";
echo "  2. La méthode create() vérifie aussi manuellement\n";
echo "\n";
echo "  SOLUTION: Modifier le contrôleur pour utiliser une approche cohérente\n";
echo "  Voir: fix_assignment_controller_authorization.php\n";

echo "\n";
