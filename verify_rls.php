<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PHASE 1 - RLS VERIFICATION ===\n\n";

echo "1. Checking if RLS policies exist...\n";
$policies = DB::select("
    SELECT schemaname, tablename, policyname, permissive, roles, qual, with_check
    FROM pg_policies
    WHERE schemaname = 'public'
    ORDER BY tablename, policyname
");

if (count($policies) > 0) {
    echo "✅ Found " . count($policies) . " RLS policies:\n";
    foreach ($policies as $policy) {
        echo "  - Table: {$policy->tablename}, Policy: {$policy->policyname}\n";
    }
} else {
    echo "⚠️  No RLS policies found in public schema.\n";
}

echo "\n2. Checking if RLS is ENABLED on tables...\n";
$rlsStatus = DB::select("
    SELECT relname AS table_name, 
           relrowsecurity AS rls_enabled,
           relforcerowsecurity AS rls_forced
    FROM pg_class
    WHERE relnamespace = 'public'::regnamespace
      AND relkind = 'r'
      AND relrowsecurity = true
");

if (count($rlsStatus) > 0) {
    echo "✅ RLS enabled on " . count($rlsStatus) . " tables:\n";
    foreach ($rlsStatus as $table) {
        echo "  - {$table->table_name} (Forced: " . ($table->rls_forced ? 'YES' : 'NO') . ")\n";
    }
} else {
    echo "⚠️  RLS is NOT enabled on any tables.\n";
}

echo "\n3. Testing session variable injection...\n";
// Simulate what SetTenantSession does
DB::statement("SET LOCAL app.current_user_id = '999'");
DB::statement("SET LOCAL app.current_organization_id = '1'");

$userId = DB::selectOne("SELECT current_setting('app.current_user_id', true) AS value");
$orgId = DB::selectOne("SELECT current_setting('app.current_organization_id', true) AS value");

echo "  - app.current_user_id: " . ($userId->value ?? 'NOT SET') . "\n";
echo "  - app.current_organization_id: " . ($orgId->value ?? 'NOT SET') . "\n";

echo "\n4. Summary:\n";
if (count($policies) > 0 && count($rlsStatus) > 0) {
    echo "✅ RLS is CONFIGURED and ENABLED.\n";
} elseif (count($policies) > 0) {
    echo "⚠️  RLS policies exist but are NOT ENABLED on tables.\n";
    echo "   To enable: ALTER TABLE table_name ENABLE ROW LEVEL SECURITY;\n";
} else {
    echo "❌ RLS is NOT CONFIGURED (no policies found).\n";
}
