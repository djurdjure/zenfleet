<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource pour les alertes de maintenance dans l'API
 */
class MaintenanceAlertResource extends JsonResource
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
            'message' => $this->message,
            'priority' => $this->priority,
            'priority_label' => $this->getPriorityLabel(),
            'type' => $this->type,
            'is_acknowledged' => $this->is_acknowledged,
            'created_at' => $this->created_at?->toISOString(),
            'acknowledged_at' => $this->acknowledged_at?->toISOString(),
            'acknowledgment_notes' => $this->acknowledgment_notes,

            // Relations
            'vehicle' => $this->whenLoaded('vehicle', function () {
                return [
                    'id' => $this->vehicle->id,
                    'registration_plate' => $this->vehicle->registration_plate,
                    'brand' => $this->vehicle->brand,
                    'model' => $this->vehicle->model,
                    'display_name' => $this->vehicle->brand . ' ' . $this->vehicle->model . ' (' . $this->vehicle->registration_plate . ')'
                ];
            }),

            'schedule' => $this->whenLoaded('schedule', function () {
                return [
                    'id' => $this->schedule->id,
                    'next_due_date' => $this->schedule->next_due_date?->toDateString(),
                    'next_due_mileage' => $this->schedule->next_due_mileage,
                    'maintenance_type' => $this->whenLoaded('schedule.maintenanceType', function () {
                        return [
                            'id' => $this->schedule->maintenanceType->id,
                            'name' => $this->schedule->maintenanceType->name,
                            'category' => $this->schedule->maintenanceType->category
                        ];
                    })
                ];
            }),

            // Métadonnées
            'meta' => [
                'days_since_created' => $this->created_at?->diffInDays(now()),
                'urgency_score' => $this->getUrgencyScore(),
                'can_acknowledge' => !$this->is_acknowledged,
                'can_create_operation' => !$this->is_acknowledged && $this->type === 'maintenance_due'
            ]
        ];
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
     * Calculer un score d'urgence
     */
    private function getUrgencyScore(): int
    {
        $score = 0;

        // Score basé sur la priorité
        $score += match($this->priority) {
            'critical' => 100,
            'high' => 75,
            'medium' => 50,
            'low' => 25,
            default => 0
        };

        // Score basé sur l'âge
        $daysOld = $this->created_at?->diffInDays(now()) ?? 0;
        $score += min($daysOld * 2, 50);

        // Pénalité si non acquittée
        if (!$this->is_acknowledged) {
            $score += 25;
        }

        return min($score, 200);
    }
}