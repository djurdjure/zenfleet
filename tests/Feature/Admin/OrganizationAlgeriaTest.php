<?php

namespace Tests\Feature\Admin;

use App\Models\Organization;
use App\Models\User;
use App\Models\AlgeriaWilaya;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrganizationAlgeriaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        // Seed Algeria wilayas
        $this->artisan('migrate');
        AlgeriaWilaya::create([
            'code' => '16',
            'name_fr' => 'Alger',
            'is_active' => true,
        ]);
        AlgeriaWilaya::create([
            'code' => '31',
            'name_fr' => 'Oran',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_organization_create_form_with_algeria_wilayas(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizations.create'));

        $response->assertOk()
            ->assertViewIs('admin.organizations.create')
            ->assertViewHas('wilayas')
            ->assertViewHas('organizationTypes')
            ->assertSee('Nouvelle Organisation')
            ->assertSee('16 - Alger')
            ->assertSee('31 - Oran');
    }

    /** @test */
    public function admin_can_create_organization_with_valid_algeria_data(): void
    {
        Storage::fake('public');

        $organizationData = [
            'name' => 'Test Transport Algeria',
            'legal_name' => 'Test Transport Algeria SARL',
            'organization_type' => 'enterprise',
            'industry' => 'Transport',
            'description' => 'Une entreprise de transport en Algérie',
            'email' => 'contact@testtransport.dz',
            'phone_number' => '+213 21 12 34 56',
            'website' => 'https://testtransport.dz',
            'status' => 'active',

            // Legal information
            'trade_register' => '16/23-123456 A 15',
            'nif' => '098765432109876',
            'ai' => '01234567890123',
            'nis' => '123456789012345',

            // Address
            'address' => '123 Rue de la Paix',
            'city' => 'Alger-Centre',
            'commune' => 'Centre',
            'zip_code' => '16000',
            'wilaya' => '16',

            // Legal representative
            'manager_first_name' => 'Ahmed',
            'manager_last_name' => 'Benali',
            'manager_nin' => '162012345678901234',
            'manager_address' => '456 Rue des Martyrs',
            'manager_dob' => '1975-03-15',
            'manager_pob' => 'Alger',
            'manager_phone_number' => '+213 21 98 76 54',

            // Files
            'scan_nif' => UploadedFile::fake()->create('nif.pdf', 100),
            'logo' => UploadedFile::fake()->image('logo.png', 200, 200),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertRedirect();

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Transport Algeria',
            'email' => 'contact@testtransport.dz',
            'nif' => '098765432109876',
            'wilaya' => '16',
            'manager_nin' => '162012345678901234',
        ]);

        // Check files were uploaded
        $organization = Organization::where('email', 'contact@testtransport.dz')->first();
        $this->assertNotNull($organization->scan_nif_path);
        $this->assertNotNull($organization->logo_path);
        Storage::disk('public')->assertExists($organization->scan_nif_path);
        Storage::disk('public')->assertExists($organization->logo_path);
    }

    /** @test */
    public function organization_creation_fails_with_invalid_wilaya(): void
    {
        $organizationData = [
            'name' => 'Test Transport',
            'email' => 'test@example.dz',
            'phone_number' => '+213 21 12 34 56',
            'trade_register' => '16/23-123456 A 15',
            'nif' => '098765432109876',
            'address' => '123 Rue Test',
            'city' => 'Test City',
            'wilaya' => '99', // Invalid wilaya
            'manager_first_name' => 'Ahmed',
            'manager_last_name' => 'Test',
            'manager_nin' => '162012345678901234',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertSessionHasErrors(['wilaya']);
        $this->assertDatabaseMissing('organizations', ['name' => 'Test Transport']);
    }

    /** @test */
    public function organization_creation_validates_algeria_phone_format(): void
    {
        $invalidPhones = [
            '0123456789',      // Too short
            '+33 12 34 56 78', // Wrong country code
            'invalid-phone',    // Non-numeric
            '+213 1234',       // Too short
        ];

        foreach ($invalidPhones as $phone) {
            $organizationData = [
                'name' => 'Test Transport ' . $phone,
                'email' => 'test' . rand(1000, 9999) . '@example.dz',
                'phone_number' => $phone,
                'trade_register' => '16/23-123456 A 15',
                'nif' => '098765432109876',
                'address' => '123 Rue Test',
                'city' => 'Test City',
                'wilaya' => '16',
                'manager_first_name' => 'Ahmed',
                'manager_last_name' => 'Test',
                'manager_nin' => '162012345678901234',
            ];

            $response = $this->actingAs($this->admin)
                ->post(route('admin.organizations.store'), $organizationData);

            $response->assertSessionHasErrors(['phone_number']);
        }
    }

    /** @test */
    public function organization_creation_validates_manager_nin_format(): void
    {
        $invalidNins = [
            '12345',               // Too short
            '1234567890123456789', // Too long
            'abcdefghijklmnopqr',  // Non-numeric
            '1234567890123456A',   // Contains letter
        ];

        foreach ($invalidNins as $nin) {
            $organizationData = [
                'name' => 'Test Transport ' . $nin,
                'email' => 'test' . rand(1000, 9999) . '@example.dz',
                'phone_number' => '+213 21 12 34 56',
                'trade_register' => '16/23-123456 A 15',
                'nif' => '098765432109876',
                'address' => '123 Rue Test',
                'city' => 'Test City',
                'wilaya' => '16',
                'manager_first_name' => 'Ahmed',
                'manager_last_name' => 'Test',
                'manager_nin' => $nin,
            ];

            $response = $this->actingAs($this->admin)
                ->post(route('admin.organizations.store'), $organizationData);

            $response->assertSessionHasErrors(['manager_nin']);
        }
    }

    /** @test */
    public function organization_normalizes_phone_numbers_on_creation(): void
    {
        $organizationData = [
            'name' => 'Test Transport Normalize',
            'email' => 'normalize@example.dz',
            'phone_number' => '021 12 34 56', // Local format
            'trade_register' => '16/23-123456 A 15',
            'nif' => '098765432109876',
            'address' => '123 Rue Test',
            'city' => 'Test City',
            'wilaya' => '16',
            'manager_first_name' => 'Ahmed',
            'manager_last_name' => 'Test',
            'manager_nin' => '162012345678901234',
            'manager_phone_number' => '0661234567', // Mobile format
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertRedirect();

        $organization = Organization::where('email', 'normalize@example.dz')->first();
        $this->assertEquals('+213 21 12 34 56', $organization->phone_number);
        $this->assertEquals('+213 661234567', $organization->manager_phone_number);
    }

    /** @test */
    public function organization_has_relationship_with_wilaya(): void
    {
        $organization = Organization::factory()->create(['wilaya' => '16']);

        $this->assertInstanceOf(AlgeriaWilaya::class, $organization->wilayaInfo);
        $this->assertEquals('Alger', $organization->wilayaInfo->name_fr);
    }

    /** @test */
    public function organization_can_filter_by_wilaya(): void
    {
        Organization::factory()->create(['wilaya' => '16', 'name' => 'Alger Org']);
        Organization::factory()->create(['wilaya' => '31', 'name' => 'Oran Org']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.organizations.index', ['wilaya' => '16']));

        $response->assertOk()
            ->assertSee('Alger Org')
            ->assertDontSee('Oran Org');
    }

    /** @test */
    public function organization_settings_default_to_algeria_context(): void
    {
        $organization = Organization::factory()->create();

        $settings = $organization->settings;

        $this->assertEquals('ar', $settings['locale']);
        $this->assertEquals('d/m/Y', $settings['date_format']);
        $this->assertEquals('+213', $settings['phone_format']);
        $this->assertTrue($settings['notifications']['maintenance_alerts']);
    }

    /** @test */
    public function organization_to_api_array_includes_wilaya(): void
    {
        $organization = Organization::factory()->create(['wilaya' => '16']);

        $apiArray = $organization->toApiArray();

        $this->assertArrayHasKey('wilaya', $apiArray);
        $this->assertEquals('16', $apiArray['wilaya']);
        $this->assertArrayNotHasKey('country', $apiArray); // No longer country field
    }
}