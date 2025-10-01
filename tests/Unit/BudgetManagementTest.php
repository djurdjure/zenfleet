<?php

namespace Tests\Unit;

use App\Jobs\CheckBudgetOverruns;
use App\Jobs\CheckPaymentsDue;
use App\Models\ExpenseBudget;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Notifications\BudgetOverrunAlert;
use App\Notifications\SupplierPaymentDue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BudgetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $organization;
    protected $manager;
    protected $vehicle;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create([
            'type' => 'enterprise'
        ]);

        $this->manager = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        $this->manager->assignRole('Gestionnaire Flotte');

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Notification::fake();
        Queue::fake();
    }

    public function test_budget_calculation_methods()
    {
        $budget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 100000.00,
            'spent_amount' => 75000.00
        ]);

        $this->assertEquals(25000.00, $budget->remaining_amount);
        $this->assertEquals(75.0, $budget->utilization_percentage);
        $this->assertTrue($budget->isWithinBudget());

        // Test budget overrun
        $budget->update(['spent_amount' => 110000.00]);
        $this->assertEquals(-10000.00, $budget->remaining_amount);
        $this->assertEquals(110.0, $budget->utilization_percentage);
        $this->assertFalse($budget->isWithinBudget());
    }

    public function test_budget_recalculation_from_expenses()
    {
        $budget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'scope_type' => 'vehicle',
            'scope_id' => $this->vehicle->id,
            'budgeted_amount' => 50000.00,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth()
        ]);

        // Create approved expenses within budget period
        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'total_ttc' => 15000.00,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'expense_date' => now()
        ]);

        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'total_ttc' => 22000.00,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'expense_date' => now()->addDays(5)
        ]);

        // Create expense outside budget period (should not count)
        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'total_ttc' => 10000.00,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'expense_date' => now()->subMonth()
        ]);

        $budget->recalculateSpentAmount();

        $this->assertEquals(37000.00, $budget->spent_amount);
        $this->assertEquals(74.0, $budget->utilization_percentage);
    }

    public function test_budget_threshold_alerts()
    {
        $budget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 100000.00,
            'warning_threshold' => 75.0,
            'critical_threshold' => 90.0,
            'spent_amount' => 0.00
        ]);

        // Test warning threshold
        $budget->update(['spent_amount' => 76000.00]);
        $this->assertTrue($budget->isAtWarningThreshold());
        $this->assertFalse($budget->isAtCriticalThreshold());
        $this->assertFalse($budget->isOverBudget());

        // Test critical threshold
        $budget->update(['spent_amount' => 92000.00]);
        $this->assertTrue($budget->isAtWarningThreshold());
        $this->assertTrue($budget->isAtCriticalThreshold());
        $this->assertFalse($budget->isOverBudget());

        // Test budget overrun
        $budget->update(['spent_amount' => 105000.00]);
        $this->assertTrue($budget->isAtWarningThreshold());
        $this->assertTrue($budget->isAtCriticalThreshold());
        $this->assertTrue($budget->isOverBudget());
    }

    public function test_budget_overrun_job_execution()
    {
        // Create budget at warning threshold
        $warningBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 50000.00,
            'spent_amount' => 38000.00, // 76% - above warning threshold
            'warning_threshold' => 75.0,
            'critical_threshold' => 90.0,
            'status' => 'active'
        ]);

        // Create budget at critical threshold
        $criticalBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 100000.00,
            'spent_amount' => 95000.00, // 95% - above critical threshold
            'warning_threshold' => 80.0,
            'critical_threshold' => 90.0,
            'status' => 'active'
        ]);

        // Create overrun budget
        $overrunBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 75000.00,
            'spent_amount' => 82000.00, // 109% - over budget
            'warning_threshold' => 80.0,
            'critical_threshold' => 95.0,
            'status' => 'active'
        ]);

        // Execute budget check job
        $job = new CheckBudgetOverruns();
        $job->handle();

        // Verify notifications were sent
        Notification::assertSentTo(
            $this->manager,
            BudgetOverrunAlert::class,
            function ($notification) {
                return in_array($notification->alertType, ['warning', 'critical', 'overrun']);
            }
        );
    }

    public function test_payment_due_job_execution()
    {
        // Create expense with payment due tomorrow
        $dueTomorrowExpense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'supplier_id' => $this->supplier->id,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'payment_status' => VehicleExpense::PAYMENT_PENDING,
            'payment_due_date' => now()->addDay(),
            'total_ttc' => 25000.00
        ]);

        // Create overdue payment
        $overdueExpense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'supplier_id' => $this->supplier->id,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'payment_status' => VehicleExpense::PAYMENT_PENDING,
            'payment_due_date' => now()->subDays(3),
            'total_ttc' => 45000.00
        ]);

        // Create payment due in a week
        $weekDueExpense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'supplier_id' => $this->supplier->id,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'payment_status' => VehicleExpense::PAYMENT_PENDING,
            'payment_due_date' => now()->addDays(7),
            'total_ttc' => 15000.00
        ]);

        // Execute payment check job
        $job = new CheckPaymentsDue();
        $job->handle();

        // Verify payment due notifications were sent
        Notification::assertSentTo(
            $this->manager,
            SupplierPaymentDue::class
        );
    }

    public function test_budget_scope_filtering()
    {
        // Create organization-wide budget
        $orgBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'scope_type' => 'organization',
            'scope_id' => $this->organization->id,
            'budgeted_amount' => 500000.00
        ]);

        // Create vehicle-specific budget
        $vehicleBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'scope_type' => 'vehicle',
            'scope_id' => $this->vehicle->id,
            'budgeted_amount' => 50000.00
        ]);

        // Create category budget
        $categoryBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'scope_type' => 'category',
            'scope_value' => 'fuel',
            'budgeted_amount' => 100000.00
        ]);

        $this->assertTrue($orgBudget->isOrganizationScope());
        $this->assertTrue($vehicleBudget->isVehicleScope());
        $this->assertTrue($categoryBudget->isCategoryScope());

        $this->assertEquals('organization', $orgBudget->scope_type);
        $this->assertEquals('vehicle', $vehicleBudget->scope_type);
        $this->assertEquals('category', $categoryBudget->scope_type);
    }

    public function test_budget_period_validation()
    {
        $budget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth()
        ]);

        // Test current period
        $this->assertTrue($budget->isCurrentPeriod());

        // Test past period
        $pastBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now()->subMonth()->endOfMonth()
        ]);

        $this->assertFalse($pastBudget->isCurrentPeriod());
        $this->assertTrue($pastBudget->isPastPeriod());

        // Test future period
        $futureBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'period_start' => now()->addMonth()->startOfMonth(),
            'period_end' => now()->addMonth()->endOfMonth()
        ]);

        $this->assertFalse($futureBudget->isCurrentPeriod());
        $this->assertTrue($futureBudget->isFuturePeriod());
    }

    public function test_budget_rollover_functionality()
    {
        $expiredBudget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'budgeted_amount' => 100000.00,
            'spent_amount' => 75000.00,
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now()->subMonth()->endOfMonth(),
            'allow_rollover' => true
        ]);

        $rolloverAmount = $expiredBudget->calculateRolloverAmount();
        $this->assertEquals(25000.00, $rolloverAmount); // Unspent amount

        // Create new budget with rollover
        $newBudget = $expiredBudget->createRolloverBudget([
            'budgeted_amount' => 120000.00,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth()
        ]);

        $this->assertEquals(145000.00, $newBudget->budgeted_amount); // 120000 + 25000 rollover
        $this->assertNotNull($newBudget->rollover_from_budget_id);
        $this->assertEquals($expiredBudget->id, $newBudget->rollover_from_budget_id);
    }
}