<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Berline',
            'SUV',
            'Citadine',           // Ajouté pour Clio, etc.
            'Break',              // Véhicules familiaux
            'Coupé',              // Véhicules sportifs
            'Cabriolet',          // Véhicules décapotables
            'Monospace',          // Véhicules familiaux
            'Crossover',          // Entre SUV et berline
            'Pick-up',            // Véhicules utilitaires
            'Utilitaire léger',   // Fourgons, etc.
            'Camionnette',        // Utilitaires moyens
            'Camion',             // Poids lourds
            'Bus',                // Transport en commun
            'Autocar',            // Transport longue distance
            'Minibus',            // Transport de groupes
            'Moto',               // Deux roues
            'Scooter',            // Deux roues urbains
            'Quad',               // Tout-terrain
            'Remorque',           // Remorques
            'Semi-remorque'       // Transport lourd
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(['name' => $type]);
        }
    }
}
