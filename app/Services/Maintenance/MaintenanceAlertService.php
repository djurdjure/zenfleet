<?php

namespace App\Services\Maintenance;

use App\Models\MaintenanceAlert;
use App\Models\MaintenanceOperation;
use App\Models\MaintenanceSchedule;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * 🔔 SERVICE ALERTES MAINTENANCE
 * 
 * Gestion des notifications et alertes maintenance
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceAlertService
{
    /**
     * Types d'alertes
     */
    const TYPE_OVERDUE = 'overdue';
    const TYPE_DUE_SOON = 'due_soon';
    const TYPE_MILEAGE_THRESHOLD = 'mileage_threshold';
    const TYPE_COST_EXCEEDED = 'cost_exceeded';

    /**
     * Obtenir toutes les alertes actives
     */
    public function getActiveAlerts(): Collection
    {
        return MaintenanceAlert::where('is_active', true)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Créer alerte opération en retard
     */
    public function createOverdueAlert(MaintenanceOperation $operation): MaintenanceAlert
    {
        return MaintenanceAlert::create([
            'organization_id' => $operation->organization_id,
            'maintenance_operation_id' => $operation->id,
            'alert_type' => self::TYPE_OVERDUE,
            'severity' => 'high',
            'title' => "Maintenance en retard",
            'message' => "La maintenance {$operation->maintenanceType->name} pour {$operation->vehicle->registration_plate} est en retard de " . $operation->scheduled_date->diffInDays(Carbon::today()) . " jours.",
            'is_active' => true,
        ]);
    }

    /**
     * Créer alerte maintenance bientôt due
     */
    public function createDueSoonAlert(MaintenanceSchedule $schedule): MaintenanceAlert
    {
        return MaintenanceAlert::create([
            'organization_id' => $schedule->organization_id,
            'maintenance_schedule_id' => $schedule->id,
            'alert_type' => self::TYPE_DUE_SOON,
            'severity' => 'medium',
            'title' => "Maintenance à prévoir",
            'message' => "La maintenance {$schedule->maintenanceType->name} pour {$schedule->vehicle->registration_plate} est prévue dans " . Carbon::today()->diffInDays($schedule->next_due_date) . " jours.",
            'is_active' => true,
        ]);
    }

    /**
     * Scanner et créer toutes les alertes nécessaires
     */
    public function scanAndCreateAlerts(): int
    {
        $alertsCreated = 0;

        // Alertes pour opérations en retard
        $alertsCreated += $this->createOverdueAlerts();

        // Alertes pour maintenances bientôt dues
        $alertsCreated += $this->createDueSoonAlerts();

        return $alertsCreated;
    }

    /**
     * Créer alertes pour opérations en retard
     */
    private function createOverdueAlerts(): int
    {
        $overdueOperations = MaintenanceOperation::where('status', MaintenanceOperation::STATUS_PLANNED)
            ->where('scheduled_date', '<', Carbon::today())
            ->whereDoesntHave('alerts', function($q) {
                $q->where('alert_type', self::TYPE_OVERDUE)
                  ->where('created_at', '>=', Carbon::today());
            })
            ->get();

        $count = 0;
        foreach ($overdueOperations as $operation) {
            try {
                $this->createOverdueAlert($operation);
                $count++;
            } catch (\Exception $e) {
                \Log::error("Erreur création alerte overdue: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Créer alertes pour maintenances bientôt dues
     */
    private function createDueSoonAlerts(): int
    {
        $dueSoonSchedules = MaintenanceSchedule::where('is_active', true)
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->whereDoesntHave('alerts', function($q) {
                $q->where('alert_type', self::TYPE_DUE_SOON)
                  ->where('created_at', '>=', Carbon::today());
            })
            ->get();

        $count = 0;
        foreach ($dueSoonSchedules as $schedule) {
            try {
                $this->createDueSoonAlert($schedule);
                $count++;
            } catch (\Exception $e) {
                \Log::error("Erreur création alerte due soon: " . $e->getMessage());
            }
        }

        return $count;
    }

    /**
     * Marquer alerte comme lue
     */
    public function markAsRead(MaintenanceAlert $alert): bool
    {
        return $alert->update(['is_read' => true]);
    }

    /**
     * Désactiver une alerte
     */
    public function deactivateAlert(MaintenanceAlert $alert): bool
    {
        return $alert->update(['is_active' => false]);
    }
}
