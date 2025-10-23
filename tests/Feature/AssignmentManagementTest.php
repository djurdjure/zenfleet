<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AssignmentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Vehicle $vehicle1;
    protected Driver $driver1;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les permissions nécessaires
        Permission::firstOrCreate(['name' => 'view assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit assignments', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete assignments', 'guard_name' => 'web']);

        // Créer un rôle Admin et lui attribuer les permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(['view assignments', 'create assignments', 'edit assignments', 'delete assignments']);

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');
        $this->actingAs($this->adminUser);

        $this->vehicle1 = Vehicle::factory()->create(['organization_id' => $this->adminUser->organization_id]);
        $this->driver1 = Driver::factory()->create(['organization_id' => $this->adminUser->organization_id]);
    }

    /** @test */
    public function an_assignment_cannot_overlap_with_an_existing_assignment_for_the_same_vehicle()
    {
        // Créer une affectation existante
        Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->subDays(5),
            'end_datetime' => Carbon::now()->addDays(5),
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Tenter de créer une nouvelle affectation qui chevauche le véhicule
        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->adminUser->organization_id])->id,
            'start_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'end_time' => '14:00',
            'assignment_type' => 'scheduled',
            'reason' => 'Test Overlap Vehicle',
        ]);

        $response->assertSessionHas('error'); // Vérifier le message d'erreur de chevauchement
        $this->assertCount(1, Assignment::all()); // S'assurer qu'aucune nouvelle affectation n'a été créée
    }

    /** @test */
    public function an_assignment_cannot_overlap_with_an_existing_assignment_for_the_same_driver()
    {
        // Créer une affectation existante
        Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->subDays(5),
            'end_datetime' => Carbon::now()->addDays(5),
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Tenter de créer une nouvelle affectation qui chevauche le chauffeur
        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => Vehicle::factory()->create(['organization_id' => $this->adminUser->organization_id])->id,
            'driver_id' => $this->driver1->id,
            'start_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'end_time' => '14:00',
            'assignment_type' => 'scheduled',
            'reason' => 'Test Overlap Driver',
        ]);

        $response->assertSessionHas('error');
        $this->assertCount(1, Assignment::all());
    }

    /** @test */
    public function an_assignment_can_be_created_for_a_past_date()
    {
        $pastStartDate = Carbon::now()->subDays(10);
        $pastEndDate = Carbon::now()->subDays(8);

        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_date' => $pastStartDate->format('Y-m-d'),
            'start_time' => '09:00',
            'end_date' => $pastEndDate->format('Y-m-d'),
            'end_time' => '17:00',
            'assignment_type' => 'scheduled',
            'reason' => 'Past Assignment',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('assignments', [
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => $pastStartDate->setTime(9, 0)->format('Y-m-d H:i:s'),
            'end_datetime' => $pastEndDate->setTime(17, 0)->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    public function updating_an_assignment_prevents_overlaps()
    {
        // Créer une première affectation
        $assignment1 = Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->subDays(10),
            'end_datetime' => Carbon::now()->subDays(5),
            'status' => Assignment::STATUS_COMPLETED,
            'created_by' => $this->adminUser->id,
        ]);

        // Créer une deuxième affectation (celle que nous allons essayer de modifier pour qu'elle chevauche)
        $assignment2 = Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => Vehicle::factory()->create(['organization_id' => $this->adminUser->organization_id])->id,
            'driver_id' => Driver::factory()->create(['organization_id' => $this->adminUser->organization_id])->id,
            'start_datetime' => Carbon::now()->addDays(1),
            'end_datetime' => Carbon::now()->addDays(3),
            'status' => Assignment::STATUS_SCHEDULED,
            'created_by' => $this->adminUser->id,
        ]);

        // Tenter de modifier assignment2 pour qu'elle chevauche assignment1 sur le véhicule1
        $response = $this->put(route('admin.assignments.update', $assignment2->id), [
            'vehicle_id' => $this->vehicle1->id, // Changer pour chevaucher
            'driver_id' => $assignment2->driver_id,
            'start_datetime' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
            'end_datetime' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
            'status' => Assignment::STATUS_COMPLETED,
            'reason' => 'Updated to overlap',
        ]);

        $response->assertSessionHas('error');
        // Assurez-vous que l'affectation n'a pas été modifiée avec le chevauchement
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment2->id,
            'vehicle_id' => $assignment2->vehicle_id, // Doit rester l'ancien véhicule
        ]);
    }

    /** @test */
    public function assignment_does_not_overlap_with_itself_during_update()
    {
        // Créer une affectation
        $assignment = Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->addDays(1),
            'end_datetime' => Carbon::now()->addDays(3),
            'status' => Assignment::STATUS_SCHEDULED,
            'created_by' => $this->adminUser->id,
            'reason' => 'Original Assignment',
        ]);

        // Modifier l'affectation sans créer de chevauchement
        $response = $this->put(route('admin.assignments.update', $assignment->id), [
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_datetime' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'), // Prolonger
            'status' => Assignment::STATUS_SCHEDULED,
            'reason' => 'Extended Assignment',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'end_datetime' => Carbon::now()->addDays(4)->format('Y-m-d H:i:s'),
        ]);
    }

    /** @test */
    public function cancelled_assignments_are_not_considered_for_overlapping()
    {
        // Créer une affectation annulée
        Assignment::create([
            'organization_id' => $this->adminUser->organization_id,
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_datetime' => Carbon::now()->subDays(5),
            'end_datetime' => Carbon::now()->addDays(5),
            'status' => Assignment::STATUS_CANCELLED,
            'created_by' => $this->adminUser->id,
        ]);

        // Créer une nouvelle affectation sur la même période - ne devrait PAS chevaucher car la première est annulée
        $response = $this->post(route('admin.assignments.store'), [
            'vehicle_id' => $this->vehicle1->id,
            'driver_id' => $this->driver1->id,
            'start_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'end_time' => '14:00',
            'assignment_type' => 'scheduled',
            'reason' => 'New Assignment',
        ]);

        $response->assertSessionHas('success');
        $this->assertCount(2, Assignment::all());
    }
}
