<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * 📊 Contrôleur des Relevés Kilométriques - Enterprise Grade
 *
 * Gère l'affichage de la page des relevés kilométriques avec composant Livewire.
 * Pattern moderne Livewire 3: Route → Controller → View → @livewire
 *
 * @package App\Http\Controllers\Admin
 * @author ZenFleet Development Team
 * @version 2.0 - Livewire 3 Compatible
 */
class MileageReadingController extends Controller
{
    /**
     * Afficher la page principale des relevés kilométriques
     *
     * Cette méthode retourne une vue Blade qui charge le composant Livewire
     * MileageReadingsIndex pour une gestion interactive des relevés.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.mileage-readings.index');
    }

    /**
     * Afficher l'historique kilométrique d'un véhicule
     *
     * Cette méthode retourne une vue Blade qui charge le composant Livewire
     * VehicleMileageHistory avec le bon binding du paramètre vehicleId.
     *
     * Pattern Enterprise: Controller gère le routing et Model Binding,
     * puis passe l'ID au composant Livewire pour éviter les problèmes
     * de résolution de dépendances.
     *
     * @param int $vehicle ID du véhicule (auto-résolu par Laravel)
     * @return \Illuminate\View\View
     */
    public function history(int $vehicle)
    {
        return view('admin.mileage-readings.history', [
            'vehicleId' => $vehicle
        ]);
    }

    /**
     * Afficher la page de mise à jour du kilométrage
     *
     * Cette méthode retourne une vue Blade qui charge le composant Livewire
     * UpdateVehicleMileage avec contrôles d'accès par rôle.
     *
     * Permissions et accès:
     * - Chauffeur: uniquement son véhicule assigné
     * - Superviseur/Chef de parc: véhicules de son dépôt
     * - Admin/Gestionnaire: tous les véhicules de l'organisation
     *
     * @param int|null $vehicle ID du véhicule (optionnel, pour URL directe)
     * @return \Illuminate\View\View
     */
    public function update(?int $vehicle = null)
    {
        return view('admin.mileage-readings.update', [
            'vehicleId' => $vehicle
        ]);
    }
}
