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
use App\Http\Requests\Admin\Driver\StoreDriverRequest; // <-- AJOUT
use App\Http\Requests\Admin\Driver\UpdateDriverRequest; // <-- AJOUT

class DriverController extends Controller
{
    // ... (index et create sont déjà corrects) ...
    ////////_____OK
    public function index(Request $request): View
    {
        $this->authorize('view drivers');

        $perPage = $request->query('per_page', 15);
        $query = Driver::query()->with(['driverStatus', 'user']);

        // AJOUT : Logique pour voir les archives
        if ($request->query('view_deleted')) {
            $query->onlyTrashed();
        }

        // Filtre par Statut
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Moteur de Recherche (CORRIGÉ pour être insensible à la casse)
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search); // Convertit le terme de recherche en minuscules
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(employee_number) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(license_number) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        $drivers = $query->orderBy('last_name')->orderBy('first_name')->paginate($perPage)->withQueryString();
        $driverStatuses = DriverStatus::orderBy('name')->get();

        return view('admin.drivers.index', [
            'drivers' => $drivers,
            'driverStatuses' => $driverStatuses,
            'filters' => $request->only(['search', 'status_id', 'per_page', 'view_deleted']),
        ]);
    }



    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        if ($request->hasFile('photo')) {
            $validatedData['photo_path'] = $request->file('photo')->store('drivers/photos', 'public');
        }
        Driver::create($validatedData);
        return redirect()->route('admin.drivers.index')->with('success', 'Nouveau chauffeur ajouté avec succès.');
    }
    ////////____OK
    public function edit(Driver $driver): View
    {
        $this->authorize('edit drivers');
        $linkableUsers = User::whereDoesntHave('driver')->orWhere('id', $driver->user_id)->orderBy('name')->get();
        $driverStatuses = DriverStatus::orderBy('name')->get();
        return view('admin.drivers.edit', compact('driver', 'linkableUsers', 'driverStatuses'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $validatedData = $request->validated();
        if ($request->hasFile('photo')) {
            if ($driver->photo_path) {
                Storage::disk('public')->delete($driver->photo_path);
            }
            $validatedData['photo_path'] = $request->file('photo')->store('drivers/photos', 'public');
        }
        $driver->update($validatedData);
        return redirect()->route('admin.drivers.index')->with('success', "Le chauffeur {$driver->first_name} {$driver->last_name} a été mis à jour.");
    }

    // ... (destroy, restore, forceDelete sont déjà corrects) ...

      /**
     * Affiche la liste des chauffeurs avec une recherche insensible à la casse.
     */

    public function create(): View
    {
        $this->authorize('create drivers');
        $linkableUsers = User::whereDoesntHave('driver')->orderBy('name')->get();
        $driverStatuses = DriverStatus::orderBy('name')->get();
        return view('admin.drivers.create', compact('linkableUsers', 'driverStatuses'));
    }


    public function destroy(Driver $driver): RedirectResponse
    {
        $this->authorize('delete drivers');
        $driver->delete();
        return redirect()->route('admin.drivers.index')->with('success', "Le chauffeur {$driver->first_name} a été archivé.");
    }

     public function restore($driverId): RedirectResponse
    {
        $this->authorize('restore drivers');
        $driver = Driver::onlyTrashed()->findOrFail($driverId);
        $driver->restore();
        return redirect()->route('admin.drivers.index', ['view_deleted' => 'true'])->with('success', "Le chauffeur {$driver->first_name} a été restauré.");
    }

    public function forceDelete($driverId): RedirectResponse
    {
        $this->authorize('force delete drivers');
        $driver = Driver::onlyTrashed()->findOrFail($driverId);
        if ($driver->photo_path) {
            Storage::disk('public')->delete($driver->photo_path);
        }
        $driver->forceDelete();
        return redirect()->route('admin.drivers.index', ['view_deleted' => 'true'])->with('success', 'Le chauffeur a été supprimé définitivement.');
    }

    private function getValidationRules(?int $driverId = null): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:100', Rule::unique('drivers')->ignore($driverId)->whereNull('deleted_at')],
            'user_id' => ['nullable', 'sometimes', 'exists:users,id', Rule::unique('drivers')->ignore($driverId)->whereNull('deleted_at')],
            'status_id' => ['required', 'exists:driver_statuses,id'],
            'birth_date' => ['nullable', 'date'],
            'personal_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'license_number' => ['nullable', 'string', 'max:100'],
            'license_category' => ['nullable', 'string', 'max:50'],
            'license_issue_date' => ['nullable', 'date'],
            'license_authority' => ['nullable', 'string', 'max:255'],
            'recruitment_date' => ['nullable', 'date'],
            'contract_end_date' => ['nullable', 'date', 'after_or_equal:recruitment_date'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'personal_email' => ['nullable', 'email', 'max:255'],
        ];
    }





}

