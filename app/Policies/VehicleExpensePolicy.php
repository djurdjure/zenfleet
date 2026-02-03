<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleExpense;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ====================================================================
 * 💰 VEHICLE EXPENSE POLICY - ENTERPRISE GRADE
 * ====================================================================
 * 
 * Policy de sécurité pour la gestion des autorisations du module
 * de dépenses véhicules avec isolation multi-tenant.
 * 
 * @package App\Policies
 * @version 1.0.0-Enterprise
 * @since 2025-10-28
 * ====================================================================
 */
class VehicleExpensePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can expenses.view.any.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('expenses.view') || $user->can('expenses.view.any');
    }

    /**
     * Determine whether the user can view the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function view(User $user, VehicleExpense $expense): bool
    {
        // Super Admin et Admin peuvent tout voir
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Finance'])) {
            return true;
        }

        // Vérifier la permission générale
        if (!$user->can('expenses.view')) {
            return false;
        }

        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Permission spéciale pour voir toutes les dépenses de l'organisation
        if ($user->can('expenses.view.all')) {
            return true;
        }

        // Les managers peuvent voir les dépenses de leur équipe
        if ($user->hasRole('Manager') || $user->hasRole('Gestionnaire Flotte')) {
            return true;
        }

        // Les chauffeurs ne peuvent voir que leurs propres dépenses
        if ($user->hasRole('Chauffeur')) {
            return $expense->driver_id === $user->id || 
                   $expense->requester_id === $user->id ||
                   $expense->recorded_by === $user->id;
        }

        // Par défaut, on peut voir les dépenses qu'on a créées
        return $expense->recorded_by === $user->id || 
               $expense->requester_id === $user->id;
    }

    /**
     * Determine whether the user can expenses.create.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('expenses.create');
    }

    /**
     * Determine whether the user can update the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function update(User $user, VehicleExpense $expense): bool
    {
        // Vérifier la permission de base
        if (!$user->can('expenses.update')) {
            return false;
        }

        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Super Admin et Admin peuvent tout modifier
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Finance'])) {
            // Vérifier si la dépense est approuvée
            if ($expense->approval_status === 'approved' && !$user->can('expenses.update.approved')) {
                return false;
            }
            return true;
        }

        // Les dépenses approuvées ne peuvent pas être modifiées sauf permission spéciale
        if ($expense->approval_status === 'approved') {
            return $user->can('expenses.update.approved');
        }

        // Les dépenses payées ne peuvent jamais être modifiées
        if ($expense->payment_status === 'paid') {
            return false;
        }

        // Les managers peuvent modifier les dépenses de leur équipe
        if ($user->hasRole(['Manager', 'Gestionnaire Flotte'])) {
            return $expense->approval_status === 'draft' || 
                   $expense->approval_status === 'rejected';
        }

        // On peut modifier ses propres dépenses si elles sont en brouillon ou rejetées
        if ($expense->recorded_by === $user->id || $expense->requester_id === $user->id) {
            return $expense->approval_status === 'draft' || 
                   $expense->approval_status === 'rejected';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function delete(User $user, VehicleExpense $expense): bool
    {
        // Vérifier la permission de base
        if (!$user->can('expenses.delete')) {
            return false;
        }

        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Les dépenses approuvées ne peuvent être supprimées qu'avec permission spéciale
        if ($expense->approval_status === 'approved') {
            return $user->can('expenses.delete.approved');
        }

        // Les dépenses payées ne peuvent jamais être supprimées
        if ($expense->payment_status === 'paid') {
            return false;
        }

        // Super Admin et Admin peuvent supprimer
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Finance'])) {
            return true;
        }

        // On peut supprimer ses propres dépenses si elles sont en brouillon
        if ($expense->recorded_by === $user->id || $expense->requester_id === $user->id) {
            return $expense->approval_status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can restore the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function restore(User $user, VehicleExpense $expense): bool
    {
        // Vérifier la permission
        if (!$user->can('expenses.restore')) {
            return false;
        }

        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can permanently delete the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function forceDelete(User $user, VehicleExpense $expense): bool
    {
        // Seuls les Super Admin peuvent supprimer définitivement
        if (!$user->hasRole('Super Admin')) {
            return false;
        }

        // Vérifier la permission
        if (!$user->can('expenses.force-delete')) {
            return false;
        }

        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can approve the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function approve(User $user, VehicleExpense $expense): bool
    {
        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Ne peut pas approuver ses propres dépenses
        if ($expense->recorded_by === $user->id || $expense->requester_id === $user->id) {
            // Sauf si permission spéciale
            if (!$user->can('expenses.approval.bypass')) {
                return false;
            }
        }

        // Vérifier le niveau d'approbation requis
        if ($expense->approval_status === 'pending_level1') {
            return $user->can('expenses.approve.level1') || 
                   $user->can('expenses.approve');
        }

        if ($expense->approval_status === 'pending_level2') {
            return $user->can('expenses.approve.level2') || 
                   $user->can('expenses.approve');
        }

        // Permission générale d'approbation
        return $user->can('expenses.approve');
    }

    /**
     * Determine whether the user can reject the expense.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function reject(User $user, VehicleExpense $expense): bool
    {
        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Vérifier la permission
        if (!$user->can('expenses.reject')) {
            return false;
        }

        // Ne peut pas rejeter ses propres dépenses
        if ($expense->recorded_by === $user->id || $expense->requester_id === $user->id) {
            return false;
        }

        // La dépense doit être en attente d'approbation
        return in_array($expense->approval_status, ['pending_level1', 'pending_level2']);
    }

    /**
     * Determine whether the user can mark the expense as paid.
     * 
     * @param  \App\Models\User  $user
     * @param  \App\Models\VehicleExpense  $expense
     * @return bool
     */
    public function markAsPaid(User $user, VehicleExpense $expense): bool
    {
        // Isolation multi-tenant
        if ($expense->organization_id !== $user->organization_id) {
            return false;
        }

        // Vérifier la permission
        if (!$user->can('expenses.mark-paid')) {
            return false;
        }

        // La dépense doit être approuvée
        if ($expense->approval_status !== 'approved') {
            return false;
        }

        // Ne peut pas être déjà payée
        return $expense->payment_status !== 'paid';
    }

    /**
     * Determine whether the user can expenses.export.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function export(User $user): bool
    {
        return $user->can('expenses.export');
    }

    /**
     * Determine whether the user can expenses.import.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function import(User $user): bool
    {
        return $user->can('expenses.import');
    }

    /**
     * Determine whether the user can analytics.view.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->can('expenses.analytics.view') || 
               $user->can('expenses.dashboard.view');
    }

    /**
     * Determine whether the user can expenses.groups.manage.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageGroups(User $user): bool
    {
        return $user->can('expenses.groups.manage');
    }

    /**
     * Determine whether the user can expenses.budgets.manage.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function manageBudgets(User $user): bool
    {
        return $user->can('expenses.budgets.manage');
    }

    /**
     * Determine whether the user can audit-logs.view.
     * 
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAuditLogs(User $user): bool
    {
        return $user->can('expenses.audit.view');
    }
}
