<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 ANALYSE DU 403 RÉEL - Capture HTML de la réponse       ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$user = \App\Models\User::where('email', 'admin@zenfleet.dz')->first();

if (!$user) {
    echo "❌ Utilisateur non trouvé\n";
    exit(1);
}

// Authentifier l'utilisateur
\Illuminate\Support\Facades\Auth::login($user);

// Créer la requête
$request = \Illuminate\Http\Request::create('/admin/assignments/create', 'GET');

echo "👤 Utilisateur: {$user->email}\n";
echo "📍 Route: /admin/assignments/create\n\n";

echo str_repeat("─", 66) . "\n";
echo "🚀 EXÉCUTION DE LA REQUÊTE\n";
echo str_repeat("─", 66) . "\n\n";

try {
    // Exécuter à travers le kernel complet
    $response = $kernel->handle($request);

    $statusCode = $response->getStatusCode();
    echo "📥 Status Code: {$statusCode}\n\n";

    if ($statusCode === 403) {
        echo "❌ 403 DÉTECTÉ ! Analysons le contenu HTML...\n\n";

        $content = $response->getContent();

        // Extraire le titre
        if (preg_match('/<title>(.*?)<\/title>/s', $content, $matches)) {
            echo "📄 Titre: {$matches[1]}\n\n";
        }

        // Extraire le message principal
        if (preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $content, $matches)) {
            echo "🔴 Message principal: " . strip_tags($matches[1]) . "\n\n";
        }

        // Chercher le message d'erreur détaillé
        if (preg_match('/<div[^>]*class="[^"]*message[^"]*"[^>]*>(.*?)<\/div>/s', $content, $matches)) {
            echo "📝 Détails: " . strip_tags($matches[1]) . "\n\n";
        }

        // Chercher "This action is unauthorized"
        if (str_contains($content, 'This action is unauthorized')) {
            echo "⚠️  Type: Laravel AuthorizationException standard\n";
            echo "   → Provient d'un \$this->authorize() dans un contrôleur ou composant\n\n";
        }

        // Chercher le message EnterprisePermissionMiddleware
        if (preg_match('/Vous n.avez pas l.autorisation/i', $content)) {
            echo "⚠️  Type: EnterprisePermissionMiddleware\n";
            echo "   → Provient du middleware personnalisé\n\n";
        }

        // Afficher les 500 premiers caractères du body
        if (preg_match('/<body[^>]*>(.*?)<\/body>/s', $content, $matches)) {
            $body = strip_tags($matches[1]);
            $body = preg_replace('/\s+/', ' ', $body);
            $body = trim(substr($body, 0, 500));
            echo "📄 Extrait du contenu:\n";
            echo "   " . $body . "...\n\n";
        }

        // Sauvegarder le HTML complet
        file_put_contents(__DIR__ . '/debug_403_response.html', $content);
        echo "💾 HTML complet sauvegardé dans: debug_403_response.html\n\n";

    } elseif ($statusCode === 200) {
        echo "✅ 200 OK - La page fonctionne !\n";
        echo "   Taille du contenu: " . strlen($response->getContent()) . " octets\n";

    } elseif ($statusCode === 302) {
        echo "↪️  302 Redirection vers: " . $response->headers->get('Location') . "\n";
    }

    echo "\n" . str_repeat("─", 66) . "\n";
    echo "🔍 ANALYSE DES HEADERS\n";
    echo str_repeat("─", 66) . "\n\n";

    foreach ($response->headers->all() as $name => $values) {
        foreach ($values as $value) {
            if (in_array(strtolower($name), ['content-type', 'location', 'set-cookie', 'x-powered-by'])) {
                echo "   • {$name}: {$value}\n";
            }
        }
    }

} catch (\Illuminate\Auth\Access\AuthorizationException $e) {
    echo "❌ AuthorizationException capturée :\n";
    echo "   • Message: {$e->getMessage()}\n";
    echo "   • Fichier: {$e->getFile()}:{$e->getLine()}\n";
    echo "   • Trace:\n";

    $trace = $e->getTrace();
    for ($i = 0; $i < min(5, count($trace)); $i++) {
        $item = $trace[$i];
        echo "      #{$i} ";
        if (isset($item['file'])) {
            echo basename($item['file']) . ":{$item['line']} ";
        }
        if (isset($item['class'])) {
            echo "{$item['class']}{$item['type']}";
        }
        if (isset($item['function'])) {
            echo "{$item['function']}()\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Exception capturée:\n";
    echo "   • Type: " . get_class($e) . "\n";
    echo "   • Message: {$e->getMessage()}\n";
    echo "   • Fichier: {$e->getFile()}:{$e->getLine()}\n";
}

echo "\n";

$kernel->terminate($request, $response ?? null);
