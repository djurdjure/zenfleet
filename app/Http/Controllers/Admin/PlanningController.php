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
        $this->authorize('assignments.view'); // Reuse permission from assignments

        // --- Gestion de la Période ---
        $viewMode = $request->input('view_mode', 'week');
        $baseDate = $request->has('date') ? Carbon::parse($request->input('date')) : Carbon::today();

        switch ($viewMode) {
            case 'day':
                $startDate = $baseDate->copy()->startOfDay();
                $endDate = $baseDate->copy()->endOfDay();
                break;
            case 'month':
                $startDate = $baseDate->copy()->startOfMonth();
                $endDate = $baseDate->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $baseDate->copy()->startOfYear();
                $endDate = $baseDate->copy()->endOfYear();
                break;
            case 'week':
            default:
                $startDate = $baseDate->copy()->startOfWeek(Carbon::MONDAY);
                $endDate = $baseDate->copy()->endOfWeek(Carbon::SUNDAY);
                break;
        }

        // --- Récupération des données ---
        $filters = $request->only(['search', 'per_page', 'sort']);
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
