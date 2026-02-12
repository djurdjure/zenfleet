<?php

namespace Tests\Feature\RepairRequest;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CreateRepairRequestTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;
    private User $driverUser;
    private Driver $driver;
    private Vehicle $vehicle;
    private Permission $createPermission;

    protected function setUp(): void
    {
        parent::setUp();

        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        $this->organization = Organization::factory()->create();
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);

        $driverRole = Role::firstOrCreate([
            'name' => 'Chauffeur',
            'guard_name' => 'web',
            'organization_id' => $this->organization->id,
        ]);
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
            'organization_id' => $this->organization->id,
        ]);

        $createPermission = Permission::firstOrCreate([
            'name' => 'create repair requests',
            'guard_name' => 'web',
        ]);
        $this->createPermission = $createPermission;

        $driverRole->givePermissionTo($createPermission);
        $adminRole->givePermissionTo($createPermission);

        $this->driverUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);
        $this->driverUser->assignRole('Chauffeur');
        $this->driverUser->givePermissionTo($this->createPermission);
        DB::table('model_has_roles')
            ->where('model_id', $this->driverUser->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);
        DB::table('model_has_permissions')
            ->where('model_id', $this->driverUser->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);

        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->driverUser->id,
        ]);

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null,
            'status' => Assignment::STATUS_ACTIVE,
        ]);

        Storage::fake('public');
    }

    public function test_driver_can_create_repair_request_with_valid_data()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Pneu creve',
            'description' => 'Le pneu avant droit est completement a plat apres avoir roule sur un clou',
            'urgency' => 'high',
            'current_mileage' => 45000,
            'current_location' => 'Parking central',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('repair_requests', [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Pneu creve',
            'urgency' => 'high',
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);
    }

    public function test_driver_can_create_repair_request_with_photos()
    {
        $this->actingAs($this->driverUser);

        $photo1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
        $photo2 = UploadedFile::fake()->image('photo2.jpg', 800, 600);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Probleme de phares',
            'description' => 'Les phares avant ne fonctionnent plus correctement depuis ce matin',
            'urgency' => 'normal',
            'photos' => [$photo1, $photo2],
        ]);

        $response->assertRedirect();

        $repairRequest = RepairRequest::latest()->first();
        $this->assertIsArray($repairRequest->photos);
        $this->assertCount(2, $repairRequest->photos);

        Storage::disk('public')->assertExists($repairRequest->photos[0]['path']);
        Storage::disk('public')->assertExists($repairRequest->photos[1]['path']);
    }

    public function test_driver_cannot_create_repair_request_without_description()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Probleme moteur',
            'description' => 'Court',
            'urgency' => 'high',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_driver_cannot_create_repair_request_without_title()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'description' => 'Une description suffisamment longue pour passer la validation',
            'urgency' => 'high',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_driver_cannot_create_repair_request_with_invalid_urgency()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Probleme',
            'description' => 'Une description suffisamment longue pour passer la validation',
            'urgency' => 'invalid_urgency',
        ]);

        $response->assertSessionHasErrors('urgency');
    }

    public function test_non_driver_can_create_repair_request()
    {
        $adminUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        app(PermissionRegistrar::class)->setPermissionsTeamId($this->organization->id);
        $adminUser->assignRole('Admin');
        $adminUser->givePermissionTo($this->createPermission);
        DB::table('model_has_roles')
            ->where('model_id', $adminUser->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);
        DB::table('model_has_permissions')
            ->where('model_id', $adminUser->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);

        $this->actingAs($adminUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Probleme',
            'description' => 'Une description suffisamment longue pour passer la validation',
            'urgency' => 'high',
        ]);

        $response->assertRedirect();
    }

    public function test_driver_cannot_create_repair_request_for_another_organization_vehicle()
    {
        $this->actingAs($this->driverUser);

        $otherOrgVehicle = Vehicle::factory()->create([
            'organization_id' => Organization::factory()->create()->id,
        ]);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $otherOrgVehicle->id,
            'title' => 'Probleme',
            'description' => 'Une description suffisamment longue pour passer la validation',
            'urgency' => 'high',
        ]);

        $response->assertSessionHasErrors('vehicle_id');
    }

    public function test_repair_request_defaults_to_normal_urgency_if_not_specified()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Probleme mineur',
            'description' => 'Une description suffisamment longue pour passer la validation',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('repair_requests', [
            'title' => 'Probleme mineur',
            'urgency' => RepairRequest::URGENCY_NORMAL,
        ]);
    }

    public function test_repair_request_creates_history_entry_on_creation()
    {
        $this->actingAs($this->driverUser);

        $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Test demande',
            'description' => 'Une description suffisamment longue pour passer la validation',
        ]);

        $repairRequest = RepairRequest::latest()->first();

        $this->assertDatabaseHas('repair_request_history', [
            'repair_request_id' => $repairRequest->id,
            'action' => 'created',
        ]);
    }
}
