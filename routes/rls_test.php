<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/test-vehicle-access', function () {
    if (!Auth::check()) {
        return 'Please login first';
    }

    $user = Auth::user();
    $userId = $user->id;
    $orgId = $user->organization_id;
    $roles = $user->getRoleNames();

    // 1. Check if session variable is set (via Middleware)
    $sessionUser = DB::scalar("SELECT current_setting('app.current_user_id', true)");
    $sessionOrg = DB::scalar("SELECT current_setting('app.current_organization_id', true)");

    // 2. Raw SQL count (should be filtered by RLS)
    $rawCount = DB::selectOne("SELECT count(*) as count FROM vehicles")->count;

    // 3. Eloquent count (filtered by Global Scope + RLS)
    $eloquentCount = \App\Models\Vehicle::count();
    
    // 4. Get IDs visible via Eloquent
    $visibleIds = \App\Models\Vehicle::pluck('id')->take(10);
    
    // 5. Check specific access details
    $driverAccess = $user->driver ? 'Yes (Driver ID: ' . $user->driver->id . ')' : 'No';
    $manualAccessCount = $user->vehicles()->wherePivot('access_type', 'manual')->count();
    $autoAccessCount = $user->vehicles()->wherePivot('access_type', 'auto_driver')->count();

    return [
        'user' => [
            'id' => $userId,
            'name' => $user->name,
            'roles' => $roles,
            'org_id' => $orgId,
            'is_driver' => $driverAccess,
        ],
        'session_context' => [
            'session_user_id' => $sessionUser,
            'session_org_id' => $sessionOrg,
            'rls_active' => ($sessionUser == $userId),
        ],
        'counts' => [
            'raw_sql_count (RLS only)' => $rawCount,
            'eloquent_count (Scope + RLS)' => $eloquentCount,
        ],
        'access_details' => [
            'manual_grants' => $manualAccessCount,
            'auto_grants' => $autoAccessCount,
        ],
        'visible_vehicles_sample' => $visibleIds,
        'status' => ($sessionUser == $userId) ? '✅ RLS ACTIVE' : '❌ RLS FAILED'
    ];
})->middleware(['web', 'auth']);
