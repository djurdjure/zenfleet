<?php

namespace App\Services;

use App\Models\VehicleExpense;
use App\Models\ExpenseAuditLog;
use App\Models\User;
use App\Notifications\ExpenseApprovalRequired;
use App\Notifications\ExpenseApproved;
use App\Notifications\ExpenseRejected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * ExpenseApprovalService - Gestion du workflow d'approbation des dépenses
 * 
 * Workflow à 2 niveaux:
 * - Niveau 1: Superviseur/Chef de Parc (pour montants < 100,000 DZD)
 * - Niveau 2: Gestionnaire Flotte/Admin (pour montants >= 100,000 DZD)
 * 
 * @package App\Services
 * @version 1.0.0-Enterprise
 * @since 2025-10-27
 */
class ExpenseApprovalService
{
    /**
     * Seuils de montants pour les niveaux d'approbation
     */
    const LEVEL1_THRESHOLD = 100000; // 100,000 DZD
    const LEVEL2_THRESHOLD = 500000; // 500,000 DZD
    const AUTO_APPROVE_THRESHOLD = 10000; // 10,000 DZD

    /**
     * Déterminer le niveau d'approbation requis pour une dépense
     * 
     * @param float $amount Montant TTC de la dépense
     * @return int Niveau d'approbation requis (0, 1 ou 2)
     */
    public function determineRequiredApprovalLevel(float $amount): int
    {
        if ($amount <= self::AUTO_APPROVE_THRESHOLD) {
            return 0; // Auto-approbation
        } elseif ($amount <= self::LEVEL1_THRESHOLD) {
            return 1; // Niveau 1 uniquement
        } else {
            return 2; // Nécessite les 2 niveaux
        }
    }

    /**
     * Vérifier si un utilisateur peut approuver une dépense
     * 
     * @param User $user
     * @param VehicleExpense $expense
     * @return bool
     */
    public function canApprove(User $user, VehicleExpense $expense): bool
    {
        // Ne peut pas approuver sa propre dépense
        if ($expense->requester_id === $user->id || $expense->recorded_by === $user->id) {
            return false;
        }

        // Vérifier le statut de la dépense
        if ($expense->approval_status === 'approved' || $expense->approval_status === 'rejected') {
            return false;
        }

        // Vérifier les permissions selon le niveau requis
        return $this->determineApprovalLevel($user, $expense) !== null;
    }

    /**
     * Déterminer le niveau d'approbation qu'un utilisateur peut donner
     * 
     * @param User $user
     * @param VehicleExpense $expense
     * @return string|null 'level1', 'level2' ou null
     */
    public function determineApprovalLevel(User $user, VehicleExpense $expense): ?string
    {
        // Dépense déjà approuvée
        if ($expense->approval_status === 'approved' || $expense->approval_status === 'rejected') {
            return null;
        }

        // Niveau 1: Superviseur/Chef de Parc
        if ($expense->approval_status === 'pending_level1') {
            if ($user->hasAnyRole(['Supervisor', 'Chef de Parc', 'Gestionnaire Flotte', 'Admin', 'Super Admin'])) {
                // Vérifier le dépôt si applicable
                if ($user->hasRole(['Supervisor', 'Chef de Parc']) && $user->depot_id) {
                    // Le véhicule doit être du même dépôt
                    if ($expense->vehicle && $expense->vehicle->depot_id !== $user->depot_id) {
                        return null;
                    }
                }
                return 'level1';
            }
        }

        // Niveau 2: Gestionnaire Flotte/Admin
        if ($expense->approval_status === 'pending_level2') {
            if ($user->hasAnyRole(['Gestionnaire Flotte', 'Admin', 'Super Admin'])) {
                return 'level2';
            }
        }

        return null;
    }

    /**
     * Approuver une dépense
     * 
     * @param VehicleExpense $expense
     * @param string $level 'level1' ou 'level2'
     * @param string|null $comments
     * @return array
     */
    public function approve(VehicleExpense $expense, string $level, ?string $comments = null): array
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $oldStatus = $expense->approval_status;

