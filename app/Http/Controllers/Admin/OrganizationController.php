<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
    }

    /**
     * 📊 Liste des organisations
     */
    public function index(Request $request): View
    {
        try {
            $organizations = Organization::with(['users'])
                ->withCount(['users'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.organizations.index', compact('organizations'));

        } catch (\Exception $e) {
            Log::error('Organizations index error: ' . $e->getMessage());

            $organizations = Organization::paginate(20);
            return view('admin.organizations.index', compact('organizations'))
                ->withErrors(['error' => 'Erreur lors du chargement des organisations.']);
        }
    }

    /**
     * 👁️ Affichage détaillé d'une organisation
     */
    public function show(Organization $organization): View
    {
        try {
            $organization->load(['users', 'vehicles', 'drivers']);

            return view('admin.organizations.show', compact('organization'));

        } catch (\Exception $e) {
            Log::error('Organization show error: ' . $e->getMessage());

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
        return view('admin.organizations.create');
    }

    /**
     * 💾 Enregistrement d'une nouvelle organisation
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                // Informations générales
                'name' => 'required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'organization_type' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'phone_number' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive,suspended',

                // Informations légales
                'trade_register' => 'nullable|string|max:255',
                'nif' => 'nullable|string|max:255',
                'ai' => 'nullable|string|max:255',
                'nis' => 'nullable|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'wilaya' => 'required|string|max:255',

                // Représentant légal
                'manager_first_name' => 'nullable|string|max:255',
                'manager_last_name' => 'nullable|string|max:255',
                'manager_nin' => 'nullable|string|max:255',
                'manager_address' => 'nullable|string|max:255',
                'manager_dob' => 'nullable|date',
                'manager_pob' => 'nullable|string|max:255',
                'manager_phone_number' => 'nullable|string|max:255',
            ]);

            // Generate UUID
            $validated['uuid'] = Str::uuid();
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            $organization = Organization::create($validated);

            return redirect()
                ->route('admin.organizations.show', $organization)
                ->with('success', 'Organisation créée avec succès.');

        } catch (\Exception $e) {
            Log::error('Organization store error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la création de l\'organisation.']);
        }
    }

    /**
     * ✏️ Formulaire d'édition
     */
    public function edit(Organization $organization): View
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    /**
     * 🔄 Mise à jour d'une organisation
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        try {
            $validated = $request->validate([
                // Informations générales
                'name' => 'required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'organization_type' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'phone_number' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive,suspended',

                // Informations légales
                'trade_register' => 'nullable|string|max:255',
                'nif' => 'nullable|string|max:255',
                'ai' => 'nullable|string|max:255',
                'nis' => 'nullable|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'wilaya' => 'required|string|max:255',

                // Représentant légal
                'manager_first_name' => 'nullable|string|max:255',
                'manager_last_name' => 'nullable|string|max:255',
                'manager_nin' => 'nullable|string|max:255',
                'manager_address' => 'nullable|string|max:255',
                'manager_dob' => 'nullable|date',
                'manager_pob' => 'nullable|string|max:255',
                'manager_phone_number' => 'nullable|string|max:255',
            ]);

            // No need to update slug as it's not in the new structure

            $organization->update($validated);

            return redirect()
                ->route('admin.organizations.show', $organization)
                ->with('success', 'Organisation mise à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Organization update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur lors de la mise à jour de l\'organisation.']);
        }
    }

    /**
     * 🗑️ Suppression d'une organisation
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        try {
            $organization->delete();

            return redirect()
                ->route('admin.organizations.index')
                ->with('success', 'Organisation supprimée avec succès.');

        } catch (\Exception $e) {
            Log::error('Organization destroy error: ' . $e->getMessage());

            return back()
                ->withErrors(['error' => 'Erreur lors de la suppression de l\'organisation.']);
        }
    }
}