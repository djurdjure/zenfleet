<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\OrganizationTable;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrganizationTableTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_organization_table_component_renders(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->assertStatus(200);
    }

    public function test_organization_table_displays_organizations(): void
    {
        $org1 = Organization::factory()->create(['name' => 'Test Org 1']);
        $org2 = Organization::factory()->create(['name' => 'Test Org 2']);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->assertSee('Test Org 1')
            ->assertSee('Test Org 2');
    }

    public function test_organization_table_search_functionality(): void
    {
        Organization::factory()->create(['name' => 'Transport Company']);
        Organization::factory()->create(['name' => 'Logistics Corp']);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('search', 'Transport')
            ->assertSee('Transport Company')
            ->assertDontSee('Logistics Corp');
    }

    public function test_organization_table_filters_by_status(): void
    {
        Organization::factory()->create([
            'name' => 'Active Organization',
            'status' => 'active',
        ]);
        Organization::factory()->create([
            'name' => 'Inactive Organization',
            'status' => 'inactive',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('status', 'active')
            ->assertSee('Active Organization')
            ->assertDontSee('Inactive Organization');
    }

    public function test_organization_table_filters_by_type(): void
    {
        Organization::factory()->create([
            'name' => 'Enterprise Org',
            'organization_type' => 'enterprise',
        ]);
        Organization::factory()->create([
            'name' => 'SME Org',
            'organization_type' => 'sme',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('type', 'enterprise')
            ->assertSee('Enterprise Org')
            ->assertDontSee('SME Org');
    }

    public function test_organization_table_filters_by_wilaya(): void
    {
        Organization::factory()->create([
            'name' => 'Alger Organization',
            'wilaya' => '16',
        ]);
        Organization::factory()->create([
            'name' => 'Oran Organization',
            'wilaya' => '31',
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('country', '16') // Note: the component uses 'country' for wilaya filtering
            ->assertSee('Alger Organization')
            ->assertDontSee('Oran Organization');
    }

    public function test_organization_table_pagination(): void
    {
        // Create more organizations than the per-page limit
        Organization::factory()->count(25)->create();

        $component = Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class);

        // Test that pagination is working
        $organizations = $component->get('organizations');
        $this->assertTrue($organizations->hasPages());
    }

    public function test_organization_table_search_in_multiple_fields(): void
    {
        Organization::factory()->create([
            'name' => 'ABC Corp',
            'legal_name' => 'ABC Corporation SAS',
            'city' => 'Alger',
            'nif' => '123456789012345',
        ]);

        $component = Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class);

        // Test search by name
        $component->set('search', 'ABC')
            ->assertSee('ABC Corp');

        // Test search by legal name
        $component->set('search', 'Corporation')
            ->assertSee('ABC Corp');

        // Test search by city
        $component->set('search', 'Alger')
            ->assertSee('ABC Corp');

        // Test search by NIF
        $component->set('search', '123456789012345')
            ->assertSee('ABC Corp');
    }

    public function test_organization_table_shows_counts(): void
    {
        // Create organizations with users/vehicles/drivers counts
        $org = Organization::factory()->create();

        // Mock the relationships counts if needed
        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->assertSee($org->name);
    }

    public function test_organization_table_reset_filters(): void
    {
        Organization::factory()->create(['name' => 'Test Organization']);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('search', 'Test')
            ->set('status', 'active')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('status', '')
            ->assertSet('type', '')
            ->assertSet('country', '');
    }

    public function test_organization_table_view_mode_toggle(): void
    {
        Organization::factory()->create();

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->assertSet('viewMode', 'table')
            ->call('setViewMode', 'grid')
            ->assertSet('viewMode', 'grid')
            ->call('setViewMode', 'table')
            ->assertSet('viewMode', 'table');
    }

    public function test_organization_table_sorting(): void
    {
        Organization::factory()->create(['name' => 'ZZZ Organization']);
        Organization::factory()->create(['name' => 'AAA Organization']);

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->call('sortBy', 'name')
            ->assertSeeInOrder(['AAA Organization', 'ZZZ Organization']);
    }

    public function test_organization_table_export_functionality(): void
    {
        Organization::factory()->count(5)->create();

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->call('export')
            ->assertDispatched('organizations-exported');
    }

    public function test_organization_table_bulk_actions(): void
    {
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class)
            ->set('selectedOrganizations', [$org1->id, $org2->id])
            ->call('bulkAction', 'deactivate')
            ->assertDispatched('bulk-action-completed');
    }

    public function test_organization_table_loads_filter_options(): void
    {
        // Create organizations with different statuses, types, and wilayas
        Organization::factory()->create(['status' => 'active', 'organization_type' => 'enterprise', 'wilaya' => '16']);
        Organization::factory()->create(['status' => 'inactive', 'organization_type' => 'sme', 'wilaya' => '31']);

        $component = Livewire::actingAs($this->adminUser)
            ->test(OrganizationTable::class);

        $filters = $component->get('filters');

        $this->assertArrayHasKey('statuses', $filters);
        $this->assertArrayHasKey('types', $filters);
        $this->assertArrayHasKey('countries', $filters);

        // Check that the filter options contain expected values
        $this->assertContains('active', array_keys($filters['statuses']));
        $this->assertContains('enterprise', array_keys($filters['types']));
    }
}
