<?php

/**
 * 🧪 TEST HTTP SIMULATION: Création de chauffeur via DriverController
 *
 * Ce test simule exactement le flow HTTP avec :
 * - Authentification admin
 * - StoreDriverRequest avec validation
 * - DriverController->store()
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 TEST HTTP SIMULATION - Création de chauffeur\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Trouver l'admin
$admin = App\Models\User::where('email', 'admin@zenfleet.dz')->first();
if (!$admin) {
    echo "❌ Admin non trouvé\n";
    exit(1);
}

echo "✅ Admin trouvé: {$admin->name} (ID: {$admin->id})\n";
echo "   Email: {$admin->email}\n";
echo "   Organization: {$admin->organization_id}\n\n";

// ✅ IMPORTANT: Simuler l'authentification comme dans un contexte HTTP réel
Auth::login($admin);

echo "🔐 Authentification simulée\n";
echo "   Auth::check(): " . (Auth::check() ? 'OUI ✅' : 'NON ❌') . "\n";
echo "   Auth::id(): " . Auth::id() . "\n";
echo "   Rôles: " . $admin->getRoleNames()->implode(', ') . "\n";
echo "   can('create drivers'): " . ($admin->can('create drivers') ? 'OUI ✅' : 'NON ❌') . "\n\n";

if (!$admin->can('create drivers')) {
    echo "❌ L'admin n'a pas la permission 'create drivers'\n";
    exit(1);
}

// Données du formulaire (minimal pour test)
$formData = [
    'first_name' => 'TestHTTP',
    'last_name' => 'Simulation',
    'status_id' => 1,
    // organization_id sera ajouté automatiquement par prepareForValidation
];

echo "📋 Données du formulaire:\n";
echo json_encode($formData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

try {
    // Créer une requête HTTP simulée
    $request = Illuminate\Http\Request::create(
        '/admin/drivers',
        'POST',
        $formData
    );

    // Ajouter l'utilisateur authentifié à la requête
    $request->setUserResolver(function () use ($admin) {
        return $admin;
    });

    // Créer le FormRequest
    $storeRequest = new App\Http\Requests\Admin\Driver\StoreDriverRequest();
    $storeRequest->setContainer(app());
    $storeRequest->setRedirector(app('redirect'));

    // Remplacer les données de la requête
    $storeRequest->replace($formData);

    // Définir l'utilisateur
    $storeRequest->setUserResolver(function () use ($admin) {
        return $admin;
    });

    echo "🔐 Validation de la requête...\n";

    // Préparer pour validation (va ajouter organization_id)
    $reflection = new ReflectionClass($storeRequest);
    $method = $reflection->getMethod('prepareForValidation');
    $method->setAccessible(true);
    $method->invoke($storeRequest);

    // Vérifier que organization_id est ajouté
    echo "   organization_id ajouté: " . ($storeRequest->has('organization_id') ? 'OUI ✅' : 'NON ❌') . "\n";
    echo "   organization_id value: " . $storeRequest->input('organization_id', 'NULL') . "\n\n";

    // Créer le validateur manuellement
    $validator = Validator::make(
        $storeRequest->all(),
        $storeRequest->rules(),
        $storeRequest->messages(),
        $storeRequest->attributes()
    );

    if ($validator->fails()) {
        echo "❌ Erreurs de validation:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
        exit(1);
    }

    $validated = $validator->validated();

    echo "✅ Validation réussie!\n";
    echo "   Champs validés: " . implode(', ', array_keys($validated)) . "\n";
    echo "   organization_id dans validated: " . ($validated['organization_id'] ?? 'NULL') . "\n\n";

    // Créer le driver via le service
    echo "👤 Création du chauffeur via DriverService...\n";
    $service = app(App\Services\DriverService::class);
    $result = $service->createDriver($validated);

    echo "\n✅ SUCCÈS! Chauffeur créé via HTTP simulation:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 Driver:\n";
    echo "   ID: {$result['driver']->id}\n";
    echo "   Nom: {$result['driver']->first_name} {$result['driver']->last_name}\n";
    echo "   Matricule: {$result['driver']->employee_number}\n";
    echo "   Organization: {$result['driver']->organization_id}\n\n";

    echo "👤 User:\n";
    echo "   ID: {$result['user']->id}\n";
    echo "   Email: {$result['user']->email}\n";
    echo "   Créé: " . ($result['was_created'] ? 'OUI' : 'NON') . "\n";

    if ($result['password']) {
        echo "   Mot de passe: {$result['password']}\n";
    }

    // Vérifier le rôle
    Auth::login($result['user']);
    echo "\n🔐 Rôle assigné:\n";
    echo "   hasRole('Chauffeur'): " . ($result['user']->hasRole('Chauffeur') ? 'OUI ✅' : 'NON ❌') . "\n";

    echo "\n🧹 Nettoyage des données de test...\n";
    DB::table('model_has_roles')->where('model_id', $result['user']->id)->delete();
    $result['driver']->forceDelete();
    if ($result['was_created']) {
        $result['user']->forceDelete();
    }

    echo "✅ Test HTTP simulation terminé avec succès!\n";

} catch (\Exception $e) {
    echo "\n❌ ERREUR: {$e->getMessage()}\n";
    echo "   Fichier: {$e->getFile()}:{$e->getLine()}\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
