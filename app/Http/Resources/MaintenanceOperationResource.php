<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les opérations de maintenance dans l'API
 */
class MaintenanceOperationResource extends JsonResource
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
            'description' => $this->description,
            'notes' => $this->notes,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'priority' => $this->priority,
            'priority_label' => $this->getPriorityLabel(),

            // Dates
            'scheduled_date' => $this->scheduled_date?->toDateString(),
            'started_date' => $this->started_date?->toISOString(),
            'completed_date' => $this->completed_date?->toISOString(),
            'cancelled_date' => $this->cancelled_date?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Données financières
            'estimated_cost' => $this->estimated_cost,
            'total_cost' => $this->total_cost,
            'cost_variance' => $this->getCostVariance(),
            'cost_variance_percentage' => $this->getCostVariancePercentage(),

            // Données techniques
            'duration_minutes' => $this->duration_minutes,
            'estimated_duration_minutes' => $this->maintenanceType?->estimated_duration_minutes,
            'duration_variance' => $this->getDurationVariance(),
            'mileage_at_service' => $this->mileage_at_service,

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
                    'estimated_duration_minutes' => $this->maintenanceType->estimated_duration_minutes,
                    'estimated_cost' => $this->maintenanceType->estimated_cost
                ];
            }),

            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'name' => $this->provider->name,
                    'company_name' => $this->provider->company_name,
                    'phone' => $this->provider->phone,
                    'email' => $this->provider->email,
                    'rating' => $this->provider->rating
                ];
            }),

            // Métadonnées
            'meta' => [
                'is_overdue' => $this->isOverdue(),
                'days_until_scheduled' => $this->getDaysUntilScheduled(),
                'efficiency_score' => $this->getEfficiencyScore(),
                'can_start' => $this->canStart(),
                'can_complete' => $this->canComplete(),
                'can_cancel' => $this->canCancel(),
                'progress_percentage' => $this->getProgressPercentage()
            ]
        ];
    }

    /**
     * Obtenir le libellé du statut
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'planned' => 'Planifiée',
            'in_progress' => 'En Cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default => $this->status
        };
    }

    /**
     * Obtenir le libellé de la priorité
     */
    private function getPriorityLabel(): string
    {
        return match($this->priority) {
            'low' => 'Faible',
            'medium' => 'Moyenne',
            'high' => 'Élevée',
            'critical' => 'Critique',
            default => $this->priority
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
     * Calculer la variance de coût
     */
    private function getCostVariance(): ?float
    {
        if (!$this->estimated_cost || !$this->total_cost) {
            return null;
        }
        return $this->total_cost - $this->estimated_cost;
    }

    /**
     * Calculer le pourcentage de variance de coût
     */
    private function getCostVariancePercentage(): ?float
    {
        if (!$this->estimated_cost || !$this->total_cost) {
            return null;
        }
        return (($this->total_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
    }

    /**
     * Calculer la variance de durée
     */
    private function getDurationVariance(): ?int
    {
        $estimatedDuration = $this->maintenanceType?->estimated_duration_minutes;
        if (!$estimatedDuration || !$this->duration_minutes) {
            return null;
        }
        return $this->duration_minutes - $estimatedDuration;
    }

    /**
     * Vérifier si l'opération est en retard
     */
    private function isOverdue(): bool
    {
        return $this->scheduled_date &&
               $this->scheduled_date->isPast() &&
               !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Obtenir les jours jusqu'à la date planifiée
     */
    private function getDaysUntilScheduled(): ?int
    {
        if (!$this->scheduled_date) {
            return null;
        }
        return now()->diffInDays($this->scheduled_date, false);
    }

    /**
     * Calculer un score d'efficacité
     */
    private function getEfficiencyScore(): ?int
    {
        if ($this->status !== 'completed') {
            return null;
        }

        $score = 100;

        // Pénalité pour dépassement de coût
        $costVariancePercentage = $this->getCostVariancePercentage();
        if ($costVariancePercentage && $costVariancePercentage > 0) {
            $score -= min($costVariancePercentage, 50);
        }

        // Pénalité pour dépassement de durée
        $durationVariance = $this->getDurationVariance();
        $estimatedDuration = $this->maintenanceType?->estimated_duration_minutes;
        if ($durationVariance && $estimatedDuration && $durationVariance > 0) {
            $durationVariancePercentage = ($durationVariance / $estimatedDuration) * 100;
            $score -= min($durationVariancePercentage, 30);
        }

        // Pénalité pour retard
        if ($this->completed_date && $this->scheduled_date && $this->completed_date->gt($this->scheduled_date)) {
            $score -= 20;
        }

        return max(0, (int) $score);
    }

    /**
     * Vérifier si l'opération peut être démarrée
     */
    private function canStart(): bool
    {
        return $this->status === 'planned';
    }

    /**
     * Vérifier si l'opération peut être terminée
     */
    private function canComplete(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifier si l'opération peut être annulée
     */
    private function canCancel(): bool
    {
        return in_array($this->status, ['planned', 'in_progress']);
    }

    /**
     * Calculer le pourcentage de progression
     */
    private function getProgressPercentage(): int
    {
        return match($this->status) {
            'planned' => 0,
            'in_progress' => 50,
            'completed' => 100,
            'cancelled' => 0,
            default => 0
        };
    }
}