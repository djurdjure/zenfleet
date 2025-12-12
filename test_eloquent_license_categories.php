#!/usr/bin/env php
<?php
/**
 * Test 2: Eloquent update via PHP direct
 * Teste si Eloquent peut sauvegarder correctement les license_categories
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Driver;

echo "=== TEST ELOQUENT: Update license_categories ===\n\n";

$driver = Driver::find(10);

if (!$driver) {
    echo "❌ Driver ID 10 not found!\n";
    exit(1);
}

echo "AVANT update:\n";
echo "- license_categories: " . json_encode($driver->license_categories) . "\n";
echo "- Type: " . gettype($driver->license_categories) . "\n\n";

// Test 1: Update avec array
echo "Test 1: Update avec array PHP\n";
$driver->license_categories = ['B', 'C', 'D'];
$driver->save();
$driver->refresh();

echo "APRÈS update (array):\n";
echo "- license_categories: " . json_encode($driver->license_categories) . "\n";
echo "- Type: " . gettype($driver->license_categories) . "\n";
echo "- Is array: " . (is_array($driver->license_categories) ? 'YES' : 'NO') . "\n\n";

// Test 2: Vérifier en DB directement
echo "Vérification DB directe:\n";
$rawData = \DB::table('drivers')->where('id', 10)->first(['license_categories']);
echo "- Raw DB value: " . json_encode($rawData->license_categories) . "\n";
echo "- Type: " . gettype($rawData->license_categories) . "\n\n";

echo "✅ Test terminé!\n";
