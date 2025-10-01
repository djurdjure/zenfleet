<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur des alertes de maintenance
 * Version minimaliste pour éviter les erreurs 404
 */
class MaintenanceAlertController extends Controller
{
    public function index(): View
    {
        return view('admin.maintenance.alerts.index');
    }

    public function dashboard(): View
    {
        return view('admin.maintenance.alerts.dashboard');
    }

    public function show($id): View
    {
        return view('admin.maintenance.alerts.show');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.maintenance.alerts.index')
            ->with('success', 'Fonctionnalité en cours de développement');
    }

    public function acknowledge(Request $request, $id)
    {
        return redirect()->route('admin.maintenance.alerts.index')
            ->with('success', 'Alerte acquittée - Fonctionnalité en cours de développement');
    }
}