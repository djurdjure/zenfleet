<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 🚧 Placeholder Controller - Ultra Professional
 *
 * Contrôleur temporaire pour les modules en cours de développement
 *
 * @version 1.0-Development
 * @author ZenFleet Development Team
 */
class PlaceholderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * 🚧 Page en cours de développement
     */
    public function index(Request $request): View
    {
        $module = $request->route()->getName();
        $moduleName = $this->getModuleName($module);

        return view('admin.placeholder.index', [
            'module' => $moduleName,
            'route' => $module,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Obtenir le nom du module à partir de la route
     */
    private function getModuleName(string $route): string
    {
        $moduleMap = [
            'admin.assignments.index' => 'Affectations',
            'admin.drivers.index' => 'Chauffeurs',
            'admin.planning.index' => 'Planning',
            'admin.documents.index' => 'Documents',
            'admin.suppliers.index' => 'Fournisseurs',
            'admin.reports.index' => 'Rapports',
            'admin.audit.index' => 'Audit',
        ];

        return $moduleMap[$route] ?? 'Module';
    }
}