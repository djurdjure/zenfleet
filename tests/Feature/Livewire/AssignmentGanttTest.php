<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use App\Livewire\Assignments\AssignmentGantt;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Tests de fonctionnalité pour le composant Gantt des Affectations
 *
 * Couvre la visualisation temporelle et les interactions:
 * - Rendu du diagramme de Gantt
 * - Navigation temporelle
 * - Regroupement par ressource
 * - Création rapide d'affectations
 * - Calculs de position et largeur
 *
 * @group assignments
 * @group gantt
 * @group livewire
 */
class AssignmentGanttTest extends TestCase
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
    public function it_can_mount_gantt_component()
    {
        Livewire::test(AssignmentGantt::class)
            ->assertStatus(200)
            ->assertSet('groupBy', 'vehicle')
            ->assertSet('viewMode', 'week')
            ->assertSet('showOnlyActive', true);
    }

    /** @test */
    public function it_loads_gantt_data_on_mount()
    {
        Assignment::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $component = Livewire::test(AssignmentGantt::class);

        $this->assertNotEmpty($component->get('resources'));
        $this->assertNotEmpty($component->get('timeScale'));
        $this->assertIsArray($component->get('ganttData'));
    }

    /** @test */
    public function it_groups_by_vehicle_by_default()
    {
        $vehicle1 = $this->vehicle;
        $vehicle2 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $vehicle1->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $this->driver->id
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $resources = $component->get('resources');

        $this->assertCount(2, $resources);
        $this->assertTrue(collect($resources)->every(fn($r) => $r['type'] === 'vehicle'));
    }

    /** @test */
    public function it_can_switch_to_driver_grouping()
    {
        $driver1 = $this->driver;
        $driver2 = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $driver1->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $driver2->id
        ]);

        $component = Livewire::test(AssignmentGantt::class)
            ->set('groupBy', 'driver');

        $resources = $component->get('resources');
        $this->assertCount(2, $resources);
        $this->assertTrue(collect($resources)->every(fn($r) => $r['type'] === 'driver'));
    }

    /** @test */
    public function it_can_change_view_mode()
    {
        $component = Livewire::test(AssignmentGantt::class);

        // Test vue jour
        $component->set('viewMode', 'day');
        $this->assertEquals('day', $component->get('viewMode'));

        // Test vue semaine
        $component->set('viewMode', 'week');
        $this->assertEquals('week', $component->get('viewMode'));

        // Test vue mois
        $component->set('viewMode', 'month');
        $this->assertEquals('month', $component->get('viewMode'));
    }

    /** @test */
    public function it_navigates_to_previous_period()
    {
        $currentDate = now()->format('Y-m-d');

        $component = Livewire::test(AssignmentGantt::class)
            ->set('currentDate', $currentDate)
            ->call('previousPeriod');

        $newDate = $component->get('currentDate');
        $this->assertTrue(Carbon::parse($newDate)->lt(Carbon::parse($currentDate)));
    }

    /** @test */
    public function it_navigates_to_next_period()
    {
        $currentDate = now()->format('Y-m-d');

        $component = Livewire::test(AssignmentGantt::class)
            ->set('currentDate', $currentDate)
            ->call('nextPeriod');

        $newDate = $component->get('currentDate');
        $this->assertTrue(Carbon::parse($newDate)->gt(Carbon::parse($currentDate)));
    }

    /** @test */
    public function it_can_go_to_today()
    {
        $futureDate = now()->addWeek()->format('Y-m-d');

        Livewire::test(AssignmentGantt::class)
            ->set('currentDate', $futureDate)
            ->call('goToToday')
            ->assertSet('currentDate', now()->format('Y-m-d'));
    }

    /** @test */
    public function it_builds_correct_time_scale_for_week_view()
    {
        $component = Livewire::test(AssignmentGantt::class)
            ->set('viewMode', 'week')
            ->set('currentDate', now()->startOfWeek()->format('Y-m-d'));

        $timeScale = $component->get('timeScale');

        $this->assertCount(7, $timeScale); // 7 jours dans une semaine
        $this->assertArrayHasKey('date', $timeScale[0]);
        $this->assertArrayHasKey('label', $timeScale[0]);
        $this->assertArrayHasKey('isToday', $timeScale[0]);
        $this->assertArrayHasKey('isWeekend', $timeScale[0]);
    }

    /** @test */
    public function it_builds_correct_time_scale_for_day_view()
    {
        $component = Livewire::test(AssignmentGantt::class)
            ->set('viewMode', 'day')
            ->set('currentDate', now()->format('Y-m-d'));

        $timeScale = $component->get('timeScale');

        $this->assertCount(24, $timeScale); // 24 heures dans un jour
        $this->assertArrayHasKey('isWorkingHour', $timeScale[0]);
    }

    /** @test */
    public function it_builds_gantt_data_correctly()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE,
            'start_datetime' => now(),
            'end_datetime' => now()->addHours(4)
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $ganttData = $component->get('ganttData');

        $this->assertArrayHasKey($this->vehicle->id, $ganttData);
        $this->assertCount(1, $ganttData[$this->vehicle->id]);

        $assignmentData = $ganttData[$this->vehicle->id][0];
        $this->assertEquals($assignment->id, $assignmentData['id']);
        $this->assertArrayHasKey('title', $assignmentData);
        $this->assertArrayHasKey('color', $assignmentData);
        $this->assertArrayHasKey('tooltip', $assignmentData);
    }

    /** @test */
    public function it_filters_assignments_by_status()
    {
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_COMPLETED
        ]);

        $component = Livewire::test(AssignmentGantt::class)
            ->set('statusFilter', Assignment::STATUS_ACTIVE);

        $ganttData = $component->get('ganttData');
        $allAssignments = collect($ganttData)->flatten(1);

        $this->assertTrue($allAssignments->every(fn($a) => $a['status'] === Assignment::STATUS_ACTIVE));
    }

    /** @test */
    public function it_filters_assignments_by_resource()
    {
        $vehicle2 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $this->driver->id
        ]);

        $component = Livewire::test(AssignmentGantt::class)
            ->set('resourceFilter', $this->vehicle->id);

        $resources = $component->get('resources');
        $this->assertCount(1, $resources);
        $this->assertEquals($this->vehicle->id, $resources[0]['id']);
    }

    /** @test */
    public function it_shows_only_active_assignments_by_default()
    {
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_CANCELLED
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $ganttData = $component->get('ganttData');
        $allAssignments = collect($ganttData)->flatten(1);

        $this->assertCount(1, $allAssignments);
        $this->assertEquals(Assignment::STATUS_ACTIVE, $allAssignments->first()['status']);
    }

    /** @test */
    public function it_can_toggle_show_only_active()
    {
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_COMPLETED
        ]);

        $component = Livewire::test(AssignmentGantt::class)
            ->set('showOnlyActive', false);

        $ganttData = $component->get('ganttData');
        $allAssignments = collect($ganttData)->flatten(1);

        $this->assertCount(2, $allAssignments);
    }

    /** @test */
    public function it_opens_quick_create_modal()
    {
        Livewire::test(AssignmentGantt::class)
            ->call('openQuickCreate', 'vehicle', $this->vehicle->id, now()->format('Y-m-d\TH:i'), now()->addHours(2)->format('Y-m-d\TH:i'))
            ->assertSet('showQuickCreateModal', true)
            ->assertSet('selectedResourceType', 'vehicle')
            ->assertSet('selectedResourceId', $this->vehicle->id);
    }

    /** @test */
    public function it_closes_quick_create_modal()
    {
        Livewire::test(AssignmentGantt::class)
            ->call('openQuickCreate', 'vehicle', $this->vehicle->id, now()->format('Y-m-d\TH:i'))
            ->assertSet('showQuickCreateModal', true)
            ->call('closeQuickCreateModal')
            ->assertSet('showQuickCreateModal', false)
            ->assertSet('selectedResourceType', '')
            ->assertSet('selectedResourceId', null);
    }

    /** @test */
    public function it_calculates_assignment_position_correctly()
    {
        $component = Livewire::test(AssignmentGantt::class)
            ->set('viewMode', 'week')
            ->set('currentDate', now()->startOfWeek()->format('Y-m-d'));

        $startDateTime = now()->startOfWeek()->addDays(2)->format('Y-m-d\TH:i');
        $position = $component->calculateAssignmentPosition($startDateTime);

        $this->assertIsInt($position);
        $this->assertGreaterThanOrEqual(0, $position);
    }

    /** @test */
    public function it_calculates_assignment_width_correctly()
    {
        $component = Livewire::test(AssignmentGantt::class)
            ->set('viewMode', 'week');

        $startDateTime = now()->format('Y-m-d\TH:i');
        $endDateTime = now()->addHours(4)->format('Y-m-d\TH:i');
        $width = $component->calculateAssignmentWidth($startDateTime, $endDateTime);

        $this->assertIsInt($width);
        $this->assertGreaterThanOrEqual(20, $width); // Largeur minimale
    }

    /** @test */
    public function it_handles_indefinite_assignments()
    {
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE,
            'start_datetime' => now(),
            'end_datetime' => null // Affectation indéterminée
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $ganttData = $component->get('ganttData');

        $assignmentData = $ganttData[$this->vehicle->id][0];
        $this->assertTrue($assignmentData['isOngoing']);
        $this->assertNull($assignmentData['end']);
    }

    /** @test */
    public function it_provides_correct_status_colors()
    {
        $activeAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE
        ]);

        $scheduledAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_SCHEDULED
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $ganttData = $component->get('ganttData');

        $assignments = $ganttData[$this->vehicle->id];
        $activeColor = collect($assignments)->firstWhere('id', $activeAssignment->id)['color'];
        $scheduledColor = collect($assignments)->firstWhere('id', $scheduledAssignment->id)['color'];

        $this->assertEquals('#10B981', $activeColor); // Vert pour active
        $this->assertEquals('#3B82F6', $scheduledColor); // Bleu pour scheduled
    }

    /** @test */
    public function it_reloads_data_when_filters_change()
    {
        Assignment::factory()->count(2)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $originalResourceCount = count($component->get('resources'));

        // Appliquer un filtre qui réduira les ressources
        $component->set('resourceFilter', $this->vehicle->id);
        $filteredResourceCount = count($component->get('resources'));

        $this->assertLessThanOrEqual($originalResourceCount, $filteredResourceCount);
    }

    /** @test */
    public function it_responds_to_assignment_events()
    {
        $component = Livewire::test(AssignmentGantt::class);

        // Simuler l'événement de sauvegarde d'affectation
        $component->dispatch('assignment-saved');

        $this->assertTrue($component->get('showQuickCreateModal') === false);
        $this->assertNotEmpty($component->get('message'));
        $this->assertEquals('success', $component->get('messageType'));
    }

    /** @test */
    public function it_isolates_data_by_organization()
    {
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        $component = Livewire::test(AssignmentGantt::class);
        $resources = $component->get('resources');
        $ganttData = $component->get('ganttData');

        // Vérifier qu'on ne voit que nos ressources
        $this->assertTrue(collect($resources)->every(function($resource) {
            if ($resource['type'] === 'vehicle') {
                return Vehicle::find($resource['id'])->organization_id === $this->organization->id;
            } else {
                return Driver::find($resource['id'])->organization_id === $this->organization->id;
            }
        }));

        // Vérifier qu'on ne voit que nos affectations
        $allAssignments = collect($ganttData)->flatten(1);
        $this->assertCount(1, $allAssignments);
    }
}