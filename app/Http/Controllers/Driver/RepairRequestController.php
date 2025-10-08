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
        return view('driver.repair-requests.index');
    }
}
