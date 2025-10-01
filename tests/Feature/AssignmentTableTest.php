<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use App\Livewire\AssignmentTable;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;

/**
 * 🧪 Tests fonctionnels du composant AssignmentTable
 *
 * Couvre toutes les fonctionnalités enterprise-grade:
 * - Affichage et pagination des affectations
 * - Filtres avancés (véhicule, chauffeur, statut, dates)
 * - Actions (voir, éditer, terminer, supprimer, dupliquer)
 * - Export CSV
 * - Gestion des permissions
 * - Responsive et accessibilité
 */
class AssignmentTableTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function it_displays_assignments_table()
    {
        // Arrangement
        $assignments = Assignment::factory(5)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->assertSuccessful()
            ->assertSee($assignments->first()->vehicle_display)
            ->assertSee($assignments->first()->driver_display)
            ->assertSeeHtml('Affectations'); // En-tête du tableau
    }

    /** @test */
    public function it_paginates_assignments()
    {
        // Arrangement: Créer plus d'affectations que la limite par page
        Assignment::factory(30)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->assertSuccessful()
            ->assertSeeHtml('Suivant') // Lien pagination
            ->assertSet('perPage', 25) // Valeur par défaut
            ->set('perPage', 50)
            ->call('updatedPerPage')
            ->assertSet('perPage', 50);
    }

    /** @test */
    public function it_filters_assignments_by_search()
    {
        // Arrangement
        $searchableAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Mission spéciale TEST'
        ]);

        $otherAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Formation normale'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('search', 'TEST')
            ->call('updatedSearch')
            ->assertSee('Mission spéciale TEST')
            ->assertDontSee('Formation normale');
    }

    /** @test */
    public function it_filters_assignments_by_vehicle()
    {
        // Arrangement
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $assignmentVehicle1 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        $assignmentVehicle2 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('vehicleFilter', $this->vehicle->id)
            ->call('updatedVehicleFilter')
            ->assertSee($this->vehicle->registration_plate)
            ->assertDontSee($otherVehicle->registration_plate);
    }

    /** @test */
    public function it_filters_assignments_by_driver()
    {
        // Arrangement
        $otherDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $assignmentDriver1 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        $assignmentDriver2 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $otherDriver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('driverFilter', $this->driver->id)
            ->call('updatedDriverFilter')
            ->assertSee($this->driver->first_name . ' ' . $this->driver->last_name)
            ->assertDontSee($otherDriver->first_name . ' ' . $otherDriver->last_name);
    }

    /** @test */
    public function it_filters_assignments_by_status()
    {
        // Arrangement
        $activeAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null // En cours
        ]);

        $completedAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->subHour() // Terminé
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('statusFilter', 'active')
            ->call('updatedStatusFilter')
            ->assertSee('En cours')
            ->assertDontSee('Terminé');
    }

    /** @test */
    public function it_filters_assignments_by_date_range()
    {
        // Arrangement
        $assignmentInRange = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-20 10:00:00'),
        ]);

        $assignmentOutOfRange = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => Carbon::parse('2025-01-25 10:00:00'),
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('dateFromFilter', '2025-01-19')
            ->set('dateToFilter', '2025-01-21')
            ->call('updatedDateFromFilter')
            ->call('updatedDateToFilter')
            ->assertSeeInOrder(['20/01/2025']) // Date dans la période
            ->assertDontSee('25/01/2025'); // Date hors période
    }

    /** @test */
    public function it_shows_only_ongoing_assignments()
    {
        // Arrangement
        $ongoingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null // En cours
        ]);

        $completedAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->subHour() // Terminé
        ]);

        $scheduledAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addHour(),
            'end_datetime' => now()->addHours(2)
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('onlyOngoing', true)
            ->call('updatedOnlyOngoing')
            ->assertSee('En cours')
            ->assertDontSee('Terminé')
            ->assertDontSee('Programmé');
    }

    /** @test */
    public function it_sorts_assignments_by_different_columns()
    {
        // Arrangement
        $assignment1 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()
        ]);

        $assignment2 = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(2)
        ]);

        // Action & Assertion: Tri par date
        Livewire::test(AssignmentTable::class)
            ->call('sortBy', 'start_datetime')
            ->assertSet('sortBy', 'start_datetime')
            ->assertSet('sortDirection', 'asc');

        // Test double clic pour inverser l'ordre
        Livewire::test(AssignmentTable::class)
            ->set('sortBy', 'start_datetime')
            ->set('sortDirection', 'asc')
            ->call('sortBy', 'start_datetime')
            ->assertSet('sortDirection', 'desc');
    }

    /** @test */
    public function it_resets_all_filters()
    {
        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('search', 'test')
            ->set('vehicleFilter', $this->vehicle->id)
            ->set('driverFilter', $this->driver->id)
            ->set('statusFilter', 'active')
            ->set('onlyOngoing', true)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('vehicleFilter', '')
            ->assertSet('driverFilter', '')
            ->assertSet('statusFilter', '')
            ->assertSet('onlyOngoing', false);
    }

    /** @test */
    public function it_opens_create_modal()
    {
        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openCreateModal')
            ->assertSet('showCreateModal', true);
    }

    /** @test */
    public function it_opens_edit_modal()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openEditModal', $assignment->id)
            ->assertSet('showEditModal', true)
            ->assertSet('selectedAssignment.id', $assignment->id);
    }

    /** @test */
    public function it_opens_terminate_modal_for_ongoing_assignment()
    {
        // Arrangement: Affectation en cours
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openTerminateModal', $assignment->id)
            ->assertSet('showTerminateModal', true)
            ->assertSet('selectedAssignment.id', $assignment->id);
    }

    /** @test */
    public function it_prevents_terminate_modal_for_completed_assignment()
    {
        // Arrangement: Affectation terminée
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->subHour()
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openTerminateModal', $assignment->id)
            ->assertSet('showTerminateModal', false)
            ->assertHasErrors(['terminate']);
    }

    /** @test */
    public function it_terminates_assignment_successfully()
    {
        // Arrangement: Affectation en cours
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openTerminateModal', $assignment->id)
            ->set('terminateDateTime', now()->format('Y-m-d\TH:i'))
            ->set('terminateNotes', 'Mission terminée avec succès')
            ->call('terminateAssignment')
            ->assertSet('showTerminateModal', false)
            ->assertDispatched('assignment-terminated');

        // Vérifier en base de données
        $assignment->refresh();
        $this->assertNotNull($assignment->end_datetime);
        $this->assertStringContainsString('Mission terminée avec succès', $assignment->notes);
    }

    /** @test */
    public function it_validates_terminate_datetime()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null
        ]);

        // Action & Assertion: Date de fin avant le début
        Livewire::test(AssignmentTable::class)
            ->call('openTerminateModal', $assignment->id)
            ->set('terminateDateTime', now()->subHours(2)->format('Y-m-d\TH:i'))
            ->call('terminateAssignment')
            ->assertHasErrors(['terminateDateTime']);
    }

    /** @test */
    public function it_deletes_assignment()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('openDeleteModal', $assignment->id)
            ->call('deleteAssignment')
            ->assertSet('showDeleteModal', false)
            ->assertDispatched('assignment-deleted');

        // Vérifier la suppression
        $this->assertSoftDeleted($assignment);
    }

    /** @test */
    public function it_duplicates_assignment()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Mission à dupliquer'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('duplicateAssignment', $assignment->id)
            ->assertDispatched('open-assignment-form', function ($event) use ($assignment) {
                return $event['vehicle_id'] === $assignment->vehicle_id &&
                       $event['driver_id'] === $assignment->driver_id &&
                       $event['reason'] === $assignment->reason &&
                       $event['prefill'] === true;
            });
    }

    /** @test */
    public function it_exports_csv()
    {
        // Arrangement
        Assignment::factory(3)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->call('exportCsv')
            ->assertDispatched('download-csv', function ($event) {
                return str_contains($event['filename'], 'affectations_') &&
                       str_contains($event['filename'], '.csv') &&
                       is_array($event['data']) &&
                       count($event['data']) > 1; // En-têtes + données
            });
    }

    /** @test */
    public function it_closes_all_modals()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->set('showCreateModal', true)
            ->set('showEditModal', true)
            ->set('selectedAssignment', $assignment)
            ->call('closeModals')
            ->assertSet('showCreateModal', false)
            ->assertSet('showEditModal', false)
            ->assertSet('showTerminateModal', false)
            ->assertSet('showDeleteModal', false)
            ->assertSet('selectedAssignment', null);
    }

    /** @test */
    public function it_respects_organization_isolation()
    {
        // Arrangement: Affectation dans une autre organisation
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create(['organization_id' => $otherOrganization->id]);
        $otherDriver = Driver::factory()->create(['organization_id' => $otherOrganization->id]);

        $otherAssignment = Assignment::factory()->create([
            'organization_id' => $otherOrganization->id,
            'vehicle_id' => $otherVehicle->id,
            'driver_id' => $otherDriver->id,
        ]);

        $myAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->assertSee($myAssignment->vehicle_display)
            ->assertDontSee($otherAssignment->vehicle_display);
    }

    /** @test */
    public function it_handles_empty_state()
    {
        // Action & Assertion: Aucune affectation
        Livewire::test(AssignmentTable::class)
            ->assertSee('Aucune affectation trouvée')
            ->assertSeeHtml('empty-state'); // État vide avec icône
    }

    /** @test */
    public function it_refreshes_on_livewire_events()
    {
        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->dispatch('assignment-created')
            ->assertSuccessful()
            ->dispatch('assignment-updated')
            ->assertSuccessful();
    }

    /** @test */
    public function it_displays_correct_status_labels()
    {
        // Arrangement: Différents statuts
        $scheduledAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay(),
            'end_datetime' => now()->addDay()->addHours(8)
        ]);

        $activeAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subHour(),
            'end_datetime' => null
        ]);

        $completedAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->subHour()
        ]);

        // Action & Assertion
        Livewire::test(AssignmentTable::class)
            ->assertSee('Programmé')
            ->assertSee('En cours')
            ->assertSee('Terminé');
    }
}