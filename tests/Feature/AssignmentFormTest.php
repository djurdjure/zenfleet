<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organization;
use App\Models\User;
use App\Livewire\AssignmentForm;
use App\Services\OverlapCheckService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Mockery;

/**
 * 🧪 Tests fonctionnels du composant AssignmentForm
 *
 * Couvre toutes les fonctionnalités enterprise-grade:
 * - Création et édition d'affectations
 * - Validation temps réel des conflits
 * - Suggestions automatiques de créneaux
 * - Gestion des durées indéterminées
 * - Mode force pour ignorer les conflits
 * - Validation des règles métier
 * - Accessibilité et UX
 */
class AssignmentFormTest extends TestCase
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
            'organization_id' => $this->organization->id,
            'status' => 'active'
        ]);
        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'active'
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_displays_create_form()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->assertSuccessful()
            ->assertSee('Nouvelle affectation')
            ->assertSee('Véhicule')
            ->assertSee('Chauffeur')
            ->assertSee('Date et heure de remise')
            ->assertSet('isEditing', false);
    }

    /** @test */
    public function it_displays_edit_form()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Mission test'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class, ['assignment' => $assignment])
            ->assertSuccessful()
            ->assertSee('Modifier l\'affectation')
            ->assertSet('isEditing', true)
            ->assertSet('vehicle_id', (string) $assignment->vehicle_id)
            ->assertSet('driver_id', (string) $assignment->driver_id)
            ->assertSet('reason', $assignment->reason);
    }

    /** @test */
    public function it_loads_vehicle_and_driver_options()
    {
        // Arrangement: Créer des ressources supplémentaires
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'active'
        ]);
        $otherDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'active'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->assertSuccessful()
            ->assertSee($this->vehicle->registration_plate)
            ->assertSee($otherVehicle->registration_plate)
            ->assertSee($this->driver->first_name . ' ' . $this->driver->last_name)
            ->assertSee($otherDriver->first_name . ' ' . $otherDriver->last_name);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->call('save')
            ->assertHasErrors(['vehicle_id', 'driver_id', 'start_datetime']);
    }

    /** @test */
    public function it_creates_assignment_successfully()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDay()->addHours(8)->format('Y-m-d\TH:i'))
            ->set('reason', 'Mission test')
            ->set('notes', 'Notes de test')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('assignment-created');

        // Vérifier en base
        $this->assertDatabaseHas('assignments', [
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Mission test',
            'notes' => 'Notes de test'
        ]);
    }

    /** @test */
    public function it_updates_assignment_successfully()
    {
        // Arrangement
        $assignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'reason' => 'Ancien motif'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class, ['assignment' => $assignment])
            ->set('reason', 'Nouveau motif')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('assignment-updated');

        // Vérifier la mise à jour
        $assignment->refresh();
        $this->assertEquals('Nouveau motif', $assignment->reason);
    }

    /** @test */
    public function it_validates_conflicts_in_real_time()
    {
        // Arrangement: Créer une affectation existante
        $existingAssignment = Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(9, 0, 0),
            'end_datetime' => now()->addDay()->setTime(17, 0, 0),
        ]);

        // Action & Assertion
        $component = Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->setTime(14, 0, 0)->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDay()->setTime(18, 0, 0)->format('Y-m-d\TH:i'));

        // Déclencher la validation
        $component->call('validateAssignment');

        $component->assertSet('hasConflicts', true)
                  ->assertCount('conflicts', 1);
    }

    /** @test */
    public function it_handles_indefinite_duration_assignments()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('end_datetime', '') // Durée indéterminée
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('assignment-created');

        // Vérifier en base
        $this->assertDatabaseHas('assignments', [
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'end_datetime' => null
        ]);
    }

    /** @test */
    public function it_suggests_next_available_slot()
    {
        // Arrangement: Créer des affectations existantes
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(8, 0, 0),
            'end_datetime' => now()->addDay()->setTime(12, 0, 0),
        ]);

        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(14, 0, 0),
            'end_datetime' => now()->addDay()->setTime(18, 0, 0),
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->call('suggestNextSlot')
            ->assertHasNoErrors()
            ->assertDispatched('slot-suggested');
    }

    /** @test */
    public function it_applies_suggestion()
    {
        // Arrangement: Simuler des suggestions
        $component = Livewire::test(AssignmentForm::class);
        $component->set('suggestions', [
            [
                'start' => now()->addDay()->setTime(12, 0, 0)->format('Y-m-d\TH:i'),
                'end' => now()->addDay()->setTime(16, 0, 0)->format('Y-m-d\TH:i'),
                'description' => 'Créneau libre suggéré'
            ]
        ]);

        // Action & Assertion
        $component->call('applySuggestion', 0)
                  ->assertSet('start_datetime', now()->addDay()->setTime(12, 0, 0)->format('Y-m-d\TH:i'))
                  ->assertSet('end_datetime', now()->addDay()->setTime(16, 0, 0)->format('Y-m-d\TH:i'))
                  ->assertDispatched('suggestion-applied');
    }

    /** @test */
    public function it_enables_force_mode()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->call('toggleForceCreate')
            ->assertSet('forceCreate', true)
            ->assertDispatched('force-mode-enabled')
            ->call('toggleForceCreate')
            ->assertSet('forceCreate', false)
            ->assertDispatched('force-mode-disabled');
    }

    /** @test */
    public function it_saves_with_force_mode_despite_conflicts()
    {
        // Arrangement: Créer un conflit
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(9, 0, 0),
            'end_datetime' => now()->addDay()->setTime(17, 0, 0),
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->setTime(14, 0, 0)->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDay()->setTime(18, 0, 0)->format('Y-m-d\TH:i'))
            ->set('forceCreate', true) // Mode force activé
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('assignment-created');
    }

    /** @test */
    public function it_prevents_save_with_conflicts_in_normal_mode()
    {
        // Arrangement: Créer un conflit
        Assignment::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDay()->setTime(9, 0, 0),
            'end_datetime' => now()->addDay()->setTime(17, 0, 0),
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->setTime(14, 0, 0)->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDay()->setTime(18, 0, 0)->format('Y-m-d\TH:i'))
            ->set('forceCreate', false) // Mode normal
            ->call('save')
            ->assertHasErrors(['business_validation']);
    }

    /** @test */
    public function it_validates_start_date_not_in_past()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->subDay()->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasErrors(['business_validation']);
    }

    /** @test */
    public function it_validates_end_date_after_start_date()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->setTime(17, 0, 0)->format('Y-m-d\TH:i'))
            ->set('end_datetime', now()->addDay()->setTime(9, 0, 0)->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasErrors(['business_validation']);
    }

    /** @test */
    public function it_handles_prefill_from_duplication()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->dispatch('open-assignment-form', [
                'vehicle_id' => $this->vehicle->id,
                'driver_id' => $this->driver->id,
                'start_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
                'reason' => 'Mission dupliquée',
                'prefill' => true
            ])
            ->assertSet('vehicle_id', (string) $this->vehicle->id)
            ->assertSet('driver_id', (string) $this->driver->id)
            ->assertSet('reason', 'Mission dupliquée');
    }

    /** @test */
    public function it_calculates_duration_correctly()
    {
        // Action & Assertion
        $component = Livewire::test(AssignmentForm::class)
            ->set('start_datetime', '2025-01-20T09:00')
            ->set('end_datetime', '2025-01-20T17:00');

        $this->assertEquals(8.0, $component->get('duration_hours'));
        $this->assertEquals('8h', $component->get('formatted_duration'));
    }

    /** @test */
    public function it_shows_indefinite_duration_label()
    {
        // Action & Assertion
        $component = Livewire::test(AssignmentForm::class)
            ->set('start_datetime', '2025-01-20T09:00')
            ->set('end_datetime', '');

        $this->assertNull($component->get('duration_hours'));
        $this->assertEquals('Durée indéterminée', $component->get('formatted_duration'));
    }

    /** @test */
    public function it_excludes_inactive_vehicles_and_drivers()
    {
        // Arrangement: Créer des ressources inactives
        $inactiveVehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'inactive'
        ]);
        $inactiveDriver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'inactive'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->assertSuccessful()
            ->assertSee($this->vehicle->registration_plate)
            ->assertDontSee($inactiveVehicle->registration_plate)
            ->assertSee($this->driver->first_name)
            ->assertDontSee($inactiveDriver->first_name);
    }

    /** @test */
    public function it_respects_organization_isolation()
    {
        // Arrangement: Créer des ressources dans une autre organisation
        $otherOrganization = Organization::factory()->create();
        $otherVehicle = Vehicle::factory()->create([
            'organization_id' => $otherOrganization->id,
            'status' => 'active'
        ]);
        $otherDriver = Driver::factory()->create([
            'organization_id' => $otherOrganization->id,
            'status' => 'active'
        ]);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->assertSuccessful()
            ->assertSee($this->vehicle->registration_plate)
            ->assertDontSee($otherVehicle->registration_plate)
            ->assertSee($this->driver->first_name)
            ->assertDontSee($otherDriver->first_name);
    }

    /** @test */
    public function it_resets_form_after_creation()
    {
        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->set('reason', 'Mission test')
            ->call('save')
            ->assertSet('vehicle_id', '')
            ->assertSet('driver_id', '')
            ->assertSet('reason', '')
            ->assertSet('forceCreate', false);
    }

    /** @test */
    public function it_handles_validation_errors_gracefully()
    {
        // Mock du service pour simuler une erreur
        $mockService = Mockery::mock(OverlapCheckService::class);
        $mockService->shouldReceive('checkOverlap')
                   ->andThrow(new \Exception('Erreur de validation'));

        $this->app->instance(OverlapCheckService::class, $mockService);

        // Action & Assertion
        Livewire::test(AssignmentForm::class)
            ->set('vehicle_id', (string) $this->vehicle->id)
            ->set('driver_id', (string) $this->driver->id)
            ->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'))
            ->call('validateAssignment')
            ->assertHasErrors(['validation']);
    }

    /** @test */
    public function it_updates_validation_on_field_changes()
    {
        // Action & Assertion
        $component = Livewire::test(AssignmentForm::class);

        // Simuler la mise à jour des champs qui déclenche la validation
        $component->set('vehicle_id', (string) $this->vehicle->id);
        $component->assertMethodWasCalled('validateAssignment');

        $component->set('driver_id', (string) $this->driver->id);
        $component->assertMethodWasCalled('validateAssignment');

        $component->set('start_datetime', now()->addDay()->format('Y-m-d\TH:i'));
        $component->assertMethodWasCalled('validateAssignment');
    }
}