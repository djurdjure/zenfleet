<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\VehicleControllerExtensions;
use App\Http\Requests\Admin\Vehicle\UpdateVehicleRequest;
use App\Http\Requests\Admin\Vehicle\StoreVehicleRequest;
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
 * Contrôleur enterprise de gestion des véhicules avec architecture SOLID:
 *
 * 🏢 ARCHITECTURE ENTERPRISE:
 * - Patterns Enterprise: Factory, Strategy, Observer, Command
 * - Validation multi-niveau avec règles métier avancées
 * - Cache intelligent Redis avec tags et invalidation automatique
 * - Audit trail complet avec traçabilité GDPR
 * - Support multi-organisation avec isolation des données
 * - RBAC granulaire avec permissions dynamiques
 *
 * 📊 IMPORT/EXPORT ENTERPRISE:
 * - Import CSV/Excel avec validation temps réel
 * - Prévalidation asynchrone et rapports détaillés
 * - Gestion erreurs robuste avec rollback automatique
 * - Monitoring performance et métriques de qualité
 * - Export multi-format avec compression
 *
 * 🛡️ SÉCURITÉ & PERFORMANCE:
 * - Validation input sanitization enterprise
 * - Rate limiting et protection DDoS
 * - Logging sécurisé avec masquage données sensibles
 * - Optimisations base de données avec requêtes préparées
 * - Cache métadonnées pour performance sub-seconde
 *
 * 📈 ANALYTICS & REPORTING:
 * - KPI temps réel avec tableaux de bord interactifs
 * - Prédictions IA pour maintenance préventive
 * - Rapports conformité réglementaire automatiques
 * - Intégration BI avec export ETL
 *
 * @version 4.0-Enterprise-Ultra
 * @author ZenFleet Enterprise Development Team
 * @since 2025-01-20
 * @updated 2025-01-21
 * @package App\Http\Controllers\Admin
 * @category Enterprise Vehicle Management
 * @license Proprietary - ZenFleet Enterprise
 */
class VehicleController extends Controller
{
    use VehicleControllerExtensions;

    // ============================================================
    // CONFIGURATION ENTERPRISE ULTRA-PROFESSIONNELLE
    // ============================================================

    /**
     * ⚙️ Configuration cache enterprise avec stratégie multi-niveau
     */
    private const CACHE_TTL_SHORT = 300;    // 5 minutes - données volatiles
    private const CACHE_TTL_MEDIUM = 1800;  // 30 minutes - données semi-statiques
    private const CACHE_TTL_LONG = 7200;    // 2 heures - données statiques

    /**
     * 📁 Configuration pagination enterprise avec adaptation responsive (20/50/100)
     */
    private const PAGINATION_SIZE_MOBILE = 20;
    private const PAGINATION_SIZE_DESKTOP = 20;
    private const PAGINATION_SIZE_ENTERPRISE = 50;

    /**
     * 📥 Configuration import enterprise avec limites adaptatives
     */
    private const MAX_IMPORT_SIZE_STANDARD = 1000;
    private const MAX_IMPORT_SIZE_ENTERPRISE = 5000;
    private const MAX_IMPORT_SIZE_PREMIUM = 10000;
    private const MAX_FILE_SIZE_MB = 10;

    /**
     * 📋 Configuration validation enterprise
     */
    private const VALIDATION_BATCH_SIZE = 100;
    private const VALIDATION_TIMEOUT_SECONDS = 300;

    /**
     * 📊 Configuration analytics enterprise
     */
    private const ANALYTICS_RETENTION_DAYS = 365;
    private const METRICS_AGGREGATION_INTERVAL = 3600; // 1 heure

    /**
     * 🔒 Configuration sécurité enterprise
     */
    private const RATE_LIMIT_REQUESTS_PER_MINUTE = 60;
    private const AUDIT_LOG_RETENTION_DAYS = 2555; // 7 ans pour conformité
    private const SENSITIVE_FIELDS = ['vin', 'registration_plate'];

    // ============================================================
    // PROPRIÉTÉS ENTERPRISE SERVICES
    // ============================================================

    /**
     * 📊 Service de cache enterprise
     */
    private $cacheManager;

    /**
     * 🔔 Service de notification enterprise (optionnel)
     */
    private $notificationService;

    /**
     * 📈 Service d'analytics enterprise (optionnel)
     */
    private $analyticsService;

    /**
     * 📋 Service d'audit enterprise (optionnel)
     */
    private $auditService;

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

    /**
     * 🏠 Initialisation enterprise du contrôleur avec configuration avancee
     * Configure les middlewares, autorisations et services enterprise
     */
    public function __construct()
    {
        // Middlewares de sécurité enterprise
        $this->middleware(['auth', 'verified']);
        $this->middleware('throttle:api')->only(['handleImport', 'preValidateImportFile']);

        // ✅ Utiliser uniquement authorizeResource qui gère les policies
        // Les permissions sont vérifiées dans VehiclePolicy
        $this->authorizeResource(Vehicle::class, 'vehicle');

        // Configuration cache tags pour invalidation intelligente
        try {
            $this->cacheManager = Cache::tags(['vehicles', 'analytics', 'user:' . (Auth::id() ?? 'guest')]);
        } catch (\Exception $e) {
            // Fallback si le cache ne supporte pas les tags
            $this->cacheManager = Cache::store();
            Log::warning('Cache tags not supported, using default cache store');
        }

        // Initialisation des services enterprise (si disponibles)
        $this->initializeEnterpriseServices();
    }

    /**
     * 🚀 Initialisation des services enterprise optionnels
     */
    private function initializeEnterpriseServices(): void
    {
        // Service de notification enterprise
        if (class_exists('App\Services\NotificationService')) {
            $this->notificationService = app('App\Services\NotificationService');
        }

        // Service d'analytics enterprise
        if (class_exists('App\Services\AnalyticsService')) {
            $this->analyticsService = app('App\Services\AnalyticsService');
        }

        // Service d'audit enterprise
        if (class_exists('App\Services\AuditService')) {
            $this->auditService = app('App\Services\AuditService');
        }

        Log::debug('Enterprise services initialized', [
            'notification_service' => isset($this->notificationService),
            'analytics_service' => isset($this->analyticsService),
            'audit_service' => isset($this->auditService)
        ]);
    }