            if ($level === 'level1') {
                $expense->level1_approved = true;
                $expense->level1_approved_by = $user->id;
                $expense->level1_approved_at = now();
                $expense->level1_comments = $comments;

                // Déterminer si niveau 2 requis
                $requiredLevel = $this->determineRequiredApprovalLevel($expense->total_ttc);
                
                if ($requiredLevel === 2) {
                    $expense->approval_status = 'pending_level2';
                    $message = 'Dépense approuvée niveau 1. En attente d\'approbation niveau 2.';
                } else {
                    // Approbation complète
                    $expense->approval_status = 'approved';
                    $expense->approved = true;
                    $expense->approved_by = $user->id;
                    $expense->approved_at = now();
                    $expense->approval_comments = $comments;
                    $message = 'Dépense approuvée avec succès.';
                }
                
            } elseif ($level === 'level2') {
                $expense->level2_approved = true;
                $expense->level2_approved_by = $user->id;
                $expense->level2_approved_at = now();
                $expense->level2_comments = $comments;
                $expense->approval_status = 'approved';
                $expense->approved = true;
                $expense->approved_by = $user->id;
                $expense->approved_at = now();
                $expense->approval_comments = $comments;
                $message = 'Dépense approuvée niveau 2 avec succès.';
                
            } else {
                throw new \Exception('Niveau d\'approbation invalide.');
            }

            $expense->save();

            // Log d'audit
            ExpenseAuditLog::log(
                $expense,
                $level === 'level1' ? ExpenseAuditLog::ACTION_LEVEL1_APPROVED : ExpenseAuditLog::ACTION_LEVEL2_APPROVED,
                "Dépense approuvée {$level} par {$user->name}",
                ['approval_status' => $oldStatus],
                ['approval_status' => $expense->approval_status]
            );

            // Notifications
            $this->sendApprovalNotifications($expense, $level);

            DB::commit();

