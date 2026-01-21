<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $organization;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create([
            'organization_type' => 'enterprise'
        ]);

        $this->admin = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        $this->admin->assignRole('Admin');
    }

    public function test_can_create_supplier_with_algerian_compliance()
    {
        $this->actingAs($this->admin);

        $supplierData = [
            'company_name' => 'SARL Pièces Auto Alger',
            'company_type' => 'sarl',
            'legal_representative' => 'Mohamed Benali',
            'nif' => '123456789012345', // 15 digits
            'nis' => '098765432109876', // 15 digits
            'trade_register' => '16/00-1234567', // Format wilaya/année-numéro
            'trade_register_place' => 'Alger',
            'article_of_association' => 'Acte notarié n° 2024/156',
            'rib' => '12345678901234567890', // 20 digits
            'bank_name' => 'BNA Alger Centre',
            'email' => 'contact@piecesauto-alger.dz',
            'phone' => '+213-21-123456',
            'mobile' => '+213-555-123456',
            'address_line_1' => '123 Rue Didouche Mourad',
            'address_line_2' => 'Résidence El Badr, Local 15',
            'wilaya' => 'Alger',
            'postal_code' => '16000',
            'specialties' => ['engine', 'brake', 'electrical'],
            'certifications' => ['ISO 9001:2015'],
            'website' => 'https://piecesauto-alger.dz',
            'business_hours' => [
                'monday' => '08:00-17:00',
                'tuesday' => '08:00-17:00',
                'wednesday' => '08:00-17:00',
                'thursday' => '08:00-17:00',
                'saturday' => '08:00-12:00'
            ],
            'payment_terms_days' => 30,
            'credit_limit' => 500000.00,
            'tax_rate' => 19.00,
            'emergency_contact' => '+213-555-987654',
            'notes' => 'Fournisseur de confiance, livraisons rapides'
        ];

        $response = $this->post(route('admin.suppliers-enterprise.store'), $supplierData);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', [
            'company_name' => 'SARL Pièces Auto Alger',
            'nif' => '123456789012345',
            'trade_register' => '16/00-1234567',
            'organization_id' => $this->organization->id
        ]);
    }

    public function test_nif_validation_enforces_15_digits()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.suppliers-enterprise.store'), [
            'company_name' => 'Test Supplier',
            'nif' => '12345', // Invalid - too short
            'email' => 'test@example.dz'
        ]);

        $response->assertSessionHasErrors(['nif']);
    }

    public function test_trade_register_validation_enforces_correct_format()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.suppliers-enterprise.store'), [
            'company_name' => 'Test Supplier',
            'nif' => '123456789012345',
            'trade_register' => 'invalid-format', // Invalid format
            'email' => 'test@example.dz'
        ]);

        $response->assertSessionHasErrors(['trade_register']);
    }

    public function test_rib_validation_enforces_20_digits()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.suppliers-enterprise.store'), [
            'company_name' => 'Test Supplier',
            'nif' => '123456789012345',
            'rib' => '123456789', // Invalid - too short
            'email' => 'test@example.dz'
        ]);

        $response->assertSessionHasErrors(['rib']);
    }

    public function test_supplier_rating_system()
    {
        $supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id,
            'quality_rating' => 0.0,
            'delivery_rating' => 0.0,
            'price_rating' => 0.0,
            'overall_rating' => 0.0
        ]);

        // Test rating update
        $supplier->updateRating('quality', 4.5);
        $supplier->updateRating('delivery', 4.0);
        $supplier->updateRating('price', 4.8);

        $supplier->refresh();

        $this->assertEquals(4.5, $supplier->quality_rating);
        $this->assertEquals(4.0, $supplier->delivery_rating);
        $this->assertEquals(4.8, $supplier->price_rating);
        $this->assertEquals(4.43, round($supplier->overall_rating, 2)); // Average
    }

    public function test_supplier_contract_management()
    {
        $supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id,
            'contract_start_date' => now(),
            'contract_end_date' => now()->addYear(),
            'contract_type' => 'annual'
        ]);

        $this->assertTrue($supplier->hasActiveContract());

        // Test expired contract
        $supplier->update([
            'contract_end_date' => now()->subDays(10)
        ]);

        $this->assertFalse($supplier->hasActiveContract());
    }

    public function test_supplier_credit_limit_tracking()
    {
        $supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id,
            'credit_limit' => 100000.00,
            'current_debt' => 0.00
        ]);

        // Test credit availability
        $this->assertTrue($supplier->hasAvailableCredit(50000.00));
        $this->assertFalse($supplier->hasAvailableCredit(150000.00));

        // Test debt update
        $supplier->addToDebt(75000.00);
        $this->assertEquals(75000.00, $supplier->current_debt);
        $this->assertEquals(25000.00, $supplier->available_credit);
    }

    public function test_supplier_performance_metrics()
    {
        $supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id,
            'total_orders' => 50,
            'orders_completed' => 45,
            'orders_late' => 8,
            'average_delivery_days' => 3.5
        ]);

        $metrics = $supplier->getPerformanceMetrics();

        $this->assertEquals(90.0, $metrics['completion_rate']); // 45/50 * 100
        $this->assertEquals(16.0, $metrics['late_delivery_rate']); // 8/50 * 100
        $this->assertEquals(3.5, $metrics['average_delivery_days']);
        $this->assertEquals(5, $metrics['pending_orders']); // 50 - 45
    }

    public function test_supplier_search_and_filtering()
    {
        Supplier::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'specialties' => ['engine'],
            'wilaya' => 'Alger',
            'status' => 'active'
        ]);

        Supplier::factory()->count(2)->create([
            'organization_id' => $this->organization->id,
            'specialties' => ['brake'],
            'wilaya' => 'Oran',
            'status' => 'active'
        ]);

        $this->actingAs($this->admin);

        // Test specialty filter
        $response = $this->get(route('admin.suppliers-enterprise.index', [
            'specialty' => 'engine'
        ]));
        $response->assertStatus(200);

        // Test wilaya filter
        $response = $this->get(route('admin.suppliers-enterprise.index', [
            'wilaya' => 'Alger'
        ]));
        $response->assertStatus(200);

        // Test search by company name
        $specificSupplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id,
            'company_name' => 'SARL Unique Motors'
        ]);

        $response = $this->get(route('admin.suppliers-enterprise.index', [
            'search' => 'Unique'
        ]));
        $response->assertStatus(200);
    }

    public function test_organization_isolation_in_suppliers()
    {
        $otherOrganization = Organization::factory()->create();
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id
        ]);

        $supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($otherUser);
        $response = $this->get(route('admin.suppliers-enterprise.show', $supplier));
        $response->assertStatus(404);
    }

    public function test_supplier_export_functionality()
    {
        Supplier::factory()->count(10)->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.suppliers-enterprise.export'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_supplier_import_validation()
    {
        $this->actingAs($this->admin);

        // Test with invalid CSV structure
        $invalidCsv = \Illuminate\Http\UploadedFile::fake()->create('invalid.csv', 100, 'text/csv');

        $response = $this->post(route('admin.suppliers-enterprise.import'), [
            'file' => $invalidCsv
        ]);

        $response->assertSessionHasErrors();
    }
}
