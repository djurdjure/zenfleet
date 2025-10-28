<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * ====================================================================
 * 💰 MIGRATION PERMISSIONS DÉPENSES - ENTERPRISE GRADE
 * ====================================================================
 * 
 * Ajoute toutes les permissions nécessaires pour le module de gestion
 * des dépenses véhicules avec workflow d'approbation multi-niveaux.
 * 
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ===============================================
        // CRÉATION DES PERMISSIONS
        // ===============================================
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
            'view tco analysis' => 'Voir l\'analyse TCO (Total Cost of Ownership)',
            
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

        // Créer les permissions avec guard web
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // ===============================================
        // ASSIGNATION AUX RÔLES
        // ===============================================
        
        // Super Admin - Toutes les permissions
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_keys($permissions));
        }

        // Admin - Presque toutes les permissions
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $adminPermissions = [
                'view expenses',
                'view any expenses',
                'view expense',
                'create expenses',
                'edit expenses',
                'update expenses',
                'delete expenses',
                'restore expenses',
                'approve expenses',
                'approve expenses level1',
                'approve expenses level2',
                'reject expenses',
                'request expense approval',
                'mark expenses as paid',
                'cancel expense payment',
                'manage expense payments',
                'view expense analytics',
                'view expense reports',
                'view expense dashboard',
                'view expense statistics',
                'view expense trends',
                'view tco analysis',
                'export expenses',
                'import expenses',
                'download expense reports',
                'manage expense groups',
                'manage expense budgets',
                'manage expense categories',
                'manage expense settings',
                'view expense audit logs',
                'export expense audit logs',
                'view all organization expenses',
                'manage recurring expenses',
                'set expense priorities',
                'manage expense attachments',
            ];
            $admin->givePermissionTo($adminPermissions);
        }

        // Finance Manager - Permissions financières complètes
        $financeManager = Role::where('name', 'Finance')->first();
        if ($financeManager) {
            $financePermissions = [
                'view expenses',
                'view any expenses',
                'view expense',
                'create expenses',
                'edit expenses',
                'update expenses',
                'delete expenses',
                'approve expenses',
                'approve expenses level1',
                'approve expenses level2',
                'reject expenses',
                'request expense approval',
                'mark expenses as paid',
                'cancel expense payment',
                'manage expense payments',
                'view expense analytics',
                'view expense reports',
                'view expense dashboard',
                'view expense statistics',
                'view expense trends',
                'view tco analysis',
                'export expenses',
                'import expenses',
                'download expense reports',
                'manage expense groups',
                'manage expense budgets',
                'view expense audit logs',
                'view all organization expenses',
                'manage recurring expenses',
                'manage expense attachments',
            ];
            $financeManager->givePermissionTo($financePermissions);
        }

        // Gestionnaire Flotte - Permissions de gestion
        $fleetManager = Role::where('name', 'Gestionnaire Flotte')->first();
        if ($fleetManager) {
            $fleetPermissions = [
                'view expenses',
                'view expense',
                'create expenses',
                'edit expenses',
                'update expenses',
                'request expense approval',
                'view expense analytics',
                'view expense reports',
                'view expense dashboard',
                'view expense statistics',
                'view expense trends',
                'export expenses',
                'download expense reports',
                'manage expense attachments',
            ];
            $fleetManager->givePermissionTo($fleetPermissions);
        }

        // Manager - Permissions d'approbation niveau 1
        $manager = Role::where('name', 'Manager')->first();
        if ($manager) {
            $managerPermissions = [
                'view expenses',
                'view expense',
                'create expenses',
                'edit expenses',
                'update expenses',
                'approve expenses level1',
                'reject expenses',
                'request expense approval',
                'view expense analytics',
                'view expense reports',
                'view expense dashboard',
                'view expense statistics',
                'export expenses',
                'manage expense attachments',
            ];
            $manager->givePermissionTo($managerPermissions);
        }

        // Superviseur - Permissions limitées
        $supervisor = Role::where('name', 'Superviseur')->first();
        if ($supervisor) {
            $supervisorPermissions = [
                'view expenses',
                'view expense',
                'create expenses',
                'request expense approval',
                'view expense dashboard',
                'view expense statistics',
                'manage expense attachments',
            ];
            $supervisor->givePermissionTo($supervisorPermissions);
        }

        // Chauffeur - Permissions très limitées (ses propres dépenses)
        $driver = Role::where('name', 'Chauffeur')->first();
        if ($driver) {
            $driverPermissions = [
                'view expenses', // Seulement ses propres dépenses via Policy
                'view expense',  // Seulement ses propres dépenses via Policy
                'create expenses',
                'request expense approval',
                'manage expense attachments',
            ];
            $driver->givePermissionTo($driverPermissions);
        }

        // Viewer/Consultant - Lecture seule
        $viewer = Role::where('name', 'Viewer')->first();
        if ($viewer) {
            $viewerPermissions = [
                'view expenses',
                'view expense',
                'view expense dashboard',
                'view expense statistics',
            ];
            $viewer->givePermissionTo($viewerPermissions);
        }

        // ===============================================
        // LOG DE LA MIGRATION
        // ===============================================
        \Log::info('Permissions du module de dépenses créées avec succès', [
            'total_permissions' => count($permissions),
            'timestamp' => now()->toIso8601String()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Liste des permissions à supprimer
        $permissions = [
            'view expenses',
            'view any expenses',
            'view expense',
            'create expenses',
            'edit expenses',
            'update expenses',
            'delete expenses',
            'restore expenses',
            'force delete expenses',
            'approve expenses',
            'approve expenses level1',
            'approve expenses level2',
            'reject expenses',
            'request expense approval',
            'mark expenses as paid',
            'cancel expense payment',
            'manage expense payments',
            'view expense analytics',
            'view expense reports',
            'view expense dashboard',
            'view expense statistics',
            'view expense trends',
            'view tco analysis',
            'export expenses',
            'import expenses',
            'download expense reports',
            'manage expense groups',
            'manage expense budgets',
            'manage expense categories',
            'manage expense workflows',
            'manage expense settings',
            'view expense audit logs',
            'export expense audit logs',
            'bypass expense approval',
            'edit approved expenses',
            'delete approved expenses',
            'view all organization expenses',
            'manage recurring expenses',
            'set expense priorities',
            'manage expense attachments',
        ];

        // Supprimer les permissions
        Permission::whereIn('name', $permissions)->delete();
        
        \Log::info('Permissions du module de dépenses supprimées', [
            'total_removed' => count($permissions),
            'timestamp' => now()->toIso8601String()
        ]);
    }
};
