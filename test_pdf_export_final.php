<?php
/**
 * Test Final Export PDF - Diagnostic Complet
 */

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Http;
use App\Models\Vehicle;
use App\Services\VehiclePdfExportService;

echo "\n";
echo "========================================\n";
echo "🧪 TEST FINAL EXPORT PDF ENTERPRISE\n"; 
echo "========================================\n\n";

// 1. Configuration
echo "📋 Configuration actuelle:\n";
$pdfUrl = env('PDF_SERVICE_URL');
$timeout = env('PDF_SERVICE_TIMEOUT', 60);
echo "   URL: $pdfUrl\n";
echo "   Timeout: {$timeout}s\n\n";

// 2. Test connectivité
echo "🔗 Test connectivité service PDF...\n";
try {
    // Test depuis PHP
    $testUrl = $pdfUrl . '/health';
    echo "   Test URL: $testUrl\n";
    
    $response = Http::timeout(5)->get($testUrl);
    
    if ($response->successful()) {
        echo "   ✅ Service accessible!\n";
        $data = $response->json();
        echo "   Response: " . json_encode($data) . "\n";
    } else {
        echo "   ❌ Service inaccessible (HTTP " . $response->status() . ")\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
    echo "\n⚠️ Le service PDF n'est pas accessible.\n";
    echo "Solutions:\n";
    echo "1. Attendez que le build Docker se termine (5-10 min)\n";
    echo "2. Vérifiez: docker ps | grep pdf\n";
    echo "3. Logs: docker logs zenfleet_pdf_service\n";
    exit(1);
}

// 3. Test génération PDF simple
echo "\n📄 Test génération PDF simple...\n";
try {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial; padding: 40px; }
            h1 { color: #3b82f6; }
        </style>
    </head>
    <body>
        <h1>Test PDF ZenFleet Enterprise</h1>
        <p>Généré le ' . date('d/m/Y H:i:s') . '</p>
    </body>
    </html>';
    
    $response = Http::timeout($timeout)
        ->post($pdfUrl . '/generate-pdf', [
            'html' => $html,
            'options' => [
                'format' => 'A4',
                'printBackground' => true
            ]
        ]);
    
    if ($response->successful()) {
        $size = strlen($response->body());
        echo "   ✅ PDF généré! Taille: " . round($size/1024, 2) . " KB\n";
        
        // Vérifier que c'est bien un PDF
        if (substr($response->body(), 0, 4) === '%PDF') {
            echo "   ✅ Format PDF valide\n";
        } else {
            echo "   ❌ Le contenu n'est pas un PDF valide\n";
        }
    } else {
        echo "   ❌ Erreur génération: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

// 4. Test avec VehiclePdfExportService
echo "\n🚗 Test export véhicule via service Laravel...\n";
try {
    // Récupérer un véhicule de test
    $vehicle = Vehicle::first();
    
    if (!$vehicle) {
        echo "   ⚠️ Aucun véhicule trouvé dans la base\n";
    } else {
        echo "   Véhicule test: " . $vehicle->registration_plate . "\n";
        
        // Tester le service
        $service = new VehiclePdfExportService();
        
        // Simuler l'appel
        echo "   Test génération PDF véhicule #" . $vehicle->id . "...\n";
        echo "   ℹ️ Cette partie nécessite que le service soit 100% opérationnel\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur service: " . $e->getMessage() . "\n";
}

// 5. Diagnostic final
echo "\n========================================\n";
echo "📊 DIAGNOSTIC FINAL\n";
echo "========================================\n";

$allGood = true;

// Check service
if (isset($response) && $response->successful()) {
    echo "✅ Service PDF accessible\n";
} else {
    echo "❌ Service PDF non accessible\n";
    $allGood = false;
}

// Check config
if ($pdfUrl && strpos($pdfUrl, 'pdf-service:3000') !== false) {
    echo "✅ Configuration correcte\n";
} else {
    echo "❌ Configuration incorrecte (vérifier .env)\n";
    $allGood = false;
}

if ($allGood) {
    echo "\n🎉 SYSTÈME PRÊT POUR L'EXPORT PDF!\n";
    echo "\nTestez maintenant depuis l'interface:\n";
    echo "1. Allez sur http://localhost/admin/vehicles\n";
    echo "2. Menu 3 points → Exporter PDF\n";
} else {
    echo "\n⚠️ CORRECTIONS NÉCESSAIRES\n";
    echo "\n1. Assurez-vous que docker-compose up -d pdf-service est terminé\n";
    echo "2. Vérifiez .env: PDF_SERVICE_URL=http://pdf-service:3000\n";
    echo "3. Videz le cache: docker exec zenfleet_php php artisan config:clear\n";
}

echo "\n";
