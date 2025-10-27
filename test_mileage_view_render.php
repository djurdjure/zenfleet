<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "TEST: Vérification du rendu de la vue\n";
echo "========================================\n\n";

// 1. Vérifier que le fichier blade principal existe
$mainBlade = resource_path('views/admin/mileage-readings/update.blade.php');
echo "1. Fichier blade principal:\n";
echo "   Chemin: $mainBlade\n";
echo "   Existe: " . (file_exists($mainBlade) ? '✅ OUI' : '❌ NON') . "\n";

if (file_exists($mainBlade)) {
    $content = file_get_contents($mainBlade);
    echo "   Contenu:\n";
    echo "   " . str_replace("\n", "\n   ", trim($content)) . "\n";
}

echo "\n";

// 2. Vérifier que le fichier Livewire existe
$livewireBlade = resource_path('views/livewire/admin/update-vehicle-mileage.blade.php');
echo "2. Fichier blade Livewire:\n";
echo "   Chemin: $livewireBlade\n";
echo "   Existe: " . (file_exists($livewireBlade) ? '✅ OUI' : '❌ NON') . "\n";
echo "   Taille: " . (file_exists($livewireBlade) ? filesize($livewireBlade) . ' bytes' : 'N/A') . "\n";
echo "   Modifié: " . (file_exists($livewireBlade) ? date('Y-m-d H:i:s', filemtime($livewireBlade)) : 'N/A') . "\n";

echo "\n";

// 3. Vérifier le contenu du fichier Livewire
if (file_exists($livewireBlade)) {
    $content = file_get_contents($livewireBlade);
    
    echo "3. Analyse du contenu du fichier Livewire:\n";
    echo "   Contient 'vehicleData': " . (strpos($content, 'vehicleData') !== false ? '✅ OUI' : '❌ NON') . "\n";
    echo "   Contient 'selectedVehicle': " . (strpos($content, 'selectedVehicle') !== false ? '✅ OUI' : '❌ NON') . "\n";
    
    // Compter les occurrences
    $vehicleDataCount = substr_count($content, 'vehicleData');
    $selectedVehicleCount = substr_count($content, 'selectedVehicle');
    
    echo "   Occurrences 'vehicleData': $vehicleDataCount\n";
    echo "   Occurrences 'selectedVehicle': $selectedVehicleCount\n";
    
    // Afficher quelques lignes avec vehicleData
    echo "\n   Exemples de lignes avec 'vehicleData':\n";
    $lines = explode("\n", $content);
    $count = 0;
    foreach ($lines as $num => $line) {
        if (strpos($line, 'vehicleData') !== false && $count < 5) {
            echo "   Ligne " . ($num + 1) . ": " . trim($line) . "\n";
            $count++;
        }
    }
}

echo "\n";

// 4. Vérifier le composant Livewire
echo "4. Composant Livewire:\n";
$componentClass = \App\Livewire\Admin\UpdateVehicleMileage::class;
echo "   Classe: $componentClass\n";
echo "   Existe: " . (class_exists($componentClass) ? '✅ OUI' : '❌ NON') . "\n";

if (class_exists($componentClass)) {
    $reflection = new ReflectionClass($componentClass);
    $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    
    echo "   Propriétés publiques:\n";
    foreach ($properties as $prop) {
        echo "      - " . $prop->getName() . "\n";
    }
    
    // Vérifier la méthode render
    if ($reflection->hasMethod('render')) {
        $renderMethod = $reflection->getMethod('render');
        $renderFile = $renderMethod->getFileName();
        $renderLine = $renderMethod->getStartLine();
        
        echo "\n   Méthode render():\n";
        echo "      Fichier: $renderFile\n";
        echo "      Ligne: $renderLine\n";
        
        // Lire le code de la méthode render
        $file = file($renderFile);
        $methodCode = '';
        for ($i = $renderLine - 1; $i < min($renderLine + 10, count($file)); $i++) {
            $methodCode .= $file[$i];
        }
        echo "      Code:\n";
        echo "      " . str_replace("\n", "\n      ", trim($methodCode)) . "\n";
    }
}

echo "\n";
echo "========================================\n";
echo "FIN DU TEST\n";
echo "========================================\n";
