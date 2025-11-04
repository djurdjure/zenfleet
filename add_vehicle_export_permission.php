<?php

// Script pour ajouter la permission 'export vehicles'
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Créer la permission si elle n'existe pas
$permission = Permission::firstOrCreate(
    ['name' => 'export vehicles'],
    ['guard_name' => 'web']
);

echo "✅ Permission 'export vehicles' créée ou déjà existante.\n";

// Assigner la permission aux rôles appropriés
$roles = ['Super Admin', 'Admin', 'Gestionnaire Flotte'];

foreach ($roles as $roleName) {
    $role = Role::where('name', $roleName)->first();
    if ($role) {
        if (!$role->hasPermissionTo('export vehicles')) {
            $role->givePermissionTo('export vehicles');
            echo "✅ Permission 'export vehicles' assignée au rôle: $roleName\n";
        } else {
            echo "ℹ️ Le rôle $roleName a déjà la permission 'export vehicles'\n";
        }
    }
}

echo "\n✅ Configuration terminée!\n";
