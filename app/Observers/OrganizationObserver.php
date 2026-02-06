<?php

namespace App\Observers;

use App\Models\Organization;
use App\Services\OrganizationRoleProvisioner;
use Illuminate\Support\Facades\Log;

class OrganizationObserver
{
    public function created(Organization $organization): void
    {
        try {
            app(OrganizationRoleProvisioner::class)
                ->ensureRolesForOrganization($organization);
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('organization.roles.provision_failed', [
                'organization_id' => $organization->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
