<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 VÉRIFICATION PERMISSIONS FORMAT MODERNE                ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->email}\n";
echo "👑 Rôle: " . ($user->roles->pluck('name')->first() ?? 'Aucun') . "\n\n";

echo str_repeat("─", 66) . "\n";
echo "PERMISSIONS ACTUELLES DE L'UTILISATEUR\n";
echo str_repeat("─", 66) . "\n\n";

$permissions = $user->getAllPermissions()->pluck('name')->toArray();
sort($permissions);

// Filtrer les permissions liées aux assignments
$assignmentPermissions = array_filter($permissions, function($perm) {
    return str_contains(strtolower($perm), 'assignment');
});

echo "Permissions liées aux assignments:\n";
foreach ($assignmentPermissions as $perm) {
    echo "  • {$perm}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "TEST DES FORMATS\n";
echo str_repeat("─", 66) . "\n\n";

$formats = [
    // Ancien format (avec espace)
    'create assignments',
    'view assignments',
    'edit assignments',
    'end assignments',

    // Format moderne (avec point)
    'assignments.create',
    'assignments.view',
    'assignments.update',
    'assignments.delete',
    'assignments.end',
];

echo "Format ANCIEN (avec espace):\n";
foreach (['create assignments', 'view assignments', 'edit assignments', 'end assignments'] as $perm) {
    $has = $user->can($perm);
    echo "  • {$perm}: " . ($has ? "✅" : "❌") . "\n";
}

echo "\nFormat MODERNE (avec point):\n";
foreach (['assignments.create', 'assignments.view', 'assignments.update', 'assignments.delete', 'assignments.end'] as $perm) {
    $has = $user->can($perm);
    echo "  • {$perm}: " . ($has ? "✅" : "❌") . "\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "🎯 CONCLUSION\n";
echo str_repeat("─", 66) . "\n\n";

$hasModern = $user->can('assignments.create');
$hasOld = $user->can('create assignments');

if ($hasModern) {
    echo "✅ L'utilisateur a la permission 'assignments.create' (format moderne)\n";
    echo "✅ Le middleware EnterprisePermissionMiddleware devrait laisser passer\n";
} elseif ($hasOld) {
    echo "⚠️  L'utilisateur a la permission 'create assignments' (ancien format)\n";
    echo "⚠️  Il faut migrer vers le format moderne ou créer la permission\n";
} else {
    echo "❌ L'utilisateur n'a AUCUNE des deux permissions\n";
    echo "❌ Il faut attribuer la permission 'assignments.create' à l'utilisateur\n";
}

echo "\n";
