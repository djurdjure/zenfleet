<?php

namespace Tests\Feature;

use App\Livewire\Admin\Vehicles\VehicleIndex;
use App\Livewire\AssignmentTable;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VehicleAssignmentFixTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function can_change_vehicle_status_individually()
    {
        // Create or get statuses
        $status1 = VehicleStatus::firstOrCreate(['slug' => 'available'], ['name' => 'Available', 'color' => 'green']);
        $status2 = VehicleStatus::firstOrCreate(['slug' => 'maintenance'], ['name' => 'Maintenance', 'color' => 'red']);

        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $status1->id,
        ]);

        Livewire::test(VehicleIndex::class)
            ->call('confirmIndividualStatusChange', $vehicle->id)
            ->assertSet('individualStatusVehicleId', $vehicle->id)
            ->assertSet('showIndividualStatusModal', true)
            ->set('individualStatusId', $status2->id)
            ->call('updateIndividualStatus')
            ->assertSet('showIndividualStatusModal', false);

        $this->assertEquals($status2->id, $vehicle->fresh()->status_id);
    }

    /** @test */
    public function assignment_deletion_updates_statuses()
    {
        // Setup statuses
        $parkingStatus = VehicleStatus::firstOrCreate(['slug' => 'parking'], ['name' => 'Parking', 'color' => 'blue']);
        $activeStatus = VehicleStatus::firstOrCreate(['slug' => 'active'], ['name' => 'Active', 'color' => 'green']);
        
        $availableDriverStatus = DriverStatus::firstOrCreate(['slug' => 'available'], ['name' => 'Available', 'color' => 'green']);
        $busyDriverStatus = DriverStatus::firstOrCreate(['slug' => 'busy'], ['name' => 'Busy', 'color' => 'red']);

        // Create vehicle and driver
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $activeStatus->id,
        ]);
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $busyDriverStatus->id,
        ]);

        // Create assignment
        $assignment = Assignment::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'start_datetime' => now(),
            'organization_id' => $this->organization->id,
            'status' => 'active'
        ]);

        // Test deletion
        Livewire::test(AssignmentTable::class)
            ->call('openDeleteModal', $assignment->id)
            ->call('deleteAssignment');

        // Verify assignment is deleted
        $this->assertSoftDeleted($assignment);

        // Verify statuses updated
        $this->assertEquals($parkingStatus->id, $vehicle->fresh()->status_id);
        $this->assertEquals($availableDriverStatus->id, $driver->fresh()->status_id);
    }
}
