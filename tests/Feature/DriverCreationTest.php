<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * 🧪 TEST ENTERPRISE: Driver Creation Flow
 *
 * Teste le workflow complet de création de chauffeur avec user auto-généré
 */
class DriverCreationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Organization $organization;
    private DriverStatus $status;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer une organisation
        $this->organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'status' => 'active',
        ]);

        // Créer un admin
        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'organization_id' => $this->organization->id,
        ]);

        // Créer le statut
        $this->status = DriverStatus::firstOrCreate(
            ['id' => 1],
            ['name' => 'Actif', 'color' => '#10b981', 'can_drive' => true, 'can_assign' => true]
        );

        // Donner les permissions
        $this->admin->givePermissionTo('create drivers');
    }

    /** @test */
    public function it_creates_driver_with_auto_generated_user()
    {
        // Arrange: Données du formulaire
        $driverData = [
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'birth_date' => '1990-01-15',
            'personal_phone' => '+213555123456',
            'personal_email' => 'ahmed.benali@personal.com',
            'address' => 'Rue de la République, Alger',
            'blood_type' => 'O+',
            'employee_number' => 'EMP-' . rand(1000, 9999),
            'status_id' => $this->status->id,
            'recruitment_date' => now()->subYear()->format('Y-m-d'),
            'license_number' => 'LIC-' . rand(100000, 999999),
            'license_category' => 'B, C',
            'license_issue_date' => now()->subYears(5)->format('Y-m-d'),
            'license_authority' => 'Préfecture d\'Alger',
            'emergency_contact_name' => 'Fatima Benali',
            'emergency_contact_phone' => '+213555654321',
        ];

        // Act: Soumettre le formulaire
        $response = $this->actingAs($this->admin)
            ->post(route('admin.drivers.store'), $driverData);

        // Assert: Vérifier la redirection
        $response->assertRedirect(route('admin.drivers.create'));
        $response->assertSessionHas('driver_success');

        // Vérifier que le driver a été créé
        $this->assertDatabaseHas('drivers', [
            'first_name' => 'Ahmed',
            'last_name' => 'Benali',
            'organization_id' => $this->organization->id,
        ]);

        // Vérifier que l'user a été créé
        $driver = Driver::where('first_name', 'Ahmed')
            ->where('last_name', 'Benali')
            ->first();

        $this->assertNotNull($driver);
        $this->assertNotNull($driver->user_id);

        $user = User::find($driver->user_id);
        $this->assertNotNull($user);
        $this->assertStringContainsString('ahmed', strtolower($user->email));
        $this->assertStringContainsString('benali', strtolower($user->email));
        $this->assertStringContainsString('@zenfleet.dz', $user->email);

        // Vérifier les données de session
        $sessionData = session('driver_success');
        $this->assertNotNull($sessionData);
        $this->assertTrue($sessionData['driver_created']);
        $this->assertTrue($sessionData['user_was_created']);
        $this->assertNotNull($sessionData['user_password']);
        $this->assertStringStartsWith('Chauffeur@2025', $sessionData['user_password']);

        dump([
            '✅ Driver created' => $driver->id,
            '✅ User created' => $user->id,
            '✅ Email' => $user->email,
            '✅ Password' => $sessionData['user_password'],
            '✅ Session data' => $sessionData,
        ]);
    }

    /** @test */
    public function it_creates_driver_with_existing_user()
    {
        // Arrange: Créer un user existant
        $existingUser = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'existing@zenfleet.dz',
            'organization_id' => $this->organization->id,
        ]);

        $driverData = [
            'first_name' => 'Mohamed',
            'last_name' => 'Alaoui',
            'status_id' => $this->status->id,
            'user_id' => $existingUser->id,
        ];

        // Act
        $response = $this->actingAs($this->admin)
            ->post(route('admin.drivers.store'), $driverData);

        // Assert
        $response->assertRedirect(route('admin.drivers.create'));
        $response->assertSessionHas('driver_success');

        $driver = Driver::where('first_name', 'Mohamed')->first();
        $this->assertNotNull($driver);
        $this->assertEquals($existingUser->id, $driver->user_id);

        $sessionData = session('driver_success');
        $this->assertFalse($sessionData['user_was_created']);
        $this->assertNull($sessionData['user_password']);

        dump([
            '✅ Driver created' => $driver->id,
            '✅ User associated' => $existingUser->id,
            '✅ User was NOT created' => !$sessionData['user_was_created'],
        ]);
    }
}
