<?php

/**
 * Script pour créer un utilisateur Admin d'organisation
 * Usage: docker compose exec -u zenfleet_user php php create_organization_admin.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 CRÉATION D'UN UTILISATEUR ADMIN D'ORGANISATION\n";
echo str_repeat("=", 70) . "\n\n";

// Vérifier si l'organisation FADERCO existe
$faderco = App\Models\Organization::where('name', 'FADERCO')->first();

if (!$faderco) {
    echo "❌ Organisation FADERCO introuvable\n";
    exit(1);
}

echo "✅ Organisation trouvée: {$faderco->name} (ID: {$faderco->id})\n\n";

// Vérifier si le rôle Admin existe
$adminRole = Spatie\Permission\Models\Role::where('name', 'Admin')->first();

if (!$adminRole) {
    echo "❌ Rôle 'Admin' introuvable\n";
    exit(1);
}

echo "✅ Rôle 'Admin' trouvé (ID: {$adminRole->id})\n";
echo "   Permissions: " . $adminRole->permissions->count() . "\n\n";

// Vérifier si l'utilisateur existe déjà
$existingUser = App\Models\User::where('email', 'admin@faderco.dz')->first();

if ($existingUser) {
    echo "⚠️  L'utilisateur admin@faderco.dz existe déjà\n";
    echo "   Nom: {$existingUser->first_name} {$existingUser->last_name}\n";
    echo "   Organisation: {$existingUser->organization->name}\n";
    echo "   Rôles: " . $existingUser->getRoleNames()->implode(', ') . "\n\n";

    $response = readline("Voulez-vous réinitialiser le mot de passe ? (y/n): ");

    if (strtolower($response) === 'y') {
        $existingUser->password = Hash::make('Admin123!@#');
        $existingUser->save();

        // S'assurer qu'il a le rôle Admin
        if (!$existingUser->hasRole('Admin')) {
            $existingUser->assignRole('Admin');
        }

        echo "✅ Mot de passe réinitialisé: Admin123!@#\n";
        echo "✅ Rôle Admin assigné\n\n";
    }

    echo str_repeat("=", 70) . "\n";
    echo "📧 Email: admin@faderco.dz\n";
    echo "🔑 Mot de passe: Admin123!@#\n";
    echo "🏢 Organisation: FADERCO\n";
    echo "👤 Rôle: Admin\n";
    echo str_repeat("=", 70) . "\n";
    exit(0);
}

// Créer le nouvel utilisateur
try {
    $user = App\Models\User::create([
        'first_name' => 'Admin',
        'last_name' => 'FADERCO',
        'name' => 'Admin FADERCO', // Pour compatibilité
        'email' => 'admin@faderco.dz',
        'password' => Hash::make('Admin123!@#'),
        'organization_id' => $faderco->id,
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    echo "✅ Utilisateur créé avec succès\n";
    echo "   ID: {$user->id}\n";
    echo "   Nom: {$user->first_name} {$user->last_name}\n";
    echo "   Email: {$user->email}\n\n";

    // Assigner le rôle Admin
    $user->assignRole('Admin');
    echo "✅ Rôle 'Admin' assigné\n\n";

    // Afficher les permissions
    echo "📋 Permissions héritées du rôle Admin:\n";
    $permissions = $adminRole->permissions->pluck('name')->sort();
    $count = 1;
    foreach ($permissions as $permission) {
        echo "   {$count}. {$permission}\n";
        $count++;
    }

    echo "\n" . str_repeat("=", 70) . "\n";
    echo "✨ COMPTE ADMIN D'ORGANISATION CRÉÉ AVEC SUCCÈS!\n";
    echo str_repeat("=", 70) . "\n\n";

    echo "🔐 INFORMATIONS DE CONNEXION:\n";
    echo "   URL: http://localhost/login\n";
    echo "   📧 Email: admin@faderco.dz\n";
    echo "   🔑 Mot de passe: Admin123!@#\n";
    echo "   🏢 Organisation: FADERCO\n";
    echo "   👤 Rôle: Admin\n\n";

    echo "🎯 CAPACITÉS:\n";
    echo "   ✅ Gérer les utilisateurs de son organisation\n";
    echo "   ✅ Gérer les véhicules\n";
    echo "   ✅ Gérer les chauffeurs\n";
    echo "   ✅ Voir les maintenances\n";
    echo "   ✅ Gérer les affectations\n";
    echo "   ❌ Ne peut PAS voir d'autres organisations\n";
    echo "   ❌ Ne peut PAS assigner le rôle Super Admin\n\n";

    echo "⚠️  LIMITATIONS DE SÉCURITÉ:\n";
    echo "   - Accès limité à l'organisation FADERCO uniquement\n";
    echo "   - Ne peut pas s'auto-promouvoir en Super Admin\n";
    echo "   - Ne peut pas créer d'utilisateurs Super Admin\n";
    echo "   - Ne peut pas modifier les utilisateurs d'autres organisations\n\n";

    echo str_repeat("=", 70) . "\n";

} catch (\Exception $e) {
    echo "❌ Erreur lors de la création: {$e->getMessage()}\n";
    echo "   Trace: {$e->getTraceAsString()}\n";
    exit(1);
}
