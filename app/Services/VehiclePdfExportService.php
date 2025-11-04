<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

/**
 * 📑 Enterprise Vehicle PDF Export Service
 * 
 * Service d'export PDF utilisant le microservice Node.js
 * Architecture microservices avec communication HTTP
 * 
 * @package App\Services
 * @version 1.0
 * @since 2025-11-03
 */
class VehiclePdfExportService
{
    protected $pdfServiceUrl;
    protected $filters;
    protected $organization_id;

    /**
     * Constructeur
     */
    public function __construct($filters = [])
    {
        $this->pdfServiceUrl = config('services.pdf.url', 'http://pdf-service:3000');
        $this->filters = $filters;
        $this->organization_id = Auth::user()->organization_id;
    }

    /**
     * Exporter un véhicule unique en PDF
     */
    public function exportSingle($vehicleId)
    {
        try {
            $vehicle = Vehicle::where('organization_id', $this->organization_id)
                ->with([
                    'vehicleType',
                    'vehicleStatus',
                    'fuelType',
                    'transmissionType',
                    'depot',
                    'category',
                    'assignments.driver.user'
                ])
                ->findOrFail($vehicleId);

            $html = $this->generateSingleVehicleHtml($vehicle);
            
            return $this->generatePdf($html, "vehicle_{$vehicle->registration_plate}.pdf");
        } catch (\Exception $e) {
            Log::error('Export PDF véhicule unique échoué', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Exporter la liste des véhicules en PDF
     */
    public function exportList()
    {
        try {
            $vehicles = $this->getVehicles();
            $html = $this->generateListHtml($vehicles);
            
            return $this->generatePdf($html, 'vehicles_list_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Export PDF liste véhicules échoué', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Récupérer les véhicules avec filtres
     */
    protected function getVehicles()
    {
        $query = Vehicle::query()
            ->where('organization_id', $this->organization_id)
            ->with([
                'vehicleType',
                'vehicleStatus',
                'fuelType',
                'transmissionType',
                'depot',
                'category',
                'assignments' => function($q) {
                    $q->where('is_active', true)
                      ->with('driver.user');
                }
            ]);

        // Appliquer les filtres
        if (isset($this->filters['archived'])) {
            if ($this->filters['archived'] === 'true') {
                $query->where('is_archived', true);
            } elseif ($this->filters['archived'] === 'false') {
                $query->where('is_archived', false);
            }
        } else {
            $query->where('is_archived', false);
        }

        if (isset($this->filters['status_id'])) {
            $query->where('status_id', $this->filters['status_id']);
        }

        if (isset($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('registration_plate', 'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('model', 'ilike', "%{$search}%");
            });
        }

        return $query->limit(100)->get(); // Limiter pour éviter timeout
    }

    /**
     * Générer HTML pour un véhicule unique
     */
    protected function generateSingleVehicleHtml($vehicle)
    {
        $activeAssignment = $vehicle->assignments->where('is_active', true)->first();
        $driver = $activeAssignment ? $activeAssignment->driver : null;
        $user = $driver ? $driver->user : null;

        return view('exports.pdf.vehicle-single', [
            'vehicle' => $vehicle,
            'driver' => $driver,
            'user' => $user,
            'organization' => Auth::user()->organization
        ])->render();
    }

    /**
     * Générer HTML pour la liste des véhicules
     */
    protected function generateListHtml($vehicles)
    {
        return view('exports.pdf.vehicles-list', [
            'vehicles' => $vehicles,
            'organization' => Auth::user()->organization,
            'filters' => $this->filters
        ])->render();
    }

    /**
     * Appeler le microservice PDF pour générer le fichier
     */
    protected function generatePdf($html, $filename)
    {
        try {
            // Configuration avancée pour PDF premium
            $pdfOptions = [
                'format' => 'A4',
                'printBackground' => true,
                'preferCSSPageSize' => false,
                'displayHeaderFooter' => true,
                'headerTemplate' => '<div></div>',
                'footerTemplate' => '<div style="width:100%; text-align:center; font-size:10px; color:#999;">
                    <span class="pageNumber"></span> / <span class="totalPages"></span>
                </div>',
                'margin' => [
                    'top' => '15mm',
                    'right' => '10mm',
                    'bottom' => '15mm',
                    'left' => '10mm'
                ],
                'scale' => 0.95,
                'landscape' => false,
                'preferredColorScheme' => 'light',
                'omitBackground' => false
            ];

            // Appel au microservice avec configuration enterprise
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/pdf'
                ])
                ->post($this->pdfServiceUrl . '/generate-pdf', [
                    'html' => $html,
                    'options' => $pdfOptions,
                    'waitUntil' => 'networkidle0',
                    'emulateMediaType' => 'print'
                ]);

            if ($response->successful()) {
                // Forcer le téléchargement du PDF
                return Response::make($response->body(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($response->body()),
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0',
                    'X-Content-Type-Options' => 'nosniff',
                    'X-Frame-Options' => 'DENY'
                ]);
            }

            Log::error('Microservice PDF erreur status', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new \Exception('Le service PDF a retourné une erreur: ' . $response->status());
            
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Ne pas faire de fallback HTML - plutôt retourner une erreur claire
            return Response::json([
                'error' => 'Le service de génération PDF est temporairement indisponible',
                'message' => 'Veuillez réessayer dans quelques instants ou contacter l\'administrateur'
            ], 503);
        }
    }
}
