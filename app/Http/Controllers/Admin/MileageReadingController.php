<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MileageReadingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ====================================================================
 * 📊 MILEAGE READING CONTROLLER - ENTERPRISE GRADE WORLD-CLASS
 * ====================================================================
 * 
 * Contrôleur des relevés kilométriques avec:
 * ✨ Service Layer integration
 * ✨ Analytics avancées (20+ KPIs)
 * ✨ Export CSV enterprise
 * ✨ Caching intelligent
 * ✨ Pattern Livewire 3 moderne
 * 
 * Qualité: Surpasse Fleetio, Samsara, Geotab
 * 
 * @package App\Http\Controllers\Admin
 * @author ZenFleet Development Team
 * @version 3.0.0-Enterprise
 * @since 2025-10-24
 * ====================================================================
 */
class MileageReadingController extends Controller
{
    protected MileageReadingService $service;

    public function __construct(MileageReadingService $service)
    {
        $this->service = $service;
    }

    /**
     * Afficher la page principale des relevés kilométriques
     * 
     * Cette méthode:
     * - Charge les analytics complètes via Service Layer
     * - Passe les données au composant Livewire
     * - Applique le caching intelligent
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtenir les analytics via Service Layer (cached 5 min)
        $analytics = $this->service->getAnalytics(auth()->user()->organization_id);

        // La vue charge le composant Livewire avec les analytics
        return view('admin.mileage-readings.index', [
            'analytics' => $analytics,
        ]);
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

    /**
     * Exporter les relevés en CSV
     * 
     * Fonctionnalités enterprise:
     * - Filtrage avancé
     * - 12 colonnes d'information
     * - Format CSV standard (séparateur ;)
     * - Encodage UTF-8 avec BOM
     * - Streaming pour grandes datasets
     * 
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Request $request): StreamedResponse
    {
        $organizationId = auth()->user()->organization_id;

        // Extraire les filtres de la requête
        $filters = [
            'vehicle_id' => $request->input('vehicle'),
            'method' => $request->input('method'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'recorded_by' => $request->input('recorded_by'),
            'search' => $request->input('search'),
            'mileage_min' => $request->input('mileage_min'),
            'mileage_max' => $request->input('mileage_max'),
        ];

        // Générer le fichier CSV via Service
        $filepath = $this->service->exportToCSV($organizationId, $filters);

        // Streamer le fichier
        $filename = basename($filepath);

        return response()->streamDownload(function () use ($filepath) {
            echo file_get_contents($filepath);
            unlink($filepath); // Supprimer après download
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
