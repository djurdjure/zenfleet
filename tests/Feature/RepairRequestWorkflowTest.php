<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\RepairRequestApproved;
use App\Notifications\RepairRequestRejected;
use App\Notifications\RepairRequestValidated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepairRequestWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $organization;
    protected $driver;
    protected $supervisor;
    protected $manager;
    protected $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create([
            'type' => 'enterprise'
        ]);

        $this->driver = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        $this->driver->assignRole('Chauffeur');

        $this->supervisor = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        $this->supervisor->assignRole('Superviseur');

        $this->manager = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        $this->manager->assignRole('Gestionnaire Flotte');

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Notification::fake();
        Storage::fake('public');
    }

    public function test_driver_can_create_repair_request()
    {
        $this->actingAs($this->driver);

        $attachments = [
            UploadedFile::fake()->image('damage1.jpg'),
            UploadedFile::fake()->create('report.pdf', 100)
        ];

        $response = $this->post(route('admin.repair-requests.store'), [
            'vehicle_id' => $this->vehicle->id,
            'category' => 'engine',
            'priority' => 'high',
            'description' => 'Problème moteur urgent nécessitant intervention',
            'estimated_cost' => 25000.00,
            'attachments' => $attachments
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('repair_requests', [
            'vehicle_id' => $this->vehicle->id,
            'requested_by' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING,
            'category' => 'engine',
            'priority' => 'high'
        ]);

        $repairRequest = RepairRequest::latest()->first();
        $this->assertCount(2, $repairRequest->attachments);
    }

    public function test_supervisor_can_approve_repair_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'requested_by' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->post(route('admin.repair-requests.approve', $repairRequest), [
            'supervisor_comments' => 'Approuvé après vérification terrain'
        ]);

        $response->assertJson(['success' => true]);

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_INITIAL_APPROVAL, $repairRequest->status);
        $this->assertEquals(RepairRequest::SUPERVISOR_ACCEPT, $repairRequest->supervisor_decision);
        $this->assertEquals($this->supervisor->id, $repairRequest->supervisor_id);

        Notification::assertSentTo(
            $this->driver,
            RepairRequestApproved::class
        );
    }

    public function test_supervisor_can_reject_repair_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'requested_by' => $this->driver->id,
            'status' => RepairRequest::STATUS_PENDING
        ]);

        $this->actingAs($this->supervisor);

        $response = $this->post(route('admin.repair-requests.reject', $repairRequest), [
            'supervisor_comments' => 'Réparation non justifiée - maintenance préventive suffisante'
        ]);

        $response->assertJson(['success' => true]);

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_REJECTED, $repairRequest->status);
        $this->assertEquals(RepairRequest::SUPERVISOR_REJECT, $repairRequest->supervisor_decision);

        Notification::assertSentTo(
            $this->driver,
            RepairRequestRejected::class
        );
    }

    public function test_manager_can_validate_approved_request()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'requested_by' => $this->driver->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => RepairRequest::STATUS_INITIAL_APPROVAL,
            'supervisor_decision' => RepairRequest::SUPERVISOR_ACCEPT
        ]);

        $this->actingAs($this->manager);

        $response = $this->post(route('admin.repair-requests.validate', $repairRequest), [
            'manager_comments' => 'Budget validé - procéder aux réparations'
        ]);

        $response->assertJson(['success' => true]);

        $repairRequest->refresh();
        $this->assertEquals(RepairRequest::STATUS_APPROVED, $repairRequest->status);
        $this->assertEquals(RepairRequest::MANAGER_VALIDATE, $repairRequest->manager_decision);
        $this->assertEquals($this->manager->id, $repairRequest->manager_id);

        Notification::assertSentTo(
            $this->driver,
            RepairRequestValidated::class
        );
    }

    public function test_workflow_prevents_unauthorized_actions()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => RepairRequest::STATUS_PENDING
        ]);

        // Manager cannot approve directly without supervisor approval
        $this->actingAs($this->manager);
        $response = $this->post(route('admin.repair-requests.validate', $repairRequest));
        $response->assertStatus(422);

        // Driver cannot approve their own request
        $this->actingAs($this->driver);
        $response = $this->post(route('admin.repair-requests.approve', $repairRequest));
        $response->assertStatus(403);
    }

    public function test_organization_isolation_in_repair_requests()
    {
        $otherOrganization = Organization::factory()->create();
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id
        ]);

        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($otherUser);
        $response = $this->get(route('admin.repair-requests.show', $repairRequest));
        $response->assertStatus(404);
    }

    public function test_repair_request_cost_tracking()
    {
        $repairRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'estimated_cost' => 50000.00,
            'status' => RepairRequest::STATUS_APPROVED
        ]);

        $this->actingAs($this->manager);

        // Update actual cost
        $response = $this->patch(route('admin.repair-requests.update', $repairRequest), [
            'actual_cost' => 47500.00,
            'completion_date' => now()->toDateString(),
            'status' => RepairRequest::STATUS_COMPLETED
        ]);

        $response->assertRedirect();

        $repairRequest->refresh();
        $this->assertEquals(47500.00, $repairRequest->actual_cost);
        $this->assertEquals(RepairRequest::STATUS_COMPLETED, $repairRequest->status);
        $this->assertNotNull($repairRequest->completion_date);
    }

    public function test_repair_request_search_and_filters()
    {
        RepairRequest::factory()->count(5)->create([
            'organization_id' => $this->organization->id,
            'category' => 'engine',
            'status' => RepairRequest::STATUS_PENDING
        ]);

        RepairRequest::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'category' => 'brake',
            'status' => RepairRequest::STATUS_APPROVED
        ]);

        $this->actingAs($this->manager);

        // Test category filter
        $response = $this->get(route('admin.repair-requests.index', [
            'category' => 'engine'
        ]));
        $response->assertStatus(200);

        // Test status filter
        $response = $this->get(route('admin.repair-requests.index', [
            'status' => RepairRequest::STATUS_APPROVED
        ]));
        $response->assertStatus(200);

        // Test search functionality
        $searchRequest = RepairRequest::factory()->create([
            'organization_id' => $this->organization->id,
            'description' => 'Problème spécifique moteur diesel'
        ]);

        $response = $this->get(route('admin.repair-requests.index', [
            'search' => 'diesel'
        ]));
        $response->assertStatus(200);
    }
}