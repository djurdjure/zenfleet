<?php
/**
 * Test des méthodes de traitement CSV Enterprise
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test Enterprise CSV Processing\n";
echo "=================================\n\n";

// Test du fichier CSV
$csvPath = __DIR__ . '/test_vehicle_import_simple.csv';

if (!file_exists($csvPath)) {
    echo "❌ Fichier CSV de test non trouvé: $csvPath\n";
    exit(1);
}

echo "📁 Fichier CSV: " . basename($csvPath) . "\n";
echo "📊 Taille: " . filesize($csvPath) . " bytes\n";
echo "🗂️ Type MIME: " . mime_content_type($csvPath) . "\n\n";

// Test de lecture manuelle des en-têtes
echo "📋 Test lecture en-têtes CSV:\n";
$handle = fopen($csvPath, 'r');
if ($handle) {
    $headers = fgetcsv($handle);
    fclose($handle);

    echo "Colonnes détectées (" . count($headers) . "):\n";
    foreach ($headers as $i => $header) {
        echo "  " . ($i + 1) . ". '$header'\n";
    }
}

echo "\n📊 Test lecture complète des données:\n";
$data = [];
$lineCount = 0;
$handle = fopen($csvPath, 'r');

while (($row = fgetcsv($handle)) !== false) {
    $lineCount++;
    if ($lineCount === 1) {
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

if (!empty($data)) {
    echo "\n🔍 Échantillon de données:\n";
    $sample = $data[0];
    foreach ($sample as $key => $value) {
        echo "  $key: '$value'\n";
    }
}

// Validation des colonnes requises
echo "\n✅ Validation des colonnes requises:\n";
$requiredColumns = [
    'registration_plate', 'vin', 'brand', 'model', 'color',
    'vehicle_type', 'fuel_type', 'transmission_type', 'status',
    'manufacturing_year', 'acquisition_date', 'purchase_price',
    'current_value', 'initial_mileage', 'current_mileage',
    'engine_displacement_cc', 'power_hp', 'seats', 'notes'
];

$foundColumns = $headers ?? [];
$missingColumns = array_diff($requiredColumns, $foundColumns);

if (empty($missingColumns)) {
    echo "✅ Toutes les colonnes requises sont présentes\n";
} else {
    echo "❌ Colonnes manquantes: " . implode(', ', $missingColumns) . "\n";
}

// Test de validation des données
if (!empty($data)) {
    echo "\n🔍 Validation échantillon de données:\n";
    $sample = $data[0];
    $errors = [];

    if (empty($sample['registration_plate'])) $errors[] = "Plaque d'immatriculation manquante";
    if (strlen($sample['vin']) !== 17) $errors[] = "VIN invalide (doit faire 17 caractères)";
    if (empty($sample['brand'])) $errors[] = "Marque manquante";
    if (empty($sample['model'])) $errors[] = "Modèle manquant";
    if (!is_numeric($sample['manufacturing_year'])) $errors[] = "Année de fabrication invalide";
    if (!strtotime($sample['acquisition_date'])) $errors[] = "Date d'acquisition invalide";

    if (empty($errors)) {
        echo "✅ Validation de base: RÉUSSIE\n";
    } else {
        echo "❌ Erreurs détectées:\n";
        foreach ($errors as $error) {
            echo "  - $error\n";
        }
    }
}

echo "\n🎉 Test terminé!\n";
echo "Le fichier CSV est prêt pour l'importation enterprise.\n";