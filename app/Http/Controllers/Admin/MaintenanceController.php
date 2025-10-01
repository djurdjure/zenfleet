<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\MaintenanceAlert;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceType;
use App\Models\Vehicle;
use App\Jobs\Maintenance\CheckMaintenanceSchedulesJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur principal du module Maintenance Enterprise-Grade
 * Dashboard et fonctionnalités centrales du système de maintenance
 */
class MaintenanceController extends Controller
{
    /**
     * 🎯 Dashboard principal du module maintenance ENTERPRISE-GRADE
     */
    public function dashboard(): View
    {
        try {
            // 🛡️ Validation d'accès enterprise
            if (!$this->validateDashboardAccess()) {
                abort(403, 'Accès non autorisé au dashboard maintenance');
            }

            $organizationId = auth()->user()->organization_id;

            // 📊 Statistiques principales ultra-complètes avec gestion d'erreur
            $stats = $this->getDashboardStats();

        // 🔴 Alertes critiques récentes avec détails complets
        $criticalAlerts = MaintenanceAlert::with([
            'vehicle:id,registration_plate,brand,model',
            'schedule.maintenanceType:id,name,category'
        ])
            ->where('organization_id', $organizationId)
            ->unacknowledged()
            ->where('priority', 'critical')
            ->latest()
            ->limit(10)
            ->get();

        // 📅 Prochaines maintenances (14 prochains jours pour meilleure visibilité)
        $upcomingMaintenance = MaintenanceSchedule::with([
            'vehicle:id,registration_plate,brand,model',
            'maintenanceType:id,name,category'
        ])
            ->where('organization_id', $organizationId)
            ->active()
            ->where('next_due_date', '>=', Carbon::today())
            ->where('next_due_date', '<=', Carbon::today()->addDays(14))
            ->orderBy('next_due_date')
            ->limit(15)
            ->get();

        // 🔄 Opérations en cours actives
        $activeOperations = MaintenanceOperation::with([
            'vehicle:id,registration_plate,brand,model',
            'maintenanceType:id,name,category'
        ])
            ->where('organization_id', $organizationId)
            ->where('status', 'in_progress')
            ->latest()
            ->limit(10)
            ->get();

        // 📊 Données pour les graphiques enterprise
        $chartData = $this->getChartData();

        // 🚨 Ajout des données manquantes pour le dashboard ultra-professionnel - VERSION ENTERPRISE SÉCURISÉE
        // Les données de véhicules sont maintenant calculées dans getDashboardStats() de manière sécurisée

        // 📊 KPIs temps réel
        $realtimeKPIs = [
            'maintenance_efficiency' => $this->calculateMaintenanceEfficiency(),
            'cost_per_vehicle' => $this->calculateCostPerVehicle(),
            'average_downtime' => $this->calculateAverageDowntime(),
            'compliance_rate' => $this->calculateComplianceRate()
        ];

            return view('admin.maintenance.dashboard', compact(
                'stats',
                'criticalAlerts',
                'upcomingMaintenance',
                'activeOperations',
                'chartData',
                'realtimeKPIs'
            ));

        } catch (\Exception $e) {
            // 🚨 Gestion d'erreur enterprise-grade avec fallback
            $errorData = $this->handleDashboardError($e, 'dashboard_main');

            // En cas d'erreur, afficher un dashboard en mode dégradé
            return view('admin.maintenance.dashboard', [
                'stats' => $errorData['fallback_data'],
                'criticalAlerts' => collect([]),
                'upcomingMaintenance' => collect([]),
                'activeOperations' => collect([]),
                'chartData' => [
                    'alertsByPriority' => [],
                    'costEvolution' => [],
                    'maintenanceTypes' => []
                ],
                'realtimeKPIs' => [
                    'maintenance_efficiency' => 0,
                    'cost_per_vehicle' => 0,
                    'average_downtime' => 0,
                    'compliance_rate' => 0
                ],
                'error' => $errorData['message'],
                'fallbackMode' => true
            ]);
        }
    }

    /**
     * Vue d'ensemble du module maintenance
     */
    public function overview(): View
    {
        $organizationId = auth()->user()->organization_id;

        // Métriques avancées
        $metrics = [
            'total_vehicles' => Vehicle::where('organization_id', $organizationId)->count(),
            'active_schedules' => MaintenanceSchedule::where('organization_id', $organizationId)->active()->count(),
            'pending_operations' => MaintenanceOperation::where('organization_id', $organizationId)->where('status', 'planned')->count(),
            'overdue_maintenance' => MaintenanceSchedule::where('organization_id', $organizationId)->overdue()->count(),
        ];

        // Analyse des coûts (derniers 12 mois)
        $costAnalysis = $this->getCostAnalysis();

        // Performance par type de maintenance
        $performanceByType = $this->getPerformanceByType();

        return view('admin.maintenance.overview', compact(
            'metrics',
            'costAnalysis',
            'performanceByType'
        ));
    }

