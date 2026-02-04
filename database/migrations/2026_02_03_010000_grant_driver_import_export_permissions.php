<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        if (class_exists(PermissionRegistrar::class)) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $guard = config('auth.defaults.guard') ?? 'web';

        $importPermission = Permission::firstOrCreate([
            'name' => 'drivers.import',
            'guard_name' => $guard,
        ]);

        $exportPermission = Permission::firstOrCreate([
            'name' => 'drivers.export',
            'guard_name' => $guard,
        ]);

        $this->syncPermissionToRoles($importPermission, [
            'drivers.import',
            'import drivers',
        ]);

        $this->syncPermissionToRoles($exportPermission, [
            'drivers.export',
            'export drivers',
        ]);

        $this->syncPermissionToUsers($importPermission, [
            'drivers.import',
            'import drivers',
        ]);

        $this->syncPermissionToUsers($exportPermission, [
            'drivers.export',
            'export drivers',
        ]);

        $this->syncPermissionToNamedRoles($importPermission, ['Admin', 'Super Admin']);
        $this->syncPermissionToNamedRoles($exportPermission, ['Admin', 'Super Admin']);
    }

    public function down(): void
    {
        // No-op: avoid removing permissions that may now be relied upon.
    }

    private function syncPermissionToRoles(Permission $permission, array $sourcePermissionNames): void
    {
        if (!Schema::hasTable('role_has_permissions')) {
            return;
        }

        $sourceIds = Permission::whereIn('name', $sourcePermissionNames)->pluck('id');
        if ($sourceIds->isEmpty()) {
            return;
        }

        $roleIds = DB::table('role_has_permissions')
            ->whereIn('permission_id', $sourceIds)
            ->pluck('role_id')
            ->unique();

        foreach ($roleIds as $roleId) {
            DB::table('role_has_permissions')->updateOrInsert([
                'role_id' => $roleId,
                'permission_id' => $permission->id,
            ], []);
        }
    }

    private function syncPermissionToUsers(Permission $permission, array $sourcePermissionNames): void
    {
        if (!Schema::hasTable('model_has_permissions')) {
            return;
        }

        $sourceIds = Permission::whereIn('name', $sourcePermissionNames)->pluck('id');
        if ($sourceIds->isEmpty()) {
            return;
        }

        $userModel = config('permission.models.user') ?? \App\Models\User::class;

        $userIds = DB::table('model_has_permissions')
            ->whereIn('permission_id', $sourceIds)
            ->where('model_type', $userModel)
            ->pluck('model_id')
            ->unique();

        foreach ($userIds as $userId) {
            DB::table('model_has_permissions')->updateOrInsert([
                'permission_id' => $permission->id,
                'model_type' => $userModel,
                'model_id' => $userId,
            ], []);
        }
    }

    private function syncPermissionToNamedRoles(Permission $permission, array $roleNames): void
    {
        if (!Schema::hasTable('roles') || !Schema::hasTable('role_has_permissions')) {
            return;
        }

        $roleIds = DB::table('roles')
            ->whereIn('name', $roleNames)
            ->pluck('id')
            ->unique();

        foreach ($roleIds as $roleId) {
            DB::table('role_has_permissions')->updateOrInsert([
                'role_id' => $roleId,
                'permission_id' => $permission->id,
            ], []);
        }
    }
};
