<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Admin\UpdateVehicleRequest;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use App\Services\ImportExportService;
use App\Services\VehicleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;


class VehicleController extends Controller
{
    protected $vehicleService;
    protected $importExportService;

    /**
     * Constructeur avec injection des services
     */
    public function __construct(VehicleService $vehicleService, ImportExportService $importExportService)
    {
        $this->vehicleService = $vehicleService;
        $this->importExportService = $importExportService;
    }

    /**
     * Affiche la liste des véhicules
     */
    public function index(Request $request)
    {
        // Utilisation de la permission spécifique 'view vehicles' comme dans DriverController
        $this->authorize('view vehicles');

        $filters = $request->only(['search', 'status_id', 'view_deleted']);
        $vehicles = $this->vehicleService->getFilteredVehicles($filters);

        // Correction: Passer la variable sous le nom attendu par la vue
        $vehicleStatuses = VehicleStatus::all();

        return view('admin.vehicles.index', compact('vehicles', 'vehicleStatuses', 'filters'));
    }


    /**
     * Méthode privée pour récupérer les utilisateurs assignables
     * en fonction du rôle de l'utilisateur connecté.
     */
    private function getAssignableUsers()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Le Super Admin peut voir tous les utilisateurs
            return User::orderBy('name')->get();
        }

        // Un Admin ne voit que les utilisateurs de sa propre organisation
        return User::where('organization_id', $user->organization_id)->orderBy('name')->get();
    }

    
    /**
     * Affiche le formulaire de création d'un véhicule
     */
    public function create()
    {
        // Utilisation de la permission spécifique 'create vehicles'
        $this->authorize('create vehicles');

        $users = $this->getAssignableUsers();
        //$users = User::orderBy('name')->get();
        $vehicleTypes = VehicleType::all();
        $fuelTypes = FuelType::all();
        $transmissionTypes = TransmissionType::all();
        $vehicleStatuses = VehicleStatus::all();

        return view('admin.vehicles.create', compact('users','vehicleTypes', 'fuelTypes', 'transmissionTypes', 'vehicleStatuses'));
    }

    /**
     * Enregistre un nouveau véhicule
     */
    public function store(Request $request)
    {
        // Utilisation de la permission spécifique 'create vehicles'
        $this->authorize('create vehicles');

        try {
            $validated = $request->validate([
                'registration_plate' => 'required|string|max:20',
                'vin' => 'nullable|string|max:50',
                'brand' => 'required|string|max:50',
                'model' => 'required|string|max:50',
                'color' => 'nullable|string|max:30',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'fuel_type_id' => 'required|exists:fuel_types,id',
                'transmission_type_id' => 'required|exists:transmission_types,id',
                'status_id' => 'required|exists:vehicle_statuses,id',
                'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'acquisition_date' => 'nullable|date',
                'purchase_price' => 'nullable|numeric|min:0',
                'current_value' => 'nullable|numeric|min:0',
                'initial_mileage' => 'nullable|integer|min:0',
                'engine_capacity' => 'nullable|integer|min:0',
                'power' => 'nullable|integer|min:0',
                'seats' => 'nullable|integer|min:0',
                'notes' => 'nullable|string',
                'photo' => 'nullable|image|max:2048',
                'users' => 'nullable|array',
                'users.*' => 'exists:users,id',
            ]);

            // Créer le véhicule
            $vehicle = $this->vehicleService->createVehicle(
                $validated,
                $request->file('photo'),
                $request->input('users', [])
            );

            return redirect()->route('admin.vehicles.index')
                ->with('success', __('vehicles.messages.create_success'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du véhicule', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création du véhicule.');
        }
    }

    /**
     * Affiche les détails d'un véhicule
     */
    public function show(Vehicle $vehicle)
    {
        // Utilisation de la permission spécifique 'view vehicles'
        $this->authorize('view vehicles');

        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Affiche le formulaire d'édition d'un véhicule
     */
    public function edit(Vehicle $vehicle)
    {
        // Utilisation de la permission spécifique 'edit vehicles'
        $this->authorize('edit vehicles');

        $users = $this->getAssignableUsers();
        //$users = User::orderBy('name')->get();
        $vehicleTypes = VehicleType::all();
        $fuelTypes = FuelType::all();
        $transmissionTypes = TransmissionType::all();
        $vehicleStatuses = VehicleStatus::all();

        return view('admin.vehicles.edit', compact('vehicle', 'users', 'vehicleTypes', 'fuelTypes', 'transmissionTypes', 'vehicleStatuses'));
    }

    /**
     * Met à jour un véhicule
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Utilisation de la permission spécifique 'edit vehicles'
        $this->authorize('edit vehicles');

        try {
            $validated = $request->validate([
                'registration_plate' => 'required|string|max:20',
                'vin' => 'nullable|string|max:50',
                'brand' => 'required|string|max:50',
                'model' => 'required|string|max:50',
                'color' => 'nullable|string|max:30',
                'vehicle_type_id' => 'required|exists:vehicle_types,id',
                'fuel_type_id' => 'required|exists:fuel_types,id',
                'transmission_type_id' => 'required|exists:transmission_types,id',
                'status_id' => 'required|exists:vehicle_statuses,id',
                'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'acquisition_date' => 'nullable|date',
                'purchase_price' => 'nullable|numeric|min:0',
                'current_value' => 'nullable|numeric|min:0',
                'initial_mileage' => 'nullable|integer|min:0',
                'engine_capacity' => 'nullable|integer|min:0',
                'power' => 'nullable|integer|min:0',
                'seats' => 'nullable|integer|min:0',
                'notes' => 'nullable|string',
                'photo' => 'nullable|image|max:2048',
                'users' => 'nullable|array',
                'users.*' => 'exists:users,id',
            ]);

            // Mettre à jour le véhicule
            $this->vehicleService->updateVehicle(
                $vehicle,
                $validated,
                $request->file('photo'),
                $request->input('users', [])
            );

            return redirect()->route('admin.vehicles.index')
                ->with('success', __('vehicles.messages.update_success'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du véhicule', [
                'vehicle_id' => $vehicle->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour du véhicule.');
        }
    }

    /**
     * Archive un véhicule (soft delete)
     */
    public function destroy(Vehicle $vehicle)
    {
        // Utilisation de la permission spécifique 'delete vehicles'
        $this->authorize('delete vehicles');

        try {
            $this->vehicleService->archiveVehicle($vehicle);

            return redirect()->route('admin.vehicles.index')
                ->with('success', __('vehicles.messages.archive_success'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'archivage du véhicule', [
                'vehicle_id' => $vehicle->id,
                'exception' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de l\'archivage du véhicule.');
        }
    }

    /**
     * Restaure un véhicule archivé
     */
    public function restore($id)
    {
        // Utilisation de la permission spécifique 'restore vehicles'
        $this->authorize('restore vehicles');

        try {
            $this->vehicleService->restoreVehicle($id);

            return redirect()->route('admin.vehicles.index')
                ->with('success', __('vehicles.messages.restore_success'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration du véhicule', [
                'vehicle_id' => $id,
                'exception' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la restauration du véhicule.');
        }
    }

    /**
     * Supprime définitivement un véhicule
     */
    public function forceDelete($id)
    {
        // Utilisation de la permission spécifique 'force delete vehicles'
        $this->authorize('force delete vehicles');

        try {
            $this->vehicleService->forceDeleteVehicle($id);

            return redirect()->route('admin.vehicles.index')
                ->with('success', __('vehicles.messages.delete_success'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression définitive du véhicule', [
                'vehicle_id' => $id,
                'exception' => $e->getMessage()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression définitive du véhicule.');
        }
    }

    /**
     * Affiche la page d'importation de véhicules
     */
    public function showImportForm()
    {
        // Utilisation de la permission spécifique 'create vehicles' pour l'importation
        $this->authorize('create vehicles');

        return view('admin.vehicles.import');
    }

    /**
     * Traite l'importation de véhicules depuis un fichier CSV
     */
     public function handleImport(Request $request): RedirectResponse
    {
        $this->authorize('create vehicles');
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt']]);

        // Pré-charger les données de référence (case-insensitive)
        $vehicleTypes = VehicleType::all()->keyBy(fn($item) => strtolower($item->name));
        $fuelTypes = FuelType::all()->keyBy(fn($item) => strtolower($item->name));
        $transmissionTypes = TransmissionType::all()->keyBy(fn($item) => strtolower($item->name));
        $vehicleStatuses = VehicleStatus::all()->keyBy(fn($item) => strtolower($item->name));

        $path = $request->file('csv_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv);

        $successCount = 0;
        $errorRows = [];
        $rowNumber = 1;

        foreach ($records as $record) {
            $rowNumber++;
            $data = array_map('trim', $record);

            // Conversion des noms en ID et formatage des données
            $preparedData = [
                'registration_plate' => $data['immatriculation'] ?? null,
                'vin' => ($data['numero_serie_vin'] === '') ? null : ($data['numero_serie_vin'] ?? null),
                'brand' => $data['marque'] ?? null,
                'model' => $data['modele'] ?? null,
                'color' => $data['couleur'] ?? null,
                'manufacturing_year' => $this->importExportService->formatInteger($data['annee_fabrication'] ?? null),
                'acquisition_date' => $this->importExportService->formatDate($data['date_acquisition'] ?? null),
                'purchase_price' => $this->importExportService->formatDecimal($data['prix_achat'] ?? null),
                'current_value' => $this->importExportService->formatDecimal($data['valeur_actuelle'] ?? null),
                'initial_mileage' => $this->importExportService->formatInteger($data['kilometrage_initial'] ?? null),
                'engine_displacement_cc' => $this->importExportService->formatInteger($data['cylindree_cc'] ?? null),
                'power_hp' => $this->importExportService->formatInteger($data['puissance_cv'] ?? null),
                'seats' => $this->importExportService->formatInteger($data['nombre_places'] ?? null),
                'notes' => $data['notes'] ?? null,
                'vehicle_type_id' => $vehicleTypes->get(strtolower($data['type_vehicule'] ?? ''))?->id,
                'fuel_type_id' => $fuelTypes->get(strtolower($data['type_carburant'] ?? ''))?->id,
                'transmission_type_id' => $transmissionTypes->get(strtolower($data['type_transmission'] ?? ''))?->id,
                'status_id' => $vehicleStatuses->get(strtolower($data['statut'] ?? ''))?->id,
            ];
            
            // Utilisation du FormRequest pour la validation pour la cohérence
            $validator = Validator::make($preparedData, (new StoreVehicleRequest())->rules());

            if ($validator->fails()) {
                $errorRows[] = ['line' => $rowNumber, 'errors' => $validator->errors()->all(), 'data' => $record];
            } else {
                try {
                    $validatedData = $validator->validated();
                    $validatedData["current_mileage"] = $validatedData["initial_mileage"] ?? 0;
                    // L'organization_id est ajouté automatiquement via le trait BelongsToOrganization
                    Vehicle::create($validatedData);
                    $successCount++;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $errorRows[] = ['line' => $rowNumber, 'errors' => ['Le numéro de série (VIN) ou l\'immatriculation est déjà utilisé.'], 'data' => $record];
                } catch (\Exception $e) {
                    Log::error("Erreur d'importation de véhicule à la ligne {$rowNumber}: " . $e->getMessage());
                    $errorRows[] = ['line' => $rowNumber, 'errors' => ['Une erreur inattendue est survenue: ' . $e->getMessage()], 'data' => $record];
                }
            }
        }

        // Redirection vers la page de résultats
        return redirect()->route('admin.vehicles.import.results')
            ->with('successCount', $successCount)
            ->with('errorRows', $errorRows)
            ->with('importId', uniqid())
            ->with('fileName', $request->file('csv_file')->getClientOriginalName());
    }

    /**
     * Affiche les résultats de l'importation
     */
    public function showImportResults(Request $request)
    {
        // Utilisation de la permission spécifique 'create vehicles' pour voir les résultats d'importation
        $this->authorize('create vehicles');

        // Récupérer les résultats de l'importation depuis la session
        $successCount = session('successCount', 0);
        $errorRows = session('errorRows', []);
        $importId = session('importId', null);
        $fileName = session('fileName', 'Fichier CSV');
        $encoding = session('encoding', 'utf8');

        // Si aucun résultat n'est disponible, rediriger vers la page d'importation
        if ($successCount === 0 && empty($errorRows)) {
            return redirect()->route('admin.vehicles.import.show')
                ->with('error', 'Aucun résultat d\'importation disponible. Veuillez importer un fichier.');
        }

        return view('admin.vehicles.import-results', compact(
            'successCount',
            'errorRows',
            'importId',
            'fileName',
            'encoding'
        ));
    }

    /**
     * Exporte les lignes en erreur vers un fichier CSV
     */
    public function exportErrors(Request $request, $import_id)
    {
        // Utilisation de la permission spécifique 'create vehicles' pour exporter les erreurs
        $this->authorize('create vehicles');

        // Récupérer les résultats de l'importation depuis la session
        $results = session('import_results');

        // Vérifier que les résultats correspondent à l'ID d'importation demandé
        if (!$results || $results['import_id'] !== $import_id) {
            return redirect()->route('admin.vehicles.import.show')
                ->with('error', 'Les données d\'erreur demandées ne sont plus disponibles.');
        }

        $errorRows = $results['error_rows'] ?? [];

        // Utiliser le service pour exporter les erreurs
        return $this->importExportService->exportErrorRows($errorRows, $import_id);
    }

    /**
     * Génère un fichier CSV modèle pour l'importation
     */
    public function downloadTemplate()
    {
        // Utilisation de la permission spécifique 'create vehicles' pour télécharger le template
        $this->authorize('create vehicles');

        // En-têtes du CSV
        $headers = [
            'immatriculation',
            'numero_serie_vin',
            'marque',
            'modele',
            'couleur',
            'type_vehicule',
            'type_carburant',
            'type_transmission',
            'statut',
            'annee_fabrication',
            'date_acquisition',
            'prix_achat',
            'valeur_actuelle',
            'kilometrage_initial',
            'cylindree_cc',
            'puissance_cv',
            'nombre_places',
            'notes'
        ];

        // Exemple de données
        $example = [
            'AB-123-CD',
            'VF1234567890ABCDE',
            'Renault',
            'Clio',
            'Bleu',
            'Berline',
            'Diesel',
            'Manuelle',
            'Parking',
            '2020',
            '2021-01-15',
            '15000',
            '12000',
            '10000',
            '1500',
            '90',
            '5',
            'Véhicule de service'
        ];

        // Utiliser le service pour générer le template
        return $this->importExportService->generateCsvTemplate(
            'modele_import_vehicules.csv',
            $headers,
            $example
        );
    }
}