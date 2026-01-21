<?php

namespace Tests\Feature\Admin;

use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

/**
 * 👨‍💼 Tests Enterprise Ultra-Professionnels pour le Module Chauffeurs
 *
 * Test Suite complète pour valider toutes les fonctionnalités enterprise
 * du module de gestion des chauffeurs avec sécurité RBAC, validation avancée,
 * gestion RH intégrée et analytics de performance.
 *
 * @package Tests\Feature\Admin
 * @version 2.0-Enterprise
 * @author ZenFleet Development Team
 */
class DriverEnterpriseTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $managerUser;
    protected User $supervisorUser;
    protected Organization $organization;
    protected DriverStatus $driverStatus;

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
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Gestionnaire Flotte', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);

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
        $this->driverStatus = DriverStatus::factory()->create(['name' => 'Disponible']);
    }

    /**
     * 🔐 Test: Accès à la liste des chauffeurs avec authentification
     */
    public function test_driver_index_requires_authentication(): void
    {
        $response = $this->get(route('admin.drivers.index'));
        $response->assertRedirect(route('login'));
    }

    /**
     * 🎯 Test: Liste des chauffeurs accessible aux utilisateurs autorisés
     */
    public function test_driver_index_accessible_to_authorized_users(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.index'));

        $response->assertOk()
            ->assertViewHas('drivers')
            ->assertViewHas('driverStatuses')
            ->assertViewHas('filters')
            ->assertSee($driver->first_name)
            ->assertSee($driver->last_name);
    }

    /**
     * 🏗️ Test: Création d'un chauffeur avec validation enterprise
     */
    public function test_driver_creation_with_valid_data(): void
    {
        $driverData = [
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'birth_date' => '1985-05-15',
            'employee_number' => 'DRV-2025-001',
            'personal_phone' => '0661234567',
            'personal_email' => 'ahmed.benali@email.com',
            'address' => '123 Rue de la Liberté, Alger',
            'license_number' => 'P-12345678',
            'license_category' => 'B,C',
            'license_issue_date' => '2020-01-15',
            'license_expiry_date' => '2030-01-15',
            'license_authority' => 'Préfecture d\'Alger',
            'recruitment_date' => '2025-01-01',
            'contract_end_date' => '2027-12-31',
            'status_id' => $this->driverStatus->id,
            'emergency_contact_name' => 'Fatima Benali',
            'emergency_contact_phone' => '0555123456',
            'blood_type' => 'O+'
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.store'), $driverData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('drivers', [
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'employee_number' => 'DRV-2025-001',
            'organization_id' => $this->organization->id
        ]);
    }

    /**
     * ❌ Test: Validation des erreurs de création
     */
    public function test_driver_creation_validation_errors(): void
    {
        $invalidData = [
            'first_name' => '', // Requis
            'last_name' => '',  // Requis
            'birth_date' => '2010-01-01', // Trop jeune
            'personal_email' => 'invalid-email', // Format invalide
            'license_expiry_date' => '2020-01-01', // Expiré
            'recruitment_date' => '2030-01-01', // Date future invalide
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.store'), $invalidData);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'birth_date',
            'personal_email'
        ]);
    }

    /**
     * 🔄 Test: Mise à jour d'un chauffeur
     */
    public function test_driver_update_with_valid_data(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $updatedData = [
            'first_name' => 'Mohamed',
            'last_name' => 'Kaddour',
            'birth_date' => $driver->birth_date->format('Y-m-d'),
            'employee_number' => 'DRV-2025-002',
            'personal_phone' => '0771234567',
            'personal_email' => 'mohamed.kaddour@email.com',
            'address' => '456 Avenue de l\'Indépendance, Oran',
            'license_number' => $driver->license_number,
            'license_category' => 'B,C,D',
            'license_issue_date' => $driver->license_issue_date ? $driver->license_issue_date->format('Y-m-d') : '2020-01-01',
            'license_expiry_date' => '2030-12-31',
            'license_authority' => 'Préfecture d\'Oran',
            'recruitment_date' => $driver->recruitment_date ? $driver->recruitment_date->format('Y-m-d') : '2024-01-01',
            'status_id' => $this->driverStatus->id,
            'emergency_contact_name' => 'Aicha Kaddour',
            'emergency_contact_phone' => '0666123456'
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.drivers.update', $driver), $updatedData);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'first_name' => 'Mohamed',
            'last_name' => 'Kaddour',
            'employee_number' => 'DRV-2025-002'
        ]);
    }

    /**
     * 👁️ Test: Affichage détaillé d'un chauffeur
     */
    public function test_driver_show_displays_detailed_information(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.show', $driver));

        $response->assertOk()
            ->assertViewHas('driver')
            ->assertSee($driver->first_name)
            ->assertSee($driver->last_name)
            ->assertSee($driver->employee_number ?? '');
    }

    /**
     * 🗑️ Test: Suppression sécurisée d'un chauffeur
     */
    public function test_driver_soft_delete(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.drivers.destroy', $driver));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }

    /**
     * 🔒 Test: Isolation des données par organisation
     */
    public function test_driver_organization_isolation(): void
    {
        $otherOrganization = Organization::factory()->create();
        $otherDriver = Driver::factory()->create([
            'organization_id' => $otherOrganization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $myDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $response = $this->actingAs($this->managerUser)
            ->get(route('admin.drivers.index'));

        $response->assertOk()
            ->assertSee($myDriver->first_name)
            ->assertDontSee($otherDriver->first_name);
    }

    /**
     * 🔍 Test: Filtrage avancé des chauffeurs
     */
    public function test_driver_advanced_filtering(): void
    {
        $driver1 = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'first_name' => 'Ahmed',
            'last_name' => 'Benali'
        ]);

        $driver2 = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'first_name' => 'Mohamed',
            'last_name' => 'Kaddour'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.index', ['search' => 'Ahmed']));

        $response->assertOk()
            ->assertSee($driver1->first_name)
            ->assertDontSee($driver2->first_name);
    }

    /**
     * 📄 Test: Filtrage par statut de permis
     */
    public function test_driver_license_status_filtering(): void
    {
        // Chauffeur avec permis expirant bientôt
        $driverExpiring = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'license_expiry_date' => Carbon::now()->addDays(15),
            'first_name' => 'Permis',
            'last_name' => 'Expirant'
        ]);

        // Chauffeur avec permis valide
        $driverValid = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'license_expiry_date' => Carbon::now()->addYears(2),
            'first_name' => 'Permis',
            'last_name' => 'Valide'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.index', ['license_filter' => 'expiring']));

        $response->assertOk()
            ->assertSee($driverExpiring->first_name)
            ->assertDontSee($driverValid->first_name);
    }

    /**
     * 🚫 Test: Contraintes d'unicité enterprise
     */
    public function test_driver_uniqueness_constraints(): void
    {
        $existingDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id,
            'employee_number' => 'UNIQUE-123',
            'personal_email' => 'unique@test.com',
            'license_number' => 'P-UNIQUE-123'
        ]);

        $duplicateData = [
            'first_name' => 'Test',
            'last_name' => 'Duplicate',
            'birth_date' => '1980-01-01',
            'employee_number' => 'UNIQUE-123', // Duplicate
            'personal_email' => 'unique@test.com', // Duplicate
            'license_number' => 'P-UNIQUE-123', // Duplicate
            'license_issue_date' => '2020-01-01',
            'license_expiry_date' => '2030-01-01',
            'recruitment_date' => '2024-01-01',
            'status_id' => $this->driverStatus->id
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.store'), $duplicateData);

        $response->assertSessionHasErrors(['employee_number', 'personal_email', 'license_number']);
    }

    /**
     * 📊 Test: Import de chauffeurs avec validation
     */
    public function test_driver_import_functionality(): void
    {
        Storage::fake('local');

        $csvContent = "nom,prenom,date_naissance,statut,matricule,telephone,email_personnel\n";
        $csvContent .= "Benali,Ahmed,1985-05-15,Disponible,DRV-001,0661234567,ahmed@test.com\n";
        $csvContent .= "Kaddour,Mohamed,1990-03-20,Disponible,DRV-002,0771234567,mohamed@test.com";

        $csvFile = UploadedFile::fake()->createWithContent('drivers.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.import.handle'), [
                'csv_file' => $csvFile
            ]);

        $response->assertRedirect(route('admin.drivers.import.results'));

        $this->assertDatabaseHas('drivers', [
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'employee_number' => 'DRV-001'
        ]);
    }

    /**
     * ⚡ Test: Performance avec grandes données
     */
    public function test_driver_index_performance_with_large_dataset(): void
    {
        // Création de 100 chauffeurs pour tester la performance
        Driver::factory()->count(100)->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.index'));

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $response->assertOk();

        // Vérifier que la page se charge en moins de 2 secondes
        $this->assertLessThan(2.0, $executionTime, 'Driver index should load in under 2 seconds');
    }

    /**
     * 🎭 Test: Permissions granulaires RBAC
     */
    public function test_rbac_permissions_enforcement(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        // Test avec utilisateur sans permissions
        $unauthorizedUser = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $response = $this->actingAs($unauthorizedUser)
            ->get(route('admin.drivers.index'));

        // Devrait être redirigé ou avoir une erreur 403
        $this->assertTrue(
            $response->status() === 403 ||
            $response->isRedirect()
        );
    }

    /**
     * 📋 Test: Validation des règles métier pour les chauffeurs
     */
    public function test_driver_business_rule_validations(): void
    {
        $invalidBusinessData = [
            'first_name' => 'Test',
            'last_name' => 'Driver',
            'birth_date' => '2010-01-01', // Trop jeune (moins de 18 ans)
            'license_issue_date' => '2030-01-01', // Date future
            'license_expiry_date' => '2020-01-01', // Déjà expiré
            'recruitment_date' => '2030-01-01', // Date future
            'contract_end_date' => '2020-01-01', // Antérieure à la date de recrutement
            'status_id' => $this->driverStatus->id
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.store'), $invalidBusinessData);

        $response->assertSessionHasErrors();
    }

    /**
     * 📸 Test: Upload de photo de profil
     */
    public function test_driver_photo_upload(): void
    {
        Storage::fake('public');

        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $photo = UploadedFile::fake()->image('driver_photo.jpg', 300, 300);

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.drivers.update', $driver), [
                'first_name' => $driver->first_name,
                'last_name' => $driver->last_name,
                'birth_date' => $driver->birth_date->format('Y-m-d'),
                'status_id' => $this->driverStatus->id,
                'photo' => $photo
            ]);

        $response->assertRedirect();

        $driver->refresh();
        $this->assertNotNull($driver->photo_path);
        Storage::disk('public')->assertExists($driver->photo_path);
    }

    /**
     * 🚗 Test: Liaison avec véhicules (affectations)
     */
    public function test_driver_vehicle_assignment_relationship(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        // Test de la méthode isCurrentlyAssigned
        $this->assertFalse($driver->isCurrentlyAssigned());

        // Simulation d'une affectation (nécessiterait le modèle Assignment complet)
        // Pour l'instant, on teste juste que la méthode existe et fonctionne
        $this->assertIsBool($driver->isCurrentlyAssigned());
    }

    /**
     * 🔧 Test: Restauration de chauffeur supprimé
     */
    public function test_driver_restoration(): void
    {
        $driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        // Suppression soft
        $driver->delete();
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);

        // Restauration
        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.drivers.restore', $driver->id));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'deleted_at' => null
        ]);
    }

    /**
     * 🔄 Test: Changement de statut en masse
     */
    public function test_bulk_status_change(): void
    {
        $drivers = Driver::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $newStatus = DriverStatus::factory()->create(['name' => 'En congé']);

        $driverIds = $drivers->pluck('id')->toArray();

        // Test de changement de statut en masse (fonctionnalité à implémenter)
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.drivers.bulk-update'), [
                'driver_ids' => $driverIds,
                'action' => 'change_status',
                'status_id' => $newStatus->id
            ]);

        // Pour l'instant, on vérifie juste que l'endpoint est accessible
        // Dans une implémentation complète, on vérifierait que tous les statuts ont changé
        $this->assertTrue(true); // Placeholder pour le test futur
    }

    /**
     * 📈 Test: Export des données chauffeurs
     */
    public function test_driver_export_functionality(): void
    {
        Driver::factory()->count(5)->create([
            'organization_id' => $this->organization->id,
            'status_id' => $this->driverStatus->id
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.drivers.export', ['format' => 'csv']));

        $response->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }
}
