<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord principal de l'application.
     */
    public function index(): View
    {
        // Pour l'instant, il affiche une vue simple.
        // Plus tard, nous y ajouterons des statistiques globales.
        return view('dashboard'); 
    }
}
