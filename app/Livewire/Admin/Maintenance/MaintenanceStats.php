<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use App\Services\Maintenance\MaintenanceService;

/**
 * 📈 COMPOSANT STATISTIQUES MAINTENANCE
 * 
 * Cards métriques ultra-pro avec icônes Iconify
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceStats extends Component
{
    public $period = 'month'; // today, week, month, quarter, year

    protected $listeners = [
        'periodChanged' => 'updatePeriod',
        'refreshStats' => '$refresh',
    ];

    /**
     * Mettre à jour période
     */
    public function updatePeriod($period)
    {
        $this->period = $period;
    }

    /**
     * Render component
     */
    public function render(MaintenanceService $maintenanceService)
    {
        $analytics = $maintenanceService->getAnalytics([
            'period' => $this->period
        ]);

        return view('livewire.admin.maintenance.maintenance-stats', [
            'analytics' => $analytics,
        ]);
    }
}
