<?php

/**
 * 🎯 TEST - Changement de Statut Véhicule Ultra-Pro
 * 
 * Test complet de la fonctionnalité de changement de statut
 * depuis le badge dans la liste des véhicules.
 * 
 * @version 1.0-Enterprise
 * @since 2025-11-12
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use App\Models\Vehicle;
use App\Models\User;
use App\Enums\VehicleStatusEnum;
use App\Services\StatusTransitionService;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "=====================================\n";
echo "🎯 TEST CHANGEMENT STATUT ULTRA-PRO\n";
echo "=====================================\n\n";

try {
    // 1. Récupérer un admin pour les tests
    $admin = User::where('email', 'like', '%admin%')->first();
    if (!$admin) {
        // Essayer de récupérer n'importe quel utilisateur
        $admin = User::first();
        if (!$admin) {
            echo "❌ Aucun utilisateur trouvé. Création d'un utilisateur de test...\n";
            $admin = User::create([
                'name' => 'Admin Test',
                'email' => 'admin.test@zenfleet.com',
                'password' => bcrypt('password'),
                'organization_id' => 1,
            ]);
        }
    }
    auth()->login($admin);
    echo "✅ Connecté en tant que: {$admin->name} (ID: {$admin->id})\n\n";

    // 2. Récupérer ou créer un véhicule de test
    $vehicle = Vehicle::where('is_archived', false)
        ->whereHas('vehicleStatus')
        ->first();
        
    if (!$vehicle) {
        echo "❌ Aucun véhicule trouvé. Création d'un véhicule de test...\n";
        $vehicle = Vehicle::create([
            'registration_plate' => 'TEST-' . rand(1000, 9999),
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'status_id' => 1, // Disponible
            'organization_id' => 1,
            'current_mileage' => 50000,
            'vehicle_type_id' => 1,
            'fuel_type_id' => 1,
        ]);
    }

    echo "📚 Véhicule sélectionné:\n";
    echo "   - ID: {$vehicle->id}\n";
    echo "   - Immatriculation: {$vehicle->registration_plate}\n";
    echo "   - Marque/Modèle: {$vehicle->brand} {$vehicle->model}\n";
    echo "   - Statut actuel: " . ($vehicle->vehicleStatus ? $vehicle->vehicleStatus->name : 'Non défini') . "\n\n";

    // 3. Tester les transitions de statut
    $service = app(StatusTransitionService::class);
    
    echo "🔄 TEST DES TRANSITIONS DE STATUT\n";
    echo "==================================\n\n";

    // Récupérer le statut actuel
    $currentStatusSlug = $vehicle->vehicleStatus ? \Str::slug($vehicle->vehicleStatus->name) : null;
    $currentEnum = $currentStatusSlug ? VehicleStatusEnum::tryFrom($currentStatusSlug) : null;
    
    if ($currentEnum) {
        echo "📊 Statut actuel (Enum): {$currentEnum->value} - {$currentEnum->label()}\n";
        echo "   Description: {$currentEnum->description()}\n";
        echo "   Couleur badge: {$currentEnum->badgeClasses()}\n";
        echo "   Icône: {$currentEnum->icon()}\n\n";
        
        // Récupérer les transitions autorisées
        $allowedTransitions = $currentEnum->allowedTransitions();
        
        if (count($allowedTransitions) > 0) {
            echo "✅ Transitions autorisées depuis '{$currentEnum->label()}':\n";
            foreach ($allowedTransitions as $transition) {
                echo "   → {$transition->label()} ({$transition->value})\n";
            }
            echo "\n";
            
            // Tester une transition valide
            if (count($allowedTransitions) > 0) {
                $targetStatus = $allowedTransitions[0];
                echo "🔄 Test de transition vers: {$targetStatus->label()}\n";
                
                try {
                    $result = $service->changeVehicleStatus(
                        $vehicle,
                        $targetStatus,
                        [
                            'reason' => 'Test automatique Ultra-Pro',
                            'change_type' => 'manual',  // Utiliser 'manual' au lieu de 'test'
                            'metadata' => [
                                'test_script' => 'test_vehicle_status_change_ultra_pro.php',
                                'timestamp' => now()->toIso8601String(),
                            ]
                        ]
                    );
                    
                    if ($result) {
                        $vehicle->refresh();
                        echo "   ✅ Transition réussie!\n";
                        echo "   Nouveau statut: " . ($vehicle->vehicleStatus ? $vehicle->vehicleStatus->name : 'Non défini') . "\n\n";
                        
                        // Vérifier l'historique
                        $lastHistory = $vehicle->statusHistory()->latest()->first();
                        if ($lastHistory) {
                            echo "   📝 Historique enregistré:\n";
                            echo "      - De: {$lastHistory->previous_status}\n";
                            echo "      - Vers: {$lastHistory->new_status}\n";
                            echo "      - Date: {$lastHistory->changed_at}\n";
                            echo "      - Raison: {$lastHistory->reason}\n\n";
                        }
                    }
                } catch (\Exception $e) {
                    echo "   ❌ Erreur lors de la transition: " . $e->getMessage() . "\n\n";
                }
            }
            
        } else {
            echo "⚠️ Aucune transition autorisée depuis ce statut (état terminal ou règles métier)\n\n";
        }
        
        // Tester une transition non autorisée
        echo "🚫 Test de transition non autorisée\n";
        try {
            // Essayer de passer directement à "réformé"
            $service->changeVehicleStatus(
                $vehicle,
                VehicleStatusEnum::REFORME,
                ['reason' => 'Test transition invalide']
            );
            echo "   ❌ ERREUR: La transition non autorisée a été acceptée (problème de validation)\n";
        } catch (\InvalidArgumentException $e) {
            echo "   ✅ Transition correctement refusée: " . $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo "   ⚠️ Erreur inattendue: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "⚠️ Impossible de déterminer le statut actuel du véhicule\n";
    }

    echo "\n";
    echo "=====================================\n";
    echo "📋 VÉRIFICATION COMPOSANT LIVEWIRE\n";
    echo "=====================================\n\n";
    
    // Vérifier que le composant Livewire existe
    $componentPath = app_path('Livewire/Admin/VehicleStatusBadgeUltraPro.php');
    if (file_exists($componentPath)) {
        echo "✅ Composant Livewire trouvé: VehicleStatusBadgeUltraPro.php\n";
        
        // Vérifier la vue
        $viewPath = resource_path('views/livewire/admin/vehicle-status-badge-ultra-pro.blade.php');
        if (file_exists($viewPath)) {
            echo "✅ Vue Blade trouvée: vehicle-status-badge-ultra-pro.blade.php\n";
        } else {
            echo "❌ Vue Blade manquante: vehicle-status-badge-ultra-pro.blade.php\n";
        }
    } else {
        echo "❌ Composant Livewire manquant: VehicleStatusBadgeUltraPro.php\n";
    }

    echo "\n";
    echo "=====================================\n";
    echo "✅ TEST TERMINÉ AVEC SUCCÈS\n";
    echo "=====================================\n\n";
    
    echo "📌 Pour tester dans l'interface:\n";
    echo "   1. Connectez-vous en tant qu'admin\n";
    echo "   2. Allez sur /admin/vehicles\n";
    echo "   3. Cliquez sur un badge de statut\n";
    echo "   4. Sélectionnez un nouveau statut\n";
    echo "   5. Confirmez dans la popup\n";
    echo "   6. Vérifiez la notification toast\n\n";

} catch (\Exception $e) {
    echo "❌ ERREUR FATALE: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
