<?php

namespace Tests\Feature\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Super Admin role if it doesn't exist
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // Create admin user with Super Admin role
        $organization = Organization::factory()->create();
        $this->adminUser = User::factory()->create(['organization_id' => $organization->id]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($organization->id);
        $this->adminUser->assignRole($superAdminRole);
    }

    public function test_admin_can_view_organizations_index(): void
    {
        // Create some test organizations
        Organization::factory()->count(3)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.organizations.index');
    }

    public function test_organizations_index_shows_existing_records(): void
    {
        // Create test organizations with known data
        $org1 = Organization::factory()->create([
            'name' => 'Test Organization 1',
            'email' => 'test1@example.dz',
            'status' => 'active',
        ]);

        $org2 = Organization::factory()->create([
            'name' => 'Test Organization 2',
            'email' => 'test2@example.dz',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Organization 1');
        $response->assertSee('Test Organization 2');
        $response->assertSee('test1@example.dz');
        $response->assertSee('test2@example.dz');
    }

    public function test_admin_can_view_create_organization_form(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.organizations.create');
        $response->assertSee('Créer une Organisation');
    }

    public function test_admin_can_create_organization_with_valid_data(): void
    {
        $organizationData = [
            'name' => 'New Test Organization',
            'legal_name' => 'New Test Organization SAS',
            'organization_type' => 'enterprise',
            'industry' => 'Transport et Logistique',
            'description' => 'Test organization description',
            'website' => 'https://newtest.dz',
            'phone_number' => '+213123456789',
            'email' => 'contact@newtest.dz',
            'status' => 'active',
            'trade_register' => '00/00-1234567 B 16',
            'nif' => '123456789012345',
            'ai' => '12345678901234',
            'nis' => '123456789012345',
            'address' => '123 Test Street',
            'city' => 'Alger',
            'zip_code' => '16000',
            'wilaya' => '16',
            'manager_first_name' => 'John',
            'manager_last_name' => 'Doe',
            'manager_nin' => '123456789012345678',
            'manager_address' => '456 Manager Street',
            'manager_dob' => '1980-01-01',
            'manager_pob' => 'Alger',
            'manager_phone_number' => '+213987654321',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organizations', [
            'name' => 'New Test Organization',
            'email' => 'contact@newtest.dz',
            'organization_type' => 'enterprise',
        ]);
    }

    public function test_organization_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.organizations.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone_number',
            'trade_register',
            'nif',
            'address',
            'city',
            'wilaya',
            'manager_first_name',
            'manager_last_name',
            'manager_nin',
        ]);
    }

    public function test_organization_creation_validates_unique_email(): void
    {
        $existingOrg = Organization::factory()->create([
            'email' => 'existing@test.dz',
        ]);

        $organizationData = [
            'name' => 'New Organization',
            'email' => 'existing@test.dz', // Duplicate email
            'phone_number' => '+213123456789',
            'trade_register' => '00/00-1234567 B 16',
            'nif' => '123456789012345',
            'address' => '123 Test Street',
            'city' => 'Alger',
            'wilaya' => '16',
            'manager_first_name' => 'John',
            'manager_last_name' => 'Doe',
            'manager_nin' => '123456789012345678',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_organization_creation_validates_organization_type(): void
    {
        $organizationData = [
            'name' => 'Test Organization',
            'email' => 'test@example.dz',
            'organization_type' => 'invalid_type',
            'phone_number' => '+213123456789',
            'trade_register' => '00/00-1234567 B 16',
            'nif' => '123456789012345',
            'address' => '123 Test Street',
            'city' => 'Alger',
            'wilaya' => '16',
            'manager_first_name' => 'John',
            'manager_last_name' => 'Doe',
            'manager_nin' => '123456789012345678',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.organizations.store'), $organizationData);

        $response->assertSessionHasErrors(['organization_type']);
    }

    public function test_admin_can_view_edit_organization_form(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.edit', $organization));

        $response->assertStatus(200);
        $response->assertViewIs('admin.organizations.edit');
        $response->assertSee('Modifier');
        $response->assertSee($organization->name);
    }

    public function test_admin_can_update_organization(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Original Name',
            'industry' => 'Original Industry',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'legal_name' => $organization->legal_name,
            'organization_type' => 'sme',
            'industry' => 'Updated Industry',
            'description' => 'Updated description',
            'website' => $organization->website,
            'phone_number' => $organization->phone_number,
            'email' => $organization->email,
            'status' => 'active',
            'trade_register' => $organization->trade_register,
            'nif' => $organization->nif,
            'address' => $organization->address,
            'city' => $organization->city,
            'wilaya' => $organization->wilaya,
            'manager_first_name' => $organization->manager_first_name,
            'manager_last_name' => $organization->manager_last_name,
            'manager_nin' => $organization->manager_nin,
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.organizations.update', $organization), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Name',
            'organization_type' => 'sme',
            'industry' => 'Updated Industry',
        ]);
    }

    public function test_organization_update_validates_unique_email_except_self(): void
    {
        $org1 = Organization::factory()->create(['email' => 'org1@test.dz']);
        $org2 = Organization::factory()->create(['email' => 'org2@test.dz']);

        // Try to update org2 with org1's email
        $updateData = [
            'name' => $org2->name,
            'email' => 'org1@test.dz', // Should fail
            'phone_number' => $org2->phone_number,
            'trade_register' => $org2->trade_register,
            'nif' => $org2->nif,
            'address' => $org2->address,
            'city' => $org2->city,
            'wilaya' => $org2->wilaya,
            'manager_first_name' => $org2->manager_first_name,
            'manager_last_name' => $org2->manager_last_name,
            'manager_nin' => $org2->manager_nin,
            'status' => $org2->status,
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch(route('admin.organizations.update', $org2), $updateData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_view_organization_details(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Organization',
            'industry' => 'Test Industry',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.show', $organization));

        $response->assertStatus(200);
        $response->assertSee($organization->name);
        $response->assertSee($organization->industry);
    }

    public function test_unauthorized_user_cannot_access_organization_pages(): void
    {
        $regularUser = User::factory()->create();
        $organization = Organization::factory()->create();

        // Test index
        $response = $this->actingAs($regularUser)
            ->get(route('admin.organizations.index'));
        $response->assertStatus(403);

        // Test create
        $response = $this->actingAs($regularUser)
            ->get(route('admin.organizations.create'));
        $response->assertStatus(403);

        // Test store
        $response = $this->actingAs($regularUser)
            ->post(route('admin.organizations.store'), []);
        $response->assertStatus(403);

        // Test edit
        $response = $this->actingAs($regularUser)
            ->get(route('admin.organizations.edit', $organization));
        $response->assertStatus(403);

        // Test update
        $response = $this->actingAs($regularUser)
            ->patch(route('admin.organizations.update', $organization), []);
        $response->assertStatus(403);
    }

    public function test_livewire_component_loads_organizations(): void
    {
        Organization::factory()->count(5)->create();

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.organizations.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.organization-table');
    }
}
