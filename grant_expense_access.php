<?php
/**
 * Script rapide pour donner l'accès au module de dépenses
 * Exécuter avec: php grant_expense_access.php
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

echo "\n=== Attribution rapide des permissions de dépenses ===\n\n";

// Créer les permissions essentielles si elles n'existent pas
$essentialPermissions = [
    'view expenses',
    'create expenses',
    'edit expenses',
    'view expense analytics',
    'view expense dashboard',
];

foreach ($essentialPermissions as $permName) {
    Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
}

// Donner les permissions aux rôles principaux
$roles = ['Super Admin', 'Admin', 'Finance', 'Gestionnaire Flotte'];

foreach ($roles as $roleName) {
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        $role->givePermissionTo($essentialPermissions);
        echo "✅ Permissions ajoutées au rôle: $roleName\n";
    }
}

// Trouver l'utilisateur principal (ID 1 ou premier admin)
$user = User::find(1) ?? User::role(['Admin', 'Super Admin'])->first();

if ($user) {
    // Donner directement les permissions à l'utilisateur
    $user->givePermissionTo($essentialPermissions);
    echo "\n✅ Permissions attribuées à: {$user->first_name} {$user->last_name}\n";
    echo "   Email: {$user->email}\n";
} else {
    echo "⚠️ Aucun utilisateur admin trouvé\n";
}

// Vider le cache
app()['cache']->forget('spatie.permission.cache');

echo "\n✅ Cache des permissions vidé\n";
echo "\n=== Terminé! Testez maintenant l'accès au module de dépenses ===\n\n";
