<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur des fournisseurs de maintenance
 * Version minimaliste pour éviter les erreurs 404
 */
class MaintenanceProviderController extends Controller
{
    public function index(): View
    {
        return view('admin.maintenance.providers.index');
    }

    public function create(): View
    {
        return view('admin.maintenance.providers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.maintenance.providers.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function show($id): View
    {
        return view('admin.maintenance.providers.show');
    }

    public function edit($id): View
    {
        return view('admin.maintenance.providers.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.maintenance.providers.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.maintenance.providers.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }
}