<?php

namespace Tests\Feature;

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

class RepairRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected User $driverUser;
    protected Driver $driver;
    protected User $supervisor;
    protected User $manager;
    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        // Create organization
        $this->organization = Organization::factory()->create([
            'organization_type' => 'enterprise',
        ]);

        // Set team context
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);

        // Create roles WITH organization_id (required by schema)
        $driverRole = Role::firstOrCreate([
            'name' => 'Chauffeur',
            'guard_name' => 'web',
            'organization_id' => $this->organization->id,
        ]);
        $supervisorRole = Role::firstOrCreate([
            'name' => 'Superviseur',
            'guard_name' => 'web',
            'organization_id' => $this->organization->id,
        ]);
        $fleetManagerRole = Role::firstOrCreate([
            'name' => 'Gestionnaire Flotte',
            'guard_name' => 'web',
            'organization_id' => $this->organization->id,
        ]);

        // Create permissions
        $permissions = [];
        $permissionNames = [
            'repair-requests.create',
            'repair-requests.view.own',
            'repair-requests.view.team',
            'repair-requests.view.all',
            'repair-requests.approve.level1',
            'repair-requests.approve.level2',
            'repair-requests.reject.level1',
            'repair-requests.reject.level2',
            'repair-requests.delete',
            'repair-requests.export',
        ];
        foreach ($permissionNames as $pName) {
            $permissions[$pName] = Permission::firstOrCreate([
                'name' => $pName,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to roles
        $driverRole->givePermissionTo([
            $permissions['repair-requests.create'],
            $permissions['repair-requests.view.own'],
        ]);
        $supervisorRole->givePermissionTo([
            $permissions['repair-requests.view.own'],
            $permissions['repair-requests.view.team'],
            $permissions['repair-requests.approve.level1'],
            $permissions['repair-requests.reject.level1'],
        ]);
        $fleetManagerRole->givePermissionTo([
            $permissions['repair-requests.view.own'],
            $permissions['repair-requests.view.all'],
            $permissions['repair-requests.approve.level2'],
            $permissions['repair-requests.reject.level2'],
        ]);

        // Create driver user
        $this->driverUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);
        $this->driverUser->assignRole('Chauffeur');
        DB::table('model_has_roles')
            ->where('model_id', $this->driverUser->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);

        // Create supervisor user
        $this->supervisor = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);
        $this->supervisor->assignRole('Superviseur');
        DB::table('model_has_roles')
            ->where('model_id', $this->supervisor->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);

        // Create fleet manager user
        $this->manager = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $permissionRegistrar->setPermissionsTeamId($this->organization->id);
        $this->manager->assignRole('Gestionnaire Flotte');
        DB::table('model_has_roles')
            ->where('model_id', $this->manager->id)
            ->where('model_type', User::class)
            ->update(['organization_id' => $this->organization->id]);

        // Create driver record linked to driverUser, supervised by supervisor
        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'user_id' => $this->driverUser->id,
            'supervisor_id' => $this->supervisor->id,
        ]);

        // Create vehicle
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

    public function test_driver_can_create_repair_request()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post(route('admin.repair-requests.store'), [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'title' => 'Problème moteur urgent',
            'description' => 'Problème moteur urgent nécessitant intervention rapide',
            'urgency' => 'high',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('repair_requests', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
            'urgency' => 'high',
        ]);
    }

    public function test_supervisor_can_approve_repair_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->post(route('admin.repair-requests.approve-supervisor', $repairRequest), [
            'comment' => 'Approuvé après vérification terrain',
        ]);

        $response->assertRedirect();

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_PENDING_FLEET_MANAGER, $repairRequest->status);
        $this->assertEquals('approved', $repairRequest->supervisor_status);
        $this->assertEquals($this->supervisor->id, $repairRequest->supervisor_id);
    }

    public function test_supervisor_can_reject_repair_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->post(route('admin.repair-requests.reject-supervisor', $repairRequest), [
            'reason' => 'Réparation non justifiée - maintenance préventive suffisante',
        ]);

        $response->assertRedirect();

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_REJECTED_SUPERVISOR, $repairRequest->status);
        $this->assertEquals('rejected', $repairRequest->supervisor_status);
        $this->assertNotNull($repairRequest->rejected_at);
    }

    public function test_fleet_manager_can_approve_after_supervisor()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => RepairRequest::STATUS_PENDING_FLEET_MANAGER,
            'supervisor_status' => 'approved',
            'supervisor_approved_at' => now(),
        ]);

        $this->actingAs($this->manager);

        $response = $this->post(route('admin.repair-requests.approve-fleet-manager', $repairRequest), [
            'comment' => 'Budget validé - procéder aux réparations',
        ]);

        $response->assertRedirect();

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_APPROVED_FINAL, $repairRequest->status);
        $this->assertEquals('approved', $repairRequest->fleet_manager_status);
        $this->assertEquals($this->manager->id, $repairRequest->fleet_manager_id);
        $this->assertNotNull($repairRequest->final_approved_at);
    }

    public function test_fleet_manager_cannot_approve_without_supervisor_approval()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);

        // Fleet manager cannot approve level 2 when status is pending_supervisor
        $this->actingAs($this->manager);
        $response = $this->post(route('admin.repair-requests.approve-fleet-manager', $repairRequest), [
            'comment' => 'Tentative directe',
        ]);
        $response->assertForbidden();
    }

    public function test_driver_cannot_approve_repair_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);

        // Driver cannot approve their own request
        $this->actingAs($this->driverUser);
        $response = $this->post(route('admin.repair-requests.approve-supervisor', $repairRequest), [
            'comment' => 'Auto-approbation',
        ]);
        $response->assertForbidden();
    }

    public function test_organization_isolation_in_repair_requests()
    {
        $otherOrganization = Organization::factory()->create();
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id,
        ]);

        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        $this->actingAs($otherUser);
        $response = $this->get(route('admin.repair-requests.show', $repairRequest));
        // Should be forbidden (different organization) or 404
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }

    public function test_repair_request_creates_history_on_approval()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
        ]);

        $this->actingAs($this->supervisor);

        $this->post(route('admin.repair-requests.approve-supervisor', $repairRequest), [
            'comment' => 'Approuvé',
        ]);

        $this->assertDatabaseHas('repair_request_history', [
            'repair_request_id' => $repairRequest->id,
            'action' => 'supervisor_approved',
        ]);
    }
}
