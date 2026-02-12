<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Livewire\Admin\RepairRequestManager;

/**
 * 🚗 Contrôleur des Demandes de Réparation - Espace Chauffeur
 * Wrapper pour le composant Livewire avec layout approprié
 */
class RepairRequestController extends Controller
{
    /**
     * Afficher la page des demandes de réparation pour le chauffeur
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        abort_unless($user, 403, 'This action is unauthorized.');

        // Canonical workflow entrypoint.
        // - If user can list own/team/all requests: go to unified index.
        // - If user can only create: go directly to create page.
        if ($user->canAny([
            'repair-requests.view.own',
            'repair-requests.view.team',
            'repair-requests.view.all',
            'view own repair requests',
            'view team repair requests',
            'view all repair requests',
        ])) {
            return redirect()->route('admin.repair-requests.index');
        }

        if ($user->canAny([
            'repair-requests.create',
            'create repair requests',
        ])) {
            return redirect()->route('admin.repair-requests.create');
        }

        abort(403, 'This action is unauthorized.');
    }
}
