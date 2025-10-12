<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\Organization;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\TransmissionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * 🧪 TESTS ENTERPRISE: Vehicle Status Scopes
 *
 * Valide que tous les scopes Vehicle fonctionnent correctement
 * et que plus aucune erreur "column status does not exist" ne survient
 *
 * @version 1.0-TEST
 * @author ZenFleet QA Team
 */
class VehicleStatusScopeTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;
    private VehicleType $vehicleType;
    private FuelType $fuelType;
    private TransmissionType $transmissionType;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une organisation de test
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'status' => 'active',
        ]);

        // Créer les dépendances nécessaires
        $this->vehicleType = VehicleType::firstOrCreate(['name' => 'Sedan']);
        $this->fuelType = FuelType::firstOrCreate(['name' => 'Diesel']);
        $this->transmissionType = TransmissionType::firstOrCreate(['name' => 'Manual']);

        // Vérifier que les statuts existent
        VehicleStatus::firstOrCreate(['id' => 1], ['name' => 'Actif']);
        VehicleStatus::firstOrCreate(['id' => 2], ['name' => 'En maintenance']);
        VehicleStatus::firstOrCreate(['id' => 3], ['name' => 'Inactif']);
    }

    /**
     * @test
     * ✅ Test: Scope active() retourne uniquement les véhicules actifs
     */
    public function it_returns_only_active_vehicles()
    {
        // Arrange: Créer 3 véhicules avec différents status
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $maintenanceVehicle = $this->createVehicle(['status_id' => 2]);
        $inactiveVehicle = $this->createVehicle(['status_id' => 3]);

        // Act: Récupérer uniquement les véhicules actifs
        $result = Vehicle::active()->get();

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($activeVehicle));
        $this->assertFalse($result->contains($maintenanceVehicle));
        $this->assertFalse($result->contains($inactiveVehicle));
    }

    /**
     * @test
     * ✅ Test: Scope inMaintenance() fonctionne
     */
    public function it_returns_only_maintenance_vehicles()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $maintenanceVehicle = $this->createVehicle(['status_id' => 2]);

        // Act
        $result = Vehicle::inMaintenance()->get();

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($maintenanceVehicle));
        $this->assertFalse($result->contains($activeVehicle));
    }

    /**
     * @test
     * ✅ Test: Scope inactive() fonctionne
     */
    public function it_returns_only_inactive_vehicles()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $inactiveVehicle = $this->createVehicle(['status_id' => 3]);

        // Act
        $result = Vehicle::inactive()->get();

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($inactiveVehicle));
        $this->assertFalse($result->contains($activeVehicle));
    }

    /**
     * @test
     * ✅ Test: Scope byStatus($id) fonctionne
     */
    public function it_filters_by_status_id()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $maintenanceVehicle = $this->createVehicle(['status_id' => 2]);

        // Act
        $result = Vehicle::byStatus(1)->get();

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($activeVehicle));
    }

    /**
     * @test
     * ✅ Test: Helper isActive() fonctionne
     */
    public function it_checks_if_vehicle_is_active()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $inactiveVehicle = $this->createVehicle(['status_id' => 3]);

        // Act & Assert
        $this->assertTrue($activeVehicle->isActive());
        $this->assertFalse($inactiveVehicle->isActive());
    }

    /**
     * @test
     * ✅ Test: Helper getStatusName() retourne le bon nom
     */
    public function it_returns_correct_status_name()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $maintenanceVehicle = $this->createVehicle(['status_id' => 2]);
        $inactiveVehicle = $this->createVehicle(['status_id' => 3]);

        // Act & Assert
        $this->assertEquals('Actif', $activeVehicle->getStatusName());
        $this->assertEquals('En maintenance', $maintenanceVehicle->getStatusName());
        $this->assertEquals('Inactif', $inactiveVehicle->getStatusName());
    }

    /**
     * @test
     * ✅ Test: Pas d'erreur "column status does not exist"
     */
    public function it_does_not_throw_status_column_error()
    {
        // Arrange
        $this->createVehicle(['status_id' => 1]);

        // Act & Assert: Ces requêtes ne doivent PAS lancer d'exception
        try {
            Vehicle::where('organization_id', $this->organization->id)->active()->get();
            Vehicle::active()->count();
            Vehicle::inMaintenance()->count();
            Vehicle::inactive()->count();

            $this->assertTrue(true, 'Aucune erreur SQL détectée');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->fail('Erreur SQL détectée: ' . $e->getMessage());
        }
    }

    /**
     * @test
     * ✅ Test: Organization->activeVehicles() utilise bien le scope
     */
    public function organization_active_vehicles_uses_scope()
    {
        // Arrange
        $activeVehicle = $this->createVehicle(['status_id' => 1]);
        $inactiveVehicle = $this->createVehicle(['status_id' => 3]);

        // Act
        $result = $this->organization->activeVehicles;

        // Assert
        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($activeVehicle));
        $this->assertFalse($result->contains($inactiveVehicle));
    }

    /**
     * Helper: Créer un véhicule de test
     */
    private function createVehicle(array $attributes = []): Vehicle
    {
        return Vehicle::create(array_merge([
            'organization_id' => $this->organization->id,
            'registration_plate' => 'TEST-' . rand(1000, 9999),
            'vin' => 'VIN' . rand(100000, 999999),
            'brand' => 'Test Brand',
            'model' => 'Test Model',
            'color' => 'Blue',
            'vehicle_type_id' => $this->vehicleType->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'status_id' => 1, // Par défaut actif
            'manufacturing_year' => 2020,
            'acquisition_date' => now(),
            'purchase_price' => 25000,
            'initial_mileage' => 0,
            'current_mileage' => 0,
            'seats' => 5,
        ], $attributes));
    }
}
