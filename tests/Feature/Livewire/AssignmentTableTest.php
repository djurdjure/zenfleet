<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use App\Livewire\Assignments\AssignmentTable;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

/**
 * Tests de fonctionnalité pour le composant Table des Affectations
 *
 * Couvre l'interface utilisateur et les interactions:
 * - Affichage des affectations
 * - Filtrage et recherche
 * - Pagination
 * - Actions CRUD
 * - Export de données
 *
 * @group assignments
 * @group livewire
 */
class AssignmentTableTest extends TestCase
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

        // Configurer le contexte de l'organisation pour les permissions (CRITIQUE)
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($this->organization->id);

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        // Créer les permissions nécessaires pour les tests de table
        $permissions = [
            'view assignments',
            'create assignments',
            'edit assignments',
            'delete assignments',
            'view vehicles',
            'view drivers',
            // Enterprise Permissions (required by Middleware)
            'assignments.view',
            'assignments.create',
            'assignments.update',
            'assignments.delete',
            'assignments.end'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $this->user->givePermissionTo($permissions);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_mount_assignment_table_component()
    {
        Livewire::test(AssignmentTable::class)
            ->assertStatus(200)
            ->assertSeeText('Aucune affectation trouvée');
    }

    /** @test */
    public function it_displays_assignments_for_current_organization_only()
    {
        // Créer des affectations pour notre organisation
        $ourAssignments = Assignment::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        // Créer des affectations pour une autre organisation
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);
        Assignment::factory()->count(2)->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->assertStatus(200)
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 3;
            });
    }

    /** @test */
    public function it_can_search_assignments_by_vehicle_registration()
    {
        $specificVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'registration_plate' => 'ABC-123'
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $specificVehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('search', 'ABC-123')
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1;
            });
    }

    /** @test */
    public function it_can_search_assignments_by_driver_name()
    {
        $specificDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont'
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $specificDriver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('search', 'Jean Dupont')
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1;
            });
    }

    /** @test */
    public function it_can_filter_assignments_by_status()
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

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_CANCELLED
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('statusFilter', Assignment::STATUS_ACTIVE)
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1 &&
                    $assignments->first()->status === Assignment::STATUS_ACTIVE;
            });
    }

    /** @test */
    public function it_can_filter_assignments_by_vehicle()
    {
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('vehicleFilter', $this->vehicle->id)
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1 &&
                    $assignments->first()->vehicle_id === $this->vehicle->id;
            });
    }

    /** @test */
    public function it_can_filter_assignments_by_driver()
    {
        $otherDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $otherDriver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('driverFilter', $this->driver->id)
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1 &&
                    $assignments->first()->driver_id === $this->driver->id;
            });
    }

    /** @test */
    public function it_can_filter_assignments_by_date_range()
    {
        $today = now();
        $yesterday = now()->subDay();
        $tomorrow = now()->addDay();

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => $yesterday
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => $today
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => $tomorrow
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('dateFrom', $today->format('Y-m-d'))
            ->set('dateTo', $tomorrow->format('Y-m-d'))
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 2;
            });
    }

    /** @test */
    public function it_can_sort_assignments_by_start_date()
    {
        $assignment1 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(1)
        ]);

        $assignment2 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(2)
        ]);

        $assignment3 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('sortBy', 'start_datetime')
            ->set('sortDirection', 'asc')
            ->assertViewHas('assignments', function ($assignments) use ($assignment3, $assignment1, $assignment2) {
                $assignmentIds = $assignments->pluck('id')->toArray();
                return $assignmentIds === [$assignment3->id, $assignment1->id, $assignment2->id];
            });
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        Assignment::factory()->count(15)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('perPage', 10)
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 10;
            })
            ->assertSee('Page 1 sur 2');
    }

    /** @test */
    public function it_can_open_create_modal()
    {
        Livewire::test(AssignmentTable::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->assertSet('selectedAssignment', null);
    }

    /** @test */
    public function it_can_open_edit_modal()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->call('openEditModal', $assignment->id)
            ->assertSet('showEditModal', true)
            ->assertSet('selectedAssignment.id', $assignment->id);
    }

    /** @test */
    public function it_can_close_modals()
    {
        Livewire::test(AssignmentTable::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true)
            ->call('closeModal')
            ->assertSet('showCreateModal', false)
            ->assertSet('showEditModal', false)
            ->assertSet('selectedAssignment', null);
    }

    /** @test */
    public function it_can_terminate_active_assignment()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE,
            'start_datetime' => now()->subHours(2),
            'end_datetime' => null
        ]);

        Livewire::test(AssignmentTable::class)
            ->call('terminateAssignment', $assignment->id, [
                'end_datetime' => now()->format('Y-m-d\TH:i'),
                'end_mileage' => 15000
            ])
            ->assertHasNoErrors();

        $assignment->refresh();
        $this->assertEquals(Assignment::STATUS_COMPLETED, $assignment->status);
        $this->assertNotNull($assignment->end_datetime);
        $this->assertEquals(15000, $assignment->end_mileage);
    }

    /** @test */
    public function it_validates_termination_data()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_ACTIVE,
            'start_datetime' => now(),
            'start_mileage' => 10000
        ]);

        Livewire::test(AssignmentTable::class)
            ->call('terminateAssignment', $assignment->id, [
                'end_datetime' => now()->subHour()->format('Y-m-d\TH:i'), // Date antérieure
                'end_mileage' => 5000 // Kilométrage inférieur
            ])
            ->assertHasErrors(['end_datetime', 'end_mileage']);
    }

    /** @test */
    public function it_can_delete_assignment_with_confirmation()
    {
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->call('confirmDelete', $assignment->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('assignmentToDelete.id', $assignment->id)
            ->call('deleteAssignment')
            ->assertSet('showDeleteModal', false)
            ->assertDispatchedBrowserEvent('assignment-deleted');

        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }

    /** @test */
    public function it_resets_filters_correctly()
    {
        Livewire::test(AssignmentTable::class)
            ->set('search', 'test')
            ->set('statusFilter', Assignment::STATUS_ACTIVE)
            ->set('vehicleFilter', $this->vehicle->id)
            ->set('driverFilter', $this->driver->id)
            ->set('dateFrom', now()->format('Y-m-d'))
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('statusFilter', '')
            ->assertSet('vehicleFilter', '')
            ->assertSet('driverFilter', '')
            ->assertSet('dateFrom', '')
            ->assertSet('dateTo', '');
    }

    /** @test */
    public function it_shows_only_current_organization_options_in_filters()
    {
        // Créer des ressources pour une autre organisation
        $otherOrganization = Organization::factory()->create();
        Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        $component = Livewire::test(AssignmentTable::class);

        $vehicleOptions = $component->get('vehicleOptions');
        $driverOptions = $component->get('driverOptions');

        // Vérifier que seules les ressources de notre organisation sont disponibles
        $this->assertTrue($vehicleOptions->contains('organization_id', $this->organization->id));
        $this->assertFalse($vehicleOptions->contains('organization_id', $otherOrganization->id));

        $this->assertTrue($driverOptions->contains('organization_id', $this->organization->id));
        $this->assertFalse($driverOptions->contains('organization_id', $otherOrganization->id));
    }

    /** @test */
    public function it_handles_empty_search_gracefully()
    {
        Assignment::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        Livewire::test(AssignmentTable::class)
            ->set('search', '')
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 3;
            });
    }

    /** @test */
    public function it_updates_real_time_when_assignments_change()
    {
        Livewire::test(AssignmentTable::class)
            ->assertSeeText('Aucune affectation trouvée');

        // Créer une affectation
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id
        ]);

        // Simuler un rafraîchissement
        Livewire::test(AssignmentTable::class)
            ->call('loadAssignments')
            ->assertViewHas('assignments', function ($assignments) {
                return $assignments->count() === 1;
            });
    }
}
