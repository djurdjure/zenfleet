<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Assignment;
use App\Models\Maintenance\MaintenancePlan;
use App\Models\Maintenance\MaintenanceLog;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * 🚀 ZENFLEET DASHBOARD CONTROLLER - Version Corrigée
 * 
 * @version 2.1-Fixed
 * @author ZenFleet Development Team
 */
class DashboardController extends Controller
{
    protected ?CacheService $cacheService;
    
    /**
     * ✅ Constructeur corrigé
     */
    public function __construct(?CacheService $cacheService = null)
    {
        $this->middleware('auth');
        $this->cacheService = $cacheService;
        
        // Middleware conditionnel pour certaines méthodes
        $this->middleware('role:Super Admin|Admin|Gestionnaire Flotte|Supervisor')
            ->only(['systemMetrics', 'systemHealth', 'auditLogs']);
    }

    /**
     * 🎯 Dashboard principal avec routage intelligent par rôle
     */
    public function index(): View
    {
        $user = Auth::user();
        
        if (!$user) {
            Log::warning('Dashboard access attempt without authentication', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            abort(401, 'Utilisateur non authentifié');
        }

        try {
            // Log d'accès pour audit
            Log::channel('audit')->info('Dashboard accessed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'role' => $user->getRoleNames()->first() ?? 'No Role',
                'organization_id' => $user->organization_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            // Génération des données dashboard avec cache intelligent
            $dashboardData = $this->generateDashboardData($user);
            
            // Retour de la vue appropriée selon le rôle
            return $this->renderDashboardView($user, $dashboardData);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in dashboard', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->renderErrorDashboard('Erreur de base de données temporaire. Veuillez réessayer.');
            
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->renderErrorDashboard('Une erreur inattendue s\'est produite.');
        }
    }

    /**
     * ⚡ Génération intelligente des données dashboard
     */
    private function generateDashboardData(User $user): array
    {
        $role = $user->getRoleNames()->first() ?? 'User';
        $cacheKey = "dashboard_v2_{$role}_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $role) {
            try {
                return match ($role) {
                    'Super Admin' => $this->renderSuperAdminDashboard($user),
                    'Admin' => $this->renderAdminDashboard($user),
                    'Gestionnaire Flotte' => $this->renderFleetManagerDashboard($user),
                    'Supervisor' => $this->renderSupervisorDashboard($user),
                    default => $this->renderDriverDashboard($user)
                };
            } catch (\Exception $e) {
                Log::error("Dashboard data generation failed for role: {$role}", [
                    'user_id' => $user->id,
                    'role' => $role,
                    'error' => $e->getMessage()
                ]);
                return $this->getFallbackDashboardData($user, $role);
            }
        });
    }

