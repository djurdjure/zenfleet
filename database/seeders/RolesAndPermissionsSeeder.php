<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Assurez-vous que le namespace est correct pour votre modèle User
use Illuminate\Support\Facades\Hash; // Importer la façade Hash pour le mot de passe

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Réinitialiser les rôles et permissions mis en cache.
        // Ceci est utile pour éviter les problèmes de cache lors du re-seed pendant le développement.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === CRÉATION DES PERMISSIONS ===
        // Permissions pour la gestion des utilisateurs
        Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'web']); // CRUD complet sur les utilisateurs
        Permission::firstOrCreate(['name' => 'view users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'web']);

        // Permissions pour la gestion des rôles et permissions
        Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage permissions', 'guard_name' => 'web']); // Souvent réservé au Super Admin

        // Permissions pour les futurs modules (exemples)
        Permission::firstOrCreate(['name' => 'manage fleet', 'guard_name' => 'web']); // Gestion complète de la flotte
        Permission::firstOrCreate(['name' => 'view fleet dashboard', 'guard_name' => 'web']); // Voir le tableau de bord de la flotte
        Permission::firstOrCreate(['name' => 'manage vehicles', 'guard_name' => 'web']); // CRUD véhicules
        Permission::firstOrCreate(['name' => 'view vehicles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage drivers', 'guard_name' => 'web']); // CRUD chauffeurs (en tant qu'entité spécifique)
        Permission::firstOrCreate(['name' => 'view drivers', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage missions', 'guard_name' => 'web']); // CRUD missions
        Permission::firstOrCreate(['name' => 'view missions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage document_categories', 'guard_name' => 'web']);


        // === CRÉATION DES RÔLES ET ASSIGNATION DES PERMISSIONS ===

        // Rôle: Super Administrateur (a accès à tout, souvent géré via un Gate::before)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        // Pour le Super Admin, on peut soit lui donner toutes les permissions explicitement,
        // soit utiliser un Gate::before (voir étape suivante) pour un accès global.
        $superAdminRole->givePermissionTo('manage document_categories');
        // $superAdminRole->givePermissionTo(Permission::all()); // Optionnel si Gate::before est utilisé

        // Rôle: Gestionnaire de Flotte
        $fleetManagerRole = Role::firstOrCreate(['name' => 'Fleet Manager', 'guard_name' => 'web']);
        $fleetManagerRole->givePermissionTo([
            'view users',       // Peut voir les utilisateurs (ex: chauffeurs)
            'create users',     // Peut créer des chauffeurs (si pertinent, sinon gérer via 'manage drivers')
            'edit users',       // Peut modifier les chauffeurs (si pertinent)
            'manage fleet',
            'view fleet dashboard',
            'manage vehicles',
            'view vehicles',
            'manage drivers',
            'view drivers',
            'manage missions',
            'view missions',
            'manage document_categories',
        ]);

        // Rôle: Chauffeur
        $driverRole = Role::firstOrCreate(['name' => 'Driver', 'guard_name' => 'web']);
        $driverRole->givePermissionTo([
            'view fleet dashboard', // Peut voir son propre tableau de bord / missions assignées
            'view missions',        // Peut voir ses missions
            // Ajouter d'autres permissions spécifiques aux chauffeurs au fur et à mesure
        ]);

        // === CRÉATION DES UTILISATEURS DE DÉMONSTRATION ===

        // Utilisateur Super Administrateur
        // `firstOrCreate` évite de créer des doublons si le seeder est exécuté plusieurs fois.
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@zenfleet.com'], // Clé unique pour la recherche
            [                                       // Valeurs à créer si non trouvé
                'name' => 'Super Administrator',
                'password' => Hash::make('password'), // Utilisez un mot de passe sécurisé !
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Utilisateur Gestionnaire de Flotte (pour tests)
        $fleetManagerUser = User::firstOrCreate(
            ['email' => 'manager@zenfleet.com'],
            [
                'name' => 'Fleet Manager User',
                'password' => Hash::make('password123'), // Changez ceci !
            ]
        );
        $fleetManagerUser->assignRole($fleetManagerRole);

         // Utilisateur Chauffeur (pour tests)
         $driverUser = User::firstOrCreate(
            ['email' => 'driver@zenfleet.com'],
            [
                'name' => 'Driver User',
                'password' => Hash::make('password123'), // Changez ceci !
            ]
        );
        $driverUser->assignRole($driverRole);
    }
}
