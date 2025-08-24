<?php

namespace Tests\Feature\Admin;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
        $this->adminUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        // In a real app, we'd assign a role like 'Admin' and check permissions.
        // For this test, acting as the user is sufficient to bypass auth middleware.
        $this->actingAs($this->adminUser);
    }

    public function test_admin_can_create_document_with_metadata_and_polymorphic_links()
    {
        // 1. Arrange
        Storage::fake('s3');

        $category = DocumentCategory::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Facture Test',
            'meta_schema' => [
                'fields' => [
                    ['name' => 'numero_facture', 'label' => 'Numéro de Facture', 'type' => 'string', 'required' => true],
                    ['name' => 'montant_ht', 'label' => 'Montant HT', 'type' => 'number', 'required' => true],
                ]
            ]
        ]);

        $vehicle = Vehicle::factory()->create(['organization_id' => $this->organization->id]);
        $driver = User::factory()->create(['organization_id' => $this->organization->id]);
        $supplier = Supplier::factory()->create(['organization_id' => $this->organization->id]);

        $postData = [
            'document_category_id' => $category->id,
            'description' => 'Test facture description',
            'document_file' => UploadedFile::fake()->create('facture.pdf', 100, 'application/pdf'),
            'extra_metadata' => [
                'numero_facture' => 'INV-2025-123',
                'montant_ht' => 99.99,
            ],
            'linked_vehicles' => [$vehicle->id],
            'linked_drivers' => [$driver->id],
            'linked_suppliers' => [$supplier->id],
        ];

        // 2. Act
        $response = $this->post(route('admin.documents.store'), $postData);

        // 3. Assert
        $response->assertRedirect(route('admin.documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'document_category_id' => $category->id,
            'description' => 'Test facture description',
            'original_filename' => 'facture.pdf',
        ]);

        $document = Document::latest()->first();
        $this->assertNotNull($document);

        // Assert metadata was saved correctly
        $this->assertEquals('INV-2025-123', $document->extra_metadata['numero_facture']);
        $this->assertEquals(99.99, $document->extra_metadata['montant_ht']);

        // Assert file was stored
        Storage::disk('s3')->assertExists($document->file_path);

        // Assert polymorphic relationships were created
        $this->assertCount(1, $document->vehicles);
        $this->assertEquals($vehicle->id, $document->vehicles->first()->id);

        $this->assertCount(1, $document->users);
        $this->assertEquals($driver->id, $document->users->first()->id);

        $this->assertCount(1, $document->suppliers);
        $this->assertEquals($supplier->id, $document->suppliers->first()->id);

        $this->assertDatabaseHas('documentables', [
            'document_id' => $document->id,
            'documentable_id' => $vehicle->id,
            'documentable_type' => Vehicle::class,
        ]);
        $this->assertDatabaseHas('documentables', [
            'document_id' => $document->id,
            'documentable_id' => $driver->id,
            'documentable_type' => User::class,
        ]);
        $this->assertDatabaseHas('documentables', [
            'document_id' => $document->id,
            'documentable_id' => $supplier->id,
            'documentable_type' => Supplier::class,
        ]);
    }
}
