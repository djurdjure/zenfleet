<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationAlgeriaRequest;
use App\Models\Organization;
use App\Models\AlgeriaWilaya;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
            Log::error('Organizations index error: '.$e->getMessage());

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
            Log::error('Organization show error: '.$e->getMessage());

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
        $wilayas = AlgeriaWilaya::getSelectOptions();
        $organizationTypes = [
            'enterprise' => 'Grande Entreprise',
            'sme' => 'PME',
            'startup' => 'Start-up',
            'public' => 'Secteur Public',
            'ngo' => 'ONG',
            'cooperative' => 'Coopérative'
        ];

        return view('admin.organizations.create', compact('wilayas', 'organizationTypes'));
    }

    /**
     * 💾 Enregistrement d'une nouvelle organisation
     */
    public function store(StoreOrganizationAlgeriaRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            // Handle file uploads
            $validated = $this->handleFileUploads($validated, $request);

            // Generate UUID and set defaults
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
        $wilayas = AlgeriaWilaya::getSelectOptions();
        $organizationTypes = [
            'enterprise' => 'Grande Entreprise',
            'sme' => 'PME',
            'startup' => 'Start-up',
            'public' => 'Secteur Public',
            'ngo' => 'ONG',
            'cooperative' => 'Coopérative'
        ];

        return view('admin.organizations.edit', compact('organization', 'wilayas', 'organizationTypes'));
    }

    /**
     * 🔄 Mise à jour d'une organisation
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        try {
            $validated = $request->validate([
                // Informations générales
                'name' => 'required|string|max:255|unique:organizations,name,'.$organization->id,
                'legal_name' => 'nullable|string|max:255',
                'organization_type' => 'nullable|in:enterprise,sme,startup,public,ngo,cooperative',
                'industry' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'phone_number' => 'required|string|max:255',
                'primary_email' => 'required|email|max:255|unique:organizations,primary_email,'.$organization->id,
                'status' => 'required|in:active,inactive,suspended',

                // Informations légales
                'trade_register' => 'required|string|max:255',
                'nif' => 'required|string|max:255',
                'ai' => 'nullable|string|max:255',
                'nis' => 'nullable|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'commune' => 'nullable|string|max:255',
                'zip_code' => 'nullable|string|max:255',
                'wilaya' => 'required|string|max:255',

                // Représentant légal
                'manager_first_name' => 'required|string|max:255',
                'manager_last_name' => 'required|string|max:255',
                'manager_nin' => 'required|string|max:255',
                'manager_address' => 'nullable|string|max:255',
                'manager_dob' => 'nullable|date',
                'manager_pob' => 'nullable|string|max:255',
                'manager_phone_number' => 'nullable|string|max:255',

                // Documents
                'scan_nif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'scan_ai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'scan_nis' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'manager_id_scan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            ]);

            // Handle file uploads
            $validated = $this->handleFileUploads($validated, $request);

            $organization->update($validated);

            return redirect()
                ->route('admin.organizations.show', $organization)
                ->with('success', 'Organisation mise à jour avec succès.');

        } catch (\Exception $e) {
            Log::error('Organization update error: '.$e->getMessage());

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
            Log::error('Organization destroy error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Erreur lors de la suppression de l\'organisation.']);
        }
    }

    /**
     * Handle file uploads for organization documents
     */
    private function handleFileUploads(array $validated, Request $request): array
    {
        $fileFields = [
            'scan_nif' => 'scan_nif_path',
            'scan_ai' => 'scan_ai_path',
            'scan_nis' => 'scan_nis_path',
            'manager_id_scan' => 'manager_id_scan_path',
            'logo' => 'logo_path'
        ];

        foreach ($fileFields as $uploadField => $dbField) {
            if ($request->hasFile($uploadField)) {
                $file = $request->file($uploadField);
                $path = $file->store('organizations/' . $uploadField, 'public');
                $validated[$dbField] = $path;
            }
            // Remove the upload field from validated data
            unset($validated[$uploadField]);
        }

        return $validated;
    }
}
