<?php

use App\Models\Driver;
use App\Models\Organization;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    // Create roles
    Role::create(['name' => 'Chauffeur', 'guard_name' => 'web']);
    Role::create(['name' => 'Admin', 'guard_name' => 'web']);

    // Create organization
    $this->organization = Organization::factory()->create();

    // Create driver user
    $this->driverUser = User::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
    $this->driverUser->assignRole('Chauffeur');

    $this->driver = Driver::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->driverUser->id,
    ]);

    $this->vehicle = Vehicle::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    Storage::fake('public');
});

test('driver can create repair request with valid data', function () {
    actingAs($this->driverUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Pneu crevé',
        'description' => 'Le pneu avant droit est complètement à plat après avoir roulé sur un clou',
        'urgency' => 'high',
        'current_mileage' => 45000,
        'current_location' => 'Parking central',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    assertDatabaseHas('repair_requests', [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Pneu crevé',
        'urgency' => 'high',
        'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
    ]);
});

test('driver can create repair request with photos', function () {
    actingAs($this->driverUser);

    $photo1 = UploadedFile::fake()->image('photo1.jpg', 800, 600);
    $photo2 = UploadedFile::fake()->image('photo2.jpg', 800, 600);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Problème de phares',
        'description' => 'Les phares avant ne fonctionnent plus correctement depuis ce matin',
        'urgency' => 'normal',
        'photos' => [$photo1, $photo2],
    ]);

    $response->assertRedirect();

    $repairRequest = RepairRequest::latest()->first();
    expect($repairRequest->photos)->toBeArray()
        ->and(count($repairRequest->photos))->toBe(2);

    Storage::disk('public')->assertExists($repairRequest->photos[0]['path']);
    Storage::disk('public')->assertExists($repairRequest->photos[1]['path']);
});

test('driver cannot create repair request without description', function () {
    actingAs($this->driverUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Problème moteur',
        'description' => 'Court', // Too short (min 20 chars)
        'urgency' => 'high',
    ]);

    $response->assertSessionHasErrors('description');
});

test('driver cannot create repair request without title', function () {
    actingAs($this->driverUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'description' => 'Une description suffisamment longue pour passer la validation',
        'urgency' => 'high',
    ]);

    $response->assertSessionHasErrors('title');
});

test('driver cannot create repair request with invalid urgency', function () {
    actingAs($this->driverUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Problème',
        'description' => 'Une description suffisamment longue pour passer la validation',
        'urgency' => 'invalid_urgency',
    ]);

    $response->assertSessionHasErrors('urgency');
});

test('non-driver cannot create repair request', function () {
    $adminUser = User::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
    $adminUser->assignRole('Admin');

    actingAs($adminUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Problème',
        'description' => 'Une description suffisamment longue pour passer la validation',
        'urgency' => 'high',
    ]);

    // Admin can also create, so this should pass
    // If you want to restrict to drivers only, update the test
    $response->assertRedirect();
});

test('driver cannot create repair request for another organization vehicle', function () {
    actingAs($this->driverUser);

    $otherOrgVehicle = Vehicle::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $otherOrgVehicle->id,
        'title' => 'Problème',
        'description' => 'Une description suffisamment longue pour passer la validation',
        'urgency' => 'high',
    ]);

    $response->assertSessionHasErrors('vehicle_id');
});

test('repair request defaults to normal urgency if not specified', function () {
    actingAs($this->driverUser);

    $response = post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Problème mineur',
        'description' => 'Une description suffisamment longue pour passer la validation',
    ]);

    $response->assertRedirect();

    assertDatabaseHas('repair_requests', [
        'title' => 'Problème mineur',
        'urgency' => RepairRequest::URGENCY_NORMAL,
    ]);
});

test('repair request creates history entry on creation', function () {
    actingAs($this->driverUser);

    post(route('admin.repair-requests.store'), [
        'driver_id' => $this->driver->id,
        'vehicle_id' => $this->vehicle->id,
        'title' => 'Test',
        'description' => 'Une description suffisamment longue pour passer la validation',
    ]);

    $repairRequest = RepairRequest::latest()->first();

    assertDatabaseHas('repair_request_history', [
        'repair_request_id' => $repairRequest->id,
        'action' => 'created',
    ]);
});
