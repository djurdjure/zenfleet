<?php

namespace App\Services\Maintenance;

use App\Models\MaintenanceOperation;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use App\Models\MaintenanceProvider;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * 🔧 SERVICE MAINTENANCE ENTERPRISE-GRADE
 * 
 * Service centralisé pour toute la logique métier maintenance
 * Pattern: Service Layer Architecture
 * 
 * @version 1.0 Enterprise
 * @author ZenFleet Architecture Team
 */
class MaintenanceService
{
    /**
     * Obtenir les opérations de maintenance avec filtres avancés
     */
    public function getOperations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = MaintenanceOperation::with([
            'vehicle:id,registration_plate,brand,model,vehicle_type_id',
            'vehicle.vehicleType:id,name',
            'maintenanceType:id,name,category,color',
            'provider:id,name,contact_phone',
            'creator:id,name'
        ]);

        // Filtre par recherche (immatriculation, type, fournisseur)
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($q) use ($search) {
                    $q->where('registration_plate', 'ILIKE', "%{$search}%")
                      ->orWhere('brand', 'ILIKE', "%{$search}%")
                      ->orWhere('model', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('maintenanceType', function($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('provider', function($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%");
                })
                ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filtre par statut
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtre par type de maintenance
        if (!empty($filters['maintenance_type_id'])) {
            $query->where('maintenance_type_id', $filters['maintenance_type_id']);
        }

        // Filtre par fournisseur
        if (!empty($filters['provider_id'])) {
            $query->where('provider_id', $filters['provider_id']);
        }

        // Filtre par véhicule
        if (!empty($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        // Filtre par catégorie de maintenance
        if (!empty($filters['category'])) {
            $query->whereHas('maintenanceType', function($q) use ($filters) {
                $q->where('category', $filters['category']);
            });
        }

        // Filtre par période
        if (!empty($filters['date_from'])) {
            $query->where('scheduled_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('scheduled_date', '<=', $filters['date_to']);
        }

        // Filtre par coût
        if (!empty($filters['cost_min'])) {
            $query->where('total_cost', '>=', $filters['cost_min']);
        }
        if (!empty($filters['cost_max'])) {
            $query->where('total_cost', '<=', $filters['cost_max']);
        }

        // Filtre opérations en retard
        if (!empty($filters['overdue'])) {
            $query->where('status', MaintenanceOperation::STATUS_PLANNED)
                  ->where('scheduled_date', '<', Carbon::today());
        }

        // Tri
        $sortField = $filters['sort'] ?? 'scheduled_date';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Obtenir les analytics dashboard
     */
    public function getAnalytics(array $filters = []): array
    {
        $cacheKey = 'maintenance_analytics_' . auth()->user()->organization_id . '_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, 300, function() use ($filters) {
            $query = MaintenanceOperation::query();

            // Appliquer filtre de période si fourni
            if (!empty($filters['period'])) {
                $this->applyPeriodFilter($query, $filters['period']);
            }

            $baseQuery = clone $query;

            return [
                // Métriques principales
                'total_operations' => $baseQuery->count(),
                'planned_operations' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_PLANNED)->count(),
                'in_progress_operations' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_IN_PROGRESS)->count(),
                'completed_operations' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_COMPLETED)->count(),
                'cancelled_operations' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_CANCELLED)->count(),
                
                // Opérations en retard
                'overdue_operations' => (clone $baseQuery)
                    ->where('status', MaintenanceOperation::STATUS_PLANNED)
                    ->where('scheduled_date', '<', Carbon::today())
                    ->count(),
                
                // Coûts
                'total_cost' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_COMPLETED)->sum('total_cost') ?? 0,
                'avg_cost' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_COMPLETED)->avg('total_cost') ?? 0,
                'cost_planned' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_PLANNED)->sum('total_cost') ?? 0,
                
                // Durées
                'avg_duration_minutes' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_COMPLETED)->avg('duration_minutes') ?? 0,
                'total_duration_hours' => ((clone $baseQuery)->where('status', MaintenanceOperation::STATUS_COMPLETED)->sum('duration_minutes') ?? 0) / 60,
                
                // Véhicules en maintenance
                'vehicles_in_maintenance' => (clone $baseQuery)->where('status', MaintenanceOperation::STATUS_IN_PROGRESS)->distinct('vehicle_id')->count('vehicle_id'),
                
                // Tendances (comparaison période précédente)
                'trends' => $this->calculateTrends($filters),
                
                // Top véhicules avec plus de maintenances
                'top_vehicles' => $this->getTopMaintenanceVehicles(5, $filters),
                
                // Top types de maintenance
                'top_types' => $this->getTopMaintenanceTypes(5, $filters),
                
                // Distribution par statut
                'status_distribution' => $this->getStatusDistribution($filters),
                
                // Prochaines maintenances (7 prochains jours)
                'upcoming_count' => (clone $baseQuery)
                    ->where('status', MaintenanceOperation::STATUS_PLANNED)
                    ->whereBetween('scheduled_date', [Carbon::today(), Carbon::today()->addDays(7)])
                    ->count(),
            ];
        });
    }

    /**
     * Créer une nouvelle opération de maintenance
     */
    public function createOperation(array $data): MaintenanceOperation
    {
        DB::beginTransaction();
        try {
            // Ajouter l'utilisateur créateur
            $data['created_by'] = auth()->id();
            $data['organization_id'] = auth()->user()->organization_id;

            // Créer l'opération
            $operation = MaintenanceOperation::create($data);

            // Si une planification est liée, la marquer comme traitée
            if (!empty($data['maintenance_schedule_id'])) {
                $operation->schedule()->update(['last_execution_date' => now()]);
            }

            // Invalider le cache
            $this->invalidateCache();

            DB::commit();
            return $operation->load(['vehicle', 'maintenanceType', 'provider']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mettre à jour une opération
     */
    public function updateOperation(MaintenanceOperation $operation, array $data): MaintenanceOperation
    {
        DB::beginTransaction();
        try {
            $operation->update($data);
            
            // Invalider le cache
            $this->invalidateCache();

            DB::commit();
            return $operation->fresh(['vehicle', 'maintenanceType', 'provider']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Démarrer une opération
     */
    public function startOperation(MaintenanceOperation $operation): bool
    {
        DB::beginTransaction();
        try {
            $result = $operation->start();
            
            if ($result) {
                // Créer une alerte si nécessaire
                $this->createMaintenanceStartedAlert($operation);
                
                // Invalider le cache
                $this->invalidateCache();
            }

            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Terminer une opération
     */
    public function completeOperation(MaintenanceOperation $operation, array $data): bool
    {
        DB::beginTransaction();
        try {
            $result = $operation->complete($data);
            
            if ($result) {
                // Mettre à jour le véhicule si nécessaire
                if (!empty($data['mileage_at_maintenance'])) {
                    $this->updateVehicleMileage($operation->vehicle, $data['mileage_at_maintenance']);
                }
                
                // Invalider le cache
                $this->invalidateCache();
            }

            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Annuler une opération
     */
    public function cancelOperation(MaintenanceOperation $operation): bool
    {
        DB::beginTransaction();
        try {
            $result = $operation->cancel();
            
            if ($result) {
                $this->invalidateCache();
            }

            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Supprimer une opération
     */
    public function deleteOperation(MaintenanceOperation $operation): bool
    {
        DB::beginTransaction();
        try {
            // Supprimer les documents associés
            $operation->documents()->delete();
            
            $result = $operation->delete();
            
            if ($result) {
                $this->invalidateCache();
            }

            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtenir les opérations pour vue Kanban
     */
    public function getKanbanData(array $filters = []): array
    {
        $statuses = [
            MaintenanceOperation::STATUS_PLANNED,
            MaintenanceOperation::STATUS_IN_PROGRESS,
            MaintenanceOperation::STATUS_COMPLETED,
        ];

        $kanbanData = [];
        
        foreach ($statuses as $status) {
            $operations = MaintenanceOperation::with([
                'vehicle:id,registration_plate,brand,model',
                'maintenanceType:id,name,category,color',
                'provider:id,name'
            ])
            ->where('status', $status)
            ->orderBy('scheduled_date', 'asc')
            ->limit(50)
            ->get();

            $kanbanData[$status] = [
                'status' => $status,
                'label' => MaintenanceOperation::STATUSES[$status],
                'count' => $operations->count(),
                'operations' => $operations,
            ];
        }

        return $kanbanData;
    }

    /**
     * Obtenir les opérations pour vue calendrier
     */
    public function getCalendarEvents(Carbon $startDate, Carbon $endDate): Collection
    {
        return MaintenanceOperation::with([
            'vehicle:id,registration_plate,brand,model',
            'maintenanceType:id,name,category,color'
        ])
        ->whereBetween('scheduled_date', [$startDate, $endDate])
        ->whereIn('status', [MaintenanceOperation::STATUS_PLANNED, MaintenanceOperation::STATUS_IN_PROGRESS])
        ->get()
        ->map(function($operation) {
            return [
                'id' => $operation->id,
                'title' => $operation->vehicle->registration_plate . ' - ' . $operation->maintenanceType->name,
                'start' => $operation->scheduled_date->toDateString(),
                'backgroundColor' => $operation->maintenanceType->getCategoryColor(),
                'borderColor' => $operation->maintenanceType->getCategoryColor(),
                'textColor' => '#FFFFFF',
                'extendedProps' => [
                    'vehicle' => $operation->vehicle->registration_plate,
                    'type' => $operation->maintenanceType->name,
                    'status' => $operation->status,
                    'provider' => $operation->provider?->name,
                    'cost' => $operation->total_cost,
                ],
            ];
        });
    }

    /**
     * MÉTHODES PRIVÉES - HELPERS
     */

    private function applyPeriodFilter($query, string $period): void
    {
        switch ($period) {
            case 'today':
                $query->whereDate('scheduled_date', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('scheduled_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('scheduled_date', Carbon::now()->month)
                      ->whereYear('scheduled_date', Carbon::now()->year);
                break;
            case 'quarter':
                $query->whereBetween('scheduled_date', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]);
                break;
            case 'year':
                $query->whereYear('scheduled_date', Carbon::now()->year);
                break;
        }
    }

    private function calculateTrends(array $filters): array
    {
        // TODO: Implémenter calcul des tendances
        return [
            'operations' => 0,
            'cost' => 0,
            'duration' => 0,
        ];
    }

    private function getTopMaintenanceVehicles(int $limit, array $filters): Collection
    {
        return MaintenanceOperation::select('vehicle_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_cost) as total_cost'))
            ->with('vehicle:id,registration_plate,brand,model')
            ->groupBy('vehicle_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    private function getTopMaintenanceTypes(int $limit, array $filters): Collection
    {
        return MaintenanceOperation::select('maintenance_type_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_cost) as total_cost'))
            ->with('maintenanceType:id,name,category,color')
            ->groupBy('maintenance_type_id')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    private function getStatusDistribution(array $filters): Collection
    {
        return MaintenanceOperation::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->status => $item->count];
            });
    }

    private function createMaintenanceStartedAlert(MaintenanceOperation $operation): void
    {
        // TODO: Créer notification/alerte
    }

    private function updateVehicleMileage(Vehicle $vehicle, int $mileage): void
    {
        if ($mileage > $vehicle->current_mileage) {
            $vehicle->update(['current_mileage' => $mileage]);
        }
    }

    private function invalidateCache(): void
    {
        Cache::tags(['maintenance', 'maintenance_analytics_' . auth()->user()->organization_id])->flush();
    }
}
