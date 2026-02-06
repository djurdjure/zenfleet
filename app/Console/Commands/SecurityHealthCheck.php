<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\OrganizationRoleProvisioner;
use App\Support\PermissionAliases;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SecurityHealthCheck extends Command
{
    protected $signature = 'security:health-check
        {--fix-missing-roles : Auto-provision missing organization roles}
        {--organization_id= : Check a single organization}';

    protected $description = 'Enterprise security health check (RBAC, legacy permissions, tenant role coverage).';

    public function handle(OrganizationRoleProvisioner $provisioner): int
    {
        $organizationId = $this->option('organization_id');

        $legacyPermissions = $this->countLegacyPermissions();
        $duplicatePermissions = $this->countDuplicatePermissions();
        $orphanRolePermissions = $this->countOrphanRolePermissions();
        $orphanUserPermissions = $this->countOrphanUserPermissions();
        $orphanUserRoles = $this->countOrphanUserRoles();

        $missingRolesByOrg = $this->detectMissingRoles($organizationId);
        $missingRolesCount = $missingRolesByOrg->count();

        $this->table(['Metric', 'Count'], [
            ['Legacy permissions', $legacyPermissions],
            ['Duplicate permissions', $duplicatePermissions],
            ['Orphan role permissions', $orphanRolePermissions],
            ['Orphan user permissions', $orphanUserPermissions],
            ['Orphan user roles', $orphanUserRoles],
            ['Organizations missing roles', $missingRolesCount],
        ]);

        if ($missingRolesCount > 0) {
            $this->warn('Organizations with missing roles: ' . $missingRolesByOrg->keys()->implode(', '));
        }

        if ($this->option('fix-missing-roles')) {
            $reports = [];
            $organizations = $organizationId
                ? Organization::where('id', $organizationId)->get()
                : Organization::orderBy('id')->get();

            foreach ($organizations as $organization) {
                if ($missingRolesByOrg->has($organization->id)) {
                    $reports[] = $provisioner->ensureRolesForOrganization($organization);
                }
            }

            if ($reports) {
                $this->info('Auto-provisioning completed.');
                foreach ($reports as $report) {
                    $this->line("Org #{$report['organization_id']} → created={$report['created']} synced={$report['synced']}");
                }
            }
        }

        Log::channel('audit')->info('security.health_check', [
            'legacy_permissions' => $legacyPermissions,
            'duplicate_permissions' => $duplicatePermissions,
            'orphan_role_permissions' => $orphanRolePermissions,
            'orphan_user_permissions' => $orphanUserPermissions,
            'orphan_user_roles' => $orphanUserRoles,
            'organizations_missing_roles' => $missingRolesByOrg->keys()->values()->all(),
        ]);

        return self::SUCCESS;
    }

    private function countLegacyPermissions(): int
    {
        if (!class_exists(PermissionAliases::class)) {
            return 0;
        }

        $legacy = collect(PermissionAliases::legacyMap())->flatten()->unique();

        if ($legacy->isEmpty()) {
            return 0;
        }

        return Permission::whereIn('name', $legacy)->count();
    }

    private function countDuplicatePermissions(): int
    {
        return (int) Permission::query()
            ->select('name', 'guard_name', DB::raw('COUNT(*) as total'))
            ->groupBy('name', 'guard_name')
            ->havingRaw('COUNT(*) > 1')
            ->count();
    }

    private function countOrphanRolePermissions(): int
    {
        return (int) DB::table('role_has_permissions')
            ->leftJoin('roles', 'role_has_permissions.role_id', '=', 'roles.id')
            ->leftJoin('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('roles.id')
            ->orWhereNull('permissions.id')
            ->count();
    }

    private function countOrphanUserPermissions(): int
    {
        return (int) DB::table('model_has_permissions')
            ->leftJoin('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('permissions.id')
            ->count();
    }

    private function countOrphanUserRoles(): int
    {
        return (int) DB::table('model_has_roles')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereNull('roles.id')
            ->count();
    }

    private function detectMissingRoles(?int $organizationId = null): Collection
    {
        $templateRoles = $this->resolveTemplateRoleNames();

        if ($templateRoles->isEmpty()) {
            return collect();
        }

        $organizations = $organizationId
            ? Organization::where('id', $organizationId)->get()
            : Organization::orderBy('id')->get();

        $missing = collect();

        foreach ($organizations as $organization) {
            $orgRoles = Role::where('organization_id', $organization->id)->pluck('name');
            $missingRoles = $templateRoles->diff($orgRoles);
            if ($missingRoles->isNotEmpty()) {
                $missing->put($organization->id, $missingRoles->values());
            }
        }

        return $missing;
    }

    private function resolveTemplateRoleNames(): Collection
    {
        $globalRoles = Role::whereNull('organization_id')->pluck('name');
        if ($globalRoles->isNotEmpty()) {
            return $globalRoles->values();
        }

        $templateOrg = Role::whereNotNull('organization_id')
            ->selectRaw('organization_id, COUNT(*) as total')
            ->groupBy('organization_id')
            ->orderByDesc('total')
            ->first();

        if ($templateOrg) {
            return Role::where('organization_id', $templateOrg->organization_id)->pluck('name')->values();
        }

        return collect();
    }
}
