<?php

/*
 * 🔐 Script de Vérification des Permissions Utilisateur
 * Usage: php verify_user_permissions.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "\n🔐 VÉRIFICATION DES PERMISSIONS VÉHICULES\n";
echo str_repeat("=", 60) . "\n\n";

// Permissions requises pour le module véhicules
$requiredPermissions = [
    'view vehicles',
    'create vehicles',
    'update vehicles',
    'delete vehicles',
];

// Lister tous les utilisateurs avec leurs permissions
$users = User::with('roles.permissions', 'permissions')->get();

foreach ($users as $user) {
    echo "👤 Utilisateur: {$user->name} (ID: {$user->id})\n";
    echo "   Email: {$user->email}\n";
    echo "   Rôles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "   Permissions véhicules:\n";
    
    foreach ($requiredPermissions as $permission) {
        $hasPermission = $user->can($permission);
        $status = $hasPermission ? "✅" : "❌";
        echo "      $status $permission\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 60) . "\n";
echo "✅ Vérification terminée\n\n";
