<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use League\Csv\Reader;
use League\Csv\Statement;
// On importe nos nouvelles Form Requests
use App\Http\Requests\Admin\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Admin\Vehicle\UpdateVehicleRequest;





class VehicleController extends Controller
{
    /**
     * Affiche la liste des véhicules.
     */
    public function index(Request $request): View
    {
        $this->authorize('view vehicles');
        $perPage = $request->query('per_page', 15);
        $query = Vehicle::query()->with(['vehicleType', 'vehicleStatus']);

        // AJOUT : Logique pour voir les archives
        if ($request->query('view_deleted')) {
            $query->onlyTrashed();
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(registration_plate) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereRaw('LOWER(brand) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereRaw('LOWER(model) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereHas('vehicleType', function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']);
                });
            });
        }

        $vehicles = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();

        return view('admin.vehicles.index', [
            'vehicles' => $vehicles,
            'vehicleStatuses' => $vehicleStatuses,
            'filters' => $request->only(['search', 'status_id', 'per_page', 'view_deleted']),
        ]);
    }


        /**
     * Affiche le formulaire de création.
     */
    public function create(): View
    {
        $this->authorize('create vehicles');
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $transmissionTypes = TransmissionType::orderBy('name')->get();
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();
        return view('admin.vehicles.create', compact('vehicleTypes', 'fuelTypes', 'transmissionTypes', 'vehicleStatuses'));
    }


     /**
     * Enregistre un nouveau véhicule.
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->authorize('create vehicles');
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            $validatedData['photo_path'] = $request->file('photo')->store('vehicles/photos', 'public');
        }
        $validatedData['current_mileage'] = $validatedData['initial_mileage'];
        
        Vehicle::create($validatedData);
        return redirect()->route('admin.vehicles.index')->with('success', 'Nouveau véhicule ajouté avec succès.');
    }


    /**
     * Affiche une ressource spécifique.
     */
    public function show(Vehicle $vehicle)
    {
        $this->authorize('view vehicles');
        return redirect()->route('admin.vehicles.edit', $vehicle);
    }

        /**
     * Affiche le formulaire de modification.
     */
    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('edit vehicles');
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $transmissionTypes = TransmissionType::orderBy('name')->get();
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();
        return view('admin.vehicles.edit', compact('vehicle', 'vehicleTypes', 'fuelTypes', 'transmissionTypes', 'vehicleStatuses'));
    }


    /**
     * Met à jour un véhicule existant en utilisant une Form Request.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $validatedData = $request->validated();
        if ($request->hasFile('photo')) {
            if ($vehicle->photo_path) {
                Storage::disk('public')->delete($vehicle->photo_path);
            }
            $validatedData['photo_path'] = $request->file('photo')->store('vehicles/photos', 'public');
        }
        $vehicle->update($validatedData);
        return redirect()->route('admin.vehicles.index')->with('success', 'Les informations du véhicule ont été mises à jour.');
    }



  //////////////////////////// ARCHIVER SUPPRIMER ET RESTAURER

     /**
     * Archive un véhicule.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete vehicles');
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', "Le véhicule {$vehicle->registration_plate} a été archivé.");
    }

    /**
     * Restaure un véhicule archivé.
     */
    public function restore($vehicleId): RedirectResponse
    {
        $this->authorize('restore vehicles');
        $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);
        $vehicle->restore();
        return redirect()->route('admin.vehicles.index', ['view_deleted' => 'true'])->with('success', "Le véhicule {$vehicle->registration_plate} a été restauré.");
    }

    /**
     * Supprime définitivement un véhicule.
     */
    public function forceDelete($vehicleId): RedirectResponse
    {
        $this->authorize('force delete vehicles');
        $vehicle = Vehicle::onlyTrashed()->findOrFail($vehicleId);
        if ($vehicle->photo_path) {
            Storage::disk('public')->delete($vehicle->photo_path);
        }
        $vehicle->forceDelete();
        return redirect()->route('admin.vehicles.index', ['view_deleted' => 'true'])->with('success', 'Le véhicule a été supprimé définitivement.');
    }

    //////////////////////////  FIN DES METHODES DE SUPPRIMER RESTAURER

    
    /**
     * Affiche le formulaire pour l'importation de véhicules via un fichier CSV.
     */
    public function showImportForm(): View
    {
        $this->authorize('create vehicles');
        return view('admin.vehicles.import');
    }

    /**
     * Génère et télécharge un fichier CSV modèle avec des en-têtes en français et un exemple.
     */
    public function downloadTemplate()
    {
        $this->authorize('create vehicles');
        $headers = ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename="template_import_vehicules.csv"'];
        $columns = array_keys($this->getImportHeaderMap());
        $exampleRow = [
            'AA-123-BB', '1G1YB2D33E4F56789', 'Renault', 'Clio', 'Grise',
            'Berline', 'Diesel', 'Manuelle', 'En service',
            '2022', '15/01/2023', '2500000.00', '2400000.00',
            '15000', '1461', '90', '5', 'Véhicule de service pour le département commercial.'
        ];
        $callback = function() use ($columns, $exampleRow) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);
            fputcsv($file, $exampleRow);
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Traite le fichier CSV uploadé pour l'importation de véhicules.
     */
    public function handleImport(Request $request): RedirectResponse
    {
        $this->authorize('create vehicles');
        $request->validate(['csv_file' => ['required', 'file', 'mimes:csv,txt']]);

        $vehicleTypes = VehicleType::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $fuelTypes = FuelType::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $transmissionTypes = TransmissionType::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $vehicleStatuses = VehicleStatus::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);

        $headerMap = $this->getImportHeaderMap();

        $path = $request->file('csv_file')->getRealPath();
        $csv = Reader::createFromPath($path, 'r');
        $headerFromFile = array_map(fn($h) => trim(preg_replace('/^\\x{FEFF}/u', '', $h)), $csv->fetchOne());
        $csv->setHeaderOffset(0);
        $records = Statement::create()->process($csv, $headerFromFile);

        $successCount = 0;
        $errorRows = [];
        $rowNumber = 1;

        foreach ($records as $record) {
            $rowNumber++;

            $data = [];
            foreach ($headerMap as $frenchHeader => $systemKey) {
                $value = $record[$frenchHeader] ?? null;
                $data[$systemKey] = ($value === '' || $value === null) ? null : trim($value);
            }

            if (empty($data['vehicle_type_name'])) $data['vehicle_type_name'] = 'Berline';
            if (empty($data['transmission_type_name'])) $data['transmission_type_name'] = 'Manuelle';
            if (empty($data['status_name'])) $data['status_name'] = 'En attente';

            if (!empty($data['acquisition_date'])) {
                try {
                    $data['acquisition_date'] = Carbon::createFromFormat('d/m/Y', $data['acquisition_date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Laisser la date originale, le validateur Laravel échouera.
                }
            }

            $dataToValidate = $this->prepareDataForValidation($data, $vehicleTypes, $fuelTypes, $transmissionTypes, $vehicleStatuses);

            $validator = Validator::make($dataToValidate, $this->getValidationRules());

            if ($validator->fails()) {
                $errorRows[] = ['row_number' => $rowNumber, 'errors' => $validator->errors()->all(), 'data' => $record];
            } else {
                $validatedData = $validator->validated();
                $validatedData['current_mileage'] = $validatedData['initial_mileage'] ?? 0;

                Vehicle::create($validatedData);
                $successCount++;
            }
        }

        return redirect()->route('admin.vehicles.import.results')
            ->with('successCount', $successCount)
            ->with('errorRows', $errorRows);
    }

    /**
     * Affiche la page des résultats de l'importation.
     */
    public function showImportResults(): View
    {
        $successCount = session('successCount', 0);
        $errorRows = session('errorRows', []);
        return view('admin.vehicles.import-results', compact('successCount', 'errorRows'));
    }

    /**
     * Méthode privée pour centraliser la map des en-têtes CSV.
     */
    private function getImportHeaderMap(): array
    {
        return [
            'Immatriculation*' => 'registration_plate', 'N° de Série (VIN)' => 'vin',
            'Marque*' => 'brand', 'Modèle*' => 'model', 'Couleur' => 'color',
            'Type de Véhicule*' => 'vehicle_type_name', 'Type de Carburant*' => 'fuel_type_name',
            'Type de Transmission*' => 'transmission_type_name', 'Statut Initial*' => 'status_name',
            'Année de Fabrication' => 'manufacturing_year', 'Date d\'Acquisition' => 'acquisition_date',
            'Prix d\'Achat (DA)' => 'purchase_price', 'Valeur Actuelle (DA)' => 'current_value',
            'Kilométrage Initial' => 'initial_mileage', 'Cylindrée (cc)' => 'engine_displacement_cc',
            'Puissance (CV)' => 'power_hp', 'Nombre de Places' => 'seats', 'Notes' => 'notes',
        ];
    }

    /**
     * Prépare les données pour la validation.
     */
    private function prepareDataForValidation(array $data, $vehicleTypes, $fuelTypes, $transmissionTypes, $vehicleStatuses): array
    {
        $dataToValidate = $data;
        $dataToValidate['vehicle_type_id'] = $vehicleTypes[strtolower($data['vehicle_type_name'] ?? '')] ?? null;
        $dataToValidate['fuel_type_id'] = $fuelTypes[strtolower($data['fuel_type_name'] ?? '')] ?? null;
        $dataToValidate['transmission_type_id'] = $transmissionTypes[strtolower($data['transmission_type_name'] ?? '')] ?? null;
        $dataToValidate['status_id'] = $vehicleStatuses[strtolower($data['status_type_name'] ?? '')] ?? null; // Corrected from status_name to status_type_name

        // Assurer que les champs numériques vides deviennent 0 pour la validation/création
        $dataToValidate['initial_mileage'] = $data['initial_mileage'] ?? 0;

        return $dataToValidate;
    }

     /**
     * Centralise les règles de validation pour les véhicules.
     */
    private function getValidationRules(?int $vehicleId = null): array
    {
        $rules = [
            'registration_plate' => ['required', 'string', 'max:50', Rule::unique('vehicles')->ignore($vehicleId)->whereNull('deleted_at')],
            'vin' => ['nullable', 'string', 'size:17', Rule::unique('vehicles')->ignore($vehicleId)->whereNull('deleted_at')],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:50'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'transmission_type_id' => ['required', 'exists:transmission_types,id'],
            'status_id' => ['required', 'exists:vehicle_statuses,id'],
            'manufacturing_year' => ['nullable', 'integer', 'digits:4'],
            'acquisition_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'engine_displacement_cc' => ['nullable', 'integer', 'min:0'],
            'power_hp' => ['nullable', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($vehicleId) {
            // Règle pour la mise à jour
            $vehicle = Vehicle::find($vehicleId);
            $rules['current_mileage'] = ['required', 'integer', 'min:0', 'gte:' . ($vehicle->current_mileage ?? 0)];
        } else {
            // Règles pour la création
            $rules['initial_mileage'] = ['required', 'integer', 'min:0'];
            $rules['current_mileage'] = ['required', 'integer', 'min:0', 'gte:initial_mileage'];
        }

        return $rules;
    }
}