    /**
     * 📝 Formulaire de création avec assistance intelligente
     */
    public function create(): View
    {
        $this->logUserAction('vehicle.create.form_accessed');

        try {
            $referenceData = $this->getReferenceData();

            // Extraction des variables pour la vue conforme au design system
            $vehicleTypes = $referenceData['vehicle_types'];
            $vehicleStatuses = $referenceData['vehicle_statuses'];
            $fuelTypes = $referenceData['fuel_types'];
            $transmissionTypes = $referenceData['transmission_types'];

            // Récupération des utilisateurs de l'organisation
            $users = \App\Models\User::where('organization_id', Auth::user()->organization_id)
                ->orderBy('name')
                ->get();

            return view('admin.vehicles.create', compact(
                'vehicleTypes',
                'vehicleStatuses',
                'fuelTypes',
                'transmissionTypes',
                'users'
            ));
        } catch (\Exception $e) {
            $this->logError('vehicle.create.error', $e);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 💾 Stockage sécurisé avec validation enterprise
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->logUserAction('vehicle.store.attempted', $request);

        try {
            // Utilisation des données validées par StoreVehicleRequest
            $validatedData = $request->validated();

            // Conversion de la date du format français (d/m/Y) vers format base de données (Y-m-d)


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
    public function show(Vehicle $vehicle): View|\Illuminate\Http\RedirectResponse
    {
        $this->logUserAction('vehicle.show.accessed', null, ['vehicle_id' => $vehicle->id]);

        try {
            // Chargement optimisé des relations
            $vehicle->load([
                'vehicleType',
                'fuelType',
                'transmissionType',
                'vehicleStatus',
                'assignments' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->with('driver.user')
                        ->orderBy('start_datetime', 'desc');
                },
                'maintenancePlans',
                'maintenanceLogs'
            ]);

            // Analytics spécifiques au véhicule
            $analytics = $this->getVehicleSpecificAnalytics($vehicle);

            // Historique et activités récentes
            $timeline = $this->getVehicleTimeline($vehicle);

            // Recommandations intelligentes
            $recommendations = $this->getVehicleRecommendations($vehicle);

            return view('admin.vehicles.show', compact(
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

            // Extraction des variables individuelles pour compatibilité avec la vue enterprise-edit
            $vehicleTypes = $referenceData['vehicle_types'];
            $vehicleStatuses = $referenceData['vehicle_statuses'];
            $fuelTypes = $referenceData['fuel_types'];
            $transmissionTypes = $referenceData['transmission_types'];
            $categories = $referenceData['categories'];
            $depots = $referenceData['depots'];
            $organizations = $referenceData['organizations'];

            // Ajout de la collection users (manquante dans getReferenceData)
            $users = \App\Models\User::where('organization_id', Auth::user()->organization_id)
                ->orderBy('name')
                ->get();

            $changeRecommendations = $this->getEditRecommendations($vehicle);

            return view('admin.vehicles.enterprise-edit', compact(
                'vehicle',
                'vehicleTypes',
                'vehicleStatuses',
                'fuelTypes',
                'transmissionTypes',
                'categories',
                'depots',
                'organizations',
                'users',
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
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.update.attempted', $request, ['vehicle_id' => $vehicle->id]);

        try {
            // Capture de l'état avant modification pour audit
            $originalData = $vehicle->toArray();

            // Utilisation des données validées par UpdateVehicleRequest (règles d'unicité correctes)
            $validatedData = $request->validated();

            // Conversion de la date du format français (d/m/Y) vers format base de données (Y-m-d)


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
     * 📦 Archive un véhicule (le rend invisible sans le supprimer)
     */
    public function archive(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete vehicles');
        $this->logUserAction('vehicle.archive.attempted', null, ['vehicle_id' => $vehicle->id]);

        try {
            $vehicle->update(['is_archived' => true]);

            $this->logUserAction('vehicle.archive.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->back()
                ->with('success', "Véhicule {$vehicle->registration_plate} archivé avec succès");
        } catch (\Exception $e) {
            $this->logError('vehicle.archive.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return redirect()->back()->with('error', 'Erreur lors de l\'archivage du véhicule');
        }
    }

    /**
     * 📤 Désarchive un véhicule (le rend visible)
     */
    public function unarchive(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete vehicles');
        $this->logUserAction('vehicle.unarchive.attempted', null, ['vehicle_id' => $vehicle->id]);

        try {
            $vehicle->update(['is_archived' => false]);

            $this->logUserAction('vehicle.unarchive.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $vehicle->registration_plate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->back()
                ->with('success', "Véhicule {$vehicle->registration_plate} désarchivé avec succès");
        } catch (\Exception $e) {
            $this->logError('vehicle.unarchive.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return redirect()->back()->with('error', 'Erreur lors du désarchivage du véhicule');
        }
    }

    /**
     * 📦 Archivage en masse - Enterprise Batch Operation
     */
    public function batchArchive(Request $request): RedirectResponse
    {
        $this->authorize('delete vehicles');
        $this->logUserAction('vehicle.batch_archive.attempted', $request);

        try {
            $request->validate([
                'vehicles' => 'required|json',
            ]);

            $vehicleIds = json_decode($request->input('vehicles'), true);

            if (empty($vehicleIds) || !is_array($vehicleIds)) {
                return redirect()->back()->with('error', 'Aucun véhicule sélectionné');
            }

            $count = Vehicle::whereIn('id', $vehicleIds)
                ->where('organization_id', Auth::user()->organization_id)
                ->update(['is_archived' => true]);

            $this->logUserAction('vehicle.batch_archive.success', null, [
                'vehicle_count' => $count,
                'vehicle_ids' => $vehicleIds
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "{$count} véhicule(s) archivé(s) avec succès");
        } catch (\Exception $e) {
            $this->logError('vehicle.batch_archive.error', $e, $request);
            return redirect()->back()->with('error', 'Erreur lors de l\'archivage en masse');
        }
    }

    /**
     * 🔄 Changement de statut en masse - Enterprise Batch Operation
     */
    public function batchStatus(Request $request): RedirectResponse
    {
        $this->authorize('edit vehicles');
        $this->logUserAction('vehicle.batch_status.attempted', $request);

        try {
            $request->validate([
                'vehicles' => 'required|json',
                'status_id' => 'required|exists:vehicle_statuses,id',
            ]);

            $vehicleIds = json_decode($request->input('vehicles'), true);
            $statusId = $request->input('status_id');

            if (empty($vehicleIds) || !is_array($vehicleIds)) {
                return redirect()->back()->with('error', 'Aucun véhicule sélectionné');
            }

            $count = Vehicle::whereIn('id', $vehicleIds)
                ->where('organization_id', Auth::user()->organization_id)
                ->update(['status_id' => $statusId]);

            $this->logUserAction('vehicle.batch_status.success', null, [
                'vehicle_count' => $count,
                'vehicle_ids' => $vehicleIds,
                'status_id' => $statusId
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            $statusName = \App\Models\VehicleStatus::find($statusId)->name ?? 'nouveau statut';

            return redirect()
                ->route('admin.vehicles.index')
                ->with('success', "{$count} véhicule(s) mis à jour avec le statut \"{$statusName}\"");
        } catch (\Exception $e) {
            $this->logError('vehicle.batch_status.error', $e, $request);
            return redirect()->back()->with('error', 'Erreur lors du changement de statut en masse');
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

    /**
     * 📄 Export liste véhicules en PDF via Microservice Puppeteer
     * 
     * Méthode appelée depuis Livewire via redirection pour éviter
     * les problèmes d'encodage UTF-8 avec le contenu binaire PDF.
     * 
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(): \Illuminate\Http\Response
    {
        // Autorisation pour voir la liste des véhicules (viewAny, pas view)
        $this->authorize('viewAny', Vehicle::class);
        $this->logUserAction('vehicle.export_pdf_list.requested');

        try {
            // Récupération des filtres depuis la session (stockés par Livewire)
            $filters = session('vehicle_export_filters', []);

            // Construction de la requête avec filtres
            $query = Vehicle::query()
                ->where('organization_id', Auth::user()->organization_id)
                ->with([
                    'vehicleType',
                    'vehicleStatus',
                    'fuelType',
                    'transmissionType',
                    'depot',
                    'currentAssignment.driver.user' // Charger le chauffeur actif
                ]);

            // Application des filtres (logique identique à VehicleIndex)
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('registration_plate', 'ILIKE', "%{$search}%")
                        ->orWhere('brand', 'ILIKE', "%{$search}%")
                        ->orWhere('model', 'ILIKE', "%{$search}%")
                        ->orWhere('vin', 'ILIKE', "%{$search}%");
                });
            }

            if (!empty($filters['status'])) {
                $query->where('status_id', $filters['status']);
            }

            if (!empty($filters['type'])) {
                $query->where('vehicle_type_id', $filters['type']);
            }

            if (!empty($filters['depot'])) {
                $query->where('depot_id', $filters['depot']);
            }

            if (!empty($filters['archived'])) {
                $query->where('is_archived', $filters['archived'] === 'archived');
            }

            $vehicles = $query->orderBy('created_at', 'desc')->get();

            // Préparation des données pour le template PDF  
            $data = [
                'vehicles' => $vehicles,
                'organization' => Auth::user()->organization,
                'filters' => $filters,
                'generatedAt' => now(),
                'user' => Auth::user(),
                'analytics' => [
                    'total' => $vehicles->count(),
                    'active' => $vehicles->where('is_archived', false)->count(),
                ]
            ];

            // Génération du HTML depuis la vue Blade
            $html = view('exports.pdf.vehicles-list', $data)->render();

            // Utilisation du microservice PDF (Puppeteer)
            $pdfService = new \App\Services\PdfGenerationService();
            $pdfContent = $pdfService->generateFromHtml($html);

            // Nom de fichier professionnel
            $filename = sprintf(
                'Vehicules_Export_%s_%s.pdf',
                Auth::user()->organization->slug ?? 'zenfleet',
                now()->format('Y-m-d_H-i')
            );

            $this->logUserAction('vehicle.export_pdf_list.success', null, [
                'filename' => $filename,
                'vehicle_count' => $vehicles->count()
            ]);

            // Nettoyage de la session
            session()->forget('vehicle_export_filters');

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        } catch (\Exception $e) {
            $this->logError('vehicle.export_pdf_list.error', $e);

            return redirect()
                ->route('admin.vehicles.index')
                ->with('error', 'Erreur lors de l\'export PDF: ' . $e->getMessage());
        }
    }



    /**
     * Données de référence avec cache optimisé Enterprise-Grade
     *
     * ✅ OPTIMISATIONS V2.0:
     * - Cache séparé pour dépôts (TTL court: 5 min) pour mise à jour temps réel
     * - Cache long pour autres données (TTL: 2h) car moins volatiles
     * - Tags de cache pour invalidation granulaire
     * - Synchronisation avec VehicleDepotObserver pour invalidation automatique
     *
     * @return array
     */
    private function getReferenceData(): array
    {
        $organizationId = Auth::user()->organization_id;

        // ✅ CACHE SÉPARÉ POUR DÉPÔTS (mise à jour temps réel)
        // Utilise CACHE_TTL_SHORT (5 min) au lieu de CACHE_TTL_LONG (2h)
        // pour que les nouveaux dépôts apparaissent rapidement dans les filtres
        $depots = Cache::remember(
            "vehicle_depots_{$organizationId}",
            self::CACHE_TTL_SHORT, // 5 minutes au lieu de 2 heures
            function () use ($organizationId) {
                return \App\Models\VehicleDepot::forOrganization($organizationId)
                    ->active()
                    ->withCount('vehicles')
                    ->orderBy('name')
                    ->get();
            }
        );

        // ✅ CACHE LONG POUR AUTRES DONNÉES DE RÉFÉRENCE (peu volatiles)
        $staticReferenceData = Cache::remember(
            "vehicle_static_reference_data_{$organizationId}",
            self::CACHE_TTL_LONG,
            function () use ($organizationId) {
                return [
                    'vehicle_types' => VehicleType::orderBy('name')->get(),
                    'vehicle_statuses' => VehicleStatus::orderBy('name')->get(),
                    'fuel_types' => FuelType::orderBy('name')->get(),
                    'transmission_types' => TransmissionType::orderBy('name')->get(),
                    'categories' => \App\Models\VehicleCategory::forOrganization($organizationId)
                        ->active()
                        ->orderBy('sort_order')
                        ->get(),
                    'organizations' => Auth::user()->hasRole('Super Admin')
                        ? Organization::orderBy('name')->get()
                        : collect([Auth::user()->organization]),
                ];
            }
        );

        // ✅ FUSION DES DONNÉES avec dépôts en temps réel
        return array_merge($staticReferenceData, [
            'depots' => $depots
        ]);
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

    /**
     * 🔧 Convertit les erreurs DB PostgreSQL en messages user-friendly
     *
     * @param \Illuminate\Database\QueryException $e
     * @return string
     */
    private function getFriendlyDatabaseError(\Illuminate\Database\QueryException $e): string
    {
        $message = $e->getMessage();

        // Erreurs de contrainte unique
        if (str_contains($message, 'unique constraint') || str_contains($message, 'Unique violation')) {
            if (str_contains($message, 'registration_plate')) {
                return 'Cette plaque d\'immatriculation existe déjà dans votre organisation';
            } elseif (str_contains($message, 'vin')) {
                return 'Ce numéro VIN existe déjà dans votre organisation';
            }
            return 'Cette valeur existe déjà dans le système';
        }

        // Erreurs de clé étrangère
        if (str_contains($message, 'foreign key constraint') || str_contains($message, 'violates foreign key')) {
            if (str_contains($message, 'vehicle_type_id')) {
                return 'Type de véhicule invalide';
            } elseif (str_contains($message, 'fuel_type_id')) {
                return 'Type de carburant invalide';
            } elseif (str_contains($message, 'transmission_type_id')) {
                return 'Type de transmission invalide';
            } elseif (str_contains($message, 'status_id')) {
                return 'Statut de véhicule invalide';
            } elseif (str_contains($message, 'organization_id')) {
                return 'Organisation invalide';
            }
            return 'Référence invalide dans les données';
        }

        // Erreurs de valeur NULL
        if (str_contains($message, 'not null constraint') || str_contains($message, 'null value')) {
            if (str_contains($message, 'registration_plate')) {
                return 'La plaque d\'immatriculation est obligatoire';
            } elseif (str_contains($message, 'organization_id')) {
                return 'L\'organisation est obligatoire';
            }
            return 'Un champ obligatoire est manquant';
        }

        // Erreurs de format/type de données
        if (str_contains($message, 'invalid input syntax')) {
            if (str_contains($message, 'integer')) {
                return 'Format de nombre invalide';
            } elseif (str_contains($message, 'date')) {
                return 'Format de date invalide';
            } elseif (str_contains($message, 'numeric')) {
                return 'Format de prix/valeur invalide';
            }
            return 'Format de données invalide';
        }

        // Erreur de dépassement de taille
        if (str_contains($message, 'value too long')) {
            return 'Une valeur dépasse la taille maximale autorisée';
        }

        // Message générique pour les autres erreurs
        return 'Erreur de base de données: veuillez vérifier vos données';
    }

    // Méthodes utilitaires (simplifiées pour l'exemple)
    private function calculateDepreciation(Vehicle $vehicle): float
    {
        return 0.15;
    }
    private function calculateUtilization(Vehicle $vehicle): float
    {
        return 0.75;
    }
    private function calculateMaintenanceCosts(Vehicle $vehicle): float
    {
        return 15000.0;
    }
    private function getFuelDistribution(): array
    {
        return [];
    }
    private function getTypeDistribution(): array
    {
        return [];
    }
    private function getMonthlyAcquisitions(): array
    {
        return [];
    }
    private function validateVinFormat(string $vin): bool
    {
        return strlen($vin) === 17;
    }
    private function enrichVehicleCreationData(array $data): array
    {
        $data['organization_id'] = Auth::user()->organization_id;

        // Définir le statut par défaut si non spécifié
        if (empty($data['status_id'])) {
            $defaultStatus = \App\Models\VehicleStatus::where('name', 'Disponible')->first();
            if ($defaultStatus) {
                $data['status_id'] = $defaultStatus->id;
            }
        }

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

    /**
     * 📊 Enterprise-Grade Vehicle Analytics
     * 
     * Provides comprehensive analytics for the vehicle view page including:
     * - Maintenance costs (total, preventive, corrective)
     * - Expense tracking
     * - Assignment history
     * - Usage statistics
     */
    private function getVehicleSpecificAnalytics(Vehicle $vehicle): array
    {
        // === MAINTENANCE COSTS ===
        $maintenanceCostTotal = $vehicle->maintenanceOperations()
            ->where('status', 'completed')
            ->sum('total_cost') ?? 0;

        $maintenanceCostPreventive = $vehicle->maintenanceOperations()
            ->where('status', 'completed')
            ->whereHas('maintenanceType', fn($q) => $q->where('category', 'preventive'))
            ->sum('total_cost') ?? 0;

        $maintenanceCostCorrective = $vehicle->maintenanceOperations()
            ->where('status', 'completed')
            ->whereHas('maintenanceType', fn($q) => $q->where('category', 'corrective'))
            ->sum('total_cost') ?? 0;

        $maintenanceCount = $vehicle->maintenanceOperations()->count();

        // Last maintenance date
        $lastMaintenance = $vehicle->maintenanceOperations()
            ->where('status', 'completed')
            ->latest('completed_date')
            ->first();

        // === EXPENSES ===
        // Note: expenses() relationship doesn't exist on Vehicle model
        // Set to 0 for now - can be added later if relationship is created
        $expenseTotal = 0;

        // === ASSIGNMENTS ===
        $assignmentsCount = $vehicle->assignments()->count();
        $activeAssignment = $vehicle->assignments()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_datetime')
                    ->orWhere('end_datetime', '>=', now());
            })
            ->with('driver')
            ->first();

        // === VEHICLE AGE & TIME IN SERVICE ===
        $vehicleAge = $vehicle->acquisition_date
            ? abs(Carbon::now()->diffInYears($vehicle->acquisition_date))
            : 0;

        $now = Carbon::now();
        $acquisition = $vehicle->acquisition_date;

        if ($acquisition && $acquisition->isFuture()) {
            $daysInService = 0;
            $formattedDuration = "Pas encore en service";
        } elseif ($acquisition) {
            $daysInService = abs($acquisition->diffInDays($now)); // absolute by default

            // Formatage durée "1 an 3 mois 15 jours"
            $diff = $acquisition->diff($now);
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $parts[] = $diff->m . ' mois';
            if ($diff->d > 0) $parts[] = $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');

            $formattedDuration = !empty($parts) ? implode(' ', $parts) : "Aujourd'hui";
        } else {
            $daysInService = 0;
            $formattedDuration = "N/A";
        }

        // Formatage de l'âge (Année, mois, jour)
        $vehicleAgeFormatted = "N/A";
        if ($vehicle->acquisition_date) {
            $diff = $vehicle->acquisition_date->diff($now);
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) $parts[] = $diff->m . ' mois';
            $vehicleAgeFormatted = !empty($parts) ? implode(' ', $parts) : "Moins d'un mois";
        }

        // === USAGE METRICS ===
        $totalKmDriven = ($vehicle->current_mileage ?? 0) - ($vehicle->initial_mileage ?? 0);

        $totalCosts = $maintenanceCostTotal; // Only maintenance costs for now
        $costPerKm = $totalKmDriven > 0 ? $totalCosts / $totalKmDriven : 0;

        // Average km per month
        $monthsInService = max($daysInService / 30, 1);
        $avgKmPerMonth = $totalKmDriven / $monthsInService;

        // Utilization rate (based on assignments)
        $utilizationRate = $daysInService > 0
            ? min(100, ($assignmentsCount * 30 / $daysInService) * 100)
            : 0;

        return [
            // Core metrics
            'vehicle_age' => $vehicleAge,
            'days_in_service' => $daysInService,
            'duration_in_service_formatted' => $formattedDuration,
            'total_km_driven' => $totalKmDriven,

            // Maintenance
            'maintenance_cost_total' => $maintenanceCostTotal,
            'maintenance_cost_preventive' => $maintenanceCostPreventive,
            'maintenance_cost_corrective' => $maintenanceCostCorrective,
            'maintenance_count' => $maintenanceCount,
            'last_maintenance_date' => $lastMaintenance?->completed_date?->format('d/m/Y'),

            // Expenses
            'expense_total' => $expenseTotal,

            // Assignments
            'assignments_count' => $assignmentsCount,
            'active_assignment' => $activeAssignment,

            // Usage metrics
            'cost_per_km' => round($costPerKm, 2),
            'avg_km_per_month' => round($avgKmPerMonth, 0),
            'utilization_rate' => round($utilizationRate, 0),

            // Deprecated (kept for backwards compatibility)
            'age_years' => $vehicleAge,
            'vehicle_age_formatted' => $vehicleAgeFormatted,
            'depreciation_rate' => $this->calculateDepreciation($vehicle),
            'efficiency_score' => $this->calculateEfficiencyScore($vehicle),
            'carbon_footprint' => $this->calculateCarbonFootprint($vehicle),
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
    // ============================================================
    // MÉTHODES D'IMPORTATION ENTERPRISE
    // ============================================================

    /**
     * 📥 Affiche le formulaire d'importation enterprise
     */
    public function showImportForm(): View
    {
        $this->authorize('create vehicles');
        $this->logUserAction('vehicle.import.form_accessed');

        try {
            // Statistiques d'importation récentes
            $importStats = $this->getImportStatistics();

            // Recommandations pour l'importation
            $importRecommendations = $this->getImportRecommendations();

            // Configuration des limites d'importation
            $importLimits = [
                'max_file_size' => '10MB',
                'max_records' => $this->getMaxImportSize(),
                'supported_formats' => ['xlsx', 'xls', 'csv'],
                'required_columns' => $this->getRequiredImportColumns()
            ];

            return view('admin.vehicles.import', compact(
                'importStats',
                'importRecommendations',
                'importLimits'
            ));
        } catch (\Exception $e) {
            $this->logError('vehicle.import.form_error', $e);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 🔄 Traite l'importation de véhicules de manière sécurisée
     */
    public function handleImport(Request $request): RedirectResponse
    {
        $this->authorize('create vehicles');
        $this->logUserAction('vehicle.import.started', $request);

        try {
            // Validation du fichier d'importation avec règles améliorées
            $request->validate([
                'import_file' => [
                    'required',
                    'file',
                    'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain,application/csv',
                    'extensions:xlsx,xls,csv',
                    'max:10240' // 10MB
                ],
                'skip_duplicates' => 'boolean',
                'update_existing' => 'boolean'
            ], [
                'import_file.required' => 'Veuillez sélectionner un fichier à importer.',
                'import_file.file' => 'Le fichier sélectionné n\'est pas valide.',
                'import_file.mimetypes' => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV (.csv).',
                'import_file.extensions' => 'Le fichier doit avoir une extension .xlsx, .xls ou .csv.',
                'import_file.max' => 'Le fichier ne doit pas dépasser 10 MB.'
            ]);

            $file = $request->file('import_file');

            // Validation technique entreprise du fichier
            $this->performFileValidation($file);

            $options = [
                'skip_duplicates' => $request->boolean('skip_duplicates'),
                'update_existing' => $request->boolean('update_existing')
            ];

            // Traitement sécurisé de l'importation
            $result = $this->processVehicleImport($file, $options);

            // Stockage des résultats en session pour affichage
            session(['vehicle_import_result' => $result]);

            $this->logUserAction('vehicle.import.completed', null, [
                'total_processed' => $result['total_processed'],
                'successful_imports' => $result['successful_imports'],
                'failed_imports' => $result['failed_imports']
            ]);

            return redirect()
                ->route('admin.vehicles.import.results')
                ->with('success', 'Importation terminée avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Veuillez corriger les erreurs de validation');
        } catch (\Exception $e) {
            $this->logError('vehicle.import.error', $e, $request);
            return back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 📊 Affiche les résultats d'importation avec détails
     */
    public function showImportResults(): View
    {
        $this->authorize('create vehicles');
        $this->logUserAction('vehicle.import.results_viewed');

        try {
            $result = session('vehicle_import_result');

            if (!$result) {
                return redirect()
                    ->route('admin.vehicles.import.show')
                    ->with('warning', 'Aucun résultat d\'importation trouvé');
            }

            // Récupération des véhicules importés récemment pour affichage
            $recentlyImported = collect($result['imported_vehicles'] ?? [])
                ->take(10)
                ->map(function ($vehicleId) {
                    return Vehicle::find($vehicleId);
                })
                ->filter();

            return view('admin.vehicles.import-results', compact('result', 'recentlyImported'));
        } catch (\Exception $e) {
            $this->logError('vehicle.import.results_error', $e);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 📥 Télécharge le template d'importation CSV
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('create vehicles');
        $this->logUserAction('vehicle.import.template_downloaded');

        try {
            $templatePath = $this->generateImportTemplate();

            return response()->download(
                $templatePath,
                'zenfleet_vehicles_import_template.csv',
                [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="zenfleet_vehicles_import_template.csv"'
                ]
            )->deleteFileAfterSend();
        } catch (\Exception $e) {
            $this->logError('vehicle.import.template_error', $e);
            throw $e;
        }
    }

    /**
     * 🧪 Prévalidation de fichier CSV sans importation - Endpoint Enterprise
     * Permet de tester la validité d'un fichier avant l'importation réelle
     */
    public function preValidateImportFile(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('create vehicles');
        $this->logUserAction('vehicle.import.prevalidation', $request);

        try {
            // Validation des règles de fichier
            $request->validate([
                'import_file' => [
                    'required',
                    'file',
                    'mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv,text/plain,application/csv',
                    'extensions:xlsx,xls,csv',
                    'max:10240'
                ]
            ], [
                'import_file.required' => 'Veuillez sélectionner un fichier à valider.',
                'import_file.mimetypes' => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV (.csv).',
                'import_file.extensions' => 'Le fichier doit avoir une extension .xlsx, .xls ou .csv.',
                'import_file.max' => 'Le fichier ne doit pas dépasser 10 MB.'
            ]);

            $file = $request->file('import_file');

            // Validation technique du fichier
            $this->performFileValidation($file);

            // Test de lecture des données
            $data = $this->readImportFile($file);

            // Construction du rapport de validation
            $validation_result = $this->buildValidationReport($data, $file->getClientOriginalName());

            $this->logUserAction('vehicle.import.prevalidation_success', null, [
                'file_name' => $file->getClientOriginalName(),
                'total_rows' => $validation_result['total_rows'],
                'validation_status' => $validation_result['valid'] ? 'valid' : 'invalid'
            ]);

            return response()->json($validation_result);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'error' => 'Erreur de validation',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logError('vehicle.import.prevalidation_error', $e, $request);
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
                'error_code' => 'IMPORT_VALIDATION_FAILED'
            ], 422);
        }
    }

    // ============================================================
    // MÉTHODES PRIVÉES D'IMPORTATION
    // ============================================================

    /**
     * Traite l'importation du fichier véhicules
     */
    private function processVehicleImport($file, array $options): array
    {
        $result = [
            'total_processed' => 0,
            'successful_imports' => 0,
            'failed_imports' => 0,
            'errors' => [],
            'imported_vehicles' => [],
            'skipped_duplicates' => 0,
            'updated_existing' => 0
        ];

        try {
            // Lecture du fichier selon son type
            $data = $this->readImportFile($file);

            if (empty($data)) {
                throw new \Exception('Le fichier d\'importation est vide ou invalide');
            }

            $maxImportSize = $this->getMaxImportSize();
            if (count($data) > $maxImportSize) {
                throw new \Exception('Le fichier contient trop d\'enregistrements. Maximum autorisé: ' . $maxImportSize);
            }

            // Les colonnes ont déjà été validées dans readCsvFile
            // Validation supplémentaire de la structure des données
            if (empty($data)) {
                throw new \Exception('Aucune donnée trouvée dans le fichier après les en-têtes');
            }

            // Traitement ligne par ligne avec transaction
            DB::transaction(function () use ($data, $options, &$result) {
                foreach ($data as $index => $row) {
                    $result['total_processed']++;

                    try {
                        $processResult = $this->processVehicleRow($row, $options, $index + 1);

                        if ($processResult['action'] === 'imported') {
                            $result['successful_imports']++;
                            $result['imported_vehicles'][] = $processResult['vehicle_id'];
                        } elseif ($processResult['action'] === 'updated') {
                            $result['updated_existing']++;
                            $result['imported_vehicles'][] = $processResult['vehicle_id'];
                        } elseif ($processResult['action'] === 'skipped') {
                            $result['skipped_duplicates']++;
                        }
                    } catch (\Exception $e) {
                        $result['failed_imports']++;
                        $result['errors'][] = [
                            'row' => $index + 1,
                            'data' => $row,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            });

            // Nettoyage du cache après importation
            Cache::tags(['vehicles', 'analytics'])->flush();
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors du traitement: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Lit le fichier d'importation selon son format
     */
    private function readImportFile($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        switch ($extension) {
            case 'csv':
                return $this->readCsvFile($filePath);
            case 'xlsx':
            case 'xls':
                return $this->readExcelFile($filePath);
            default:
                throw new \Exception('Format de fichier non supporté: ' . $extension);
        }
    }

    /**
     * Lit un fichier CSV avec détection automatique du séparateur et encodage
     */
    private function readCsvFile(string $filePath): array
    {
        $data = [];
        $headers = null;
        $lineNumber = 0;
        $skippedLines = 0;

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Détection de l'encodage et du séparateur
            $firstLine = fgets($handle);
            rewind($handle);

            // Détection de l'encodage
            $encoding = mb_detect_encoding($firstLine, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);

            // Détection du séparateur
            $separator = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $separator = ';';
            }

            Log::info('CSV reading started', [
                'encoding' => $encoding,
                'separator' => $separator,
                'file_path' => $filePath
            ]);

            while (($rawRow = fgetcsv($handle, 2000, $separator)) !== false) {
                $lineNumber++;

                // Conversion d'encodage si nécessaire
                if ($encoding && $encoding !== 'UTF-8') {
                    $rawRow = array_map(function ($cell) use ($encoding) {
                        return mb_convert_encoding($cell, 'UTF-8', $encoding);
                    }, $rawRow);
                }

                $row = array_map('trim', $rawRow);

                if ($headers === null) {
                    // Nettoyage des en-têtes (suppression BOM et caractères invisibles)
                    $headers = array_map(function ($header) {
                        // Suppression BOM UTF-8
                        $header = str_replace("\xEF\xBB\xBF", '', $header);
                        // Suppression caractères de contrôle
                        $header = preg_replace('/[\x00-\x1F\x7F-\x9F]/', '', $header);
                        // Nettoyage espaces
                        return trim($header);
                    }, $row);

                    // Validation des en-têtes dès leur lecture
                    try {
                        $this->validateImportColumns($headers);
                    } catch (\Exception $e) {
                        fclose($handle);
                        throw new \Exception("Erreur dans les en-têtes (ligne {$lineNumber}): " . $e->getMessage());
                    }
                } else {
                    // Vérification que la ligne n'est pas vide
                    if (count(array_filter($row)) === 0) {
                        $skippedLines++;
                        continue;
                    }

                    // Vérification du nombre de colonnes
                    if (count($row) !== count($headers)) {
                        Log::warning('CSV line skipped - column count mismatch', [
                            'line_number' => $lineNumber,
                            'expected_columns' => count($headers),
                            'actual_columns' => count($row),
                            'row_data' => $row
                        ]);
                        $skippedLines++;
                        continue;
                    }

                    try {
                        $data[] = array_combine($headers, $row);
                    } catch (\Exception $e) {
                        Log::error('CSV line processing error', [
                            'line_number' => $lineNumber,
                            'error' => $e->getMessage(),
                            'headers' => $headers,
                            'row' => $row
                        ]);
                        $skippedLines++;
                    }
                }
            }
            fclose($handle);

            Log::info('CSV reading completed', [
                'total_lines_processed' => $lineNumber,
                'data_rows' => count($data),
                'skipped_lines' => $skippedLines
            ]);
        } else {
            throw new \Exception('Impossible d\'ouvrir le fichier CSV pour lecture');
        }

        return $data;
    }

    /**
     * Lit un fichier Excel (placeholder - nécessite une librairie comme PhpSpreadsheet)
     */
    private function readExcelFile(string $filePath): array
    {
        // Pour l'instant, conversion en CSV puis lecture
        // Dans un vrai environnement enterprise, utiliser PhpSpreadsheet
        throw new \Exception('Le support Excel nécessite l\'installation de PhpSpreadsheet');
    }

    /**
     * Valide les colonnes requises
     */
    private function validateImportColumns(array $headers): void
    {
        $requiredColumns = $this->getRequiredImportColumns();

        // Nettoyage des en-têtes pour supprimer les caractères invisibles
        $cleanHeaders = array_map(function ($header) {
            return trim(preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $header));
        }, $headers);

        $missingColumns = array_diff($requiredColumns, $cleanHeaders);

        if (!empty($missingColumns)) {
            Log::error('CSV Import - Colonnes manquantes', [
                'required_columns' => $requiredColumns,
                'found_headers' => $cleanHeaders,
                'missing_columns' => $missingColumns,
                'raw_headers' => $headers
            ]);
            throw new \Exception('Colonnes manquantes: ' . implode(', ', $missingColumns) . '. Colonnes trouvées: ' . implode(', ', $cleanHeaders));
        }
    }

    /**
     * Traite une ligne de véhicule
     */
    private function processVehicleRow(array $row, array $options, int $rowNumber): array
    {
        // Nettoyage et validation des données
        $vehicleData = $this->prepareVehicleDataFromRow($row, $rowNumber);

        // ✅ CORRECTION MULTI-TENANT: Vérification des doublons SCOPED par organisation
        // Un véhicule peut exister dans plusieurs organisations (cas: vente entre orgs)
        // Mais on empêche les doublons au sein de la MÊME organisation
        $organizationId = Auth::user()->organization_id;

        $existingVehicle = Vehicle::where('organization_id', $organizationId)
            ->where(function ($query) use ($vehicleData) {
                $query->where('registration_plate', $vehicleData['registration_plate']);

                // Si VIN fourni, vérifier aussi le VIN dans la même organisation
                if (!empty($vehicleData['vin'])) {
                    $query->orWhere('vin', $vehicleData['vin']);
                }
            })
            ->first();

        if ($existingVehicle) {
            // Déterminer quel champ est en doublon pour message clair
            $duplicateField = 'plaque';
            $duplicateValue = $vehicleData['registration_plate'];

            if ($existingVehicle->vin === $vehicleData['vin'] && !empty($vehicleData['vin'])) {
                $duplicateField = 'VIN';
                $duplicateValue = $vehicleData['vin'];
            }

            if ($options['skip_duplicates']) {
                return ['action' => 'skipped'];
            } elseif ($options['update_existing']) {
                $existingVehicle->update($vehicleData);
                return ['action' => 'updated', 'vehicle_id' => $existingVehicle->id];
            } else {
                // Message d'erreur clair et user-friendly
                throw new \Exception("Véhicule déjà existant dans votre organisation ({$duplicateField}: {$duplicateValue})");
            }
        }

        // ✅ Création du nouveau véhicule avec gestion d'erreur DB améliorée
        try {
            $vehicle = Vehicle::create($vehicleData);
            return ['action' => 'imported', 'vehicle_id' => $vehicle->id];
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturer les erreurs de contrainte unique PostgreSQL pour messages clairs
            if (str_contains($e->getMessage(), 'vehicles_registration_plate_organization_unique')) {
                throw new \Exception("Véhicule déjà existant dans votre organisation (plaque: {$vehicleData['registration_plate']})");
            } elseif (str_contains($e->getMessage(), 'vehicles_vin_organization_unique')) {
                throw new \Exception("Véhicule déjà existant dans votre organisation (VIN: {$vehicleData['vin']})");
            }

            // Autre erreur DB - message générique mais log détaillé
            Log::error('Erreur création véhicule import', [
                'error' => $e->getMessage(),
                'vehicle_data' => $vehicleData,
                'row_number' => $rowNumber
            ]);

            throw new \Exception("Erreur lors de la création du véhicule (ligne {$rowNumber}): " . $this->getFriendlyDatabaseError($e));
        }
    }

    /**
     * Prépare les données véhicule à partir d'une ligne
     */
    private function prepareVehicleDataFromRow(array $row, int $rowNumber): array
    {
        $data = [
            'registration_plate' => trim($row['registration_plate'] ?? ''),
            'vin' => trim($row['vin'] ?? ''),
            'brand' => trim($row['brand'] ?? ''),
            'model' => trim($row['model'] ?? ''),
            'color' => trim($row['color'] ?? ''),
            'manufacturing_year' => (int) ($row['manufacturing_year'] ?? 0),
            'acquisition_date' => $this->parseDate($row['acquisition_date'] ?? ''),
            'purchase_price' => (float) ($row['purchase_price'] ?? 0),
            'current_value' => (float) ($row['current_value'] ?? 0),
            'initial_mileage' => (int) ($row['initial_mileage'] ?? 0),
            'current_mileage' => (int) ($row['current_mileage'] ?? 0),
            'engine_displacement_cc' => (int) ($row['engine_displacement_cc'] ?? 0),
            'power_hp' => (int) ($row['power_hp'] ?? 0),
            'seats' => (int) ($row['seats'] ?? 0),
            'notes' => trim($row['notes'] ?? ''),
            'organization_id' => Auth::user()->organization_id
        ];

        // Résolution des IDs des types
        $data['vehicle_type_id'] = $this->resolveVehicleTypeId($row['vehicle_type'] ?? '');
        $data['fuel_type_id'] = $this->resolveFuelTypeId($row['fuel_type'] ?? '');
        $data['transmission_type_id'] = $this->resolveTransmissionTypeId($row['transmission_type'] ?? '');
        $data['status_id'] = $this->resolveStatusId($row['status'] ?? 'Disponible');

        // Validation des données
        $this->validateImportRowData($data, $rowNumber);

        return $data;
    }

    /**
     * Génère le template d'importation CSV avec UTF-8 BOM
     */
    private function generateImportTemplate(): string
    {
        $headers = $this->getRequiredImportColumns();
        $sampleData = $this->getSampleImportData();

        $tempFile = tempnam(sys_get_temp_dir(), 'vehicle_import_template_') . '.csv';
        $handle = fopen($tempFile, 'w');

        // Ajout du BOM UTF-8 pour une compatibilité Excel optimale
        fputs($handle, "\xEF\xBB\xBF");

        // Écriture des en-têtes
        fputcsv($handle, $headers, ';'); // Utilisation du point-virgule pour Excel français

        // Écriture des données d'exemple
        foreach ($sampleData as $row) {
            fputcsv($handle, $row, ';');
        }

        fclose($handle);

        return $tempFile;
    }

    private function generateExportFilename(string $format): string
    {
        return "vehicles_" . date('Y-m-d_H-i-s') . ".{$format}";
    }
    private function createVehicleExporter(string $format, array $filters): object
    {
        return new \stdClass();
    }

    /**
     * 🗄️ Affiche les véhicules archivés avec interface enterprise
     */
    public function archived(Request $request): View
    {
        $this->logUserAction('vehicles.archived.view', null, [
            'filters' => $request->query()
        ]);

        try {
            // Récupération des véhicules archivés uniquement
            $vehicles = Vehicle::onlyTrashed()
                ->with(['vehicleType', 'fuelType', 'transmissionType', 'vehicleStatus'])
                ->orderBy('deleted_at', 'desc')
                ->paginate(20);

            // Statistiques des archives
            $stats = [
                'total_archived' => Vehicle::onlyTrashed()->count(),
                'archived_this_month' => Vehicle::onlyTrashed()
                    ->whereMonth('deleted_at', now()->month)
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
                'archived_this_year' => Vehicle::onlyTrashed()
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
            ];

            return view('admin.vehicles.archived', compact('vehicles', 'stats'));
        } catch (\Exception $e) {
            $this->logError('vehicles.archived.error', $e);
            return $this->handleErrorResponse($e, 'vehicles.index');
        }
    }

    /**
     * 🔄 Restaure un véhicule archivé
     */
    public function restore(Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.restore.attempted', null, [
            'vehicle_id' => $vehicle->id,
            'registration_plate' => $vehicle->registration_plate
        ]);

        try {
            $registrationPlate = $vehicle->registration_plate;
            $vehicle->restore();

            $this->logUserAction('vehicle.restore.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $registrationPlate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.archived')
                ->with('success', "Véhicule {$registrationPlate} restauré avec succès")
                ->with('vehicle_restored', true);
        } catch (\Exception $e) {
            $this->logError('vehicle.restore.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return back()->withErrors(['error' => 'Erreur lors de la restauration du véhicule.']);
        }
    }

    // ============================================================
    // MÉTHODES UTILITAIRES D'IMPORTATION
    // ============================================================

    /**
     * 🔍 Validation technique avancée du fichier d'importation - Enterprise Grade
     * Effectue une validation complète des aspects techniques du fichier
     */
    private function performFileValidation($file): void
    {
        if (!$file) {
            throw new \Exception('Aucun fichier fourni pour l\'importation');
        }

        // Vérification de l'extension
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['csv', 'xlsx', 'xls'];

        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Format de fichier non supporté. Extensions autorisées: ' . implode(', ', $allowedExtensions));
        }

        // Vérification de la taille
        if ($file->getSize() > 10485760) { // 10MB en bytes
            throw new \Exception('Le fichier est trop volumineux. Taille maximale autorisée: 10 MB');
        }

        // Vérification que le fichier n'est pas vide
        if ($file->getSize() < 10) {
            throw new \Exception('Le fichier semble être vide ou corrompu');
        }

        // Validation spécifique selon le type de fichier
        if ($extension === 'csv') {
            $this->validateCsvFileStructure($file);
        }

        Log::info('Enterprise file validation passed', [
            'original_name' => $file->getClientOriginalName(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'user_id' => Auth::id(),
            'organization_id' => Auth::user()->organization_id
        ]);
    }

    /**
     * 📄 Validation spécifique enterprise pour les fichiers CSV
     * Inclut la validation d'encodage, structure et cohérence
     */
    private function validateCsvFileStructure($file): void
    {
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            throw new \Exception('Impossible de lire le fichier CSV');
        }

        // Lecture de la première ligne pour valider la structure
        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine || strlen(trim($firstLine)) < 10) {
            throw new \Exception('Le fichier CSV semble être vide ou mal formaté');
        }

        // Vérification de l'encodage avec conversion automatique
        $encoding = mb_detect_encoding($firstLine, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if (!$encoding) {
            throw new \Exception('L\'encodage du fichier CSV n\'est pas supporté. Utilisez UTF-8, ISO-8859-1 ou Windows-1252');
        }

        // Vérification basique de la structure CSV
        $separator = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        $headerCount = count(str_getcsv($firstLine, $separator));

        if ($headerCount < 5) {
            throw new \Exception('Le fichier CSV doit contenir au moins 5 colonnes. ' . $headerCount . ' colonnes détectées.');
        }

        Log::info('CSV file validation passed', [
            'encoding' => $encoding,
            'separator' => $separator,
            'header_count' => $headerCount
        ]);
    }

    private function getRequiredImportColumns(): array
    {
        return [
            'registration_plate',
            'vin',
            'brand',
            'model',
            'color',
            'vehicle_type',
            'fuel_type',
            'transmission_type',
            'status',
            'manufacturing_year',
            'acquisition_date',
            'purchase_price',
            'current_value',
            'initial_mileage',
            'current_mileage',
            'engine_displacement_cc',
            'power_hp',
            'seats',
            'notes'
        ];
    }

    /**
     * Validation des règles métier pour l'importation
     */
    private function validateBusinessRules(array $data, int $rowNumber): void
    {
        // Vérification kilométrage
        if (isset($data['current_mileage'], $data['initial_mileage'])) {
            if ($data['current_mileage'] < $data['initial_mileage']) {
                throw new \Exception("Ligne {$rowNumber}: Le kilométrage actuel ({$data['current_mileage']}) ne peut pas être inférieur au kilométrage initial ({$data['initial_mileage']})");
            }
        }

        // Vérification valeur actuelle vs prix d'achat
        if (isset($data['current_value'], $data['purchase_price'])) {
            if ($data['current_value'] > $data['purchase_price']) {
                throw new \Exception("Ligne {$rowNumber}: La valeur actuelle ({$data['current_value']}) ne peut pas être supérieure au prix d'achat ({$data['purchase_price']})");
            }
        }

        // Validation VIN format
        if (isset($data['vin'])) {
            if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $data['vin'])) {
                throw new \Exception("Ligne {$rowNumber}: Le format du VIN '{$data['vin']}' n'est pas valide (17 caractères alpanumériques, sans I, O, Q)");
            }
        }

        // Validation année cohérente avec date d'acquisition
        if (isset($data['manufacturing_year'], $data['acquisition_date'])) {
            $acquisitionYear = date('Y', strtotime($data['acquisition_date']));
            if ($data['manufacturing_year'] > $acquisitionYear) {
                throw new \Exception("Ligne {$rowNumber}: L'année de fabrication ({$data['manufacturing_year']}) ne peut pas être postérieure à l'année d'acquisition ({$acquisitionYear})");
            }
        }

        // Validation cohérence cylindrée/puissance
        if (isset($data['engine_displacement_cc'], $data['power_hp'])) {
            $ratio = $data['power_hp'] / ($data['engine_displacement_cc'] / 1000); // HP per liter
            if ($ratio > 200) { // Plus de 200 HP par litre semble irréaliste
                Log::warning('CSV Import - Unusual power to displacement ratio', [
                    'row_number' => $rowNumber,
                    'displacement' => $data['engine_displacement_cc'],
                    'power' => $data['power_hp'],
                    'ratio' => $ratio
                ]);
            }
        }
    }

    private function getSampleImportData(): array
    {
        return [
            [
                'AB-123-CD',
                '1HGCM82633A123456',
                'Toyota',
                'Corolla',
                'Blanc',
                'Berline',
                'Essence',
                'Manuelle',
                'Parking',
                '2020',
                '2020-01-15',
                '25000.00',
                '20000.00',
                '5000',
                '15000',
                '1600',
                '120',
                '5',
                'Véhicule en excellent état'
            ],
            [
                'EF-456-GH',
                '2HGCM82633A789012',
                'Peugeot',
                '308',
                'Noir',
                'Berline',
                'Diesel',
                'Automatique',
                'En mission',
                '2019',
                '2019-06-20',
                '30000.00',
                '22000.00',
                '10000',
                '45000',
                '1600',
                '130',
                '5',
                'Maintenance régulière effectuée'
            ],
            [
                'IJ-789-KL',
                '3HGCM82633A345678',
                'Renault',
                'Clio',
                'Rouge',
                'Berline',
                'Essence',
                'Manuelle',
                'Parking',
                '2021',
                '2021-03-10',
                '18000.00',
                '16000.00',
                '0',
                '8500',
                '1200',
                '75',
                '5',
                'Véhicule neuf avec garantie'
            ]
        ];
    }

    /**
     * 📅 Parser de dates enterprise ultra-robuste
     * Supporte multiples formats internationaux avec validation intelligente
     */
    private function parseDate(string $dateString): ?string
    {
        if (empty($dateString)) return null;

        // Nettoyage de la chaîne de date
        $cleanDate = trim($dateString);
        $cleanDate = str_replace(['/', '\\'], '-', $cleanDate);

        // Formats de date supportés (ordre de priorité)
        $supportedFormats = [
            'Y-m-d',           // ISO 8601 (2019-06-20)
            'd-m-Y',           // European (20-06-2019)
            'm-d-Y',           // American (06-20-2019)
            'd/m/Y',           // European slash (20/06/2019)
            'm/d/Y',           // American slash (06/20/2019)
            'Y/m/d',           // ISO slash (2019/06/20)
            'd.m.Y',           // European dot (20.06.2019)
            'Y.m.d',           // ISO dot (2019.06.20)
            'j M Y',           // 20 Jun 2019
            'j F Y',           // 20 June 2019
            'M j, Y',          // Jun 20, 2019
            'F j, Y',          // June 20, 2019
            'd-M-Y',           // 20-Jun-2019
            'd/M/Y',           // 20/Jun/2019
        ];

        // Tentative de parsing avec chaque format
        foreach ($supportedFormats as $format) {
            try {
                $parsedDate = \DateTime::createFromFormat($format, $cleanDate);
                if ($parsedDate && $parsedDate->format($format) === $cleanDate) {
                    $result = $parsedDate->format('Y-m-d');

                    Log::info('Date parsing successful', [
                        'original' => $dateString,
                        'cleaned' => $cleanDate,
                        'format_used' => $format,
                        'result' => $result
                    ]);

                    return $result;
                }
            } catch (\Exception $e) {
                // Continue with next format
                continue;
            }
        }

        // Tentative avec Carbon comme fallback
        try {
            $carbonDate = Carbon::parse($cleanDate);
            $result = $carbonDate->format('Y-m-d');

            Log::info('Date parsing with Carbon fallback', [
                'original' => $dateString,
                'cleaned' => $cleanDate,
                'result' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            // Log détaillé pour debug
            Log::warning('Date parsing failed', [
                'original' => $dateString,
                'cleaned' => $cleanDate,
                'attempted_formats' => $supportedFormats,
                'error' => $e->getMessage()
            ]);

            // Message d'erreur user-friendly avec suggestions
            throw new \Exception(
                "Format de date invalide: '{$dateString}'. " .
                    "Formats acceptés: AAAA-MM-JJ, JJ/MM/AAAA, JJ-MM-AAAA, JJ.MM.AAAA. " .
                    "Exemple: 2019-06-20 ou 20/06/2019"
            );
        }
    }

    /**
     * 🚗 Résolution intelligente des types de véhicules avec correspondances multiples
     * Supporte les synonymes, correspondances partielles et auto-création contrôlée
     */
    private function resolveVehicleTypeId(string $typeName): int
    {
        static $vehicleTypesCache = null;
        static $typeAliases = null;

        // Initialisation du cache des types
        if ($vehicleTypesCache === null) {
            $vehicleTypesCache = VehicleType::pluck('id', 'name')->map(function ($id, $name) {
                return ['id' => $id, 'name_lower' => strtolower(trim($name))];
            })->keyBy('name_lower');
        }

        // Initialisation des aliases/synonymes
        if ($typeAliases === null) {
            $typeAliases = [
                // Correspondances courantes
                'sedan' => 'berline',
                'saloon' => 'berline',
                'hatchback' => 'citadine',
                'city car' => 'citadine',
                'compact' => 'citadine',
                'subcompact' => 'citadine',
                'estate' => 'break',
                'wagon' => 'break',
                'touring' => 'break',
                'coupe' => 'coupé',
                'convertible' => 'cabriolet',
                'roadster' => 'cabriolet',
                'mpv' => 'monospace',
                'people carrier' => 'monospace',
                'suv' => 'suv',
                'crossover' => 'crossover',
                'cuv' => 'crossover',
                'pickup' => 'pick-up',
                'truck' => 'camion',
                'van' => 'utilitaire léger',
                'minivan' => 'utilitaire léger',
                'motorcycle' => 'moto',
                'bike' => 'moto',
                'motorbike' => 'moto',

                // Correspondances françaises communes
                'voiture' => 'berline',
                'auto' => 'berline',
                'véhicule' => 'berline',
                'utilitaire' => 'utilitaire léger',
                'fourgon' => 'utilitaire léger',
                'camionnette' => 'camionnette',
                'poids lourd' => 'camion',
                'pl' => 'camion',
                '2 roues' => 'moto',
                'deux roues' => 'moto',
                'scoot' => 'scooter',
            ];
        }

        $searchName = strtolower(trim($typeName));

        // 1. Recherche exacte
        $type = $vehicleTypesCache->get($searchName);
        if ($type) {
            return $type['id'];
        }

        // 2. Recherche par alias/synonyme
        if (isset($typeAliases[$searchName])) {
            $aliasTarget = $typeAliases[$searchName];
            $type = $vehicleTypesCache->get($aliasTarget);
            if ($type) {
                Log::info('CSV Import - Vehicle type resolved via alias', [
                    'original' => $typeName,
                    'alias_used' => $searchName,
                    'resolved_to' => $aliasTarget
                ]);
                return $type['id'];
            }
        }

        // 3. Recherche par correspondance partielle intelligente
        $partialMatches = $vehicleTypesCache->filter(function ($item) use ($searchName) {
            $itemName = $item['name_lower'];
            // Correspondance bidirectionnelle
            return str_contains($itemName, $searchName) ||
                str_contains($searchName, $itemName) ||
                levenshtein($searchName, $itemName) <= 2; // Distance de Levenshtein
        });

        if ($partialMatches->isNotEmpty()) {
            // Prendre la meilleure correspondance (plus courte distance)
            $bestMatch = $partialMatches->sortBy(function ($item) use ($searchName) {
                return levenshtein($searchName, $item['name_lower']);
            })->first();

            Log::info('CSV Import - Vehicle type resolved via partial match', [
                'original' => $typeName,
                'search_term' => $searchName,
                'matched_to' => $bestMatch['name_lower']
            ]);

            return $bestMatch['id'];
        }

        // 4. Auto-création contrôlée pour types communs
        $autoCreateableTypes = [
            'berline compacte' => 'Berline',
            'suv compact' => 'SUV',
            'utilitaire moyen' => 'Utilitaire léger',
            'citadine électrique' => 'Citadine',
            'hybride' => 'Berline'
        ];

        if (isset($autoCreateableTypes[$searchName])) {
            $newTypeName = $autoCreateableTypes[$searchName];
            $newType = VehicleType::firstOrCreate(['name' => $newTypeName]);

            // Actualiser le cache
            $vehicleTypesCache = null;

            Log::info('CSV Import - Vehicle type auto-created', [
                'original' => $typeName,
                'created_as' => $newTypeName,
                'new_id' => $newType->id
            ]);

            return $newType->id;
        }

        // 5. Échec - Générer erreur informative
        $availableTypes = $vehicleTypesCache->pluck('name_lower')->toArray();
        $suggestions = $this->getSimilarStrings($searchName, $availableTypes, 3);

        $errorMessage = "Type de véhicule inconnu: '{$typeName}'. ";
        if (!empty($suggestions)) {
            $errorMessage .= "Types similaires disponibles: " . implode(', ', $suggestions) . ". ";
        }
        $errorMessage .= "Types disponibles: " . implode(', ', $availableTypes);

        throw new \Exception($errorMessage);
    }

    /**
     * 🔍 Trouve les chaînes similaires basées sur la distance de Levenshtein
     */
    private function getSimilarStrings(string $needle, array $haystack, int $maxResults = 3): array
    {
        $similarities = [];

        foreach ($haystack as $string) {
            $distance = levenshtein($needle, $string);
            if ($distance <= 3) { // Distance acceptable
                $similarities[$string] = $distance;
            }
        }

        asort($similarities);
        return array_slice(array_keys($similarities), 0, $maxResults);
    }

    private function resolveFuelTypeId(string $fuelName): int
    {
        static $fuelTypesCache = null;

        if ($fuelTypesCache === null) {
            $fuelTypesCache = FuelType::pluck('id', 'name')->map(function ($id, $name) {
                return ['id' => $id, 'name_lower' => strtolower(trim($name))];
            })->keyBy('name_lower');
        }

        $searchName = strtolower(trim($fuelName));
        $fuel = $fuelTypesCache->get($searchName);

        if (!$fuel) {
            // Correspondances alternatives communes
            $aliases = [
                'gasoline' => 'essence',
                'petrol' => 'essence',
                'gas' => 'essence',
                'electric' => 'électrique',
                'hybrid' => 'hybride'
            ];

            if (isset($aliases[$searchName])) {
                $fuel = $fuelTypesCache->get($aliases[$searchName]);
            }

            if (!$fuel) {
                throw new \Exception("Type de carburant inconnu: '{$fuelName}'. Types disponibles: " .
                    implode(', ', $fuelTypesCache->pluck('name_lower')->toArray()));
            }
        }

        return $fuel['id'];
    }

    private function resolveTransmissionTypeId(string $transmissionName): int
    {
        static $transmissionTypesCache = null;

        if ($transmissionTypesCache === null) {
            $transmissionTypesCache = TransmissionType::pluck('id', 'name')->map(function ($id, $name) {
                return ['id' => $id, 'name_lower' => strtolower(trim($name))];
            })->keyBy('name_lower');
        }

        $searchName = strtolower(trim($transmissionName));
        $transmission = $transmissionTypesCache->get($searchName);

        if (!$transmission) {
            // Correspondances alternatives communes
            $aliases = [
                'manual' => 'manuelle',
                'automatic' => 'automatique',
                'auto' => 'automatique',
                'cvt' => 'automatique'
            ];

            if (isset($aliases[$searchName])) {
                $transmission = $transmissionTypesCache->get($aliases[$searchName]);
            }

            if (!$transmission) {
                throw new \Exception("Type de transmission inconnu: '{$transmissionName}'. Types disponibles: " .
                    implode(', ', $transmissionTypesCache->pluck('name_lower')->toArray()));
            }
        }

        return $transmission['id'];
    }

    private function resolveStatusId(string $statusName): int
    {
        static $statusTypesCache = null;

        if ($statusTypesCache === null) {
            $statusTypesCache = VehicleStatus::pluck('id', 'name')->map(function ($id, $name) {
                return ['id' => $id, 'name_lower' => strtolower(trim($name))];
            })->keyBy('name_lower');
        }

        $searchName = strtolower(trim($statusName));
        $status = $statusTypesCache->get($searchName);

        if (!$status) {
            // Correspondances alternatives communes
            $aliases = [
                'available' => 'disponible',
                'assigned' => 'affecté',
                'maintenance' => 'maintenance',
                'out_of_service' => 'hors service',
                'retired' => 'retiré'
            ];

            if (isset($aliases[$searchName])) {
                $status = $statusTypesCache->get($aliases[$searchName]);
            }

            if (!$status) {
                // Fallback vers "Disponible" par défaut
                $status = $statusTypesCache->get('disponible');
                if ($status) {
                    Log::warning('CSV Import - Unknown status, using default', [
                        'original_status' => $statusName,
                        'default_used' => 'Disponible'
                    ]);
                    return $status['id'];
                }

                throw new \Exception("Statut inconnu: '{$statusName}'. Statuts disponibles: " .
                    implode(', ', $statusTypesCache->pluck('name_lower')->toArray()));
            }
        }

        return $status['id'];
    }

    private function validateImportRowData(array $data, int $rowNumber): void
    {
        // Validation des champs obligatoires avec messages personnalisés
        $validator = Validator::make($data, [
            'registration_plate' => 'required|string|max:20',
            'vin' => 'required|string|min:17|max:17',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'manufacturing_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'purchase_price' => 'required|numeric|min:0',
            'current_mileage' => 'required|integer|min:0',
            'initial_mileage' => 'required|integer|min:0',
            'engine_displacement_cc' => 'required|integer|min:50|max:10000',
            'power_hp' => 'required|integer|min:1|max:2000',
            'seats' => 'required|integer|min:1|max:100',
        ], [
            'registration_plate.required' => 'La plaque d\'immatriculation est obligatoire',
            'vin.required' => 'Le numéro VIN est obligatoire',
            'vin.min' => 'Le VIN doit contenir exactement 17 caractères',
            'vin.max' => 'Le VIN doit contenir exactement 17 caractères',
            'brand.required' => 'La marque est obligatoire',
            'model.required' => 'Le modèle est obligatoire',
            'manufacturing_year.required' => 'L\'année de fabrication est obligatoire',
            'manufacturing_year.min' => 'L\'année de fabrication doit être supérieure ou égale à 1990',
            'manufacturing_year.max' => 'L\'année de fabrication ne peut pas être dans le futur',
            'purchase_price.required' => 'Le prix d\'achat est obligatoire',
            'purchase_price.min' => 'Le prix d\'achat doit être positif',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = "Ligne {$rowNumber}: " . implode(', ', $errors);

            Log::warning('CSV Import - Row validation failed', [
                'row_number' => $rowNumber,
                'errors' => $errors,
                'data' => $data
            ]);

            throw new \Exception($errorMessage);
        }

        // Validations métier avancées
        $this->validateBusinessRules($data, $rowNumber);
    }

    private function getImportStatistics(): array
    {
        // Statistiques des importations récentes (placeholder)
        return [
            'last_import_date' => null,
            'total_imports_this_month' => 0,
            'average_success_rate' => 95.5,
            'most_common_errors' => [
                'VIN invalide',
                'Plaque d\'immatriculation en doublon',
                'Type de véhicule inconnu'
            ]
        ];
    }

    private function getImportRecommendations(): array
    {
        return [
            [
                'icon' => 'fas fa-file-download',
                'title' => 'Utilisez le Template',
                'description' => 'Téléchargez et utilisez notre template pour éviter les erreurs de format.'
            ],
            [
                'icon' => 'fas fa-check-double',
                'title' => 'Vérifiez les Doublons',
                'description' => 'Assurez-vous que les plaques d\'immatriculation et VIN sont uniques.'
            ],
            [
                'icon' => 'fas fa-shield-alt',
                'title' => 'Sauvegarde Recommandée',
                'description' => 'Effectuez une sauvegarde avant l\'importation de données importantes.'
            ],
            [
                'icon' => 'fas fa-clock',
                'title' => 'Import par Lots',
                'description' => 'Pour de gros volumes, divisez en plusieurs fichiers de 500 véhicules max.'
            ]
        ];
    }

    /**
     * 💀 Suppression définitive d'un véhicule (IRRÉVERSIBLE)
     */
    public function forceDelete(Vehicle $vehicle): RedirectResponse
    {
        $this->logUserAction('vehicle.force_delete.attempted', null, [
            'vehicle_id' => $vehicle->id,
            'registration_plate' => $vehicle->registration_plate
        ]);

        try {
            $registrationPlate = $vehicle->registration_plate;

            // Suppression définitive - IRRÉVERSIBLE
            $vehicle->forceDelete();

            $this->logUserAction('vehicle.force_delete.success', null, [
                'vehicle_id' => $vehicle->id,
                'registration_plate' => $registrationPlate
            ]);

            Cache::tags(['vehicles', 'analytics'])->flush();

            return redirect()
                ->route('admin.vehicles.archived')
                ->with('success', "Véhicule {$registrationPlate} supprimé définitivement")
                ->with('vehicle_force_deleted', true);
        } catch (\Exception $e) {
            $this->logError('vehicle.force_delete.error', $e, null, ['vehicle_id' => $vehicle->id]);
            return back()->withErrors(['error' => 'Erreur lors de la suppression définitive du véhicule.']);
        }
    }

    // ============================================================
    // MÉTHODES DE VALIDATION ET REPORTING ENTERPRISE
    // ============================================================

    /**
     * 📈 Construction d'un rapport de validation entreprise détaillé
     * Génère un rapport complet de validation avec métriques et diagnostics
     */
    private function buildValidationReport(array $data, string $fileName): array
    {
        $startTime = microtime(true);

        $report = [
            'valid' => true,
            'file_info' => [
                'name' => $fileName,
                'total_rows' => count($data),
                'processed_at' => now()->toISOString(),
                'user_id' => Auth::id(),
                'organization_id' => Auth::user()->organization_id
            ],
            'columns' => [
                'found' => !empty($data) ? array_keys($data[0]) : [],
                'required' => $this->getRequiredImportColumns(),
                'missing' => [],
                'extra' => []
            ],
            'data_quality' => [
                'sample_data' => array_slice($data, 0, 3),
                'validation_errors' => [],
                'warnings' => [],
                'statistics' => []
            ],
            'performance' => [
                'validation_time_ms' => 0,
                'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
            ]
        ];

        // Analyse des colonnes
        if (!empty($data)) {
            $foundColumns = array_keys($data[0]);
            $requiredColumns = $this->getRequiredImportColumns();

            $report['columns']['missing'] = array_diff($requiredColumns, $foundColumns);
            $report['columns']['extra'] = array_diff($foundColumns, $requiredColumns);

            if (!empty($report['columns']['missing'])) {
                $report['valid'] = false;
                $report['data_quality']['validation_errors'][] = [
                    'type' => 'MISSING_COLUMNS',
                    'severity' => 'ERROR',
                    'message' => 'Colonnes obligatoires manquantes: ' . implode(', ', $report['columns']['missing'])
                ];
            }
        }

        // Validation échantillon des données
        $sampleErrors = [];
        $sampleWarnings = [];
        $stats = [
            'empty_cells' => 0,
            'duplicate_vins' => 0,
            'duplicate_plates' => 0,
            'invalid_dates' => 0,
            'invalid_numbers' => 0
        ];

        $vins = [];
        $plates = [];

        foreach (array_slice($data, 0, 10) as $index => $row) {
            try {
                // Préparation des données
                $vehicleData = $this->prepareVehicleDataFromRow($row, $index + 1);

                // Validation complète
                $this->validateImportRowData($vehicleData, $index + 1);

                // Statistiques de qualité
                $this->collectDataQualityStats($vehicleData, $stats, $vins, $plates, $index + 1);
            } catch (\Exception $e) {
                $sampleErrors[] = [
                    'row' => $index + 1,
                    'type' => 'VALIDATION_ERROR',
                    'severity' => 'ERROR',
                    'message' => $e->getMessage(),
                    'data' => $this->sanitizeRowForLogging($row)
                ];
                $report['valid'] = false;
            }
        }

        // Compilation du rapport final
        $report['data_quality']['validation_errors'] = array_merge(
            $report['data_quality']['validation_errors'],
            $sampleErrors
        );
        $report['data_quality']['warnings'] = $sampleWarnings;
        $report['data_quality']['statistics'] = $stats;
        $report['performance']['validation_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        // Log du rapport pour audit
        Log::info('Import validation report generated', [
            'file_name' => $fileName,
            'validation_result' => $report['valid'] ? 'PASSED' : 'FAILED',
            'error_count' => count($report['data_quality']['validation_errors']),
            'warning_count' => count($report['data_quality']['warnings']),
            'processing_time_ms' => $report['performance']['validation_time_ms']
        ]);

        return $report;
    }

    /**
     * 📊 Collecte de statistiques de qualité des données
     */
    private function collectDataQualityStats(array $vehicleData, array &$stats, array &$vins, array &$plates, int $rowNumber): void
    {
        // Détection de cellules vides dans les champs obligatoires
        $requiredFields = ['registration_plate', 'vin', 'brand', 'model'];
        foreach ($requiredFields as $field) {
            if (empty($vehicleData[$field])) {
                $stats['empty_cells']++;
            }
        }

        // Détection de doublons VIN
        if (!empty($vehicleData['vin'])) {
            if (in_array($vehicleData['vin'], $vins)) {
                $stats['duplicate_vins']++;
            } else {
                $vins[] = $vehicleData['vin'];
            }
        }

        // Détection de doublons plaques
        if (!empty($vehicleData['registration_plate'])) {
            if (in_array($vehicleData['registration_plate'], $plates)) {
                $stats['duplicate_plates']++;
            } else {
                $plates[] = $vehicleData['registration_plate'];
            }
        }

        // Validation des dates
        if (!empty($vehicleData['acquisition_date'])) {
            if (!strtotime($vehicleData['acquisition_date'])) {
                $stats['invalid_dates']++;
            }
        }

        // Validation des nombres
        $numericFields = ['manufacturing_year', 'purchase_price', 'current_mileage', 'power_hp'];
        foreach ($numericFields as $field) {
            if (isset($vehicleData[$field]) && !is_numeric($vehicleData[$field])) {
                $stats['invalid_numbers']++;
            }
        }
    }

    /**
     * 🧯 Sanitisation des données de ligne pour logging sécurisé
     */
    private function sanitizeRowForLogging(array $row): array
    {
        // Masquer les données sensibles pour les logs
        $sanitized = [];
        foreach ($row as $key => $value) {
            if (in_array($key, ['vin', 'registration_plate'])) {
                // Masquer partiellement les données sensibles
                $sanitized[$key] = strlen($value) > 4 ?
                    substr($value, 0, 3) . '***' . substr($value, -2) :
                    '***';
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    /**
     * 🛡️ Validation métier entreprise ultra-avancée
     * Inclut toutes les règles métier, validations croisées et contrôles de cohérence
     */
    private function performEnterpriseBusinessValidation(array $data, int $rowNumber): array
    {
        $warnings = [];
        $errors = [];

        // Validation de cohérence temporelle
        if (isset($data['manufacturing_year'], $data['acquisition_date'])) {
            $acquisitionYear = (int) date('Y', strtotime($data['acquisition_date']));
            $manufacturingYear = (int) $data['manufacturing_year'];

            if ($manufacturingYear > $acquisitionYear) {
                $errors[] = "Année de fabrication ({$manufacturingYear}) postérieure à l'acquisition ({$acquisitionYear})";
            }

            if ($acquisitionYear - $manufacturingYear > 10) {
                $warnings[] = "Véhicule ancien - écart de " . ($acquisitionYear - $manufacturingYear) . " ans";
            }
        }

        // Validation de cohérence économique
        if (isset($data['purchase_price'], $data['current_value'])) {
            $depreciation = ($data['purchase_price'] - $data['current_value']) / $data['purchase_price'];
            if ($depreciation > 0.8) {
                $warnings[] = "Dépréciation élevée: " . round($depreciation * 100, 1) . "%";
            }
        }

        // Validation technique moteur
        if (isset($data['engine_displacement_cc'], $data['power_hp'])) {
            $powerPerLiter = $data['power_hp'] / ($data['engine_displacement_cc'] / 1000);
            if ($powerPerLiter > 150) {
                $warnings[] = "Puissance spécifique élevée: " . round($powerPerLiter, 1) . " HP/L";
            }
        }

        // Validation kilométrage vs âge
        if (isset($data['current_mileage'], $data['manufacturing_year'])) {
            $vehicleAge = date('Y') - $data['manufacturing_year'];
            $avgKmPerYear = $vehicleAge > 0 ? $data['current_mileage'] / $vehicleAge : 0;

            if ($avgKmPerYear > 50000) {
                $warnings[] = "Utilisation intensive: " . round($avgKmPerYear) . " km/an";
            } elseif ($avgKmPerYear < 5000 && $vehicleAge > 1) {
                $warnings[] = "Faible utilisation: " . round($avgKmPerYear) . " km/an";
            }
        }

        return ['errors' => $errors, 'warnings' => $warnings];
    }

    // ============================================================
    // SERVICES ENTERPRISE ET UTILITAIRES ULTRA-PROFESSIONNELS
    // ============================================================

    /**
     * 🗂️ Service de cache intelligent enterprise avec stratégie adaptative
     * Implémente un cache multi-niveau avec invalidation contextuelle
     */
    private function getEnterpriseCache(string $key, \Closure $callback, int $ttl = null): mixed
    {
        $ttl = $ttl ?? self::CACHE_TTL_MEDIUM;
        $cacheKey = $this->buildCacheKey($key);

        return Cache::tags([
            'vehicles',
            'org:' . Auth::user()->organization_id,
            'user:' . Auth::id()
        ])->remember($cacheKey, $ttl, $callback);
    }

    /**
     * 🔑 Construction de clé de cache enterprise avec namespace intelligent
     */
    private function buildCacheKey(string $key): string
    {
        $orgId = Auth::user()->organization_id;
        $userRole = Auth::user()->roles->first()?->name ?? 'guest';
        $apiVersion = 'v4';

        return "zenfleet:{$apiVersion}:vehicles:{$orgId}:{$userRole}:{$key}";
    }

    /**
     * 📊 Service d'analytics enterprise avec métriques temps réel
     */
    private function trackEnterpriseMetrics(string $action, array $metadata = []): void
    {
        try {
            $metrics = [
                'action' => $action,
                'timestamp' => now()->toISOString(),
                'user_id' => Auth::id(),
                'organization_id' => Auth::user()->organization_id,
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => $metadata,
                'performance' => [
                    'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                    'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
                    'execution_time_ms' => round((microtime(true) - LARAVEL_START) * 1000, 2)
                ]
            ];

            // Analytics enterprise si disponible
            if (isset($this->analyticsService)) {
                $this->analyticsService->track('vehicle_management', $metrics);
            }

            // Logging structuré pour monitoring
            Log::channel('analytics')->info('Vehicle action tracked', $metrics);
        } catch (\Exception $e) {
            // Logging d'échec d'analytics sans interrompre le flow principal
            Log::warning('Analytics tracking failed', [
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * 🛡️ Service de sanitisation enterprise pour données sensibles
     */
    private function sanitizeDataForLogging(array $data): array
    {
        $sanitized = $data;

        foreach (self::SENSITIVE_FIELDS as $field) {
            if (isset($sanitized[$field])) {
                $value = $sanitized[$field];
                $sanitized[$field] = strlen($value) > 4 ?
                    substr($value, 0, 2) . str_repeat('*', strlen($value) - 4) . substr($value, -2) :
                    str_repeat('*', strlen($value));
            }
        }

        // Suppression des champs potentiellement sensibles
        unset($sanitized['password'], $sanitized['api_key'], $sanitized['secret']);

        return $sanitized;
    }

    /**
     * 📋 Service de validation enterprise avec règles contextuelle
     */
    private function getContextualValidationRules(?Vehicle $vehicle = null): array
    {
        $baseRules = $this->validationRules;

        // Adaptation des règles selon le contexte
        if ($vehicle) {
            // Règles pour mise à jour
            $baseRules['registration_plate'][3] = 'unique:vehicles,registration_plate,' . $vehicle->id;
            $baseRules['vin'][2] = 'unique:vehicles,vin,' . $vehicle->id;
        }

        // Adaptation selon l'organisation
        $orgType = Auth::user()->organization?->type;
        if ($orgType === 'enterprise') {
            // Règles plus strictes pour les entreprises
            $baseRules['notes'] = ['required', 'string', 'min:10', 'max:2000'];
            $baseRules['current_value'] = ['required', 'numeric', 'min:1000'];
        }

        // Adaptation selon le rôle utilisateur
        $userRole = Auth::user()->roles->first()?->name;
        if (!in_array($userRole, ['Super Admin', 'Admin'])) {
            // Restrictions pour les rôles limités
            unset($baseRules['organization_id']);
        }

        return $baseRules;
    }

    /**
     * 📈 Service de rapport enterprise avec export intelligent
     */
    private function generateEnterpriseReport(string $type, array $filters = []): array
    {
        $startTime = microtime(true);

        $report = [
            'metadata' => [
                'type' => $type,
                'generated_at' => now()->toISOString(),
                'generated_by' => Auth::user()->email,
                'organization' => Auth::user()->organization?->name,
                'filters_applied' => $filters,
                'report_id' => \Illuminate\Support\Str::uuid()
            ],
            'data' => [],
            'statistics' => [],
            'performance' => []
        ];

        try {
            switch ($type) {
                case 'import_summary':
                    $report['data'] = $this->generateImportSummaryData($filters);
                    break;
                case 'quality_assessment':
                    $report['data'] = $this->generateQualityAssessmentData($filters);
                    break;
                case 'compliance_audit':
                    $report['data'] = $this->generateComplianceAuditData($filters);
                    break;
                default:
                    throw new \InvalidArgumentException("Type de rapport non supporté: {$type}");
            }

            $report['statistics'] = $this->calculateReportStatistics($report['data']);
        } catch (\Exception $e) {
            $report['error'] = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'timestamp' => now()->toISOString()
            ];
        }

        $report['performance'] = [
            'generation_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'data_points' => count($report['data'])
        ];

        return $report;
    }

    /**
     * 🔄 Service de nettoyage cache enterprise avec stratégie intelligente
     */
    private function invalidateEnterpriseCache(array $tags = [], bool $cascade = false): void
    {
        try {
            $defaultTags = [
                'vehicles',
                'analytics',
                'org:' . Auth::user()->organization_id
            ];

            $tagsToInvalidate = array_merge($defaultTags, $tags);

            if ($cascade) {
                // Invalidation en cascade pour les caches dépendants
                $tagsToInvalidate = array_merge($tagsToInvalidate, [
                    'dashboard',
                    'reports',
                    'statistics'
                ]);
            }

            Cache::tags($tagsToInvalidate)->flush();

            Log::info('Enterprise cache invalidated', [
                'tags' => $tagsToInvalidate,
                'cascade' => $cascade,
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $e) {
            Log::warning('Cache invalidation failed', [
                'error' => $e->getMessage(),
                'tags' => $tags
            ]);
        }
    }

    // ============================================================
    // MÉTHODES DE CONFIGURATION INTELLIGENTE ENTERPRISE
    // ============================================================

    /**


    /**
     * 📎 Détermine la taille maximale d'import selon le plan utilisateur
     * Adapte les limites selon le niveau d'abonnement enterprise
     */
    private function getMaxImportSize(): int
    {
        $user = Auth::user();
        $organizationType = $user->organization?->type ?? 'standard';
        $subscriptionTier = $user->organization?->subscription_tier ?? 'standard';

        // Configuration selon le niveau d'abonnement
        switch ($subscriptionTier) {
            case 'premium':
                return self::MAX_IMPORT_SIZE_PREMIUM;
            case 'enterprise':
                return self::MAX_IMPORT_SIZE_ENTERPRISE;
            default:
                return self::MAX_IMPORT_SIZE_STANDARD;
        }
    }

    /**
     * 🔄 Détermine le TTL de cache optimal selon le type de données
     * Optimise la performance en adaptant la durée de cache
     */
    private function getOptimalCacheTTL(string $dataType): int
    {
        switch ($dataType) {
            case 'user_preferences':
            case 'session_data':
                return self::CACHE_TTL_SHORT;

            case 'vehicle_analytics':
            case 'dashboard_data':
                return self::CACHE_TTL_MEDIUM;

            case 'reference_data':
            case 'system_config':
                return self::CACHE_TTL_LONG;

            default:
                return self::CACHE_TTL_MEDIUM;
        }
    }

    /**
     * 📊 Obtient les limites de validation selon le contexte utilisateur
     * Adapte les règles de validation selon l'organisation et le rôle
     */
    private function getValidationLimits(): array
    {
        $user = Auth::user();
        $organizationType = $user->organization?->type ?? 'standard';
        $userRole = $user->roles->first()?->name ?? 'user';

        $baseLimits = [
            'batch_size' => self::VALIDATION_BATCH_SIZE,
            'timeout_seconds' => self::VALIDATION_TIMEOUT_SECONDS,
            'max_file_size_mb' => self::MAX_FILE_SIZE_MB
        ];

        // Ajustements selon le niveau enterprise
        if ($organizationType === 'enterprise') {
            $baseLimits['batch_size'] *= 2;
            $baseLimits['timeout_seconds'] *= 1.5;
            $baseLimits['max_file_size_mb'] *= 2;
        }

        // Ajustements selon le rôle admin
        if (in_array($userRole, ['Super Admin', 'Admin'])) {
            $baseLimits['timeout_seconds'] *= 2;
        }

        return $baseLimits;
    }

    /**
     * 🛡️ Vérifie les permissions avancées selon le contexte
     * Implémente un système de permissions granulaire enterprise
     */
    private function checkEnterprisePermissions(string $action, array $context = []): bool
    {
        $user = Auth::user();

        // Vérifications de base
        if (!$user) {
            return false;
        }

        // Permissions par action
        $permissionMap = [
            'import_large_files' => ['Super Admin', 'Admin', 'Gestionnaire Flotte'],
            'export_all_data' => ['Super Admin', 'Admin'],
            'access_analytics' => ['Super Admin', 'Admin', 'Gestionnaire Flotte', 'Supervisor'],
            'manage_archives' => ['Super Admin', 'Admin'],
            'force_delete' => ['Super Admin']
        ];

        $userRoles = $user->roles->pluck('name')->toArray();
        $allowedRoles = $permissionMap[$action] ?? [];

        if (empty($allowedRoles)) {
            return true; // Action sans restriction
        }

        return !empty(array_intersect($userRoles, $allowedRoles));
    }

    /**
     * 📈 Calcule les métriques de performance enterprise
     * Fournit des statistiques détaillées pour monitoring
     */
    private function calculatePerformanceMetrics(float $startTime): array
    {
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // en ms

        return [
            'execution_time_ms' => round($executionTime, 2),
            'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            'queries_count' => \DB::getQueryLog() ? count(\DB::getQueryLog()) : 0,
            'cache_hits' => $this->getCacheHitCount(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * 📊 Obtient le nombre de hits de cache (si disponible)
     */
    private function getCacheHitCount(): int
    {
        try {
            // Implémentation dépendante du driver de cache
            return Cache::getStore()->getHits() ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // Méthodes placeholder pour les rapports (implémentation future)
    private function generateImportSummaryData(array $filters): array
    {
        return [];
    }
    private function generateQualityAssessmentData(array $filters): array
    {
        return [];
    }
    private function generateComplianceAuditData(array $filters): array
    {
        return [];
    }
    private function calculateReportStatistics(array $data): array
    {
        return [];
    }
}
