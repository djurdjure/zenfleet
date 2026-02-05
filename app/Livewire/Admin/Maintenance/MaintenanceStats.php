<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use App\Services\Maintenance\MaintenanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\MaintenanceOperation;

/**
 * 📈 COMPOSANT STATISTIQUES MAINTENANCE
 * 
 * Cards métriques ultra-pro avec icônes Iconify
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceStats extends Component
{
    use AuthorizesRequests;
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
        $this->authorize('viewAny', MaintenanceOperation::class);
        $analytics = $maintenanceService->getAnalytics([
            'period' => $this->period
        ]);

        return view('livewire.admin.maintenance.maintenance-stats', [
            'analytics' => $analytics,
        ]);
    }
}
