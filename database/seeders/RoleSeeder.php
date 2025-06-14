<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Importez le modèle Role

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des rôles définis dans le document
        // La méthode firstOrCreate s'assure que le rôle n'est créé que s'il n'existe pas déjà.
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Gestionnaire Flotte', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Chauffeur', 'guard_name' => 'web']);
        // 'guard_name' => 'web' est le guard par défaut pour les applications web Laravel.
        // Vous pouvez le personnaliser dans config/auth.php si besoin.

        $this->command->info('Roles created successfully.');
    }
}
