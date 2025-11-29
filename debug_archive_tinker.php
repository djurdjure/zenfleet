<?php
$matricules = ['DIF-2025-837', 'DLS-84745'];

foreach ($matricules as $matricule) {
    echo "\n--------------------------------------------------\n";
    echo "Checking Driver: $matricule\n";
    $driver = App\Models\Driver::where('employee_number', $matricule)->first();
    
    if (!$driver) { 
        echo "❌ Driver not found\n"; 
        continue; 
    }

    echo "👤 Name: {$driver->full_name}\n";
    echo "🆔 ID: {$driver->id}\n";

    // Check Active Assignments Logic
    $query = $driver->assignments()->where(function ($q) {
        $q->whereNull('end_datetime')->orWhere('end_datetime', '>', now());
    });

    $exists = $query->exists();
    echo "🔍 Has Active Assignments? " . ($exists ? "YES 🔴" : "NO ✅") . "\n";

    if ($exists) {
        $assignments = $query->get();
        foreach ($assignments as $a) {
            echo "   - Assignment #{$a->id} | Start: {$a->start_datetime} | End: " . ($a->end_datetime ?? 'NULL') . "\n";
        }
    } else {
        echo "✅ Logic allows archiving. Testing delete()...\n";
        try {
            // Wrap in transaction to rollback after test
            DB::beginTransaction();
            $result = $driver->delete();
            echo "   🗑️ Delete Result: " . ($result ? 'TRUE' : 'FALSE') . "\n";
            
            if ($result) {
                echo "   ✅ Soft Delete Successful (simulated)\n";
            } else {
                echo "   ❌ Soft Delete Returned False\n";
            }
            DB::rollBack();
            echo "   🔄 Rolled back transaction.\n";
        } catch (\Exception $e) {
            echo "   💥 Exception during delete: " . $e->getMessage() . "\n";
            DB::rollBack();
        }
    }
}
