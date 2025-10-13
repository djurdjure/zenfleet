<?php

/**
 * 🧪 TEST MANUEL: Création de chauffeur via HTTP simulation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST MANUEL: Création de chauffeur\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Trouver un admin
$admin = App\Models\User::where('email', 'admin@zenfleet.dz')->first();
if (!$admin) {
    echo "❌ Admin non trouvé\n";
    exit(1);
}

echo "✅ Admin trouvé: {$admin->name} (ID: {$admin->id})\n";
echo "   Organization: {$admin->organization_id}\n\n";

// Auth simulation
Auth::login($admin);

// Données du formulaire
$data = [
    'first_name' => 'Karim',
    'last_name' => 'Benabdallah',
    'birth_date' => '1988-03-20',
    'personal_phone' => '+213770123456',
    'personal_email' => 'karim.benabdallah@gmail.com',
    'address' => '25 Rue Didouche Mourad, Alger Centre',
    'blood_type' => 'A+',
    'employee_number' => 'EMP-' . rand(10000, 99999),
    'status_id' => 1,
    'recruitment_date' => now()->subMonths(6)->format('Y-m-d'),
    'contract_end_date' => now()->addYears(2)->format('Y-m-d'),
    'license_number' => '16-' . rand(100000, 999999),
    'license_category' => 'B, C, D',
    'license_issue_date' => now()->subYears(8)->format('Y-m-d'),
    'license_authority' => 'Préfecture d\'Alger',
    'emergency_contact_name' => 'Samira Benabdallah',
    'emergency_contact_phone' => '+213770654321',
];

echo "📋 Données du formulaire préparées\n\n";

try {
    // Créer le FormRequest
    $request = new App\Http\Requests\Admin\Driver\StoreDriverRequest();
    $request->setContainer(app());
    $request->replace($data);

    echo "🔐 Validation du FormRequest...\n";

    // Valider
    $validated = $request->validated();

    echo "✅ Validation réussie!\n";
    echo "   Organization ID ajouté: {$validated['organization_id']}\n\n";

    // Créer le driver
    echo "👤 Création du chauffeur...\n";
    $service = app(App\Services\DriverService::class);
    $result = $service->createDriver($validated);

    echo "\n✅ SUCCÈS! Chauffeur créé:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 Driver:\n";
    echo "   ID: {$result['driver']->id}\n";
    echo "   Nom: {$result['driver']->first_name} {$result['driver']->last_name}\n";
    echo "   Matricule: {$result['driver']->employee_number}\n";
    echo "   Organization: {$result['driver']->organization_id}\n\n";

    echo "👤 User:\n";
    echo "   ID: {$result['user']->id}\n";
    echo "   Email: {$result['user']->email}\n";
    echo "   Créé: " . ($result['was_created'] ? 'OUI' : 'NON (existant)') . "\n";

    if ($result['password']) {
        echo "   Mot de passe: {$result['password']}\n";
    }

    echo "\n🔐 Rôles assignés:\n";
    $roles = $result['user']->roles()->get();
    foreach ($roles as $role) {
        $pivot = DB::table('model_has_roles')
            ->where('model_id', $result['user']->id)
            ->where('role_id', $role->id)
            ->first();
        echo "   - {$role->name} (organization_id: {$pivot->organization_id})\n";
    }

    echo "\n📊 Données de session (pour popup):\n";
    $sessionData = [
        'driver_created' => true,
        'driver_id' => $result['driver']->id,
        'driver_name' => $result['driver']->first_name . ' ' . $result['driver']->last_name,
        'driver_employee_number' => $result['driver']->employee_number,
        'user_email' => $result['user']->email,
        'user_password' => $result['password'],
        'user_was_created' => $result['was_created'],
    ];

    echo json_encode($sessionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    echo "🧹 Nettoyage des données de test...\n";
    DB::table('model_has_roles')->where('model_id', $result['user']->id)->delete();
    $result['driver']->forceDelete();
    if ($result['was_created']) {
        $result['user']->forceDelete();
    }

    echo "✅ Test terminé avec succès!\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR: {$e->getMessage()}\n";
    echo "   Fichier: {$e->getFile()}:{$e->getLine()}\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
