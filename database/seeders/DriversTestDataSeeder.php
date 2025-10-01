<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;

class DriversTestDataSeeder extends Seeder
{
    /**
     * Create comprehensive test data for drivers page
     */
    public function run(): void
    {
        echo "🚛 Création des données de test pour la page chauffeurs...\n";

        // 1. S'assurer que les statuts de chauffeurs existent
        $this->ensureDriverStatusesExist();

        // 2. Créer des chauffeurs de test pour chaque organisation
        $this->createTestDrivers();

        echo "✅ Données de test pour chauffeurs créées avec succès!\n";
        $this->displaySummary();
    }

    private function ensureDriverStatusesExist()
    {
        echo "   📋 Vérification des statuts de chauffeurs...\n";

        // Vérifier si la table existe
        if (!\Schema::hasTable('driver_statuses')) {
            echo "   ⚠️ Table driver_statuses non trouvée - elle sera créée par la migration\n";
            return;
        }

        // Vérifier si des statuts existent
        $statusCount = DriverStatus::count();
        if ($statusCount === 0) {
            echo "   ⚠️ Aucun statut de chauffeur trouvé - ils seront créés par la migration\n";
            return;
        }

        echo "   ✅ {$statusCount} statuts de chauffeurs trouvés\n";
    }

    private function createTestDrivers()
    {
        echo "   🚛 Création de chauffeurs de test...\n";

        $organizations = Organization::all();
        $activeStatus = $this->getActiveStatus();
        $onLeaveStatus = $this->getOnLeaveStatus();
        $suspendedStatus = $this->getSuspendedStatus();

        $driverCount = 0;

        foreach ($organizations as $org) {
            // Créer 3-8 chauffeurs par organisation
            $numDrivers = rand(3, 8);

            for ($i = 1; $i <= $numDrivers; $i++) {
                $driver = $this->createDriverForOrganization($org, $i, $activeStatus, $onLeaveStatus, $suspendedStatus);

                if ($driver) {
                    $driverCount++;
                }
            }

            echo "      ✅ {$org->name}: {$numDrivers} chauffeurs créés\n";
        }

        echo "   📊 Total: {$driverCount} chauffeurs créés\n";
    }

    private function createDriverForOrganization($org, $index, $activeStatus, $onLeaveStatus, $suspendedStatus)
    {
        $firstNames = ['Ahmed', 'Mohamed', 'Ali', 'Youcef', 'Karim', 'Fatima', 'Aicha', 'Samira', 'Rachid', 'Nabil'];
        $lastNames = ['Benali', 'Benaissa', 'Meradji', 'Bencherif', 'Khelifi', 'Boudiaf', 'Hamidi', 'Zeroual'];

        // Déterminer le statut (80% actifs, 15% en congé, 5% suspendus)
        $rand = rand(1, 100);
        if ($rand <= 80) {
            $status = $activeStatus;
        } elseif ($rand <= 95) {
            $status = $onLeaveStatus;
        } else {
            $status = $suspendedStatus;
        }

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $licenseNumber = "DL-{$org->wilaya}-" . str_pad($org->id . $index, 8, '0', STR_PAD_LEFT);

        try {
            return Driver::firstOrCreate(
                ['driver_license_number' => $licenseNumber],
                [
                    'organization_id' => $org->id,
                    'employee_number' => "EMP-{$org->id}-" . str_pad($index, 4, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'personal_phone' => '+213-555-' . rand(100000, 999999),
                    'email' => strtolower($firstName . '.' . $lastName . '.org' . $org->id . '@test.dz'),
                    'driver_license_number' => $licenseNumber,
                    'driver_license_expiry' => now()->addYears(rand(1, 5)),
                    'status_id' => $status ? $status->id : null,
                    'status' => $status ? $status->slug : 'active',
                    'birth_date' => now()->subYears(rand(25, 55)),
                    'recruitment_date' => now()->subDays(rand(30, 1095)), // Entre 1 mois et 3 ans
                    'blood_type' => collect(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->random(),
                    'address' => "Adresse {$firstName} {$lastName}, {$org->city}",
                    'emergency_contact_name' => 'Contact Urgence ' . $lastName,
                    'emergency_contact_phone' => '+213-555-' . rand(100000, 999999),
                    'created_at' => now()->subDays(rand(1, 90)),
                    'updated_at' => now()->subDays(rand(0, 30)),
                ]
            );
        } catch (\Exception $e) {
            echo "   ⚠️ Erreur lors de la création du chauffeur {$firstName} {$lastName}: " . $e->getMessage() . "\n";
            return null;
        }
    }

    private function getActiveStatus()
    {
        try {
            return DriverStatus::where('slug', 'active')->first() ?:
                   DriverStatus::where('name', 'ILIKE', '%actif%')->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getOnLeaveStatus()
    {
        try {
            return DriverStatus::where('slug', 'on-leave')->first() ?:
                   DriverStatus::where('name', 'ILIKE', '%congé%')->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getSuspendedStatus()
    {
        try {
            return DriverStatus::where('slug', 'suspended')->first() ?:
                   DriverStatus::where('name', 'ILIKE', '%suspendu%')->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function displaySummary()
    {
        echo "\n📊 Résumé des données chauffeurs:\n";

        try {
            $totalDrivers = Driver::count();
            $driversWithStatus = Driver::whereNotNull('status_id')->count();
            $driversPerOrg = Driver::selectRaw('organization_id, COUNT(*) as count')
                ->groupBy('organization_id')
                ->get();

            echo "   - Total chauffeurs: {$totalDrivers}\n";
            echo "   - Chauffeurs avec statut: {$driversWithStatus}\n";

            if (\Schema::hasTable('driver_statuses')) {
                $statusStats = DriverStatus::withCount('drivers')->get();
                echo "   - Répartition par statut:\n";
                foreach ($statusStats as $status) {
                    echo "     * {$status->name}: {$status->drivers_count}\n";
                }
            }

            echo "   - Répartition par organisation:\n";
            foreach ($driversPerOrg as $orgStat) {
                $orgName = Organization::find($orgStat->organization_id)->name ?? "Org ID {$orgStat->organization_id}";
                echo "     * {$orgName}: {$orgStat->count}\n";
            }

        } catch (\Exception $e) {
            echo "   ⚠️ Erreur lors du calcul des statistiques: " . $e->getMessage() . "\n";
        }

        echo "\n🎯 La page chauffeurs est maintenant prête pour les tests!\n";
        echo "   💡 N'oubliez pas d'exécuter la migration pour créer la table driver_statuses\n";
    }
}