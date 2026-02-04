<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\PermissionAliases;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable(config('permission.table_names.permissions'))) {
            return;
        }

        $permissionModel = app(config('permission.models.permission'));
        $permissionsTable = config('permission.table_names.permissions');
        $roleHasPermissions = config('permission.table_names.role_has_permissions');
        $modelHasPermissions = config('permission.table_names.model_has_permissions');
        $teamKey = config('permission.column_names.team_foreign_key', 'organization_id');

        DB::transaction(function () use (
            $permissionModel,
            $permissionsTable,
            $roleHasPermissions,
            $modelHasPermissions,
            $teamKey
        ) {
            foreach (PermissionAliases::legacyMap() as $canonical => $aliases) {
                foreach ($aliases as $legacyName) {
                    $legacyPermissions = $permissionModel::where('name', $legacyName)->get();

                    foreach ($legacyPermissions as $legacyPermission) {
                        $canonicalPermission = $permissionModel::where('name', $canonical)
                            ->where('guard_name', $legacyPermission->guard_name)
                            ->first();

                        if (!$canonicalPermission) {
                            $canonicalPermission = $permissionModel::create([
                                'name' => $canonical,
                                'guard_name' => $legacyPermission->guard_name,
                                $teamKey => $legacyPermission->{$teamKey} ?? null,
                            ]);
                        }

                        // Role permissions
                        $roleIds = DB::table($roleHasPermissions)
                            ->where('permission_id', $legacyPermission->id)
                            ->pluck('role_id');

                        foreach ($roleIds as $roleId) {
                            $exists = DB::table($roleHasPermissions)
                                ->where('role_id', $roleId)
                                ->where('permission_id', $canonicalPermission->id)
                                ->exists();

                            if (!$exists) {
                                DB::table($roleHasPermissions)->insert([
                                    'role_id' => $roleId,
                                    'permission_id' => $canonicalPermission->id,
                                ]);
                            }
                        }

                        // User direct permissions
                        $modelRows = DB::table($modelHasPermissions)
                            ->where('permission_id', $legacyPermission->id)
                            ->get(['model_type', 'model_id', $teamKey]);

                        foreach ($modelRows as $row) {
                            $exists = DB::table($modelHasPermissions)
                                ->where('permission_id', $canonicalPermission->id)
                                ->where('model_type', $row->model_type)
                                ->where('model_id', $row->model_id)
                                ->where($teamKey, $row->{$teamKey})
                                ->exists();

                            if (!$exists) {
                                DB::table($modelHasPermissions)->insert([
                                    'permission_id' => $canonicalPermission->id,
                                    'model_type' => $row->model_type,
                                    'model_id' => $row->model_id,
                                    $teamKey => $row->{$teamKey},
                                ]);
                            }
                        }

                        // Remove legacy assignments
                        DB::table($roleHasPermissions)
                            ->where('permission_id', $legacyPermission->id)
                            ->delete();

                        DB::table($modelHasPermissions)
                            ->where('permission_id', $legacyPermission->id)
                            ->delete();
                    }
                }
            }
        });

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Non réversible automatiquement.
    }
};
