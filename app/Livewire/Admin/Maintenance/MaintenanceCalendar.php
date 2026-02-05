<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use App\Services\Maintenance\MaintenanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\MaintenanceOperation;

/**
 * 📅 COMPOSANT CALENDRIER MAINTENANCE
 * 
 * Calendrier interactif FullCalendar.js + Livewire
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceCalendar extends Component
{
    use AuthorizesRequests;
    public $currentMonth;
    public $currentYear;

    protected $listeners = [
        'monthChanged' => 'changeMonth',
        'refreshCalendar' => '$refresh',
    ];

    /**
     * Mount component
     */
    public function mount()
    {
        $this->authorize('viewAny', MaintenanceOperation::class);
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
    }

    /**
     * Changer de mois
     */
    public function changeMonth($month, $year)
    {
        $this->currentMonth = $month;
        $this->currentYear = $year;
    }

    /**
     * Aller au mois précédent
     */
    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Aller au mois suivant
     */
    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    /**
     * Revenir au mois actuel
     */
    public function today()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
    }

    /**
     * Render component
     */
    public function render(MaintenanceService $maintenanceService)
    {
        $this->authorize('viewAny', MaintenanceOperation::class);
        $startDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        $events = $maintenanceService->getCalendarEvents($startDate, $endDate);

        return view('livewire.admin.maintenance.maintenance-calendar', [
            'events' => $events,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
