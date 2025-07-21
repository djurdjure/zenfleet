<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organization\StoreOrganizationRequest;
use App\Http\Requests\Admin\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        $this->authorize('view organizations');
        // On désactive le scope global pour que l'Admin puisse voir toutes les organisations
        $organizations = Organization::withoutGlobalScope('organization')
            ->withCount('users') // Compte les utilisateurs pour l'affichage
            ->paginate(15);
        return view('admin.organizations.index', compact('organizations'));
    }

    public function create(): View
    {
        $this->authorize('create organizations');
        return view('admin.organizations.create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        Organization::create($request->validated());
        return redirect()->route('admin.organizations.index')->with('success', 'Organisation créée avec succès.');
    }

    public function edit(Organization $organization): View
    {
        $this->authorize('edit organizations');
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());
        return redirect()->route('admin.organizations.index')->with('success', 'Organisation mise à jour avec succès.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete organizations');
        // A FAIRE : Ajouter une logique pour empêcher la suppression de sa propre organisation
        $organization->delete();
        return redirect()->route('admin.organizations.index')->with('success', 'Organisation supprimée. Attention, toutes les données associées (véhicules, chauffeurs, etc.) ont également été supprimées.');
    }
}
