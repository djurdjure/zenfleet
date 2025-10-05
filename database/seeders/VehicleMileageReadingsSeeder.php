<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleMileageReading;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

/**
 * VehicleMileageReadingsSeeder - Données de test pour les relevés kilométriques
 *
 * Crée des relevés réalistes pour démonstration:
 * - Relevés manuels et automatiques
 * - Progression cohérente du kilométrage
 * - Historique sur plusieurs mois
 *
 * @version 1.0-Enterprise
 */
class VehicleMileageReadingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📊 Création des relevés kilométriques de test...');

        // Récupérer quelques véhicules
        $vehicles = Vehicle::with('organization')->limit(5)->get();

        if ($vehicles->isEmpty()) {
            $this->command->warn('⚠️  Aucun véhicule trouvé. Créez d\'abord des véhicules.');
            return;
        }

        $totalCreated = 0;

        foreach ($vehicles as $vehicle) {
            $this->command->info("  🚗 Véhicule: {$vehicle->registration_number}");

            // Récupérer un utilisateur de l'organisation pour les relevés manuels
            $user = User::where('organization_id', $vehicle->organization_id)->first();

            // Créer un historique de relevés sur 3 mois
            $startDate = Carbon::now()->subMonths(3);
            $currentMileage = rand(50000, 100000); // Kilométrage de départ

            // Relevé initial (automatique)
            VehicleMileageReading::create([
                'organization_id' => $vehicle->organization_id,
                'vehicle_id' => $vehicle->id,
                'recorded_at' => $startDate,
                'mileage' => $currentMileage,
                'recorded_by_id' => null,
                'recording_method' => VehicleMileageReading::METHOD_AUTOMATIC,
                'notes' => 'Relevé initial du système',
            ]);
            $totalCreated++;

            // Créer des relevés hebdomadaires
            $currentDate = clone $startDate;
            $endDate = Carbon::now();

            while ($currentDate->lte($endDate)) {
                $currentDate->addWeek();

                // Incrément réaliste: 200-500 km par semaine
                $weeklyMileage = rand(200, 500);
                $currentMileage += $weeklyMileage;

                // Alterner entre relevés manuels et automatiques
                $isManual = rand(0, 1) === 1;

                $reading = VehicleMileageReading::create([
                    'organization_id' => $vehicle->organization_id,
                    'vehicle_id' => $vehicle->id,
                    'recorded_at' => clone $currentDate,
                    'mileage' => $currentMileage,
                    'recorded_by_id' => $isManual && $user ? $user->id : null,
                    'recording_method' => $isManual
                        ? VehicleMileageReading::METHOD_MANUAL
                        : VehicleMileageReading::METHOD_AUTOMATIC,
                    'notes' => $this->getRandomNote($isManual, $weeklyMileage),
                ]);
                $totalCreated++;

                // 20% de chance d'avoir un relevé intermédiaire en milieu de semaine
                if (rand(1, 5) === 1) {
                    $midWeekDate = (clone $currentDate)->subDays(3);
                    $midWeekMileage = $currentMileage - rand(50, 200);

                    VehicleMileageReading::create([
                        'organization_id' => $vehicle->organization_id,
                        'vehicle_id' => $vehicle->id,
                        'recorded_at' => $midWeekDate,
                        'mileage' => $midWeekMileage,
                        'recorded_by_id' => $user?->id,
                        'recording_method' => VehicleMileageReading::METHOD_MANUAL,
                        'notes' => 'Relevé intermédiaire',
                    ]);
                    $totalCreated++;
                }
            }

            $readingsCount = VehicleMileageReading::where('vehicle_id', $vehicle->id)->count();
            $this->command->info("    ✓ {$readingsCount} relevés créés");
        }

        $this->command->info('');
        $this->command->info("✅ Total: {$totalCreated} relevés kilométriques créés");

        // Afficher quelques statistiques
        $this->displayStatistics();
    }

    /**
     * Générer une note aléatoire réaliste
     */
    private function getRandomNote(bool $isManual, int $weeklyMileage): ?string
    {
        if (!$isManual) {
            return null; // Les relevés automatiques n'ont généralement pas de notes
        }

        $notes = [
            null, // 40% sans note
            null,
            null,
            null,
            'Relevé après maintenance',
            'Contrôle hebdomadaire',
            'Relevé avant départ mission',
            'Vérification kilométrage',
            'Relevé mensuel',
            "Semaine intensive: {$weeklyMileage} km parcourus",
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Afficher des statistiques sur les relevés créés
     */
    private function displayStatistics(): void
    {
        $this->command->info('📈 Statistiques:');
        $this->command->info('');

        $totalReadings = VehicleMileageReading::count();
        $manualReadings = VehicleMileageReading::manualOnly()->count();
        $automaticReadings = VehicleMileageReading::automaticOnly()->count();

        $this->command->info("  Total relevés: {$totalReadings}");
        $this->command->info("  Manuels: {$manualReadings}");
        $this->command->info("  Automatiques: {$automaticReadings}");

        // Statistiques par véhicule
        $this->command->info('');
        $this->command->info('  Par véhicule:');

        $vehiclesWithReadings = Vehicle::whereHas('mileageReadings')->get();

        foreach ($vehiclesWithReadings as $vehicle) {
            $count = $vehicle->mileageReadings()->count();
            $latest = $vehicle->mileageReadings()->latest('recorded_at')->first();

            if ($latest) {
                $this->command->info("    - {$vehicle->registration_number}: {$count} relevés, dernier: {$latest->formatted_mileage}");
            }
        }
    }
}
