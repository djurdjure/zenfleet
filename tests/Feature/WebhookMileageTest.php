<?php

namespace Tests\Feature;

use App\Models\FuelType;
use App\Models\Organization;
use App\Models\TransmissionType;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookMileageTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_updates_vehicle_mileage_for_matching_org(): void
    {
        $org = Organization::factory()->create(['status' => 'active']);
        $token = 'webhook-token-1';
        config(['app.webhook_tokens' => [$org->id => $token]]);

        $this->seedVehicleRefs();
        $vehicle = $this->createVehicleForOrg($org->id, ['current_mileage' => 1000]);

        $payload = [
            'vehicle_id' => $vehicle->id,
            'current_mileage' => 1200,
            'timestamp' => now()->toISOString(),
        ];

        $this->postJson('/api/webhooks/vehicle/mileage-update', $payload, [
            'X-Webhook-Token' => $token,
        ])->assertStatus(200);

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'current_mileage' => 1200,
        ]);
    }

    public function test_webhook_rejects_invalid_token(): void
    {
        $org = Organization::factory()->create(['status' => 'active']);
        config(['app.webhook_tokens' => [$org->id => 'valid-token']]);

        $this->seedVehicleRefs();
        $vehicle = $this->createVehicleForOrg($org->id, ['current_mileage' => 1000]);

        $payload = [
            'vehicle_id' => $vehicle->id,
            'current_mileage' => 1100,
            'timestamp' => now()->toISOString(),
        ];

        $this->postJson('/api/webhooks/vehicle/mileage-update', $payload, [
            'X-Webhook-Token' => 'invalid-token',
        ])->assertStatus(401);
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
