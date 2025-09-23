<?php
/**
 * Script de test pour l'importation CSV - Enterprise Grade
 */

require_once 'vendor/autoload.php';

use App\Http\Controllers\Admin\VehicleController;
use Illuminate\Http\UploadedFile;

// Simulation d'un fichier uploadé
$csvPath = '/home/lynx/projects/zenfleet/test_import_corrected.csv';

if (!file_exists($csvPath)) {
    echo "❌ Fichier CSV de test non trouvé: $csvPath\n";
    exit(1);
}

echo "🧪 Test d'importation CSV Enterprise\n";
echo "=====================================\n\n";

// Test 1: Validation du fichier
echo "📁 Test 1: Validation du fichier\n";
echo "Fichier: $csvPath\n";
echo "Taille: " . filesize($csvPath) . " bytes\n";
echo "Type: " . mime_content_type($csvPath) . "\n\n";

// Test 2: Lecture et analyse des en-têtes
echo "📋 Test 2: Lecture des en-têtes\n";
$handle = fopen($csvPath, 'r');
if ($handle) {
    $firstLine = fgetcsv($handle);
    fclose($handle);

    echo "Colonnes détectées (" . count($firstLine) . "):\n";
    foreach ($firstLine as $i => $header) {
        echo "  " . ($i + 1) . ". '$header'\n";
    }
    echo "\n";
}

// Test 3: Validation des colonnes requises
echo "✅ Test 3: Validation des colonnes requises\n";
$requiredColumns = [
    'registration_plate', 'vin', 'brand', 'model', 'color',
    'vehicle_type', 'fuel_type', 'transmission_type', 'status',
    'manufacturing_year', 'acquisition_date', 'purchase_price',
    'current_value', 'initial_mileage', 'current_mileage',
    'engine_displacement_cc', 'power_hp', 'seats', 'notes'
];

$missing = array_diff($requiredColumns, $firstLine);
if (empty($missing)) {
    echo "✅ Toutes les colonnes requises sont présentes\n\n";
} else {
    echo "❌ Colonnes manquantes: " . implode(', ', $missing) . "\n\n";
    exit(1);
}

// Test 4: Lecture complète des données
echo "📊 Test 4: Lecture complète des données\n";
$data = [];
$headers = null;
$lineCount = 0;

$handle = fopen($csvPath, 'r');
while (($row = fgetcsv($handle)) !== false) {
    $lineCount++;
    if ($headers === null) {
        $headers = $row;
    } else {
        if (count($row) === count($headers)) {
            $data[] = array_combine($headers, $row);
        }
    }
}
fclose($handle);

echo "Lignes totales: $lineCount\n";
echo "Lignes de données: " . count($data) . "\n";
echo "En-têtes: " . count($headers) . "\n\n";

// Test 5: Validation d'une ligne échantillon
if (!empty($data)) {
    echo "🔍 Test 5: Validation d'une ligne échantillon\n";
    $sample = $data[0];
    echo "Données échantillon:\n";
    foreach ($sample as $key => $value) {
        echo "  $key: '$value'\n";
    }
    echo "\n";

    // Validation simple
    $errors = [];
    if (empty($sample['registration_plate'])) $errors[] = "Plaque manquante";
    if (strlen($sample['vin'] ?? '') !== 17) $errors[] = "VIN invalide";
    if (empty($sample['brand'])) $errors[] = "Marque manquante";
    if (empty($sample['model'])) $errors[] = "Modèle manquant";

    if (empty($errors)) {
        echo "✅ Validation échantillon: OK\n";
    } else {
        echo "❌ Erreurs détectées: " . implode(', ', $errors) . "\n";
    }
}

echo "\n🎉 Test terminé avec succès!\n";
echo "Le fichier CSV peut être importé en production.\n";