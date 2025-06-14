<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ValidationLevel; // Assurez-vous que le modèle existe et est importé

class ValidationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['level_number' => 1, 'name' => 'Demandeur', 'description' => 'Niveau initial de la demande.'],
            ['level_number' => 2, 'name' => 'Validation Intermédiaire', 'description' => 'Premier niveau d\'approbation.'], // Exemple de niveau intermédiaire
            ['level_number' => 3, 'name' => 'Validation Finale', 'description' => 'Approbation finale.'],
            // Ajoutez d'autres niveaux selon votre document ou vos besoins
        ];

        foreach ($levels as $level) {
            ValidationLevel::firstOrCreate(
                ['level_number' => $level['level_number']], // Critère de recherche
                $level // Valeurs à insérer/mettre à jour
            );
        }

        $this->command->info('Validation levels created successfully.');
    }
}
