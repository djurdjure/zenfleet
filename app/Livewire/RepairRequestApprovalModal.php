<?php

namespace App\Livewire;

use App\Models\RepairRequest;
use App\Services\RepairRequestService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * RepairRequestApprovalModal - Modal pour approuver/rejeter les demandes
 *
 * Features:
 * - Approbation niveau 1 (Supervisor)
 * - Approbation niveau 2 (Fleet Manager)
 * - Rejet avec raison obligatoire
 * - Appel direct au RepairRequestService
 * - Dispatch events pour refresh
 *
 * @version 1.0-Livewire3
 */
class RepairRequestApprovalModal extends Component
{
    /**
     * 🔑 PROPRIÉTÉS DU MODAL
     */
    public bool $open = false;
    public ?int $requestId = null;
    public string $action = 'approve'; // 'approve' or 'reject'
    public string $level = 'supervisor'; // 'supervisor' or 'fleet_manager'

    /**
     * 📝 PROPRIÉTÉS DU FORMULAIRE
     */
    public string $comment = '';
    public string $rejectionReason = '';

    /**
     * 🎛️ LISTENERS POUR ÉVÉNEMENTS
     */
    protected $listeners = [
        'open-approval-modal' => 'openModal',
    ];

    /**
     * 📋 RÈGLES DE VALIDATION
     */
    protected function rules(): array
    {
        return [
            'comment' => 'nullable|string|max:1000',
            'rejectionReason' => $this->action === 'reject' ? 'required|string|min:10|max:1000' : 'nullable|string|max:1000',
        ];
    }

    /**
     * 💬 MESSAGES DE VALIDATION
     */
    protected function messages(): array
    {
        return [
            'rejectionReason.required' => 'La raison du rejet est obligatoire.',
            'rejectionReason.min' => 'La raison doit contenir au moins 10 caractères.',
            'rejectionReason.max' => 'La raison ne peut pas dépasser 1000 caractères.',
            'comment.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * 🔓 OUVERTURE DU MODAL
     */
    public function openModal(int $requestId, string $action = 'approve', string $level = 'supervisor'): void
    {
        $this->requestId = $requestId;
        $this->action = $action;
        $this->level = $level;
        $this->open = true;
        $this->reset(['comment', 'rejectionReason']);
    }

    /**
     * 🔒 FERMETURE DU MODAL
     */
    public function closeModal(): void
    {
        $this->open = false;
        $this->reset(['requestId', 'action', 'level', 'comment', 'rejectionReason']);
    }

    /**
     * ✅ SOUMISSION DU FORMULAIRE
     */
    public function submit(RepairRequestService $repairService): void
    {
        $this->validate();

        try {
            $repairRequest = RepairRequest::findOrFail($this->requestId);
            $user = auth()->user();

            // 🔄 TRAITEMENT SELON ACTION ET NIVEAU
            if ($this->action === 'approve') {
                if ($this->level === 'supervisor') {
                    // Vérifier autorisation
                    $this->authorize('approveLevelOne', $repairRequest);

                    // Approbation superviseur
                    $repairService->approveBySupervisor(
                        $repairRequest,
                        $user,
                        $this->comment ?: null
                    );

                    $message = 'Demande approuvée avec succès. Les gestionnaires de flotte ont été notifiés.';
                } else {
                    // Vérifier autorisation
                    $this->authorize('approveLevelTwo', $repairRequest);

                    // Approbation fleet manager
                    $repairService->approveByFleetManager(
                        $repairRequest,
                        $user,
                        $this->comment ?: null
                    );

                    $message = 'Demande approuvée définitivement. Une opération de maintenance a été créée automatiquement.';
                }
            } else {
                // REJET
                if ($this->level === 'supervisor') {
                    // Vérifier autorisation
                    $this->authorize('rejectLevelOne', $repairRequest);

                    // Rejet superviseur
                    $repairService->rejectBySupervisor(
                        $repairRequest,
                        $user,
                        $this->rejectionReason
                    );

                    $message = 'Demande rejetée. Le chauffeur a été notifié.';
                } else {
                    // Vérifier autorisation
                    $this->authorize('rejectLevelTwo', $repairRequest);

                    // Rejet fleet manager
                    $repairService->rejectByFleetManager(
                        $repairRequest,
                        $user,
                        $this->rejectionReason
                    );

                    $message = 'Demande rejetée définitivement. Le superviseur et le chauffeur ont été notifiés.';
                }
            }

            // ✅ SUCCÈS
            $this->dispatch('repair-request-updated');
            $this->dispatch('refresh-table');
            session()->flash('success', $message);
            $this->closeModal();

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->dispatch('error', 'Vous n\'avez pas l\'autorisation d\'effectuer cette action.');
            Log::warning('RepairRequestApprovalModal: Authorization failed', [
                'user_id' => auth()->id(),
                'request_id' => $this->requestId,
                'action' => $this->action,
                'level' => $this->level,
            ]);
        } catch (\Exception $e) {
            $this->dispatch('error', 'Erreur lors du traitement: ' . $e->getMessage());
            Log::error('RepairRequestApprovalModal: Error processing request', [
                'user_id' => auth()->id(),
                'request_id' => $this->requestId,
                'action' => $this->action,
                'level' => $this->level,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 📊 RÉCUPÉRATION DE LA DEMANDE
     */
    public function getRepairRequestProperty(): ?RepairRequest
    {
        if (!$this->requestId) {
            return null;
        }

        return RepairRequest::with([
            'driver.user',
            'vehicle',
            'supervisor',
        ])->find($this->requestId);
    }

    /**
     * 🎨 RENDER
     */
    public function render(): View
    {
        return view('livewire.repair-request-approval-modal', [
            'repairRequest' => $this->repairRequest,
        ]);
    }
}
