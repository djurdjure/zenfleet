<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentSchedulingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;
    protected $vehicle;
    protected $driver;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une organisation de test
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'status' => 'active'
        ]);

        // Créer un utilisateur de test
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
            'role' => 'admin'
        ]);

        // Configurer le contexte de l'organisation pour les permissions (CRITIQUE pour Enterprise/Multi-tenant)
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($this->organization->id);

        // Créer un véhicule disponible
        $vehicleStatus = VehicleStatus::factory()->create(['name' => 'Disponible']);
        $vehicleType = VehicleType::factory()->create(['name' => 'Berline']);

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'registration_plate' => 'TEST-123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'current_mileage' => 50000,
            'status_id' => $vehicleStatus->id,
            'vehicle_type_id' => $vehicleType->id
        ]);

        // Créer un chauffeur disponible
        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'personal_phone' => '0555123456'
        ]);

        // Créer les permissions nécessaires (Legacy + Enterprise pour compatibilité Middleware)
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create assignments', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'assignments.create', 'guard_name' => 'web']);

        // Assigner les permissions nécessaires
        $this->user->givePermissionTo('create assignments');
        $this->user->givePermissionTo('assignments.create');
    }

    /** @test */
    public function it_can_create_open_assignment_with_separated_date_time()
    {
        $this->actingAs($this->user);

        $startDate = now()->format('d/m/Y'); // ✅ Format français requis
        $startTime = '09:30';

        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => $startDate,
            'start_time' => $startTime,
            'start_mileage' => 50000,
            'assignment_type' => 'open',
            'purpose' => 'mission',
            'notes' => 'Test affectation ouverte'
        ]);

        $response->assertRedirect(route('admin.assignments.index'));
        $response->assertSessionHas('success');

        // Vérifier que l'affectation a été créée
        // Note: En base de donnée c'est stocké en Y-m-d H:i:s
        $this->assertDatabaseHas('assignments', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_mileage' => 50000,
            'status' => 'active',
            'end_datetime' => null, // Affectation ouverte
        ]);

        // Vérifier que la date/heure a été correctement combinée
        $assignment = Assignment::where('vehicle_id', $this->vehicle->id)->first();
        $expectedDateTime = Carbon::createFromFormat('d/m/Y H:i', $startDate . ' ' . $startTime);

        $this->assertEquals(
            $expectedDateTime->format('Y-m-d H:i'),
            $assignment->start_datetime->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_can_create_scheduled_assignment_with_end_date_time()
    {
        $this->actingAs($this->user);

        // Utiliser une date future pour que le statut soit "scheduled"
        // L'observer recalculera le statut et forcera "active" si la date est passée/aujourd'hui
        $startDate = now()->addDay()->format('d/m/Y');
        $startTime = '10:00';
        $endDate = now()->addDays(2)->format('d/m/Y');
        $endTime = '17:00';

        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => $startDate,
            'start_time' => $startTime,
            'start_mileage' => 50000,
            'assignment_type' => 'scheduled',
            'end_date' => $endDate,
            'end_time' => $endTime,
            'estimated_end_mileage' => 50200,
            'purpose' => 'formation',
            'notes' => 'Test affectation programmée'
        ]);

        $response->assertRedirect(route('admin.assignments.index'));
        $response->assertSessionHas('success');

        // Vérifier que l'affectation programmée a été créée
        $this->assertDatabaseHas('assignments', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_mileage' => 50000,
            'status' => 'scheduled',
        ]);

        // Vérifier les dates de début et fin
        $assignment = Assignment::where('vehicle_id', $this->vehicle->id)->first();

        $expectedStartDateTime = Carbon::createFromFormat('d/m/Y H:i', $startDate . ' ' . $startTime);
        $expectedEndDateTime = Carbon::createFromFormat('d/m/Y H:i', $endDate . ' ' . $endTime);

        $this->assertEquals(
            $expectedStartDateTime->format('Y-m-d H:i'),
            $assignment->start_datetime->format('Y-m-d H:i')
        );

        $this->assertEquals(
            $expectedEndDateTime->format('Y-m-d H:i'),
            $assignment->end_datetime->format('Y-m-d H:i')
        );
    }

    /** @test */
    public function it_validates_required_fields_for_scheduled_assignment()
    {
        $this->actingAs($this->user);

        // Test affectation programmée sans date de fin
        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => now()->format('d/m/Y'),
            'start_time' => '10:00',
            'start_mileage' => 50000,
            'assignment_type' => 'scheduled',
            // end_date et end_time manquants
        ]);

        $response->assertSessionHasErrors(['end_date', 'end_time']);
    }

    /** @test */
    public function it_validates_end_date_must_be_after_start_date()
    {
        $this->actingAs($this->user);

        $startDate = now()->format('d/m/Y');
        $endDate = now()->subDay()->format('d/m/Y'); // Date antérieure

        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => $startDate,
            'start_time' => '10:00',
            'start_mileage' => 50000,
            'assignment_type' => 'scheduled',
            'end_date' => $endDate,
            'end_time' => '17:00',
        ]);

        $response->assertSessionHasErrors(['end_date']);
    }

    /** @test */
    public function it_validates_time_format()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => now()->format('d/m/Y'),
            'start_time' => '25:00', // Heure invalide
            'start_mileage' => 50000,
            'assignment_type' => 'open',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    /** @test */
    /*
    public function it_logs_assignment_creation()
    {
        $this->actingAs($this->user);

        // Capturer les logs
        \Log::fake();

        $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_date' => now()->format('d/m/Y'),
            'start_time' => '09:30',
            'start_mileage' => 50000,
            'assignment_type' => 'open',
            'purpose' => 'mission',
            'notes' => 'Test avec log'
        ]);

        // Vérifier que le log a été créé
        \Log::assertLogged('info', function ($message, $context) {
            return $message === 'Nouvelle affectation créée' &&
                isset($context['assignment_id']) &&
                isset($context['vehicle_id']) &&
                isset($context['driver_id']);
        });
    }
    */
}
