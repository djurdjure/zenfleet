<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EnterprisePermissionService
{
    private const CACHE_TTL = 3600;
    
    public function initializeEnterpriseSystem(): void
    {
        DB::transaction(function () {
            $this->createEnterprisePermissions();
            $this->createHierarchicalRoles();
            $this->setupDefaultNotifications();
            $this->initializeSubscriptionPlans();
            $this->createSystemDashboards();
        });
    }

    private function createEnterprisePermissions(): void
    {
        $enterpriseModules = [
            'analytics' => [
                'view_basic_analytics', 'view_advanced_analytics', 'export_analytics',
                'create_custom_reports', 'schedule_reports', 'share_reports'
            ],
            'api_management' => [
                'view_api_keys', 'create_api_keys', 'revoke_api_keys',
                'view_api_logs', 'configure_webhooks', 'manage_integrations'
            ],
            'security_management' => [
                'view_audit_logs', 'export_audit_logs', 'configure_security_policies',
                'manage_ip_restrictions', 'force_password_reset', 'manage_2fa_policies'
            ],
            'subscription_management' => [
                'view_billing', 'manage_subscriptions', 'view_usage_metrics',
                'configure_limits', 'manage_addons'
            ],
            'system_administration' => [
                'manage_system_settings', 'view_system_health', 'manage_integrations',
                'configure_notifications', 'manage_templates'
            ]
        ];

        foreach ($enterpriseModules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }
        }
    }

    private function createHierarchicalRoles(): void
    {
        // Super Admin Système (au-dessus de tout)
        $systemAdminRole = Role::firstOrCreate(['name' => 'system_admin', 'guard_name' => 'web']);
        $systemAdminRole->syncPermissions(Permission::all());

        // Super Admin Organisation
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminPermissions = Permission::where('name', 'not like', '%system_%')->pluck('name');
        $superAdminRole->syncPermissions($superAdminPermissions);

        // Admin Organisation Avancé
        $adminRole = Role::firstOrCreate(['name' => 'organization_admin', 'guard_name' => 'web']);
        $adminPermissions = [
            // Gestion complète organisation
            'view_dashboard', 'manage_users', 'manage_roles', 'manage_permissions',
            'view_vehicles', 'create_vehicles', 'edit_vehicles', 'delete_vehicles',
            'view_drivers', 'create_drivers', 'edit_drivers', 'delete_drivers',
            'view_maintenance', 'create_maintenance', 'edit_maintenance', 'delete_maintenance',
            'view_reports', 'create_reports', 'export_reports',
            'view_basic_analytics', 'view_advanced_analytics',
            'view_billing', 'manage_subscriptions',
            'view_audit_logs', 'configure_security_policies'
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Gestionnaire de Flotte Senior
        $seniorFleetManagerRole = Role::firstOrCreate(['name' => 'senior_fleet_manager', 'guard_name' => 'web']);
        $seniorFleetPermissions = [
            'view_dashboard', 'manage_fleet_users',
            'view_vehicles', 'create_vehicles', 'edit_vehicles', 'assign_vehicles',
            'view_drivers', 'create_drivers', 'edit_drivers', 'assign_drivers',
            'view_maintenance', 'create_maintenance', 'edit_maintenance', 'schedule_maintenance',
            'view_assignments', 'create_assignments', 'edit_assignments',
            'view_reports', 'create_reports', 'export_reports',
            'view_basic_analytics', 'create_custom_reports'
        ];
        $seniorFleetManagerRole->syncPermissions($seniorFleetPermissions);

        // Superviseur Avancé (NOUVEAU RÔLE ENRICHI)
        $advancedSupervisorRole = Role::firstOrCreate(['name' => 'advanced_supervisor', 'guard_name' => 'web']);
        $supervisorPermissions = [
            'view_dashboard',
            'view_vehicles_supervised', 'track_vehicles_supervised', 'assign_vehicles_supervised',
            'view_drivers_supervised', 'manage_drivers_supervised', 'assign_drivers_supervised',
            'view_maintenance_supervised', 'create_maintenance_supervised',
            'view_assignments_supervised', 'create_assignments_supervised',
            'view_reports_supervised', 'create_reports_supervised',
            'view_basic_analytics_supervised'
        ];
        $advancedSupervisorRole->syncPermissions($supervisorPermissions);

        // Superviseur Standard
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
        $basicSupervisorPermissions = [
            'view_dashboard',
            'view_vehicles_supervised', 'track_vehicles_supervised',
            'view_drivers_supervised',
            'view_maintenance_supervised',
            'view_assignments_supervised'
        ];
        $supervisorRole->syncPermissions($basicSupervisorPermissions);

        // Chauffeur Premium
        $premiumDriverRole = Role::firstOrCreate(['name' => 'premium_driver', 'guard_name' => 'web']);
        $premiumDriverPermissions = [
            'view_dashboard',
            'view_vehicles_own', 'track_vehicles_own', 'report_vehicle_issues',
            'view_assignments_own', 'update_assignment_status',
            'view_maintenance_own', 'request_maintenance',
            'view_performance_metrics_own'
        ];
        $premiumDriverRole->syncPermissions($premiumDriverPermissions);

        // Chauffeur Standard
        $driverRole = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
        $driverPermissions = [
            'view_dashboard',
            'view_vehicles_own', 'track_vehicles_own',
            'view_assignments_own',
            'view_maintenance_own'
        ];
        $driverRole->syncPermissions($driverPermissions);
    }

    private function initializeSubscriptionPlans(): void
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'tier' => 'trial',
                'description' => 'Essai gratuit 14 jours - Fonctionnalités de base',
                'pricing_structure' => [
                    'type' => 'free',
                    'trial_duration' => 14
                ],
                'base_monthly_price' => 0,
                'base_annual_price' => 0,
                'feature_limits' => [
                    'max_users' => 3,
                    'max_vehicles' => 10,
                    'max_drivers' => 5,
                    'max_api_calls_per_month' => 1000
                ],
                'included_features' => [
                    'basic_fleet_management',
                    'basic_reporting',
                    'email_notifications',
                    'mobile_app_access'
                ],
                'trial_days' => 14,
                'is_public' => true
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'tier' => 'professional',
                'description' => 'Solution complète pour les flottes moyennes',
                'pricing_structure' => [
                    'type' => 'tiered',
                    'base_users' => 10,
                    'additional_user_cost' => 15
                ],
                'base_monthly_price' => 299.00,
                'base_annual_price' => 2990.00,
                'feature_limits' => [
                    'max_users' => 50,
                    'max_vehicles' => 200,
                    'max_drivers' => 100,
                    'max_api_calls_per_month' => 25000
                ],
                'included_features' => [
                    'advanced_fleet_management',
                    'advanced_reporting',
                    'custom_dashboards',
                    'api_access',
                    'advanced_analytics',
                    'webhook_notifications',
                    'priority_support'
                ],
                'is_public' => true,
                'is_recommended' => true
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'tier' => 'enterprise',
                'description' => 'Solution haute performance pour grandes flottes',
                'pricing_structure' => [
                    'type' => 'custom',
                    'requires_quote' => true
                ],
                'base_monthly_price' => 999.00,
                'base_annual_price' => 9990.00,
                'feature_limits' => [
                    'max_users' => null, // Illimité
                    'max_vehicles' => null,
                    'max_drivers' => null,
                    'max_api_calls_per_month' => null
                ],
                'included_features' => [
                    'enterprise_fleet_management',
                    'unlimited_reporting',
                    'custom_integrations',
                    'white_labeling',
                    'advanced_security',
                    'compliance_tools',
                    'dedicated_account_manager',
                    'sla_guarantee'
                ],
                'is_public' => true
            ]
        ];

        foreach ($plans as $planData) {
            DB::table('subscription_plans')->insert(array_merge($planData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }

    public function assignSupervisorWithAdvancedPermissions(User $supervisor, Collection $drivers, Collection $vehicles): bool
    {
        return DB::transaction(function () use ($supervisor, $drivers, $vehicles) {
            // Vérifier les permissions
            if (!$supervisor->hasAnyRole(['supervisor', 'advanced_supervisor'])) {
                throw new \InvalidArgumentException('User must have supervisor role');
            }

            // Enregistrer les assignations avec audit
            $this->logAuditEvent([
                'event_category' => 'security',
                'event_type' => 'supervisor_assignment',
                'event_action' => 'assign',
                'severity_level' => 'medium',
                'event_description' => "Supervisor {$supervisor->name} assigned to manage " . $drivers->count() . " drivers and " . $vehicles->count() . " vehicles",
                'resource_type' => 'User',
                'resource_id' => $supervisor->id
            ]);

            // Assigner les chauffeurs
            foreach ($drivers as $driver) {
                DB::table('supervisor_driver_assignments')->updateOrInsert(
                    [
                        'supervisor_id' => $supervisor->id,
                        'driver_id' => $driver->id
                    ],
                    [
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            // Assigner les véhicules
            foreach ($vehicles as $vehicle) {
                DB::table('user_vehicle_assignments')->updateOrInsert(
                    [
                        'supervisor_id' => $supervisor->id,
                        'vehicle_id' => $vehicle->id
                    ],
                    [
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now(),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            // Invalider le cache des permissions
            $this->clearUserPermissionsCache($supervisor);

            return true;
        });
    }

    private function logAuditEvent(array $eventData): void
    {
        DB::table('comprehensive_audit_logs')->insert(array_merge($eventData, [
            'uuid' => \Illuminate\Support\Str::uuid(),
            'organization_id' => auth()->user()->organization_id ?? 1,
            'user_id' => auth()->id(),
            'event_data' => json_encode($eventData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]));
    }

    private function clearUserPermissionsCache(User $user): void
    {
        Cache::forget("user_permissions_{$user->id}");
        Cache::forget("user_supervised_resources_{$user->id}");
    }

    public function getOrganizationAdvancedMetrics(Organization $organization): array
    {
        return [
            'user_metrics' => [
                'total_users' => $organization->users()->count(),
                'active_users_30d' => $organization->users()
                    ->where('last_activity_at', '>=', now()->subDays(30))
                    ->count(),
                'role_distribution' => $organization->users()
                    ->with('roles')
                    ->get()
                    ->groupBy(function ($user) {
                        return $user->roles->first()->name ?? 'no_role';
                    })
                    ->map->count(),
                'engagement_score' => $this->calculateUserEngagementScore($organization)
            ],
            'fleet_metrics' => [
                'total_vehicles' => $organization->vehicles()->count(),
                'active_vehicles' => $organization->vehicles()
                    ->where('status', 'active')
                    ->count(),
                'utilization_rate' => $this->calculateFleetUtilization($organization),
                'maintenance_efficiency' => $this->calculateMaintenanceEfficiency($organization)
            ],
            'financial_metrics' => [
                'monthly_costs' => $this->calculateMonthlyCosts($organization),
                'cost_per_km' => $this->calculateCostPerKm($organization),
                'roi_analysis' => $this->calculateROI($organization)
            ],
            'compliance_metrics' => [
                'audit_score' => $this->calculateComplianceScore($organization),
                'security_incidents' => $this->getSecurityIncidentsCount($organization),
                'data_protection_status' => $this->getDataProtectionStatus($organization)
            ]
        ];
    }

    private function calculateUserEngagementScore(Organization $organization): float
    {
        $totalUsers = $organization->users()->count();
        if ($totalUsers === 0) return 0;

        $activeUsers = $organization->users()
            ->where('last_activity_at', '>=', now()->subDays(30))
            ->count();

        return round(($activeUsers / $totalUsers) * 100, 2);
    }

    private function calculateFleetUtilization(Organization $organization): float
    {
        // Logique de calcul de l'utilisation de la flotte
        // À adapter selon votre modèle de données
        return 85.5; // Exemple
    }

    private function calculateMaintenanceEfficiency(Organization $organization): float
    {
        // Logique de calcul de l'efficacité de maintenance
        return 92.3; // Exemple
    }

    private function calculateMonthlyCosts(Organization $organization): array
    {
        return [
            'fuel' => 15420.50,
            'maintenance' => 8750.30,
            'insurance' => 2340.00,
            'total' => 26510.80
        ];
    }

    private function calculateCostPerKm(Organization $organization): float
    {
        return 0.45; // Exemple en EUR/km
    }

    private function calculateROI(Organization $organization): float
    {
        return 23.5; // Exemple en pourcentage
    }

    private function calculateComplianceScore(Organization $organization): float
    {
        return 96.7; // Exemple
    }

    private function getSecurityIncidentsCount(Organization $organization): int
    {
        return DB::table('security_alerts')
            ->where('organization_id', $organization->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
    }

    private function getDataProtectionStatus(Organization $organization): string
    {
        return $organization->gdpr_compliant ? 'compliant' : 'pending';
    }
}