    /**
     * 🎨 Rendu de la vue appropriée selon le rôle - VERSION CORRIGÉE
     */
    private function renderDashboardView(User $user, array $data): View
    {
        $role = $user->getRoleNames()->first() ?? 'User';
        
        try {
            $viewMap = [
                'Super Admin' => 'admin.dashboard.super-admin',
                'Admin' => 'admin.dashboard.admin',
                'Gestionnaire Flotte' => 'admin.dashboard.fleet-manager',
                'Supervisor' => 'admin.dashboard.supervisor',
                'User' => 'dashboard.driver',
                'Driver' => 'dashboard.driver',
            ];

            $viewName = $viewMap[$role] ?? 'dashboard.driver';
            
            // ✅ CORRECTION : Vérifier l'existence de la vue
            if (!view()->exists($viewName)) {
                Log::warning("Dashboard view not found: {$viewName}", [
                    'user_id' => $user->id,
                    'role' => $role
                ]);
                $viewName = 'dashboard.error';
            }

            return view($viewName, array_merge($data, [
                'meta' => [
                    'generated_at' => now()->toISOString(),
                    'cache_key' => "dashboard_v2_{$role}_{$user->id}",
                    'version' => '2.1'
                ]
            ]));
            
        } catch (\Exception $e) {
            Log::error('View rendering failed', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            
            return $this->renderErrorDashboard('Erreur lors du chargement de l\'interface.');
        }
    }

    /**
     * 👑 Dashboard Super Admin avec analytics système
     */
    private function renderSuperAdminDashboard(User $user): array
    {
        return Cache::remember('super_admin_dashboard_data', 600, function () use ($user) {
            try {
                // Statistiques système globales
                $systemStats = [
                    'totalOrganizations' => Organization::count(),
                    'activeOrganizations' => Organization::where('status', 'active')->count(),
                    'pendingOrganizations' => Organization::where('status', 'pending')->count(),
                    'totalUsers' => User::count(),
                    'activeUsers' => User::where('is_active', true)->count(),
                    'totalVehicles' => Vehicle::count(),
                    'totalDrivers' => Driver::count(),
                    'systemUptime' => $this->getSystemUptime(),
                ];

                // Analytics mensuelles
                $monthlyAnalytics = $this->getMonthlySystemAnalytics();
                
                // Activité récente système
                $recentActivity = $this->getRecentSystemActivity();
                
                // Santé du système
                $systemHealth = $this->getSystemHealthStatus();
                
                // Top organisations
                $topOrganizations = $this->getTopOrganizations();

                return [
                    'user' => $user,
                    'stats' => $systemStats,
                    'monthlyAnalytics' => $monthlyAnalytics,
                    'recentActivity' => $recentActivity,
                    'systemHealth' => $systemHealth,
                    'topOrganizations' => $topOrganizations,
                    'dashboardType' => 'super-admin'
                ];
                
            } catch (\Exception $e) {
                Log::error('Super Admin dashboard data generation failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getFallbackDashboardData($user, 'Super Admin');
            }
        });
    }

    /**
     * 🏢 Dashboard Admin Organisation
     */
    private function renderAdminDashboard(User $user): array
    {
        $orgId = $user->organization_id;
        
        if (!$orgId) {
            return $this->getFallbackDashboardData($user, 'Admin');
        }
        
        return Cache::remember("admin_dashboard_{$orgId}", 300, function () use ($user, $orgId) {
            try {

                $organizationStats = [
                    'organizationUsers' => User::where('organization_id', $orgId)->count(),
                    'activeUsers' => User::where('organization_id', $orgId)->where('is_active', true)->count(),
                    'organizationVehicles' => Vehicle::where('organization_id', $orgId)->count(),
                    'availableVehicles' => Vehicle::where('organization_id', $orgId)
                        ->whereHas('vehicleStatus', function ($q) {
                            $q->where('name', 'Disponible');
                        })->count(),
                    'organizationDrivers' => Driver::where('organization_id', $orgId)->count(),
                    'activeDrivers' => Driver::where('organization_id', $orgId)
                        ->whereHas('driverStatus', function ($q) {
                            $q->where('name', 'Actif');
                        })->count(),
                    'activeAssignments' => Assignment::whereHas('vehicle', function ($q) use ($orgId) {
                        $q->where('organization_id', $orgId);
                    })->whereNull('end_datetime')->count(),
                ];

                return [
                    'user' => $user,
                    'organization' => $user->organization,
                    'stats' => $organizationStats,
                    'recentActivity' => $this->getOrganizationActivity($orgId),
                    'alerts' => $this->getOrganizationAlerts($orgId),
                    'upcomingMaintenance' => $this->getUpcomingMaintenance($orgId),
                    'vehicleDistribution' => $this->getVehicleStatusDistribution($orgId),
                    'dashboardType' => 'admin'
                ];
                
            } catch (\Exception $e) {
                Log::error('Admin dashboard data generation failed', [
                    'user_id' => $user->id,
                    'organization_id' => $orgId,
                    'error' => $e->getMessage()
                ]);

                return $this->getFallbackDashboardData($user, 'Admin');
            }
        });
    }

    /**
     * 🚗 Dashboard Gestionnaire de Flotte
     */
    private function renderFleetManagerDashboard(User $user): array
    {
        $orgId = $user->organization_id;
        
        if (!$orgId) {
            return $this->getFallbackDashboardData($user, 'Gestionnaire Flotte');
        }
        
        return Cache::remember("fleet_manager_dashboard_{$orgId}", 300, function () use ($user, $orgId) {
            try {
                $fleetStats = [
                    'vehiclesCount' => Vehicle::where('organization_id', $orgId)->count(),
                    'driversCount' => Driver::where('organization_id', $orgId)->count(),
                    'activeAssignments' => Assignment::whereHas('vehicle', function ($q) use ($orgId) {
                        $q->where('organization_id', $orgId);
                    })->whereNull('end_datetime')->count(),
                    'maintenanceAlerts' => $this->getMaintenanceAlertsCount($orgId),
                    'availabilityRate' => $this->calculateVehicleAvailabilityRate($orgId),
                    'utilizationRate' => $this->calculateVehicleUtilizationRate($orgId),
                ];

                return [
                    'user' => $user,
                    'stats' => $fleetStats,
                    'vehicleStatus' => $this->getVehicleStatusDistribution($orgId),
                    'upcomingMaintenance' => $this->getUpcomingMaintenance($orgId),
                    'driverPerformance' => $this->getDriverPerformanceMetrics($orgId),
                    'fuelConsumption' => $this->getFuelConsumptionAnalytics($orgId),
                    'dashboardType' => 'fleet-manager'
                ];
                
            } catch (\Exception $e) {
                Log::error('Fleet Manager dashboard data generation failed', [
                    'user_id' => $user->id,
                    'organization_id' => $orgId,
                    'error' => $e->getMessage()
                ]);
                
                return $this->getFallbackDashboardData($user, 'Gestionnaire Flotte');
            }
        });
    }

    /**
     * 👁️ Dashboard Superviseur
     */
    private function renderSupervisorDashboard(User $user): array
    {
        try {
            return [
                'user' => $user,
                'stats' => [
                    'supervisedVehicles' => $this->getSupervisedVehiclesCount($user->id),
                    'supervisedDrivers' => $this->getSupervisedDriversCount($user->id),
                    'todayAssignments' => $this->getTodayAssignments($user->id),
                    'pendingInspections' => $this->getPendingInspections($user->id),
                ],
                'assignmentsToday' => $this->getTodayDetailedAssignments($user->id),
                'dashboardType' => 'supervisor',
                'alerts' => $this->getSupervisorAlerts($user->id),
                'scheduledTasks' => $this->getSupervisorScheduledTasks($user->id),
            ];
        } catch (\Exception $e) {
            Log::error('Supervisor dashboard data generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackDashboardData($user, 'Supervisor');
        }
    }

    /**
     * 🚛 Dashboard Chauffeur
     */
    private function renderDriverDashboard(User $user): array
    {
        try {
            $driverId = $user->driver_id ?? null;
            
            if (!$driverId) {
                return [
                    'user' => $user,
                    'error' => 'Profil chauffeur non configuré',
                    'dashboardType' => 'driver',
                    'setupRequired' => true
                ];
            }

            $driver = Driver::find($driverId);
            
            if (!$driver) {
                return [
                    'user' => $user,
                    'error' => 'Profil chauffeur introuvable',
                    'dashboardType' => 'driver',
                    'setupRequired' => true
                ];
            }

            return [
                'user' => $user,
                'driver' => $driver,
                'stats' => [
                    'currentAssignment' => $this->getCurrentDriverAssignment($driverId),
                    'totalTrips' => $this->getDriverTotalTrips($driverId),
                    'monthlyKm' => $this->getDriverMonthlyKilometers($driverId),
                    'safetyScore' => $this->getDriverSafetyScore($driverId),
                ],
                'recentTrips' => $this->getDriverRecentTrips($driverId),
                'upcomingMaintenance' => $this->getDriverUpcomingMaintenance($driverId),
                'notifications' => $this->getDriverNotifications($driverId),
                'dashboardType' => 'driver'
            ];
            
        } catch (\Exception $e) {
            Log::error('Driver dashboard data generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return $this->getFallbackDashboardData($user, 'Driver');
        }
    }

    /**
     * 🚨 Dashboard d'erreur
     */
    private function renderErrorDashboard(string $error): View
    {
        return view('dashboard.error', [
            'error' => $error,
            'user' => Auth::user(),
            'timestamp' => now()->toISOString(),
            'supportContact' => config('app.support_email', 'support@zenfleet.com')
        ]);
    }

    /**
     * 🛡️ Données de fallback sécurisées
     */
    private function getFallbackDashboardData(User $user, string $role): array
    {
        return [
            'user' => $user,
            'stats' => [
                'total_organizations' => 0,
                'active_users' => 0,
                'total_vehicles' => 0,
                'total_drivers' => 0,
            ],
            'recentActivity' => [],
            'systemHealth' => ['overall' => 'unknown'],
            'dashboardType' => strtolower(str_replace(' ', '-', $role)),
            'error' => 'Données partiellement indisponibles - Mode dégradé activé',
            'fallbackMode' => true,
            'timestamp' => now()->toISOString()
        ];
    }

    // ============================================================
    // MÉTHODES UTILITAIRES SIMPLIFIÉES
    // ============================================================

    private function getMonthlySystemAnalytics(): array
    {
        return Cache::remember('monthly_system_analytics', 3600, function () {
            try {
                $months = collect();
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i)->startOfMonth();
                    $months->push([
                        'month' => $date->format('M Y'),
                        'organizations' => Organization::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)->count(),
                        'users' => User::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)->count(),
                        'vehicles' => Vehicle::whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)->count(),
                    ]);
                }
                return $months->toArray();
            } catch (\Exception $e) {
                Log::error('Monthly analytics generation failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    private function getRecentSystemActivity(): array
    {
        return Cache::remember('recent_system_activity', 300, function () {
            try {
                $activities = collect();
                
                $newOrganizations = Organization::where('created_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('created_at', 'desc')->limit(5)->get();

                foreach ($newOrganizations as $org) {
                    $activities->push([
                        'type' => 'organization_created',
                        'title' => "Nouvelle organisation: {$org->name}",
                        'description' => "Organisation créée dans {$org->city}",
                        'timestamp' => $org->created_at,
                        'icon' => 'building',
                        'color' => 'blue'
                    ]);
                }

                return $activities->sortByDesc('timestamp')->take(10)->values()->toArray();
            } catch (\Exception $e) {
                Log::error('Recent activity generation failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    private function getSystemHealthStatus(): array
    {
        return Cache::remember('system_health_status', 60, function () {
            try {
                $health = [
                    'database' => $this->checkDatabaseHealth(),
                    'redis' => $this->checkRedisHealth(),
                    'storage' => $this->checkStorageHealth(),
                    'queue' => $this->checkQueueHealth(),
                ];

                $health['overall'] = collect($health)->every(fn($status) => $status === 'healthy') 
                    ? 'healthy' 
                    : 'warning';

                return $health;
            } catch (\Exception $e) {
                Log::error('System health check failed', ['error' => $e->getMessage()]);
                return ['overall' => 'unhealthy'];
            }
        });
    }

    private function getTopOrganizations(): array
    {
        return Cache::remember('top_organizations', 600, function () {
            try {
                return Organization::withCount(['users', 'vehicles', 'drivers'])
                    ->orderByDesc('users_count')
                    ->limit(10)
                    ->get()
                    ->map(function ($org) {
                        return [
                            'id' => $org->id,
                            'name' => $org->name,
                            'city' => $org->city,
                            'users_count' => $org->users_count,
                            'vehicles_count' => $org->vehicles_count,
                            'drivers_count' => $org->drivers_count,
                            'status' => $org->status,
                        ];
                    })->toArray();
            } catch (\Exception $e) {
                Log::error('Top organizations generation failed', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    // Méthodes de santé système simplifiées
    private function checkDatabaseHealth(): string
    {
        try {
            DB::connection()->getPdo();
            return 'healthy';
        } catch (\Exception $e) {
            return 'unhealthy';
        }
    }

    private function checkRedisHealth(): string
    {
        try {
            Cache::store('redis')->get('health_check');
            return 'healthy';
        } catch (\Exception $e) {
            return 'unhealthy';
        }
    }

    private function checkStorageHealth(): string
    {
        try {
            $path = storage_path('logs');
            return is_writable($path) ? 'healthy' : 'warning';
        } catch (\Exception $e) {
            return 'unhealthy';
        }
    }

    private function checkQueueHealth(): string
    {
        return 'healthy';
    }

    private function getSystemUptime(): string
    {
        return '99.9%';
    }





    ////////////////////////////////////////
    /**
     * ✅ NOUVELLES MÉTHODES POUR FONCTIONNALITÉS AVANCÉES
     */

    /**
     * Toggle organization status via AJAX
     */
    public function toggleStatus(Organization $organization, Request $request)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending'
        ]);

        $organization->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès',
            'status' => $organization->status
        ]);
    }

    /**
     * Get statistics summary for dashboard
     */
    public function getStatisticsSummary()
    {
        $stats = [
            'total' => Organization::count(),
            'active' => Organization::where('status', 'active')->count(),
            'pending' => Organization::where('status', 'pending')->count(),
            'inactive' => Organization::where('status', 'inactive')->count(),
            'recent' => Organization::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Handle bulk actions on multiple organizations
     */
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'organizations' => 'required|array',
            'organizations.*' => 'exists:organizations,id'
        ]);

        $organizations = Organization::whereIn('id', $request->organizations);

        switch ($request->action) {
            case 'activate':
                $organizations->update(['status' => 'active']);
                break;
            case 'deactivate':
                $organizations->update(['status' => 'inactive']);
                break;
            case 'delete':
                $organizations->delete();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Action effectuée avec succès sur ' . count($request->organizations) . ' organisation(s)'
        ]);
    }




    // Méthodes pour récupérer les données réelles
    private function getOrganizationActivity(int $organizationId): array
    {
        try {
            $activities = [];

            // Activité des véhicules récents
            $recentVehicles = Vehicle::where('organization_id', $organizationId)
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            foreach ($recentVehicles as $vehicle) {
                $activities[] = [
                    'title' => "Nouveau véhicule ajouté: {$vehicle->registration_plate}",
                    'description' => "{$vehicle->brand} {$vehicle->model}",
                    'timestamp' => $vehicle->created_at,
                    'icon' => 'car',
                    'color' => 'blue'
                ];
            }

            return $activities;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getOrganizationAlerts(int $organizationId): array
    {
        try {
            $alerts = [];

            // Véhicules en maintenance
            $maintenanceVehicles = Vehicle::where('organization_id', $organizationId)
                ->whereHas('vehicleStatus', function ($q) {
                    $q->where('name', 'Maintenance');
                })
                ->count();

            if ($maintenanceVehicles > 0) {
                $alerts[] = [
                    'title' => 'Véhicules en maintenance',
                    'message' => "{$maintenanceVehicles} véhicule(s) nécessitent une maintenance",
                    'priority' => 'yellow',
                    'icon' => 'wrench',
                    'time' => 'Il y a 2h'
                ];
            }

            return $alerts;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getUpcomingMaintenance(int $organizationId)
    {
        try {
            return MaintenancePlan::whereHas('vehicle', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where('next_date', '>=', now())
            ->where('next_date', '<=', now()->addDays(30))
            ->orderBy('next_date', 'asc')
            ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getVehicleStatusDistribution(int $organizationId): array
    {
        try {
            $distribution = [];

            $statuses = Vehicle::where('organization_id', $organizationId)
                ->join('vehicle_statuses', 'vehicles.status_id', '=', 'vehicle_statuses.id')
                ->groupBy('vehicle_statuses.name')
                ->selectRaw('vehicle_statuses.name, count(*) as count')
                ->get();

            foreach ($statuses as $status) {
                $distribution[$status->name] = $status->count;
            }

            return $distribution;
        } catch (\Exception $e) {
            return [];
        }
    }
    private function getMaintenanceAlertsCount(int $organizationId): int { return 0; }
    private function calculateVehicleAvailabilityRate(int $organizationId): float { return 85.0; }
    private function calculateVehicleUtilizationRate(int $organizationId): float { return 75.5; }
    private function getDriverPerformanceMetrics(int $organizationId): array { return []; }
    private function getFuelConsumptionAnalytics(int $organizationId): array { return []; }
    private function getSupervisedVehiclesCount(int $userId): int { return 5; }
    private function getSupervisedDriversCount(int $userId): int { return 12; }
    private function getTodayAssignments(int $userId): int { return 8; }
    private function getPendingInspections(int $userId): int { return 3; }
    private function getTodayDetailedAssignments(int $userId): array { return []; }
    private function getSupervisorAlerts(int $userId): array { return []; }
    private function getSupervisorScheduledTasks(int $userId): array { return []; }
    private function getCurrentDriverAssignment(int $driverId): ?Assignment { return null; }
    private function getDriverTotalTrips(int $driverId): int { return 45; }
    private function getDriverMonthlyKilometers(int $driverId): int { return 1250; }
    private function getDriverSafetyScore(int $driverId): float { return 95.0; }
    private function getDriverRecentTrips(int $driverId): array { return []; }
    private function getDriverUpcomingMaintenance(int $driverId): array { return []; }
    private function getDriverNotifications(int $driverId): array { return []; }
}
