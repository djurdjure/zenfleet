<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\StatusTransitionService;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\StatusHistory;
use App\Models\VehicleStatus;
use App\Models\DriverStatus;
use App\Models\Scopes\UserVehicleAccessScope;
use App\Models\User;
use App\Models\Organization;
use App\Enums\VehicleStatusEnum;
use App\Enums\DriverStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * 🧪 STATUS TRANSITION SERVICE - Unit Tests
 *
 * Tests exhaustifs du service de gestion des transitions de statuts.
 *
 * Couverture:
 * - Transitions valides (happy path)
 * - Transitions invalides (error path)
 * - Validation State Machine
 * - Création historique
 * - Métadonnées JSON
 * - Hooks de callback
 * - Opérations bulk
 * - Gestion erreurs
 *
 * @version 1.0-Enterprise
 */
class StatusTransitionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StatusTransitionService $service;
    protected User $user;
    protected Organization $organization;

    /**
     * Setup avant chaque test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new StatusTransitionService();

        // Créer organisation
        $this->organization = Organization::factory()->create();

        // Créer utilisateur de test
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        Auth::login($this->user);

        // Créer les statuts de base (simuler migrations)
        $this->seedStatuses();
    }

    /**
     * Seed les statuts minimaux pour les tests
     */
    protected function seedStatuses(): void
    {
        // Statuts véhicules
        $vehicleStatuses = [
            ['name' => 'Parking', 'slug' => 'parking'],
            ['name' => 'Affecté', 'slug' => 'affecte'],
            ['name' => 'En panne', 'slug' => 'en_panne'],
            ['name' => 'En maintenance', 'slug' => 'en_maintenance'],
            ['name' => 'Réformé', 'slug' => 'reforme'],
        ];

        foreach ($vehicleStatuses as $status) {
            VehicleStatus::firstOrCreate(
                ['name' => $status['name']],
                ['slug' => $status['slug']]
            );
        }

        // Statuts chauffeurs
        $driverStatuses = [
            ['name' => 'Disponible', 'slug' => 'disponible'],
            ['name' => 'En mission', 'slug' => 'en_mission'],
            ['name' => 'En congé', 'slug' => 'en_conge'],
            ['name' => 'Autre', 'slug' => 'autre'],
        ];

        foreach ($driverStatuses as $status) {
            DriverStatus::firstOrCreate(
                ['name' => $status['name']],
                ['slug' => $status['slug']]
            );
        }
    }

    /**
     * @test
     * Test transition VALIDE de véhicule (PARKING → EN_PANNE)
     */
    public function it_can_change_vehicle_status_with_valid_transition()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        // Act
        $result = $this->service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::EN_PANNE,
            ['reason' => 'Panne moteur']
        );

        // Assert
        $this->assertTrue($result);
        $vehicle->refresh();

        $enPanneStatus = VehicleStatus::where('slug', 'en_panne')->first();
        $this->assertEquals($enPanneStatus->id, $vehicle->status_id);

        // Vérifier l'historique créé
        $this->assertDatabaseHas('status_history', [
            'statusable_type' => Vehicle::class,
            'statusable_id' => $vehicle->id,
            'from_status' => 'parking',
            'to_status' => 'en_panne',
            'reason' => 'Panne moteur',
        ]);
    }

    /**
     * @test
     * Test transition INVALIDE (rejetée par State Machine)
     */
    public function it_rejects_invalid_vehicle_status_transition()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Transition impossible/');

        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        // Act - Essayer transition invalide PARKING → REFORMÉ
        $this->service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::REFORME,
            ['reason' => 'Test invalide']
        );
    }

    /**
     * @test
     * Test création historique avec métadonnées JSON
     */
    public function it_stores_metadata_in_status_history()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        $metadata = [
            'severity' => 'high',
            'estimated_repair_days' => 7,
            'cost_estimate' => 1500.50,
        ];

        // Act
        $this->service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::EN_PANNE,
            [
                'reason' => 'Panne critique moteur',
                'metadata' => $metadata,
            ]
        );

        // Assert
        $history = StatusHistory::where('statusable_id', $vehicle->id)
            ->where('statusable_type', Vehicle::class)
            ->latest('changed_at')
            ->first();

        $this->assertNotNull($history);
        $this->assertEquals($metadata, $history->metadata);
        $this->assertEquals('high', $history->metadata['severity']);
    }

    /**
     * @test
     * Test transaction rollback en cas d'erreur
     */
    public function it_rolls_back_transaction_on_error()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        $initialStatusId = $vehicle->status_id;

        // Forcer une erreur en utilisant un callback qui throw
        try {
            // Act
            $this->service->changeVehicleStatus(
                $vehicle,
                VehicleStatusEnum::EN_PANNE,
                [
                    'reason' => 'Test rollback',
                    'after_transition' => function () {
                        throw new \Exception('Erreur simulée');
                    }
                ]
            );

            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            // Assert
            $vehicle->refresh();
            $this->assertEquals($initialStatusId, $vehicle->status_id, 'Le statut ne devrait pas avoir changé');

            // Vérifier qu'aucun historique n'a été créé
            $historyCount = StatusHistory::where('statusable_id', $vehicle->id)
                ->where('to_status', 'en_panne')
                ->count();

            $this->assertEquals(0, $historyCount, 'Aucun historique ne devrait être créé en cas d\'erreur');
        }
    }

    /**
     * @test
     * Test changement de statut chauffeur
     */
    public function it_can_change_driver_status()
    {
        // Arrange
        $disponibleStatus = DriverStatus::where('slug', 'disponible')->first();
        $driver = Driver::factory()->create([
            'status_id' => $disponibleStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        // Act
        $result = $this->service->changeDriverStatus(
            $driver,
            DriverStatusEnum::EN_MISSION,
            ['reason' => 'Affectation nouveau véhicule']
        );

        // Assert
        $this->assertTrue($result);
        $driver->refresh();

        $enMissionStatus = DriverStatus::where('slug', 'en_mission')->first();
        $this->assertEquals($enMissionStatus->id, $driver->status_id);
    }

    /**
     * @test
     * Test hook before_transition
     */
    public function it_executes_before_transition_hook()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        $hookExecuted = false;

        // Act
        $this->service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::AFFECTE,
            [
                'reason' => 'Test hook',
                'before_transition' => function ($v, $from, $to) use (&$hookExecuted) {
                    $hookExecuted = true;
                    $this->assertEquals(VehicleStatusEnum::PARKING, $from);
                    $this->assertEquals(VehicleStatusEnum::AFFECTE, $to);
                }
            ]
        );

        // Assert
        $this->assertTrue($hookExecuted, 'Le hook before_transition devrait avoir été exécuté');
    }

    /**
     * @test
     * Test opération bulk sur plusieurs véhicules
     */
    public function it_can_bulk_change_vehicle_statuses()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();

        $vehicles = Vehicle::factory()->count(3)->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        $vehicleIds = $vehicles->pluck('id')->toArray();

        // Act
        $results = $this->service->bulkChangeVehicleStatus(
            $vehicleIds,
            VehicleStatusEnum::AFFECTE,
            ['reason' => 'Affectation en masse']
        );

        // Assert
        $this->assertSame(3, $results['success']);
        $this->assertSame(0, $results['failed']);

        // Vérifier que tous les véhicules ont bien changé de statut
        $affecteStatus = VehicleStatus::where('slug', 'affecte')->first();
        foreach ($vehicleIds as $id) {
            $vehicle = Vehicle::withoutGlobalScope(UserVehicleAccessScope::class)->find($id);
            $this->assertEquals($affecteStatus->id, $vehicle->status_id);
        }
    }

    /**
     * @test
     * Test que l'utilisateur connecté est enregistré dans l'historique
     */
    public function it_records_authenticated_user_in_history()
    {
        // Arrange
        $parkingStatus = VehicleStatus::where('slug', 'parking')->first();
        $vehicle = Vehicle::factory()->create([
            'status_id' => $parkingStatus->id,
            'organization_id' => $this->organization->id,
        ]);

        // Act
        $this->service->changeVehicleStatus(
            $vehicle,
            VehicleStatusEnum::EN_PANNE,
            ['reason' => 'Test user tracking']
        );

        // Assert
        $history = StatusHistory::where('statusable_id', $vehicle->id)
            ->latest('changed_at')
            ->first();

        $this->assertEquals($this->user->id, $history->changed_by_user_id);
        $this->assertEquals('manual', $history->change_type);
    }

    /**
     * Cleanup après chaque test
     */
    protected function tearDown(): void
    {
        Auth::logout();
        parent::tearDown();
    }
}
