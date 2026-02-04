<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('use_custom_permissions')->default(false)->after('organization_id');
        });

        $permissionsTable = config('permission.table_names.model_has_permissions');
        $rolesTable = config('permission.table_names.model_has_roles');
        $userClass = addslashes(\App\Models\User::class);

        $usersWithDirect = DB::table($permissionsTable)
            ->where('model_type', $userClass)
            ->distinct()
            ->pluck('model_id');

        if ($usersWithDirect->isEmpty()) {
            return;
        }

        $usersWithRoles = DB::table($rolesTable)
            ->where('model_type', $userClass)
            ->distinct()
            ->pluck('model_id');

        $usersDirectOnly = $usersWithDirect->diff($usersWithRoles);

        if ($usersDirectOnly->isNotEmpty()) {
            DB::table('users')
                ->whereIn('id', $usersDirectOnly->values())
                ->update(['use_custom_permissions' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('use_custom_permissions');
        });
    }
};
