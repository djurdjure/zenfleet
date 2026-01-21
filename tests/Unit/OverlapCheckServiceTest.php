<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OverlapCheckService;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 🧪 Tests unitaires du service de vérification de chevauchement
 *
 * Couvre tous les scénarios enterprise-grade:
 * - Détection de chevauchements véhicule/chauffeur
 * - Gestion des durées indéterminées (NULL)
 * - Frontières exactes (autorisées)
 * - Suggestions de créneaux libres
 * - Performance sur gros volumes
 */
class OverlapCheckServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private OverlapCheckService $service;
    private Organization $organization;
    private Vehicle $vehicle;
    private Driver $driver;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(OverlapCheckService::class);

        // Créer les entités de test
        $this->organization = Organization::factory()->create();
        $this->vehicle = Vehicle::factory()->create(['organization_id' => $this->organization->id]);
        $this->driver = Driver::factory()->create(['organization_id' => $this->organization->id]);
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_detects_no_conflicts_for_non_overlapping_assignments()
    {
        // Arrangement: Créer une affectation existante
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Vérifier une nouvelle affectation non chevauchante
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-21 08:00:00'),
            end: Carbon::parse('2025-01-21 17:00:00')
        );

        // Assertion
        $this->assertFalse($result['has_conflicts']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_detects_vehicle_overlap_conflicts()
    {
        $otherDriver = Driver::factory()->create(['organization_id' => $this->organization->id]);

        // Arrangement: Affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $otherDriver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Nouvelle affectation chevauchante sur le même véhicule
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 14:00:00'),
            end: Carbon::parse('2025-01-20 20:00:00')
        );

        // Assertion
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertEquals($existingAssignment->id, $result['conflicts'][0]['id']);
    }

    /** @test */
    public function it_detects_driver_overlap_conflicts()
    {
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $this->organization->id]);

        // Arrangement: Affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Nouvelle affectation chevauchante avec le même chauffeur
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 14:00:00'),
            end: Carbon::parse('2025-01-20 20:00:00')
        );

        // Assertion
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertEquals($existingAssignment->id, $result['conflicts'][0]['id']);
    }

    /** @test */
    public function it_allows_exact_boundary_transitions()
    {
        // Arrangement: Affectation se terminant à 17:00
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Nouvelle affectation commençant exactement à 17:00
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 17:00:00'),
            end: Carbon::parse('2025-01-20 22:00:00')
        );

        // Assertion: Pas de conflit car frontière exacte
        $this->assertFalse($result['has_conflicts']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_handles_indefinite_duration_assignments()
    {
        // Arrangement: Affectation sans fin (durée indéterminée)
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => null, // Durée indéterminée
        ]);

        // Action: Nouvelle affectation tentant de commencer plus tard
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-25 08:00:00'),
            end: Carbon::parse('2025-01-25 17:00:00')
        );

        // Assertion: Conflit détecté car l'affectation indéterminée est considérée comme active
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(1, $result['conflicts']);
    }

    /** @test */
    public function it_excludes_assignment_being_edited()
    {
        // Arrangement: Affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Vérifier la même affectation (simulation d'édition)
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 08:00:00'),
            end: Carbon::parse('2025-01-20 17:00:00'),
            excludeId: $existingAssignment->id
        );

        // Assertion: Pas de conflit car l'affectation elle-même est exclue
        $this->assertFalse($result['has_conflicts']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_respects_organization_isolation()
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        // Arrangement: Affectation dans une autre organisation
        Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        // Action: Vérifier affectation dans notre organisation
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 08:00:00'),
            end: Carbon::parse('2025-01-20 17:00:00')
        );

        // Assertion: Pas de conflit car organisations différentes
        $this->assertFalse($result['has_conflicts']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_finds_next_available_slot()
    {
        // Arrangement: Créer quelques affectations
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-21 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-21 17:00:00'),
        ]);

        // Action: Chercher le prochain créneau libre de 8h
        $result = $this->service->findNextAvailableSlot(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            durationHours: 8
        );

        // Assertion
        $this->assertNotNull($result);
        $this->assertArrayHasKey('start', $result);
        $this->assertArrayHasKey('end', $result);

        // Le créneau proposé doit être après la dernière affectation
        $proposedStart = Carbon::parse($result['start']);
        $this->assertTrue($proposedStart->gte(Carbon::parse('2025-01-21 17:00:00')));
    }

    /** @test */
    public function it_validates_complete_assignment_with_business_rules()
    {
        // Action: Valider une affectation avec date de début dans le passé
        $result = $this->service->validateAssignment(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-01 08:00:00'), // Date passée
            end: Carbon::parse('2025-01-01 17:00:00')
        );

        // Assertion: Les affectations rétroactives sont autorisées
        $this->assertTrue($result['is_valid']);
        $this->assertEmpty($result['errors']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_validates_assignment_with_end_before_start()
    {
        // Action: Valider affectation avec fin avant début
        $result = $this->service->validateAssignment(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 17:00:00'),
            end: Carbon::parse('2025-01-20 08:00:00') // Fin avant début
        );

        // Assertion: Erreur de validation
        $this->assertFalse($result['is_valid']);
        $this->assertNotEmpty($result['errors']);
        $this->assertStringContainsString('antérieure', $result['errors'][0]);
    }

    /** @test */
    public function it_generates_conflict_suggestions()
    {
        // Arrangement: Créer des affectations pour avoir des créneaux libres
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(8, 0, 0),
            'end_datetime' => now()->addDay()->setTime(12, 0, 0),
        ]);

        // Action: Tester une affectation en conflit
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: now()->addDay()->setTime(10, 0, 0),
            end: now()->addDay()->setTime(14, 0, 0)
        );

        // Assertion: Des suggestions doivent être générées
        $this->assertTrue($result['has_conflicts']);
        $this->assertNotEmpty($result['suggestions']);
        $this->assertArrayHasKey('start', $result['suggestions'][0]);
        $this->assertArrayHasKey('description', $result['suggestions'][0]);
    }

    /** @test */
    public function it_handles_multiple_conflicts()
    {
        $otherDriver = Driver::factory()->create(['organization_id' => $this->organization->id]);
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $this->organization->id]);

        // Arrangement: Créer des conflits véhicule ET chauffeur
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $otherDriver->id,
            'start_datetime' => Carbon::parse('2025-01-20 08:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 17:00:00'),
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 10:00:00'),
            'end_datetime' => Carbon::parse('2025-01-20 15:00:00'),
        ]);

        // Action: Tester affectation conflictuelle sur les deux ressources
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-20 12:00:00'),
            end: Carbon::parse('2025-01-20 18:00:00')
        );

        // Assertion: Deux conflits détectés
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(2, $result['conflicts']);
    }

    /** @test */
    public function it_performs_well_with_large_dataset()
    {
        // Arrangement: Créer beaucoup d'affectations
        $vehicles = Vehicle::factory(10)->create(['organization_id' => $this->organization->id]);
        $drivers = Driver::factory(10)->create(['organization_id' => $this->organization->id]);

        // Créer 1000 affectations
        for ($i = 0; $i < 1000; $i++) {
            $start = now()->addDays(rand(1, 365))->setTime(rand(6, 20), 0, 0);
            $end = (clone $start)->addHours(rand(1, 8));

            Assignment::factory()->create([
                'organization_id' => $this->organization->id,
                'vehicle_id' => $vehicles->random()->id,
                'driver_id' => $drivers->random()->id,
                'start_datetime' => $start,
                'end_datetime' => $end,
            ]);
        }

        // Action: Mesurer le temps d'exécution
        $startTime = microtime(true);

        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: now()->addMonth(),
            end: now()->addMonth()->addHours(8)
        );

        $executionTime = microtime(true) - $startTime;

        // Assertion: Performance acceptable (< 1 seconde)
        $this->assertLessThan(1.0, $executionTime, 'Service trop lent avec 1000 affectations');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('has_conflicts', $result);
    }

    /** @test */
    public function it_handles_edge_case_midnight_transitions()
    {
        // Arrangement: Affectation traversant minuit
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 22:00:00'),
            'end_datetime' => Carbon::parse('2025-01-21 06:00:00'),
        ]);

        // Action: Tester chevauchement sur la période nocturne
        $result = $this->service->checkOverlap(
            vehicleId: $this->vehicle->id,
            driverId: $this->driver->id,
            start: Carbon::parse('2025-01-21 02:00:00'),
            end: Carbon::parse('2025-01-21 10:00:00')
        );

        // Assertion: Conflit détecté
        $this->assertTrue($result['has_conflicts']);
        $this->assertCount(1, $result['conflicts']);
    }
}
