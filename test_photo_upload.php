<?php

/**
 * 🧪 TEST ENTERPRISE: Upload de Photo pour Chauffeur
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST: Upload de Photo Chauffeur\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Créer une fausse photo de test
$testImagePath = storage_path('app/test_photo.jpg');
if (!file_exists($testImagePath)) {
    // Créer une image de test simple
    $image = imagecreatetruecolor(200, 200);
    $bgColor = imagecolorallocate($image, 100, 150, 200);
    imagefill($image, 0, 0, $bgColor);
    imagejpeg($image, $testImagePath);
    imagedestroy($image);
    echo "✅ Image de test créée: $testImagePath\n";
}

// Simuler un UploadedFile
$uploadedFile = new \Illuminate\Http\UploadedFile(
    $testImagePath,
    'test_photo.jpg',
    'image/jpeg',
    null,
    true // test mode
);

echo "📋 Fichier uploadé simulé:\n";
echo "   Nom: {$uploadedFile->getClientOriginalName()}\n";
echo "   Taille: " . $uploadedFile->getSize() . " bytes\n";
echo "   MIME: {$uploadedFile->getMimeType()}\n\n";

// Trouver un admin
$admin = App\Models\User::where('email', 'admin@zenfleet.dz')->first();
Auth::login($admin);

echo "👤 Admin connecté: {$admin->name}\n\n";

// Test 1: Création avec photo
echo "📸 TEST 1: Création de chauffeur AVEC photo\n";
echo "─────────────────────────────────────────\n";

try {
    $service = app(App\Services\DriverService::class);

    $data = [
        'first_name' => 'PhotoTest',
        'last_name' => 'Chauffeur',
        'status_id' => 1,
        'organization_id' => $admin->organization_id,
        'photo' => $uploadedFile,
    ];

    $result = $service->createDriver($data);

    echo "✅ SUCCÈS! Chauffeur créé avec photo:\n";
    echo "   Driver ID: {$result['driver']->id}\n";
    echo "   Nom: {$result['driver']->first_name} {$result['driver']->last_name}\n";
    echo "   Photo: {$result['driver']->photo}\n";
    echo "   Fichier existe: " . (Storage::disk('public')->exists($result['driver']->photo) ? 'OUI ✅' : 'NON ❌') . "\n\n";

    // Test 2: Mise à jour avec nouvelle photo
    echo "📸 TEST 2: Mise à jour de chauffeur AVEC nouvelle photo\n";
    echo "───────────────────────────────────────────────────────\n";

    // Créer une nouvelle photo de test
    $newTestImagePath = storage_path('app/test_photo_update.jpg');
    $image2 = imagecreatetruecolor(300, 300);
    $bgColor2 = imagecolorallocate($image2, 200, 100, 150);
    imagefill($image2, 0, 0, $bgColor2);
    imagejpeg($image2, $newTestImagePath);
    imagedestroy($image2);

    $uploadedFile2 = new \Illuminate\Http\UploadedFile(
        $newTestImagePath,
        'test_photo_update.jpg',
        'image/jpeg',
        null,
        true
    );

    $oldPhoto = $result['driver']->photo;

    $updateData = [
        'address' => 'Nouvelle adresse de test',
        'photo' => $uploadedFile2,
    ];

    $updatedDriver = $service->updateDriver($result['driver'], $updateData);

    echo "✅ SUCCÈS! Chauffeur mis à jour avec nouvelle photo:\n";
    echo "   Driver ID: {$updatedDriver->id}\n";
    echo "   Ancienne photo: $oldPhoto\n";
    echo "   Nouvelle photo: {$updatedDriver->photo}\n";
    echo "   Ancienne photo supprimée: " . (!Storage::disk('public')->exists($oldPhoto) ? 'OUI ✅' : 'NON ❌') . "\n";
    echo "   Nouvelle photo existe: " . (Storage::disk('public')->exists($updatedDriver->photo) ? 'OUI ✅' : 'NON ❌') . "\n\n";

    // Nettoyage
    echo "🧹 Nettoyage des données de test...\n";
    if ($updatedDriver->photo) {
        Storage::disk('public')->delete($updatedDriver->photo);
    }
    DB::table('model_has_roles')->where('model_id', $result['user']->id)->delete();
    $updatedDriver->forceDelete();
    if ($result['was_created']) {
        $result['user']->forceDelete();
    }

    @unlink($testImagePath);
    @unlink($newTestImagePath);

    echo "✅ Tests terminés avec succès!\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR: {$e->getMessage()}\n";
    echo "   Fichier: {$e->getFile()}:{$e->getLine()}\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
