<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Admin\VehicleExpenses\ExpenseAnalytics;
use App\Models\Organization;
use App\Models\User;
use App\Services\ExpenseAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpenseAnalyticsChartsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->actingAs($user);
        $this->app->instance(ExpenseAnalyticsService::class, $this->fakeAnalyticsService());
    }

    #[Test]
    public function it_dispatches_dashboard_refresh_event_after_loading_analytics(): void
    {
        Livewire::test(ExpenseAnalytics::class)
            ->call('loadAnalytics')
            ->assertDispatched('dashboard:data-updated');
    }

    #[Test]
    public function it_renders_unified_chart_widgets_for_all_view_modes(): void
    {
        Livewire::test(ExpenseAnalytics::class)
            ->assertSee('expense-category-pie', false)
            ->assertSee('expense-monthly-trend', false)
            ->assertDontSee('<canvas', false)
            ->set('viewMode', 'tco')
            ->assertSee('expense-tco-vehicles', false)
            ->assertDontSee('<canvas', false)
            ->set('viewMode', 'trends')
            ->assertSee('expense-trends-monthly', false)
            ->assertDontSee('<canvas', false)
            ->set('viewMode', 'suppliers')
            ->assertSee('expense-suppliers-distribution', false)
            ->assertDontSee('<canvas', false)
            ->set('viewMode', 'budgets')
            ->assertSee('expense-budgets-usage', false)
            ->assertDontSee('<canvas', false);
    }

    private function fakeAnalyticsService(): object
    {
        return new class
        {
            public function getDashboardStats(array $filters): array
            {
                return [
                    'total_amount' => 120000.50,
                    'expense_count' => 24,
                    'avg_per_vehicle' => 30000.12,
                    'vehicle_count' => 4,
                    'top_category' => [
                        'name' => 'Carburant',
                        'amount' => 60000,
                    ],
                ];
            }

            public function getCategoryBreakdown(array $filters): array
            {
                return [
                    'categories' => [
                        ['category' => 'fuel', 'total' => 60000],
                        ['category' => 'maintenance', 'total' => 35000],
                    ],
                ];
            }

            public function getVehicleCosts(array $filters): array
            {
                return [
                    [
                        'license_plate' => '123-TEST-16',
                        'brand' => 'Renault',
                        'model' => 'Kangoo',
                        'total_cost' => 42000,
                        'expense_count' => 8,
                    ],
                ];
            }

            public function getTrends(array $filters): array
            {
                return [
                    'periods' => [
                        ['label' => 'Jan', 'total_amount' => 40000, 'expense_count' => 7],
                        ['label' => 'Fev', 'total_amount' => 38000, 'expense_count' => 6],
                        ['label' => 'Mar', 'total_amount' => 42000, 'expense_count' => 8],
                    ],
                    'seasonal_patterns' => [],
                ];
            }

            public function getComplianceScore(array $filters): int
            {
                return 91;
            }

            public function calculateTCO(array $filters): array
            {
                return [
                    'total_tco' => 250000,
                    'avg_tco' => 62500,
                    'avg_cost_per_km' => 21.5,
                    'vehicles' => [
                        ['license_plate' => '123-TEST-16', 'tco' => 70000],
                        ['license_plate' => '456-TEST-31', 'tco' => 65000],
                    ],
                ];
            }

            public function getPredictions(array $filters): array
            {
                return [
                    'monthly' => [
                        'M+1' => ['amount' => 43000, 'confidence' => 84],
                        'M+2' => ['amount' => 44500, 'confidence' => 81],
                        'M+3' => ['amount' => 45250, 'confidence' => 79],
                    ],
                ];
            }

            public function getSupplierAnalysis(array $filters): array
            {
                return [
                    'top_suppliers' => [
                        ['name' => 'TotalEnergies', 'total_amount' => 50000, 'expense_count' => 12, 'payment_delay' => 9],
                        ['name' => 'Midas', 'total_amount' => 28000, 'expense_count' => 6, 'payment_delay' => 16],
                    ],
                ];
            }

            public function analyzeBudgets(array $filters): array
            {
                return [
                    'total_allocated' => 300000,
                    'total_used' => 180000,
                    'total_remaining' => 120000,
                    'groups' => [
                        [
                            'name' => 'Carburant',
                            'budget_allocated' => 180000,
                            'budget_used' => 120000,
                            'usage_percentage' => 66.7,
                        ],
                        [
                            'name' => 'Maintenance',
                            'budget_allocated' => 120000,
                            'budget_used' => 60000,
                            'usage_percentage' => 50.0,
                        ],
                    ],
                ];
            }

            public function getEfficiencyMetrics(array $filters): array
            {
                return [
                    'fuel_efficiency' => 7.8,
                    'maintenance_cost_per_km' => 12.3,
                    'avg_downtime' => 2.1,
                    'maintenance_roi' => 14.9,
                ];
            }

            public function getDriverPerformance(array $filters): array
            {
                return [];
            }
        };
    }
}
