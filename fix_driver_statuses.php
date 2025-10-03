#!/usr/bin/env php
<?php

/**
 * 🔧 SCRIPT DE CORRECTION - Statuts Chauffeurs
 *
 * Exécute le seeder DriverStatusSeeder pour créer/mettre à jour les statuts
 * Vérifie ensuite que les données sont correctement créées
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 CORRECTION DES STATUTS CHAUFFEURS\n";
echo "=====================================\n\n";

try {
    // Exécuter le seeder directement sans command mock
    echo "📥 Exécution du DriverStatusSeeder...\n";
    $seeder = new \Database\Seeders\DriverStatusSeeder();

    // Utiliser la méthode callWith pour injecter un output handler
    \Illuminate\Database\Eloquent\Model::unguard();
    $seeder->__invoke();

    // Vérifier les résultats
    echo "\n📊 Vérification des statuts créés:\n";
    $statuses = \App\Models\DriverStatus::orderBy('sort_order')->get();

    echo "\n   Total: " . $statuses->count() . " statuts\n\n";

    foreach ($statuses as $status) {
        $canDrive = $status->can_drive ? '✓' : '✗';
        $canAssign = $status->can_assign ? '✓' : '✗';
        $isActive = $status->is_active ? '✓' : '✗';

        echo "   [{$status->sort_order}] {$status->name}\n";
        echo "       Actif: {$isActive} | Peut conduire: {$canDrive} | Peut être affecté: {$canAssign}\n";
        echo "       Couleur: {$status->color} | Icône: {$status->icon}\n";
        echo "       Description: {$status->description}\n\n";
    }

    echo "✅ CORRECTION TERMINÉE AVEC SUCCÈS!\n";
    echo "\n💡 Les statuts sont maintenant disponibles dans le formulaire d'ajout de chauffeurs.\n";

} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
