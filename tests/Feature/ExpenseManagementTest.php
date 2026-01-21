<?php

namespace Tests\Feature;

use App\Models\ExpenseBudget;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Notifications\ExpenseApprovalRequired;
use App\Notifications\BudgetOverrunAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Contracts\PermissionsTeamResolver;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $organization;
    protected $driver;
    protected $manager;
    protected $vehicle;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create([
            'organization_type' => 'enterprise'
        ]);

        $this->driver = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        app(PermissionsTeamResolver::class)->setPermissionsTeamId($this->organization->id);
        Role::firstOrCreate(['name' => 'Chauffeur', 'guard_name' => 'web']);
        $this->driver->assignRole('Chauffeur');

        $this->manager = User::factory()->create([
            'organization_id' => $this->organization->id
        ]);
        Role::firstOrCreate(['name' => 'Gestionnaire Flotte', 'guard_name' => 'web']);
        $this->manager->assignRole('Gestionnaire Flotte');

        $this->vehicle = Vehicle::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->supplier = Supplier::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        Notification::fake();
        Storage::fake('public');
    }

    public function test_can_create_expense_with_automatic_calculations()
    {
        $this->actingAs($this->driver);

        $receiptFile = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->post(route('admin.vehicle-expenses.store'), [
            'vehicle_id' => $this->vehicle->id,
            'supplier_id' => $this->supplier->id,
            'expense_category' => 'fuel',
            'expense_type' => 'carburant_diesel',
            'description' => 'Plein de carburant - 50 litres',
            'amount_ht' => 5000.00,
            'tva_rate' => 19.00,
            'expense_date' => now()->toDateString(),
            'odometer_reading' => 125000,
            'quantity' => 50.0,
            'unit_type' => 'liters',
            'invoice_reference' => 'FCT-2025-001',
            'payment_method' => 'corporate_card',
            'receipt_file' => $receiptFile
        ]);

        $response->assertRedirect();

        $expense = VehicleExpense::latest()->first();

        $this->assertEquals(5000.00, $expense->amount_ht);
        $this->assertEquals(950.00, $expense->tva_amount); // 5000 * 0.19
        $this->assertEquals(5950.00, $expense->total_ttc);
        $this->assertEquals(VehicleExpense::APPROVAL_PENDING, $expense->approval_status);
        $this->assertNotNull($expense->receipt_file_path);
    }

    public function test_expense_requires_approval_above_threshold()
    {
        $expense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'recorded_by' => $this->driver->id,
            'total_ttc' => 50000.00, // Above typical threshold
            'approval_status' => VehicleExpense::APPROVAL_PENDING
        ]);

        // Manager should receive notification
        Notification::assertSentTo(
            [$this->manager],
            ExpenseApprovalRequired::class
        );
    }

    public function test_manager_can_approve_expense()
    {
        $expense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'approval_status' => VehicleExpense::APPROVAL_PENDING,
            'total_ttc' => 25000.00
        ]);

        $this->actingAs($this->manager);

        $response = $this->post(route('admin.vehicle-expenses.approve', $expense), [
            'approval_comments' => 'Dépense justifiée et conforme au budget'
        ]);

        $response->assertJson(['success' => true]);

        $expense->refresh();
        $this->assertEquals(VehicleExpense::APPROVAL_APPROVED, $expense->approval_status);
        $this->assertEquals($this->manager->id, $expense->approved_by);
        $this->assertNotNull($expense->approved_at);
    }

    public function test_manager_can_reject_expense()
    {
        $expense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'approval_status' => VehicleExpense::APPROVAL_PENDING
        ]);

        $this->actingAs($this->manager);

        $response = $this->post(route('admin.vehicle-expenses.reject', $expense), [
            'approval_comments' => 'Facture non conforme - manque informations fiscales'
        ]);

        $response->assertJson(['success' => true]);

        $expense->refresh();
        $this->assertEquals(VehicleExpense::APPROVAL_REJECTED, $expense->approval_status);
        $this->assertNotNull($expense->rejected_at);
    }

    public function test_fuel_efficiency_calculation()
    {
        // Create previous expense for comparison
        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'expense_category' => 'fuel',
            'odometer_reading' => 120000,
            'quantity' => 45.0,
            'unit_type' => 'liters',
            'expense_date' => now()->subDays(5)
        ]);

        $currentExpense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'expense_category' => 'fuel',
            'odometer_reading' => 125000,
            'quantity' => 50.0,
            'unit_type' => 'liters',
            'expense_date' => now()
        ]);

        $efficiency = $currentExpense->calculateFuelEfficiency();

        // Distance: 125000 - 120000 = 5000 km
        // Consumption: 50 liters
        // Efficiency: 5000 / 50 = 100 km/l
        $this->assertEquals(100.0, $efficiency);
    }

    public function test_budget_tracking_and_alerts()
    {
        $budget = ExpenseBudget::factory()->create([
            'organization_id' => $this->organization->id,
            'scope_type' => 'vehicle',
            'scope_id' => $this->vehicle->id,
            'budgeted_amount' => 100000.00,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'warning_threshold' => 80.0,
            'critical_threshold' => 95.0
        ]);

        // Create expense that triggers warning threshold
        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'total_ttc' => 85000.00, // 85% of budget
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'expense_date' => now()
        ]);

        $budget->recalculateSpentAmount();
        $this->assertEquals(85.0, $budget->getUtilizationPercentage());

        // Test budget overrun alert
        VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'total_ttc' => 20000.00, // Total now 105% of budget
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'expense_date' => now()
        ]);

        $budget->recalculateSpentAmount();
        $this->assertTrue($budget->getUtilizationPercentage() > 100);

        Notification::assertSentTo(
            [$this->manager],
            BudgetOverrunAlert::class
        );
    }

    public function test_expense_categorization_and_reporting()
    {
        // Create various expense types
        $expenses = [
            ['category' => 'fuel', 'amount' => 15000.00],
            ['category' => 'maintenance', 'amount' => 25000.00],
            ['category' => 'insurance', 'amount' => 35000.00],
            ['category' => 'fuel', 'amount' => 18000.00],
            ['category' => 'maintenance', 'amount' => 12000.00]
        ];

        foreach ($expenses as $expenseData) {
            VehicleExpense::factory()->create([
                'organization_id' => $this->organization->id,
                'vehicle_id' => $this->vehicle->id,
                'expense_category' => $expenseData['category'],
                'total_ttc' => $expenseData['amount'],
                'expense_date' => now()
            ]);
        }

        $this->actingAs($this->manager);

        // Test expense summary by category
        $response = $this->get(route('admin.vehicle-expenses.summary', [
            'vehicle_id' => $this->vehicle->id,
            'period' => 'current_month'
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_expenses',
            'by_category',
            'trends'
        ]);
    }

    public function test_recurring_expense_management()
    {
        $recurringExpense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'is_recurring' => true,
            'recurring_frequency' => 'monthly',
            'next_occurrence' => now()->addMonth(),
            'expense_category' => 'insurance'
        ]);

        $this->assertTrue($recurringExpense->is_recurring);
        $this->assertEquals('monthly', $recurringExpense->recurring_frequency);

        // Test recurring expense generation
        $nextExpense = $recurringExpense->generateNextRecurrence();

        $this->assertNotNull($nextExpense);
        $this->assertEquals($recurringExpense->expense_category, $nextExpense->expense_category);
        $this->assertEquals($recurringExpense->amount_ht, $nextExpense->amount_ht);
    }

    public function test_expense_export_and_reporting()
    {
        VehicleExpense::factory()->count(20)->create([
            'organization_id' => $this->organization->id,
            'vehicle_id' => $this->vehicle->id,
            'expense_date' => now()->subDays(rand(1, 30))
        ]);

        $this->actingAs($this->manager);

        // Test Excel export
        $response = $this->get(route('admin.vehicle-expenses.export', [
            'format' => 'excel',
            'vehicle_id' => $this->vehicle->id,
            'date_from' => now()->subDays(30)->toDateString(),
            'date_to' => now()->toDateString()
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_payment_tracking_and_due_dates()
    {
        $expense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id,
            'supplier_id' => $this->supplier->id,
            'total_ttc' => 50000.00,
            'approval_status' => VehicleExpense::APPROVAL_APPROVED,
            'payment_status' => VehicleExpense::PAYMENT_PENDING,
            'payment_due_date' => now()->addDays(30)
        ]);

        $this->assertEquals(VehicleExpense::PAYMENT_PENDING, $expense->payment_status);
        $this->assertFalse($expense->isPaymentOverdue());

        // Test payment processing
        $this->actingAs($this->manager);

        $response = $this->post(route('admin.vehicle-expenses.process-payment', $expense), [
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'VIR-2025-001',
            'payment_date' => now()->toDateString()
        ]);

        $response->assertJson(['success' => true]);

        $expense->refresh();
        $this->assertEquals(VehicleExpense::PAYMENT_PAID, $expense->payment_status);
        $this->assertNotNull($expense->payment_date);
    }

    public function test_organization_isolation_in_expenses()
    {
        $otherOrganization = Organization::factory()->create();
        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id
        ]);

        $expense = VehicleExpense::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $this->actingAs($otherUser);
        $response = $this->get(route('admin.vehicle-expenses.show', $expense));
        $response->assertStatus(404);
    }
}
