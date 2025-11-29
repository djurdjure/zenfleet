<?php

use App\Models\Driver;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$matricules = ['DIF-2025-837', 'DLS-84745'];
$drivers = Driver::whereIn('employee_number', $matricules)->get();

echo "🔍 DEBUG DRIVER ARCHIVING DEEP DIVE\n";
echo "====================================\n";

foreach ($drivers as $driver) {
    echo "\n👤 Driver: {$driver->full_name} (#{$driver->employee_number})\n";
    echo "   ID: {$driver->id}\n";
    echo "   Status ID: {$driver->status_id}\n";
    
    // 1. Check Assignments
    echo "   📋 Assignments:\n";
    $assignments = $driver->assignments()->get();
    if ($assignments->isEmpty()) {
        echo "      - No assignments found.\n";
    } else {
        foreach ($assignments as $a) {
            $start = $a->start_datetime ? Carbon::parse($a->start_datetime)->toDateTimeString() : 'NULL';
            $end = $a->end_datetime ? Carbon::parse($a->end_datetime)->toDateTimeString() : 'NULL';
            $isActive = $a->end_datetime === null || Carbon::parse($a->end_datetime)->isFuture();
            
            echo "      - [ID: {$a->id}] Start: {$start} | End: {$end} | Active? " . ($isActive ? "YES 🔴" : "NO ✅") . "\n";
        }
    }

    // 2. Check Sanctions
    echo "   ⚖️ Sanctions:\n";
    if (method_exists($driver, 'sanctions')) {
        $sanctions = $driver->sanctions()->get();
        echo "      - Count: " . $sanctions->count() . "\n";
    } else {
        echo "      - Relation 'sanctions' not defined.\n";
    }

    // 3. Check Repair Requests
    echo "   🔧 Repair Requests:\n";
    if (method_exists($driver, 'repairRequests')) {
        $requests = $driver->repairRequests()->get();
        echo "      - Count: " . $requests->count() . "\n";
    } else {
        echo "      - Relation 'repairRequests' not defined.\n";
    }

    // 4. Check Soft Delete Status
    echo "   🗑️ Deleted At: " . ($driver->deleted_at ? $driver->deleted_at->toDateTimeString() : 'NULL') . "\n";

    // 5. Test Archive Logic (Simulation)
    echo "   🧪 Simulation Archive Logic:\n";
    $hasActiveAssignments = $driver->assignments()
        ->where(function ($query) {
            $query->whereNull('end_datetime')
                  ->orWhere('end_datetime', '>', now());
        })
        ->exists();
    
    if ($hasActiveAssignments) {
        echo "      ❌ BLOCKED: Has active assignments.\n";
    } else {
        echo "      ✅ ALLOWED: No active assignments detected.\n";
    }
}
