<?php
/**
 * Script de test du service PDF Enterprise
 * Usage: php test_pdf_service.php
 */

require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Http;

echo "\n";
echo "🧪 TEST DU SERVICE PDF ENTERPRISE\n";
echo "==================================\n\n";

// Configuration
$pdfServiceUrl = env('PDF_SERVICE_URL', 'http://pdf-service:3000');
$timeout = env('PDF_SERVICE_TIMEOUT', 60);

echo "📍 Configuration:\n";
echo "   URL: $pdfServiceUrl\n";
echo "   Timeout: {$timeout}s\n\n";

// Test 1: Health Check
echo "Test 1: Health Check...\n";
try {
    $response = Http::timeout(5)->get($pdfServiceUrl . '/health');
    
    if ($response->successful()) {
        echo "✅ Service en ligne!\n";
        $data = $response->json();
        echo "   Status: " . ($data['status'] ?? 'unknown') . "\n";
        echo "   Version: " . ($data['version'] ?? 'unknown') . "\n";
        echo "   Uptime: " . round(($data['uptime'] ?? 0) / 60, 2) . " minutes\n";
    } else {
        echo "❌ Erreur health check: HTTP " . $response->status() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Impossible de contacter le service: " . $e->getMessage() . "\n";
    echo "\n⚠️ Vérifiez que le service est démarré avec:\n";
    echo "   ./start-pdf-service.sh\n";
    exit(1);
}

// Test 2: Génération PDF simple
echo "\nTest 2: Génération PDF simple...\n";
try {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { 
                font-family: Arial, sans-serif; 
                padding: 40px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            h1 { 
                font-size: 48px; 
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            }
            .box {
                background: rgba(255,255,255,0.1);
                border-radius: 10px;
                padding: 20px;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <h1>Test PDF ZenFleet</h1>
        <div class="box">
            <p>Ce PDF a été généré le ' . date('d/m/Y à H:i:s') . '</p>
            <p>Service: PDF Microservice Enterprise v2.0</p>
            <p>Status: ✅ Opérationnel</p>
        </div>
    </body>
    </html>';

    $response = Http::timeout($timeout)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/pdf'
        ])
        ->post($pdfServiceUrl . '/generate-pdf', [
            'html' => $html,
            'options' => [
                'format' => 'A4',
                'printBackground' => true
            ]
        ]);

    if ($response->successful()) {
        $pdfSize = strlen($response->body());
        echo "✅ PDF généré avec succès!\n";
        echo "   Taille: " . round($pdfSize / 1024, 2) . " KB\n";
        
        // Sauvegarder le PDF de test
        $testFile = '/tmp/test_zenfleet_' . time() . '.pdf';
        file_put_contents($testFile, $response->body());
        echo "   Fichier sauvé: $testFile\n";
    } else {
        echo "❌ Erreur génération PDF: HTTP " . $response->status() . "\n";
        echo "   Réponse: " . $response->body() . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de la génération: " . $e->getMessage() . "\n";
}

// Test 3: Vérifier l'intégration avec VehiclePdfExportService
echo "\nTest 3: Intégration avec VehiclePdfExportService...\n";
try {
    if (class_exists('App\Services\VehiclePdfExportService')) {
        echo "✅ Classe VehiclePdfExportService trouvée\n";
        
        // Vérifier si on peut instancier le service
        $service = new App\Services\VehiclePdfExportService();
        echo "✅ Service instancié avec succès\n";
    } else {
        echo "❌ Classe VehiclePdfExportService non trouvée\n";
    }
} catch (Exception $e) {
    echo "⚠️ Erreur lors de l'instanciation: " . $e->getMessage() . "\n";
}

echo "\n==================================\n";
echo "📊 RÉSUMÉ DES TESTS\n";
echo "==================================\n";

if (isset($response) && $response->successful()) {
    echo "✅ Service PDF pleinement opérationnel!\n";
    echo "\n📝 Prochaines étapes:\n";
    echo "1. Assurez-vous que .env contient: PDF_SERVICE_URL=http://pdf-service:3000\n";
    echo "2. Testez l'export depuis l'interface web\n";
    echo "3. Surveillez les logs: docker logs -f zenfleet_pdf_service\n";
} else {
    echo "❌ Des problèmes ont été détectés\n";
    echo "\n🔧 Actions correctives:\n";
    echo "1. Démarrez le service: ./start-pdf-service.sh\n";
    echo "2. Vérifiez les logs: docker logs zenfleet_pdf_service\n";
    echo "3. Vérifiez docker-compose.yml\n";
}

echo "\n";
