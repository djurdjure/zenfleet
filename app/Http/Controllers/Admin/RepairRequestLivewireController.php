<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

/**
 * 🔧 Contrôleur des Demandes de Réparation - Version Livewire
 * Wrapper pour le composant Livewire Kanban
 */
class RepairRequestLivewireController extends Controller
{
    /**
     * Afficher la page Livewire de gestion des demandes de réparation
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.repair-requests.index');
    }
}
