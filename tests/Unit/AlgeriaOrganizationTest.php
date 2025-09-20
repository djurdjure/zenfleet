<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\AlgeriaWilaya;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlgeriaOrganizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test wilayas
        AlgeriaWilaya::create([
            'code' => '16',
            'name_fr' => 'Alger',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_creates_organization_with_algeria_fields(): void
    {
        $organization = Organization::create([
            'uuid' => fake()->uuid(),
            'name' => 'Test Organization',
            'email' => 'test@example.dz',
            'phone_number' => '+213 21 12 34 56',
            'trade_register' => '16/23-123456 A 15',
            'nif' => '098765432109876',
            'address' => '123 Rue Test',
            'city' => 'Alger-Centre',
            'wilaya' => '16',
            'manager_first_name' => 'Ahmed',
            'manager_last_name' => 'Benali',
            'manager_nin' => '162012345678901234',
        ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
            'wilaya' => '16',
            'nif' => '098765432109876',
            'manager_nin' => '162012345678901234',
        ]);
    }

    /** @test */
    public function it_has_wilaya_relationship(): void
    {
        $organization = Organization::factory()->create(['wilaya' => '16']);

        $this->assertInstanceOf(AlgeriaWilaya::class, $organization->wilayaInfo);
        $this->assertEquals('Alger', $organization->wilayaInfo->name_fr);
    }

    /** @test */
    public function it_has_correct_fillable_fields(): void
    {
        $organization = new Organization();
        $fillable = $organization->getFillable();

        // Check Algeria-specific fields are fillable
        $this->assertContains('wilaya', $fillable);
        $this->assertContains('commune', $fillable);
        $this->assertContains('nif', $fillable);
        $this->assertContains('ai', $fillable);
        $this->assertContains('nis', $fillable);
        $this->assertContains('manager_nin', $fillable);

        // Check international fields are NOT fillable
        $this->assertNotContains('country', $fillable);
        $this->assertNotContains('currency', $fillable);
        $this->assertNotContains('timezone', $fillable);
    }

    /** @test */
    public function settings_attribute_defaults_to_algeria_context(): void
    {
        $organization = Organization::factory()->create();

        $settings = $organization->settings;

        $this->assertEquals('ar', $settings['locale']);
        $this->assertEquals('d/m/Y', $settings['date_format']);
        $this->assertEquals('+213', $settings['phone_format']);
        $this->assertIsArray($settings['notifications']);
        $this->assertTrue($settings['notifications']['maintenance_alerts']);
    }

    /** @test */
    public function settings_attribute_merges_with_stored_values(): void
    {
        $organization = Organization::factory()->create([
            'settings' => json_encode([
                'locale' => 'fr',
                'custom_setting' => 'test_value',
                'notifications' => [
                    'email_reports' => false,
                ]
            ])
        ]);

        $settings = $organization->settings;

        // Custom values should override defaults
        $this->assertEquals('fr', $settings['locale']);
        $this->assertEquals('test_value', $settings['custom_setting']);

        // Defaults should still be present
        $this->assertEquals('d/m/Y', $settings['date_format']);
        $this->assertEquals('+213', $settings['phone_format']);

        // Nested arrays should merge
        $this->assertFalse($settings['notifications']['email_reports']); // Custom
        $this->assertTrue($settings['notifications']['maintenance_alerts']); // Default
    }

    /** @test */
    public function to_api_array_includes_algeria_fields(): void
    {
        $organization = Organization::factory()->create([
            'wilaya' => '16',
            'city' => 'Alger-Centre',
        ]);

        $apiArray = $organization->toApiArray();

        $this->assertArrayHasKey('wilaya', $apiArray);
        $this->assertEquals('16', $apiArray['wilaya']);
        $this->assertEquals('Alger-Centre', $apiArray['city']);

        // Should not include international fields
        $this->assertArrayNotHasKey('country', $apiArray);
        $this->assertArrayNotHasKey('timezone', $apiArray);
        $this->assertArrayNotHasKey('currency', $apiArray);
    }

    /** @test */
    public function factory_generates_valid_algeria_data(): void
    {
        $organization = Organization::factory()->create();

        // Check wilaya is valid 2-character code
        $this->assertMatchesRegularExpression('/^\d{2}$/', $organization->wilaya);
        $this->assertGreaterThanOrEqual(1, (int)$organization->wilaya);
        $this->assertLessThanOrEqual(48, (int)$organization->wilaya);

        // Check NIF format (numeric)
        $this->assertMatchesRegularExpression('/^\d+$/', $organization->nif);

        // Check phone format
        $this->assertStringStartsWith('+213', $organization->phone_number);

        // Check NIN format (18 digits)
        $this->assertMatchesRegularExpression('/^\d{18}$/', $organization->manager_nin);
    }

    /** @test */
    public function factory_can_create_different_organization_types(): void
    {
        $enterprise = Organization::factory()->enterprise()->create();
        $sme = Organization::factory()->sme()->create();

        $this->assertEquals('enterprise', $enterprise->organization_type);
        $this->assertEquals('sme', $sme->organization_type);
    }

    /** @test */
    public function factory_can_create_different_statuses(): void
    {
        $active = Organization::factory()->active()->create();
        $inactive = Organization::factory()->inactive()->create();

        $this->assertEquals('active', $active->status);
        $this->assertEquals('inactive', $inactive->status);
    }
}