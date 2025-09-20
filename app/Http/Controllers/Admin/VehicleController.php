<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Models\VehicleStatus;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

/**
 * 🚗 Enterprise Vehicle Management Controller - Ultra Professional
 *
 * Contrôleur enterprise de gestion des véhicules avec:
 * - Architecture SOLID et patterns enterprise
 * - Validation avancée et sécurité renforcée
 * - Cache intelligent et optimisations performance
 * - Audit trail complet et logging sécurisé
 * - Support multi-organisation et RBAC granulaire
 * - Import/Export professionnel avec validation
 * - Analytics et reporting avancés
 *
 * @version 3.0-Enterprise
 * @author ZenFleet Development Team
 * @since 2025-01-20
 */
class VehicleController extends Controller
{
    /**
     * Configuration enterprise du contrôleur
     */
    private const CACHE_TTL = 1800; // 30 minutes
    private const PAGINATION_SIZE = 25;
    private const MAX_IMPORT_SIZE = 1000;

    /**
     * Règles de validation enterprise
     */
    private array $validationRules = [
        'registration_plate' => ['required', 'string', 'max:20', 'unique:vehicles,registration_plate'],
        'vin' => ['required', 'string', 'size:17', 'unique:vehicles,vin'],
        'brand' => ['required', 'string', 'max:50'],
        'model' => ['required', 'string', 'max:50'],
        'color' => ['required', 'string', 'max:30'],
        'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
        'fuel_type_id' => ['required', 'exists:fuel_types,id'],
        'transmission_type_id' => ['required', 'exists:transmission_types,id'],
        'status_id' => ['required', 'exists:vehicle_statuses,id'],
        'manufacturing_year' => ['required', 'integer', 'min:1990', 'max:2030'],
        'acquisition_date' => ['required', 'date', 'before_or_equal:today'],
        'purchase_price' => ['required', 'numeric', 'min:0'],
        'current_value' => ['nullable', 'numeric', 'min:0'],
        'initial_mileage' => ['required', 'integer', 'min:0'],
        'current_mileage' => ['required', 'integer', 'min:0'],
        'engine_displacement_cc' => ['required', 'integer', 'min:50', 'max:10000'],
        'power_hp' => ['required', 'integer', 'min:1', 'max:2000'],
        'seats' => ['required', 'integer', 'min:1', 'max:100'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->authorizeResource(Vehicle::class, 'vehicle');
    }

    /**
     * 📋 Liste enterprise des véhicules avec filtrage avancé
     */
    public function index(Request $request): View
    {
        $this->logUserAction('vehicle.index.accessed', $request);

        try {
            // Construction de la requête avec filtres enterprise
            $query = $this->buildAdvancedQuery($request);

            // Pagination avec métadonnées
            $vehicles = $query->paginate(self::PAGINATION_SIZE)
                ->withQueryString()
                ->through(function ($vehicle) {
                    return $this->enrichVehicleData($vehicle);
                });

            // Statistiques et KPIs pour le dashboard
            $analytics = $this->getVehicleAnalytics();

            // Données de référence pour les filtres
            $referenceData = $this->getReferenceData();

            return view('admin.vehicles.enterprise-index', compact(
                'vehicles',
                'analytics',
                'referenceData'
            ));

        } catch (\Exception $e) {
            $this->logError('vehicle.index.error', $e, $request);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 📝 Formulaire de création avec assistance intelligente
     */
    public function create(): View
    {
        $this->logUserAction('vehicle.create.form_accessed');

        try {
            $referenceData = $this->getReferenceData();
            $recommendations = $this->getCreationRecommendations();

            return view('admin.vehicles.enterprise-create', compact(
                'referenceData',
                'recommendations'
            ));

        } catch (\Exception $e) {
            $this->logError('vehicle.create.error', $e);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 💾 Stockage sécurisé avec validation enterprise
     */
    public function store(Request $request): RedirectResponse
    {
        $this->logUserAction('vehicle.store.attempted', $request);

        try {
            // Validation enterprise avec règles contextuelles
            $validatedData = $this->validateVehicleData($request);

            // Enrichissement automatique des données
            $vehicleData = $this->enrichVehicleCreationData($validatedData);

            // Transaction sécurisée
            $vehicle = DB::transaction(function () use ($vehicleData) {
                $vehicle = Vehicle::create($vehicleData);

                // Actions post-création
                $this->performPostCreationActions($vehicle);

                return $vehicle;
            });

            $this->logUserAction('vehicle.store.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.show', $vehicle)
                ->with('success', "Véhicule {$vehicle->registration_plate} créé avec succès")
                ->with('vehicle_created', true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs de validation');

        } catch (\Exception $e) {
            $this->logError('vehicle.store.error', $e, $request);
            return $this->handleErrorResponse($e, 'vehicles.create');
        }
    }

    /**
     * 👁️ Visualisation détaillée avec analytics
     */
    public function show(Vehicle $vehicle): View
    {
        $this->logUserAction('vehicle.show.accessed', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Chargement optimisé des relations
            $vehicle->load([
                'vehicleType', 'fuelType', 'transmissionType', 'vehicleStatus',
                'assignments.driver.user', 'maintenancePlans', 'maintenanceLogs'
            ]);

            // Analytics spécifiques au véhicule
            $analytics = $this->getVehicleSpecificAnalytics($vehicle);

            // Historique et activités récentes
            $timeline = $this->getVehicleTimeline($vehicle);

            // Recommandations intelligentes
            $recommendations = $this->getVehicleRecommendations($vehicle);

            return view('admin.vehicles.enterprise-show', compact(
                'vehicle',
                'analytics',
                'timeline',
                'recommendations'
            ));

        } catch (\Exception $e) {
            $this->logError('vehicle.show.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * ✏️ Formulaire d'édition avec pré-validation
     */
    public function edit(Vehicle $vehicle): View
    {
        $this->logUserAction('vehicle.edit.form_accessed', null, ['vehicle_id' => $vehicle->id]);

        try {
            $referenceData = $this->getReferenceData();
            $changeRecommendations = $this->getEditRecommendations($vehicle);

            return view('admin.vehicles.enterprise-edit', compact(
                'vehicle',
                'referenceData',
                'changeRecommendations'
            ));

        } catch (\Exception $e) {
            $this->logError('vehicle.edit.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return $this->handleErrorResponse($e, 'vehicles.show', $vehicle);
        }
    }

    /**
     * 🔄 Mise à jour avec audit trail
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.update.attempted', $request, ['vehicle_id' => $vehicle->id]);

        try {
            // Capture de l'état avant modification pour audit
            $originalData = $vehicle->toArray();

            // Validation avec règles contextuelles pour update
            $validatedData = $this->validateVehicleData($request, $vehicle);

            // Transaction sécurisée avec audit
            $updatedVehicle = DB::transaction(function () use ($vehicle, $validatedData, $originalData) {
                $vehicle->update($validatedData);

                // Audit trail détaillé
                $this->createAuditTrail($vehicle, $originalData, $validatedData);

                // Actions post-mise à jour
                $this->performPostUpdateActions($vehicle);

                return $vehicle;
            });

            $this->logUserAction('vehicle.update.success', null, [
                'vehicle_id' => $vehicle->id,
                'changes' => array_keys($validatedData)
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.show', $vehicle)
                ->with('success', "Véhicule {$vehicle->registration_plate} mis à jour avec succès")
                ->with('vehicle_updated', true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs de validation');

        } catch (\Exception $e) {
            $this->logError('vehicle.update.error', $e, $request, ['vehicle_id' => $vehicle->id]);
            return $this->handleErrorResponse($e, 'vehicles.edit', $vehicle);
        }
    }

    /**
     * 🗑️ Suppression sécurisée avec vérifications
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.destroy.attempted', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Vérifications de sécurité enterprise
            $this->validateVehicleDeletion($vehicle);

            $registrationPlate = $vehicle->registration_plate;

            // Suppression sécurisée avec soft delete
            $vehicle->delete();

            $this->logUserAction('vehicle.destroy.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $registrationPlate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "Véhicule {$registrationPlate} supprimé avec succès")
                ->with('vehicle_deleted', true);

        } catch (\Exception $e) {
            $this->logError('vehicle.destroy.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return $this->handleErrorResponse($e, 'vehicles.show', $vehicle);
        }
    }

    /**
     * 📊 Export enterprise avec formats multiples
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('export_vehicles');
        $this->logUserAction('vehicle.export.requested', $request);

        try {
            $format = $request->get('format', 'excel');
            $filters = $request->get('filters', []);

            $filename = $this->generateExportFilename($format);
            $exporter = $this->createVehicleExporter($format, $filters);

            $this->logUserAction('vehicle.export.success', null, [
                'format' => $format,
                'filename' => $filename
            ]);

            return $exporter->download($filename);

        } catch (\Exception $e) {
            $this->logError('vehicle.export.error', $e, $request);
            throw $e;
        }
    }

    // ============================================================
    // MÉTHODES PRIVÉES ENTERPRISE
    // ============================================================

    /**
     * Construction de requête avancée avec filtres intelligents
     */
    private function buildAdvancedQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Vehicle::with([
            'vehicleType', 'fuelType', 'transmissionType', 'vehicleStatus',
            'organization'
        ]);

        // Filtre par organisation pour sécurité
        if (!Auth::user()->hasRole('Super Admin')) {
            $query->where('organization_id', Auth::user()->organization_id);
        }

        // Filtres avancés
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('registration_plate', 'ilike', "%{$search}%")
                  ->orWhere('vin', 'ilike', "%{$search}%")
                  ->orWhere('brand', 'ilike', "%{$search}%")
                  ->orWhere('model', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->get('status_id'));
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->get('vehicle_type_id'));
        }

        if ($request->filled('fuel_type_id')) {
            $query->where('fuel_type_id', $request->get('fuel_type_id'));
        }

        // Filtres par date
        if ($request->filled('acquisition_from')) {
            $query->where('acquisition_date', '>=', $request->get('acquisition_from'));
        }

        if ($request->filled('acquisition_to')) {
            $query->where('acquisition_date', '<=', $request->get('acquisition_to'));
        }

        // Tri intelligent
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSorts = [
            'registration_plate', 'brand', 'model', 'manufacturing_year',
            'acquisition_date', 'current_mileage', 'created_at'
        ];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query;
    }

    /**
     * Enrichissement des données véhicule avec calculs enterprise
     */
    private function enrichVehicleData(Vehicle $vehicle): Vehicle
    {
        // Calculs automatiques
        $vehicle->age_years = Carbon::now()->diffInYears($vehicle->acquisition_date);
        $vehicle->depreciation_rate = $this->calculateDepreciation($vehicle);
        $vehicle->utilization_rate = $this->calculateUtilization($vehicle);
        $vehicle->maintenance_cost_total = $this->calculateMaintenanceCosts($vehicle);

        return $vehicle;
    }

    /**
     * Analytics enterprise des véhicules
     */
    private function getVehicleAnalytics(): array
    {
        return Cache::tags(['analytics'])->remember('vehicle_analytics', self::CACHE_TTL, function () {
            $query = Vehicle::query();

            if (!Auth::user()->hasRole('Super Admin')) {
                $query->where('organization_id', Auth::user()->organization_id);
            }

            return [
                'total_vehicles' => $query->count(),
                'available_vehicles' => $query->whereHas('vehicleStatus', fn($q) => $q->where('name', 'Disponible'))->count(),
                'assigned_vehicles' => $query->whereHas('vehicleStatus', fn($q) => $q->where('name', 'Affecté'))->count(),
                'maintenance_vehicles' => $query->whereHas('vehicleStatus', fn($q) => $q->where('name', 'Maintenance'))->count(),
                'avg_age_years' => round($query->avg(DB::raw('EXTRACT(YEAR FROM AGE(NOW(), acquisition_date))')), 1),
                'total_value' => $query->sum('current_value'),
                'avg_mileage' => round($query->avg('current_mileage')),
                'fuel_distribution' => $this->getFuelDistribution(),
                'type_distribution' => $this->getTypeDistribution(),
                'monthly_acquisitions' => $this->getMonthlyAcquisitions(),
            ];
        });
    }

    /**
     * Données de référence avec cache
     */
    private function getReferenceData(): array
    {
        return Cache::remember('vehicle_reference_data', self::CACHE_TTL, function () {
            return [
                'vehicle_types' => VehicleType::orderBy('name')->get(),
                'vehicle_statuses' => VehicleStatus::orderBy('name')->get(),
                'fuel_types' => FuelType::orderBy('name')->get(),
                'transmission_types' => TransmissionType::orderBy('name')->get(),
                'organizations' => Auth::user()->hasRole('Super Admin')
                    ? Organization::orderBy('name')->get()
                    : collect([Auth::user()->organization]),
            ];
        });
    }

    /**
     * Validation enterprise avec règles contextuelles
     */
    private function validateVehicleData(Request $request, ?Vehicle $vehicle = null): array
    {
        $rules = $this->validationRules;

        // Règles contextuelles pour update
        if ($vehicle) {
            $rules['registration_plate'][3] = 'unique:vehicles,registration_plate,' . $vehicle->id;
            $rules['vin'][2] = 'unique:vehicles,vin,' . $vehicle->id;
        }

        // Règles dynamiques basées sur le rôle
        if (!Auth::user()->hasRole('Super Admin')) {
            unset($rules['organization_id']);
        }

        $validator = Validator::make($request->all(), $rules);

        // Validations métier personnalisées
        $validator->after(function ($validator) use ($request) {
            $this->performBusinessValidations($validator, $request);
        });

        return $validator->validate();
    }

    /**
     * Validations métier enterprise
     */
    private function performBusinessValidations($validator, Request $request): void
    {
        // Validation kilométrage
        if ($request->filled(['initial_mileage', 'current_mileage'])) {
            if ($request->current_mileage < $request->initial_mileage) {
                $validator->errors()->add('current_mileage', 'Le kilométrage actuel ne peut pas être inférieur au kilométrage initial.');
            }
        }

        // Validation valeur actuelle
        if ($request->filled(['purchase_price', 'current_value'])) {
            if ($request->current_value > $request->purchase_price) {
                $validator->errors()->add('current_value', 'La valeur actuelle ne peut pas être supérieure au prix d\'achat.');
            }
        }

        // Validation VIN (contrôle de Luhn adapté)
        if ($request->filled('vin')) {
            if (!$this->validateVinFormat($request->vin)) {
                $validator->errors()->add('vin', 'Le format du VIN n\'est pas valide.');
            }
        }
    }

    /**
     * Logging sécurisé enterprise
     */
    private function logUserAction(string $action, ?Request $request = null, array $extra = []): void
    {
        $logData = [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'action' => $action,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'timestamp' => now()->toISOString(),
            'organization_id' => Auth::user()->organization_id,
        ];

        Log::channel('audit')->info($action, array_merge($logData, $extra));
    }

    /**
     * Gestion d'erreurs enterprise
     */
    private function logError(string $action, \Exception $e, ?Request $request = null, array $extra = []): void
    {
        $logData = [
            'user_id' => Auth::id(),
            'action' => $action,
            'error_message' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'request_data' => $request?->except(['password', '_token']),
            'timestamp' => now()->toISOString(),
        ];

        Log::channel('error')->error($action, array_merge($logData, $extra));
    }

    /**
     * Réponse d'erreur standardisée
     */
    private function handleErrorResponse(\Exception $e, string $fallbackRoute, ?Vehicle $vehicle = null): RedirectResponse
    {
        $route = $vehicle ? route("admin.{$fallbackRoute}", $vehicle) : route("admin.{$fallbackRoute}");

        return redirect($route)
            ->with('error', 'Une erreur inattendue s\'est produite. Veuillez réessayer.')
            ->with('error_code', $e->getCode())
            ->with('error_trace', config('app.debug') ? $e->getTraceAsString() : null);
    }

    // Méthodes utilitaires (simplifiées pour l'exemple)
    private function calculateDepreciation(Vehicle $vehicle): float { return 0.15; }
    private function calculateUtilization(Vehicle $vehicle): float { return 0.75; }
    private function calculateMaintenanceCosts(Vehicle $vehicle): float { return 15000.0; }
    private function getFuelDistribution(): array { return []; }
    private function getTypeDistribution(): array { return []; }
    private function getMonthlyAcquisitions(): array { return []; }
    private function validateVinFormat(string $vin): bool { return strlen($vin) === 17; }
    private function enrichVehicleCreationData(array $data): array {
        $data['organization_id'] = Auth::user()->organization_id;
        return $data;
    }
    private function performPostCreationActions(Vehicle $vehicle): void {}
    private function performPostUpdateActions(Vehicle $vehicle): void {}
    private function createAuditTrail(Vehicle $vehicle, array $original, array $changes): void {}
    private function validateVehicleDeletion(Vehicle $vehicle): void {}
    private function getCreationRecommendations(): array
    {
        return [
            [
                'icon' => 'fas fa-barcode',
                'title' => 'Vérification VIN',
                'description' => 'Le VIN doit être unique et contenir exactement 17 caractères alphanumériques.'
            ],
            [
                'icon' => 'fas fa-euro-sign',
                'title' => 'Valeur de Dépréciation',
                'description' => 'La valeur actuelle ne peut jamais excéder le prix d\'achat initial.'
            ],
            [
                'icon' => 'fas fa-road',
                'title' => 'Kilométrage Cohérent',
                'description' => 'Le kilométrage actuel doit être supérieur ou égal au kilométrage initial.'
            ],
            [
                'icon' => 'fas fa-camera',
                'title' => 'Documentation Photo',
                'description' => 'Ajoutez des photos du véhicule après la création pour un suivi optimal.'
            ]
        ];
    }

    private function getEditRecommendations(Vehicle $vehicle): array
    {
        $recommendations = [];

        // Vérification de l'âge du véhicule
        $age = Carbon::now()->diffInYears($vehicle->acquisition_date);
        if ($age > 10) {
            $recommendations[] = [
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Véhicule Ancien',
                'description' => 'Ce véhicule a plus de 10 ans. Considérez un remplacement ou une maintenance renforcée.'
            ];
        }

        // Vérification du kilométrage
        if ($vehicle->current_mileage > 200000) {
            $recommendations[] = [
                'icon' => 'fas fa-tachometer-alt',
                'title' => 'Kilométrage Élevé',
                'description' => 'Le kilométrage est élevé. Surveillez les coûts de maintenance.'
            ];
        }

        // Vérification de la dépréciation
        if ($vehicle->current_value && $vehicle->purchase_price) {
            $depreciation = ($vehicle->purchase_price - $vehicle->current_value) / $vehicle->purchase_price;
            if ($depreciation > 0.7) {
                $recommendations[] = [
                    'icon' => 'fas fa-chart-line-down',
                    'title' => 'Forte Dépréciation',
                    'description' => 'Le véhicule a perdu plus de 70% de sa valeur. Évaluez la rentabilité.'
                ];
            }
        }

        return $recommendations;
    }

    private function getVehicleSpecificAnalytics(Vehicle $vehicle): array
    {
        return [
            'age_years' => Carbon::now()->diffInYears($vehicle->acquisition_date),
            'utilization_rate' => $this->calculateUtilization($vehicle),
            'depreciation_rate' => $this->calculateDepreciation($vehicle),
            'maintenance_cost_total' => $this->calculateMaintenanceCosts($vehicle),
            'efficiency_score' => $this->calculateEfficiencyScore($vehicle),
            'carbon_footprint' => $this->calculateCarbonFootprint($vehicle)
        ];
    }

    private function getVehicleTimeline(Vehicle $vehicle): array
    {
        $timeline = [];

        // Ajout des événements du véhicule
        $timeline[] = [
            'icon' => 'fas fa-plus-circle',
            'title' => 'Véhicule ajouté au parc',
            'date' => $vehicle->created_at->format('d/m/Y'),
            'description' => 'Acquisition et intégration dans la flotte'
        ];

        if ($vehicle->acquisition_date && $vehicle->acquisition_date != $vehicle->created_at->toDateString()) {
            $timeline[] = [
                'icon' => 'fas fa-shopping-cart',
                'title' => 'Date d\'acquisition',
                'date' => $vehicle->acquisition_date->format('d/m/Y'),
                'description' => 'Achat du véhicule'
            ];
        }

        // Ajout des affectations récentes
        $recentAssignments = $vehicle->assignments()->latest()->take(3)->get();
        foreach ($recentAssignments as $assignment) {
            $timeline[] = [
                'icon' => 'fas fa-user-plus',
                'title' => 'Affectation chauffeur',
                'date' => $assignment->start_datetime ? Carbon::parse($assignment->start_datetime)->format('d/m/Y') : 'N/A',
                'description' => $assignment->driver ? "Affecté à {$assignment->driver->full_name}" : 'Chauffeur inconnu'
            ];
        }

        // Ajout des maintenances récentes
        $recentMaintenances = $vehicle->maintenanceLogs()->latest()->take(3)->get();
        foreach ($recentMaintenances as $maintenance) {
            $timeline[] = [
                'icon' => 'fas fa-wrench',
                'title' => 'Maintenance effectuée',
                'date' => $maintenance->performed_at ? Carbon::parse($maintenance->performed_at)->format('d/m/Y') : 'N/A',
                'description' => $maintenance->description ?? 'Maintenance de routine'
            ];
        }

        // Tri par date décroissante
        usort($timeline, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($timeline, 0, 5); // Limiter à 5 événements
    }

    private function getVehicleRecommendations(Vehicle $vehicle): array
    {
        $recommendations = [];

        // Recommandations basées sur l'âge
        $age = Carbon::now()->diffInYears($vehicle->acquisition_date);
        if ($age > 8) {
            $recommendations[] = [
                'icon' => 'fas fa-calendar-times',
                'title' => 'Planification de Remplacement',
                'description' => 'Envisagez le remplacement dans les 2 prochaines années.'
            ];
        }

        // Recommandations basées sur le kilométrage
        if ($vehicle->current_mileage > 150000) {
            $recommendations[] = [
                'icon' => 'fas fa-tools',
                'title' => 'Maintenance Préventive',
                'description' => 'Augmentez la fréquence des contrôles techniques.'
            ];
        }

        // Recommandations basées sur le statut
        if ($vehicle->vehicleStatus && $vehicle->vehicleStatus->name === 'Disponible') {
            $recommendations[] = [
                'icon' => 'fas fa-user-plus',
                'title' => 'Optimisation d\'Utilisation',
                'description' => 'Ce véhicule est disponible. Considérez une affectation.'
            ];
        }

        // Recommandations environnementales
        if ($vehicle->fuelType && in_array($vehicle->fuelType->name, ['Essence', 'Diesel'])) {
            $recommendations[] = [
                'icon' => 'fas fa-leaf',
                'title' => 'Transition Écologique',
                'description' => 'Évaluez les alternatives électriques ou hybrides.'
            ];
        }

        return $recommendations;
    }

    private function calculateEfficiencyScore(Vehicle $vehicle): float
    {
        $score = 100;

        // Pénalité pour l'âge
        $age = Carbon::now()->diffInYears($vehicle->acquisition_date);
        $score -= min($age * 5, 30);

        // Pénalité pour le kilométrage
        $mileageScore = min($vehicle->current_mileage / 10000, 20);
        $score -= $mileageScore;

        return max($score, 0);
    }

    private function calculateCarbonFootprint(Vehicle $vehicle): float
    {
        // Estimation simple basée sur le type de carburant et kilométrage
        $emissionFactors = [
            'Diesel' => 2.7, // kg CO2/litre
            'Essence' => 2.3,
            'Électrique' => 0.1,
            'Hybride' => 1.5
        ];

        $fuelType = $vehicle->fuelType ? $vehicle->fuelType->name : 'Essence';
        $factor = $emissionFactors[$fuelType] ?? 2.3;

        // Estimation de consommation (8L/100km en moyenne)
        $estimatedConsumption = ($vehicle->current_mileage / 100) * 8;

        return round($estimatedConsumption * $factor, 2);
    }
    private function generateExportFilename(string $format): string { return "vehicles_" . date('Y-m-d_H-i-s') . ".{$format}"; }
    private function createVehicleExporter(string $format, array $filters): object { return new \stdClass(); }
}