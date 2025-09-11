<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use App\Models\PermissionAuditLog;
use App\Models\SupervisorDriverAssignment;
use App\Models\UserVehicleAssignment;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionService
{
    private const CACHE_TTL = 3600; // 1 heure
    private const PERMISSION_MODULES = [
        'dashboard' => ['view'],
        'vehicles' => ['view', 'create', 'edit', 'delete', 'assign', 'track'],
        'drivers' => ['view', 'create', 'edit', 'delete', 'assign'],
        'maintenance' => ['view', 'create', 'edit', 'delete', 'schedule'],
        'trips' => ['view', 'create', 'edit', 'delete', 'assign'],
        'reports' => ['view', 'create', 'export'],
        'suppliers' => ['view', 'create', 'edit', 'delete'],
        'users' => ['view', 'create', 'edit', 'delete', 'invite'],
        'organizations' => ['view', 'edit'], // Super admin uniquement
        'settings' => ['view', 'edit'],
        'audit' => ['view']
    ];

    public function initializeSystemPermissions(): void
    {
        DB::transaction(function () {
            // Créer toutes les permissions système
            foreach (self::PERMISSION_MODULES as $module => $actions) {
                foreach ($actions as $action) {
                    foreach (['organization', 'supervised', 'own'] as $scope) {
                        if ($module === 'organizations' && $scope !== 'organization') {
                            continue; // Organizations n'a que le scope organization
                        }
                        
                        $permissionName = "{$action} {$module}" . ($scope !== 'organization' ? " ({$scope})" : '');
                        
                        Permission::firstOrCreate([
                            'name' => $permissionName,
                            'guard_name' => 'web'
                        ]);
                    }
                }
            }

            $this->createSystemRoles();
        });
    }

    private function createSystemRoles(): void
    {
        // Super Admin - Accès total
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);
        
        $superAdminRole->syncPermissions(Permission::all());

        // Admin Organisation - Accès complet à son organisation
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        
        $adminPermissions = Permission::where('name', 'not like', '%organizations%')
            ->where('name', 'not like', '%(supervised)%')
            ->where('name', 'not like', '%(own)%')
            ->pluck('name')->toArray();
            
        $adminRole->syncPermissions($adminPermissions);

        // Gestionnaire de Flotte
        $fleetManagerRole = Role::firstOrCreate([
            'name' => 'fleet_manager',
            'guard_name' => 'web'
        ]);
        
        $fleetManagerPermissions = [
            'view dashboard',
            'view vehicles', 'create vehicles', 'edit vehicles', 'assign vehicles', 'track vehicles',
            'view drivers', 'create drivers', 'edit drivers', 'assign drivers',
            'view maintenance', 'create maintenance', 'edit maintenance', 'schedule maintenance',
            'view trips', 'create trips', 'edit trips', 'assign trips',
            'view reports', 'create reports', 'export reports',
            'view suppliers', 'create suppliers', 'edit suppliers',
            'view settings'
        ];
        
        $fleetManagerRole->syncPermissions($fleetManagerPermissions);

        // Superviseur - Nouveau rôle
        $supervisorRole = Role::firstOrCreate([
            'name' => 'supervisor',
            'guard_name' => 'web'
        ]);
        
        $supervisorPermissions = [
            'view dashboard',
            'view vehicles (supervised)', 'track vehicles (supervised)',
            'view drivers (supervised)', 'assign drivers (supervised)',
            'view maintenance (supervised)',
            'view trips (supervised)', 'create trips (supervised)', 'assign trips (supervised)',
            'view reports (supervised)', 'create reports (supervised)',
        ];
        
        $supervisorRole->syncPermissions($supervisorPermissions);

        // Chauffeur
        $driverRole = Role::firstOrCreate([
            'name' => 'driver',
            'guard_name' => 'web'
        ]);
        
        $driverPermissions = [
            'view dashboard',
            'view vehicles (own)', 'track vehicles (own)',
            'view trips (own)',
            'view maintenance (own)'
        ];
        
        $driverRole->syncPermissions($driverPermissions);
    }

    public function assignUserToOrganization(User $user, Organization $organization, string $role): bool
    {
        return DB::transaction(function () use ($user, $organization, $role) {
            $user->update(['organization_id' => $organization->id]);
            $user->assignRole($role);
            
            $this->clearUserPermissionsCache($user);
            $this->auditLog($user, 'role_assigned', $user, ['role' => $role]);
            
            return true;
        });
    }

    public function assignSupervisorToDrivers(User $supervisor, Collection $drivers): bool
    {
        if (!$supervisor->hasRole('supervisor')) {
            throw new \InvalidArgumentException('User must have supervisor role');
        }

        return DB::transaction(function () use ($supervisor, $drivers) {
            // Supprimer les anciennes assignations
            SupervisorDriverAssignment::where('supervisor_id', $supervisor->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Créer les nouvelles assignations
            foreach ($drivers as $driver) {
                if ($driver->organization_id !== $supervisor->organization_id) {
                    throw new \InvalidArgumentException('Driver and supervisor must be in same organization');
                }

                SupervisorDriverAssignment::create([
                    'supervisor_id' => $supervisor->id,
                    'driver_id' => $driver->id,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'is_active' => true
                ]);
            }

            $this->clearUserPermissionsCache($supervisor);
            $this->auditLog(auth()->user(), 'supervisor_assignment', $supervisor, [
                'drivers' => $drivers->pluck('id')->toArray()
            ]);

            return true;
        });
    }

    public function assignSupervisorToVehicles(User $supervisor, Collection $vehicles): bool
    {
        if (!$supervisor->hasRole('supervisor')) {
            throw new \InvalidArgumentException('User must have supervisor role');
        }

        return DB::transaction(function () use ($supervisor, $vehicles) {
            // Supprimer les anciennes assignations
            UserVehicleAssignment::where('supervisor_id', $supervisor->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            // Créer les nouvelles assignations
            foreach ($vehicles as $vehicle) {
                if ($vehicle->organization_id !== $supervisor->organization_id) {
                    throw new \InvalidArgumentException('Vehicle and supervisor must be in same organization');
                }

                UserVehicleAssignment::create([
                    'supervisor_id' => $supervisor->id,
                    'vehicle_id' => $vehicle->id,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'is_active' => true
                ]);
            }

            $this->clearUserPermissionsCache($supervisor);
            $this->auditLog(auth()->user(), 'vehicle_assignment', $supervisor, [
                'vehicles' => $vehicles->pluck('id')->toArray()
            ]);

            return true;
        });
    }

    public function canUserAccessResource(User $user, string $permission, $resource = null): bool
    {
        // Super admin a tous les droits
        if ($user->is_super_admin) {
            return true;
        }

        // Vérifier si l'utilisateur a la permission directement
        if ($user->can($permission)) {
            return true;
        }

        // Vérifier les permissions avec scope
        return $this->checkScopedPermission($user, $permission, $resource);
    }

    private function checkScopedPermission(User $user, string $permission, $resource): bool
    {
        // Permission supervised
        $supervisedPermission = str_replace(' ', ' ', $permission) . ' (supervised)';
        if ($user->can($supervisedPermission)) {
            return $this->isResourceSupervised($user, $resource);
        }

        // Permission own
        $ownPermission = str_replace(' ', ' ', $permission) . ' (own)';
        if ($user->can($ownPermission)) {
            return $this->isResourceOwned($user, $resource);
        }

        return false;
    }

    private function isResourceSupervised(User $user, $resource): bool
    {
        if (!$resource) {
            return true;
        }

        if ($resource instanceof User) {
            return SupervisorDriverAssignment::where('supervisor_id', $user->id)
                ->where('driver_id', $resource->id)
                ->where('is_active', true)
                ->exists();
        }

        if ($resource instanceof Vehicle) {
            return UserVehicleAssignment::where('supervisor_id', $user->id)
                ->where('vehicle_id', $resource->id)
                ->where('is_active', true)
                ->exists();
        }

        return false;
    }

    private function isResourceOwned(User $user, $resource): bool
    {
        if (!$resource) {
            return true;
        }

        if ($resource instanceof User) {
            return $resource->id === $user->id;
        }

        if ($resource instanceof Vehicle) {
            return $resource->assigned_driver_id === $user->id;
        }

        return false;
    }

    public function getUserPermissionsCache(User $user): array
    {
        return Cache::remember(
            "user_permissions_{$user->id}",
            self::CACHE_TTL,
            function () use ($user) {
                $permissions = $user->getAllPermissions()->pluck('name')->toArray();
                
                // Ajouter les ressources supervisées
                if ($user->hasRole('supervisor')) {
                    $permissions['supervised_drivers'] = SupervisorDriverAssignment::where('supervisor_id', $user->id)
                        ->where('is_active', true)
                        ->pluck('driver_id')
                        ->toArray();
                        
                    $permissions['supervised_vehicles'] = UserVehicleAssignment::where('supervisor_id', $user->id)
                        ->where('is_active', true)
                        ->pluck('vehicle_id')
                        ->toArray();
                }
                
                return $permissions;
            }
        );
    }

    public function clearUserPermissionsCache(User $user): void
    {
        Cache::forget("user_permissions_{$user->id}");
        
        // Mettre à jour le cache en base
        $user->update([
            'permissions_cache' => $this->getUserPermissionsCache($user)
        ]);
    }

    private function auditLog(User $user, string $action, $resource, array $data = []): void
    {
        PermissionAuditLog::create([
            'user_id' => $user->id,
            'organization_id' => $user->organization_id ?? 1,
            'action' => $action,
            'resource_type' => get_class($resource),
            'resource_id' => $resource->id,
            'new_values' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now()
        ]);
    }

    public function getOrganizationStats(Organization $organization): array
    {
        return [
            'users_count' => $organization->users()->count(),
            'vehicles_count' => $organization->vehicles()->count(),
            'active_trips' => $organization->trips()->where('status', 'active')->count(),
            'pending_maintenance' => $organization->maintenanceRecords()
                ->where('status', 'pending')->count(),
            'roles_distribution' => $organization->users()
                ->with('roles')
                ->get()
                ->groupBy(function ($user) {
                    return $user->roles->first()->name ?? 'no_role';
                })
                ->map(function ($users) {
                    return $users->count();
                })
        ];
    }
}

