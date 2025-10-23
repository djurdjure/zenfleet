<?php

namespace App\Livewire\Admin\Maintenance;

use Livewire\Component;
use App\Services\Maintenance\MaintenanceService;
use App\Models\MaintenanceOperation;

/**
 * 📋 COMPOSANT VUE KANBAN MAINTENANCE
 * 
 * Vue Kanban drag & drop avec Alpine.js + Sortable.js
 * 
 * @version 1.0 Enterprise
 */
class MaintenanceKanban extends Component
{
    protected $listeners = [
        'operationMoved' => 'moveOperation',
        'refreshKanban' => '$refresh',
    ];

    /**
     * Déplacer opération vers nouveau statut
     */
    public function moveOperation($operationId, $newStatus)
    {
        $operation = MaintenanceOperation::findOrFail($operationId);

        // Validation du changement de statut
        if (!$this->canMoveToStatus($operation, $newStatus)) {
            $this->dispatch('kanban-error', 'Changement de statut non autorisé');
            return;
        }

        $operation->update(['status' => $newStatus]);

        $this->dispatch('kanban-success', 'Statut mis à jour avec succès');
        $this->dispatch('refreshKanban');
    }

    /**
     * Vérifier si le changement est autorisé
     */
    private function canMoveToStatus(MaintenanceOperation $operation, string $newStatus): bool
    {
        // Règles de workflow
        $allowedTransitions = [
            MaintenanceOperation::STATUS_PLANNED => [
                MaintenanceOperation::STATUS_IN_PROGRESS,
                MaintenanceOperation::STATUS_CANCELLED
            ],
            MaintenanceOperation::STATUS_IN_PROGRESS => [
                MaintenanceOperation::STATUS_COMPLETED,
                MaintenanceOperation::STATUS_CANCELLED
            ],
            MaintenanceOperation::STATUS_COMPLETED => [], // Pas de changement après completion
            MaintenanceOperation::STATUS_CANCELLED => [], // Pas de changement après annulation
        ];

        return in_array($newStatus, $allowedTransitions[$operation->status] ?? []);
    }

    /**
     * Render component
     */
    public function render(MaintenanceService $maintenanceService)
    {
        $kanbanData = $maintenanceService->getKanbanData();

        return view('livewire.admin.maintenance.maintenance-kanban', [
            'kanbanData' => $kanbanData,
        ]);
    }
}
