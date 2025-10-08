<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceAlert;
use App\Models\RepairRequest;
use App\Models\VehicleExpense;
use App\Models\ExpenseBudget;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Affichage du dashboard des alertes enterprise
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        // Récupération des alertes par catégorie
        $alerts = $this->getSystemAlerts($organizationId);
        $criticalAlerts = $this->getCriticalAlerts($organizationId);
        $maintenanceAlerts = $this->getMaintenanceAlerts($organizationId);
        $budgetAlerts = $this->getBudgetAlerts($organizationId);
        $repairAlerts = $this->getRepairAlerts($organizationId);

        // Statistiques des alertes
        $stats = [
            'total_alerts' => $alerts->count(),
            'critical_count' => $criticalAlerts->count(),
            'maintenance_count' => $maintenanceAlerts->count(),
            'budget_overruns' => $budgetAlerts->where('type', 'budget_overrun')->count(),
            'pending_repairs' => $repairAlerts->where('status', 'en_attente')->count(),
            'overdue_maintenance' => $maintenanceAlerts->where('priority', 'urgent')->count()
        ];

        // Alertes récentes (7 derniers jours)
        $recentAlerts = collect([
            ...$maintenanceAlerts->take(5),
            ...$budgetAlerts->take(5),
            ...$repairAlerts->take(5)
        ])->sortByDesc('created_at')->take(10);

        return view('admin.alerts.index', compact(
            'alerts',
            'criticalAlerts',
            'maintenanceAlerts',
            'budgetAlerts',
            'repairAlerts',
            'stats',
            'recentAlerts'
        ));
    }

    /**
     * Alertes système globales
     */
    private function getSystemAlerts($organizationId)
    {
        $alerts = collect();

        // Alertes de maintenance en retard
        $overdueMaintenanceCount = DB::table('maintenance_schedules')
            ->where('organization_id', $organizationId)
            ->where('next_due_date', '<', now())
            ->where('is_active', true)
            ->count();

        if ($overdueMaintenanceCount > 0) {
            $alerts->push((object)[
                'id' => 'overdue_maintenance',
                'type' => 'maintenance',
                'priority' => 'urgent',
                'title' => 'Maintenance en retard',
                'message' => "{$overdueMaintenanceCount} opération(s) de maintenance en retard",
                'count' => $overdueMaintenanceCount,
                'icon' => 'alert-triangle',
                'color' => 'red',
                'created_at' => now()
            ]);
        }

        // Alertes de budget dépassé - ENTERPRISE: Vérification existence table
        try {
            if (DB::getSchemaBuilder()->hasTable('expense_budgets')) {
                $budgetOverruns = DB::table('expense_budgets')
                    ->where('organization_id', $organizationId)
                    ->whereRaw('spent_amount > budgeted_amount')
                    ->where('status', 'active')
                    ->count();

                if ($budgetOverruns > 0) {
                    $alerts->push((object)[
                        'id' => 'budget_overrun',
                        'type' => 'budget',
                        'priority' => 'high',
                        'title' => 'Budgets dépassés',
                        'message' => "{$budgetOverruns} budget(s) dépassé(s)",
                        'count' => $budgetOverruns,
                        'icon' => 'wallet',
                        'color' => 'red',
                        'created_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Budget alerts unavailable', ['error' => $e->getMessage()]);
        }

        // Alertes de paiements en retard - ENTERPRISE: Vérification existence table
        try {
            if (DB::getSchemaBuilder()->hasTable('vehicle_expenses')) {
                $overduePayments = DB::table('vehicle_expenses')
                    ->where('organization_id', $organizationId)
                    ->where('payment_due_date', '<', now())
                    ->where('payment_status', '!=', 'paid')
                    ->where('approval_status', 'approved')
                    ->count();

                if ($overduePayments > 0) {
                    $alerts->push((object)[
                        'id' => 'overdue_payments',
                        'type' => 'payment',
                        'priority' => 'high',
                        'title' => 'Paiements en retard',
                        'message' => "{$overduePayments} paiement(s) en retard",
                        'count' => $overduePayments,
                        'icon' => 'credit-card',
                        'color' => 'orange',
                        'created_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Payment alerts unavailable', ['error' => $e->getMessage()]);
        }

        return $alerts->sortByDesc('priority');
    }

    /**
     * Alertes critiques nécessitant une action immédiate - ENTERPRISE: Colonnes optionnelles
     */
    private function getCriticalAlerts($organizationId)
    {
        $criticalAlerts = collect();

        try {
            // Véhicules avec assurance expirée - seulement si la colonne existe
            if (DB::getSchemaBuilder()->hasColumn('vehicles', 'insurance_expiry_date')) {
                $expiredInsurance = Vehicle::where('organization_id', $organizationId)
                    ->where('insurance_expiry_date', '<', now())
                    ->where('status', 'active')
                    ->count();

                if ($expiredInsurance > 0) {
                    $criticalAlerts->push((object)[
                        'id' => 'expired_insurance',
                        'type' => 'vehicle',
                        'priority' => 'critical',
                        'title' => 'Assurances expirées',
                        'message' => "{$expiredInsurance} véhicule(s) avec assurance expirée",
                        'action_required' => true,
                        'created_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Insurance alerts unavailable', ['error' => $e->getMessage()]);
        }

        try {
            // Véhicules avec contrôle technique expiré - seulement si la colonne existe
            if (DB::getSchemaBuilder()->hasColumn('vehicles', 'technical_inspection_date')) {
                $expiredInspection = Vehicle::where('organization_id', $organizationId)
                    ->where('technical_inspection_date', '<', now())
                    ->where('status', 'active')
                    ->count();

                if ($expiredInspection > 0) {
                    $criticalAlerts->push((object)[
                        'id' => 'expired_inspection',
                        'type' => 'vehicle',
                        'priority' => 'critical',
                        'title' => 'Contrôles techniques expirés',
                        'message' => "{$expiredInspection} véhicule(s) avec contrôle technique expiré",
                        'action_required' => true,
                        'created_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Technical inspection alerts unavailable', ['error' => $e->getMessage()]);
        }

        return $criticalAlerts;
    }

    /**
     * Alertes de maintenance
     */
    private function getMaintenanceAlerts($organizationId)
    {
        return DB::table('maintenance_schedules')
            ->join('vehicles', 'maintenance_schedules.vehicle_id', '=', 'vehicles.id')
            ->join('maintenance_types', 'maintenance_schedules.maintenance_type_id', '=', 'maintenance_types.id')
            ->where('maintenance_schedules.organization_id', $organizationId)
            ->where('maintenance_schedules.next_due_date', '<=', now()->addDays(7))
            ->where('maintenance_schedules.is_active', true)
            ->select([
                'maintenance_schedules.id',
                'maintenance_types.name as maintenance_type',
                'maintenance_schedules.next_due_date',
                'vehicles.registration_plate',
                'vehicles.brand',
                'vehicles.model',
                DB::raw("CASE
                    WHEN next_due_date < NOW() THEN 'overdue'
                    WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 'high'
                    ELSE 'medium'
                END as alert_priority"),
                DB::raw("CASE
                    WHEN next_due_date < NOW() THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 'urgent'
                    WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 'high'
                    ELSE 'medium'
                END as priority"),
                DB::raw("'maintenance' as type"),
                'maintenance_schedules.created_at'
            ])
            ->orderByRaw("CASE
                WHEN next_due_date < NOW() THEN 1
                WHEN next_due_date <= NOW() + INTERVAL '1 day' THEN 2
                WHEN next_due_date <= NOW() + INTERVAL '3 days' THEN 3
                ELSE 4
            END")
            ->orderBy('maintenance_schedules.next_due_date')
            ->get();
    }

    /**
     * Alertes budgétaires - ENTERPRISE: Avec gestion d'absence de table
     */
    private function getBudgetAlerts($organizationId)
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('expense_budgets')) {
                return collect([]);
            }

            return DB::table('expense_budgets')
                ->where('organization_id', $organizationId)
                ->where('status', 'active')
                ->whereRaw('(spent_amount / budgeted_amount) * 100 >= warning_threshold')
                ->select([
                    'id',
                    'scope_type',
                    'scope_description',
                    'budgeted_amount',
                    'spent_amount',
                    'warning_threshold',
                    'critical_threshold',
                    DB::raw('(spent_amount / budgeted_amount) * 100 as utilization_percentage'),
                    DB::raw("CASE
                        WHEN spent_amount > budgeted_amount THEN 'budget_overrun'
                        WHEN (spent_amount / budgeted_amount) * 100 >= critical_threshold THEN 'budget_critical'
                        ELSE 'budget_warning'
                    END as type"),
                    DB::raw("CASE
                        WHEN spent_amount > budgeted_amount THEN 'urgent'
                        WHEN (spent_amount / budgeted_amount) * 100 >= critical_threshold THEN 'high'
                        ELSE 'medium'
                    END as priority"),
                    'created_at'
                ])
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 ELSE 3 END")
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Error fetching budget alerts', ['error' => $e->getMessage()]);
            return collect([]);
        }
    }

    /**
     * Alertes de réparation - ENTERPRISE: PostgreSQL compatible
     */
    private function getRepairAlerts($organizationId)
    {
        return RepairRequest::with(['vehicle', 'driver'])
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['pending', 'supervisor_review', 'fleet_manager_review'])
            ->where('created_at', '>=', now()->subDays(30)) // Derniers 30 jours
            ->orderByRaw("CASE urgency
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($repair) {
                return (object)[
                    'id' => $repair->id,
                    'type' => 'repair',
                    'priority' => $repair->urgency ?? 'medium',
                    'title' => $repair->title ?? "Demande de réparation #{$repair->id}",
                    'message' => $repair->description,
                    'vehicle' => $repair->vehicle?->registration_plate ?? 'N/A',
                    'status' => $repair->status,
                    'requested_by' => $repair->driver?->name ?? 'N/A',
                    'created_at' => $repair->created_at,
                    'days_pending' => $repair->created_at->diffInDays(now())
                ];
            });
    }

    /**
     * Marquer une alerte comme lue
     */
    public function markAsRead(Request $request)
    {
        $alertId = $request->input('alert_id');
        $alertType = $request->input('alert_type');

        // Logique pour marquer comme lue selon le type
        switch ($alertType) {
            case 'maintenance':
                // Marquer l'alerte de maintenance comme acknowledgeée
                DB::table('maintenance_schedules')
                    ->where('id', $alertId)
                    ->update(['acknowledged_at' => now(), 'acknowledged_by' => Auth::id()]);
                break;

            case 'repair':
                // Ajouter un commentaire système sur la demande de réparation
                RepairRequest::where('id', $alertId)
                    ->update(['last_viewed_at' => now(), 'last_viewed_by' => Auth::id()]);
                break;
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir les alertes pour l'API (notifications temps réel)
     */
    public function getAlertsApi(Request $request)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;

        $alerts = [
            'system' => $this->getSystemAlerts($organizationId),
            'critical' => $this->getCriticalAlerts($organizationId),
            'maintenance' => $this->getMaintenanceAlerts($organizationId)->take(5),
            'budget' => $this->getBudgetAlerts($organizationId)->take(5),
            'repair' => $this->getRepairAlerts($organizationId)->take(5)
        ];

        return response()->json($alerts);
    }

    /**
     * Export des alertes en Excel
     */
    public function export(Request $request)
    {
        // Implémentation de l'export Excel
        // À compléter selon les besoins

        return response()->json(['message' => 'Export en développement']);
    }
}