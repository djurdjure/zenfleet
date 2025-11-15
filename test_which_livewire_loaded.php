<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║  🔍 QUEL COMPOSANT LIVEWIRE EST CHARGÉ ?                   ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Tester la résolution du nom
$componentName = 'assignment-form';

echo "Nom du composant: '{$componentName}'\n\n";

// Méthode 1: Via le ComponentRegistry de Livewire
try {
    $livewire = app('livewire');
    $componentClass = $livewire->getClass($componentName);

    echo "✅ Composant résolu par Livewire:\n";
    echo "  • Classe: {$componentClass}\n";

    if (class_exists($componentClass)) {
        $reflection = new ReflectionClass($componentClass);
        echo "  • Fichier: {$reflection->getFileName()}\n";
        echo "  • Namespace: {$reflection->getNamespaceName()}\n";

        // Vérifier la méthode mount()
        if ($reflection->hasMethod('mount')) {
            $mountMethod = $reflection->getMethod('mount');
            $startLine = $mountMethod->getStartLine();
            echo "  • Méthode mount() à la ligne: {$startLine}\n";

            // Lire le code de mount()
            $file = file($reflection->getFileName());
            echo "\n  📄 Code de mount():\n";
            for ($i = $startLine - 1; $i < min($startLine + 15, count($file)); $i++) {
                echo "    " . ($i + 1) . ": " . $file[$i];
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Erreur: {$e->getMessage()}\n";
}

// Méthode 2: Vérifier tous les AssignmentForm disponibles
echo "\n" . str_repeat("─", 66) . "\n";
echo "📁 Tous les fichiers AssignmentForm trouvés:\n\n";

$files = [
    '/var/www/html/app/Livewire/AssignmentForm.php',
    '/var/www/html/app/Livewire/Assignments/AssignmentForm.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}\n";

        // Lire les premières lignes pour trouver le namespace
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];
            $fullClass = $namespace . '\\AssignmentForm';
            echo "   Classe: {$fullClass}\n";

            // Vérifier si c'est celle utilisée
            if (isset($componentClass) && $componentClass === $fullClass) {
                echo "   🎯 <-- C'EST CELLE-CI QUI EST UTILISÉE\n";
            }
        }
        echo "\n";
    } else {
        echo "❌ {$file} (n'existe pas)\n\n";
    }
}

echo str_repeat("─", 66) . "\n";
echo "🔍 CONCLUSION:\n\n";

if (isset($componentClass)) {
    if (strpos($componentClass, 'Assignments\\') !== false) {
        echo "⚠️  Le composant chargé est dans le sous-dossier 'Assignments/'\n";
        echo "   → Fichier: app/Livewire/Assignments/AssignmentForm.php\n";
        echo "   → C'est CE FICHIER qu'il faut modifier !\n";
    } else {
        echo "✅ Le composant chargé est à la racine\n";
        echo "   → Fichier: app/Livewire/AssignmentForm.php\n";
        echo "   → C'est le bon fichier qui a été modifié\n";
    }
}

echo "\n";
