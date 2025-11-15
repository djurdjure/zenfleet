<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🌐 TEST HTTP RÉEL - Simulation Navigateur                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Créer une requête HTTP GET vers /admin/assignments/create
$request = \Illuminate\Http\Request::create(
    '/admin/assignments/create',
    'GET',
    [], // parameters
    [], // cookies
    [], // files
    ['REMOTE_ADDR' => '127.0.0.1'] // server
);

echo "📤 Requête HTTP simulée:\n";
echo "   • URI: /admin/assignments/create\n";
echo "   • Méthode: GET\n";
echo "   • IP: 127.0.0.1\n\n";

// Connexion de l'utilisateur dans la session
$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur admin@zenfleet.dz non trouvé\n";
    exit(1);
}

echo "👤 Utilisateur simulé:\n";
echo "   • Email: {$user->email}\n";
echo "   • Rôle: " . ($user->roles->pluck('name')->first() ?? 'Aucun') . "\n\n";

// Créer une session Laravel
$session = new \Illuminate\Session\Store(
    'test-session',
    new \Illuminate\Session\ArraySessionHandler(60)
);

// Authentifier l'utilisateur dans la session
\Illuminate\Support\Facades\Auth::setUser($user);
$session->put('_token', 'test-token');
$session->put('login_web_' . sha1(\Illuminate\Support\Facades\Auth::getDefaultDriver()), $user->id);

$request->setLaravelSession($session);

echo str_repeat("─", 66) . "\n";
echo "🚀 EXÉCUTION DE LA REQUÊTE HTTP\n";
echo str_repeat("─", 66) . "\n\n";

try {
    // Exécuter la requête à travers le kernel HTTP complet
    $response = $kernel->handle($request);

    $statusCode = $response->getStatusCode();
    echo "📥 Réponse HTTP reçue:\n";
    echo "   • Status Code: {$statusCode}\n";

    if ($statusCode === 200) {
        echo "   • ✅ SUCCÈS - Page accessible\n";

        // Analyser le contenu
        $content = $response->getContent();
        $contentLength = strlen($content);
        echo "   • Taille du contenu: " . number_format($contentLength) . " octets\n";

        // Vérifier si c'est bien la page wizard
        if (str_contains($content, 'assignment') || str_contains($content, 'wizard') || str_contains($content, 'Affectation')) {
            echo "   • ✅ Contenu: Page d'affectation détectée\n";
        } else {
            echo "   • ⚠️  Contenu: Pas de trace de page d'affectation\n";
        }

    } elseif ($statusCode === 403) {
        echo "   • ❌ ERREUR 403 - Accès non autorisé\n";

        $content = $response->getContent();

        // Extraire le message d'erreur
        if (preg_match('/<h1[^>]*>([^<]+)<\/h1>/', $content, $matches)) {
            echo "   • Message: {$matches[1]}\n";
        }

        if (preg_match('/<div class="message">([^<]+)<\/div>/', $content, $matches)) {
            echo "   • Détails: {$matches[1]}\n";
        }

        // Chercher le texte "This action is unauthorized"
        if (str_contains($content, 'This action is unauthorized')) {
            echo "   • ⚠️  Message Laravel standard détecté\n";
        }

        if (str_contains($content, 'Vous n\'avez pas l\'autorisation')) {
            echo "   • ⚠️  Message EnterprisePermissionMiddleware détecté\n";
        }

    } elseif ($statusCode === 302) {
        echo "   • ↪️  REDIRECTION\n";
        echo "   • Location: " . $response->headers->get('Location') . "\n";

        // Vérifier si c'est une redirection vers login
        if (str_contains($response->headers->get('Location'), 'login')) {
            echo "   • ⚠️  Redirection vers login (problème d'authentification)\n";
        }

    } else {
        echo "   • ⚠️  Status inattendu: {$statusCode}\n";
    }

    echo "\n" . str_repeat("─", 66) . "\n";
    echo "📋 HEADERS DE RÉPONSE\n";
    echo str_repeat("─", 66) . "\n\n";

    foreach ($response->headers->all() as $name => $values) {
        foreach ($values as $value) {
            echo "   • {$name}: {$value}\n";
        }
    }

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ AuthorizationException capturée:\n";
    echo "   • Message: {$e->getMessage()}\n";
    echo "   • Fichier: {$e->getFile()}:{$e->getLine()}\n";

} catch (Exception $e) {
    echo "❌ Exception capturée:\n";
    echo "   • Type: " . get_class($e) . "\n";
    echo "   • Message: {$e->getMessage()}\n";
    echo "   • Fichier: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n" . str_repeat("─", 66) . "\n";
echo "🎯 CONCLUSION\n";
echo str_repeat("─", 66) . "\n\n";

if (isset($statusCode)) {
    if ($statusCode === 200) {
        echo "✅ L'accès HTTP fonctionne correctement\n";
        echo "   → Le problème vient peut-être du navigateur (cache, cookies, session)\n";
        echo "   → Recommandations:\n";
        echo "     1. Vider le cache du navigateur (Ctrl+Shift+Delete)\n";
        echo "     2. Se déconnecter et se reconnecter\n";
        echo "     3. Essayer en navigation privée\n";
        echo "     4. Vérifier les cookies de session\n";
    } elseif ($statusCode === 403) {
        echo "❌ Le problème 403 PERSISTE au niveau HTTP\n";
        echo "   → Il y a un problème dans la chaîne de middlewares HTTP\n";
        echo "   → Vérifier les logs Laravel pour plus de détails\n";
    } elseif ($statusCode === 302) {
        echo "⚠️  Redirection détectée\n";
        echo "   → Vérifier l'authentification de la session\n";
    }
}

echo "\n";

// Terminer la requête
$kernel->terminate($request, $response);
