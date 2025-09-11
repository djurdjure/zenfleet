<?php

namespace App\Http\Controllers\Admin\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * 🔧 MAINTENANCE DASHBOARD - VERSION ENTERPRISE
     * 
     * Affiche le tableau de bord principal de maintenance avec statistiques
     * en temps réel et alertes de maintenance urgente.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $this->authorize('view maintenance dashboard');
        
        try {
            // Cache des statistiques pour 5 minutes pour optimiser les performances
            $cacheKey = 'maintenance_dashboard_stats_' . Auth::user()->organization_id;
            
            $dashboardData = Cache::remember($cacheKey, now()->addMinutes(5), function () {
                return $this->generateDashboardStatistics();
            });
            
            // Log de l'accès au dashboard
            Log::channel('maintenance')->info('Maintenance dashboard accessed', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'organization_id' => Auth::user()->organization_id,
                'timestamp' => now()->toISOString()
            ]);
            
            return view('admin.maintenance.dashboard', $dashboardData);
            
        } catch (\Exception $e) {
            Log::channel('errors')->error('Maintenance dashboard error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('admin.maintenance.dashboard', [
                'vehicleStats' => [],
                'urgentPlans' => collect(),
                'maintenanceStats' => [],
                'error' => 'Erreur lors du chargement du dashboard de maintenance.'
            ]);
        }
    }

    /**
     * 📊 Générer les statistiques complètes du dashboard
     *
     * @return array
     */
    private function generateDashboardStatistics(): array
    {
        $organizationId = Auth::user()->organization_id;
        
        // Statistiques des véhicules par statut
        $vehicleStats = Vehicle::when($organizationId, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        
        // Plans de maintenance urgents avec calcul intelligent de priorité
        $urgentPlans = MaintenancePlan::with(['vehicle', 'maintenanceType', 'recurrenceUnit'])
            ->whereHas('vehicle', function ($query) use ($organizationId) {
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                $query->whereNull('deleted_at');
            })
            ->where(function ($query) {
                // Échéance par date dans les 45 prochains jours ou dépassée
                $query->where('next_due_date', '<=', Carbon::now()->addDays(45))
                      ->whereNotNull('next_due_date');
            })
            ->orWhere(function ($query) use ($organizationId) {
                // Échéance par kilométrage proche (2000 km ou moins) ou dépassée
                $query->whereNotNull('next_due_mileage')
                      ->whereHas('vehicle', function ($vehicleQuery) use ($organizationId) {
                          if ($organizationId) {
                              $vehicleQuery->where('organization_id', $organizationId);
                          }
                          $vehicleQuery->whereRaw('maintenance_plans.next_due_mileage - vehicles.current_mileage <= 2000')
                                      ->whereNull('deleted_at');
                      });
            })
            ->orderByRaw('
                CASE 
                    WHEN next_due_date IS NOT NULL AND next_due_date < NOW() THEN 1
                    WHEN next_due_mileage IS NOT NULL AND EXISTS (
                        SELECT 1 FROM vehicles v 
                        WHERE v.id = maintenance_plans.vehicle_id 
                        AND maintenance_plans.next_due_mileage <= v.current_mileage
                    ) THEN 2
                    ELSE 3 
                END
            ')
            ->orderBy('next_due_date', 'asc')
            ->limit(8)
            ->get()
            ->map(function ($plan) {
                return $this->formatUrgentPlan($plan);
            });

        // Statistiques de maintenance globales
        $maintenanceStats = $this->calculateMaintenanceStatistics($organizationId);
        
        // Coûts de maintenance ce mois
        $monthlyMaintenanceCosts = MaintenanceLog::when($organizationId, function ($query) use ($organizationId) {
                $query->whereHas('vehicle', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                });
            })
            ->where('performed_on_date', '>=', now()->startOfMonth())
            ->sum('cost') ?? 0;

        // Prochaines maintenances planifiées (7 prochains jours)
        $upcomingMaintenance = MaintenancePlan::with(['vehicle', 'maintenanceType'])
            ->whereHas('vehicle', function ($query) use ($organizationId) {
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                $query->whereNull('deleted_at');
            })
            ->where('next_due_date', '>=', now())
            ->where('next_due_date', '<=', now()->addDays(7))
            ->orderBy('next_due_date')
            ->limit(10)
            ->get();

        return [
            'vehicleStats' => $vehicleStats,
            'urgentPlans' => $urgentPlans,
            'maintenanceStats' => $maintenanceStats,
            'monthlyMaintenanceCosts' => $monthlyMaintenanceCosts,
            'upcomingMaintenance' => $upcomingMaintenance,
            'lastUpdated' => now()->format('d/m/Y H:i')
        ];
    }

    /**
     * 🚨 Formater un plan de maintenance urgent avec calculs de priorité
     *
     * @param MaintenancePlan $plan
     * @return array
     */
    private function formatUrgentPlan(MaintenancePlan $plan): array
    {
        $urgencyLevel = 'low';
        $urgencyPercent = 0;
        $urgencyText = '';
        
        // Calcul de l'urgence par date
        if ($plan->next_due_date) {
            $daysUntilDue = now()->diffInDays($plan->next_due_date, false);
            
            if ($daysUntilDue < 0) {
                $urgencyLevel = 'critical';
                $urgencyPercent = 100;
                $urgencyText = 'En retard de ' . abs($daysUntilDue) . ' jour(s)';
            } elseif ($daysUntilDue <= 7) {
                $urgencyLevel = 'high';
                $urgencyPercent = max(85, 100 - ($daysUntilDue * 5));
                $urgencyText = 'Dans ' . $daysUntilDue . ' jour(s)';
            } elseif ($daysUntilDue <= 30) {
                $urgencyLevel = 'medium';
                $urgencyPercent = max(50, 85 - ($daysUntilDue * 2));
                $urgencyText = 'Dans ' . $daysUntilDue . ' jour(s)';
            } else {
                $urgencyPercent = max(20, 50 - ($daysUntilDue - 30));
                $urgencyText = 'Dans ' . $daysUntilDue . ' jour(s)';
            }
        }
        
        // Calcul de l'urgence par kilométrage (prioritaire sur la date)
        if ($plan->next_due_mileage && $plan->vehicle && $plan->vehicle->current_mileage) {
            $kmRemaining = $plan->next_due_mileage - $plan->vehicle->current_mileage;
            
            if ($kmRemaining <= 0) {
                $urgencyLevel = 'critical';
                $urgencyPercent = 100;
                $urgencyText = 'Dépassé de ' . number_format(abs($kmRemaining), 0, ',', ' ') . ' km';
            } elseif ($kmRemaining <= 500) {
                $urgencyLevel = 'high';
                $urgencyPercent = max(90, 100 - ($kmRemaining / 10));
                $urgencyText = 'Dans ' . number_format($kmRemaining, 0, ',', ' ') . ' km';
            } elseif ($kmRemaining <= 2000) {
                $urgencyLevel = 'medium';
                $urgencyPercent = max(60, 90 - ($kmRemaining / 50));
                $urgencyText = 'Dans ' . number_format($kmRemaining, 0, ',', ' ') . ' km';
            } else {
                $urgencyPercent = max(30, 60 - (($kmRemaining - 2000) / 100));
                $urgencyText = 'Dans ' . number_format($kmRemaining, 0, ',', ' ') . ' km';
            }
        }

        return [
            'id' => $plan->id,
            'vehicle_name' => trim($plan->vehicle->brand . ' ' . $plan->vehicle->model),
            'vehicle_plate' => $plan->vehicle->registration_plate,
            'maintenance_type' => $plan->maintenanceType->name ?? 'N/A',
            'urgency_level' => $urgencyLevel,
            'urgency_percent' => min((int) $urgencyPercent, 100),
            'urgency_text' => $urgencyText,
            'next_due_display' => $plan->next_due_mileage 
                ? number_format($plan->next_due_mileage, 0, ',', ' ') . ' km' 
                : ($plan->next_due_date ? $plan->next_due_date->format('d/m/Y') : 'Non défini'),
            'vehicle_current_mileage' => $plan->vehicle->current_mileage 
                ? number_format($plan->vehicle->current_mileage, 0, ',', ' ') . ' km' 
                : 'Non renseigné',
            'recurrence_display' => $this->formatRecurrence($plan),
        ];
    }

    /**
     * 📈 Calculer les statistiques globales de maintenance
     *
     * @param int|null $organizationId
     * @return array
     */
    private function calculateMaintenanceStatistics(?int $organizationId): array
    {
        $baseQuery = MaintenanceLog::query();
        
        if ($organizationId) {
            $baseQuery->whereHas('vehicle', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            });
        }
        
        return [
            'total_this_month' => (clone $baseQuery)
                ->where('performed_on_date', '>=', now()->startOfMonth())
                ->count(),
            'total_last_month' => (clone $baseQuery)
                ->whereBetween('performed_on_date', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ])
                ->count(),
            'average_cost' => (clone $baseQuery)
                ->where('performed_on_date', '>=', now()->subMonths(6))
                ->avg('cost') ?? 0,
            'pending_plans' => MaintenancePlan::when($organizationId, function ($query) use ($organizationId) {
                $query->whereHas('vehicle', function ($q) use ($organizationId) {
                    $q->where('organization_id', $organizationId);
                });
            })->count(),
        ];
    }

    /**
     * 🔄 Formater l'affichage de récurrence
     *
     * @param MaintenancePlan $plan
     * @return string
     */
    private function formatRecurrence(MaintenancePlan $plan): string
    {
        if (!$plan->recurrenceUnit || !$plan->recurrence_value) {
            return 'Non défini';
        }
        
        $value = $plan->recurrence_value;
        $unit = $plan->recurrenceUnit->name;
        
        return "Tous les {$value} {$unit}";
    }

    /**
     * 📅 Vue calendrier de maintenance
     *
     * @return View
     */
    public function calendar(): View
    {
        $this->authorize('view maintenance dashboard');
        
        $organizationId = Auth::user()->organization_id;
        
        // Récupérer les événements de maintenance pour le calendrier
        $events = MaintenancePlan::with(['vehicle', 'maintenanceType'])
            ->whereHas('vehicle', function ($query) use ($organizationId) {
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                $query->whereNull('deleted_at');
            })
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', now()->subMonth())
            ->where('next_due_date', '<=', now()->addMonths(3))
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->vehicle->registration_plate . ' - ' . $plan->maintenanceType->name,
                    'start' => $plan->next_due_date->format('Y-m-d'),
                    'className' => $this->getEventClassName($plan),
                    'extendedProps' => [
                        'vehicle' => $plan->vehicle->brand . ' ' . $plan->vehicle->model,
                        'plate' => $plan->vehicle->registration_plate,
                        'maintenance_type' => $plan->maintenanceType->name,
                        'notes' => $plan->notes,
                    ]
                ];
            });
        
        return view('admin.maintenance.calendar', compact('events'));
    }

    /**
     * 🚨 Vue des alertes de maintenance
     *
     * @return View
     */
    public function alerts(): View
    {
        $this->authorize('view maintenance dashboard');
        
        $organizationId = Auth::user()->organization_id;
        
        // Alertes critiques (en retard)
        $criticalAlerts = MaintenancePlan::with(['vehicle', 'maintenanceType'])
            ->whereHas('vehicle', function ($query) use ($organizationId) {
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                $query->whereNull('deleted_at');
            })
            ->where(function ($query) {
                $query->where('next_due_date', '<', now())
                      ->orWhereRaw('next_due_mileage < (
                          SELECT current_mileage FROM vehicles 
                          WHERE vehicles.id = maintenance_plans.vehicle_id
                      )');
            })
            ->get();
        
        // Alertes importantes (dans les 7 prochains jours)
        $importantAlerts = MaintenancePlan::with(['vehicle', 'maintenanceType'])
            ->whereHas('vehicle', function ($query) use ($organizationId) {
                if ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }
                $query->whereNull('deleted_at');
            })
            ->where('next_due_date', '>=', now())
            ->where('next_due_date', '<=', now()->addDays(7))
            ->get();
        
        return view('admin.maintenance.alerts', compact('criticalAlerts', 'importantAlerts'));
    }

    /**
     * 📊 API pour obtenir les données du dashboard en AJAX
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function apiData(Request $request): JsonResponse
    {
        $this->authorize('view maintenance dashboard');
        
        try {
            $data = $this->generateDashboardStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::channel('errors')->error('Maintenance dashboard API error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du chargement des données'
            ], 500);
        }
    }

    /**
     * 🎨 Obtenir la classe CSS pour les événements du calendrier
     *
     * @param MaintenancePlan $plan
     * @return string
     */
    private function getEventClassName(MaintenancePlan $plan): string
    {
        if (!$plan->next_due_date) {
            return 'fc-event-default';
        }
        
        $daysUntilDue = now()->diffInDays($plan->next_due_date, false);
        
        if ($daysUntilDue < 0) {
            return 'fc-event-critical'; // Rouge - En retard
        } elseif ($daysUntilDue <= 7) {
            return 'fc-event-warning'; // Orange - Urgent
        } elseif ($daysUntilDue <= 30) {
            return 'fc-event-info'; // Bleu - À venir
        } else {
            return 'fc-event-success'; // Vert - Planifié
        }
    }
}
