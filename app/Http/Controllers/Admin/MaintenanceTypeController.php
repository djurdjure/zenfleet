<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur des types de maintenance
 * Version minimaliste pour éviter les erreurs 404
 */
class MaintenanceTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.maintenance.types.index');
    }

    public function create(): View
    {
        return view('admin.maintenance.types.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.maintenance.types.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function show($id): View
    {
        return view('admin.maintenance.types.show');
    }

    public function edit($id): View
    {
        return view('admin.maintenance.types.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.maintenance.types.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.maintenance.types.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }
}