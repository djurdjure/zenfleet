<?php

namespace Tests\Feature\Admin;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleStatus;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

/**
 * 🚗 Tests Enterprise Ultra-Professionnels pour le Module Véhicules
 *
 * Test Suite complète pour valider toutes les fonctionnalités enterprise
 * du module de gestion des véhicules avec sécurité RBAC, validation avancée
 * et analytics business intelligence.
 *
 * @package Tests\Feature\Admin
 * @version 2.0-Enterprise
 * @author ZenFleet Development Team
 */
class VehicleEnterpriseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $managerUser;
    protected User $supervisorUser;
    protected Organization $organization;
    protected VehicleType $vehicleType;
    protected VehicleStatus $vehicleStatus;
    protected FuelType $fuelType;
    protected TransmissionType $transmissionType;

    /**
     * Configuration Enterprise du Test Environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Création de l'organisation de test
        $this->organization = Organization::factory()->create([
            'name' => 'ZenFleet Test Enterprise',
            'email' => 'test@zenfleet.com',
            'is_active' => true
        ]);

        // Création des rôles enterprise
        $adminRole = Role::create(['name' => 'Super Admin']);
        $managerRole = Role::create(['name' => 'Gestionnaire Flotte']);
        $supervisorRole = Role::create(['name' => 'Supervisor']);

        // Création des utilisateurs de test avec rôles
        $this->adminUser = User::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'admin@zenfleet.com'
        ]);
        $this->adminUser->assignRole($adminRole);

        $this->managerUser = User::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'manager@zenfleet.com'
        ]);
        $this->managerUser->assignRole($managerRole);

        $this->supervisorUser = User::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'supervisor@zenfleet.com'
        ]);
        $this->supervisorUser->assignRole($supervisorRole);

        // Création des données de référence
        $this->vehicleType = VehicleType::factory()->create(['name' => 'Berline']);
        $this->vehicleStatus = VehicleStatus::factory()->create(['name' => 'Disponible']);
        $this->fuelType = FuelType::factory()->create(['name' => 'Essence']);
        $this->transmissionType = TransmissionType::factory()->create(['name' => 'Manuelle']);
    }

    /**
     * 🔐 Test: Accès à la liste des véhicules avec authentification
     */
    public function test_vehicle_index_requires_authentication(): void
    {
        $response = $this->get(route('admin.vehicles.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 🎯 Test: Liste des véhicules accessible aux utilisateurs autorisés
     */
    public function test_vehicle_index_accessible_to_authorized_users(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.index'));

        $response->assertOk()
            ->assertViewIs('admin.vehicles.enterprise-index')
            ->assertViewHas('vehicles')
            ->assertViewHas('analytics')
            ->assertViewHas('referenceData')
            ->assertSee($vehicle->registration_plate);
    }

    /**
     * 🏗️ Test: Création d'un véhicule avec validation enterprise
     */
    public function test_vehicle_creation_with_valid_data(): void
    {
        $vehicleData = [
            'registration_plate' => 'ABC-123-01',
            'vin' => '1HGBH41JXMN109186',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'color' => 'Blanc',
            'vehicle_type_id' => $this->vehicleType->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'status_id' => $this->vehicleStatus->id,
            'manufacturing_year' => 2023,
            'acquisition_date' => '2023-01-15',
            'purchase_price' => 25000.00,
            'current_value' => 22000.00,
            'initial_mileage' => 0,
            'current_mileage' => 15000,
            'engine_displacement_cc' => 1600,
            'power_hp' => 120,
            'seats' => 5,
            'notes' => 'Véhicule de test enterprise'
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.vehicles.store'), $vehicleData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vehicles', [
            'registration_plate' => 'ABC-123-01',
            'vin' => '1HGBH41JXMN109186',
            'organization_id' => $this->organization->id
        ]);
    }

    /**
     * ❌ Test: Validation des erreurs de création
     */
    public function test_vehicle_creation_validation_errors(): void
    {
        $invalidData = [
            'registration_plate' => '', // Requis
            'vin' => '123', // Trop court
            'brand' => '',
            'manufacturing_year' => 1800, // Trop ancien
            'purchase_price' => -1000, // Négatif
            'current_mileage' => -500 // Négatif
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.vehicles.store'), $invalidData);

        $response->assertSessionHasErrors([
            'registration_plate',
            'vin',
            'brand',
            'manufacturing_year',
            'purchase_price',
            'current_mileage'
        ]);
    }

    /**
     * 🔄 Test: Mise à jour d'un véhicule
     */
    public function test_vehicle_update_with_valid_data(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $updatedData = [
            'registration_plate' => 'XYZ-789-02',
            'vin' => $vehicle->vin,
            'brand' => 'Honda',
            'model' => 'Civic',
            'color' => 'Noir',
            'vehicle_type_id' => $this->vehicleType->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'status_id' => $this->vehicleStatus->id,
            'manufacturing_year' => 2022,
            'acquisition_date' => '2022-03-01',
            'purchase_price' => 28000.00,
            'current_value' => 24000.00,
            'initial_mileage' => 0,
            'current_mileage' => 25000,
            'engine_displacement_cc' => 1800,
            'power_hp' => 140,
            'seats' => 5
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.vehicles.update', $vehicle), $updatedData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'registration_plate' => 'XYZ-789-02',
            'brand' => 'Honda'
        ]);
    }

    /**
     * 👁️ Test: Affichage détaillé d'un véhicule
     */
    public function test_vehicle_show_displays_detailed_information(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.show', $vehicle));

        $response->assertOk()
            ->assertViewIs('admin.vehicles.enterprise-show')
            ->assertViewHas('vehicle')
            ->assertViewHas('analytics')
            ->assertViewHas('timeline')
            ->assertViewHas('recommendations')
            ->assertSee($vehicle->registration_plate)
            ->assertSee($vehicle->brand)
            ->assertSee($vehicle->model);
    }

    /**
     * 🗑️ Test: Suppression sécurisée d'un véhicule
     */
    public function test_vehicle_soft_delete(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.vehicles.destroy', $vehicle));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    /**
     * 🔒 Test: Isolation des données par organisation
     */
    public function test_vehicle_organization_isolation(): void
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $myVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $response = $this->actingAs($this->managerUser)
            ->get(route('admin.vehicles.index'));

        $response->assertOk()
            ->assertSee($myVehicle->registration_plate)
            ->assertDontSee($otherVehicle->registration_plate);
    }

    /**
     * 📊 Test: Analytics et métriques enterprise
     */
    public function test_vehicle_analytics_generation(): void
    {
        // Création de véhicules avec différents statuts
        Vehicle::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.index'));

        $response->assertOk();
        $analytics = $response->viewData('analytics');

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('total_vehicles', $analytics);
        $this->assertArrayHasKey('available_vehicles', $analytics);
        $this->assertEquals(3, $analytics['total_vehicles']);
    }

    /**
     * 🔍 Test: Filtrage avancé des véhicules
     */
    public function test_vehicle_advanced_filtering(): void
    {
        $vehicle1 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'brand' => 'Toyota'
        ]);

        $vehicle2 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'brand' => 'Honda'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.index', ['search' => 'Toyota']));

        $response->assertOk()
            ->assertSee($vehicle1->registration_plate)
            ->assertDontSee($vehicle2->registration_plate);
    }

    /**
     * 🚫 Test: Contraintes d'unicité enterprise
     */
    public function test_vehicle_uniqueness_constraints(): void
    {
        $existingVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'registration_plate' => 'UNIQUE-123',
            'vin' => '1HGBH41JXMN109187'
        ]);

        $duplicateData = [
            'registration_plate' => 'UNIQUE-123', // Duplicate
            'vin' => '1HGBH41JXMN109187', // Duplicate
            'brand' => 'Test',
            'model' => 'Test',
            'color' => 'Test',
            'vehicle_type_id' => $this->vehicleType->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'status_id' => $this->vehicleStatus->id,
            'manufacturing_year' => 2023,
            'acquisition_date' => '2023-01-15',
            'purchase_price' => 25000.00,
            'initial_mileage' => 0,
            'current_mileage' => 15000,
            'engine_displacement_cc' => 1600,
            'power_hp' => 120,
            'seats' => 5
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.vehicles.store'), $duplicateData);

        $response->assertSessionHasErrors(['registration_plate', 'vin']);
    }

    /**
     * ⚡ Test: Performance avec grandes données
     */
    public function test_vehicle_index_performance_with_large_dataset(): void
    {
        // Création de 100 véhicules pour tester la performance
        Vehicle::factory()->count(100)->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.index'));

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertOk();

        // Vérifier que la page se charge en moins de 2 secondes
        $this->assertLessThan(2.0, $executionTime, 'Vehicle index should load in under 2 seconds');
    }

    /**
     * 🎭 Test: Permissions granulaires RBAC
     */
    public function test_rbac_permissions_enforcement(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id
        ]);

        // Test avec utilisateur sans permissions
        $unauthorizedUser = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('admin.vehicles.index'));

        // Devrait être redirigé ou avoir une erreur 403
        $this->assertTrue(
            $response->status() === 403 ||
            $response->isRedirect()
        );
    }

    /**
     * 📈 Test: Calculs analytics avancés
     */
    public function test_vehicle_analytics_calculations(): void
    {
        $vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status_id' => $this->vehicleStatus->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'acquisition_date' => now()->subYears(5),
            'purchase_price' => 30000,
            'current_value' => 18000,
            'current_mileage' => 80000
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.vehicles.show', $vehicle));

        $response->assertOk();
        $analytics = $response->viewData('analytics');

        $this->assertIsArray($analytics);
        $this->assertArrayHasKey('age_years', $analytics);
        $this->assertArrayHasKey('depreciation_rate', $analytics);
        $this->assertArrayHasKey('utilization_rate', $analytics);
    }

    /**
     * 🛡️ Test: Validation métier enterprise
     */
    public function test_business_rule_validations(): void
    {
        $invalidBusinessData = [
            'registration_plate' => 'TEST-123',
            'vin' => '1HGBH41JXMN109188',
            'brand' => 'Test',
            'model' => 'Test',
            'color' => 'Test',
            'vehicle_type_id' => $this->vehicleType->id,
            'fuel_type_id' => $this->fuelType->id,
            'transmission_type_id' => $this->transmissionType->id,
            'status_id' => $this->vehicleStatus->id,
            'manufacturing_year' => 2023,
            'acquisition_date' => '2023-01-15',
            'purchase_price' => 25000.00,
            'current_value' => 30000.00, // Plus élevée que le prix d'achat
            'initial_mileage' => 50000,
            'current_mileage' => 25000, // Inférieur au kilométrage initial
            'engine_displacement_cc' => 1600,
            'power_hp' => 120,
            'seats' => 5
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.vehicles.store'), $invalidBusinessData);

        $response->assertSessionHasErrors();
    }
}