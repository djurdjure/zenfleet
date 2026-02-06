<?php

namespace App\Console\Commands;

use App\Support\PermissionAliases;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class AuditPermissions extends Command
{
    protected $signature = 'permissions:audit {--json : Output JSON report}';

    protected $description = 'Audit RBAC integrity (legacy permissions, orphan pivots, duplicates).';

    public function handle(): int
    {
        $legacyNames = collect(PermissionAliases::legacyMap())
            ->values()
            ->flatten()
            ->unique()
            ->values();

        $legacyPermissions = Permission::whereIn('name', $legacyNames)->get(['id', 'name', 'guard_name']);
        $legacyCount = $legacyPermissions->count();

        $orphansRole = DB::table(config('permission.table_names.role_has_permissions'))
            ->leftJoin(config('permission.table_names.permissions'), 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('permissions.id')
            ->count();

        $orphansUser = DB::table(config('permission.table_names.model_has_permissions'))
            ->leftJoin(config('permission.table_names.permissions'), 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->whereNull('permissions.id')
            ->count();

        $duplicates = Permission::select('name', 'guard_name', DB::raw('count(*) as total'))
            ->groupBy('name', 'guard_name')
            ->havingRaw('count(*) > 1')
            ->get();

        $report = [
            'legacy_permissions' => $legacyCount,
            'orphan_role_permissions' => $orphansRole,
            'orphan_user_permissions' => $orphansUser,
            'duplicate_permissions' => $duplicates->count(),
        ];

        Log::channel('audit')->info('permissions.audit', $report);

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->info('Permissions audit');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Legacy permissions', $legacyCount],
                    ['Orphan role permissions', $orphansRole],
                    ['Orphan user permissions', $orphansUser],
                    ['Duplicate permissions', $duplicates->count()],
                ]
            );
        }

        if ($legacyCount > 0) {
            $this->warn('Legacy permissions detected. Normalize the database before removing legacy support.');
        }

        return ($legacyCount + $orphansRole + $orphansUser + $duplicates->count()) > 0 ? 1 : 0;
    }
}
