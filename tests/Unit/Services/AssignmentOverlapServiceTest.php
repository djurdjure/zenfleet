<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AssignmentOverlapService;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests unitaires pour le service de détection de chevauchement d'affectations
 *
 * Couvre tous les cas d'usage critiques:
 * - Détection de chevauchements temporels
 * - Validation de nouvelles affectations
 * - Suggestions de créneaux libres
 * - Gestion des affectations indéterminées
 *
 * @group assignments
 * @group overlap-service
 */
class AssignmentOverlapServiceTest extends TestCase
{
    use RefreshDatabase;

    private AssignmentOverlapService $service;
    private Organization $organization;
    private User $user;
    private Vehicle $vehicle;
    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AssignmentOverlapService::class);

        // Créer une organisation de test
        $this->organization = Organization::factory()->create();

        // Créer un utilisateur de test
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        // Créer un véhicule et un chauffeur de test
        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_detects_no_overlap_when_no_existing_assignments()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertFalse($result['hasOverlap']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_detects_vehicle_overlap_with_exact_same_period()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        // Créer une affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertEquals('vehicle', $result['conflicts'][0]['type']);
        $this->assertEquals($existingAssignment->id, $result['conflicts'][0]['assignment_id']);
    }

    /** @test */
    public function it_detects_driver_overlap_with_exact_same_period()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        // Créer une affectation existante pour le même chauffeur
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => Vehicle::factory()->create(['organization_id' => $this->organization->id])->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertCount(1, $result['conflicts']);
        $this->assertEquals('driver', $result['conflicts'][0]['type']);
        $this->assertEquals($existingAssignment->id, $result['conflicts'][0]['assignment_id']);
    }

    /** @test */
    public function it_detects_partial_overlap_at_start()
    {
        $existingStart = now()->addHours(2);
        $existingEnd = now()->addHours(6);

        // Affectation existante
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $existingStart,
            'end_datetime' => $existingEnd,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Nouvelle affectation qui chevauche au début
        $newStart = now()->addHours(1);
        $newEnd = now()->addHours(4);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $newStart,
            $newEnd
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertCount(1, $result['conflicts']);
    }

    /** @test */
    public function it_detects_partial_overlap_at_end()
    {
        $existingStart = now()->addHours(2);
        $existingEnd = now()->addHours(6);

        // Affectation existante
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $existingStart,
            'end_datetime' => $existingEnd,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Nouvelle affectation qui chevauche à la fin
        $newStart = now()->addHours(4);
        $newEnd = now()->addHours(8);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $newStart,
            $newEnd
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertCount(1, $result['conflicts']);
    }

    /** @test */
    public function it_detects_overlap_with_indefinite_assignment()
    {
        $existingStart = now()->addHours(2);

        // Affectation existante indéterminée (end_datetime = null)
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $existingStart,
            'end_datetime' => null,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Nouvelle affectation après le début de l'indéterminée
        $newStart = now()->addHours(4);
        $newEnd = now()->addHours(8);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $newStart,
            $newEnd
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertCount(1, $result['conflicts']);
    }

    /** @test */
    public function it_allows_adjacent_assignments_without_overlap()
    {
        $existingStart = now()->addHours(2);
        $existingEnd = now()->addHours(6);

        // Affectation existante
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $existingStart,
            'end_datetime' => $existingEnd,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Nouvelle affectation qui commence exactement quand l'autre finit
        $newStart = $existingEnd;
        $newEnd = now()->addHours(10);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $newStart,
            $newEnd
        );

        $this->assertFalse($result['hasOverlap']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_excludes_cancelled_assignments_from_overlap_check()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        // Affectation annulée
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => Assignment::STATUS_CANCELLED
        ]);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertFalse($result['hasOverlap']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_excludes_specific_assignment_from_overlap_check()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        // Affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Vérification en excluant l'affectation existante (cas d'édition)
        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end,
            $existingAssignment->id
        );

        $this->assertFalse($result['hasOverlap']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_validates_assignment_and_returns_comprehensive_result()
    {
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        $result = $this->service->validateAssignment(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertArrayHasKey('isValid', $result);
        $this->assertArrayHasKey('validationErrors', $result);
        $this->assertArrayHasKey('overlapErrors', $result);
        $this->assertArrayHasKey('conflicts', $result);
        $this->assertArrayHasKey('suggestedSlots', $result);

        $this->assertTrue($result['isValid']);
        $this->assertEmpty($result['validationErrors']);
        $this->assertEmpty($result['overlapErrors']);
    }

    /** @test */
    public function it_finds_next_available_slot()
    {
        // Créer des affectations existantes
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addHours(2),
            'end_datetime' => now()->addHours(6),
            'status' => Assignment::STATUS_ACTIVE
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addHours(8),
            'end_datetime' => now()->addHours(12),
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $nextSlot = $this->service->findNextAvailableSlot(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            now(),
            4 // 4 heures de durée
        );

        $this->assertNotNull($nextSlot);
        $this->assertArrayHasKey('start', $nextSlot);
        $this->assertArrayHasKey('end', $nextSlot);
        $this->assertArrayHasKey('start_formatted', $nextSlot);
        $this->assertArrayHasKey('end_formatted', $nextSlot);

        // Le slot devrait être après 12h (fin de la dernière affectation)
        $expectedStart = now()->addHours(12);
        $this->assertTrue(Carbon::parse($nextSlot['start'])->gte($expectedStart));
    }

    /** @test */
    public function it_suggests_alternative_slots_when_overlap_detected()
    {
        // Occuper une plage horaire
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addHours(4),
            'end_datetime' => now()->addHours(8),
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Tenter de créer une affectation en conflit
        $start = now()->addHours(2);
        $end = now()->addHours(6);

        $result = $this->service->validateAssignment(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertFalse($result['isValid']);
        $this->assertNotEmpty($result['suggestedSlots']);

        $suggestedSlot = $result['suggestedSlots'][0];
        $this->assertArrayHasKey('start', $suggestedSlot);
        $this->assertArrayHasKey('end', $suggestedSlot);
        $this->assertArrayHasKey('description', $suggestedSlot);
    }

    /** @test */
    public function it_isolates_checks_by_organization()
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $otherOrganization->id
        ]);
        $otherDriver = Driver::factory()->create([
            'organization_id' => $otherOrganization->id
        ]);

        $start = now()->addHours(2);
        $end = now()->addHours(6);

        // Créer une affectation dans l'autre organisation
        Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Vérifier qu'il n'y a pas de conflit pour notre organisation
        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $start,
            $end
        );

        $this->assertFalse($result['hasOverlap']);
        $this->assertEmpty($result['conflicts']);
    }

    /** @test */
    public function it_handles_validation_errors_gracefully()
    {
        // Test avec des données invalides
        $result = $this->service->validateAssignment(
            $this->organization->id,
            999999, // ID véhicule inexistant
            $this->driver->id,
            now()->addHours(2),
            now()->addHours(6)
        );

        $this->assertFalse($result['isValid']);
        $this->assertNotEmpty($result['validationErrors']);
    }

    /** @test */
    public function it_calculates_correct_overlap_duration()
    {
        $existingStart = now()->addHours(2);
        $existingEnd = now()->addHours(8);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->organization->id])->id,
            'start_datetime' => $existingStart,
            'end_datetime' => $existingEnd,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Nouvelle affectation qui chevauche partiellement
        $newStart = now()->addHours(6); // Chevauche 2 heures
        $newEnd = now()->addHours(10);

        $result = $this->service->checkOverlap(
            $this->organization->id,
            $this->vehicle->id,
            $this->driver->id,
            $newStart,
            $newEnd
        );

        $this->assertTrue($result['hasOverlap']);
        $this->assertEquals(2, $result['conflicts'][0]['overlap_hours']);
    }
}