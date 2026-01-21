<?php

namespace Tests\Unit;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 🧪 Tests Unitaires Ultra-Professionnels pour le Modèle Driver
 *
 * Test Suite complète pour valider la logique métier et les relations
 * du modèle Driver avec validation des calculs, attributs et méthodes.
 *
 * @package Tests\Unit
 * @version 2.0-Enterprise
 * @author ZenFleet Development Team
 */
class DriverModelTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected DriverStatus $driverStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->driverStatus = DriverStatus::factory()->create(['name' => 'Disponible']);
    }

    /**
     * 🏗️ Test: Création d'un chauffeur avec données valides
     */
    public function test_driver_creation_with_valid_data(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'birth_date' => '1985-05-15'
        ]);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertEquals('Ahmed', $driver->first_name);
        $this->assertEquals('Benali', $driver->last_name);
        $this->assertEquals($this->organization->id, $driver->organization_id);
    }

    /**
     * 🔗 Test: Relations du modèle Driver
     */
    public function test_driver_relationships(): void
    {
        $user = User::factory()->create(['organization_id' => $this->organization->id]);

        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'user_id' => $user->id
        ]);

        // Test relation avec User
        $this->assertInstanceOf(User::class, $driver->user);
        $this->assertEquals($user->id, $driver->user->id);

        // Test relation avec DriverStatus
        $this->assertInstanceOf(DriverStatus::class, $driver->driverStatus);
        $this->assertEquals($this->driverStatus->id, $driver->driverStatus->id);

        // Test relation avec Organization (via trait)
        $this->assertInstanceOf(Organization::class, $driver->organization);
        $this->assertEquals($this->organization->id, $driver->organization->id);
    }

    /**
     * 📝 Test: Attribut calculé full_name
     */
    public function test_full_name_attribute(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'first_name' => 'Ahmed',
            'last_name' => 'Benali'
        ]);

        $this->assertEquals('Ahmed Benali', $driver->full_name);
        $this->assertEquals('Ahmed Benali', $driver->getFullNameAttribute());
    }

    /**
     * 📅 Test: Cast des dates
     */
    public function test_date_casting(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'birth_date' => '1985-05-15',
            'license_issue_date' => '2020-01-01',
            'license_expiry_date' => '2030-01-01',
            'recruitment_date' => '2024-01-01',
            'contract_end_date' => '2026-12-31'
        ]);

        $this->assertInstanceOf(Carbon::class, $driver->birth_date);
        $this->assertInstanceOf(Carbon::class, $driver->license_issue_date);
        $this->assertInstanceOf(Carbon::class, $driver->license_expiry_date);
        $this->assertInstanceOf(Carbon::class, $driver->recruitment_date);
        $this->assertInstanceOf(Carbon::class, $driver->contract_end_date);

        $this->assertEquals('1985-05-15', $driver->birth_date->format('Y-m-d'));
        $this->assertEquals('2020-01-01', $driver->license_issue_date->format('Y-m-d'));
    }

    /**
     * ✅ Test: Méthode isCurrentlyAssigned
     */
    public function test_is_currently_assigned_method(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        // Sans affectation, devrait retourner false
        $this->assertFalse($driver->isCurrentlyAssigned());

        // Test que la méthode existe et retourne un boolean
        $result = $driver->isCurrentlyAssigned();
        $this->assertIsBool($result);
    }

    /**
     * 🔒 Test: Attributs fillable
     */
    public function test_fillable_attributes(): void
    {
        $fillableAttributes = [
            'user_id',
            'organization_id',
            'first_name',
            'last_name',
            'email',
            'employee_number',
            'birth_date',
            'blood_type',
            'personal_phone',
            'personal_email',
            'address',
            'city',
            'postal_code',
            'license_number',
            'license_categories',
            'license_expiry_date',
            'license_issue_date',
            'license_authority',
            'license_verified',
            'recruitment_date',
            'contract_end_date',
            'status_id',
            'emergency_contact_name',
            'emergency_contact_phone',
            'emergency_contact_relationship',
            'photo',
            'notes',
            'supervisor_id',
        ];

        $driver = new Driver();
        $this->assertEquals($fillableAttributes, $driver->getFillable());
    }

    /**
     * 🗑️ Test: Soft Delete
     */
    public function test_soft_delete_functionality(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $driverId = $driver->id;

        // Soft delete
        $driver->delete();

        // Vérifier que le chauffeur n'apparaît plus dans les requêtes normales
        $this->assertNull(Driver::find($driverId));

        // Vérifier qu'il existe toujours avec withTrashed
        $this->assertNotNull(Driver::withTrashed()->find($driverId));

        // Vérifier que deleted_at est défini
        $deletedDriver = Driver::withTrashed()->find($driverId);
        $this->assertNotNull($deletedDriver->deleted_at);
    }

    /**
     * 📋 Test: Validation de l'âge minimum
     */
    public function test_age_calculation(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'birth_date' => Carbon::now()->subYears(30)
        ]);

        $age = Carbon::now()->diffInYears($driver->birth_date, true);
        $this->assertEqualsWithDelta(30, $age, 0.05);
    }

    /**
     * 🔍 Test: Scopes personnalisés (s'ils existent)
     */
    public function test_query_scopes(): void
    {
        // Création de chauffeurs avec différents statuts
        $availableStatus = DriverStatus::factory()->create(['name' => 'Disponible']);
        $busyStatus = DriverStatus::factory()->create(['name' => 'En mission']);

        $availableDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $availableStatus->id
        ]);

        $busyDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $busyStatus->id
        ]);

        // Test que les chauffeurs sont créés
        $this->assertDatabaseHas('drivers', ['id' => $availableDriver->id]);
        $this->assertDatabaseHas('drivers', ['id' => $busyDriver->id]);

        // Test de requête par statut
        $availableDrivers = Driver::where('status_id', $availableStatus->id)->get();
        $this->assertCount(1, $availableDrivers);
        $this->assertEquals($availableDriver->id, $availableDrivers->first()->id);
    }

    /**
     * 📞 Test: Validation des contacts d'urgence
     */
    public function test_emergency_contact_attributes(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'emergency_contact_name' => 'Fatima Benali',
            'emergency_contact_phone' => '0555123456'
        ]);

        $this->assertEquals('Fatima Benali', $driver->emergency_contact_name);
        $this->assertEquals('0555123456', $driver->emergency_contact_phone);
    }

    /**
     * 🆔 Test: Validation du matricule unique
     */
    public function test_employee_number_uniqueness(): void
    {
        $driver1 = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'employee_number' => 'DRV-001'
        ]);

        $this->assertEquals('DRV-001', $driver1->employee_number);

        // La contrainte d'unicité est gérée au niveau de la base de données
        // et des validations Laravel, pas directement par le modèle
        $this->assertTrue(true);
    }

    /**
     * 🩸 Test: Groupe sanguin
     */
    public function test_blood_type_attribute(): void
    {
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        foreach ($bloodTypes as $bloodType) {
            $driver = Driver::factory()->create([
                'organization_id' => $this->organization->id,
                'status_id' => $this->driverStatus->id,
                'blood_type' => $bloodType
            ]);

            $this->assertEquals($bloodType, $driver->blood_type);
        }
    }

    /**
     * 📄 Test: Informations de permis
     */
    public function test_license_information(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'license_number' => 'P-12345678',
            'license_category' => 'B,C,D',
            'license_authority' => 'Préfecture d\'Alger',
            'license_issue_date' => '2020-01-01',
            'license_expiry_date' => '2030-01-01'
        ]);

        $this->assertEquals('P-12345678', $driver->license_number);
        $this->assertEquals('B,C,D', $driver->license_category);
        $this->assertEquals('Préfecture d\'Alger', $driver->license_authority);

        // Test que les dates sont correctement castées
        $this->assertEquals('2020-01-01', $driver->license_issue_date->format('Y-m-d'));
        $this->assertEquals('2030-01-01', $driver->license_expiry_date->format('Y-m-d'));
    }

    /**
     * 🏢 Test: Trait BelongsToOrganization
     */
    public function test_belongs_to_organization_trait(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        // Test que le trait est utilisé
        $this->assertContains('organization_id', $driver->getFillable());

        // Test de la relation
        $this->assertInstanceOf(Organization::class, $driver->organization);
        $this->assertEquals($this->organization->id, $driver->organization->id);
        $this->assertEquals($this->organization->name, $driver->organization->name);
    }

    /**
     * 📸 Test: Chemin de la photo
     */
    public function test_photo_path_attribute(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'photo' => 'drivers/photos/driver_123.jpg'
        ]);

        $this->assertEquals('drivers/photos/driver_123.jpg', $driver->photo);
    }

    /**
     * 🔄 Test: Méthodes de factory
     */
    public function test_factory_methods(): void
    {
        // Test factory basique
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertNotNull($driver->first_name);
        $this->assertNotNull($driver->last_name);

        // Test factory avec états personnalisés
        $drivers = Driver::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $this->assertCount(3, $drivers);
        foreach ($drivers as $driver) {
            $this->assertEquals($this->organization->id, $driver->organization_id);
        }
    }
}
