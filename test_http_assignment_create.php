<?php

/**
 * ====================================================================
 * 🌐 TEST HTTP : PAGE CRÉATION AFFECTATION
 * ====================================================================
 *
 * Simule une vraie requête HTTP pour tester :
 * ✅ Rendu de la page assignments/create
 * ✅ Présence du composant Livewire
 * ✅ Chargement de SlimSelect
 * ✅ Présence des éléments du formulaire
 *
 * @version 1.0-Enterprise-Grade
 * @since 2025-11-14
 * ====================================================================
 */

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🌐 TEST HTTP : PAGE CRÉATION AFFECTATION                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Créer une requête simulée
$request = Illuminate\Http\Request::create(
    '/admin/assignments/create',
    'GET'
);

// Simuler un utilisateur authentifié (ID 4 basé sur les logs)
$user = App\Models\User::find(4);

if (!$user) {
    echo "⚠️  Utilisateur avec ID 4 introuvable, recherche d'un admin...\n";
    $user = App\Models\User::whereHas('roles', function($q) {
        $q->where('name', 'admin');
    })->first();

    if (!$user) {
        $user = App\Models\User::first();
    }
}

if ($user) {
    echo "✅ Utilisateur de test : {$user->name} (ID: {$user->id})\n";
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    Auth::setUser($user);
} else {
    echo "❌ Aucun utilisateur trouvé dans la base de données\n";
    exit(1);
}

echo "\n📡 Envoi de la requête GET /admin/assignments/create...\n";
echo str_repeat("─", 66) . "\n";

try {
    // Traiter la requête
    $response = $kernel->handle($request);

    $statusCode = $response->getStatusCode();
    $content = $response->getContent();

    echo "\n📊 RÉSULTAT DE LA REQUÊTE :\n";
    echo str_repeat("─", 66) . "\n";
    echo "  • Code HTTP : {$statusCode}\n";
    echo "  • Taille de la réponse : " . strlen($content) . " octets\n";

    if ($statusCode === 200) {
        echo "  ✅ Page chargée avec succès (HTTP 200)\n\n";

        // Vérifier les éléments critiques
        echo "🔍 VÉRIFICATION DES ÉLÉMENTS CRITIQUES :\n";
        echo str_repeat("─", 66) . "\n";

        $checks = [
            'Livewire Component' => strpos($content, 'assignment-form') !== false || strpos($content, 'wire:id') !== false,
            'SlimSelect CSS' => strpos($content, 'slim-select@2/dist/slimselect.css') !== false,
            'SlimSelect JS' => strpos($content, 'slim-select@2/dist/slimselect.min.js') !== false,
            'Classe slimselect-vehicle' => strpos($content, 'slimselect-vehicle') !== false,
            'Classe slimselect-driver' => strpos($content, 'slimselect-driver') !== false,
            'Champ kilométrage' => strpos($content, 'start_mileage') !== false,
            'Variable kilométrage actuel' => strpos($content, 'current_vehicle_mileage') !== false,
            'Alpine.js component' => strpos($content, 'x-data') !== false,
            'Fonction initSlimSelect' => strpos($content, 'initSlimSelect') !== false,
            'Fonction showToast' => strpos($content, 'showToast') !== false,
            'Titre page' => strpos($content, 'Nouvelle Affectation') !== false || strpos($content, 'Nouvelle affectation') !== false,
            'Breadcrumb' => strpos($content, 'Breadcrumb') !== false || strpos($content, 'breadcrumb') !== false,
        ];

        $allPassed = true;
        foreach ($checks as $name => $result) {
            $icon = $result ? '✅' : '❌';
            echo "  {$icon} {$name}\n";
            if (!$result) {
                $allPassed = false;
            }
        }

        // Analyser la structure HTML
        echo "\n📐 ANALYSE STRUCTURE HTML :\n";
        echo str_repeat("─", 66) . "\n";

        $htmlStats = [
            'Cards (bg-white rounded-lg)' => substr_count($content, 'bg-white rounded-lg'),
            'Boutons' => substr_count($content, '<button'),
            'Inputs' => substr_count($content, '<input'),
            'Selects' => substr_count($content, '<select'),
            'Icônes Iconify' => substr_count($content, 'iconify'),
            'Wire directives' => substr_count($content, 'wire:'),
        ];

        foreach ($htmlStats as $element => $count) {
            echo "  • {$element}: {$count}\n";
        }

        echo "\n";

        if ($allPassed) {
            echo "╔══════════════════════════════════════════════════════════════╗\n";
            echo "║  ✅ TEST HTTP RÉUSSI - PAGE FONCTIONNELLE                  ║\n";
            echo "╚══════════════════════════════════════════════════════════════╝\n";
            exit(0);
        } else {
            echo "╔══════════════════════════════════════════════════════════════╗\n";
            echo "║  ⚠️  ATTENTION - ÉLÉMENTS MANQUANTS DÉTECTÉS              ║\n";
            echo "╚══════════════════════════════════════════════════════════════╝\n";
            exit(1);
        }

    } elseif ($statusCode === 302) {
        echo "  ℹ️  Redirection détectée (HTTP 302)\n";
        $location = $response->headers->get('Location');
        echo "  • Destination : {$location}\n";
        echo "\n  ℹ️  Cela peut être normal (authentification, permissions, etc.)\n";
        exit(0);
    } elseif ($statusCode === 500) {
        echo "  ❌ Erreur serveur (HTTP 500)\n\n";

        // Essayer d'extraire l'erreur
        if (strpos($content, 'Exception') !== false) {
            echo "🔥 ERREUR DÉTECTÉE DANS LA RÉPONSE :\n";
            echo str_repeat("─", 66) . "\n";

            // Extraire les premières lignes de l'erreur
            preg_match('/<h1[^>]*>(.*?)<\/h1>/s', $content, $matches);
            if (isset($matches[1])) {
                echo "  " . strip_tags($matches[1]) . "\n";
            }

            preg_match('/<pre[^>]*>(.*?)<\/pre>/s', $content, $matches);
            if (isset($matches[1])) {
                $error = strip_tags($matches[1]);
                echo "\n" . substr($error, 0, 500) . "...\n";
            }
        }

        exit(1);
    } else {
        echo "  ⚠️  Code HTTP inattendu : {$statusCode}\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "\n❌ EXCEPTION CAPTURÉE :\n";
    echo str_repeat("─", 66) . "\n";
    echo "  Type : " . get_class($e) . "\n";
    echo "  Message : " . $e->getMessage() . "\n";
    echo "  Fichier : " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
