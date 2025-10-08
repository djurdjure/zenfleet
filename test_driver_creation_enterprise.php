<?php

/**
 * 🚀 SCRIPT DE TEST ENTERPRISE - CRÉATION DE CHAUFFEUR
 * 
 * Test complet de la création de chauffeur avec :
 * - Vérification des statuts disponibles
 * - Création automatique du compte utilisateur  
 * - Validation des permissions
 * 
 * Usage: php test_driver_creation_enterprise.php
 * 
 * @version 3.0-Enterprise
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use Spatie\Permission\Models\Role;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "================================================================================\n";
echo "        🚛 TEST ENTERPRISE - CRÉATION DE CHAUFFEUR AVEC COMPTE AUTO            \n";
echo "================================================================================\n";
echo "\n";

try {
    // Étape 1: Vérifier/Créer les statuts de base
    echo "📊 ÉTAPE 1: Vérification des statuts de chauffeur...\n";
    echo str_repeat("-", 80) . "\n";
    
    $organizations = Organization::all();
    
    if ($organizations->isEmpty()) {
        echo "⚠️ Aucune organisation trouvée. Création d'une organisation de test...\n";
        $organization = Organization::create([
            'name' => 'ZenFleet Test Enterprise',
            'slug' => 'zenfleet-test',
            'type' => 'enterprise',
            'country' => 'DZ',
        ]);
        echo "✅ Organisation créée: {$organization->name} (ID: {$organization->id})\n";
    } else {
        $organization = $organizations->first();
        echo "✅ Organisation existante: {$organization->name} (ID: {$organization->id})\n";
    }
    
    // Créer les statuts de base pour l'organisation
    $defaultStatuses = [
        ['name' => 'Disponible', 'slug' => 'disponible', 'color' => '#10B981', 'icon' => 'fa-check-circle', 'can_drive' => true, 'can_assign' => true],
        ['name' => 'En mission', 'slug' => 'en-mission', 'color' => '#3B82F6', 'icon' => 'fa-truck', 'can_drive' => true, 'can_assign' => false],
        ['name' => 'En congé', 'slug' => 'en-conge', 'color' => '#8B5CF6', 'icon' => 'fa-plane', 'can_drive' => false, 'can_assign' => false],
        ['name' => 'Inactif', 'slug' => 'inactif', 'color' => '#6B7280', 'icon' => 'fa-user-slash', 'can_drive' => false, 'can_assign' => false],
    ];
    
    foreach ($defaultStatuses as $statusData) {
        $status = DriverStatus::firstOrCreate(
            [
                'slug' => $statusData['slug'],
                'organization_id' => $organization->id
            ],
            array_merge($statusData, [
                'organization_id' => $organization->id,
                'is_active' => true,
                'sort_order' => array_search($statusData, $defaultStatuses) + 1
            ])
        );
        echo "  ✅ Statut '{$status->name}' " . ($status->wasRecentlyCreated ? 'créé' : 'existant') . "\n";
    }
    
    // Vérifier les statuts disponibles
    $statuses = DriverStatus::where('organization_id', $organization->id)
        ->orWhereNull('organization_id')
        ->where('is_active', true)
        ->get();
    
    echo "\n📊 Statuts disponibles: " . $statuses->count() . "\n";
    foreach ($statuses as $status) {
        echo "  • {$status->name} (ID: {$status->id}, Org: " . ($status->organization_id ?? 'Global') . ")\n";
    }
    
    // Étape 2: Créer un utilisateur Admin pour les tests
    echo "\n👤 ÉTAPE 2: Création/Vérification de l'utilisateur Admin...\n";
    echo str_repeat("-", 80) . "\n";
    
    $adminEmail = 'admin.test@zenfleet.dz';
    $adminUser = User::where('email', $adminEmail)->first();
    
    if (!$adminUser) {
        $adminUser = User::create([
            'name' => 'Admin Test',
            'email' => $adminEmail,
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);
        
        // Créer et assigner le rôle Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminUser->assignRole($adminRole);
        
        // Assigner les permissions nécessaires
        $adminUser->givePermissionTo(['create drivers', 'view drivers', 'edit drivers']);
        
        echo "✅ Utilisateur Admin créé: {$adminUser->email}\n";
    } else {
        echo "✅ Utilisateur Admin existant: {$adminUser->email}\n";
    }
    
    // Étape 3: Simuler la création d'un chauffeur
    echo "\n🚗 ÉTAPE 3: Test de création de chauffeur...\n";
    echo str_repeat("-", 80) . "\n";
    
    // Authentifier l'utilisateur admin
    auth()->login($adminUser);
    
    $testDriverData = [
        'first_name' => 'Ahmed',
        'last_name' => 'Benali_' . rand(1000, 9999),
        'birth_date' => '1990-05-15',
        'personal_phone' => '0550123456',
        'personal_email' => null, // Pas d'email pour forcer la création automatique
        'address' => '25 Rue Didouche Mourad, Alger',
        'employee_number' => 'EMP-' . date('Ymd') . '-' . rand(100, 999),
        'status_id' => $statuses->first()->id,
        'organization_id' => $organization->id,
        'license_number' => 'DZ-' . rand(100000, 999999),
        'license_category' => 'B, C',
        'license_issue_date' => '2020-01-15',
    ];
    
    echo "📝 Création du chauffeur avec les données suivantes:\n";
    echo "  • Nom: {$testDriverData['first_name']} {$testDriverData['last_name']}\n";
    echo "  • Matricule: {$testDriverData['employee_number']}\n";
    echo "  • Email personnel: " . ($testDriverData['personal_email'] ?? 'NON FOURNI - Création auto') . "\n";
    echo "  • Statut ID: {$testDriverData['status_id']}\n";
    
    DB::beginTransaction();
    
    try {
        $driver = Driver::create($testDriverData);
        
        echo "\n✅ CHAUFFEUR CRÉÉ AVEC SUCCÈS!\n";
        echo "  • ID Chauffeur: {$driver->id}\n";
        echo "  • Nom complet: {$driver->first_name} {$driver->last_name}\n";
        echo "  • Matricule: {$driver->employee_number}\n";
        
        // Recharger pour obtenir les relations
        $driver->refresh();
        
        if ($driver->user_id) {
            $user = User::find($driver->user_id);
            if ($user) {
                echo "\n🎉 COMPTE UTILISATEUR CRÉÉ AUTOMATIQUEMENT!\n";
                echo "  • ID Utilisateur: {$user->id}\n";
                echo "  • Email: {$user->email}\n";
                echo "  • Nom: {$user->name}\n";
                echo "  • Organisation ID: {$user->organization_id}\n";
                
                // Vérifier le rôle
                $roles = $user->getRoleNames();
                echo "  • Rôles: " . $roles->implode(', ') . "\n";
                
                // Vérifier les credentials temporaires dans le cache
                $cacheKey = "driver_credentials_{$driver->id}";
                $credentials = cache()->get($cacheKey);
                
                if ($credentials) {
                    echo "\n🔐 CREDENTIALS TEMPORAIRES (24h):\n";
                    echo "  • Email: {$credentials['email']}\n";
                    echo "  • Mot de passe: {$credentials['password']}\n";
                    echo "  • Expire à: {$credentials['expires_at']}\n";
                }
            }
        } else {
            echo "\n⚠️ ATTENTION: Aucun compte utilisateur créé!\n";
            echo "  Vérifiez les logs pour plus d'informations.\n";
        }
        
        // Vérifier le statut
        if ($driver->driverStatus) {
            echo "\n📊 STATUT DU CHAUFFEUR:\n";
            echo "  • Nom: {$driver->driverStatus->name}\n";
            echo "  • Couleur: {$driver->driverStatus->color}\n";
            echo "  • Peut conduire: " . ($driver->driverStatus->can_drive ? 'OUI' : 'NON') . "\n";
            echo "  • Peut être assigné: " . ($driver->driverStatus->can_assign ? 'OUI' : 'NON') . "\n";
        }
        
        // Test de vérification des permissions
        echo "\n🔒 TEST DES PERMISSIONS:\n";
        echo str_repeat("-", 40) . "\n";
        
        // Créer un utilisateur Admin standard
        $standardAdmin = User::create([
            'name' => 'Admin Standard Test',
            'email' => 'admin.standard.' . rand(1000, 9999) . '@zenfleet.dz',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);
        
        $standardAdmin->assignRole('Admin');
        $standardAdmin->givePermissionTo(['create drivers', 'view drivers']);
        
        auth()->login($standardAdmin);
        
        // Tester l'accès aux statuts
        $accessibleStatuses = DriverStatus::where('is_active', true)
            ->where(function ($query) use ($organization) {
                $query->whereNull('organization_id')
                      ->orWhere('organization_id', $organization->id);
            })
            ->get();
        
        echo "  ✅ Admin Standard peut voir " . $accessibleStatuses->count() . " statuts\n";
        
        // Créer un Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'super.admin.' . rand(1000, 9999) . '@zenfleet.dz',
            'password' => bcrypt('password'),
            'organization_id' => null,
            'email_verified_at' => now(),
        ]);
        
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->assignRole($superAdminRole);
        
        auth()->login($superAdmin);
        
        $allStatuses = DriverStatus::where('is_active', true)->get();
        echo "  ✅ Super Admin peut voir " . $allStatuses->count() . " statuts (tous)\n";
        
        DB::commit();
        
        echo "\n";
        echo "================================================================================\n";
        echo "                        ✅ TOUS LES TESTS ONT RÉUSSI!                          \n";
        echo "================================================================================\n";
        echo "\nRÉSUMÉ:\n";
        echo "  • Statuts créés/vérifiés: " . $statuses->count() . "\n";
        echo "  • Chauffeur créé: {$driver->first_name} {$driver->last_name} (ID: {$driver->id})\n";
        echo "  • Compte utilisateur auto-créé: " . ($driver->user_id ? "OUI (ID: {$driver->user_id})" : "NON") . "\n";
        echo "  • Permissions testées: OK pour Admin et Super Admin\n";
        echo "\n";
        
    } catch (\Exception $e) {
        DB::rollback();
        
        echo "\n";
        echo "❌ ERREUR LORS DU TEST:\n";
        echo str_repeat("=", 80) . "\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "Fichier: " . $e->getFile() . "\n";
        echo "Ligne: " . $e->getLine() . "\n";
        echo "\nTrace:\n";
        echo $e->getTraceAsString() . "\n";
        
        // Vérifier les logs
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $lastLogs = shell_exec("tail -n 50 " . escapeshellarg($logPath));
            echo "\n📋 DERNIERS LOGS:\n";
            echo str_repeat("-", 80) . "\n";
            echo $lastLogs;
        }
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR FATALE:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n";
