<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_has_fillable_attributes(): void
    {
        $fillable = [
            'uuid', 'name', 'legal_name', 'organization_type', 'industry',
            'description', 'website', 'phone_number', 'email', 'logo_path',
            'status', 'trade_register', 'nif', 'ai', 'nis',
            'address', 'city', 'commune', 'zip_code', 'wilaya',
            'scan_nif_path', 'scan_ai_path', 'scan_nis_path', 'manager_first_name',
            'manager_last_name', 'manager_nin', 'manager_address',
            'manager_dob', 'manager_pob', 'manager_phone_number',
            'manager_id_scan_path',
        ];

        $organization = new Organization;

        $this->assertEquals($fillable, $organization->getFillable());
    }

    public function test_organization_can_be_created_with_minimum_required_fields(): void
    {
        $organizationData = [
            'name' => 'Test Organization',
            'email' => 'test@example.dz',
            'phone_number' => '+213123456789',
            'trade_register' => '16/23-1234567 B 01',
            'nif' => '123456789012345',
            'address' => '123 Test Street',
            'city' => 'Alger',
            'wilaya' => '16',
            'manager_first_name' => 'John',
            'manager_last_name' => 'Doe',
            'manager_nin' => '123456789012345678',
            'status' => 'active',
        ];

        $organization = Organization::create($organizationData);

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertEquals('Test Organization', $organization->name);
        $this->assertEquals('test@example.dz', $organization->email);
        $this->assertEquals('active', $organization->status);
    }

    public function test_organization_casts_manager_dob_to_date(): void
    {
        $organization = Organization::factory()->create([
            'manager_dob' => '1980-01-01',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $organization->manager_dob);
        $this->assertEquals('1980-01-01', $organization->manager_dob->format('Y-m-d'));
    }

    public function test_organization_uses_soft_deletes(): void
    {
        $organization = Organization::factory()->create();

        $organization->delete();

        $this->assertSoftDeleted($organization);
        $this->assertNotNull($organization->deleted_at);
    }

    public function test_organization_has_uuid_cast(): void
    {
        $organization = Organization::factory()->create();

        $this->assertIsString($organization->uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $organization->uuid
        );
    }

    public function test_organization_can_have_users_relationship(): void
    {
        $organization = Organization::factory()->create();

        // This test assumes that the User model has an organization_id field
        // and a relationship with Organization
        $this->assertTrue(method_exists($organization, 'users'));
    }

    public function test_organization_has_proper_table_name(): void
    {
        $organization = new Organization;

        $this->assertEquals('organizations', $organization->getTable());
    }

    public function test_organization_validation_accepts_valid_organization_types(): void
    {
        $validTypes = ['enterprise', 'sme', 'startup', 'public'];

        foreach ($validTypes as $type) {
            $organization = Organization::factory()->create([
                'organization_type' => $type,
            ]);

            $this->assertEquals($type, $organization->organization_type);
        }
    }

    public function test_organization_can_be_created_with_algerian_wilaya(): void
    {
        $organization = Organization::factory()->create([
            'wilaya' => '16', // Alger
            'city' => 'Alger',
        ]);

        $this->assertEquals('16', $organization->wilaya);
        $this->assertEquals('Alger', $organization->city);
    }

    public function test_organization_factory_generates_valid_algerian_data(): void
    {
        $organization = Organization::factory()->create();

        // Test Algerian-specific fields
        $this->assertNotNull($organization->nif);
        $this->assertEquals(15, strlen($organization->nif));

        $this->assertNotNull($organization->trade_register);
        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}-\d{6} [AB] \d{2}$/', $organization->trade_register);

        $this->assertNotNull($organization->wilaya);
        $this->assertMatchesRegularExpression('/^\d{2}$/', $organization->wilaya);

        $this->assertNotNull($organization->manager_nin);
        $this->assertEquals(18, strlen($organization->manager_nin));
    }

    public function test_organization_active_status_scope(): void
    {
        Organization::factory()->create(['status' => 'active']);
        Organization::factory()->create(['status' => 'inactive']);
        Organization::factory()->create(['status' => 'suspended']);

        // If there's an active scope method
        if (method_exists(Organization::class, 'scopeActive')) {
            $activeOrganizations = Organization::active()->get();
            $this->assertCount(1, $activeOrganizations);
            $this->assertEquals('active', $activeOrganizations->first()->status);
        }
    }

    public function test_organization_has_proper_model_structure(): void
    {
        $organization = new Organization;

        // Test that the model uses the correct traits
        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', class_uses($organization));
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($organization));
    }
}
