<?php

namespace Tests\Feature\Assignment;

use App\Livewire\Admin\Assignment\CreateAssignment;
use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 🧪 TESTS ENTERPRISE-GRADE - Module Affectation Véhicule-Chauffeur
 *
 * Couverture complète des cas d'usage:
 * - Création d'affectations ouvertes et planifiées
 * - Détection de conflits véhicule/chauffeur
 * - Validation rétroactive
 * - Isolation multi-tenant
 * - Audit trail
 * - Override administrateur
 * - Règles de validation
 */
class CreateAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected Organization $anotherOrganization;
    protected User $adminUser;
    protected User $superAdminUser;
    protected User $regularUser;
    protected Vehicle $vehicle;
    protected Vehicle $anotherOrgVehicle;
    protected Driver $driver;
    protected Driver $anotherOrgDriver;
    protected VehicleStatus $availableStatus;
    protected DriverStatus $activeDriverStatus;

    /**
     * Configuration initiale avant chaque test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Création des organisations
        $this->organization = Organization::factory()->create(['name' => 'Test Org 1']);
        $this->anotherOrganization = Organization::factory()->create(['name' => 'Test Org 2']);

        // Création des utilisateurs avec différents rôles
        $this->adminUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->adminUser->assignRole('Admin');

        $this->superAdminUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->superAdminUser->assignRole('Super Admin');

        $this->regularUser = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        // Création des statuts
        $this->availableStatus = VehicleStatus::firstOrCreate(
            ['name' => 'Disponible', 'organization_id' => null],
            ['is_active' => true, 'color' => '#10B981']
        );

        $this->activeDriverStatus = DriverStatus::firstOrCreate(
            ['name' => 'Actif', 'organization_id' => null],
            ['is_active' => true, 'color' => '#10B981']
        );

        // Création des véhicules
        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'registration_plate' => 'ABC-123',
            'vehicle_status_id' => $this->availableStatus->id,
            'current_mileage' => 10000,
        ]);

        $this->anotherOrgVehicle = Vehicle::factory()->create([
            'organization_id' => $this->anotherOrganization->id,
            'registration_plate' => 'XYZ-789',
        ]);

        // Création des chauffeurs
        $this->driver = Driver::factory()->create([
            'organization_id' => $this->organization->id,
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'driver_status_id' => $this->activeDriverStatus->id,
        ]);

        $this->anotherOrgDriver = Driver::factory()->create([
            'organization_id' => $this->anotherOrganization->id,
            'first_name' => 'Marie',
            'last_name' => 'Martin',
        ]);
    }

    /**
     * TEST 1: Création d'une affectation ouverte (sans date de fin)
     */
    public function test_can_create_open_assignment(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->set('notes', 'Affectation longue durée')
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.assignments.index'));

        $this->assertDatabaseHas('assignments', [
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_mileage' => 10000,
            'end_datetime' => null,
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);
    }

    /**
     * TEST 2: Création d'une affectation planifiée (avec date de fin)
     */
    public function test_can_create_scheduled_assignment(): void
    {
        Auth::login($this->adminUser);

        $startDate = now()->addDays(2);
        $endDate = now()->addDays(5);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', $startDate->format('Y-m-d'))
            ->set('start_time', '09:00')
            ->set('end_date', $endDate->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 10000)
            ->set('end_mileage', 10500)
            ->set('notes', 'Mission de 3 jours')
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.assignments.index'));

        $this->assertDatabaseHas('assignments', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'status' => Assignment::STATUS_SCHEDULED,
            'start_mileage' => 10000,
            'end_mileage' => 10500,
        ]);

        $assignment = Assignment::where('vehicle_id', $this->vehicle->id)->first();
        $this->assertNotNull($assignment->end_datetime);
    }

    /**
     * TEST 3: Détection de conflit véhicule
     */
    public function test_detects_vehicle_conflict(): void
    {
        Auth::login($this->adminUser);

        // Création d'une affectation existante
        Assignment::create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(1),
            'end_datetime' => now()->addDays(3),
            'start_mileage' => 10000,
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Tentative de création d'une affectation qui chevauche
        $component = Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDays(4)->format('Y-m-d'))
            ->set('end_time', '10:00')
            ->set('start_mileage', 10000)
            ->call('checkConflicts');

        $component->assertSet('has_conflicts', true);
        $this->assertNotEmpty($component->get('conflicts'));

        // Vérifier que le conflit concerne le véhicule
        $conflicts = $component->get('conflicts');
        $this->assertStringContainsString('Véhicule', $conflicts[0]['resource']);
    }

    /**
     * TEST 4: Détection de conflit chauffeur
     */
    public function test_detects_driver_conflict(): void
    {
        Auth::login($this->adminUser);

        // Création d'un deuxième véhicule disponible
        $vehicle2 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'registration_plate' => 'DEF-456',
            'vehicle_status_id' => $this->availableStatus->id,
        ]);

        // Affectation existante du chauffeur
        Assignment::create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $vehicle2->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(1),
            'end_datetime' => now()->addDays(3),
            'start_mileage' => 5000,
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Tentative d'affecter le même chauffeur à un autre véhicule
        $component = Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDays(4)->format('Y-m-d'))
            ->set('end_time', '10:00')
            ->set('start_mileage', 10000)
            ->call('checkConflicts');

        $component->assertSet('has_conflicts', true);

        $conflicts = $component->get('conflicts');
        $this->assertStringContainsString('Chauffeur', $conflicts[0]['resource']);
    }

    /**
     * TEST 5: Validation de cohérence du kilométrage
     */
    public function test_validates_mileage_consistency(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('end_date', now()->addDays(1)->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 10000)
            ->set('end_mileage', 9000) // Kilométrage de fin < début
            ->call('create')
            ->assertHasErrors(['end_mileage']);
    }

    /**
     * TEST 6: Isolation multi-tenant stricte
     */
    public function test_respects_multi_tenant_isolation(): void
    {
        Auth::login($this->adminUser);

        // Tentative d'utiliser un véhicule d'une autre organisation
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->anotherOrgVehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 5000)
            ->call('create')
            ->assertHasErrors(['vehicle_id']);

        // Tentative d'utiliser un chauffeur d'une autre organisation
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->anotherOrgDriver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->call('create')
            ->assertHasErrors(['driver_id']);
    }

    /**
     * TEST 7: Validation de planification rétroactive (autorisée)
     */
    public function test_allows_retroactive_assignment_when_enabled(): void
    {
        Auth::login($this->adminUser);

        // Avec allow_retroactive = true
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->subDays(5)->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('end_date', now()->subDays(3)->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 9500)
            ->set('end_mileage', 9800)
            ->set('reason', 'Correction oubli affectation')
            ->set('allow_retroactive', true)
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.assignments.index'));

        $this->assertDatabaseHas('assignments', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_mileage' => 9500,
        ]);
    }

    /**
     * TEST 8: Blocage de planification rétroactive (non autorisée)
     */
    public function test_blocks_retroactive_assignment_when_disabled(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->subDays(5)->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('end_date', now()->subDays(3)->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 9500)
            ->set('allow_retroactive', false) // Non autorisé
            ->call('create')
            ->assertHasErrors(['start_date']);
    }

    /**
     * TEST 9: Override administrateur en cas de conflit
     */
    public function test_admin_can_override_conflicts(): void
    {
        Auth::login($this->adminUser);

        // Création d'une affectation existante
        Assignment::create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(1),
            'end_datetime' => now()->addDays(3),
            'start_mileage' => 10000,
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Admin force la création malgré le conflit
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('start_time', '10:00')
            ->set('end_date', now()->addDays(4)->format('Y-m-d'))
            ->set('end_time', '10:00')
            ->set('start_mileage', 10100)
            ->set('force_create', true) // Force l'override
            ->call('create')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.assignments.index'));

        // Vérifier qu'il y a bien 2 affectations qui se chevauchent
        $this->assertEquals(2, Assignment::where('vehicle_id', $this->vehicle->id)->count());
    }

    /**
     * TEST 10: Audit trail complet (created_by, updated_by)
     */
    public function test_tracks_audit_trail(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->call('create');

        $assignment = Assignment::where('vehicle_id', $this->vehicle->id)->first();

        $this->assertEquals($this->adminUser->id, $assignment->created_by);
        $this->assertNotNull($assignment->created_at);
        $this->assertNotNull($assignment->updated_at);
    }

    /**
     * TEST 11: Validation des champs obligatoires
     */
    public function test_validates_required_fields(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->call('create')
            ->assertHasErrors([
                'vehicle_id',
                'driver_id',
                'start_date',
                'start_time',
                'start_mileage',
                'assignment_type',
            ]);
    }

    /**
     * TEST 12: Date de fin doit être après date de début
     */
    public function test_validates_end_date_after_start_date(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('end_date', now()->addDays(2)->format('Y-m-d')) // Date de fin avant début
            ->set('end_time', '17:00')
            ->set('start_mileage', 10000)
            ->call('create')
            ->assertHasErrors(['end_date']);
    }

    /**
     * TEST 13: Computed properties - availableVehicles
     */
    public function test_loads_available_vehicles_for_organization(): void
    {
        Auth::login($this->adminUser);

        $component = Livewire::test(CreateAssignment::class);

        $availableVehicles = $component->get('availableVehicles');

        // Doit inclure le véhicule de notre organisation
        $this->assertTrue($availableVehicles->contains('id', $this->vehicle->id));

        // Ne doit PAS inclure le véhicule d'une autre organisation
        $this->assertFalse($availableVehicles->contains('id', $this->anotherOrgVehicle->id));
    }

    /**
     * TEST 14: Computed properties - availableDrivers
     */
    public function test_loads_available_drivers_for_organization(): void
    {
        Auth::login($this->adminUser);

        $component = Livewire::test(CreateAssignment::class);

        $availableDrivers = $component->get('availableDrivers');

        // Doit inclure le chauffeur de notre organisation
        $this->assertTrue($availableDrivers->contains('id', $this->driver->id));

        // Ne doit PAS inclure le chauffeur d'une autre organisation
        $this->assertFalse($availableDrivers->contains('id', $this->anotherOrgDriver->id));
    }

    /**
     * TEST 15: Vérification du statut assigné correctement
     */
    public function test_sets_correct_status_based_on_assignment_type(): void
    {
        Auth::login($this->adminUser);

        // Affectation ouverte = STATUS_ACTIVE
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->call('create');

        $openAssignment = Assignment::where('vehicle_id', $this->vehicle->id)->first();
        $this->assertEquals(Assignment::STATUS_ACTIVE, $openAssignment->status);

        // Création d'un autre véhicule pour la deuxième affectation
        $vehicle2 = Vehicle::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_status_id' => $this->availableStatus->id,
        ]);

        // Affectation planifiée = STATUS_SCHEDULED
        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $vehicle2->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('start_time', '09:00')
            ->set('end_date', now()->addDays(7)->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 5000)
            ->call('create');

        $scheduledAssignment = Assignment::where('vehicle_id', $vehicle2->id)->first();
        $this->assertEquals(Assignment::STATUS_SCHEDULED, $scheduledAssignment->status);
    }

    /**
     * TEST 16: Pas de conflit si dates ne se chevauchent pas
     */
    public function test_no_conflict_when_dates_do_not_overlap(): void
    {
        Auth::login($this->adminUser);

        // Affectation du 1er au 3
        Assignment::create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'start_datetime' => now()->addDays(1),
            'end_datetime' => now()->addDays(3),
            'start_mileage' => 10000,
            'status' => Assignment::STATUS_ACTIVE,
            'created_by' => $this->adminUser->id,
        ]);

        // Nouvelle affectation du 5 au 7 (pas de chevauchement)
        $component = Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('end_date', now()->addDays(7)->format('Y-m-d'))
            ->set('end_time', '17:00')
            ->set('start_mileage', 10100)
            ->call('checkConflicts');

        $component->assertSet('has_conflicts', false);
    }

    /**
     * TEST 17: Affectation ouverte ne nécessite pas end_date/end_time
     */
    public function test_open_assignment_does_not_require_end_date(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'open')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->set('end_date', null) // Pas de date de fin
            ->set('end_time', null)
            ->call('create')
            ->assertHasNoErrors(['end_date', 'end_time'])
            ->assertRedirect(route('admin.assignments.index'));
    }

    /**
     * TEST 18: Affectation planifiée nécessite end_date/end_time
     */
    public function test_scheduled_assignment_requires_end_date(): void
    {
        Auth::login($this->adminUser);

        Livewire::test(CreateAssignment::class)
            ->set('vehicle_id', $this->vehicle->id)
            ->set('driver_id', $this->driver->id)
            ->set('assignment_type', 'scheduled')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('start_time', '08:00')
            ->set('start_mileage', 10000)
            ->set('end_date', null) // Manquant
            ->set('end_time', null)
            ->call('create')
            ->assertHasErrors(['end_date', 'end_time']);
    }
}
