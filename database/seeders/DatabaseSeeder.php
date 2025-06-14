<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // La méthode call() exécute les seeders dans l'ordre spécifié.
        $this->call([
            // Seeders pour la gestion des utilisateurs et des accès
            RoleSeeder::class,
            PermissionSeeder::class,
            ValidationLevelSeeder::class,
	    \Database\Seeders\Maintenance\MaintenanceDataSeeder::class,


            // --- AJOUT IMPORTANT ---
            // Appel de notre nouveau seeder maître pour les données des véhicules
            VehicleDataSeeder::class,
            // ---------------------

            // Ajoutez d'autres seeders maîtres ici au fur et à mesure des modules
	    DriverStatusSeeder::class, // <--- AJOUTEZ CETTE LIGNE    
	
   	    	
	
	
	]);

        // Création de l'utilisateur Admin par défaut et assignation de son rôle.
        // Cette partie reste la même et doit être exécutée après la création des rôles.
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@zenfleet.com'], // Critère de recherche pour éviter les doublons
            [ // Valeurs à utiliser si l'utilisateur est créé pour la première fois
                'first_name' => 'Admin',
                'last_name' => 'ZenFleet',
                'phone' => '0000000000',
                'password' => bcrypt('password'), // Rappel : à changer pour un mot de passe sécurisé
                'email_verified_at' => now(),
            ]
        );

        // S'assure que l'utilisateur Admin a bien le rôle 'Admin'
        if ($adminUser->wasRecentlyCreated || !$adminUser->hasRole('Admin')) {
             $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
             if ($adminRole) {
                 $adminUser->assignRole($adminRole);
                 $this->command->info('Admin user created/updated and assigned Admin role.');
             } else {
                 $this->command->error('Admin role not found. Could not assign role to admin user.');
             }
        } else {
            $this->command->info('Admin user already exists and has Admin role.');
        }
    }
}