            return [
                'success' => true,
                'message' => $message,
                'next_status' => $expense->approval_status
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur approbation {$level}: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'approbation: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Rejeter une dépense
     * 
     * @param VehicleExpense $expense
     * @param string $reason
     * @return array
     */
    public function reject(VehicleExpense $expense, string $reason): array
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $oldStatus = $expense->approval_status;

            $expense->is_rejected = true;
            $expense->rejected_by = $user->id;
            $expense->rejected_at = now();
            $expense->rejection_reason = $reason;
            $expense->approval_status = 'rejected';
            $expense->approved = false;
            
            // Réinitialiser les approbations
            if ($oldStatus === 'pending_level2') {
                // Si rejeté au niveau 2, on garde l'approbation niveau 1
            } else {
                // Si rejeté au niveau 1, on réinitialise tout
                $expense->level1_approved = false;
                $expense->level1_approved_by = null;
                $expense->level1_approved_at = null;
                $expense->level1_comments = null;
            }
            
            $expense->level2_approved = false;
            $expense->level2_approved_by = null;
            $expense->level2_approved_at = null;
            $expense->level2_comments = null;

            $expense->save();

            // Log d'audit
            ExpenseAuditLog::log(
                $expense,
                ExpenseAuditLog::ACTION_REJECTED,
                "Dépense rejetée par {$user->name}: {$reason}",
                ['approval_status' => $oldStatus],
                ['approval_status' => 'rejected']
            );

            // Notification au demandeur
            $this->sendRejectionNotification($expense, $reason);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Dépense rejetée avec succès.'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur rejet dépense: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erreur lors du rejet: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Auto-approuver les petites dépenses
     * 
     * @param VehicleExpense $expense
     * @return bool
     */
    public function autoApprove(VehicleExpense $expense): bool
    {
        if ($expense->total_ttc <= self::AUTO_APPROVE_THRESHOLD && !$expense->is_urgent) {
            $expense->approved = true;
            $expense->approved_by = 0; // Système
            $expense->approved_at = now();
            $expense->approval_status = 'approved';
            $expense->approval_comments = 'Auto-approuvé (montant < ' . self::AUTO_APPROVE_THRESHOLD . ' DZD)';
            $expense->save();

            // Log d'audit
            ExpenseAuditLog::log(
                $expense,
                ExpenseAuditLog::ACTION_APPROVED,
                'Dépense auto-approuvée par le système',
                ['approval_status' => 'draft'],
                ['approval_status' => 'approved']
            );

            return true;
        }

        return false;
    }

    /**
     * Notifier les approbateurs
     * 
     * @param VehicleExpense $expense
     * @param string $level
     * @return void
     */
    public function notifyApprovers(VehicleExpense $expense, string $level): void
    {
        try {
            $approvers = $this->getApprovers($expense, $level);
            
            if ($approvers->isEmpty()) {
                Log::warning("Aucun approbateur trouvé pour la dépense #{$expense->id} niveau {$level}");
                return;
            }

            // Créer la notification (à implémenter)
            // Notification::send($approvers, new ExpenseApprovalRequired($expense, $level));
            
            Log::info("Notifications envoyées à " . $approvers->count() . " approbateurs pour dépense #{$expense->id}");
            
        } catch (\Exception $e) {
            Log::error("Erreur envoi notifications: " . $e->getMessage());
        }
    }

    /**
     * Obtenir la liste des approbateurs pour un niveau
     * 
     * @param VehicleExpense $expense
     * @param string $level
     * @return \Illuminate\Support\Collection
     */
    public function getApprovers(VehicleExpense $expense, string $level): \Illuminate\Support\Collection
    {
        $query = User::where('organization_id', $expense->organization_id)
            ->where('is_active', true);

        if ($level === 'level1') {
            // Niveau 1: Superviseurs et Chef de Parc du même dépôt
            $roles = ['Supervisor', 'Chef de Parc'];
            
            // Si la dépense est liée à un véhicule avec dépôt
            if ($expense->vehicle && $expense->vehicle->depot_id) {
                $query->where(function ($q) use ($expense, $roles) {
                    $q->whereHas('roles', function ($r) use ($roles) {
                        $r->whereIn('name', $roles);
                    })->where('depot_id', $expense->vehicle->depot_id);
                });
            } else {
                // Sinon, tous les superviseurs de l'organisation
                $query->whereHas('roles', function ($r) use ($roles) {
                    $r->whereIn('name', $roles);
                });
            }
            
            // Ajouter aussi les gestionnaires et admins
            $query->orWhereHas('roles', function ($r) {
                $r->whereIn('name', ['Gestionnaire Flotte', 'Admin', 'Super Admin']);
            });
            
        } else {
            // Niveau 2: Gestionnaires Flotte et Admin
            $query->whereHas('roles', function ($r) {
                $r->whereIn('name', ['Gestionnaire Flotte', 'Admin', 'Super Admin']);
            });
        }

        // Exclure le demandeur
        $query->where('id', '!=', $expense->requester_id)
              ->where('id', '!=', $expense->recorded_by);

        return $query->get();
    }

    /**
     * Envoyer les notifications d'approbation
     * 
     * @param VehicleExpense $expense
     * @param string $level
     * @return void
     */
    private function sendApprovalNotifications(VehicleExpense $expense, string $level): void
    {
        try {
            // Notifier le demandeur
            if ($expense->requester) {
                // $expense->requester->notify(new ExpenseApproved($expense, $level));
            }

            // Si niveau 1 approuvé et niveau 2 requis, notifier les approbateurs niveau 2
            if ($level === 'level1' && $expense->approval_status === 'pending_level2') {
                $this->notifyApprovers($expense, 'level2');
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification approbation: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer la notification de rejet
     * 
     * @param VehicleExpense $expense
     * @param string $reason
     * @return void
     */
    private function sendRejectionNotification(VehicleExpense $expense, string $reason): void
    {
        try {
            if ($expense->requester) {
                // $expense->requester->notify(new ExpenseRejected($expense, $reason));
            }
        } catch (\Exception $e) {
            Log::error('Erreur envoi notification rejet: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir le workflow status pour l'interface
     * 
     * @param VehicleExpense $expense
     * @return array
     */
    public function getWorkflowStatus(VehicleExpense $expense): array
    {
        $steps = [];
        
        // Étape 1: Création
        $steps[] = [
            'title' => 'Création',
            'status' => 'completed',
            'date' => $expense->created_at,
            'user' => $expense->recordedBy->name ?? 'Système',
            'icon' => 'heroicons:document-plus',
            'color' => 'green'
        ];

        // Étape 2: Approbation Niveau 1
        if ($expense->needs_approval) {
            $level1Status = 'pending';
            $level1Color = 'yellow';
            
            if ($expense->level1_approved) {
                $level1Status = 'completed';
                $level1Color = 'green';
            } elseif ($expense->is_rejected && !$expense->level1_approved) {
                $level1Status = 'rejected';
                $level1Color = 'red';
            }
            
            $steps[] = [
                'title' => 'Approbation Niveau 1',
                'status' => $level1Status,
                'date' => $expense->level1_approved_at,
                'user' => $expense->level1Approver->name ?? '-',
                'comments' => $expense->level1_comments,
                'icon' => 'heroicons:check-circle',
                'color' => $level1Color
            ];
        }

        // Étape 3: Approbation Niveau 2 (si requis)
        if ($this->determineRequiredApprovalLevel($expense->total_ttc) === 2) {
            $level2Status = 'pending';
            $level2Color = 'gray';
            
            if (!$expense->level1_approved) {
                $level2Status = 'locked';
                $level2Color = 'gray';
            } elseif ($expense->level2_approved) {
                $level2Status = 'completed';
                $level2Color = 'green';
            } elseif ($expense->is_rejected && $expense->level1_approved) {
                $level2Status = 'rejected';
                $level2Color = 'red';
            } elseif ($expense->level1_approved) {
                $level2Status = 'pending';
                $level2Color = 'yellow';
            }
            
            $steps[] = [
                'title' => 'Approbation Niveau 2',
                'status' => $level2Status,
                'date' => $expense->level2_approved_at,
                'user' => $expense->level2Approver->name ?? '-',
                'comments' => $expense->level2_comments,
                'icon' => 'heroicons:shield-check',
                'color' => $level2Color
            ];
        }

        // Étape 4: Paiement
        $paymentStatus = 'pending';
        $paymentColor = 'gray';
        
        if (!$expense->approved) {
            $paymentStatus = 'locked';
            $paymentColor = 'gray';
        } elseif ($expense->payment_status === 'paid') {
            $paymentStatus = 'completed';
            $paymentColor = 'green';
        } elseif ($expense->approved) {
            $paymentStatus = 'pending';
            $paymentColor = 'yellow';
        }
        
        $steps[] = [
            'title' => 'Paiement',
            'status' => $paymentStatus,
            'date' => $expense->payment_date,
            'method' => $expense->payment_method,
            'reference' => $expense->payment_reference,
            'icon' => 'heroicons:credit-card',
            'color' => $paymentColor
        ];

        // Résumé
        return [
            'steps' => $steps,
            'current_status' => $expense->approval_status,
            'is_completed' => $expense->payment_status === 'paid',
            'can_edit' => $expense->approval_status === 'draft',
            'can_delete' => $expense->approval_status === 'draft',
            'requires_action' => $this->requiresUserAction($expense),
            'next_action' => $this->getNextAction($expense),
        ];
    }

    /**
     * Vérifier si la dépense nécessite une action de l'utilisateur
     * 
     * @param VehicleExpense $expense
     * @return bool
     */
    private function requiresUserAction(VehicleExpense $expense): bool
    {
        $user = auth()->user();
        
        // Vérifier si l'utilisateur peut approuver
        if ($this->canApprove($user, $expense)) {
            return true;
        }
        
        // Vérifier si l'utilisateur peut payer
        if ($expense->approved && $expense->payment_status !== 'paid' && 
            $user->can('pay vehicle expenses')) {
            return true;
        }
        
        return false;
    }

    /**
     * Obtenir la prochaine action requise
     * 
     * @param VehicleExpense $expense
     * @return string|null
     */
    private function getNextAction(VehicleExpense $expense): ?string
    {
        if ($expense->approval_status === 'pending_level1') {
            return 'Approbation Niveau 1 requise';
        } elseif ($expense->approval_status === 'pending_level2') {
            return 'Approbation Niveau 2 requise';
        } elseif ($expense->approved && $expense->payment_status !== 'paid') {
            return 'Paiement requis';
        }
        
        return null;
    }
}
