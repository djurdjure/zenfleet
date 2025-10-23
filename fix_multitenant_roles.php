<?php

/*
 * 🔧 FIX MULTI-TENANT ROLES - Solution Temporaire
 * 
 * Le système Spatie personnalisé pour multi-tenant ne fonctionne pas correctement.
 * Cette solution contourne le problème en assignant les rôles avec organization_id.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "\n🔧 FIX MULTI-TENANT ROLES - SOLUTION ENTERPRISE\n";
echo str_repeat("=", 70) . "\n\n";

// Utilisateurs clés à configurer
$usersToFix = [
    'superadmin@zenfleet.dz' => 'Super Admin',
    'admin@zenfleet.dz' => 'Admin',
];

foreach ($usersToFix as $email => $roleName) {
    $user = User::where('email', $email)->first();
    $role = Role::where('name', $roleName)->first();
    
    if (!$user) {
        echo "⚠️  Utilisateur $email introuvable\n";
        continue;
    }
    
    if (!$role) {
        echo "⚠️  Rôle '$roleName' introuvable\n";
        continue;
    }
    
    echo "👤 Configuration: $email\n";
    echo "   User ID: {$user->id}, Organization ID: {$user->organization_id}\n";
    echo "   Rôle: {$roleName} (ID: {$role->id})\n";
    
    // Nettoyer les assignations existantes
    DB::table('model_has_roles')
        ->where('model_id', $user->id)
        ->where('model_type', 'App\\Models\\User')
        ->delete();
    
    // Insérer directement avec organization_id
    try {
        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => 'App\\Models\\User',
            'model_id' => $user->id,
            'organization_id' => $user->organization_id,
        ]);
        
        echo "   ✅ Rôle '$roleName' assigné avec organization_id = {$user->organization_id}\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Erreur: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 70) . "\n";
echo "🔄 Nettoyage du cache des permissions...\n";
\Artisan::call('permission:cache-reset');
echo "✅ Cache nettoyé\n\n";

echo "🔍 VALIDATION FINALE\n";
echo str_repeat("=", 70) . "\n\n";

foreach ($usersToFix as $email => $roleName) {
    $user = User::where('email', $email)->first();
    if (!$user) continue;
    
    // Forcer le rechargement
    $user->unsetRelation('roles');
    $user->load('roles');
    
    echo "👤 $email:\n";
    
    // Vérifier dans la base directement
    $dbRoles = DB::table('model_has_roles')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('model_has_roles.model_id', $user->id)
        ->where('model_has_roles.model_type', 'App\\Models\\User')
        ->select('roles.name', 'model_has_roles.organization_id')
        ->get();
    
    echo "   Rôles en DB: " . $dbRoles->pluck('name')->join(', ') . "\n";
    echo "   Rôles via Laravel: " . $user->roles->pluck('name')->join(', ') . "\n";
    
    // Tester les permissions
    $permissions = [
        'view vehicles',
        'create vehicles',
        'update vehicles',
        'delete vehicles',
    ];
    
    echo "   Permissions:\n";
    foreach ($permissions as $permission) {
        // Test direct via DB
        $hasPermissionDirect = DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->whereIn('role_has_permissions.role_id', $dbRoles->pluck('id')->toArray())
            ->where('permissions.name', $permission)
            ->exists();
        
        $statusDB = $hasPermissionDirect ? "✅" : "❌";
        
        // Test via Laravel
        $hasPermissionLaravel = $user->can($permission);
        $statusLaravel = $hasPermissionLaravel ? "✅" : "❌";
        
        echo "      $statusDB (DB) / $statusLaravel (Laravel) - $permission\n";
    }
    
    echo "\n";
}

echo "✅ Configuration terminée\n\n";
