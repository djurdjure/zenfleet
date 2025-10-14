<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Contrôleur pour la gestion des sanctions des chauffeurs
 * 
 * Ce contrôleur sert de pont entre le système de routage Laravel et le composant Livewire
 * DriverSanctionIndex. Il garantit que le composant Livewire est correctement encapsulé
 * dans le layout principal de l'application, résolvant ainsi les problèmes d'affichage
 * et maintenant la cohérence visuelle de l'interface.
 * 
 * @package App\Http\Controllers\Admin
 * @version 1.0.0
 * @author ZenFleet Enterprise Team
 */
class DriverSanctionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Affiche la page principale de gestion des sanctions
     * 
     * Cette méthode retourne la vue qui encapsule le composant Livewire DriverSanctionIndex
     * dans le layout principal de l'application. Cette approche garantit :
     * - L'utilisation correcte du layout `layouts.admin.catalyst-enterprise`
     * - L'affichage cohérent du menu latéral sans décalage
     * - Le maintien du thème visuel de l'application
     * - La préservation de toutes les fonctionnalités Livewire
     * 
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Vérification des permissions d'accès
        // Cette vérification est également effectuée dans le composant Livewire,
        // mais nous la dupliquons ici pour une sécurité renforcée
        $this->authorize('viewAny', \App\Models\DriverSanction::class);

        // Retourne la vue Blade qui encapsule le composant Livewire
        // Le layout est géré par la directive @extends dans la vue
        return view('admin.sanctions.index', [
            'pageTitle' => 'Gestion des Sanctions Chauffeurs',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Chauffeurs', 'url' => route('admin.drivers.index')],
                ['title' => 'Sanctions', 'url' => null]
            ]
        ]);
    }

    /**
     * Export des sanctions en format Excel/CSV
     * 
     * Point d'entrée pour l'export des sanctions si nécessaire
     * (peut être implémenté ultérieurement)
     * 
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        $this->authorize('export', \App\Models\DriverSanction::class);
        
        // Cette méthode peut déclencher un job d'export ou appeler
        // directement une méthode du composant Livewire
        // Pour l'instant, on redirige vers la page principale avec un message
        return redirect()->route('admin.sanctions.index')
            ->with('info', 'La fonctionnalité d\'export sera disponible prochainement.');
    }

    /**
     * Affiche les statistiques des sanctions
     * 
     * Point d'entrée pour un dashboard de statistiques
     * (peut être implémenté ultérieurement)
     * 
     * @return \Illuminate\View\View
     */
    public function statistics(): View
    {
        $this->authorize('viewAny', \App\Models\DriverSanction::class);
        
        // Pour l'instant, on redirige vers la page principale
        // Une vue dédiée peut être créée ultérieurement
        return redirect()->route('admin.sanctions.index')
            ->with('info', 'Le tableau de bord des statistiques sera disponible prochainement.');
    }
}
