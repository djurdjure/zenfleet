<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * 🌱 Seeder de démonstration pour les affectations
 *
 * Génère des données de test enterprise-grade:
 * - Affectations réalistes sur plusieurs organisations
 * - Scénarios de chevauchement contrôlés
 * - Données historiques et futures
 * - Statuts variés pour tests complets
 */
class AssignmentDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes d'exclusion pour le seeding
        DB::statement('ALTER TABLE assignments DISABLE TRIGGER ALL;');

        try {
            $this->seedAssignmentsByOrganization();
        } finally {
            // Réactiver les contraintes
            DB::statement('ALTER TABLE assignments ENABLE TRIGGER ALL;');
        }
    }

    private function seedAssignmentsByOrganization(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            $this->command->info("Génération d'affectations pour {$organization->name}...");

            $vehicles = Vehicle::where('organization_id', $organization->id)
                ->where('status', 'active')
                ->get();

            $drivers = Driver::where('organization_id', $organization->id)
                ->where('status', 'active')
                ->get();

            $users = User::where('organization_id', $organization->id)->get();

            if ($vehicles->isEmpty() || $drivers->isEmpty() || $users->isEmpty()) {
                $this->command->warn("Pas assez de ressources pour {$organization->name}, skipping...");
                continue;
            }

            // Générer des affectations pour les 30 derniers jours et 30 prochains jours
            $this->generateHistoricalAssignments($organization, $vehicles, $drivers, $users);
            $this->generateCurrentAssignments($organization, $vehicles, $drivers, $users);
            $this->generateFutureAssignments($organization, $vehicles, $drivers, $users);

            $this->command->info("✅ Affectations générées pour {$organization->name}");
        }
    }

    /**
     * Générer des affectations historiques (30 derniers jours)
     */
    private function generateHistoricalAssignments(
        Organization $organization,
        $vehicles,
        $drivers,
        $users
    ): void {
        $startDate = now()->subDays(30);
        $endDate = now()->subDay();

        for ($i = 0; $i < 50; $i++) {
            $vehicle = $vehicles->random();
            $driver = $drivers->random();
            $creator = $users->random();

            $start = $startDate->copy()->addDays(rand(0, 29))
                ->setTime(rand(6, 20), rand(0, 3) * 15, 0);

            // 90% des affectations historiques sont terminées
            $isCompleted = rand(1, 100) <= 90;
            $duration = rand(2, 48); // 2h à 48h

            $end = $isCompleted ?
                $start->copy()->addHours($duration) :
                null;

            // Éviter les chevauchements basiques
            if ($this->hasBasicOverlap($organization->id, $vehicle->id, $driver->id, $start, $end)) {
                continue;
            }

            Assignment::create([
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'reason' => $this->getRandomReason(),
                'notes' => rand(1, 100) <= 30 ? $this->getRandomNotes() : null,
                'created_by_user_id' => $creator->id,
                'created_at' => $start->copy()->subHours(rand(1, 24)),
                'updated_at' => $start->copy()->subHours(rand(1, 24))
            ]);
        }
    }

    /**
     * Générer des affectations en cours
     */
    private function generateCurrentAssignments(
        Organization $organization,
        $vehicles,
        $drivers,
        $users
    ): void {
        // 15-20 affectations en cours
        for ($i = 0; $i < rand(15, 20); $i++) {
            $vehicle = $vehicles->random();
            $driver = $drivers->random();
            $creator = $users->random();

            $start = now()->subDays(rand(0, 7))
                ->setTime(rand(6, 18), rand(0, 3) * 15, 0);

            // 60% sont en cours (sans fin), 40% ont une fin programmée
            $hasEnd = rand(1, 100) <= 40;
            $end = $hasEnd ?
                now()->addDays(rand(1, 7))->setTime(rand(8, 20), rand(0, 3) * 15, 0) :
                null;

            if ($this->hasBasicOverlap($organization->id, $vehicle->id, $driver->id, $start, $end)) {
                continue;
            }

            Assignment::create([
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'reason' => $this->getRandomReason(),
                'notes' => rand(1, 100) <= 40 ? $this->getRandomNotes() : null,
                'created_by_user_id' => $creator->id
            ]);
        }
    }

    /**
     * Générer des affectations futures (30 prochains jours)
     */
    private function generateFutureAssignments(
        Organization $organization,
        $vehicles,
        $drivers,
        $users
    ): void {
        $startDate = now()->addDay();
        $endDate = now()->addDays(30);

        for ($i = 0; $i < 40; $i++) {
            $vehicle = $vehicles->random();
            $driver = $drivers->random();
            $creator = $users->random();

            $start = $startDate->copy()->addDays(rand(0, 29))
                ->setTime(rand(6, 20), rand(0, 3) * 15, 0);

            // 80% ont une durée définie
            $hasDuration = rand(1, 100) <= 80;
            $duration = rand(2, 24); // 2h à 24h

            $end = $hasDuration ?
                $start->copy()->addHours($duration) :
                null;

            if ($this->hasBasicOverlap($organization->id, $vehicle->id, $driver->id, $start, $end)) {
                continue;
            }

            Assignment::create([
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
                'start_datetime' => $start,
                'end_datetime' => $end,
                'reason' => $this->getRandomReason(),
                'notes' => rand(1, 100) <= 25 ? $this->getRandomNotes() : null,
                'created_by_user_id' => $creator->id
            ]);
        }

        // Créer quelques affectations avec conflits intentionnels pour tester la gestion des erreurs
        $this->createConflictScenarios($organization, $vehicles, $drivers, $users);
    }

    /**
     * Créer des scénarios de conflit pour les tests
     */
    private function createConflictScenarios(
        Organization $organization,
        $vehicles,
        $drivers,
        $users
    ): void {
        // Note: Ces affectations ne seront pas créées si les contraintes GIST sont actives
        // Elles servent à tester la gestion des conflits côté application

        $vehicle = $vehicles->first();
        $driver = $drivers->first();
        $creator = $users->first();

        $baseStart = now()->addDays(5)->setTime(9, 0, 0);

        // Tentative de chevauchement véhicule (sera rejetée par les contraintes)
        try {
            Assignment::create([
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $drivers->skip(1)->first()->id,
                'start_datetime' => $baseStart,
                'end_datetime' => $baseStart->copy()->addHours(4),
                'reason' => 'Test conflit véhicule',
                'created_by_user_id' => $creator->id
            ]);

            // Si la première passe, essayer un chevauchement
            Assignment::create([
                'organization_id' => $organization->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $drivers->skip(2)->first()->id,
                'start_datetime' => $baseStart->copy()->addHours(2),
                'end_datetime' => $baseStart->copy()->addHours(6),
                'reason' => 'Test conflit véhicule - chevauchement',
                'created_by_user_id' => $creator->id
            ]);
        } catch (\Exception $e) {
            $this->command->comment("Conflit véhicule détecté (attendu): " . $e->getMessage());
        }
    }

    /**
     * Vérification basique de chevauchement (sans les contraintes PostgreSQL)
     */
    private function hasBasicOverlap(
        int $organizationId,
        int $vehicleId,
        int $driverId,
        Carbon $start,
        ?Carbon $end
    ): bool {
        $endEffective = $end ?? Carbon::create(2099, 12, 31);

        // Vérifier les chevauchements véhicule
        $vehicleOverlap = Assignment::where('organization_id', $organizationId)
            ->where('vehicle_id', $vehicleId)
            ->where(function ($query) use ($start, $endEffective) {
                $query->where(function ($q) use ($start, $endEffective) {
                    // Affectations avec fin définie
                    $q->whereNotNull('end_datetime')
                      ->where('start_datetime', '<', $endEffective)
                      ->where('end_datetime', '>', $start);
                })->orWhere(function ($q) use ($start) {
                    // Affectations sans fin
                    $q->whereNull('end_datetime')
                      ->where('start_datetime', '<', $start->copy()->addDays(30));
                });
            })
            ->exists();

        if ($vehicleOverlap) return true;

        // Vérifier les chevauchements chauffeur
        $driverOverlap = Assignment::where('organization_id', $organizationId)
            ->where('driver_id', $driverId)
            ->where(function ($query) use ($start, $endEffective) {
                $query->where(function ($q) use ($start, $endEffective) {
                    $q->whereNotNull('end_datetime')
                      ->where('start_datetime', '<', $endEffective)
                      ->where('end_datetime', '>', $start);
                })->orWhere(function ($q) use ($start) {
                    $q->whereNull('end_datetime')
                      ->where('start_datetime', '<', $start->copy()->addDays(30));
                });
            })
            ->exists();

        return $driverOverlap;
    }

    /**
     * Motifs d'affectation réalistes
     */
    private function getRandomReason(): string
    {
        $reasons = [
            'Mission commerciale',
            'Formation conducteur',
            'Maintenance préventive',
            'Transport équipe',
            'Livraison urgente',
            'Rendez-vous client',
            'Déplacement siège social',
            'Mission longue durée',
            'Remplacement véhicule',
            'Test nouveau conducteur',
            'Formation sécurité',
            'Audit qualité',
            'Intervention technique',
            'Transport matériel',
            'Réunion partenaires',
            'Salon professionnel',
            'Inspection véhicule',
            'Formation éco-conduite',
            'Mission de nuit',
            'Urgence opérationnelle'
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Notes complémentaires aléatoires
     */
    private function getRandomNotes(): string
    {
        $notes = [
            'Prévoir plein d\'essence avant départ',
            'Vérifier pression pneus',
            'Retour prévu avant 18h',
            'Contact client: 06.12.34.56.78',
            'Attention: véhicule en rodage',
            'Kilométrage à noter au retour',
            'Eviter autoroutes si possible',
            'Matériel fragile à transporter',
            'Mission confidentielle',
            'Prévoir temps supplémentaire',
            'Vérifier état véhicule retour',
            'Facture parking à conserver',
            'Itinéraire GPS fourni',
            'Contact urgence: 07.89.01.23.45',
            'Prévoir badge parking',
            'Attention: zone urbaine',
            'Mission formation - patience requise',
            'Vérifier papiers véhicule',
            'Prévoir kit première urgence',
            'Retour différé possible'
        ];

        return $notes[array_rand($notes)];
    }
}