<?php

/**
 * 🔧 CORRECTION DE L'ERREUR DE SYNTAXE DANS ASSIGNMENTFORM
 */

use Illuminate\Support\Facades\File;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n🔧 CORRECTION DE L'ERREUR DE SYNTAXE\n";
echo str_repeat("─", 70) . "\n";

$filePath = __DIR__ . '/app/Livewire/AssignmentForm.php';

// Lire le contenu
$content = file_get_contents($filePath);

// Rechercher et corriger la méthode mount mal formatée
$correctMount = <<<'PHP'
    public function mount(?Assignment $assignment = null)
    {
        try {
            // Log d'entrée en mode debug
            if (config('app.debug')) {
                \Log::debug('AssignmentForm: Mount called', [
                    'assignment_id' => $assignment?->id,
                    'user' => auth()->user()->email
                ]);
            }
            
            if ($assignment && $assignment->exists) {
                $this->authorize('update', $assignment);
                $this->assignment = $assignment;
                $this->isEditing = true;
                $this->fillFromAssignment($assignment);
            } else {
                $this->authorize('create', Assignment::class);
                $this->initializeNewAssignment();
            }
            
            $this->loadOptions();
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \Log::warning('AssignmentForm: Authorization failed', [
                'user' => auth()->user()->email,
                'action' => $assignment ? 'update' : 'create',
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('AssignmentForm: Mount error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Initialiser avec des valeurs par défaut en cas d'erreur
            $this->initializeNewAssignment();
            $this->loadOptions();
            
            session()->flash('error', 'Erreur lors de l\'initialisation du formulaire.');
        }
    }
PHP;

// Trouver et remplacer la méthode mount complète (avec le else orphelin)
$pattern = '/public function mount\(\?Assignment \$assignment = null\)\s*{[^}]*(?:}[^}]*else[^}]*}[^}]*)*}\s*}\s*else\s*{[^}]*}\s*\n\s*\$this->loadOptions\(\);\s*}/s';

if (preg_match($pattern, $content)) {
    $content = preg_replace($pattern, $correctMount, $content);
    echo "  ✅ Méthode mount corrigée (syntaxe complexe)\n";
} else {
    // Tentative alternative - remplacer entre mount et render
    $pattern = '/public function mount\(\?Assignment[^}]+}\s*}\s*else[^}]+}\s*[^}]*loadOptions[^}]*}/s';
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $correctMount, $content);
        echo "  ✅ Méthode mount corrigée (pattern alternatif)\n";
    } else {
        // Méthode brutale: chercher et nettoyer manuellement
        $startPos = strpos($content, 'public function mount(?Assignment');
        $renderPos = strpos($content, 'public function render()');
        
        if ($startPos !== false && $renderPos !== false) {
            $before = substr($content, 0, $startPos);
            $after = substr($content, $renderPos);
            $content = $before . $correctMount . "\n\n    " . $after;
            echo "  ✅ Méthode mount remplacée complètement\n";
        }
    }
}

// Sauvegarder le fichier corrigé
file_put_contents($filePath, $content);
echo "  ✅ Fichier sauvegardé\n";

// Même correction pour l'autre fichier
$filePath2 = __DIR__ . '/app/Livewire/Assignments/AssignmentForm.php';
if (file_exists($filePath2)) {
    $content2 = file_get_contents($filePath2);
    
    // Appliquer la même correction
    $startPos = strpos($content2, 'public function mount(?Assignment');
    $renderPos = strpos($content2, 'public function render()');
    
    if ($startPos !== false && $renderPos !== false) {
        $before = substr($content2, 0, $startPos);
        $after = substr($content2, $renderPos);
        $content2 = $before . $correctMount . "\n\n    " . $after;
        file_put_contents($filePath2, $content2);
        echo "  ✅ Second fichier corrigé aussi\n";
    }
}

echo "\n✅ Correction de syntaxe terminée\n";
