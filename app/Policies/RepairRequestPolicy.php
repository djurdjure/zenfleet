<?php

namespace App\Policies;

use App\Models\RepairRequest;
use App\Models\User;

/**
 * RepairRequestPolicy - Authorization policy for repair workflow
 *
 * Workflow:
 * 1. Driver creates request → pending_supervisor
 * 2. Supervisor approves/rejects → pending_fleet_manager OR rejected_supervisor
 * 3. Fleet Manager approves/rejects → approved_final OR rejected_final
 *
 * Roles:
 * - Super Admin: Full access
 * - Admin: Full access for their organization
 * - Fleet Manager: Approve level 2, view all in org
 * - Supervisor: Approve level 1, view team
 * - Driver: Create own, view own
 */
class RepairRequestPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super Admin has all permissions
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any repair requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('repair-requests.view.all')
            || $user->can('repair-requests.view.team')
            || $user->can('repair-requests.view.own');
    }

    /**
     * Determine whether the user can view the repair request.
     */
    public function view(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation (multi-tenant)
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Admin and Fleet Manager can view all in their org
        if ($user->can('repair-requests.view.all')) {
            return true;
        }

        // Supervisor can view team requests (their supervised drivers)
        if ($user->can('repair-requests.view.team')) {
            return $this->isTeamRequest($user, $repairRequest);
        }

        // Driver can view their own requests
        if ($user->can('repair-requests.view.own')) {
            return $repairRequest->driver->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can repair-requests.create.
     */
    public function create(User $user): bool
    {
        return $user->can('repair-requests.create');
    }

    /**
     * Determine whether the user can update the repair request.
     */
    public function update(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Only pending requests can be updated
        if (!$repairRequest->isPending()) {
            return false;
        }

        // Admin can update any
        if ($this->isAdminRole($user)) {
            return true;
        }

        // Driver can update own pending requests
        if ($repairRequest->driver->user_id === $user->id) {
            return $repairRequest->status === RepairRequest::STATUS_PENDING_SUPERVISOR;
        }

        return false;
    }

    /**
     * Determine whether the user can approve at level 1 (Supervisor).
     */
    public function approveLevelOne(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Must be pending supervisor approval
        if ($repairRequest->status !== RepairRequest::STATUS_PENDING_SUPERVISOR) {
            return false;
        }

        // Must have permission
        if (!$user->can('repair-requests.approve.level1')) {
            return false;
        }

        // Supervisor can approve their team's requests
        if ($this->isSupervisorRole($user)) {
            return $this->isTeamRequest($user, $repairRequest);
        }

        // Admin and Fleet Manager can also approve level 1
        return $this->isAdminRole($user) || $this->isFleetManagerRole($user);
    }

    /**
     * Determine whether the user can reject at level 1 (Supervisor).
     */
    public function rejectLevelOne(User $user, RepairRequest $repairRequest): bool
    {
        // Same logic as approveLevelOne
        return $this->approveLevelOne($user, $repairRequest);
    }

    /**
     * Determine whether the user can approve at level 2 (Fleet Manager).
     */
    public function approveLevelTwo(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Must be pending fleet manager approval
        if ($repairRequest->status !== RepairRequest::STATUS_PENDING_FLEET_MANAGER) {
            return false;
        }

        // Must have permission
        if (!$user->can('repair-requests.approve.level2')) {
            return false;
        }

        // Only Fleet Manager and Admin
        return $this->isAdminRole($user) || $this->isFleetManagerRole($user);
    }

    /**
     * Determine whether the user can reject at level 2 (Fleet Manager).
     */
    public function rejectLevelTwo(User $user, RepairRequest $repairRequest): bool
    {
        // Same logic as approveLevelTwo
        return $this->approveLevelTwo($user, $repairRequest);
    }

    /**
     * Determine whether the user can delete the repair request.
     */
    public function delete(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Must have delete permission
        if (!$user->can('repair-requests.delete')) {
            return false;
        }

        // Admin can delete any
        if ($this->isAdminRole($user)) {
            return true;
        }

        // Driver can delete own pending requests
        if ($repairRequest->driver->user_id === $user->id) {
            return $repairRequest->status === RepairRequest::STATUS_PENDING_SUPERVISOR;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the repair request.
     */
    public function forceDelete(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Only Admin can force delete
        return $this->isAdminRole($user) && $user->can('repair-requests.force-delete');
    }

    /**
     * Determine whether the user can restore the repair request.
     */
    public function restore(User $user, RepairRequest $repairRequest): bool
    {
        // Check organization isolation
        if ($user->organization_id !== $repairRequest->organization_id) {
            return false;
        }

        // Only Admin can restore
        return $this->isAdminRole($user) && $user->can('repair-requests.restore');
    }

    /**
     * Helper: Check if request is from user's supervised team.
     */
    protected function isTeamRequest(User $user, RepairRequest $repairRequest): bool
    {
        // Check if the driver's supervisor is this user
        return $repairRequest->driver->supervisor_id === $user->id;
    }

    /**
     * Determine whether user can view history.
     */
    public function viewHistory(User $user, RepairRequest $repairRequest): bool
    {
        // Same as view permission
        return $this->view($user, $repairRequest);
    }

    /**
     * Determine whether user can view notifications.
     */
    public function viewNotifications(User $user, RepairRequest $repairRequest): bool
    {
        // Same as view permission
        return $this->view($user, $repairRequest);
    }

    /**
     * Determine whether user can repair-requests.export.
     */
    public function export(User $user): bool
    {
        return $user->can('repair-requests.export')
            || $this->isAdminRole($user)
            || $this->isFleetManagerRole($user);
    }

    /**
     * Role alias helper: Supervisor in FR/EN naming.
     */
    protected function isSupervisorRole(User $user): bool
    {
        return $user->hasAnyRole(['Supervisor', 'Superviseur']);
    }

    /**
     * Role alias helper: Fleet Manager in FR/EN naming.
     */
    protected function isFleetManagerRole(User $user): bool
    {
        return $user->hasAnyRole(['Fleet Manager', 'Gestionnaire Flotte', 'Chef de parc']);
    }

    /**
     * Role alias helper: Admin role.
     */
    protected function isAdminRole(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
