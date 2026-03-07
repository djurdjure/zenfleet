<?php

namespace App\Services;

use App\Models\ExpenseBudget;
use App\Models\RepairRequest;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AlertCenterService
{
    private const CACHE_TTL_SECONDS = 30;

    public function getDashboardData(int $organizationId, bool $forceRefresh = false): array
    {
        $cacheKey = $this->dashboardCacheKey($organizationId);

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember(
            $cacheKey,
            now()->addSeconds(self::CACHE_TTL_SECONDS),
            fn () => $this->buildDashboardData($organizationId)
        );
    }

    public function getPendingAlertsCount(int $organizationId, bool $forceRefresh = false): int
    {
        $countKey = $this->pendingCountCacheKey($organizationId);

        if ($forceRefresh) {
            Cache::forget($countKey);
            Cache::forget($this->dashboardCacheKey($organizationId));
        }

        return Cache::remember(
            $countKey,
            now()->addSeconds(self::CACHE_TTL_SECONDS),
            function () use ($organizationId) {
                $data = $this->getDashboardData($organizationId);

                return (int) (
                    $data['criticalAlerts']->count()
                    + $data['maintenanceAlerts']->count()
                    + $data['budgetAlerts']->count()
                    + $data['repairAlerts']->count()
                );
            }
        );
    }

    public function clearOrganizationCache(int $organizationId): void
    {
        Cache::forget($this->dashboardCacheKey($organizationId));
        Cache::forget($this->pendingCountCacheKey($organizationId));
    }

    private function dashboardCacheKey(int $organizationId): string
    {
        return "alerts:dashboard:org:{$organizationId}";
    }

    private function pendingCountCacheKey(int $organizationId): string
    {
        return "alerts:pending-count:org:{$organizationId}";
    }

    private function buildDashboardData(int $organizationId): array
    {
        $alerts = $this->getSystemAlerts($organizationId);
        $criticalAlerts = $this->getCriticalAlerts($organizationId);
        $maintenanceAlerts = $this->getMaintenanceAlerts($organizationId);
        $budgetAlerts = $this->getBudgetAlerts($organizationId);
        $repairAlerts = $this->getRepairAlerts($organizationId);

        $stats = [
            'total_alerts' => $alerts->count(),
            'critical_count' => $criticalAlerts->count(),
            'maintenance_count' => $maintenanceAlerts->count(),
            'budget_overruns' => $budgetAlerts->where('type', 'budget_overrun')->count(),
            'pending_repairs' => $repairAlerts->where('status', 'en_attente')->count(),
            'overdue_maintenance' => $maintenanceAlerts->where('priority', 'urgent')->count(),
        ];

        $recentAlerts = collect()
            ->concat($maintenanceAlerts->take(5))
            ->concat($budgetAlerts->take(5))
            ->concat($repairAlerts->take(5))
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return [
            'alerts' => $alerts,
            'criticalAlerts' => $criticalAlerts,
            'maintenanceAlerts' => $maintenanceAlerts,
            'budgetAlerts' => $budgetAlerts,
            'repairAlerts' => $repairAlerts,
            'stats' => $stats,
            'recentAlerts' => $recentAlerts,
        ];
    }

    private function getSystemAlerts(int $organizationId): Collection
    {
        $alerts = collect();

        $overdueMaintenanceCount = DB::table('maintenance_schedules')
            ->where('organization_id', $organizationId)
            ->where('next_due_date', '<', now())
            ->where('is_active', true)
            ->count();

        if ($overdueMaintenanceCount > 0) {
            $alerts->push((object) [
                'id' => 'overdue_maintenance',
                'type' => 'maintenance',
                'priority' => 'urgent',
                'title' => 'Maintenance en retard',
                'message' => "{$overdueMaintenanceCount} opération(s) de maintenance en retard",
                'count' => $overdueMaintenanceCount,
                'icon' => 'alert-triangle',
                'color' => 'red',
                'created_at' => now(),
            ]);
        }

        try {
            if (DB::getSchemaBuilder()->hasTable('expense_budgets')) {
                $budgetQuery = ExpenseBudget::query()->where('organization_id', $organizationId);

                if (DB::getSchemaBuilder()->hasColumn('expense_budgets', 'is_active')) {
                    $budgetQuery->where('is_active', true);
                } elseif (DB::getSchemaBuilder()->hasColumn('expense_budgets', 'status')) {
                    $budgetQuery->where('status', 'active');
                }

                $budgetOverruns = $budgetQuery->whereRaw('spent_amount > budgeted_amount')->count();

                if ($budgetOverruns > 0) {
                    $alerts->push((object) [
                        'id' => 'budget_overrun',
                        'type' => 'budget',
                        'priority' => 'high',
                        'title' => 'Budgets dépassés',
                        'message' => "{$budgetOverruns} budget(s) dépassé(s)",
                        'count' => $budgetOverruns,
                        'icon' => 'wallet',
                        'color' => 'red',
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Budget alerts unavailable', ['error' => $e->getMessage()]);
        }

        try {
            if (DB::getSchemaBuilder()->hasTable('vehicle_expenses')) {
                $overduePayments = DB::table('vehicle_expenses')
                    ->where('organization_id', $organizationId)
                    ->where('payment_due_date', '<', now())
                    ->where('payment_status', '!=', 'paid')
                    ->where('approval_status', 'approved')
                    ->count();

                if ($overduePayments > 0) {
                    $alerts->push((object) [
                        'id' => 'overdue_payments',
                        'type' => 'payment',
                        'priority' => 'high',
                        'title' => 'Paiements en retard',
                        'message' => "{$overduePayments} paiement(s) en retard",
                        'count' => $overduePayments,
                        'icon' => 'credit-card',
                        'color' => 'orange',
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Payment alerts unavailable', ['error' => $e->getMessage()]);
        }

        return $alerts->sortByDesc('priority')->values();
    }

    private function getCriticalAlerts(int $organizationId): Collection
    {
        $criticalAlerts = collect();

        try {
            if (DB::getSchemaBuilder()->hasColumn('vehicles', 'insurance_expiry_date')) {
                $expiredInsurance = Vehicle::where('organization_id', $organizationId)
                    ->where('insurance_expiry_date', '<', now())
                    ->active()
                    ->count();

                if ($expiredInsurance > 0) {
                    $criticalAlerts->push((object) [
                        'id' => 'expired_insurance',
                        'type' => 'vehicle',
                        'priority' => 'critical',
                        'title' => 'Assurances expirées',
                        'message' => "{$expiredInsurance} véhicule(s) avec assurance expirée",
                        'action_required' => true,
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Insurance alerts unavailable', ['error' => $e->getMessage()]);
        }

        try {
            if (DB::getSchemaBuilder()->hasColumn('vehicles', 'technical_inspection_date')) {
                $expiredInspection = Vehicle::where('organization_id', $organizationId)
                    ->where('technical_inspection_date', '<', now())
                    ->active()
                    ->count();

                if ($expiredInspection > 0) {
                    $criticalAlerts->push((object) [
                        'id' => 'expired_inspection',
                        'type' => 'vehicle',
                        'priority' => 'critical',
                        'title' => 'Contrôles techniques expirés',
                        'message' => "{$expiredInspection} véhicule(s) avec contrôle technique expiré",
                        'action_required' => true,
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Technical inspection alerts unavailable', ['error' => $e->getMessage()]);
        }

        return $criticalAlerts->values();
    }

    private function getMaintenanceAlerts(int $organizationId): Collection
    {
        return DB::table('maintenance_schedules')
            ->join('vehicles', 'maintenance_schedules.vehicle_id', '=', 'vehicles.id')
            ->join('maintenance_types', 'maintenance_schedules.maintenance_type_id', '=', 'maintenance_types.id')
            ->where('maintenance_schedules.organization_id', $organizationId)
            ->where('maintenance_schedules.next_due_date', '<=', now()->addDays(7))
            ->where('maintenance_schedules.is_active', true)
            ->select([
                'maintenance_schedules.id',
                'maintenance_types.name as maintenance_type',
                'maintenance_schedules.next_due_date',
                'vehicles.registration_plate',
                'vehicles.brand',
                'vehicles.model',
                DB::raw("CASE
                    WHEN next_due_date < NOW() THEN 'overdue'
                    WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 'high'
                    ELSE 'medium'
                END as alert_priority"),
                DB::raw("CASE
                    WHEN next_due_date < NOW() THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 'high'
                    ELSE 'medium'
                END as priority"),
                DB::raw("'maintenance' as type"),
                'maintenance_schedules.created_at',
            ])
            ->orderByRaw("CASE
                WHEN next_due_date < NOW() THEN 1
                WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 2
                WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 3
                ELSE 4
            END")
            ->orderBy('maintenance_schedules.next_due_date')
            ->get();
    }

    private function getBudgetAlerts(int $organizationId): Collection
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('expense_budgets')) {
                return collect();
            }

            $budgetQuery = ExpenseBudget::query()->where('organization_id', $organizationId);

            if (DB::getSchemaBuilder()->hasColumn('expense_budgets', 'is_active')) {
                $budgetQuery->where('is_active', true);
            } elseif (DB::getSchemaBuilder()->hasColumn('expense_budgets', 'status')) {
                $budgetQuery->where('status', 'active');
            }

            $budgets = $budgetQuery
                ->whereRaw('(spent_amount / NULLIF(budgeted_amount, 0)) * 100 >= warning_threshold')
                ->with(['vehicle'])
                ->get();

            return $budgets
                ->map(function (ExpenseBudget $budget) {
                    $utilization = $budget->utilization_percentage;
                    $isOverBudget = $budget->isOverBudget();
                    $isCritical = $utilization >= $budget->critical_threshold;

                    $type = $isOverBudget
                        ? 'budget_overrun'
                        : ($isCritical ? 'budget_critical' : 'budget_warning');

                    $priority = $isOverBudget
                        ? 'urgent'
                        : ($isCritical ? 'high' : 'medium');

                    $scopeType = $budget->isVehicleScope()
                        ? 'vehicle'
                        : ($budget->isCategoryScope() ? 'category' : 'global');

                    return (object) [
                        'id' => $budget->id,
                        'scope_type' => $scopeType,
                        'scope_description' => $budget->scope_description,
                        'budgeted_amount' => $budget->budgeted_amount,
                        'spent_amount' => $budget->spent_amount,
                        'warning_threshold' => $budget->warning_threshold,
                        'critical_threshold' => $budget->critical_threshold,
                        'utilization_percentage' => $utilization,
                        'type' => $type,
                        'priority' => $priority,
                        'created_at' => $budget->created_at,
                    ];
                })
                ->sortBy(function ($alert) {
                    return $alert->priority === 'urgent'
                        ? 1
                        : ($alert->priority === 'high' ? 2 : 3);
                })
                ->values();
        } catch (\Throwable $e) {
            \Log::warning('Error fetching budget alerts', ['error' => $e->getMessage()]);

            return collect();
        }
    }

    private function getRepairAlerts(int $organizationId): Collection
    {
        return RepairRequest::with(['vehicle', 'driver'])
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['pending', 'supervisor_review', 'fleet_manager_review'])
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByRaw("CASE urgency
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($repair) {
                return (object) [
                    'id' => $repair->id,
                    'type' => 'repair',
                    'priority' => $repair->urgency ?? 'medium',
                    'title' => $repair->title ?? "Demande de réparation #{$repair->id}",
                    'message' => $repair->description,
                    'vehicle' => $repair->vehicle?->registration_plate ?? 'N/A',
                    'status' => $repair->status,
                    'requested_by' => $repair->driver?->name ?? 'N/A',
                    'created_at' => $repair->created_at,
                    'days_pending' => $repair->created_at->diffInDays(now()),
                ];
            })
            ->values();
    }
}
