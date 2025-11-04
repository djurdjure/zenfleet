<?php

/*
|--------------------------------------------------------------------------
| Test des Exports de Véhicules - Enterprise Grade
|--------------------------------------------------------------------------
| Script de test pour vérifier les exports CSV, Excel et PDF
*/

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

echo "\n========================================\n";
echo "🧪 TEST DES EXPORTS VÉHICULES\n";
echo "========================================\n\n";

// Se connecter comme un utilisateur admin
$user = User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur admin non trouvé\n";
    echo "Essayons de trouver un utilisateur...\n";
    $user = User::first();
}

if (!$user) {
    echo "❌ Aucun utilisateur trouvé dans la base de données\n";
    exit(1);
}

echo "✅ Utilisateur: {$user->name} ({$user->email})\n";
echo "✅ Organisation ID: {$user->organization_id}\n\n";

Auth::login($user);

// Compter les véhicules
$vehicleCount = Vehicle::where('organization_id', $user->organization_id)->count();
echo "📊 Nombre de véhicules: {$vehicleCount}\n\n";

if ($vehicleCount === 0) {
    echo "⚠️  Aucun véhicule trouvé pour cette organisation\n";
    exit(0);
}

echo "========================================\n";
echo "1. TEST EXPORT CSV\n";
echo "========================================\n";

try {
    $csvExporter = new \App\Exports\VehiclesCsvExport([]);
    $response = $csvExporter->download();
    $content = $response->getContent();
    
    echo "✅ Export CSV généré\n";
    echo "   Taille: " . strlen($content) . " octets\n";
    echo "   Premières lignes:\n";
    echo "   " . implode("\n   ", array_slice(explode("\n", $content), 0, 3)) . "\n";
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "2. TEST EXPORT EXCEL\n";
echo "========================================\n";

try {
    $excelExporter = new \App\Exports\VehiclesExport([]);
    $fileName = 'test_vehicles_' . date('Y-m-d_H-i-s') . '.xlsx';
    $filePath = storage_path('app/public/' . $fileName);
    
    \Maatwebsite\Excel\Facades\Excel::store($excelExporter, 'public/' . $fileName);
    
    if (file_exists($filePath)) {
        echo "✅ Export Excel généré\n";
        echo "   Fichier: {$filePath}\n";
        echo "   Taille: " . filesize($filePath) . " octets\n";
        
        // Nettoyer
        unlink($filePath);
        echo "   ✓ Fichier de test supprimé\n";
    } else {
        echo "❌ Fichier Excel non créé\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "3. TEST EXPORT PDF (Liste)\n";
echo "========================================\n";

try {
    $pdfService = new \App\Services\VehiclePdfExportService([]);
    $response = $pdfService->exportList();
    
    echo "✅ Export PDF de liste généré\n";
    echo "   Taille: " . strlen($response->getContent()) . " octets\n";
    echo "   Type: " . $response->headers->get('Content-Type') . "\n";
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n========================================\n";
echo "✅ TESTS TERMINÉS\n";
echo "========================================\n\n";
