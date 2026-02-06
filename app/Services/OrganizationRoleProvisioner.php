<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class OrganizationRoleProvisioner
{
    /**
     * Ensure that an organization has all expected roles.
     * Returns a report array with created/synced counts.
     */
    public function ensureRolesForOrganization(Organization $organization, ?int $templateOrganizationId = null): array
    {
        $templateRoles = $this->resolveTemplateRoles($organization, $templateOrganizationId);
        $templateByName = $templateRoles->keyBy('name');

        $created = 0;
        $synced = 0;

        foreach ($templateByName as $roleName => $templateRole) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'organization_id' => $organization->id,
                'guard_name' => $templateRole->guard_name ?? 'web',
            ]);

            if ($role->wasRecentlyCreated) {
                $created++;
            }

            if ($role->permissions()->count() === 0 && $templateRole->permissions()->count() > 0) {
                $role->syncPermissions($templateRole->permissions);
                $synced++;
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return [
            'organization_id' => $organization->id,
            'created' => $created,
            'synced' => $synced,
            'template_source' => $templateRoles->first()?->organization_id,
        ];
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
