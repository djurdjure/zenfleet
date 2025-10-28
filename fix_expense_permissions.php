<?php

/**
 * ====================================================================
 * 💰 SCRIPT FIX PERMISSIONS DÉPENSES - ENTERPRISE GRADE
 * ====================================================================
 * 
 * Script de correction immédiate des permissions pour le module dépenses.
 * Exécuter avec: php fix_expense_permissions.php
 * 
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

// Démarrer une transaction
DB::beginTransaction();

try {
    echo "\n================================================================================\n";
    echo "💰 FIX PERMISSIONS MODULE DÉPENSES - ENTERPRISE GRADE\n";
    echo "================================================================================\n\n";

    // ===============================================
    // ÉTAPE 1: CRÉER LES PERMISSIONS
    // ===============================================
    echo "📋 Création des permissions...\n";
    
    $permissions = [
        // Permissions de base CRUD
        'view expenses' => 'Voir la liste des dépenses',
        'view any expenses' => 'Voir toutes les dépenses',
        'view expense' => 'Voir le détail d\'une dépense',
        'create expenses' => 'Créer une nouvelle dépense',
        'edit expenses' => 'Modifier une dépense',
        'update expenses' => 'Mettre à jour une dépense',
        'delete expenses' => 'Supprimer une dépense',
        'restore expenses' => 'Restaurer une dépense supprimée',
        'force delete expenses' => 'Supprimer définitivement une dépense',
        
        // Permissions d'approbation
        'approve expenses' => 'Approuver les dépenses',
        'approve expenses level1' => 'Approuver les dépenses niveau 1',
        'approve expenses level2' => 'Approuver les dépenses niveau 2',
        'reject expenses' => 'Rejeter les dépenses',
        'request expense approval' => 'Demander l\'approbation d\'une dépense',
        
        // Permissions de paiement
        'mark expenses as paid' => 'Marquer les dépenses comme payées',
        'cancel expense payment' => 'Annuler le paiement d\'une dépense',
        'manage expense payments' => 'Gérer les paiements de dépenses',
        
        // Permissions analytics et rapports
        'view expense analytics' => 'Voir les analytics de dépenses',
        'view expense reports' => 'Voir les rapports de dépenses',
        'view expense dashboard' => 'Voir le dashboard de dépenses',
        'view expense statistics' => 'Voir les statistiques de dépenses',
        'view expense trends' => 'Voir les tendances de dépenses',
        'view tco analysis' => 'Voir l\'analyse TCO',
        
        // Permissions d'export/import
        'export expenses' => 'Exporter les dépenses',
        'import expenses' => 'Importer des dépenses',
        'download expense reports' => 'Télécharger les rapports de dépenses',
        
        // Permissions de gestion avancée
        'manage expense groups' => 'Gérer les groupes de dépenses',
        'manage expense budgets' => 'Gérer les budgets de dépenses',
        'manage expense categories' => 'Gérer les catégories de dépenses',
        'manage expense workflows' => 'Gérer les workflows d\'approbation',
        'manage expense settings' => 'Gérer les paramètres de dépenses',
        
        // Permissions d'audit
        'view expense audit logs' => 'Voir les logs d\'audit des dépenses',
        'export expense audit logs' => 'Exporter les logs d\'audit',
        
        // Permissions spéciales
        'bypass expense approval' => 'Contourner l\'approbation des dépenses',
        'edit approved expenses' => 'Modifier les dépenses approuvées',
        'delete approved expenses' => 'Supprimer les dépenses approuvées',
        'view all organization expenses' => 'Voir toutes les dépenses de l\'organisation',
        'manage recurring expenses' => 'Gérer les dépenses récurrentes',
        'set expense priorities' => 'Définir les priorités des dépenses',
        'manage expense attachments' => 'Gérer les pièces jointes des dépenses',
    ];

    $createdCount = 0;
    $existingCount = 0;
    
    foreach ($permissions as $name => $description) {
        $permission = Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            ['description' => $description]
        );
        
        if ($permission->wasRecentlyCreated) {
            echo "   ✅ Créé: $name\n";
            $createdCount++;
        } else {
            echo "   ⏭️  Existe déjà: $name\n";
            $existingCount++;
        }
    }
    
    echo "\n📊 Résumé: $createdCount nouvelles permissions, $existingCount existantes\n\n";

    // ===============================================
    // ÉTAPE 2: ASSIGNER AUX RÔLES
    // ===============================================
    echo "👥 Assignation des permissions aux rôles...\n\n";

    // Configuration des permissions par rôle
    $rolePermissions = [
        'Super Admin' => array_keys($permissions), // Toutes les permissions
        
        'Admin' => [
            'view expenses', 'view any expenses', 'view expense', 'create expenses',
            'edit expenses', 'update expenses', 'delete expenses', 'restore expenses',
            'approve expenses', 'approve expenses level1', 'approve expenses level2',
            'reject expenses', 'request expense approval', 'mark expenses as paid',
            'cancel expense payment', 'manage expense payments', 'view expense analytics',
            'view expense reports', 'view expense dashboard', 'view expense statistics',
            'view expense trends', 'view tco analysis', 'export expenses', 'import expenses',
            'download expense reports', 'manage expense groups', 'manage expense budgets',
            'manage expense categories', 'manage expense settings', 'view expense audit logs',
            'export expense audit logs', 'view all organization expenses',
            'manage recurring expenses', 'set expense priorities', 'manage expense attachments',
        ],
        
        'Finance' => [
            'view expenses', 'view any expenses', 'view expense', 'create expenses',
            'edit expenses', 'update expenses', 'delete expenses', 'approve expenses',
            'approve expenses level1', 'approve expenses level2', 'reject expenses',
            'request expense approval', 'mark expenses as paid', 'cancel expense payment',
            'manage expense payments', 'view expense analytics', 'view expense reports',
            'view expense dashboard', 'view expense statistics', 'view expense trends',
            'view tco analysis', 'export expenses', 'import expenses', 'download expense reports',
            'manage expense groups', 'manage expense budgets', 'view expense audit logs',
            'view all organization expenses', 'manage recurring expenses', 'manage expense attachments',
        ],
        
        'Gestionnaire Flotte' => [
            'view expenses', 'view expense', 'create expenses', 'edit expenses',
            'update expenses', 'request expense approval', 'view expense analytics',
            'view expense reports', 'view expense dashboard', 'view expense statistics',
            'view expense trends', 'export expenses', 'download expense reports',
            'manage expense attachments',
        ],
        
        'Manager' => [
            'view expenses', 'view expense', 'create expenses', 'edit expenses',
            'update expenses', 'approve expenses level1', 'reject expenses',
            'request expense approval', 'view expense analytics', 'view expense reports',
            'view expense dashboard', 'view expense statistics', 'export expenses',
            'manage expense attachments',
        ],
        
        'Superviseur' => [
            'view expenses', 'view expense', 'create expenses', 'request expense approval',
            'view expense dashboard', 'view expense statistics', 'manage expense attachments',
        ],
        
        'Chauffeur' => [
            'view expenses', 'view expense', 'create expenses', 'request expense approval',
            'manage expense attachments',
        ],
        
        'Viewer' => [
            'view expenses', 'view expense', 'view expense dashboard', 'view expense statistics',
        ]
    ];

    foreach ($rolePermissions as $roleName => $permissions) {
        $role = Role::where('name', $roleName)->first();
        
        if ($role) {
            echo "🎭 Rôle: $roleName\n";
            
            // Synchroniser les permissions (ajoute les nouvelles sans supprimer les existantes)
            $existingPermissions = $role->permissions->pluck('name')->toArray();
            $newPermissions = array_diff($permissions, $existingPermissions);
            
            if (count($newPermissions) > 0) {
                $role->givePermissionTo($newPermissions);
                echo "   ✅ " . count($newPermissions) . " nouvelles permissions ajoutées\n";
                
                // Afficher les permissions ajoutées
                foreach ($newPermissions as $perm) {
                    echo "      + $perm\n";
                }
            } else {
                echo "   ⏭️  Toutes les permissions déjà assignées\n";
            }
            echo "\n";
        } else {
            echo "⚠️  Rôle '$roleName' non trouvé\n\n";
        }
    }

    // ===============================================
    // ÉTAPE 3: DONNER L'ACCÈS IMMÉDIAT À L'ADMIN
    // ===============================================
    echo "🚀 Attribution d'accès immédiat aux utilisateurs Admin...\n";
    
    // Trouver tous les utilisateurs avec le rôle Admin ou Super Admin
    $adminUsers = \App\Models\User::role(['Admin', 'Super Admin', 'Finance', 'Gestionnaire Flotte'])->get();
    
    foreach ($adminUsers as $user) {
        $roles = $user->roles->pluck('name')->implode(', ');
        echo "   👤 {$user->first_name} {$user->last_name} ($roles) - Accès complet au module dépenses\n";
    }

    // ===============================================
    // ÉTAPE 4: VÉRIFICATION FINALE
    // ===============================================
    echo "\n🔍 Vérification finale...\n";
    
    // Vérifier qu'au moins un utilisateur a la permission
    $usersWithAccess = \App\Models\User::permission('view expenses')->count();
    echo "   📊 $usersWithAccess utilisateur(s) ont accès au module dépenses\n";
    
    if ($usersWithAccess == 0) {
        echo "   ⚠️  ATTENTION: Aucun utilisateur n'a accès au module!\n";
        echo "   💡 Solution: Assignez manuellement le rôle Admin ou Finance à un utilisateur\n";
    }

    // Commit la transaction
    DB::commit();
    
    echo "\n================================================================================\n";
    echo "✅ PERMISSIONS CONFIGURÉES AVEC SUCCÈS!\n";
    echo "================================================================================\n";
    echo "\n📌 Actions suivantes:\n";
    echo "   1. Vider le cache: php artisan cache:clear\n";
    echo "   2. Vider le cache des permissions: php artisan permission:cache-reset\n";
    echo "   3. Tester l'accès à: /admin/vehicle-expenses\n";
    echo "\n💡 Si l'erreur persiste:\n";
    echo "   - Vérifiez que l'utilisateur a bien un des rôles avec permissions\n";
    echo "   - Déconnectez-vous et reconnectez-vous\n";
    echo "   - Exécutez: php artisan config:clear && php artisan cache:clear\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
