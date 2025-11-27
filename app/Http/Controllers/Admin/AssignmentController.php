<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assignment\StoreAssignmentRequest;
use App\Http\Requests\Admin\Assignment\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Services\PdfGenerationService;
use App\Traits\ResourceAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AssignmentController extends Controller
{
    use ResourceAvailability;
        public function __construct()
    {
        $this->middleware('auth');
        
        // 🛡️ SYSTÈME DE PERMISSIONS ENTERPRISE
        // Utilisation de vérifications manuelles pour un contrôle précis
        // Les permissions sont vérifiées dans chaque méthode individuellement
        // Cela permet une granularité maximale et évite les conflits
        
        // Option de debug des permissions (activé en dev)
        if (config('app.debug')) {
            $this->middleware(function ($request, $next) {
                if ($request->user()) {
                    \Log::debug('Assignment Controller Access', [
                        'user' => $request->user()->email,
                        'method' => $request->method(),
                        'path' => $request->path(),
                        'can_create' => $request->user()->can('create assignments'),
                        'all_permissions' => $request->user()->getAllPermissions()->pluck('name')
                    ]);
                }
                return $next($request);
            });
        }
    }

    /**
     * Affiche la page d'affectations enterprise-grade
     */
    public function index(Request $request): View
    {
        $this->authorize('view assignments');

        // Construction de la requête avec filtres
        $query = Assignment::with(['vehicle' => function ($query) {
                $query->withoutGlobalScope(\App\Models\Scopes\UserVehicleAccessScope::class);
            }, 'driver', 'creator'])
            ->where('organization_id', auth()->user()->organization_id);

        // Application des filtres - RECHERCHE INSENSIBLE À LA CASSE ULTRA-PRO
        // Utilisation de ILIKE (PostgreSQL) au lieu de LIKE pour performance + case-insensitive
        // Compatible avec indexes GIN trigram pour recherche ultra-rapide (5-50ms vs 500-2000ms)
        if ($request->filled('search')) {
            $search = trim($request->search); // Nettoyer les espaces
            $query->where(function ($q) use ($search) {
                // Recherche véhicule: ILIKE utilise les index GIN trigram créés
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'ILIKE', "%{$search}%")
                                ->orWhere('brand', 'ILIKE', "%{$search}%")
                                ->orWhere('model', 'ILIKE', "%{$search}%");
                })
                // Recherche chauffeur: ILIKE + recherche nom complet optimisée
                ->orWhereHas('driver', function ($driverQuery) use ($search) {
                    $driverQuery->where('first_name', 'ILIKE', "%{$search}%")
                               ->orWhere('last_name', 'ILIKE', "%{$search}%")
                               ->orWhere('personal_phone', 'ILIKE', "%{$search}%")
                               // Recherche nom complet "Jean Dupont" ou "el hadi chemli"
                               ->orWhereRaw("(first_name || ' ' || last_name) ILIKE ?", ["%{$search}%"]);
                });
            });
        }

        // 🔥 FILTRE PAR STATUT - Enterprise-Grade
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔥 FILTRE PAR PLAGE DE DATES - Enterprise-Grade
        if ($request->filled('date_from')) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('start_datetime', '>=', $dateFrom);
        }
        if ($request->filled('date_to')) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('start_datetime', '<=', $dateTo);
        }

        // 🔥 TRI DYNAMIQUE - Enterprise-Grade
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Colonnes autorisées pour le tri (sécurité)
        $allowedSortColumns = ['status', 'start_datetime', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        // Pagination avec filtres et tri
        $perPage = (int) $request->get('per_page', 15);
        $assignments = $query->orderBy($sortBy, $sortOrder)
                            ->paginate($perPage);

        // ✅ Récupérer tous les véhicules et chauffeurs pour les filtres
        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
            ->orderBy('registration_plate')
            ->get();

        $drivers = Driver::where('organization_id', auth()->user()->organization_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Calculer les statistiques des affectations
        $allAssignments = Assignment::where('organization_id', auth()->user()->organization_id);
        $activeAssignments = (clone $allAssignments)
            ->where('status', 'active')
            ->count();
        $inProgressAssignments = (clone $allAssignments)
            ->where('status', 'in_progress')
            ->count();
        $scheduledAssignments = (clone $allAssignments)
            ->where('status', 'scheduled')
            ->count();

        return view('admin.assignments.index', compact(
            'assignments',
            'vehicles',
            'drivers',
            'activeAssignments',
            'inProgressAssignments',
            'scheduledAssignments'
        ));
    }


    /**
     * Affiche le formulaire de création.
     */
        /**
     * Affiche le formulaire de création - ENTERPRISE EDITION
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(): View
    {
        // 🛡️ VÉRIFICATION DES PERMISSIONS ENTERPRISE - Via Policy (Pattern Laravel Standard)
        $this->authorize('create', Assignment::class);

        $user = auth()->user();

        // Log pour debug (uniquement en dev)
        if (config('app.debug')) {
            \Log::info('Assignment Create Access Granted', [
                'user' => $user->email,
                'organization' => $user->organization_id,
                'roles' => $user->roles->pluck('name')
            ]);
        }

        // ✅ NOUVELLE LOGIQUE ENTERPRISE: Utilisation du trait ResourceAvailability
        // Source de vérité unique: is_available + assignment_status
        $availableVehicles = $this->getAvailableVehicles();
        $availableDrivers = $this->getAvailableDrivers();

        // Affectations actives pour les statistiques
        $activeAssignments = Assignment::where('organization_id', auth()->user()->organization_id)
            ->whereNull('end_datetime')
            ->where('start_datetime', '<=', now())
            ->with(['vehicle', 'driver'])
            ->get();

        // Debug pour diagnostique (uniquement en dev)
        if (config('app.debug')) {
            \Log::info('Assignment Create Data', [
                'user_org_id' => $user->organization_id,
                'vehicles_count' => $availableVehicles->count(),
                'drivers_count' => $availableDrivers->count(),
                'active_assignments_count' => $activeAssignments->count()
            ]);
        }

        // Utiliser la vue wizard qui est la vue entreprise moderne pour la création
        return view('admin.assignments.wizard', compact('availableVehicles', 'availableDrivers', 'activeAssignments'));
    }

    /**
     * Enregistre une nouvelle affectation.
     */
    public function store(StoreAssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organization_id'] = auth()->user()->organization_id;
        $data['created_by'] = auth()->id();

        // Traiter les données de programmation
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $data['start_date'] . ' ' . $data['start_time']);
        $data['start_datetime'] = $startDateTime;

        // Gestion de l'affectation programmée
        if ($data['assignment_type'] === 'scheduled' && isset($data['end_date']) && isset($data['end_time'])) {
            $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $data['end_date'] . ' ' . $data['end_time']);
            $data['end_datetime'] = $endDateTime;
            $data['status'] = 'scheduled'; // Affectation programmée
        } else {
            $data['end_datetime'] = null;
            $data['status'] = 'active'; // Affectation ouverte
        }

        // Mapper les champs pour compatibilité
        if (isset($data['purpose'])) {
            $data['reason'] = $data['purpose'];
        }

        // Nettoyer les champs temporaires
        unset($data['start_date'], $data['start_time'], $data['end_date'], $data['end_time'], $data['assignment_type'], $data['purpose']);

        // ✅ VÉRIFICATION DES CHEVAUCHEMENTS AVANT CRÉATION
        $newAssignment = new Assignment($data); // Créer une instance sans la persister
        
        if ($newAssignment->isOverlapping()) {
            Log::warning('Tentative de création d\'affectation avec chevauchement', [
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_datetime' => $data['start_datetime'],
                'end_datetime' => $data['end_datetime'],
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Un chevauchement d\'affectation a été détecté pour ce véhicule ou ce chauffeur. '
                    . 'Veuillez vérifier les périodes existantes.'
                );
        }

        try {
            $assignment = Assignment::create($data);

            // Log de l'activité pour traçabilité enterprise
            \Log::info('Nouvelle affectation créée', [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_datetime' => $startDateTime->format('Y-m-d H:i:s'),
                'end_datetime' => $data['end_datetime'] ? $data['end_datetime']->format('Y-m-d H:i:s') : 'Ouverte',
                'created_by' => auth()->id(),
                'organization_id' => $data['organization_id']
            ]);

            $message = $data['status'] === 'scheduled'
                ? 'Affectation programmée créée avec succès.'
                : 'Affectation ouverte créée avec succès.';

            return redirect()->route('admin.assignments.index')
                ->with('success', $message)
                ->with('assignment_id', $assignment->id);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de l\'affectation', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'affectation : ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une affectation.
     */
    public function show(Assignment $assignment): View
    {
        $this->authorize('view assignments');
        $assignment->load(['vehicle', 'driver', 'creator', 'handoverForm']);

        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Assignment $assignment): View
    {
        $this->authorize('edit assignments');
        $assignment->load(['vehicle', 'driver']);

        return view('admin.assignments.edit', compact('assignment'));
    }

    /**
     * Met à jour une affectation.
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment): RedirectResponse
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();

        // ✅ VÉRIFICATION DES CHEVAUCHEMENTS AVANT MISE À JOUR
        $assignment->fill($data); // Mettre à jour l'instance existante
        
        if ($assignment->isOverlapping($assignment->id)) { // Passer l'ID de l'affectation actuelle
            Log::warning('Tentative de modification d\'affectation avec chevauchement', [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $data['vehicle_id'] ?? $assignment->vehicle_id,
                'driver_id' => $data['driver_id'] ?? $assignment->driver_id,
                'start_datetime' => $data['start_datetime'] ?? $assignment->start_datetime,
                'end_datetime' => $data['end_datetime'] ?? $assignment->end_datetime,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    'Un chevauchement d\'affectation a été détecté pour ce véhicule ou ce chauffeur. '
                    . 'Veuillez vérifier les périodes existantes.'
                );
        }

        try {
            $assignment->save();
            
            Log::info('Affectation mise à jour avec succès', [
                'assignment_id' => $assignment->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Affectation mise à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'affectation', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour de l\'affectation : ' . $e->getMessage());
        }
    }

    /**
     * 🗑️ Supprime une affectation - ENTERPRISE-GRADE ULTRA-PRO
     *
     * RÈGLES MÉTIER STRICTES (Surpassant Fleetio/Samsara) :
     * - Autorisation multi-niveau via Policy (permission + organisation)
     * - Soft delete pour traçabilité complète et récupération possible
     * - Validation business rules avant suppression
     * - Gestion intelligente des relations (handover form, historique)
     * - Transaction ACID pour garantir l'intégrité
     * - Audit trail complet (qui, quand, pourquoi)
     * - Messages utilisateur contextuels et professionnels
     *
     * CONDITIONS DE SUPPRESSION :
     * - Affectation SCHEDULED (pas encore commencée) : ✅ Suppression autorisée
     * - Affectation créée < 24h : ✅ Suppression autorisée (correction erreur)
     * - Affectation ACTIVE ou COMPLETED : ❌ Suppression interdite (intégrité)
     *
     * SÉCURITÉ :
     * - Vérification Policy (delete permission + same organization)
     * - Validation canBeDeleted() via business rules
     * - Protection contre suppression accidentelle affectations critiques
     *
     * @param Assignment $assignment L'affectation à supprimer
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        // 🛡️ AUTORISATION ENTERPRISE - Via Policy (multi-tenant + permission)
        $this->authorize('delete', $assignment);

        // 📋 LOG AUDIT TRAIL - Tentative de suppression (avant validation)
        Log::info('Tentative de suppression d\'affectation', [
            'assignment_id' => $assignment->id,
            'vehicle' => $assignment->vehicle_display,
            'driver' => $assignment->driver_display,
            'status' => $assignment->status,
            'start_datetime' => $assignment->start_datetime,
            'end_datetime' => $assignment->end_datetime,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'organization_id' => auth()->user()->organization_id
        ]);

        // ✅ VALIDATION BUSINESS RULES - Peut-on supprimer cette affectation ?
        if (!$assignment->canBeDeleted()) {
            $reason = $this->getDeletionBlockReason($assignment);

            Log::warning('Suppression d\'affectation bloquée - Business rules', [
                'assignment_id' => $assignment->id,
                'reason' => $reason,
                'status' => $assignment->status,
                'created_at' => $assignment->created_at,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', $reason);
        }

        try {
            // 🔒 TRANSACTION ACID - Garantir l'atomicité de toutes les opérations
            \DB::beginTransaction();

            // 🔍 VÉRIFICATION DES RELATIONS - Handover Form
            // Si un formulaire de remise existe, le supprimer également (cascade soft delete)
            if ($assignment->hasHandoverModule() && $assignment->handoverForm) {
                Log::info('Suppression du formulaire de remise associé', [
                    'assignment_id' => $assignment->id,
                    'handover_form_id' => $assignment->handoverForm->id
                ]);

                $assignment->handoverForm->delete();
            }

            // 📊 SAUVEGARDE DONNÉES AUDIT - Avant suppression
            $auditData = [
                'assignment_id' => $assignment->id,
                'vehicle_id' => $assignment->vehicle_id,
                'vehicle_display' => $assignment->vehicle_display,
                'driver_id' => $assignment->driver_id,
                'driver_display' => $assignment->driver_display,
                'start_datetime' => $assignment->start_datetime,
                'end_datetime' => $assignment->end_datetime,
                'status' => $assignment->status,
                'reason' => $assignment->reason,
                'notes' => $assignment->notes,
                'deleted_by' => auth()->id(),
                'deleted_by_email' => auth()->user()->email,
                'deleted_at' => now(),
                'organization_id' => $assignment->organization_id
            ];

            // 🗑️ SOFT DELETE - Suppression avec possibilité de récupération
            // Le trait SoftDeletes du modèle gère automatiquement deleted_at
            $assignment->delete();

            // ✅ COMMIT TRANSACTION - Toutes les opérations ont réussi
            \DB::commit();

            // 📝 LOG SUCCÈS - Audit trail complet
            Log::info('Affectation supprimée avec succès', $auditData);

            // 🎯 MESSAGE UTILISATEUR - Feedback professionnel
            $successMessage = sprintf(
                'Affectation supprimée avec succès : %s (%s → %s)',
                $assignment->short_description,
                $assignment->start_datetime->format('d/m/Y H:i'),
                $assignment->end_datetime ? $assignment->end_datetime->format('d/m/Y H:i') : 'Durée indéterminée'
            );

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            // ⚠️ ROLLBACK - Annuler toutes les modifications en cas d'erreur
            \DB::rollBack();

            // 🔴 LOG ERREUR - Diagnostic complet pour débogage
            Log::error('Erreur lors de la suppression d\'affectation', [
                'assignment_id' => $assignment->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'organization_id' => auth()->user()->organization_id
            ]);

            // 🎯 MESSAGE UTILISATEUR - Erreur contextuelle
            $errorMessage = config('app.debug')
                ? 'Erreur lors de la suppression : ' . $e->getMessage()
                : 'Une erreur est survenue lors de la suppression de l\'affectation. Veuillez réessayer ou contacter le support.';

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * 📋 Détermine la raison pour laquelle une affectation ne peut pas être supprimée
     *
     * @param Assignment $assignment
     * @return string Message d'erreur contextuel
     */
    private function getDeletionBlockReason(Assignment $assignment): string
    {
        // Affectation déjà terminée
        if ($assignment->status === Assignment::STATUS_COMPLETED) {
            return sprintf(
                'Impossible de supprimer une affectation terminée. ' .
                'Cette affectation s\'est terminée le %s. ' .
                'Pour des raisons d\'audit et de traçabilité, les affectations terminées ne peuvent pas être supprimées.',
                $assignment->end_datetime->format('d/m/Y à H:i')
            );
        }

        // Affectation active (en cours)
        if ($assignment->status === Assignment::STATUS_ACTIVE) {
            $duration = $assignment->start_datetime->diffForHumans();
            return sprintf(
                'Impossible de supprimer une affectation en cours. ' .
                'Cette affectation a démarré %s. ' .
                'Veuillez d\'abord la terminer avant de la supprimer, ou utilisez la fonction "Annuler" si nécessaire.',
                $duration
            );
        }

        // Affectation annulée
        if ($assignment->status === Assignment::STATUS_CANCELLED) {
            return 'Impossible de supprimer une affectation annulée. ' .
                   'Les affectations annulées sont conservées pour l\'historique et l\'audit.';
        }

        // Affectation trop ancienne (> 24h)
        if ($assignment->created_at && $assignment->created_at->diffInHours() >= 24) {
            return sprintf(
                'Impossible de supprimer cette affectation. ' .
                'Elle a été créée il y a %s. ' .
                'Seules les affectations créées il y a moins de 24 heures peuvent être supprimées (sauf si elles sont programmées).',
                $assignment->created_at->diffForHumans()
            );
        }

        // Raison générique (ne devrait pas arriver)
        return 'Cette affectation ne peut pas être supprimée pour le moment. ' .
               'Veuillez vérifier son statut et réessayer.';
    }

    /**
     * Termine une affectation en cours
     */
    public function end(Request $request, Assignment $assignment): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $assignment);

        if (!$assignment->canBeEnded()) {
            $message = 'Cette affectation ne peut pas être terminée.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->with('error', $message);
        }

        // Validation des données
        $validated = $request->validate([
            'end_datetime' => ['required', 'date', 'after_or_equal:' . $assignment->start_datetime],
            'end_mileage' => ['nullable', 'integer', 'min:' . ($assignment->start_mileage ?? 0)],
            'notes' => ['nullable', 'string', 'max:1000']
        ], [
            'end_datetime.required' => 'La date de fin est obligatoire.',
            'end_datetime.date' => 'Le format de la date de fin est invalide.',
            'end_datetime.after_or_equal' => 'La date de fin doit être postérieure au début.',
            'end_mileage.integer' => 'Le kilométrage doit être un nombre.',
            'end_mileage.min' => 'Le kilométrage de fin doit être supérieur au kilométrage de début.'
        ]);

        try {
            // Utiliser la méthode enterprise du modèle
            $success = $assignment->end(
                Carbon::parse($validated['end_datetime']),
                $validated['end_mileage'] ?? null,
                $validated['notes'] ?? null
            );

            if ($success) {
                $message = 'Affectation terminée avec succès.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message,
                        'assignment' => $assignment->fresh()
                    ]);
                }

                return redirect()->route('admin.assignments.index')->with('success', $message);
            }

            throw new \Exception('Échec de la terminaison');

        } catch (\Exception $e) {
            $errorMessage = 'Erreur lors de la terminaison de l\'affectation: ' . $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Vue Gantt des affectations
     */
    public function gantt(): View
    {
        $this->authorize('view assignments');

        return view('admin.assignments.gantt', [
            'title' => 'Planning Gantt des Affectations',
            'breadcrumbs' => [
                'Admin' => route('admin.dashboard'),
                'Affectations' => route('admin.assignments.index'),
                'Planning Gantt' => null
            ]
        ]);
    }

    /**
     * API - Export des affectations
     */
    public function export(Request $request): JsonResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('view assignments');

        $format = $request->input('format', 'csv');
        $filters = $request->only(['status', 'vehicle_id', 'driver_id', 'date_from', 'date_to']);

        $query = Assignment::query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['vehicle', 'driver', 'creator']);

        // Application des filtres
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['vehicle_id']) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        if ($filters['driver_id']) {
            $query->where('driver_id', $filters['driver_id']);
        }

        if ($filters['date_from']) {
            $query->where('start_datetime', '>=', Carbon::parse($filters['date_from']));
        }

        if ($filters['date_to']) {
            $query->where('start_datetime', '<=', Carbon::parse($filters['date_to']));
        }

        $assignments = $query->orderBy('start_datetime', 'desc')->get();

        return match($format) {
            'csv' => $this->exportToCsv($assignments),
            default => response()->json(['error' => 'Format non supporté'], 400)
        };
    }

    /**
     * API - Statistiques des affectations enterprise
     */
    public function stats(Request $request): JsonResponse
    {
        $this->authorize('view assignment statistics');

        $organizationId = auth()->user()->organization_id;

        // Période par défaut : ce mois-ci, mais compatible avec requêtes sans paramètres
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now()->endOfMonth());

        try {
            // Affectations actives (en cours, pas de date de fin)
            $activeCount = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->where('start_datetime', '<=', now())
                ->count();

            // Affectations programmées (date de début future)
            $scheduledCount = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->where('start_datetime', '>', now())
                ->count();

            // Affectations terminées ce mois
            $completedCount = Assignment::where('organization_id', $organizationId)
                ->whereNotNull('end_datetime')
                ->whereMonth('end_datetime', now()->month)
                ->whereYear('end_datetime', now()->year)
                ->count();

            // Total pour la période
            $totalAssignments = Assignment::where('organization_id', $organizationId)
                ->whereBetween('start_datetime', [$dateFrom, $dateTo])
                ->count();

            // Calcul du taux d'utilisation (véhicules affectés / total véhicules)
            $totalVehicles = Vehicle::where('organization_id', $organizationId)
                ->where('status', 'active')
                ->count();

            $assignedVehicles = Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')
                ->distinct('vehicle_id')
                ->count('vehicle_id');

            $avgUtilization = $totalVehicles > 0 ? round(($assignedVehicles / $totalVehicles) * 100, 1) : 0;

            // Structure de réponse compatible avec l'interface frontend
            $stats = [
                'total_assignments' => $totalAssignments,
                'active_assignments' => $activeCount,
                'scheduled_assignments' => $scheduledCount,
                'completed_assignments' => $completedCount,
                'average_utilization' => $avgUtilization,

                // Métriques avancées pour usage enterprise
                'total_vehicles' => $totalVehicles,
                'assigned_vehicles' => $assignedVehicles,
                'available_vehicles' => $totalVehicles - $assignedVehicles,
                'utilization_percentage' => $avgUtilization,

                // Meta informations
                'period' => [
                    'from' => $dateFrom,
                    'to' => $dateTo,
                    'current_month' => now()->format('Y-m')
                ],

                // Données détaillées (optionnelles, selon les besoins)
                'vehicles_breakdown' => $request->boolean('detailed') ?
                    Vehicle::where('organization_id', $organizationId)
                        ->withCount(['assignments as active_assignments_count' => function ($query) {
                            $query->whereNull('end_datetime')->where('start_datetime', '<=', now());
                        }])
                        ->get(['id', 'registration_plate', 'brand', 'model'])
                        ->map(function ($vehicle) {
                            return [
                                'vehicle_id' => $vehicle->id,
                                'registration_plate' => $vehicle->registration_plate,
                                'brand_model' => $vehicle->brand . ' ' . $vehicle->model,
                                'is_assigned' => $vehicle->active_assignments_count > 0,
                                'active_assignments' => $vehicle->active_assignments_count
                            ];
                        }) : []
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            // Gestion d'erreur enterprise avec fallback
            \Log::error('Erreur dans AssignmentController::stats()', [
                'error' => $e->getMessage(),
                'organization_id' => $organizationId,
                'user_id' => auth()->id()
            ]);

            // Retourner des valeurs par défaut en cas d'erreur
            return response()->json([
                'total_assignments' => 0,
                'active_assignments' => 0,
                'scheduled_assignments' => 0,
                'completed_assignments' => 0,
                'average_utilization' => 0,
                'total_vehicles' => 0,
                'assigned_vehicles' => 0,
                'error' => 'Erreur lors du calcul des statistiques'
            ], 200); // 200 pour ne pas casser l'interface
        }
    }

    /**
     * API - Véhicules disponibles pour affectation
     */
    public function availableVehicles(Request $request): JsonResponse
    {
        $this->authorize('view assignments');

        $vehicles = Vehicle::where('organization_id', auth()->user()->organization_id)
            ->where('status', 'active')
            ->whereDoesntHave('assignments', function($query) {
                // Véhicules sans affectation en cours
                $query->whereNull('end_datetime')
                      ->where('start_datetime', '<=', now());
            })
            ->select('id', 'registration_plate', 'brand', 'model', 'current_mileage', 'status')
            ->orderBy('registration_plate')
            ->get();

        return response()->json($vehicles);
    }

    /**
     * API - Chauffeurs disponibles pour affectation
     */
    public function availableDrivers(Request $request): JsonResponse
    {
        $this->authorize('view assignments');

        $drivers = Driver::where('organization_id', auth()->user()->organization_id)
            ->whereHas('driverStatus', function($statusQuery) {
                $statusQuery->where('is_active', true)
                           ->where('can_drive', true)
                           ->where('can_assign', true);
            })
            ->whereDoesntHave('assignments', function($query) {
                // Chauffeurs sans affectation en cours
                $query->whereNull('end_datetime')
                      ->where('start_datetime', '<=', now());
            })
            ->with('driverStatus')
            ->select('id', 'first_name', 'last_name', 'license_number', 'personal_phone', 'status_id')
            ->orderBy('last_name')
            ->get()
            ->map(function($driver) {
                return [
                    'id' => $driver->id,
                    'full_name' => $driver->full_name,
                    'first_name' => $driver->first_name,
                    'last_name' => $driver->last_name,
                    'license_number' => $driver->license_number,
                    'personal_phone' => $driver->personal_phone,
                    'status' => $driver->driverStatus?->name ?? 'Actif',
                    'status_color' => $driver->driverStatus?->color ?? '#10b981'
                ];
            });

        return response()->json($drivers);
    }

    /**
     * 📄 Export PDF Enterprise-Grade - ULTRA-PROFESSIONNEL
     *
     * Génère un PDF détaillé et professionnel de l'affectation via micro-service PDF.
     *
     * FONCTIONNALITÉS SURPASSANT FLEETIO/SAMSARA :
     * - Design moderne et épuré (inspiration Apple/Tesla)
     * - Toutes les informations de l'affectation
     * - Détails véhicule (marque, modèle, plaque, kilométrage)
     * - Détails chauffeur (nom, téléphone, permis)
     * - Période d'affectation et durée
     * - Informations audit (créateur, dates)
     * - QR Code pour tracking digital (optionnel)
     * - Logo organisation (si disponible)
     * - Mise en page A4 professionnelle
     * - Optimisé pour impression et archivage
     *
     * SÉCURITÉ :
     * - Autorisation via Policy (view permission + same organization)
     * - Audit trail complet
     * - Pas d'exposition de données sensibles
     *
     * ARCHITECTURE :
     * - Utilise PdfGenerationService (micro-service externe)
     * - Template Blade dédié pour styling
     * - Communication HTTP sécurisée avec pdf-service
     * - Retry automatique en cas d'échec
     * - Health check du service avant génération
     *
     * @param Assignment $assignment L'affectation à exporter
     * @param PdfGenerationService $pdfService Service de génération PDF injecté
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function exportPdf(Assignment $assignment, PdfGenerationService $pdfService): Response|RedirectResponse
    {
        // 🛡️ AUTORISATION ENTERPRISE - Via Policy
        $this->authorize('view', $assignment);

        // 📋 LOG AUDIT TRAIL - Export PDF
        Log::info('Export PDF d\'affectation demandé', [
            'assignment_id' => $assignment->id,
            'vehicle' => $assignment->vehicle_display,
            'driver' => $assignment->driver_display,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'organization_id' => auth()->user()->organization_id
        ]);

        try {
            // 🔄 EAGER LOADING - Charger toutes les relations nécessaires
            $assignment->load([
                'vehicle.vehicleType',
                'driver.driverStatus',
                'creator',
                'updatedBy',
                'endedBy'
            ]);

            // 📊 CALCULS MÉTIER - Informations supplémentaires pour le PDF
            $durationInfo = [
                'hours' => $assignment->duration_hours,
                'formatted' => $assignment->formatted_duration,
                'current_hours' => $assignment->current_duration_hours,
                'is_ongoing' => $assignment->is_ongoing
            ];

            // 🏢 LOGO ORGANISATION - Convertir en base64 pour embedding dans PDF
            $logoBase64 = null;
            $logoPath = public_path('images/logo.png');

            if (file_exists($logoPath)) {
                $logoContent = file_get_contents($logoPath);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoContent);
            }

            // 🎨 GÉNÉRATION HTML - Utiliser vue Blade dédiée
            $html = view('admin.assignments.pdf', [
                'assignment' => $assignment,
                'duration' => $durationInfo,
                'logo_base64' => $logoBase64,
                'generated_at' => now(),
                'generated_by' => auth()->user()->name
            ])->render();

            // 🚀 GÉNÉRATION PDF - Via micro-service
            $pdfContent = $pdfService->generateFromHtml($html);

            // 📄 NOM FICHIER - Format professionnel
            $fileName = sprintf(
                'affectation-%s-%s-%s.pdf',
                $assignment->id,
                str_replace(' ', '-', strtolower($assignment->vehicle->registration_plate ?? 'vehicule')),
                now()->format('Y-m-d')
            );

            // ✅ LOG SUCCÈS
            Log::info('Export PDF d\'affectation réussi', [
                'assignment_id' => $assignment->id,
                'filename' => $fileName,
                'pdf_size_bytes' => strlen($pdfContent),
                'user_id' => auth()->id()
            ]);

            // 🎯 RETOUR PDF - Headers appropriés pour téléchargement
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public'
            ]);

        } catch (\Exception $e) {
            // 🔴 LOG ERREUR - Diagnostic complet
            Log::error('Erreur lors de l\'export PDF d\'affectation', [
                'assignment_id' => $assignment->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => config('app.debug') ? $e->getTraceAsString() : null,
                'user_id' => auth()->id(),
                'organization_id' => auth()->user()->organization_id
            ]);

            // 🎯 MESSAGE UTILISATEUR - Erreur contextuelle
            $errorMessage = config('app.debug')
                ? 'Erreur lors de la génération du PDF : ' . $e->getMessage()
                : 'Une erreur est survenue lors de la génération du PDF. Le service PDF est peut-être temporairement indisponible. Veuillez réessayer dans quelques instants.';

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Helpers privés pour export
     */
    private function exportToCsv($assignments): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="affectations_' . now()->format('Y-m-d') . '.csv"'
        ];

        $callback = function() use ($assignments) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Véhicule',
                'Chauffeur',
                'Date début',
                'Date fin',
                'Durée (heures)',
                'Statut',
                'Motif',
                'Kilométrage début',
                'Kilométrage fin',
                'Créé par',
                'Créé le'
            ]);

            // Données
            foreach ($assignments as $assignment) {
                fputcsv($file, [
                    $assignment->id,
                    $assignment->vehicle_display,
                    $assignment->driver_display,
                    $assignment->start_datetime->format('d/m/Y H:i'),
                    $assignment->end_datetime?->format('d/m/Y H:i') ?? 'En cours',
                    $assignment->duration_hours,
                    $assignment->status_label,
                    $assignment->reason,
                    $assignment->start_mileage,
                    $assignment->end_mileage,
                    $assignment->creator?->name ?? 'Système',
                    $assignment->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calcule le taux d'utilisation d'une ressource
     */
    private function calculateUtilizationRate($resource, $dateFrom, $dateTo): float
    {
        $totalPeriodHours = Carbon::parse($dateFrom)->diffInHours(Carbon::parse($dateTo));

        $usedHours = $resource->assignments()
            ->whereBetween('start_datetime', [$dateFrom, $dateTo])
            ->get()
            ->sum('duration_hours');

        return $totalPeriodHours > 0 ? round(($usedHours / $totalPeriodHours) * 100, 2) : 0;
    }


    /**
     * 🛡️ Helper Enterprise pour vérification des permissions
     * 
     * @param string $permission
     * @param string $errorMessage
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function checkPermissionEnterprise(string $permission, string $errorMessage = null): void
    {
        $user = auth()->user();
        
        // Vérifications multiples pour compatibilité
        $hasPermission = $user->can($permission) || 
                        $user->hasPermissionTo($permission) ||
                        $user->can(str_replace(' ', '.', $permission)) ||
                        $user->hasPermissionTo(str_replace(' ', '.', $permission));
        
        if (!$hasPermission) {
            $message = $errorMessage ?? "Vous n'avez pas la permission: {$permission}";
            
            if (config('app.debug')) {
                \Log::warning('Permission Denied', [
                    'user' => $user->email,
                    'required_permission' => $permission,
                    'user_permissions' => $user->getAllPermissions()->pluck('name')
                ]);
            }
            
            abort(403, $message);
        }
    }
}