    /**
     * Déclencher manuellement la vérification des planifications
     */
    public function triggerScheduleCheck(): RedirectResponse
    {
        try {
            CheckMaintenanceSchedulesJob::dispatch(auth()->user()->organization_id, true);

            return back()->with('success', 'Vérification des planifications lancée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du lancement de la vérification : ' . $e->getMessage());
        }
    }

    /**
     * Générer manuellement des alertes
     */
    public function generateAlerts(): RedirectResponse
    {
        try {
            $schedules = MaintenanceSchedule::where('organization_id', auth()->user()->organization_id)
                ->active()
                ->get();

            $alertsCreated = 0;
            foreach ($schedules as $schedule) {
                if ($schedule->createAlertIfNeeded()) {
                    $alertsCreated++;
                }
            }

            return back()->with('success', "{$alertsCreated} alerte(s) créée(s) avec succès.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération des alertes : ' . $e->getMessage());
        }
    }

    /**
     * Widget : Résumé des alertes
     */
    public function widgetAlertsSummary(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'total' => MaintenanceAlert::where('organization_id', $organizationId)->count(),
            'unacknowledged' => MaintenanceAlert::where('organization_id', $organizationId)->unacknowledged()->count(),
            'critical' => MaintenanceAlert::where('organization_id', $organizationId)->unacknowledged()->where('priority', 'critical')->count(),
            'today' => MaintenanceAlert::where('organization_id', $organizationId)->whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Widget : Prochaines maintenances
     */
    public function widgetUpcomingMaintenance(): array
    {
        $upcoming = MaintenanceSchedule::with(['vehicle:id,registration_plate', 'maintenanceType:id,name'])
            ->where('organization_id', auth()->user()->organization_id)
            ->active()
            ->where('next_due_date', '>=', Carbon::today())
            ->where('next_due_date', '<=', Carbon::today()->addDays(14))
            ->orderBy('next_due_date')
            ->limit(5)
            ->get();

        return $upcoming->map(function ($schedule) {
            return [
                'vehicle' => $schedule->vehicle->registration_plate,
                'type' => $schedule->maintenanceType->name,
                'due_date' => $schedule->next_due_date->format('d/m/Y'),
                'days_remaining' => $schedule->days_remaining,
                'status' => $schedule->status,
            ];
        })->toArray();
    }

    /**
     * 📊 Obtenir les statistiques du dashboard ENTERPRISE avec KPIs avancés
     */
    private function getDashboardStats(): array
    {
        $organizationId = auth()->user()->organization_id;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return [
            // 🚨 Alertes et surveillance
            'total_alerts' => MaintenanceAlert::where('organization_id', $organizationId)->count(),
            'unacknowledged_alerts' => MaintenanceAlert::where('organization_id', $organizationId)->unacknowledged()->count(),
            'critical_alerts' => MaintenanceAlert::where('organization_id', $organizationId)->unacknowledged()->where('priority', 'critical')->count(),

            // 📅 Planification et maintenance
            'overdue_maintenance' => MaintenanceSchedule::where('organization_id', $organizationId)->overdue()->count(),
            'scheduled_maintenance' => MaintenanceSchedule::where('organization_id', $organizationId)->active()->count(),
            'due_this_week' => MaintenanceSchedule::where('organization_id', $organizationId)
                ->active()
                ->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays(7)])
                ->count(),

            // 🔄 Opérations actives
            'active_operations' => MaintenanceOperation::where('organization_id', $organizationId)->where('status', 'in_progress')->count(),
            'pending_operations' => MaintenanceOperation::where('organization_id', $organizationId)->where('status', 'pending')->count(),
            'completed_this_month' => MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereMonth('completed_date', $currentMonth)
                ->whereYear('completed_date', $currentYear)
                ->count(),

            // 💰 Finances et coûts
            'total_cost_this_month' => MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereMonth('completed_date', $currentMonth)
                ->whereYear('completed_date', $currentYear)
                ->sum('total_cost') ?? 0,
            'average_cost_per_operation' => MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereMonth('completed_date', $currentMonth)
                ->avg('total_cost') ?? 0,

            // 🚗 Flotte et véhicules - VERSION ENTERPRISE SÉCURISÉE ET OPTIMISÉE
            'total_vehicles' => $this->getTotalVehiclesCount($organizationId),
            'vehicles_under_maintenance' => $this->getVehiclesUnderMaintenanceCount($organizationId),

            // 📊 Performance globale
            'maintenance_compliance' => $this->calculateComplianceRate(),
            'efficiency_score' => $this->calculateMaintenanceEfficiency()
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     */
    private function getChartData(): array
    {
        $organizationId = auth()->user()->organization_id;

        // Répartition des alertes par priorité - VERSION ENTERPRISE SÉCURISÉE
        $alertsByPriority = DB::table('maintenance_alerts')
            ->where('maintenance_alerts.organization_id', $organizationId)
            ->where('maintenance_alerts.acknowledged_at', null)
            ->whereNull('maintenance_alerts.deleted_at')
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Évolution des coûts (6 derniers mois)
        $costEvolution = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $cost = DB::table('maintenance_operations')
                ->where('maintenance_operations.organization_id', $organizationId)
                ->where('maintenance_operations.status', 'completed')
                ->whereRaw('EXTRACT(year FROM maintenance_operations.completed_date) = ?', [$month->year])
                ->whereRaw('EXTRACT(month FROM maintenance_operations.completed_date) = ?', [$month->month])
                ->whereNull('maintenance_operations.deleted_at')
                ->sum('total_cost') ?? 0;

            $costEvolution[] = [
                'month' => $month->format('M Y'),
                'cost' => $cost,
            ];
        }

        // Répartition préventif vs correctif - VERSION ENTERPRISE CORRIGÉE
        $maintenanceTypes = DB::table('maintenance_operations')
            ->join('maintenance_types', 'maintenance_operations.maintenance_type_id', '=', 'maintenance_types.id')
            ->where('maintenance_operations.organization_id', $organizationId)
            ->where('maintenance_operations.status', 'completed')
            ->whereRaw('EXTRACT(month FROM maintenance_operations.completed_date) = ?', [Carbon::now()->month])
            ->whereNull('maintenance_operations.deleted_at')
            ->selectRaw('maintenance_types.category, COUNT(*) as count')
            ->groupBy('maintenance_types.category')
            ->pluck('count', 'category')
            ->toArray();

        return [
            'alerts_by_priority' => $alertsByPriority,
            'cost_evolution' => $costEvolution,
            'maintenance_types' => $maintenanceTypes,
        ];
    }

    /**
     * Analyse des coûts
     */
    private function getCostAnalysis(): array
    {
        $organizationId = auth()->user()->organization_id;

        $currentMonth = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereMonth('completed_date', Carbon::now()->month)
            ->sum('total_cost') ?? 0;

        $lastMonth = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereMonth('completed_date', Carbon::now()->subMonth()->month)
            ->sum('total_cost') ?? 0;

        $yearToDate = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereYear('completed_date', Carbon::now()->year)
            ->sum('total_cost') ?? 0;

        return [
            'current_month' => $currentMonth,
            'last_month' => $lastMonth,
            'year_to_date' => $yearToDate,
            'monthly_change' => $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0,
        ];
    }

    /**
     * Performance par type de maintenance
     */
    private function getPerformanceByType(): array
    {
        $organizationId = auth()->user()->organization_id;

        return MaintenanceType::where('organization_id', $organizationId)
            ->withCount(['operations as total_operations' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->withAvg(['operations as avg_cost' => function ($query) {
                $query->where('status', 'completed');
            }], 'total_cost')
            ->withAvg(['operations as avg_duration' => function ($query) {
                $query->where('status', 'completed');
            }], 'duration_minutes')
            ->get()
            ->map(function ($type) {
                return [
                    'name' => $type->name,
                    'category' => $type->category,
                    'total_operations' => $type->total_operations,
                    'avg_cost' => $type->avg_cost ?? 0,
                    'avg_duration' => $type->avg_duration ?? 0,
                ];
            })
            ->toArray();
    }

    /**
     * 📊 Calculer le taux d'efficacité de maintenance
     */
    private function calculateMaintenanceEfficiency(): float
    {
        $organizationId = auth()->user()->organization_id;
        $completedOnTime = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereRaw('completed_date <= scheduled_date')
            ->count();

        $totalCompleted = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->count();

        return $totalCompleted > 0 ? round(($completedOnTime / $totalCompleted) * 100, 1) : 0;
    }

    /**
     * 💰 Calculer le coût moyen par véhicule - ENTERPRISE GRADE SECURED
     */
    private function calculateCostPerVehicle(): float
    {
        try {
            $organizationId = auth()->user()->organization_id;
            $totalCost = MaintenanceOperation::where('organization_id', $organizationId)
                ->where('status', 'completed')
                ->whereMonth('completed_date', Carbon::now()->month)
                ->sum('total_cost') ?? 0;

            $totalVehicles = $this->getTotalVehiclesCount($organizationId);

            return $totalVehicles > 0 ? round($totalCost / $totalVehicles, 2) : 0;
        } catch (\Exception $e) {
            \Log::error('Erreur calcul coût par véhicule', [
                'organization_id' => auth()->user()?->organization_id,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * ⏱️ Calculer le temps d'arrêt moyen
     */
    private function calculateAverageDowntime(): float
    {
        $organizationId = auth()->user()->organization_id;
        $operations = MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'completed')
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes') ?? 0;

        return round($operations / 60, 1); // Convertir en heures
    }

    /**
     * ✅ Calculer le taux de conformité
     */
    private function calculateComplianceRate(): float
    {
        $organizationId = auth()->user()->organization_id;
        $totalSchedules = MaintenanceSchedule::where('organization_id', $organizationId)->count();
        $overdueSchedules = MaintenanceSchedule::where('organization_id', $organizationId)->overdue()->count();

        return $totalSchedules > 0 ? round((($totalSchedules - $overdueSchedules) / $totalSchedules) * 100, 1) : 100;
    }

    /**
     * 🚗 Obtenir le nombre total de véhicules - ENTERPRISE GRADE SECURED
     */
    private function getTotalVehiclesCount(int $organizationId): int
    {
        try {
            return DB::table('vehicles')
                ->where('organization_id', $organizationId)
                ->whereNull('deleted_at')
                ->count();
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul du nombre total de véhicules', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * 🔧 Obtenir le nombre de véhicules en maintenance - ENTERPRISE GRADE SECURED
     */
    private function getVehiclesUnderMaintenanceCount(int $organizationId): int
    {
        try {
            return DB::table('vehicles')
                ->join('maintenance_operations', 'vehicles.id', '=', 'maintenance_operations.vehicle_id')
                ->where('vehicles.organization_id', $organizationId)
                ->where('maintenance_operations.organization_id', $organizationId)
                ->where('maintenance_operations.status', 'in_progress')
                ->whereNull('vehicles.deleted_at')
                ->whereNull('maintenance_operations.deleted_at')
                ->distinct('vehicles.id')
                ->count('vehicles.id');
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul des véhicules en maintenance', [
                'organization_id' => $organizationId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * 🛡️ Validation des données d'entrée enterprise-grade
     */
    private function validateDashboardAccess(): bool
    {
        $user = auth()->user();

        if (!$user || !$user->organization_id) {
            \Log::warning('Tentative d\'accès dashboard maintenance sans organisation', [
                'user_id' => $user?->id,
                'ip' => request()->ip()
            ]);
            return false;
        }

        return true;
    }

    /**
     * 📊 Gestion centralisée des erreurs enterprise-grade
     */
    private function handleDashboardError(\Exception $e, string $context = 'dashboard'): array
    {
        \Log::error("Erreur dashboard maintenance - {$context}", [
            'user_id' => auth()->id(),
            'organization_id' => auth()->user()?->organization_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return [
            'error' => true,
            'message' => 'Une erreur technique est survenue. Veuillez réessayer.',
            'fallback_data' => $this->getFallbackDashboardData()
        ];
    }

    /**
     * 🚨 Données de fallback en cas d'erreur - ENTERPRISE GRADE
     */
    private function getFallbackDashboardData(): array
    {
        return [
            'total_alerts' => 0,
            'unacknowledged_alerts' => 0,
            'critical_alerts' => 0,
            'overdue_maintenance' => 0,
            'scheduled_maintenance' => 0,
            'due_this_week' => 0,
            'active_operations' => 0,
            'pending_operations' => 0,
            'completed_this_month' => 0,
            'total_cost_this_month' => 0,
            'average_cost_per_operation' => 0,
            'total_vehicles' => 0,
            'vehicles_under_maintenance' => 0,
            'maintenance_compliance' => 0,
            'efficiency_score' => 0,
            'fallback_mode' => true
        ];
    }
}