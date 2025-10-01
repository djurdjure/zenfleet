<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * Resource pour les planifications de maintenance dans l'API
 */
class MaintenanceScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'is_active' => $this->is_active,
            'status' => $this->getStatus(),
            'status_label' => $this->getStatusLabel(),

            // Dates et intervalles
            'next_due_date' => $this->next_due_date?->toDateString(),
            'next_due_mileage' => $this->next_due_mileage,
            'interval_km' => $this->interval_km,
            'interval_days' => $this->interval_days,
            'last_service_date' => $this->last_service_date?->toDateString(),
            'last_service_mileage' => $this->last_service_mileage,

            // Alertes
            'alert_km_before' => $this->alert_km_before,
            'alert_days_before' => $this->alert_days_before,

            // Dates système
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Relations
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'registration_plate' => $this->vehicle->registration_plate,
                    'brand' => $this->vehicle->brand,
                    'model' => $this->vehicle->model,
                    'current_mileage' => $this->vehicle->current_mileage,
                    'display_name' => $this->vehicle->brand . ' ' . $this->vehicle->model . ' (' . $this->vehicle->registration_plate . ')'
                ];
            }),

            'maintenance_type' => $this->whenLoaded('maintenanceType', function () {
                return [
                    'id' => $this->maintenanceType->id,
                    'name' => $this->maintenanceType->name,
                    'category' => $this->maintenanceType->category,
                    'category_label' => $this->getCategoryLabel($this->maintenanceType->category),
                    'is_recurring' => $this->maintenanceType->is_recurring,
                    'estimated_duration_minutes' => $this->maintenanceType->estimated_duration_minutes,
                    'estimated_cost' => $this->maintenanceType->estimated_cost
                ];
            }),

            // Métadonnées calculées
            'meta' => [
                'days_remaining' => $this->getDaysRemaining(),
                'km_remaining' => $this->getKmRemaining(),
                'is_overdue' => $this->isOverdue(),
                'is_due_soon' => $this->isDueSoon(),
                'urgency_level' => $this->getUrgencyLevel(),
                'next_service_recommendation' => $this->getNextServiceRecommendation(),
                'compliance_status' => $this->getComplianceStatus(),
                'cost_estimate' => $this->maintenanceType?->estimated_cost,
                'duration_estimate' => $this->maintenanceType?->estimated_duration_minutes
            ]
        ];
    }

    /**
     * Obtenir le statut de la planification
     */
    private function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->isDueSoon()) {
            return 'due_soon';
        }

        return 'scheduled';
    }

    /**
     * Obtenir le libellé du statut
     */
    private function getStatusLabel(): string
    {
        return match($this->getStatus()) {
            'inactive' => 'Inactif',
            'overdue' => 'En Retard',
            'due_soon' => 'Bientôt Dû',
            'scheduled' => 'Planifié',
            default => 'Inconnu'
        };
    }

    /**
     * Obtenir le libellé de la catégorie
     */
    private function getCategoryLabel(string $category): string
    {
        return match($category) {
            'preventive' => 'Préventive',
            'corrective' => 'Corrective',
            'inspection' => 'Inspection',
            'revision' => 'Révision',
            default => $category
        };
    }

    /**
     * Calculer les jours restants
     */
    private function getDaysRemaining(): ?int
    {
        if (!$this->next_due_date) {
            return null;
        }
        return now()->diffInDays($this->next_due_date, false);
    }

    /**
     * Calculer les kilomètres restants
     */
    private function getKmRemaining(): ?int
    {
        if (!$this->next_due_mileage || !$this->vehicle?->current_mileage) {
            return null;
        }
        return $this->next_due_mileage - $this->vehicle->current_mileage;
    }

    /**
     * Vérifier si la maintenance est en retard
     */
    private function isOverdue(): bool
    {
        $dateOverdue = $this->next_due_date && $this->next_due_date->isPast();
        $mileageOverdue = $this->next_due_mileage &&
                         $this->vehicle?->current_mileage &&
                         $this->vehicle->current_mileage >= $this->next_due_mileage;

        return $dateOverdue || $mileageOverdue;
    }

    /**
     * Vérifier si la maintenance est bientôt due
     */
    private function isDueSoon(): bool
    {
        $dateSoon = $this->next_due_date &&
                   $this->next_due_date->lte(Carbon::today()->addDays($this->alert_days_before ?? 7));

        $mileageSoon = $this->next_due_mileage &&
                      $this->vehicle?->current_mileage &&
                      ($this->next_due_mileage - $this->vehicle->current_mileage) <= ($this->alert_km_before ?? 1000);

        return $dateSoon || $mileageSoon;
    }

    /**
     * Obtenir le niveau d'urgence
     */
    private function getUrgencyLevel(): string
    {
        if ($this->isOverdue()) {
            $daysOverdue = $this->next_due_date ? now()->diffInDays($this->next_due_date) : 0;
            if ($daysOverdue > 30) {
                return 'critical';
            }
            return 'high';
        }

        if ($this->isDueSoon()) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Obtenir la recommandation pour le prochain service
     */
    private function getNextServiceRecommendation(): string
    {
        if ($this->isOverdue()) {
            return 'Maintenance urgente requise - Planifier immédiatement';
        }

        if ($this->isDueSoon()) {
            $daysRemaining = $this->getDaysRemaining();
            if ($daysRemaining !== null && $daysRemaining > 0) {
                return "Planifier dans les {$daysRemaining} prochains jours";
            }
            return 'Planifier dès que possible';
        }

        $daysRemaining = $this->getDaysRemaining();
        if ($daysRemaining !== null && $daysRemaining > 0) {
            return "Maintenance prévue dans {$daysRemaining} jours";
        }

        return 'Maintenance programmée selon l\'intervalle défini';
    }

    /**
     * Obtenir le statut de conformité
     */
    private function getComplianceStatus(): array
    {
        $isCompliant = !$this->isOverdue();
        $riskLevel = $this->getUrgencyLevel();

        $status = [
            'is_compliant' => $isCompliant,
            'risk_level' => $riskLevel,
            'compliance_score' => $this->calculateComplianceScore()
        ];

        if (!$isCompliant) {
            $status['non_compliance_reason'] = 'Maintenance en retard';
            $status['action_required'] = 'Planifier immédiatement';
        }

        return $status;
    }

    /**
     * Calculer un score de conformité
     */
    private function calculateComplianceScore(): int
    {
        if ($this->isOverdue()) {
            $daysOverdue = $this->next_due_date ? now()->diffInDays($this->next_due_date) : 0;
            return max(0, 100 - ($daysOverdue * 3)); // -3 points par jour de retard
        }

        if ($this->isDueSoon()) {
            return 80; // Acceptable mais attention requise
        }

        return 100; // Parfaitement conforme
    }
}