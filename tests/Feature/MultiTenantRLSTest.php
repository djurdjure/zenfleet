<?php

namespace Tests\Feature;

use App\Models\FuelType;
use App\Models\Organization;
use App\Models\TransmissionType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantRLSTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_access_is_scoped_by_org_and_access(): void
    {
        $org1 = Organization::factory()->create(['status' => 'active']);
        $org2 = Organization::factory()->create(['status' => 'active']);

        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);

        $this->seedVehicleRefs();
        $vehicle = $this->createVehicleForOrg($org1->id);

        $user1->vehicles()->attach($vehicle->id, [
            'granted_at' => now(),
            'granted_by' => $user1->id,
            'access_type' => 'manual',
        ]);

        $this->actingAs($user1, 'sanctum')
            ->getJson('/api/v1/vehicles/' . $vehicle->id)
            ->assertStatus(200);

        $this->actingAs($user2, 'sanctum')
            ->getJson('/api/v1/vehicles/' . $vehicle->id)
            ->assertStatus(404);
    }

    private function seedVehicleRefs(): void
    {
        VehicleType::firstOrCreate(['name' => 'Sedan']);
        FuelType::firstOrCreate(['name' => 'Diesel']);
        TransmissionType::firstOrCreate(['name' => 'Manual']);
        VehicleStatus::firstOrCreate(['name' => 'Parking']);
    }

    private function createVehicleForOrg(int $organizationId, array $attributes = []): Vehicle
    {
        $vehicleType = VehicleType::where('name', 'Sedan')->first();
        $fuelType = FuelType::where('name', 'Diesel')->first();
        $transmissionType = TransmissionType::where('name', 'Manual')->first();
        $status = VehicleStatus::where('name', 'Parking')->first();

        return Vehicle::create(array_merge([
            'organization_id' => $organizationId,
            'registration_plate' => 'TEST-' . rand(1000, 9999),
            'vin' => 'VIN' . rand(100000, 999999),
            'brand' => 'Test Brand',
            'model' => 'Test Model',
            'color' => 'Blue',
            'vehicle_type_id' => $vehicleType->id,
            'fuel_type_id' => $fuelType->id,
            'transmission_type_id' => $transmissionType->id,
            'status_id' => $status->id,
            'manufacturing_year' => 2020,
            'acquisition_date' => now(),
            'purchase_price' => 25000,
            'initial_mileage' => 0,
            'current_mileage' => 0,
            'seats' => 5,
        ], $attributes));
    }
}
