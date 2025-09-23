<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Http\Requests\Admin\Driver\StoreDriverRequest;
use App\Http\Requests\Admin\Driver\UpdateDriverRequest;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\DriverService;
use App\Services\ImportExportService;


class DriverController extends Controller
{
    protected DriverService $driverService;
    protected ImportExportService $importExportService;

    public function __construct(DriverService $driverService, ImportExportService $importExportService)
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
        $this->driverService = $driverService;
        $this->importExportService = $importExportService;
    }

    /**
     * 📊 Liste des chauffeurs avec filtrage avancé
     */
    public function index(Request $request): View
    {
        $this->authorize('view drivers');

        try {
            $filters = $request->only(['search', 'status_id', 'per_page', 'view_deleted']);
            $drivers = $this->driverService->getFilteredDrivers($filters);
            $driverStatuses = DriverStatus::orderBy('name')->get();

            return view('admin.drivers.index', compact('drivers', 'driverStatuses', 'filters'));

        } catch (\Exception $e) {
            Log::error('Drivers index error: ' . $e->getMessage());

            $drivers = Driver::with(['driverStatus', 'organization', 'user'])
                ->when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })
                ->paginate(15);

            $driverStatuses = DriverStatus::orderBy('name')->get();
            $filters = [];

            return view('admin.drivers.index', compact('drivers', 'driverStatuses', 'filters'))
                ->withErrors(['error' => 'Erreur lors du chargement des chauffeurs.']);
        }
    }

    /**
     * 📝 Formulaire de création d'un chauffeur
     */
    public function create(): View
    {
        $this->authorize('create drivers');

        try {
            $driverStatuses = DriverStatus::orderBy('name')->get();

            // Récupération des utilisateurs non liés à un chauffeur
            $assignedUserIds = Driver::whereNotNull('user_id')->pluck('user_id');
            $linkableUsers = User::whereNotIn('id', $assignedUserIds)
                ->when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })
                ->orderBy('name')
                ->get();

            return view('admin.drivers.create', compact('driverStatuses', 'linkableUsers'));

        } catch (\Exception $e) {
            Log::error('Driver create form error: ' . $e->getMessage());

            return redirect()->route('admin.drivers.index')
                ->withErrors(['error' => 'Erreur lors du chargement du formulaire de création.']);
        }
    }

    /**
     * 💾 Enregistrement d'un nouveau chauffeur
     */
    public function store(StoreDriverRequest $request): RedirectResponse
    {
        try {
            $driver = $this->driverService->createDriver($request->validated());

            Log::info('Driver created successfully', [
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                'created_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.drivers.index')
                ->with('success', "Le chauffeur {$driver->first_name} {$driver->last_name} a été créé avec succès.");

        } catch (\Exception $e) {
            Log::error('Driver store error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la création du chauffeur: ' . $e->getMessage()]);
        }
    }

    /**
     * ✏️ Formulaire d'édition d'un chauffeur
     */
    public function edit(Driver $driver): View
    {
        $this->authorize('edit drivers');

        try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce chauffeur.');
            }

            $driverStatuses = DriverStatus::orderBy('name')->get();

            // Récupération des utilisateurs non liés à un chauffeur (excluant l'utilisateur actuel du chauffeur)
            $assignedUserIds = Driver::whereNotNull('user_id')
                ->where('id', '!=', $driver->id)
                ->pluck('user_id');

            $linkableUsers = User::whereNotIn('id', $assignedUserIds)
                ->when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })
                ->orderBy('name')
                ->get();

            return view('admin.drivers.edit', compact('driver', 'driverStatuses', 'linkableUsers'));

        } catch (\Exception $e) {
            Log::error('Driver edit form error: ' . $e->getMessage());

            return redirect()->route('admin.drivers.index')
                ->withErrors(['error' => 'Erreur lors du chargement du formulaire d\'édition.']);
        }
    }

    /**
     * 🔄 Mise à jour d'un chauffeur
     */
    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de modifier ce chauffeur.');
            }

            $updatedDriver = $this->driverService->updateDriver($driver, $request->validated());

            Log::info('Driver updated successfully', [
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                'updated_by' => auth()->id()
            ]);

            return redirect()
                ->route('admin.drivers.index')
                ->with('success', "Le chauffeur {$updatedDriver->first_name} {$updatedDriver->last_name} a été mis à jour avec succès.");

        } catch (\Exception $e) {
            Log::error('Driver update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la mise à jour du chauffeur: ' . $e->getMessage()]);
        }
    }

    /**
     * 🗑️ Archivage d'un chauffeur (soft delete)
     */
    public function destroy(Driver $driver): RedirectResponse
    {
        $this->authorize('delete drivers');

        try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de supprimer ce chauffeur.');
            }

            $archived = $this->driverService->archiveDriver($driver);

            if ($archived) {
                // Traçabilité enterprise complète
                Log::info('Driver archived successfully', [
                    'operation' => 'driver_archive',
                    'driver_id' => $driver->id,
                    'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                    'employee_number' => $driver->employee_number,
                    'organization_id' => $driver->organization_id,
                    'archived_by_user_id' => auth()->id(),
                    'archived_by_user_email' => auth()->user()->email,
                    'archived_by_organization' => auth()->user()->organization_id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'timestamp' => now()->toISOString(),
                    'reason' => 'Manual archive via admin interface'
                ]);

                return redirect()
                    ->route('admin.drivers.index')
                    ->with('success', "Le chauffeur {$driver->first_name} {$driver->last_name} a été archivé avec succès.");
            }

            return redirect()
                ->back()
                ->with('error', 'Impossible d\'archiver ce chauffeur car il est lié à des affectations actives.');

        } catch (\Exception $e) {
            Log::error('Driver destroy error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'archivage du chauffeur: ' . $e->getMessage());
        }
    }

    /**
     * 🔄 Restauration d'un chauffeur archivé
     */
    public function restore($driverId): RedirectResponse
    {
        $this->authorize('restore drivers');

        try {
            $driver = Driver::withTrashed()->findOrFail($driverId);

            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de restaurer ce chauffeur.');
            }

            $restoredDriver = $this->driverService->restoreDriver($driverId);

            // Traçabilité enterprise complète pour la restauration
            Log::info('Driver restored successfully', [
                'operation' => 'driver_restore',
                'driver_id' => $driver->id,
                'driver_name' => $driver->first_name . ' ' . $driver->last_name,
                'employee_number' => $driver->employee_number,
                'organization_id' => $driver->organization_id,
                'restored_by_user_id' => auth()->id(),
                'restored_by_user_email' => auth()->user()->email,
                'restored_by_organization' => auth()->user()->organization_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString(),
                'reason' => 'Manual restore via admin interface'
            ]);

            return redirect()
                ->route('admin.drivers.index', ['view_deleted' => true])
                ->with('success', "Le chauffeur {$restoredDriver->first_name} {$restoredDriver->last_name} a été restauré avec succès.");

        } catch (\Exception $e) {
            Log::error('Driver restore error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la restauration du chauffeur: ' . $e->getMessage());
        }
    }

    /**
     * ❌ Suppression définitive d'un chauffeur
     */
    public function forceDelete($driverId): RedirectResponse
    {
        $this->authorize('force delete drivers');

        try {
            $driver = Driver::withTrashed()->findOrFail($driverId);

            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de supprimer définitivement ce chauffeur.');
            }

            $driverName = $driver->first_name . ' ' . $driver->last_name;
            $deleted = $this->driverService->forceDeleteDriver($driverId);

            if ($deleted) {
                Log::warning('Driver force deleted', [
                    'driver_id' => $driver->id,
                    'driver_name' => $driverName,
                    'deleted_by' => auth()->id()
                ]);

                return redirect()
                    ->route('admin.drivers.index', ['view_deleted' => true])
                    ->with('success', "Le chauffeur {$driverName} a été supprimé définitivement.");
            }

            return redirect()
                ->back()
                ->with('error', 'Impossible de supprimer définitivement ce chauffeur car il est lié à un historique d\'affectations.');

        } catch (\Exception $e) {
            Log::error('Driver force delete error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression définitive: ' . $e->getMessage());
        }
    }

    /**
     * 👁️ Affichage détaillé d'un chauffeur
     */
    public function show(Driver $driver): View
    {
        $this->authorize('view drivers');

        try {
            // Vérification des permissions pour l'organisation
            if (!auth()->user()->hasRole('Super Admin') && $driver->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Vous n\'avez pas l\'autorisation de voir ce chauffeur.');
            }

            // Chargement des relations avec gestion d'erreurs
            $driver->load(['driverStatus', 'organization', 'user']);

            // Statistiques de base
            $stats = [
                'total_assignments' => 0, // À implémenter selon les modèles d'affectations
                'active_assignments' => 0,
                'completed_trips' => 0,
                'total_distance' => 0,
            ];

            // Activité récente simulée (à remplacer par de vraies données)
            $recentActivity = collect([
                [
                    'description' => 'Statut changé vers ' . ($driver->driverStatus?->name ?? 'Non défini'),
                    'icon' => 'user-check',
                    'color' => 'info',
                    'date' => $driver->updated_at ?? now()
                ],
                [
                    'description' => 'Chauffeur créé',
                    'icon' => 'user-plus',
                    'color' => 'success',
                    'date' => $driver->created_at
                ]
            ]);

            return view('admin.drivers.show', compact('driver', 'stats', 'recentActivity'));

        } catch (\Exception $e) {
            Log::error('Driver show error: ' . $e->getMessage());

            return redirect()
                ->route('admin.drivers.index')
                ->withErrors(['error' => 'Erreur lors du chargement des détails du chauffeur.']);
        }
    }

    /**
     * 📊 Export des chauffeurs
     */
    public function export(Request $request)
    {
        $this->authorize('view drivers');

        try {
            $filters = $request->only(['search', 'status_id', 'view_deleted']);
            $format = $request->input('format', 'xlsx');

            return $this->importExportService->exportDrivers($filters, $format);

        } catch (\Exception $e) {
            Log::error('Driver export error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de l\'export: ' . $e->getMessage());
        }
    }

    /**
     * 📈 Statistiques des chauffeurs
     */
    public function statistics(): View
    {
        $this->authorize('view drivers');

        try {
            $stats = [
                'total_drivers' => Driver::when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })->count(),

                'active_drivers' => Driver::when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })->whereHas('driverStatus', function ($query) {
                    $query->where('name', 'Disponible');
                })->count(),

                'drivers_by_status' => Driver::when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })->with('driverStatus')
                    ->get()
                    ->groupBy('driverStatus.name')
                    ->map(fn($group) => $group->count()),

                'recent_recruitments' => Driver::when(auth()->user()->hasRole('Super Admin') === false, function ($query) {
                    return $query->where('organization_id', auth()->user()->organization_id);
                })->where('recruitment_date', '>=', now()->subDays(30))
                    ->count(),
            ];

            return view('admin.drivers.statistics', compact('stats'));

        } catch (\Exception $e) {
            Log::error('Driver statistics error: ' . $e->getMessage());

            return redirect()
                ->route('admin.drivers.index')
                ->withErrors(['error' => 'Erreur lors du chargement des statistiques.']);
        }
    }

    /**
     * 📊 Affiche le formulaire d'importation de chauffeurs
     */
    public function showImportForm(): View
    {
        return view('admin.drivers.import');
    }

    /**
     * 📊 Télécharge le fichier modèle CSV ultra-professionnel pour l'importation
     * Version Enterprise avec documentation complète et exemples multiples
     */
    public function downloadTemplate()
    {
        try {
            // Traçabilité enterprise de la génération du template
            Log::info('Driver import template downloaded', [
                'operation' => 'template_download',
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'organization_id' => auth()->user()->organization_id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            $csv = Writer::createFromString('');
            $csv->setOutputBOM(Writer::BOM_UTF8);

            // 🎯 En-têtes ultra-professionnels avec ordre logique enterprise
            $headers = [
                // Informations personnelles obligatoires
                'nom',                          // [OBLIGATOIRE] Nom de famille
                'prenom',                       // [OBLIGATOIRE] Prénom(s)
                'date_naissance',              // [OBLIGATOIRE] Format: AAAA-MM-JJ

                // Informations professionnelles
                'matricule',                   // [OPTIONNEL] Numéro d'employé unique
                'statut',                      // [OPTIONNEL] Disponible/En formation/Suspendu/etc.
                'date_recrutement',            // [OPTIONNEL] Format: AAAA-MM-JJ
                'date_fin_contrat',            // [OPTIONNEL] Format: AAAA-MM-JJ

                // Coordonnées et contact
                'telephone',                   // [OPTIONNEL] Numéro de téléphone
                'email_personnel',             // [OPTIONNEL] Email personnel
                'adresse',                     // [OPTIONNEL] Adresse complète

                // Informations de conduite
                'numero_permis',               // [OPTIONNEL] Numéro du permis de conduire
                'categorie_permis',            // [OPTIONNEL] Catégories (ex: B,C,D)
                'date_delivrance_permis',      // [OPTIONNEL] Format: AAAA-MM-JJ
                'date_expiration_permis',      // [OPTIONNEL] Format: AAAA-MM-JJ
                'autorite_delivrance',         // [OPTIONNEL] Autorité qui a délivré le permis

                // Contact d'urgence
                'contact_urgence_nom',         // [OPTIONNEL] Nom du contact d'urgence
                'contact_urgence_telephone',   // [OPTIONNEL] Téléphone du contact d'urgence

                // Informations médicales
                'groupe_sanguin'               // [OPTIONNEL] Groupe sanguin (A+, B-, O+, etc.)
            ];

            // 📋 Ligne d'instructions détaillées
            $instructions = [
                '# INSTRUCTIONS DÉTAILLÉES #',
                '# Remplissez les colonnes ci-dessous #',
                '# Les dates doivent être au format AAAA-MM-JJ #',
                '# Les champs nom, prenom et date_naissance sont OBLIGATOIRES #',
                '# Supprimez cette ligne avant l\'importation #',
                '# Exemple de statuts: Disponible, En formation, Suspendu, Inactif #',
                '# Categories permis séparées par des virgules: B,C,D #',
                '# Groupes sanguins: A+, A-, B+, B-, AB+, AB-, O+, O- #',
                '# Pour plus d\'aide: contactez l\'administrateur #',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];

            // 🎯 Exemples réalistes multiples pour l'Algérie
            $examples = [
                [
                    'Benali', 'Ahmed', '1985-03-15', 'EMP-2024-001', 'Disponible',
                    '2024-01-15', '2026-12-31', '0550123456', 'ahmed.benali@email.dz',
                    '25 Rue Didouche Mourad, 16000 Alger', 'P-DZ-2020-001234', 'B,C',
                    '2020-06-15', '2030-06-14', 'Wilaya d\'Alger', 'Benali Fatima',
                    '0661234567', 'O+'
                ],
                [
                    'Kaddour', 'Amina', '1990-07-22', 'EMP-2024-002', 'En formation',
                    '2024-02-01', '', '0770987654', 'amina.kaddour@gmail.com',
                    '18 Boulevard Mohamed V, 31000 Oran', 'P-DZ-2021-005678', 'B',
                    '2021-09-10', '2031-09-09', 'Wilaya d\'Oran', 'Kaddour Mohamed',
                    '0552987654', 'A+'
                ],
                [
                    'Slimani', 'Youcef', '1982-11-08', 'EMP-2024-003', 'Disponible',
                    '2024-01-20', '2025-12-31', '0665555444', 'y.slimani@outlook.com',
                    '42 Rue Larbi Ben M\'hidi, 25000 Constantine', 'P-DZ-2019-009876', 'B,C,D',
                    '2019-04-20', '2029-04-19', 'Wilaya de Constantine', 'Slimani Aicha',
                    '0771555444', 'B-'
                ]
            ];

            // 📝 Ligne de commentaires sur les champs
            $fieldComments = [
                '# Nom de famille (obligatoire) #',
                '# Prénom(s) (obligatoire) #',
                '# Date naissance AAAA-MM-JJ (obligatoire) #',
                '# Matricule employé unique #',
                '# Statut actuel du chauffeur #',
                '# Date de recrutement AAAA-MM-JJ #',
                '# Date fin contrat AAAA-MM-JJ (vide si CDI) #',
                '# Numéro de téléphone #',
                '# Email personnel #',
                '# Adresse complète #',
                '# Numéro permis de conduire #',
                '# Catégories permis (B,C,D...) #',
                '# Date délivrance permis AAAA-MM-JJ #',
                '# Date expiration permis AAAA-MM-JJ #',
                '# Autorité de délivrance #',
                '# Nom contact urgence #',
                '# Téléphone contact urgence #',
                '# Groupe sanguin (A+, B-, O+...) #'
            ];

            // Construction du CSV ultra-professionnel
            $csv->insertOne($instructions);
            $csv->insertOne($fieldComments);
            $csv->insertOne($headers);

            // Insertion des exemples multiples
            foreach ($examples as $example) {
                $csv->insertOne($example);
            }

            // Ligne de séparation et informations additionnelles
            $csv->insertOne(['']);
            $csv->insertOne(['# INFORMATIONS IMPORTANTES #']);
            $csv->insertOne(['# 1. Encodage: UTF-8 obligatoire #']);
            $csv->insertOne(['# 2. Séparateur: virgule (,) #']);
            $csv->insertOne(['# 3. Taille max: 10 MB #']);
            $csv->insertOne(['# 4. Extensions: .csv, .txt #']);
            $csv->insertOne(['# 5. Supprimez toutes les lignes de commentaires avant import #']);
            $csv->insertOne(['']);
            $csv->insertOne(['# Généré le: ' . now()->format('d/m/Y H:i:s') . ' #']);
            $csv->insertOne(['# Par: ' . auth()->user()->email . ' #']);
            $csv->insertOne(['# Organisation: ZenFleet Enterprise #']);

            // Nom de fichier enterprise avec timestamp
            $filename = 'ZenFleet_Template_Import_Chauffeurs_' . now()->format('Ymd_His') . '.csv';

            return response($csv->toString(), 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Driver template download error', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Erreur lors de la génération du fichier modèle.']);
        }
    }

    /**
     * 🚀 Traite l'importation du fichier CSV avec gestion enterprise ultra-robuste
     * Version Enterprise avec validation avancée, traçabilité complète et gestion d'erreurs
     */
    public function handleImport(Request $request): RedirectResponse
    {
        // Validation initiale enterprise avec règles strictes
        $validator = Validator::make($request->all(), [
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:10240', // 10 MB max
                function ($attribute, $value, $fail) {
                    if ($value && !$value->isValid()) {
                        $fail('Le fichier téléchargé est corrompu ou invalide.');
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('csv_file');
        $importId = Str::uuid()->toString();
        $startTime = microtime(true);

        // Traçabilité enterprise complète du début d'importation
        Log::info('Driver import process started', [
            'operation' => 'driver_import_start',
            'import_id' => $importId,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_mime' => $file->getMimeType(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'organization_id' => auth()->user()->organization_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            // Validation de la taille de fichier et du contenu
            if ($file->getSize() > 10485760) { // 10MB
                throw new \Exception('Le fichier est trop volumineux. Taille maximum autorisée: 10 MB.');
            }

            // Lecture sécurisée du contenu avec gestion BOM
            $fileContent = file_get_contents($file->getRealPath());
            if ($fileContent === false) {
                throw new \Exception('Impossible de lire le contenu du fichier.');
            }

            // Suppression du BOM UTF-8 si présent
            if (str_starts_with($fileContent, "\xef\xbb\xbf")) {
                $fileContent = substr($fileContent, 3);
                Log::info('BOM UTF-8 detected and removed', ['import_id' => $importId]);
            }

            // Détection intelligente du format avec validation
            $format = $this->detectFileFormat($fileContent);
            Log::info('File format detected', [
                'import_id' => $importId,
                'detected_format' => $format
            ]);

            // Parsing sécurisé du contenu selon le format
            $records = match ($format) {
                'csv' => $this->parseCsvFormat($fileContent),
                'key_value' => $this->parseCustomFormat($fileContent),
                default => throw new \Exception("Format de fichier non reconnu. Utilisez le template fourni."),
            };

            // Validation du nombre de records
            if (empty($records)) {
                throw new \Exception('Le fichier ne contient aucune donnée valide à importer.');
            }

            if (count($records) > 1000) {
                throw new \Exception('Le fichier contient trop d\'enregistrements. Maximum autorisé: 1000.');
            }

            // Initialisation des compteurs et variables
            $successCount = 0;
            $errorRows = [];
            $statuses = DriverStatus::pluck('id', 'name');
            $defaultStatusId = $statuses->get('Disponible');

            Log::info('Driver import validation passed', [
                'import_id' => $importId,
                'total_records' => count($records),
                'available_statuses' => $statuses->keys()->toArray()
            ]);

            // 🚀 Traitement enterprise ultra-robuste des enregistrements
            foreach ($records as $offset => $record) {
                try {
                    // Sanitisation avancée avec préservation des données critiques
                    $sanitizedRecord = $this->sanitizeRecord($record);
                    $data = $this->prepareDataForValidation($sanitizedRecord);

                    // Validation enterprise avec rules strictes
                    $validator = Validator::make($data, $this->getValidationRules());

                    if ($validator->fails()) {
                        $validationErrors = $validator->errors()->all();

                        // Log détaillé des erreurs de validation
                        Log::warning('Driver import validation failed', [
                            'import_id' => $importId,
                            'line_number' => $offset + 1,
                            'validation_errors' => $validationErrors,
                            'record_data' => $sanitizedRecord,
                            'timestamp' => now()->toISOString()
                        ]);

                        $errorRows[] = [
                            'line' => $offset + 1,
                            'errors' => $validationErrors,
                            'data' => $record,
                            'severity' => 'validation',
                            'timestamp' => now()->format('H:i:s')
                        ];
                        continue;
                    }

                    // Préparation des données validées pour insertion
                    $validatedData = $validator->validated();
                    $statusName = $validatedData['statut'] ?? null;
                    $statusId = $statuses->get($statusName, $defaultStatusId);

                    unset($validatedData['statut']);
                    $validatedData['status_id'] = $statusId;

                    // Ajout des métadonnées enterprise
                    $validatedData['organization_id'] = auth()->user()->organization_id;

                    // Création du chauffeur avec traçabilité complète
                    $newDriver = Driver::create($validatedData);
                    $successCount++;

                    // Log de succès avec détails complets
                    Log::info('Driver imported successfully', [
                        'import_id' => $importId,
                        'line_number' => $offset + 1,
                        'driver_id' => $newDriver->id,
                        'driver_name' => $newDriver->first_name . ' ' . $newDriver->last_name,
                        'employee_number' => $newDriver->employee_number,
                        'created_by' => auth()->id(),
                        'organization_id' => $newDriver->organization_id,
                        'timestamp' => now()->toISOString()
                    ]);

                } catch (QueryException $e) {
                    $databaseError = $this->formatDatabaseError($e);

                    // Log détaillé des erreurs de base de données
                    Log::error('Driver import database error', [
                        'import_id' => $importId,
                        'line_number' => $offset + 1,
                        'error_code' => $e->getCode(),
                        'error_message' => $e->getMessage(),
                        'formatted_error' => $databaseError,
                        'record_data' => $sanitizedRecord ?? $record,
                        'timestamp' => now()->toISOString()
                    ]);

                    $errorRows[] = [
                        'line' => $offset + 1,
                        'errors' => [$databaseError],
                        'data' => $record,
                        'severity' => 'database',
                        'error_code' => $e->getCode(),
                        'timestamp' => now()->format('H:i:s')
                    ];

                } catch (\Exception $e) {
                    $criticalError = sprintf(
                        "Erreur critique: %s dans %s ligne %d",
                        $e->getMessage(),
                        basename($e->getFile()),
                        $e->getLine()
                    );

                    // Log critique avec stack trace complet
                    Log::critical('Driver import critical error', [
                        'import_id' => $importId,
                        'line_number' => $offset + 1,
                        'error_message' => $e->getMessage(),
                        'error_file' => $e->getFile(),
                        'error_line' => $e->getLine(),
                        'stack_trace' => $e->getTraceAsString(),
                        'record_data' => $record,
                        'timestamp' => now()->toISOString()
                    ]);

                    $errorRows[] = [
                        'line' => $offset + 1,
                        'errors' => [$criticalError],
                        'data' => $record,
                        'severity' => 'critical',
                        'timestamp' => now()->format('H:i:s')
                    ];
                }
            }

            // 📊 Calcul des statistiques finales et traçabilité complète
            $endTime = microtime(true);
            $processingTime = round(($endTime - $startTime) * 1000, 2); // en millisecondes
            $errorCount = count($errorRows);
            $totalProcessed = $successCount + $errorCount;
            $successRate = $totalProcessed > 0 ? round(($successCount / $totalProcessed) * 100, 2) : 0;

            // Log final avec statistiques enterprise complètes
            Log::info('Driver import process completed', [
                'operation' => 'driver_import_complete',
                'import_id' => $importId,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'total_records' => count($records),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'success_rate_percent' => $successRate,
                'processing_time_ms' => $processingTime,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'organization_id' => auth()->user()->organization_id,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toISOString()
            ]);

            // Redirection vers les résultats avec données enrichies
            return redirect()->route('admin.drivers.import.results')
                ->with('successCount', $successCount)
                ->with('errorRows', $errorRows)
                ->with('importId', $importId)
                ->with('fileName', $file->getClientOriginalName())
                ->with('fileSize', $file->getSize())
                ->with('processingTime', $processingTime)
                ->with('successRate', $successRate)
                ->with('totalRecords', count($records));
                
        } catch (\Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            // Classification des erreurs pour des messages plus précis
            $errorCategory = 'unknown';
            $userFriendlyMessage = $e->getMessage();

            if (str_contains($e->getMessage(), 'En-têtes')) {
                $errorCategory = 'headers';
                $userFriendlyMessage = "Problème avec les en-têtes du fichier CSV. " . $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'format') || str_contains($e->getMessage(), 'CSV')) {
                $errorCategory = 'format';
                $userFriendlyMessage = "Format de fichier invalide. " . $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'duplicate')) {
                $errorCategory = 'duplicates';
                $userFriendlyMessage = "Doublons détectés dans le fichier. " . $e->getMessage();
            }

            $detailedError = sprintf(
                "Erreur d'importation (%s): %s",
                $errorCategory,
                $userFriendlyMessage
            );

            Log::critical("Erreur critique lors de l'importation chauffeurs", [
                'operation' => 'driver_import_failed',
                'import_id' => $importId,
                'error_category' => $errorCategory,
                'error_message' => $e->getMessage(),
                'error_file' => basename($e->getFile()),
                'error_line' => $e->getLine(),
                'processing_time_ms' => $processingTime,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'organization_id' => auth()->user()->organization_id,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toISOString(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.drivers.import.show')
                ->with('error', $detailedError)
                ->with('errorCategory', $errorCategory)
                ->with('processingTime', $processingTime)
                ->withInput();
        }
    }
    
    /**
     * Affiche les résultats de l'importation.
     */
    public function showImportResults(): View
    {
        $this->authorize('create drivers');
        
        $successCount = session('successCount', 0);
        $errorRows = session('errorRows', []);
        $importId = session('importId', null);
        $fileName = session('fileName', 'Fichier CSV');
        $encoding = session('encoding', 'utf8');
        
        return view('admin.drivers.import-results', compact(
            'successCount', 
            'errorRows', 
            'importId', 
            'fileName', 
            'encoding'
        ));
    }

    /**
     * 🗄️ Affiche les chauffeurs archivés avec interface enterprise
     */
    public function archived(Request $request): View
    {
        try {
            // Log de l'accès aux archives avec traçabilité complète
            Log::info('Driver archives accessed', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'organization_id' => auth()->user()->organization_id ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
                'filters' => $request->query()
            ]);

            // Récupération des chauffeurs archivés avec filtrage par organisation
            $query = Driver::onlyTrashed()
                ->with(['driverStatus', 'user']);

            // Filtrage par organisation pour les non-Super Admin
            if (!auth()->user()->hasRole('Super Admin')) {
                $query->where('organization_id', auth()->user()->organization_id);
            }

            $drivers = $query->orderBy('deleted_at', 'desc')->paginate(20);

            // Statistiques des archives avec traçabilité
            $stats = [
                'total_archived' => Driver::onlyTrashed()->count(),
                'archived_this_month' => Driver::onlyTrashed()
                    ->whereMonth('deleted_at', now()->month)
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
                'archived_this_year' => Driver::onlyTrashed()
                    ->whereYear('deleted_at', now()->year)
                    ->count(),
                'organization_archived' => auth()->user()->hasRole('Super Admin')
                    ? Driver::onlyTrashed()->count()
                    : Driver::onlyTrashed()->where('organization_id', auth()->user()->organization_id)->count()
            ];

            // Log des statistiques consultées
            Log::info('Driver archives statistics accessed', [
                'user_id' => auth()->id(),
                'stats' => $stats,
                'timestamp' => now()->toISOString()
            ]);

            return view('admin.drivers.archived', compact('drivers', 'stats'));

        } catch (\Exception $e) {
            // Log d'erreur avec contexte complet
            Log::error('Driver archives access error', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString()
            ]);

            return redirect()
                ->route('admin.drivers.index')
                ->withErrors(['error' => 'Erreur lors de l\'accès aux archives des chauffeurs.']);
        }
    }

    /**
     * Détecte l'encodage d'un contenu de fichier.
     */
    private function detectEncoding(string $content): string
    {
        // Liste des encodages à tester dans l'ordre de priorité
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252'];
        
        foreach ($encodings as $encoding) {
            $sample = mb_convert_encoding($content, 'UTF-8', $encoding);
            
            // Si la conversion ne génère pas de caractères invalides, c'est probablement le bon encodage
            if (!preg_match('/\p{Cc}(?!\n|\t|\r)/u', $sample)) {
                switch ($encoding) {
                    case 'UTF-8': return 'utf8';
                    case 'ISO-8859-1': return 'iso';
                    case 'Windows-1252': return 'windows';
                }
            }
        }
        
        // Par défaut, on suppose Windows-1252 (encodage courant pour les CSV générés par Excel)
        return 'windows';
    }
    
    /**
     * Convertit un contenu vers UTF-8 depuis un encodage spécifié.
     */
    private function convertToUtf8(string $content, string $fromEncoding): string
    {
        $sourceEncoding = match($fromEncoding) {
            'iso' => 'ISO-8859-1',
            'windows' => 'Windows-1252',
            default => 'UTF-8'
        };
        
        return mb_convert_encoding($content, 'UTF-8', $sourceEncoding);
    }
    
    /**
     * Nettoie les données d'un enregistrement CSV.
     */
    private function sanitizeRecord(array $record): array
    {
        $sanitized = [];
        
        foreach ($record as $key => $value) {
            // Nettoyage des clés (y compris le BOM UTF-8)
            $cleanKey = trim($key);
            if (str_starts_with($cleanKey, "\xef\xbb\xbf")) {
                $cleanKey = substr($cleanKey, 3);
            }
            
            // Nettoyage des valeurs
            if (is_string($value)) {
                // Suppression des caractères invisibles et normalisation des espaces
                $cleanValue = trim(preg_replace('/\s+/', ' ', $value));
                
                // Conversion des chaînes vides en NULL
                $sanitized[$cleanKey] = $cleanValue === '' ? null : $cleanValue;
            } else {
                $sanitized[$cleanKey] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Detects the format of the import file based on its content.
     *
     * @param string $content
     * @return string 'csv', 'key_value', or 'unknown'
     */
    private function detectFileFormat(string $content): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $firstNonEmptyLine = '';
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $firstNonEmptyLine = $line;
                break;
            }
        }

        if ($firstNonEmptyLine === '') {
            return 'unknown';
        }

        if (str_contains($firstNonEmptyLine, ',')) {
            return 'csv';
        }

        if (str_contains($firstNonEmptyLine, ':')) {
            return 'key_value';
        }

        return 'unknown';
    }

    /**
     * Parses a custom key:value format from a string.
     * Records are separated by empty lines.
     *
     * @param string $content
     * @return array
     */
    private function parseCustomFormat(string $content): array
    {
        $records = [];
        $currentRecord = [];
        $lines = preg_split("/\r\n|\n|\r/", $content);

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                if (!empty($currentRecord)) {
                    $records[] = $currentRecord;
                    $currentRecord = [];
                }
                continue;
            }

            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $currentRecord[$key] = $value;
            }
        }

        if (!empty($currentRecord)) {
            $records[] = $currentRecord;
        }

        return $records;
    }

    /**
     * Parses a standard CSV string into an array of records.
     *
     * @param string $content
     * @return array
     */
    /**
     * 🚀 Parse le format CSV standard avec détection intelligente des en-têtes Enterprise
     * Version Ultra-Robuste avec gestion des lignes de commentaires et BOM
     */
    private function parseCsvFormat(string $content): array
    {
        try {
            // Nettoyage avancé du contenu
            $cleanContent = $this->cleanCsvContent($content);

            $csv = \League\Csv\Reader::createFromString($cleanContent);
            $csv->setDelimiter(',');
            $csv->setEnclosure('"');
            $csv->setEscape('\\');

            // Détection intelligente des en-têtes réels avec nouveau algorithme
            $headerData = $this->detectAndValidateHeaders($cleanContent);
            $headerOffset = $headerData['offset'];
            $detectedHeaders = $headerData['headers'];

            Log::info('CSV header detection enterprise', [
                'detected_header_offset' => $headerOffset,
                'detected_headers' => $detectedHeaders,
                'content_preview' => substr($cleanContent, 0, 200)
            ]);

            $csv->setHeaderOffset($headerOffset);

            // Validation finale avec les en-têtes détectés
            $csvHeaders = $csv->getHeader();
            $this->validateHeadersEnterprise($csvHeaders, $detectedHeaders);

            $records = \League\Csv\Statement::create()->process($csv);

            return iterator_to_array($records, true);

        } catch (\League\Csv\SyntaxError $e) {
            Log::error('CSV parsing syntax error', [
                'error' => $e->getMessage(),
                'content_preview' => substr($content, 0, 500)
            ]);
            throw new \Exception('Erreur de format CSV: ' . $e->getMessage() . '. Vérifiez que votre fichier respecte le format du template.');
        } catch (\Exception $e) {
            Log::error('CSV parsing general error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erreur lors du traitement du fichier: ' . $e->getMessage());
        }
    }

    /**
     * Prépare les données pour la validation.
     */
    private function prepareDataForValidation(array $record): array
    {
        $map = [
            'nom' => 'last_name', 
            'prenom' => 'first_name', 
            'date_naissance' => 'birth_date',
            'statut' => 'statut', 
            'matricule' => 'employee_number', 
            'telephone' => 'personal_phone',
            'email_personnel' => 'personal_email', 
            'adresse' => 'address', 
            'numero_permis' => 'license_number',
            'categorie_permis' => 'license_category', 
            'date_delivrance_permis' => 'license_issue_date',
            'autorite_delivrance' => 'license_authority', 
            'date_recrutement' => 'recruitment_date',
            'date_fin_contrat' => 'contract_end_date', 
            'contact_urgence_nom' => 'emergency_contact_name',
            'contact_urgence_telephone' => 'emergency_contact_phone', 
            'groupe_sanguin' => 'blood_type',
        ];
        
        $dataToValidate = [];
        
        foreach ($map as $csvHeader => $dbField) {
            if (array_key_exists($csvHeader, $record)) {
                $value = $record[$csvHeader];

                // Convert empty strings to null so they pass 'nullable' validation
                // and are correctly stored in the database.
                if ($value === '') {
                    $value = null;
                }
                
                // Traitement spécifique pour les dates
                if (in_array($dbField, ['birth_date', 'license_issue_date', 'recruitment_date', 'contract_end_date']) && $value) {
                    $value = $this->formatDate($value);
                }
                
                $dataToValidate[$dbField] = $value;
            }
        }
        
        return $dataToValidate;
    }
    
    /**
     * Formate une date en format YYYY-MM-DD.
     */
    private function formatDate($dateString)
    {
        if (!$dateString) return null;

        // Si la date est déjà au format YYYY-MM-DD, on la retourne directement.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        $formats = [
            'd/m/Y', 'd-m-Y', 'd.m.Y',
            'Y/m/d', 'Y-m-d', 'Y.m.d',
            'd/m/y', 'd-m-y', 'd.m.y'
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);

            // This is a robust check. If the date was created successfully AND
            // re-formatting it with the same format produces the original string,
            // then the parse was successful and unambiguous.
            if ($date instanceof \DateTime && $date->format($format) === $dateString) {
                return $date->format('Y-m-d');
            }
        }

        // Si aucun format n'a fonctionné, on retourne la chaîne originale pour qu'elle échoue à la validation 'date_format:Y-m-d'
        // et retourne un message d'erreur clair à l'utilisateur.
        return $dateString;
    }
    
    /**
     * Formate une erreur de base de données de manière conviviale.
     */
    private function formatDatabaseError(QueryException $e): string
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();

        // Erreurs de contrainte d'unicité (PostgreSQL: 23505)
        if ($errorCode == 23505) {
            if (str_contains($errorMessage, 'drivers_employee_number_unique')) {
                return "Le matricule fourni existe déjà.";
            }
            if (str_contains($errorMessage, 'drivers_personal_email_unique')) {
                return "L'adresse email fournie existe déjà.";
            }
            if (str_contains($errorMessage, 'drivers_license_number_unique')) {
                return "Le numéro de permis fourni existe déjà.";
            }
            // Essayer d'extraire les détails de la clé pour un message plus générique
            if (preg_match('/Key \((.*?)\)=\((.*?)\) already exists./', $errorMessage, $matches)) {
                $column = $matches[1];
                $value = $matches[2];
                return "La valeur '$value' existe déjà pour le champ '$column'.";
            }
            return "Une valeur unique existe déjà dans la base de données.";
        }

        // Erreurs de type de données (PostgreSQL: 22P02, 22007)
        if ($errorCode == '22P02' || $errorCode == '22007') {
            if (str_contains($errorMessage, 'date') || str_contains($errorMessage, 'time')) {
                return "Format de date invalide. Le format attendu est AAAA-MM-JJ.";
            }
            return "Format de données invalide pour l'une des colonnes.";
        }

        // Erreurs de contrainte de clé étrangère (PostgreSQL: 23503)
        if ($errorCode == 23503) {
            return "Référence à une valeur qui n'existe pas (ex: statut de chauffeur invalide).";
        }

        // Erreur par défaut
        return "Erreur de base de données: " . Str::limit($e->getMessage(), 150);
    }
    
    /**
     * Règles de validation pour l'importation de chauffeurs.
     */
    private function getValidationRules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date_format:Y-m-d'],
            'statut' => ['nullable', 'string'],
            'employee_number' => ['nullable', 'string', 'max:100', 'unique:drivers,employee_number,NULL,id,deleted_at,NULL'],
            'personal_phone' => ['nullable', 'string', 'max:50'],
            'personal_email' => ['nullable', 'email', 'max:255', 'unique:drivers,personal_email,NULL,id,deleted_at,NULL'],
            'address' => ['nullable', 'string', 'max:1000'],
            'license_number' => ['nullable', 'string', 'max:100', 'unique:drivers,license_number,NULL,id,deleted_at,NULL'],
            'license_category' => ['nullable', 'string', 'max:50'],
            'license_issue_date' => ['nullable', 'date_format:Y-m-d'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'recruitment_date' => ['nullable', 'date_format:Y-m-d'],
            'contract_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:recruitment_date'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:10'],
        ];
    }

    /**
     * 🧹 Nettoie le contenu CSV des caractères problématiques
     */
    private function cleanCsvContent(string $content): string
    {
        // Suppression du BOM UTF-8
        if (str_starts_with($content, "\xef\xbb\xbf")) {
            $content = substr($content, 3);
        }

        // Normalisation des fins de ligne
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Suppression des caractères de contrôle dangereux
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

        return trim($content);
    }

    /**
     * 🎯 Détection et validation ultra-robuste des en-têtes Enterprise
     * Algorithme avancé avec analyse complète du contenu
     */
    private function detectAndValidateHeaders(string $content): array
    {
        // Analyse ligne par ligne du contenu
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $requiredHeaders = ['nom', 'prenom', 'date_naissance'];

        Log::info('Header detection start', [
            'total_lines' => count($lines),
            'required_headers' => $requiredHeaders
        ]);

        // Analyse des 15 premières lignes pour plus de robustesse
        for ($offset = 0; $offset < min(15, count($lines)); $offset++) {
            $line = trim($lines[$offset]);

            // Ignore les lignes vides et les commentaires
            if (empty($line) || str_starts_with($line, '#')) {
                Log::debug('Skipping line', ['offset' => $offset, 'reason' => 'empty_or_comment']);
                continue;
            }

            // Parse la ligne comme CSV
            $headers = str_getcsv($line);

            if (count($headers) < 3) {
                Log::debug('Skipping line', ['offset' => $offset, 'reason' => 'insufficient_columns', 'count' => count($headers)]);
                continue;
            }

            // Nettoyage et normalisation des en-têtes
            $cleanHeaders = array_map(function($header) {
                return strtolower(trim($header));
            }, $headers);

            // Comptage des correspondances exactes
            $matchCount = 0;
            $foundHeaders = [];

            foreach ($requiredHeaders as $required) {
                if (in_array($required, $cleanHeaders)) {
                    $matchCount++;
                    $foundHeaders[] = $required;
                }
            }

            Log::info('Header analysis', [
                'offset' => $offset,
                'raw_headers' => $headers,
                'clean_headers' => $cleanHeaders,
                'match_count' => $matchCount,
                'found_headers' => $foundHeaders
            ]);

            // Si on trouve tous les en-têtes requis
            if ($matchCount === count($requiredHeaders)) {
                Log::info('Perfect header match found', [
                    'offset' => $offset,
                    'headers' => $cleanHeaders
                ]);

                return [
                    'offset' => $offset,
                    'headers' => $cleanHeaders,
                    'raw_headers' => $headers,
                    'match_count' => $matchCount
                ];
            }

            // Si on trouve au moins 2 des 3 en-têtes critiques
            if ($matchCount >= 2) {
                Log::info('Partial header match found', [
                    'offset' => $offset,
                    'match_count' => $matchCount,
                    'headers' => $cleanHeaders
                ]);

                return [
                    'offset' => $offset,
                    'headers' => $cleanHeaders,
                    'raw_headers' => $headers,
                    'match_count' => $matchCount
                ];
            }
        }

        // Aucun en-tête valide trouvé
        Log::error('No valid headers detected in file');
        throw new \Exception('Aucun en-tête valide détecté. Le fichier doit contenir au minimum les colonnes: ' . implode(', ', $requiredHeaders));
    }

    /**
     * ✅ Validation enterprise ultra-robuste des en-têtes
     * Double vérification avec les données détectées
     */
    private function validateHeadersEnterprise(array $csvHeaders, array $detectedHeaders): void
    {
        // Nettoyage des en-têtes CSV
        $cleanCsvHeaders = array_map(function($header) {
            return strtolower(trim($header));
        }, array_filter($csvHeaders, function($header) {
            return !empty(trim($header)) && !str_starts_with(trim($header), '#');
        }));

        Log::info('Header validation enterprise', [
            'csv_headers' => $csvHeaders,
            'clean_csv_headers' => $cleanCsvHeaders,
            'detected_headers' => $detectedHeaders
        ]);

        // Vérification des doublons avec algorithme avancé
        $headerCounts = array_count_values($cleanCsvHeaders);
        $duplicates = array_filter($headerCounts, function($count) {
            return $count > 1;
        });

        if (!empty($duplicates)) {
            $duplicateList = implode(', ', array_keys($duplicates));
            Log::error('Duplicate headers detected', ['duplicates' => $duplicates]);
            throw new \Exception('En-têtes dupliqués détectés: ' . $duplicateList . '. Vérifiez votre fichier CSV.');
        }

        // Vérification des en-têtes obligatoires avec double contrôle
        $requiredHeaders = ['nom', 'prenom', 'date_naissance'];
        $missingHeaders = [];

        foreach ($requiredHeaders as $required) {
            // Double vérification: dans les en-têtes CSV ET dans les en-têtes détectés
            $foundInCsv = in_array($required, $cleanCsvHeaders);
            $foundInDetected = in_array($required, $detectedHeaders);

            Log::debug('Header validation check', [
                'required' => $required,
                'found_in_csv' => $foundInCsv,
                'found_in_detected' => $foundInDetected
            ]);

            if (!$foundInCsv && !$foundInDetected) {
                $missingHeaders[] = $required;
            }
        }

        if (!empty($missingHeaders)) {
            Log::error('Missing required headers', [
                'missing' => $missingHeaders,
                'csv_headers' => $cleanCsvHeaders,
                'detected_headers' => $detectedHeaders
            ]);
            throw new \Exception('En-têtes obligatoires manquants: ' . implode(', ', $missingHeaders) . '. Vérifiez que votre fichier contient les colonnes requises.');
        }

        Log::info('Header validation successful', [
            'required_headers' => $requiredHeaders,
            'found_headers' => $cleanCsvHeaders
        ]);
    }
}