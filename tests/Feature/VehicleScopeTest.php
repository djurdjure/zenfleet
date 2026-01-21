<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\FuelType;
use App\Models\Organization;
use App\Models\TransmissionType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_cannot_see_other_driver_vehicles(): void
    {
        $org = Organization::factory()->create(['status' => 'active']);

        $driverStatus = DriverStatus::firstOrCreate(
            ['slug' => 'disponible'],
            ['name' => 'Disponible', 'is_active' => true, 'can_drive' => true, 'can_assign' => true, 'requires_validation' => false]
        );

        $user1 = User::factory()->create(['organization_id' => $org->id]);
        $user2 = User::factory()->create(['organization_id' => $org->id]);

        $driver1 = Driver::create([
            'user_id' => $user1->id,
            'organization_id' => $org->id,
            'first_name' => 'Driver',
            'last_name' => 'One',
            'employee_number' => 'EMP-10001',
            'personal_phone' => '0000000001',
            'license_number' => 'LN-10001',
            'status_id' => $driverStatus->id,
            'recruitment_date' => now()->subYear(),
            'birth_date' => now()->subYears(30),
        ]);

        $driver2 = Driver::create([
            'user_id' => $user2->id,
            'organization_id' => $org->id,
            'first_name' => 'Driver',
            'last_name' => 'Two',
            'employee_number' => 'EMP-10002',
            'personal_phone' => '0000000002',
            'license_number' => 'LN-10002',
            'status_id' => $driverStatus->id,
            'recruitment_date' => now()->subYear(),
            'birth_date' => now()->subYears(29),
        ]);

        $this->seedVehicleRefs();
        $vehicle1 = $this->createVehicleForOrg($org->id, ['registration_plate' => 'TEST-1001', 'vin' => 'VIN1001']);
        $vehicle2 = $this->createVehicleForOrg($org->id, ['registration_plate' => 'TEST-1002', 'vin' => 'VIN1002']);

        Assignment::create([
            'organization_id' => $org->id,
            'vehicle_id' => $vehicle1->id,
            'driver_id' => $driver1->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => now()->addHour(),
            'status' => 'active',
        ]);

        Assignment::create([
            'organization_id' => $org->id,
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $driver2->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => now()->addHour(),
            'status' => 'active',
        ]);

        $this->actingAs($user1);

        $vehicles = Vehicle::all();

        $this->assertTrue($vehicles->contains($vehicle1));
        $this->assertFalse($vehicles->contains($vehicle2));
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
