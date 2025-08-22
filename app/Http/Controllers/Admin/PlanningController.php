<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\VehicleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanningController extends Controller
{
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(VehicleRepositoryInterface $vehicleRepository)
    {
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Affiche la vue du planning GANTT.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('view assignments'); // Reuse permission from assignments

        // --- Gestion de la Période ---
        $viewMode = $request->input('view_mode', 'week');
        $baseDate = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        if ($viewMode === 'month') {
            $startDate = $baseDate->copy()->startOfMonth();
            $endDate = $baseDate->copy()->endOfMonth();
        } else {
            $startDate = $baseDate->copy()->startOfWeek(Carbon::MONDAY);
            $endDate = $baseDate->copy()->endOfWeek(Carbon::SUNDAY);
        }

        // --- Récupération des données ---
        $filters = $request->only(['search', 'per_page']);
        $vehicles = $this->vehicleRepository->getForPlanning(
            $filters,
            $startDate->toDateString(),
            $endDate->toDateString()
        );

        return view('admin.planning.index', [
            'vehicles' => $vehicles,
            'filters' => $filters,
            'dateRange' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'baseDate' => $baseDate,
            'viewMode' => $viewMode,
        ]);
    }
}
