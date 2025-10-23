<?php

namespace App\Services\Maintenance;

use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * 📅 SERVICE PLANIFICATION MAINTENANCE
 * 
 * Gestion des maintenances préventives et récurrentes
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceScheduleService
{
    /**
     * Obtenir les planifications actives nécessitant une action
     */
    public function getActionRequiredSchedules(): Collection
    {
        return MaintenanceSchedule::with(['vehicle', 'maintenanceType'])
            ->where('is_active', true)
            ->where(function($query) {
                $query->where(function($q) {
                    // Maintenance basée sur la date
                    $q->whereNotNull('next_due_date')
                      ->where('next_due_date', '<=', Carbon::today()->addDays(7));
                })
                ->orWhere(function($q) {
                    // Maintenance basée sur le kilométrage
                    $q->whereNotNull('next_due_mileage')
                      ->whereHas('vehicle', function($vehicleQuery) {
                          $vehicleQuery->whereRaw('vehicles.current_mileage >= maintenance_schedules.next_due_mileage - 1000');
                      });
                });
            })
            ->orderBy('next_due_date', 'asc')
            ->get();
    }

    /**
     * Créer des opérations automatiques depuis les planifications dues
     */
    public function createAutomaticOperations(): int
    {
        $schedulesdue = $this->getDueSchedules();
        $created = 0;

        foreach ($schedulesDue as $schedule) {
            try {
                $this->createOperationFromSchedule($schedule);
                $created++;
            } catch (\Exception $e) {
                \Log::error("Erreur création opération auto: " . $e->getMessage());
            }
        }

        return $created;
    }

    /**
     * Créer une opération depuis une planification
     */
    public function createOperationFromSchedule(MaintenanceSchedule $schedule): \App\Models\MaintenanceOperation
    {
        return DB::transaction(function() use ($schedule) {
            $operation = \App\Models\MaintenanceOperation::create([
                'organization_id' => $schedule->organization_id,
                'vehicle_id' => $schedule->vehicle_id,
                'maintenance_type_id' => $schedule->maintenance_type_id,
                'maintenance_schedule_id' => $schedule->id,
                'status' => \App\Models\MaintenanceOperation::STATUS_PLANNED,
                'scheduled_date' => $schedule->next_due_date ?? Carbon::today(),
                'description' => "Maintenance préventive: " . $schedule->maintenanceType->name,
                'created_by' => 1, // System
            ]);

            // Mettre à jour la planification
            $schedule->update(['last_execution_date' => now()]);

            return $operation;
        });
    }

    /**
     * Obtenir les planifications dues
     */
    private function getDueSchedules(): Collection
    {
        return MaintenanceSchedule::with(['vehicle', 'maintenanceType'])
            ->where('is_active', true)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->whereNotNull('next_due_date')
                      ->where('next_due_date', '<=', Carbon::today());
                })
                ->orWhere(function($q) {
                    $q->whereNotNull('next_due_mileage')
                      ->whereHas('vehicle', function($vehicleQuery) {
                          $vehicleQuery->whereRaw('vehicles.current_mileage >= maintenance_schedules.next_due_mileage');
                      });
                });
            })
            ->get();
    }
}
