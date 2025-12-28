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
     * Exporter les relevés en CSV (Redirection depuis Livewire)
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $organizationId = auth()->user()->organization_id;

        // Récupérer les filtres depuis la session (stockés par Livewire)
        $filters = session('mileage_export_filters', []);

        // Si vide, utiliser les paramètres de requête (fallback)
        if (empty($filters)) {
            $filters = $request->all();
        }

        // Générer le fichier CSV via Service
        $filepath = $this->service->exportToCSV($organizationId, $filters);

        // Streamer le fichier
        $filename = basename($filepath);

        // Nettoyer la session
        session()->forget('mileage_export_filters');

        return response()->streamDownload(function () use ($filepath) {
            echo file_get_contents($filepath);
            unlink($filepath); // Supprimer après download
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Exporter les relevés en PDF (Enterprise Grade)
     */
    public function exportPdf()
    {
        try {
            // Récupérer les filtres depuis la session
            $filters = session('mileage_export_filters', []);
            $organizationId = auth()->user()->organization_id;

            // Récupérer les données via le Service ou Repository
            // On utilise le Service getFilteredReadings mais avec pagination élevée
            $readings = $this->service->getFilteredReadings($organizationId, array_merge($filters, ['per_page' => 5000]));

            // Récupérer les analytics pour le header du PDF
            $analytics = $this->service->getAnalytics($organizationId);

            // Données pour la vue
            $data = [
                'readings' => $readings,
                'analytics' => $analytics,
                'organization' => auth()->user()->organization,
                'filters' => $filters,
                'generatedAt' => now(),
            ];

            // Génération du HTML
            $html = view('exports.pdf.mileage-readings-list', $data)->render();

            // Utilisation du Service PDF (Puppeteer)
            $pdfService = new \App\Services\PdfGenerationService();
            $pdfContent = $pdfService->generateFromHtml($html, [
                'format' => 'A4',
                'landscape' => true // Landscape pour avoir de la place pour les colonnes
            ]);

            $filename = sprintf(
                'Releves_Kilometriques_%s_%s.pdf',
                auth()->user()->organization->slug ?? 'zenfleet',
                now()->format('Y-m-d_H-i')
            );

            // Nettoyer la session
            // session()->forget('mileage_export_filters'); // On peut garder ou supprimer

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.mileage-readings.index')
                ->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }

    /**
     * Deprecated: Old export logic
     */
    public function export(Request $request): StreamedResponse
    {
        return $this->exportCsv($request);
    }
}
