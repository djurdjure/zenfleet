<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

/**
 * 📑 Enterprise Vehicle PDF Export Service
 *
 * Service d'export PDF utilisant le microservice Node.js centralisé
 * Architecture microservices avec communication HTTP via PdfGenerationService
 *
 * @package App\Services
 * @version 2.0
 * @since 2025-11-03
 * @updated 2025-11-06 - Migration vers PdfGenerationService centralisé
 */
class VehiclePdfExportService
{
    protected PdfGenerationService $pdfService;
    protected $filters;
    protected $organization_id;

    /**
     * Constructeur avec injection de dépendance
     */
    public function __construct($filters = [])
    {
        $this->pdfService = app(PdfGenerationService::class);
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
                    'currentAssignment.driver.user', // Charger le chauffeur actif explicitement
                    'assignments' => function ($q) {
                        $q->orderBy('start_datetime', 'desc')
                            ->with('driver.user') // Charger user pour historique aussi
                            ->limit(10); // Augmenter la limite pour historique
                    },
                    'maintenanceOperations' => function ($q) {
                        $q->orderBy('scheduled_date', 'desc')
                            ->with('provider')
                            ->limit(5);
                    },
                    'expenses' => function ($q) {
                        $q->orderBy('expense_date', 'desc')
                            ->limit(5);
                    }
                ])
                ->findOrFail($vehicleId);

            // Calculate analytics for the PDF
            $analytics = $this->calculateVehicleAnalytics($vehicle);

            $html = $this->generateSingleVehicleHtml($vehicle, $analytics);

            return $this->generatePdf($html, "fiche_vehicule_{$vehicle->registration_plate}.pdf");
        } catch (\Exception $e) {
            Log::error('Export PDF véhicule unique échoué', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Calculate comprehensive analytics for a vehicle
     */
    protected function calculateVehicleAnalytics(Vehicle $vehicle): array
    {
        $maintenanceCostTotal = $vehicle->maintenanceOperations->sum('total_cost') ?? 0;
        $expenseTotal = $vehicle->expenses->sum('amount') ?? 0;
        $assignmentsCount = $vehicle->assignments->count();

        // Utiliser la relation currentAssignment si chargée, sinon méhode fallback sur la collection assignments
        $activeAssignment = $vehicle->relationLoaded('currentAssignment') && $vehicle->currentAssignment
            ? $vehicle->currentAssignment
            : $vehicle->assignments->first(function ($a) {
                // Logique matching VehicleController : statut active + (pas de fin ou fin future)
                return $a->status === 'active' && (!$a->end_datetime || $a->end_datetime->isFuture());
            });

        $now = now();
        $daysInService = $vehicle->acquisition_date
            ? abs($vehicle->acquisition_date->diffInDays($now))
            : 0;

        $ageYears = $vehicle->acquisition_date
            ? abs($now->diffInYears($vehicle->acquisition_date))
            : 0;

        // Formatage de l'âge (Année, mois, jour)
        $vehicleAgeFormatted = "N/A";
        if ($vehicle->acquisition_date) {
            $diff = $vehicle->acquisition_date->diff($now);
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $parts[] = $diff->m . ' mois';
            // Pas de jours pour l'âge, seulement année/mois
            $vehicleAgeFormatted = !empty($parts) ? implode(' ', $parts) : "Moins d'un mois";
        }

        $totalKmDriven = ($vehicle->current_mileage ?? 0) - ($vehicle->initial_mileage ?? 0);
        $costPerKm = $totalKmDriven > 0 ? ($maintenanceCostTotal + $expenseTotal) / $totalKmDriven : 0;

        return [
            'maintenance_cost_total' => $maintenanceCostTotal,
            'expense_total' => $expenseTotal,
            'assignments_count' => $assignmentsCount,
            'active_assignment' => $activeAssignment,
            'days_in_service' => $daysInService,
            'age_years' => $ageYears, // Conserver pour compatibilité
            'vehicle_age_formatted' => $vehicleAgeFormatted,
            'total_km_driven' => $totalKmDriven,
            'cost_per_km' => $costPerKm,
            'maintenance_count' => $vehicle->maintenanceOperations->count(),
            'last_maintenance_date' => $vehicle->maintenanceOperations->first()?->scheduled_date?->format('d/m/Y'),
        ];
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
     * Récupérer les véhicules avec filtres - Enterprise-Grade
     *
     * Priorités de filtrage:
     * 1. Si des véhicules sont spécifiquement sélectionnés (param 'vehicles'), utiliser UNIQUEMENT ces IDs
     * 2. Sinon, appliquer TOUS les filtres disponibles (comme dans VehicleController::buildAdvancedQuery)
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
                'assignments' => function ($q) {
                    $q->where('status', 'active')
                        ->where('start_datetime', '<=', now())
                        ->where(function ($query) {
                            $query->whereNull('end_datetime')
                                ->orWhere('end_datetime', '>=', now());
                        })
                        ->with('driver.user')
                        ->limit(1);
                }
            ]);

        // 🎯 PRIORITÉ 1: Si des véhicules spécifiques sont sélectionnés
        if (isset($this->filters['vehicles']) && !empty($this->filters['vehicles'])) {
            $vehicleIds = $this->parseVehicleIds($this->filters['vehicles']);

            if (!empty($vehicleIds)) {
                Log::info('Export PDF: Véhicules sélectionnés', ['count' => count($vehicleIds), 'ids' => $vehicleIds]);
                return $query->whereIn('id', $vehicleIds)->get();
            }
        }

        // 🔍 PRIORITÉ 2: Appliquer tous les filtres (mirror de VehicleController)

        // Filtre archivage
        if (isset($this->filters['archived'])) {
            if ($this->filters['archived'] === 'true') {
                $query->where('is_archived', true);
            } elseif ($this->filters['archived'] === 'all') {
                // Afficher tous les véhicules
            } else {
                $query->where('is_archived', false);
            }
        } else {
            $query->where('is_archived', false);
        }

        // Filtre recherche
        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('registration_plate', 'ilike', "%{$search}%")
                    ->orWhere('vin', 'ilike', "%{$search}%")
                    ->orWhere('brand', 'ilike', "%{$search}%")
                    ->orWhere('model', 'ilike', "%{$search}%");
            });
        }

        // Filtre statut
        if (isset($this->filters['status_id']) && !empty($this->filters['status_id'])) {
            $query->where('status_id', $this->filters['status_id']);
        }

        // Filtre type de véhicule
        if (isset($this->filters['vehicle_type_id']) && !empty($this->filters['vehicle_type_id'])) {
            $query->where('vehicle_type_id', $this->filters['vehicle_type_id']);
        }

        // Filtre type de carburant
        if (isset($this->filters['fuel_type_id']) && !empty($this->filters['fuel_type_id'])) {
            $query->where('fuel_type_id', $this->filters['fuel_type_id']);
        }

        // Filtre dépôt
        if (isset($this->filters['depot_id']) && !empty($this->filters['depot_id'])) {
            $query->where('depot_id', $this->filters['depot_id']);
        }

        // Filtres par date d'acquisition
        if (isset($this->filters['acquisition_from']) && !empty($this->filters['acquisition_from'])) {
            $query->where('acquisition_date', '>=', $this->filters['acquisition_from']);
        }

        if (isset($this->filters['acquisition_to']) && !empty($this->filters['acquisition_to'])) {
            $query->where('acquisition_date', '<=', $this->filters['acquisition_to']);
        }

        // Tri intelligent
        $sortBy = $this->filters['sort_by'] ?? 'created_at';
        $sortDirection = $this->filters['sort_direction'] ?? 'desc';

        $allowedSorts = [
            'registration_plate',
            'brand',
            'model',
            'manufacturing_year',
            'acquisition_date',
            'current_mileage',
            'created_at'
        ];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $vehicles = $query->limit(100)->get(); // Limiter pour éviter timeout

        Log::info('Export PDF: Véhicules filtrés', [
            'count' => $vehicles->count(),
            'filters_applied' => array_keys(array_filter($this->filters))
        ]);

        return $vehicles;
    }

    /**
     * Parser les IDs de véhicules depuis différents formats
     * Gère: "1,2,3" | "[1,2,3]" | [1,2,3]
     */
    private function parseVehicleIds($vehicles)
    {
        // Si c'est déjà un tableau
        if (is_array($vehicles)) {
            return array_filter(array_map('intval', $vehicles));
        }

        // Si c'est une chaîne JSON
        if (is_string($vehicles) && (str_starts_with($vehicles, '[') || str_starts_with($vehicles, '{'))) {
            $decoded = json_decode($vehicles, true);
            if (is_array($decoded)) {
                return array_filter(array_map('intval', $decoded));
            }
        }

        // Si c'est une chaîne séparée par des virgules
        if (is_string($vehicles)) {
            $ids = explode(',', $vehicles);
            return array_filter(array_map('intval', $ids));
        }

        return [];
    }

    /**
     * Générer HTML pour un véhicule unique
     * 
     * 🔧 FIX: Utilise directement le modèle Driver sans passer par User
     */
    protected function generateSingleVehicleHtml($vehicle, array $analytics = [])
    {
        $activeAssignment = $analytics['active_assignment'] ?? null;
        $driver = $activeAssignment ? $activeAssignment->driver : null;

        return view('exports.pdf.vehicle-single', [
            'vehicle' => $vehicle,
            'driver' => $driver,
            'organization' => Auth::user()->organization,
            'analytics' => $analytics,
            'generatedAt' => now(),
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
     * Appeler le microservice PDF centralisé pour générer le fichier
     *
     * Utilise PdfGenerationService qui gère:
     * - Health checks automatiques
     * - Retry logic avec exponential backoff
     * - Configuration centralisée
     * - Logging unifié
     */
    protected function generatePdf($html, $filename)
    {
        try {
            // Déléguer la génération au service centralisé enterprise-grade
            $pdfContent = $this->pdfService->generateFromHtml($html);

            // Retourner le PDF avec headers de sécurité enterprise
            return Response::make($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'X-PDF-Service' => 'Enterprise Microservice v2.0'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF véhicules', [
                'error' => $e->getMessage(),
                'filename' => $filename,
                'html_length' => strlen($html),
                'trace' => $e->getTraceAsString()
            ]);

            // Retourner une erreur HTTP claire avec détails pour debugging
            return Response::json([
                'error' => 'Le service de génération PDF est temporairement indisponible',
                'message' => 'Veuillez réessayer dans quelques instants ou contacter l\'administrateur',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 503);
        }
    }
}
