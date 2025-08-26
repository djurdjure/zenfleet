<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\TransmissionType;
use App\Models\User;
use App\Services\VehicleService;
use App\Services\ImportExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Requests\Admin\Vehicle\StoreVehicleRequest;
use App\Http\Requests\Admin\Vehicle\UpdateVehicleRequest;

class VehicleController extends Controller
{
    protected VehicleService $vehicleService;
    protected ImportExportService $importExportService;

    public function __construct(VehicleService $vehicleService, ImportExportService $importExportService)
    {
        $this->vehicleService = $vehicleService;
        $this->importExportService = $importExportService;
    }

    public function index(Request $request): View
    {
        $this->authorize('view vehicles');
        $filters = $request->only(['search', 'status_id', 'per_page', 'view_deleted']);
        $vehicles = $this->vehicleService->getFilteredVehicles($filters);
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();
        return view('admin.vehicles.index', compact('vehicles', 'vehicleStatuses', 'filters'));
    }

    public function create(): View
    {
        $this->authorize('create vehicles');
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $transmissionTypes = TransmissionType::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.vehicles.create', compact('vehicleStatuses', 'vehicleTypes', 'fuelTypes', 'transmissionTypes', 'users'));
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->vehicleService->createVehicle($request->validated(), $request->file('photo'), $request->input('user_ids', []));
        return redirect()->route('admin.vehicles.index')->with('success', 'Nouveau véhicule ajouté avec succès.');
    }

    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('edit vehicles');
        $vehicleStatuses = VehicleStatus::orderBy('name')->get();
        $vehicleTypes = VehicleType::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();
        $transmissionTypes = TransmissionType::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $vehicle->load('users');
        return view('admin.vehicles.edit', compact('vehicle', 'vehicleStatuses', 'vehicleTypes', 'fuelTypes', 'transmissionTypes', 'users'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->vehicleService->updateVehicle($vehicle, $request->validated(), $request->file('photo'), $request->input('user_ids', []));
        return redirect()->route('admin.vehicles.index')->with('success', 'Le véhicule a été mis à jour.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete vehicles');
        $archived = $this->vehicleService->archiveVehicle($vehicle);

        if ($archived) {
            return redirect()->route('admin.vehicles.index')->with('success', "Le véhicule {$vehicle->registration_plate} a été archivé.");
        }

        return redirect()->back()->with('error', 'Impossible d\'archiver ce véhicule car il est lié à des affectations.');
    }

    public function restore($vehicleId): RedirectResponse
    {
        $this->authorize('restore vehicles');
        $this->vehicleService->restoreVehicle($vehicleId);
        return redirect()->route('admin.vehicles.index', ['view_deleted' => true])->with('success', "Le véhicule a été restauré.");
    }

    public function forceDelete($vehicleId): RedirectResponse
    {
        $this->authorize('force delete vehicles');
        $deleted = $this->vehicleService->forceDeleteVehicle($vehicleId);

        if ($deleted) {
            return redirect()->route('admin.vehicles.index', ['view_deleted' => true])->with('success', 'Le véhicule a été supprimé définitivement.');
        }

        return redirect()->back()->with('error', 'Impossible de supprimer ce véhicule car il est lié à un historique d\'affectations.');
    }

    public function showImportForm(): View
    {
        $this->authorize('create vehicles');
        return view('admin.vehicles.import');
    }

    public function downloadTemplate()
    {
        $this->authorize('create vehicles');
        $headers = [
            'registration_plate', 'vin', 'brand', 'model', 'color', 'vehicle_type',
            'fuel_type', 'transmission_type', 'status', 'manufacturing_year',
            'acquisition_date', 'purchase_price', 'current_value', 'initial_mileage',
            'engine_displacement_cc', 'power_hp', 'seats', 'status_reason', 'notes',
        ];
        $example = [
            'AA-123-BB', 'VFE...', 'Renault', 'Clio', 'Noire', 'Voiture de société',
            'Essence', 'Manuelle', 'Disponible', '2022',
            '2022-01-15', '150000.00', '120000.00', '5000',
            '1199', '90', '5', '', 'Véhicule neuf',
        ];

        return $this->importExportService->downloadCsvTemplate($headers, [$example], 'template_import_vehicules.csv');
    }

    public function handleImport(Request $request): RedirectResponse
    {
        $this->authorize('create vehicles');
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');

        list($successCount, $errorRows) = $this->importExportService->handleImport(
            $file,
            fn($data) => $this->prepareDataForValidation($data),
            fn($data) => $this->getValidationRules($data),
            fn($data) => Vehicle::create($data)
        );

        if (empty($errorRows)) {
            return redirect()->route('admin.vehicles.index')->with('success', "$successCount véhicules importés avec succès.");
        }

        return redirect()->route('admin.vehicles.import.results')
            ->with('successCount', $successCount)
            ->with('errorRows', $errorRows)
            ->with('fileName', $file->getClientOriginalName());
    }

    public function showImportResults(): View
    {
        $this->authorize('create vehicles');
        return view('admin.vehicles.import-results', [
            'successCount' => session('successCount', 0),
            'errorRows' => session('errorRows', []),
            'fileName' => session('fileName', 'Fichier CSV'),
        ]);
    }

    private function prepareDataForValidation(array $record): array
    {
        $vehicleTypes = VehicleType::pluck('id', 'name');
        $fuelTypes = FuelType::pluck('id', 'name');
        $transmissionTypes = TransmissionType::pluck('id', 'name');
        $statuses = VehicleStatus::pluck('id', 'name');

        return [
            'registration_plate' => $record['registration_plate'] ?? null,
            'vin' => $record['vin'] ?? null,
            'brand' => $record['brand'] ?? null,
            'model' => $record['model'] ?? null,
            'color' => $record['color'] ?? null,
            'vehicle_type_id' => $vehicleTypes[trim($record['vehicle_type'])] ?? null,
            'fuel_type_id' => $fuelTypes[trim($record['fuel_type'])] ?? null,
            'transmission_type_id' => $transmissionTypes[trim($record['transmission_type'])] ?? null,
            'status_id' => $statuses[trim($record['status'])] ?? null,
            'manufacturing_year' => $record['manufacturing_year'] ?? null,
            'acquisition_date' => $this->formatDate($record['acquisition_date']),
            'purchase_price' => $record['purchase_price'] ?? null,
            'current_value' => $record['current_value'] ?? null,
            'initial_mileage' => $record['initial_mileage'] ?? 0,
            'current_mileage' => $record['initial_mileage'] ?? 0,
            'engine_displacement_cc' => $record['engine_displacement_cc'] ?? null,
            'power_hp' => $record['power_hp'] ?? null,
            'seats' => $record['seats'] ?? null,
            'status_reason' => $record['status_reason'] ?? null,
            'notes' => $record['notes'] ?? null,
        ];
    }

    private function getValidationRules(array $data): array
    {
        return [
            'registration_plate' => ['required', 'string', 'max:255', 'unique:vehicles,registration_plate,' . ($data['id'] ?? 'NULL') . ',id,deleted_at,NULL'],
            'vin' => ['required', 'string', 'max:255', 'unique:vehicles,vin,' . ($data['id'] ?? 'NULL') . ',id,deleted_at,NULL'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'transmission_type_id' => ['required', 'exists:transmission_types,id'],
            'status_id' => ['required', 'exists:vehicle_statuses,id'],
            'manufacturing_year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'acquisition_date' => ['required', 'date_format:Y-m-d'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'current_value' => ['nullable', 'numeric', 'min:0'],
            'initial_mileage' => ['required', 'integer', 'min:0'],
            'current_mileage' => ['required', 'integer', 'min:0'],
            'engine_displacement_cc' => ['nullable', 'integer', 'min:0'],
            'power_hp' => ['nullable', 'integer', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:0'],
            'status_reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function formatDate($dateString)
    {
        if (!$dateString) return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }
        $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d', 'Y-m-d', 'Y.m.d', 'd/m/y', 'd-m-y', 'd.m.y'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            $errors = \DateTime::getLastErrors();
            if ($date !== false && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                return $date->format('Y-m-d');
            }
        }
        return $dateString;
    }

    public function exportErrors()
    {
        return redirect()->back()->with('error', 'La fonctionnalité d\'exportation des erreurs n\'est pas encore disponible.');
    }

    public function show(Vehicle $vehicle): View
    {
        $this->authorize('view vehicles');
        $vehicle->load(['vehicleType', 'fuelType', 'transmissionType', 'vehicleStatus', 'assignments.driver', 'maintenanceLogs', 'users']);
        return view('admin.vehicles.show', compact('vehicle'));
    }
}