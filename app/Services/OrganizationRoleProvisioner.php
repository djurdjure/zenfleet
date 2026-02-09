<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OrganizationRoleProvisioner
{
    private const GLOBAL_BASELINE_ROLE_NAMES = [
        'Super Admin',
        'Admin',
        'Gestionnaire Flotte',
        'Superviseur',
        'Chauffeur',
    ];

    private const TENANT_BASELINE_ROLE_NAMES = [
        'Admin',
        'Gestionnaire Flotte',
        'Superviseur',
        'Chauffeur',
    ];

    /**
     * Ensure that an organization has all expected roles.
     * Returns a report array with created/synced counts.
     */
    public function ensureRolesForOrganization(Organization $organization, ?int $templateOrganizationId = null): array
    {
        $baselineTemplates = $this->ensureGlobalRoleTemplates();
        $templateRoles = $this->resolveTemplateRoles($organization, $templateOrganizationId);
        $templateRoles->loadMissing('permissions');

        $baselineByName = $baselineTemplates->keyBy('name');
        $templateByName = $templateRoles->keyBy('name');
        $roleNames = collect(self::TENANT_BASELINE_ROLE_NAMES)
            ->merge($templateByName->keys()->reject(fn (string $name) => $name === 'Super Admin'))
            ->unique()
            ->values();

        $created = 0;
        $synced = 0;
        $registrar = app(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();
        $registrar->setPermissionsTeamId($organization->id);

        try {
            foreach ($roleNames as $roleName) {
                /** @var Role|null $templateRole */
                $templateRole = $templateByName->get($roleName) ?? $baselineByName->get($roleName);

                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'organization_id' => $organization->id,
                    'guard_name' => $templateRole?->guard_name ?? 'web',
                ]);

                if ($role->wasRecentlyCreated) {
                    $created++;
                }

                if ($role->permissions()->count() === 0 && $templateRole && $templateRole->permissions()->count() > 0) {
                    $role->syncPermissions($templateRole->permissions);
                    $synced++;
                }
            }
        } finally {
            $registrar->setPermissionsTeamId($previousTeamId);
        }

        $registrar->forgetCachedPermissions();

        return [
            'organization_id' => $organization->id,
            'created' => $created,
            'synced' => $synced,
            'template_source' => $templateRoles->first()?->organization_id,
        ];
    }

    private function ensureGlobalRoleTemplates(): Collection
    {
        $registrar = app(PermissionRegistrar::class);
        $previousTeamId = $registrar->getPermissionsTeamId();
        $registrar->setPermissionsTeamId(null);

        $allPermissions = Permission::query()
            ->where('guard_name', 'web')
            ->get();

        $templates = collect();

        try {
            foreach (self::GLOBAL_BASELINE_ROLE_NAMES as $roleName) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'organization_id' => null,
                    'guard_name' => 'web',
                ]);

                if ($role->permissions()->count() === 0) {
                    $defaultPermissions = $this->resolveDefaultPermissionsForRole($roleName, $allPermissions);
                    if ($defaultPermissions->isNotEmpty()) {
                        $role->syncPermissions($defaultPermissions);
                    }
                }

                $templates->push($role->fresh('permissions'));
            }
        } finally {
            $registrar->setPermissionsTeamId($previousTeamId);
        }

        return $templates;
    }

    private function resolveDefaultPermissionsForRole(string $roleName, Collection $allPermissions): Collection
    {
        if ($roleName === 'Super Admin') {
            return $allPermissions;
        }

        if ($roleName === 'Admin') {
            return $allPermissions
                ->reject(function (Permission $permission) {
                    return str_starts_with($permission->name, 'organizations.')
                        || str_starts_with($permission->name, 'system.');
                })
                ->values();
        }

        if ($roleName === 'Gestionnaire Flotte') {
            $prefixes = [
                'alerts.',
                'analytics.',
                'assignments.',
                'depots.',
                'documents.',
                'driver-sanctions.',
                'drivers.',
                'expenses.',
                'maintenance.',
                'mileage-readings.',
                'reports.',
                'suppliers.',
                'vehicles.',
            ];

            return $this->filterByPrefixes($allPermissions, $prefixes);
        }

        if ($roleName === 'Superviseur') {
            $prefixes = [
                'assignments.',
                'drivers.',
                'maintenance.',
                'mileage-readings.',
                'reports.',
                'vehicles.',
            ];

            $allowedActions = ['view', 'create', 'update', 'status'];

            return $this->filterByPrefixes($allPermissions, $prefixes)
                ->filter(function (Permission $permission) use ($allowedActions) {
                    return collect($allowedActions)->contains(fn (string $action) => str_contains($permission->name, ".{$action}"))
                        || str_ends_with($permission->name, '.manage');
                })
                ->values();
        }

        if ($roleName === 'Chauffeur') {
            $exact = [
                'assignments.view',
                'documents.view',
                'drivers.view',
                'maintenance.view',
                'mileage-readings.create',
                'mileage-readings.view.own',
                'vehicles.view',
            ];

            return $allPermissions
                ->filter(function (Permission $permission) use ($exact) {
                    return in_array($permission->name, $exact, true);
                })
                ->values();
        }

        return collect();
    }

    private function filterByPrefixes(Collection $permissions, array $prefixes): Collection
    {
        return $permissions
            ->filter(function (Permission $permission) use ($prefixes) {
                foreach ($prefixes as $prefix) {
                    if (str_starts_with($permission->name, $prefix)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    /**
     * Ensure roles for all organizations.
     */
    public function ensureRolesForAllOrganizations(?int $templateOrganizationId = null): array
    {
        $reports = [];

        foreach (Organization::orderBy('id')->get() as $organization) {
            $reports[] = $this->ensureRolesForOrganization($organization, $templateOrganizationId);
        }

        return $reports;
    }

    private function resolveTemplateRoles(Organization $organization, ?int $templateOrganizationId = null): Collection
    {
        $templateOrgId = $this->resolveTemplateOrganizationId($organization, $templateOrganizationId);

        if ($templateOrgId) {
            $roles = Role::where('organization_id', $templateOrgId)->get();
            if ($roles->isNotEmpty()) {
                return $roles;
            }
        }

        $globalRoles = Role::whereNull('organization_id')->get();
        if ($globalRoles->isNotEmpty()) {
            return $globalRoles;
        }

        Log::warning('No template roles found; role provisioning will be empty', [
            'organization_id' => $organization->id,
            'template_org_id' => $templateOrganizationId,
        ]);

        return collect();
    }

    private function resolveTemplateOrganizationId(Organization $organization, ?int $templateOrganizationId = null): ?int
    {
        if ($templateOrganizationId && $templateOrganizationId !== $organization->id) {
            return $templateOrganizationId;
        }

        $candidate = Role::whereNotNull('organization_id')
            ->where('organization_id', '!=', $organization->id)
            ->selectRaw('organization_id, COUNT(*) as total')
            ->groupBy('organization_id')
            ->orderByDesc('total')
            ->first();

        return $candidate?->organization_id;
    }
}
