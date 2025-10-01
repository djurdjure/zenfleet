<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Tests de fonctionnalité pour le contrôleur des Affectations
 *
 * Couvre les endpoints HTTP et l'intégration:
 * - Routes d'administration
 * - Vue Gantt
 * - Export de données
 * - API de statistiques
 * - Gestion des erreurs
 *
 * @group assignments
 * @group controllers
 */
class AssignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;
    private User $user;
    private Vehicle $vehicle;
    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_assignments_index_page()
    {
        $response = $this->get(route('admin.assignments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assignments.index');
    }

    /** @test */
    public function it_can_display_gantt_view()
    {
        $response = $this->get(route('admin.assignments.gantt'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assignments.gantt');
        $response->assertViewHas('title', 'Planning Gantt des Affectations');
        $response->assertViewHas('breadcrumbs');
    }

    /** @test */
    public function it_can_display_create_form()
    {
        $response = $this->get(route('admin.assignments.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assignments.create');
    }

    /** @test */
    public function it_can_display_create_form_with_prefilled_data()
    {
        $params = [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->format('Y-m-d\TH:i')
        ];

        $response = $this->get(route('admin.assignments.create', $params));

        $response->assertStatus(200);
        $response->assertViewHas('prefilledData', $params);
    }

    /** @test */
    public function it_can_display_assignment_details()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $response = $this->get(route('admin.assignments.show', $assignment));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assignments.show');
        $response->assertViewHas('assignment', $assignment);
    }

    /** @test */
    public function it_cannot_access_other_organization_assignment()
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        $otherAssignment = Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id
        ]);

        $response = $this->get(route('admin.assignments.show', $otherAssignment));

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function it_can_display_edit_form()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $response = $this->get(route('admin.assignments.edit', $assignment));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assignments.edit');
        $response->assertViewHas('assignment', $assignment);
    }

    /** @test */
    public function it_can_export_assignments_as_csv()
    {
        Assignment::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $response = $this->get(route('admin.assignments.export', ['format' => 'csv']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        $content = $response->getContent();
        $this->assertStringContains('ID,Véhicule,Chauffeur', $content);
    }

    /** @test */
    public function it_can_export_filtered_assignments()
    {
        $activeAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $completedAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_COMPLETED
        ]);

        $response = $this->get(route('admin.assignments.export', [
            'format' => 'csv',
            'status' => Assignment::STATUS_ACTIVE
        ]));

        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", trim($content));

        // En-tête + 1 ligne d'affectation active
        $this->assertCount(2, array_filter($lines));
    }

    /** @test */
    public function it_rejects_unsupported_export_formats()
    {
        $response = $this->get(route('admin.assignments.export', ['format' => 'xml']));

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Format non supporté']);
    }

    /** @test */
    public function it_can_get_assignment_statistics()
    {
        $today = now();
        $yesterday = now()->subDay();

        // Créer des affectations avec différents statuts
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE,
            'start_datetime' => $today
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_COMPLETED,
            'start_datetime' => $yesterday
        ]);

        $response = $this->get(route('admin.assignments.stats', [
            'date_from' => $yesterday->format('Y-m-d'),
            'date_to' => $today->format('Y-m-d')
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'total_assignments' => 2,
            'active_assignments' => 1,
            'completed_assignments' => 1,
            'cancelled_assignments' => 0
        ]);

        $responseData = $response->json();
        $this->assertArrayHasKey('vehicles_utilization', $responseData);
        $this->assertArrayHasKey('drivers_utilization', $responseData);
    }

    /** @test */
    public function it_isolates_statistics_by_organization()
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        // Affectation dans notre organisation
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        // Affectation dans l'autre organisation
        Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $response = $this->get(route('admin.assignments.stats'));

        $response->assertStatus(200);
        $responseData = $response->json();

        // Nos statistiques ne doivent inclure que notre organisation
        $this->assertEquals(0, $responseData['active_assignments']); // Car aucune dans la période par défaut

        $vehicleUtilization = collect($responseData['vehicles_utilization']);
        $this->assertTrue($vehicleUtilization->every(fn($v) => $v['vehicle_id'] === $this->vehicle->id));
    }

    /** @test */
    public function it_handles_date_filters_in_export()
    {
        $assignment1 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subDays(2)
        ]);

        $assignment2 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()
        ]);

        $response = $this->get(route('admin.assignments.export', [
            'format' => 'csv',
            'date_from' => now()->subDay()->format('Y-m-d'),
            'date_to' => now()->addDay()->format('Y-m-d')
        ]));

        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", trim($content));

        // En-tête + 1 ligne (seule l'affectation d'aujourd'hui)
        $this->assertCount(2, array_filter($lines));
    }

    /** @test */
    public function it_handles_vehicle_and_driver_filters_in_export()
    {
        $vehicle2 = Vehicle::factory()->create(['organization_id' => $this->organization->id]);
        $driver2 = Driver::factory()->create(['organization_id' => $this->organization->id]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $driver2->id
        ]);

        // Test filtre par véhicule
        $response = $this->get(route('admin.assignments.export', [
            'format' => 'csv',
            'vehicle_id' => $this->vehicle->id
        ]));

        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", trim($content));
        $this->assertCount(2, array_filter($lines)); // En-tête + 1 ligne

        // Test filtre par chauffeur
        $response = $this->get(route('admin.assignments.export', [
            'format' => 'csv',
            'driver_id' => $this->driver->id
        ]));

        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", trim($content));
        $this->assertCount(2, array_filter($lines)); // En-tête + 1 ligne
    }

    /** @test */
    public function it_requires_authentication()
    {
        auth()->logout();

        $response = $this->get(route('admin.assignments.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.assignments.gantt'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.assignments.export'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('admin.assignments.stats'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_calculates_utilization_rates_correctly()
    {
        // Créer une affectation de 4 heures
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->startOfDay(),
            'end_datetime' => now()->startOfDay()->addHours(4),
            'status' => Assignment::STATUS_COMPLETED
        ]);

        $response = $this->get(route('admin.assignments.stats', [
            'date_from' => now()->startOfDay()->format('Y-m-d H:i:s'),
            'date_to' => now()->startOfDay()->addDay()->format('Y-m-d H:i:s')
        ]));

        $response->assertStatus(200);
        $responseData = $response->json();

        $vehicleUtilization = collect($responseData['vehicles_utilization'])->first();
        $this->assertEquals(1, $vehicleUtilization['assignments_count']);

        // 4 heures sur 24 heures = 16.67% d'utilisation
        $this->assertEquals(16.67, $vehicleUtilization['utilization_rate']);
    }

    /** @test */
    public function it_handles_empty_results_gracefully()
    {
        // Pas d'affectations dans la base

        $response = $this->get(route('admin.assignments.stats'));
        $response->assertStatus(200);
        $responseData = $response->json();

        $this->assertEquals(0, $responseData['total_assignments']);
        $this->assertEquals(0, $responseData['active_assignments']);
        $this->assertEquals(0, $responseData['completed_assignments']);
        $this->assertEquals(0, $responseData['cancelled_assignments']);

        $response = $this->get(route('admin.assignments.export', ['format' => 'csv']));
        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", trim($content));
        $this->assertCount(1, array_filter($lines)); // Seulement l'en-tête
    }

    /** @test */
    public function it_includes_proper_breadcrumbs_in_views()
    {
        $response = $this->get(route('admin.assignments.gantt'));

        $response->assertStatus(200);
        $response->assertViewHas('breadcrumbs', function($breadcrumbs) {
            return isset($breadcrumbs['Admin']) &&
                   isset($breadcrumbs['Affectations']) &&
                   isset($breadcrumbs['Planning Gantt']);
        });
    }

    /** @test */
    public function it_exports_assignments_with_proper_csv_headers()
    {
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Test assignment',
            'start_mileage' => 10000,
            'end_mileage' => 10500
        ]);

        $response = $this->get(route('admin.assignments.export', ['format' => 'csv']));

        $response->assertStatus(200);

        $content = $response->getContent();
        $lines = explode("\n", $content);
        $headers = str_getcsv($lines[0]);

        $expectedHeaders = [
            'ID', 'Véhicule', 'Chauffeur', 'Date début', 'Date fin',
            'Durée (heures)', 'Statut', 'Motif', 'Kilométrage début',
            'Kilométrage fin', 'Créé par', 'Créé le'
        ];

        $this->assertEquals($expectedHeaders, $headers);
    }
}