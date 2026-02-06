<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Services\OrganizationRoleProvisioner;
use Illuminate\Console\Command;

class EnsureOrganizationRoles extends Command
{
    protected $signature = 'roles:ensure-organizations {--organization_id=} {--template_org_id=}';

    protected $description = 'Ensure every organization has its own roles (multi-tenant provisioning).';

    public function handle(OrganizationRoleProvisioner $provisioner): int
    {
        $organizationId = $this->option('organization_id');
        $templateOrgId = $this->option('template_org_id');

        if ($organizationId) {
            $organization = Organization::find($organizationId);
            if (!$organization) {
                $this->error("Organization not found: {$organizationId}");
                return self::FAILURE;
            }

            $report = $provisioner->ensureRolesForOrganization($organization, $templateOrgId);
            $this->line("Org #{$report['organization_id']} → created={$report['created']} synced={$report['synced']}");

            return self::SUCCESS;
        }

        $reports = $provisioner->ensureRolesForAllOrganizations($templateOrgId);
        $totalCreated = collect($reports)->sum('created');
        $totalSynced = collect($reports)->sum('synced');

        $this->info("Organizations processed: " . count($reports));
        $this->info("Roles created: {$totalCreated}");
        $this->info("Roles synced: {$totalSynced}");

        return self::SUCCESS;
    }
}
