<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceOperation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour le sous-menu Surveillance de Maintenance
 * Affiche le tableau de bord avec les maintenances planifiées, en cours, et en retard
 */
class SurveillanceController extends Controller
{
    /**
     * Afficher le tableau de bord de surveillance
     */
    public function index(Request $request): View
    {
        $organizationId = auth()->user()->organization_id;

        // Récupérer les filtres
        $filterPeriod = $request->get('period', 'all');
        $filterStatus = $request->get('status', 'all');

        // Statistiques principales
        $stats = [
            'en_cours' => $this->getMaintenancesEnCours($organizationId),
            'proches' => $this->getMaintenancesProches($organizationId),
            'echeance' => $this->getMaintenancesEcheance($organizationId),
        ];

        // Construction de la requête principale avec tous les filtres
        $query = MaintenanceSchedule::with([
            'vehicle:id,registration_plate,brand,model',
            'maintenanceType:id,name,category',
            'organization:id,name'
        ])->where('organization_id', $organizationId);

        // Appliquer les filtres
        $query = $this->applyFilters($query, $filterPeriod, $filterStatus);

        // Récupérer les maintenances avec pagination
        $maintenances = $query->orderBy('next_due_date', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Enrichir les données avec calcul des jours restants
        $maintenances->getCollection()->transform(function ($maintenance) {
            $maintenance->days_remaining_int = $this->calculateDaysRemaining($maintenance->next_due_date);
            $maintenance->urgency_level = $this->determineUrgencyLevel($maintenance->days_remaining_int);
            return $maintenance;
        });

        return view('admin.maintenance.surveillance.index', compact(
            'maintenances',
            'stats',
            'filterPeriod',
            'filterStatus'
        ));
    }

    /**
     * Obtenir le nombre de maintenances en cours
     */
    private function getMaintenancesEnCours(int $organizationId): int
    {
        return MaintenanceOperation::where('organization_id', $organizationId)
            ->where('status', 'in_progress')
            ->count();
    }

    /**
     * Obtenir le nombre de maintenances proches (dans les 7 prochains jours)
     */
    private function getMaintenancesProches(int $organizationId): int
    {
        return MaintenanceSchedule::where('organization_id', $organizationId)
            ->active()
            ->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();
    }

    /**
     * Obtenir le nombre de maintenances arrivées à échéance
     */
    private function getMaintenancesEcheance(int $organizationId): int
    {
        return MaintenanceSchedule::where('organization_id', $organizationId)
            ->active()
            ->where('next_due_date', '<', Carbon::now())
            ->count();
    }

    /**
     * Appliquer les filtres à la requête
     */
    private function applyFilters($query, string $filterPeriod, string $filterStatus)
    {
        // Filtre par période
        switch ($filterPeriod) {
            case 'today':
                $query->whereDate('next_due_date', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays(7)]);
                break;
            case 'month':
                $query->whereBetween('next_due_date', [Carbon::now(), Carbon::now()->addDays(30)]);
                break;
            case 'overdue':
                $query->where('next_due_date', '<', Carbon::now());
                break;
        }

        // Filtre par statut
        switch ($filterStatus) {
            case 'terminées':
                $query->where('status', 'completed');
                break;
            case 'en_retard':
                $query->active()->where('next_due_date', '<', Carbon::now());
                break;
            case 'en_cours':
                $query->where('status', 'in_progress');
                break;
            case 'planifiées':
                $query->active()->where('next_due_date', '>=', Carbon::now());
                break;
            default:
                $query->active();
                break;
        }

        return $query;
    }

    /**
     * Calculer le nombre de jours restants (chiffre arrondi)
     */
    private function calculateDaysRemaining($dueDate): int
    {
        if (!$dueDate) {
            return 999; // Grande valeur pour les dates indéfinies
        }

        $now = Carbon::now()->startOfDay();
        $due = Carbon::parse($dueDate)->startOfDay();

        return (int) round($now->diffInDays($due, false));
    }

    /**
     * Déterminer le niveau d'urgence
     */
    private function determineUrgencyLevel(int $daysRemaining): string
    {
        if ($daysRemaining < 0) {
            return 'critical'; // En retard
        } elseif ($daysRemaining <= 3) {
            return 'urgent'; // Très proche
        } elseif ($daysRemaining <= 7) {
            return 'warning'; // Proche
        } else {
            return 'normal'; // Normal
        }
    }
}