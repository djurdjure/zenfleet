<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * 🏢 ZENFLEET - GESTIONNAIRE D'ORGANISATIONS ENTERPRISE
 * 
 * Contrôleur ultra-professionnel pour la gestion complète des organisations
 * avec audit, sécurité, analytics et fonctionnalités avancées.
 * 
 * @version 2.0
 * @author ZenFleet Team
 */
class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    /**
     * 📊 Liste des organisations avec analytics et filtres avancés
     */
    public function index(Request $request): View
    {
        $this->authorize('manage-organizations');

        try {
            // Construction de la query optimisée
            $query = Organization::withCount(['users', 'vehicles', 'drivers'])
                ->with(['users' => fn($q) => $q->where('is_active', true)->limit(5)]);

            // Application des filtres avec validation
            $this->applyFilters($query, $request);

            // Tri avec validation des champs
            $this->applySorting($query, $request);

            // Pagination avec query string
            $organizations = $query->paginate(20)->withQueryString();

            // Calcul des statistiques
            $stats = $this->calculateGlobalStatistics();
            $monthlyStats = $this->calculateMonthlyAnalytics();

            // Données pour les filtres
            $filterData = $this->getFilterData();

            // Audit logging
            $this->logAccess('organizations_index', [
                'filters' => $request->only(['search', 'status', 'country', 'type']),
                'results_count' => $organizations->total()
            ]);

            // Breadcrumbs
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Organisations', 'url' => null]
            ];

            return view('admin.organizations.index', compact(
                'organizations',
                'stats', 
                'monthlyStats',
                'filterData',
                'breadcrumbs'
            ));

        } catch (\Exception $e) {
            $this->logError('organizations_index_failed', $e);
            
            return view('admin.organizations.index', [
                'organizations' => Organization::paginate(20),
                'stats' => $this->getDefaultStats(),
                'filterData' => $this->getFilterData(),
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                    ['title' => 'Organisations', 'url' => null]
                ]
            ])->withErrors(['error' => 'Erreur lors du chargement des organisations.']);
        }
    }

    /**
     * 👁️ Affichage détaillé d'une organisation
     */
    public function show(Organization $organization): View
    {
        $this->authorize('manage-organizations');

        try {
            // Chargement optimisé des relations
            $organization->load([
                'users' => fn($q) => $q->with('roles')->where('is_active', true),
                'vehicles' => fn($q) => $q->with(['assignments' => fn($aq) => $aq->with('driver')->whereNull('end_date')]),
                'drivers' => fn($q) => $q->with(['assignments' => fn($aq) => $aq->with('vehicle')->whereNull('end_date')])
            ]);

            // Statistiques détaillées
            $stats = $this->calculateOrganizationStatistics($organization);
            $recentActivity = $this->getRecentActivity($organization);
            $performanceData = $this->calculatePerformanceMetrics($organization);

            // Breadcrumbs
            $breadcrumbs = [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Organisations', 'url' => route('admin.organizations.index')],
                ['title' => $organization->name, 'url' => null]
            ];

            $this->logAccess('organization_view', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name
            ]);

            return view('admin.organizations.show', compact(
                'organization',
                'stats',
                'recentActivity', 
                'performanceData',
                'breadcrumbs'
            ));

        } catch (\Exception $e) {
            $this->logError('organization_show_failed', $e, ['organization_id' => $organization->id]);
            
            return redirect()
                ->route('admin.organizations.index')
                ->withErrors(['error' => 'Erreur lors du chargement de l\'organisation.']);
        }
    }

    /**
     * 📝 Formulaire de création
     */
    public function create(): View
    {
        $this->authorize('manage-organizations');

        $formData = [
            'countries' => $this->getCountriesList(),
            'organizationTypes' => $this->getOrganizationTypes(),
            'currencies' => $this->getCurrenciesList(),
            'timezones' => $this->getTimezonesList(),
            'subscriptionPlans' => $this->getSubscriptionPlans(),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Organisations', 'url' => route('admin.organizations.index')],
                ['title' => 'Nouvelle organisation', 'url' => null]
            ]
        ];

        return view('admin.organizations.create', $formData);
    }

    /**
     * 💾 Enregistrement d'une nouvelle organisation
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-organizations');

        try {
            DB::beginTransaction();

            $validated = $this->validateOrganizationData($request);
            
            // Traitement du logo
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $this->handleLogoUpload($request->file('logo'));
            }

            // Création de l'organisation
            $organization = Organization::create([
                ...$validated,
                'slug' => $this->generateUniqueSlug($validated['name']),
                'logo_path' => $logoPath,
                'status' => 'active',
                'created_by' => Auth::id(),
                'subscription_plan' => $validated['subscription_plan'] ?? 'standard',
                'subscription_expires_at' => now()->addYear(),
                'working_days' => json_encode($validated['working_days'] ?? [1,2,3,4,5]),
                'settings' => $this->buildOrganizationSettings($validated),
            ]);

            // Création de l'utilisateur admin
            $adminUser = $this->createOrganizationAdmin($organization, $validated);

            // Mise à jour des compteurs
            $organization->update([
                'total_users' => 1,
                'active_users' => 1,
                'admin_user_id' => $adminUser->id,
            ]);

            DB::commit();

            $this->logSuccess('organization_created', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'admin_user_id' => $adminUser->id
            ]);

            return redirect()
                ->route('admin.organizations.show', $organization)
                ->with('success', 'Organisation créée avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError('organization_creation_failed', $e);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création.']);
        }
    }

    /**
     * 📝 Formulaire de modification
     */
    public function edit(Organization $organization): View
    {
        $this->authorize('manage-organizations');

        $formData = [
            'organization' => $organization,
            'countries' => $this->getCountriesList(),
            'organizationTypes' => $this->getOrganizationTypes(), 
            'currencies' => $this->getCurrenciesList(),
            'timezones' => $this->getTimezonesList(),
            'subscriptionPlans' => $this->getSubscriptionPlans(),
            'settings' => json_decode($organization->settings, true) ?? [],
            'workingDays' => json_decode($organization->working_days, true) ?? [1,2,3,4,5],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['title' => 'Organisations', 'url' => route('admin.organizations.index')],
                ['title' => $organization->name, 'url' => route('admin.organizations.show', $organization)],
                ['title' => 'Modifier', 'url' => null]
            ]
        ];

        return view('admin.organizations.edit', $formData);
    }

    /**
     * 💾 Mise à jour d'une organisation
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('manage-organizations');

        try {
            DB::beginTransaction();

            $validated = $this->validateOrganizationData($request, $organization);
            $originalData = $organization->toArray();

            // Traitement du logo
            if ($request->hasFile('logo')) {
                if ($organization->logo_path) {
                    Storage::disk('public')->delete($organization->logo_path);
                }
                $validated['logo_path'] = $this->handleLogoUpload($request->file('logo'));
            }

            // Mise à jour des settings
            $validated['settings'] = $this->buildOrganizationSettings($validated);
            $validated['working_days'] = json_encode($validated['working_days'] ?? [1,2,3,4,5]);
            $validated['updated_by'] = Auth::id();

            $organization->update($validated);

            DB::commit();

            $changes = array_diff_assoc($validated, $originalData);
            $this->logSuccess('organization_updated', [
                'organization_id' => $organization->id,
                'changes' => array_keys($changes)
            ]);

            return redirect()
                ->route('admin.organizations.show', $organization)
                ->with('success', 'Organisation mise à jour avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($e->errors());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError('organization_update_failed', $e, ['organization_id' => $organization->id]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    /**
     * 🗑️ Suppression sécurisée (soft delete)
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('manage-organizations');

        // Vérifications de sécurité
        $usersCount = $organization->users()->count();
        $vehiclesCount = $organization->vehicles()->count();

        if ($usersCount > 0) {
            return back()->withErrors([
                'error' => "Impossible de supprimer une organisation avec {$usersCount} utilisateur(s)."
            ]);
        }

        if ($vehiclesCount > 0) {
            return back()->withErrors([
                'error' => "Impossible de supprimer une organisation avec {$vehiclesCount} véhicule(s)."
            ]);
        }

        try {
            DB::beginTransaction();

            $organizationData = [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email
            ];

            $organization->delete();

            DB::commit();

            $this->logCritical('organization_deleted', [
                'organization_data' => $organizationData,
                'reason' => 'Manual deletion by Super Admin'
            ]);

            return redirect()
                ->route('admin.organizations.index')
                ->with('success', "Organisation '{$organizationData['name']}' supprimée avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logError('organization_deletion_failed', $e, ['organization_id' => $organization->id]);
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la suppression.']);
        }
    }

    /**
     * 📊 Export des organisations
     */
    public function export(Request $request): RedirectResponse
    {
        $this->authorize('manage-organizations');

        try {
            $format = $request->get('format', 'xlsx');
            
            $query = Organization::withCount(['users', 'vehicles', 'drivers']);
            $this->applyFilters($query, $request);
            $organizations = $query->get();

            $this->logSuccess('organizations_export_initiated', [
                'format' => $format,
                'count' => $organizations->count(),
                'filters' => $request->only(['search', 'status', 'country'])
            ]);

            // TODO: Implémenter l'export réel avec Laravel Excel
            return back()->with('success', 
                "Export de {$organizations->count()} organisations en préparation (format: {$format})."
            );

        } catch (\Exception $e) {
            $this->logError('organizations_export_failed', $e);
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'export.']);
        }
    }

    // ============================================================
    // MÉTHODES PRIVÉES UTILITAIRES
    // ============================================================

    /**
     * Application des filtres à la query
     */
    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('siret', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('city', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }

        if ($request->filled('type')) {
            $query->where('organization_type', $request->get('type'));
        }
    }

    /**
     * Application du tri à la query
     */
    private function applySorting($query, Request $request): void
    {
        $allowedSortFields = ['name', 'created_at', 'status', 'users_count', 'vehicles_count'];
        $sortField = in_array($request->get('sort'), $allowedSortFields) 
            ? $request->get('sort') 
            : 'created_at';
        
        $sortDirection = in_array($request->get('direction'), ['asc', 'desc'])
            ? $request->get('direction')
            : 'desc';

        $query->orderBy($sortField, $sortDirection);
    }

    /**
     * Calcul des statistiques globales
     */
    private function calculateGlobalStatistics(): array
    {
        return [
            'total' => Organization::count(),
            'active' => Organization::where('status', 'active')->count(),
            'inactive' => Organization::where('status', 'inactive')->count(),
            'pending' => Organization::where('status', 'pending')->count(),
            'total_users' => User::whereNotNull('organization_id')->count(),
            'total_vehicles' => Vehicle::count(),
            'total_drivers' => Driver::count(),
            'countries' => Organization::whereNotNull('country')->distinct('country')->count(),
            'avg_users_per_org' => round(User::whereNotNull('organization_id')->count() / max(Organization::count(), 1), 2),
        ];
    }

    /**
     * Analytics mensuelles
     */
    private function calculateMonthlyAnalytics()
    {
        return Organization::selectRaw('
            DATE_TRUNC(\'month\', created_at) as month,
            COUNT(*) as count,
            organization_type,
            status
        ')
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month', 'organization_type', 'status')
        ->orderBy('month')
        ->get();
    }

    /**
     * Statistiques d'une organisation
     */
    private function calculateOrganizationStatistics(Organization $organization): array
    {
        return [
            'users' => [
                'total' => $organization->users->count(),
                'active' => $organization->users->where('is_active', true)->count(),
                'by_role' => $organization->users->groupBy(function ($user) {
                    return $user->roles->first()->name ?? 'Aucun rôle';
                })->map->count()->toArray()
            ],
            'vehicles' => [
                'total' => $organization->vehicles->count(),
                'available' => $organization->vehicles->where('status', 'available')->count(),
                'in_use' => $organization->vehicles->where('status', 'in_use')->count(),
                'maintenance' => $organization->vehicles->where('status', 'maintenance')->count(),
            ],
            'drivers' => [
                'total' => $organization->drivers->count(),
                'active' => $organization->drivers->where('status', 'active')->count(),
                'inactive' => $organization->drivers->where('status', 'inactive')->count(),
            ]
        ];
    }

    /**
     * Activité récente
     */
    private function getRecentActivity(Organization $organization)
    {
        return collect([
            ...$organization->users()
                ->where('created_at', '>=', now()->subDays(30))
                ->get()
                ->map(fn($user) => [
                    'type' => 'user_created',
                    'description' => "Nouvel utilisateur: {$user->name}",
                    'date' => $user->created_at,
                    'icon' => 'user-plus',
                    'color' => 'success'
                ]),
            ...$organization->vehicles()
                ->where('created_at', '>=', now()->subDays(30))
                ->get()
                ->map(fn($vehicle) => [
                    'type' => 'vehicle_created',
                    'description' => "Nouveau véhicule: {$vehicle->registration_plate}",
                    'date' => $vehicle->created_at,
                    'icon' => 'truck',
                    'color' => 'info'
                ]),
        ])->sortByDesc('date')->take(20);
    }

    /**
     * Métriques de performance
     */
    private function calculatePerformanceMetrics(Organization $organization): array
    {
        // TODO: Implémenter les vraies métriques
        return [
            'efficiency_score' => 85.5,
            'utilization_rate' => 78.2,
            'maintenance_compliance' => 92.1,
            'driver_satisfaction' => 87.8,
        ];
    }

    /**
     * Données pour les filtres
     */
    private function getFilterData(): array
    {
        return [
            'statuses' => [
                'active' => 'Actif',
                'inactive' => 'Inactif', 
                'pending' => 'En attente',
                'suspended' => 'Suspendu'
            ],
            'countries' => $this->getCountriesList(),
            'types' => $this->getOrganizationTypes()
        ];
    }

    /**
     * Statistiques par défaut en cas d'erreur
     */
    private function getDefaultStats(): array
    {
        return [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'pending' => 0,
            'total_users' => 0,
            'total_vehicles' => 0,
            'total_drivers' => 0,
            'countries' => 0,
            'avg_users_per_org' => 0,
        ];
    }

    // [Méthodes existantes de validation, upload, etc... gardées identiques]
    // ... [Le reste des méthodes utilitaires restent identiques] ...

    /**
     * Validation des données d'organisation
     */
    private function validateOrganizationData(Request $request, ?Organization $organization = null): array
    {
        return $request->validate([
            // Informations générales
            'name' => [
                'required',
                'string', 
                'max:255',
                Rule::unique('organizations', 'name')->ignore($organization?->id)
            ],
            'legal_name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'string', Rule::in(array_keys($this->getOrganizationTypes()))],
            'industry' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],

            // Informations légales
            'siret' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]{14}$/',
                Rule::unique('organizations', 'siret')->ignore($organization?->id)
            ],
            'vat_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('organizations', 'vat_number')->ignore($organization?->id)
            ],

            // Contact
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('organizations', 'email')->ignore($organization?->id)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],

            // Adresse
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],

            // Paramètres
            'timezone' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'language' => ['required', 'string', 'max:5'],

            // Limites
            'max_vehicles' => ['required', 'integer', 'min:1', 'max:10000'],
            'max_drivers' => ['required', 'integer', 'min:1', 'max:5000'],
            'max_users' => ['required', 'integer', 'min:1', 'max:1000'],

            // Logo
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],

            // Paramètres métier
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['integer', 'between:1,7'],
            'maintenance_alert_days' => ['required', 'integer', 'min:1', 'max:365'],
            'license_expiry_alert_days' => ['required', 'integer', 'min:1', 'max:365'],

            // Admin (création seulement)
            'admin_first_name' => $organization ? [] : ['required', 'string', 'max:100'],
            'admin_last_name' => $organization ? [] : ['required', 'string', 'max:100'],
            'admin_email' => $organization ? [] : ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:20'],
        ]);
    }

    // Méthodes utilitaires (logos, slugs, settings, admin, listes)
    private function handleLogoUpload($file): string
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Type de fichier non autorisé pour le logo.');
        }

        $filename = 'org_logo_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('organizations/logos', $filename, 'public');
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function buildOrganizationSettings(array $validated): string
    {
        return json_encode([
            'maintenance_alert_days' => $validated['maintenance_alert_days'],
            'license_expiry_alert_days' => $validated['license_expiry_alert_days'],
            'business_hours' => [
                'start' => $validated['business_hours_start'] ?? '08:00',
                'end' => $validated['business_hours_end'] ?? '18:00',
            ],
            'notifications' => [
                'email_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'security' => [
                'two_factor_required' => false,
                'password_expiry_days' => 90,
                'session_timeout_minutes' => 480,
            ],
        ]);
    }

    private function createOrganizationAdmin(Organization $organization, array $validated): User
    {
        $adminUser = User::create([
            'first_name' => $validated['admin_first_name'],
            'last_name' => $validated['admin_last_name'],
            'name' => $validated['admin_first_name'] . ' ' . $validated['admin_last_name'],
            'email' => $validated['admin_email'],
            'phone' => $validated['admin_phone'],
            'organization_id' => $organization->id,
            'password' => bcrypt('TempPassword2025!'),
            'is_active' => true,
            'user_status' => 'active',
            'email_verified_at' => now(),
            'created_by' => Auth::id(),
            'timezone' => $validated['timezone'],
            'language' => $validated['language'],
        ]);

        $adminUser->assignRole('Admin');
        return $adminUser;
    }

    // Listes de données
    private function getCountriesList(): array
    {
        return [
            'DZ' => 'Algérie',
            'FR' => 'France',
            'BE' => 'Belgique',
            'CH' => 'Suisse',
            'LU' => 'Luxembourg',
            'DE' => 'Allemagne',
            'ES' => 'Espagne',
            'IT' => 'Italie',
            'GB' => 'Royaume-Uni',
            'US' => 'États-Unis',
            'CA' => 'Canada',
            'MA' => 'Maroc',
            'TN' => 'Tunisie',
            'NL' => 'Pays-Bas',
            'PT' => 'Portugal',
        ];
    }

    private function getOrganizationTypes(): array
    {
        return [
            'enterprise' => 'Grande Entreprise',
            'sme' => 'PME',
            'startup' => 'Startup', 
            'public' => 'Secteur Public',
            'ngo' => 'ONG',
            'cooperative' => 'Coopérative',
            'association' => 'Association',
            'sole_proprietorship' => 'Entreprise Individuelle',
        ];
    }

    private function getCurrenciesList(): array
    {
        return [
            'DZD' => 'Dinar Algérien (DZD)',
            'EUR' => 'Euro (€)',
            'USD' => 'Dollar US ($)',
            'GBP' => 'Livre Sterling (£)',
            'CHF' => 'Franc Suisse (CHF)',
            'CAD' => 'Dollar Canadien (C$)',
            'MAD' => 'Dirham Marocain (MAD)',
            'TND' => 'Dinar Tunisien (TND)',
        ];
    }

    private function getTimezonesList(): array
    {
        return [
            'Africa/Algiers' => 'Algérie (GMT+1)',
            'Europe/Paris' => 'France (CET)',
            'Europe/London' => 'Royaume-Uni (GMT)',
            'Europe/Berlin' => 'Allemagne (CET)',
            'Europe/Madrid' => 'Espagne (CET)',
            'Europe/Rome' => 'Italie (CET)',
            'Europe/Brussels' => 'Belgique (CET)',
            'Europe/Zurich' => 'Suisse (CET)',
            'America/New_York' => 'New York (EST)',
            'America/Toronto' => 'Toronto (EST)',
            'Africa/Casablanca' => 'Maroc (GMT+1)',
            'Africa/Tunis' => 'Tunisie (GMT+1)',
        ];
    }

    private function getSubscriptionPlans(): array
    {
        return [
            'basic' => 'Basic (25 véhicules)',
            'standard' => 'Standard (100 véhicules)',
            'professional' => 'Professional (500 véhicules)',
            'enterprise' => 'Enterprise (illimité)',
        ];
    }

    // Méthodes de logging
    private function logAccess(string $action, array $context = []): void
    {
        Log::channel('audit')->info("Organizations: {$action}", [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            ...$context
        ]);
    }

    private function logSuccess(string $action, array $context = []): void
    {
        Log::channel('audit')->info("Organizations: {$action} - SUCCESS", [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
            ...$context
        ]);
    }

    private function logError(string $action, \Exception $e, array $context = []): void
    {
        Log::channel('errors')->error("Organizations: {$action} - FAILED", [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
            ...$context
        ]);
    }

    private function logCritical(string $action, array $context = []): void
    {
        Log::channel('security')->warning("Organizations: {$action} - CRITICAL", [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'ip_address' => request()->ip(),
            'timestamp' => now()->toISOString(),
            ...$context
        ]);
    }
}
