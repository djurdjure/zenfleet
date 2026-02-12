<?php

namespace App\Services;

use App\Models\RepairRequest;
use App\Models\RepairRequestHistory;
use App\Models\RepairNotification;
use App\Models\User;
use App\Models\Driver;
use App\Models\MaintenanceOperation;
use App\Models\Vehicle;
use App\Models\Scopes\UserVehicleAccessScope;
use App\Events\RepairRequestStatusChanged;
use App\Events\RepairRequestApproved;
use App\Events\RepairRequestRejected;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * RepairRequestService - Business logic for repair workflow
 *
 * Handles complete repair request lifecycle:
 * - Creation with photo uploads
 * - Two-level approval workflow (Supervisor → Fleet Manager)
 * - History tracking
 * - Automatic notifications
 * - Maintenance operation creation
 *
 * @version 1.0-Enterprise
 */
class RepairRequestService
{
    /**
     * Create a new repair request.
     *
     * @param array $data Request data including:
     *   - driver_id: int
     *   - vehicle_id: int
     *   - title: string
     *   - description: string
     *   - urgency: string (low|normal|high|critical)
     *   - current_mileage: int|null
     *   - current_location: string|null
     *   - estimated_cost: float|null
     *   - photos: array|null (UploadedFile[])
     *   - attachments: array|null (UploadedFile[])
     * @return RepairRequest
     * @throws \Exception
     */
    public function createRequest(array $data): RepairRequest
    {
        return DB::transaction(function () use ($data) {
            // Get driver to extract organization_id
            $driver = Driver::with('supervisor')->findOrFail($data['driver_id']);
            // Bypass user-vehicle visibility scope here:
            // the service enforces tenant consistency explicitly below.
            $vehicle = Vehicle::query()
                ->withoutGlobalScope(UserVehicleAccessScope::class)
                ->findOrFail($data['vehicle_id']);

            if ((int) $vehicle->organization_id !== (int) $driver->organization_id) {
                throw new \InvalidArgumentException('Le chauffeur et le véhicule doivent appartenir à la même organisation.');
            }

            $title = trim((string) ($data['title'] ?? ''));
            if ($title === '') {
                $title = $this->buildDefaultTitle(
                    (string) ($data['description'] ?? ''),
                    $vehicle->registration_plate ?? null
                );
            }

            // Upload photos if provided
            $photoPaths = [];
            if (!empty($data['photos'])) {
                $photoPaths = $this->uploadPhotos($data['photos'], 'repair-requests');
            }

            // Upload attachments if provided
            $attachmentPaths = [];
            if (!empty($data['attachments'])) {
                $attachmentPaths = $this->uploadPhotos($data['attachments'], 'repair-requests/attachments');
            }

            // Create repair request
            $createPayload = [
                'organization_id' => $driver->organization_id,
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'title' => $title,
                'description' => $data['description'],
                'urgency' => $data['urgency'] ?? RepairRequest::URGENCY_NORMAL,
                'current_mileage' => $data['current_mileage'] ?? null,
                'current_location' => $data['current_location'] ?? null,
                'estimated_cost' => $data['estimated_cost'] ?? null,
                'status' => RepairRequest::STATUS_PENDING_SUPERVISOR,
                'photos' => !empty($photoPaths) ? $photoPaths : null,
                'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
                'supervisor_id' => $driver->supervisor_id,
            ];

            if (Schema::hasColumn('repair_requests', 'requested_by')) {
                $requesterId = $data['requested_by'] ?? auth()->id() ?? $driver->user_id;
                if (!$requesterId) {
                    throw new \RuntimeException('Impossible de déterminer l’utilisateur demandeur.');
                }
                $createPayload['requested_by'] = $requesterId;
            }

            if (Schema::hasColumn('repair_requests', 'category_id')) {
                $createPayload['category_id'] = $data['category_id'] ?? null;
            }

            if (Schema::hasColumn('repair_requests', 'location_description')) {
                $createPayload['location_description'] = $data['location_description']
                    ?? $data['current_location']
                    ?? null;
            }

            if (Schema::hasColumn('repair_requests', 'requested_at')) {
                $createPayload['requested_at'] = $data['requested_at'] ?? now();
            }

            if (Schema::hasColumn('repair_requests', 'priority') && !isset($createPayload['priority'])) {
                $createPayload['priority'] = $createPayload['urgency'];
            }

            $repairRequest = RepairRequest::create($createPayload);

            // Log creation in history
            $this->logHistory(
                $repairRequest,
                'created',
                null,
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                $driver->user,
                'Demande de réparation créée'
            );

            // Notify supervisor if assigned
            if ($driver->supervisor_id) {
                $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
                $this->notifySupervisor(
                    $repairRequest,
                    $driver->supervisor,
                    'Nouvelle demande de réparation',
                    "Une nouvelle demande de réparation a été créée pour le véhicule {$vehicleLabel}"
                );
            }

            return $repairRequest->fresh(['driver', 'vehicle', 'supervisor', 'history']);
        });
    }

    private function buildDefaultTitle(string $description, ?string $registrationPlate): string
    {
        $prefix = 'Demande de réparation';
        $suffix = $registrationPlate ? ' - ' . $registrationPlate : '';

        $candidate = trim(Str::of($description)->squish()->limit(120, '')->value());
        if ($candidate === '') {
            return $prefix . $suffix;
        }

        return Str::limit($candidate, 255 - strlen($suffix), '') . $suffix;
    }

    /**
     * Approve repair request by supervisor (level 1).
     *
     * @param RepairRequest $repairRequest
     * @param User $supervisor
     * @param string|null $comment
     * @return RepairRequest
     * @throws \Exception
     */
    public function approveBySupervisor(RepairRequest $repairRequest, User $supervisor, ?string $comment = null): RepairRequest
    {
        return DB::transaction(function () use ($repairRequest, $supervisor, $comment) {
            // Verify current status
            if ($repairRequest->status !== RepairRequest::STATUS_PENDING_SUPERVISOR) {
                throw new \Exception('Cette demande n\'est pas en attente d\'approbation superviseur');
            }

            // Update request
            $repairRequest->update([
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'approved',
                'supervisor_comment' => $comment,
                'supervisor_approved_at' => now(),
                'status' => RepairRequest::STATUS_PENDING_FLEET_MANAGER,
            ]);

            // Log history
            $this->logHistory(
                $repairRequest,
                'supervisor_approved',
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                $supervisor,
                $comment ?? 'Approuvé par le superviseur'
            );

            // Notify fleet managers
            $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
            $this->notifyFleetManagers(
                $repairRequest,
                'Demande de réparation approuvée par superviseur',
                "La demande de réparation #{$repairRequest->id} pour {$vehicleLabel} a été approuvée par le superviseur et nécessite votre validation"
            );

            // Notify driver of progress
            $this->notifyDriver(
                $repairRequest,
                'Votre demande a été approuvée par le superviseur',
                "Votre demande de réparation pour {$vehicleLabel} a été approuvée par votre superviseur et est maintenant en attente de validation du gestionnaire de flotte"
            );

            // 🔔 DISPATCH EVENTS
            RepairRequestStatusChanged::dispatch(
                $repairRequest,
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                $supervisor
            );

            RepairRequestApproved::dispatch(
                $repairRequest,
                $supervisor,
                'supervisor'
            );

            return $repairRequest->fresh(['driver', 'vehicle', 'supervisor', 'fleetManager', 'history']);
        });
    }

    /**
     * Reject repair request by supervisor (level 1).
     *
     * @param RepairRequest $repairRequest
     * @param User $supervisor
     * @param string $reason
     * @return RepairRequest
     * @throws \Exception
     */
    public function rejectBySupervisor(RepairRequest $repairRequest, User $supervisor, string $reason): RepairRequest
    {
        return DB::transaction(function () use ($repairRequest, $supervisor, $reason) {
            // Verify current status
            if ($repairRequest->status !== RepairRequest::STATUS_PENDING_SUPERVISOR) {
                throw new \Exception('Cette demande n\'est pas en attente d\'approbation superviseur');
            }

            // Update request
            $repairRequest->update([
                'supervisor_id' => $supervisor->id,
                'supervisor_status' => 'rejected',
                'supervisor_comment' => $reason,
                'status' => RepairRequest::STATUS_REJECTED_SUPERVISOR,
                'rejection_reason' => $reason,
                'rejected_by' => $supervisor->id,
                'rejected_at' => now(),
            ]);

            // Log history
            $this->logHistory(
                $repairRequest,
                'supervisor_rejected',
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                RepairRequest::STATUS_REJECTED_SUPERVISOR,
                $supervisor,
                $reason
            );

            // Notify driver of rejection
            $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
            $this->notifyDriver(
                $repairRequest,
                'Votre demande a été rejetée',
                "Votre demande de réparation pour {$vehicleLabel} a été rejetée par votre superviseur. Raison: {$reason}"
            );

            // 🔔 DISPATCH EVENTS
            RepairRequestStatusChanged::dispatch(
                $repairRequest,
                RepairRequest::STATUS_PENDING_SUPERVISOR,
                RepairRequest::STATUS_REJECTED_SUPERVISOR,
                $supervisor
            );

            RepairRequestRejected::dispatch(
                $repairRequest,
                $supervisor,
                'supervisor',
                $reason
            );

            return $repairRequest->fresh(['driver', 'vehicle', 'supervisor', 'rejectedBy', 'history']);
        });
    }

    /**
     * Approve repair request by fleet manager (level 2 - final).
     *
     * @param RepairRequest $repairRequest
     * @param User $fleetManager
     * @param string|null $comment
     * @return RepairRequest
     * @throws \Exception
     */
    public function approveByFleetManager(RepairRequest $repairRequest, User $fleetManager, ?string $comment = null): RepairRequest
    {
        return DB::transaction(function () use ($repairRequest, $fleetManager, $comment) {
            // Verify current status
            if ($repairRequest->status !== RepairRequest::STATUS_PENDING_FLEET_MANAGER) {
                throw new \Exception('Cette demande n\'est pas en attente d\'approbation gestionnaire');
            }

            // Update request
            $repairRequest->update([
                'fleet_manager_id' => $fleetManager->id,
                'fleet_manager_status' => 'approved',
                'fleet_manager_comment' => $comment,
                'fleet_manager_approved_at' => now(),
                'status' => RepairRequest::STATUS_APPROVED_FINAL,
                'final_approved_by' => $fleetManager->id,
                'final_approved_at' => now(),
            ]);

            // Log history
            $this->logHistory(
                $repairRequest,
                'fleet_manager_approved',
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                RepairRequest::STATUS_APPROVED_FINAL,
                $fleetManager,
                $comment ?? 'Approuvé par le gestionnaire de flotte'
            );

            // Create maintenance operation if table exists
            try {
                $maintenanceOperation = $this->createMaintenanceOperation($repairRequest, $fleetManager);

                if ($maintenanceOperation) {
                    $repairRequest->update([
                        'maintenance_operation_id' => $maintenanceOperation->id,
                    ]);
                }
            } catch (\Exception $e) {
                // Log but don't fail if maintenance operation creation fails
                \Log::warning('Failed to create maintenance operation for repair request', [
                    'repair_request_id' => $repairRequest->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Notify supervisor
            if ($repairRequest->supervisor_id) {
                $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
                $this->notifySupervisor(
                    $repairRequest,
                    $repairRequest->supervisor,
                    'Demande de réparation approuvée',
                    "La demande de réparation #{$repairRequest->id} pour {$vehicleLabel} a été approuvée par le gestionnaire de flotte"
                );
            }

            // Notify driver of final approval
            $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
            $this->notifyDriver(
                $repairRequest,
                'Votre demande a été approuvée',
                "Votre demande de réparation pour {$vehicleLabel} a été approuvée. La réparation peut maintenant être planifiée."
            );

            // 🔔 DISPATCH EVENTS
            RepairRequestStatusChanged::dispatch(
                $repairRequest,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                RepairRequest::STATUS_APPROVED_FINAL,
                $fleetManager
            );

            RepairRequestApproved::dispatch(
                $repairRequest,
                $fleetManager,
                'fleet_manager'
            );

            return $repairRequest->fresh(['driver', 'vehicle', 'supervisor', 'fleetManager', 'maintenanceOperation', 'history']);
        });
    }

    /**
     * Reject repair request by fleet manager (level 2 - final).
     *
     * @param RepairRequest $repairRequest
     * @param User $fleetManager
     * @param string $reason
     * @return RepairRequest
     * @throws \Exception
     */
    public function rejectByFleetManager(RepairRequest $repairRequest, User $fleetManager, string $reason): RepairRequest
    {
        return DB::transaction(function () use ($repairRequest, $fleetManager, $reason) {
            // Verify current status
            if ($repairRequest->status !== RepairRequest::STATUS_PENDING_FLEET_MANAGER) {
                throw new \Exception('Cette demande n\'est pas en attente d\'approbation gestionnaire');
            }

            // Update request
            $repairRequest->update([
                'fleet_manager_id' => $fleetManager->id,
                'fleet_manager_status' => 'rejected',
                'fleet_manager_comment' => $reason,
                'status' => RepairRequest::STATUS_REJECTED_FINAL,
                'rejection_reason' => $reason,
                'rejected_by' => $fleetManager->id,
                'rejected_at' => now(),
            ]);

            // Log history
            $this->logHistory(
                $repairRequest,
                'fleet_manager_rejected',
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                RepairRequest::STATUS_REJECTED_FINAL,
                $fleetManager,
                $reason
            );

            // Notify supervisor
            if ($repairRequest->supervisor_id) {
                $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
                $this->notifySupervisor(
                    $repairRequest,
                    $repairRequest->supervisor,
                    'Demande de réparation rejetée',
                    "La demande de réparation #{$repairRequest->id} pour {$vehicleLabel} a été rejetée par le gestionnaire de flotte. Raison: {$reason}"
                );
            }

            // Notify driver of final rejection
            $vehicleLabel = $this->resolveVehicleLabel($repairRequest);
            $this->notifyDriver(
                $repairRequest,
                'Votre demande a été rejetée',
                "Votre demande de réparation pour {$vehicleLabel} a été rejetée par le gestionnaire de flotte. Raison: {$reason}"
            );

            // 🔔 DISPATCH EVENTS
            RepairRequestStatusChanged::dispatch(
                $repairRequest,
                RepairRequest::STATUS_PENDING_FLEET_MANAGER,
                RepairRequest::STATUS_REJECTED_FINAL,
                $fleetManager
            );

            RepairRequestRejected::dispatch(
                $repairRequest,
                $fleetManager,
                'fleet_manager',
                $reason
            );

            return $repairRequest->fresh(['driver', 'vehicle', 'supervisor', 'fleetManager', 'rejectedBy', 'history']);
        });
    }

    /**
     * Upload photos/attachments to storage.
     *
     * @param array $files Array of UploadedFile instances
     * @param string $path Storage path
     * @return array Array of file paths
     */
    protected function uploadPhotos(array $files, string $path = 'repair-requests'): array
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs($path, $filename, 'public');

                $uploadedPaths[] = [
                    'path' => $filePath,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
        }

        return $uploadedPaths;
    }

    /**
     * Log action to repair request history.
     *
     * @param RepairRequest $repairRequest
     * @param string $action
     * @param string|null $fromStatus
     * @param string $toStatus
     * @param User|null $user
     * @param string|null $comment
     * @return RepairRequestHistory
     */
    protected function logHistory(
        RepairRequest $repairRequest,
        string $action,
        ?string $fromStatus,
        string $toStatus,
        ?User $user = null,
        ?string $comment = null
    ): RepairRequestHistory {
        return RepairRequestHistory::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $user?->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'comment' => $comment,
            'metadata' => [
                'urgency' => $repairRequest->urgency,
                'vehicle_id' => $repairRequest->vehicle_id,
                'driver_id' => $repairRequest->driver_id,
            ],
        ]);
    }

    /**
     * Notify supervisor about repair request.
     *
     * @param RepairRequest $repairRequest
     * @param User $supervisor
     * @param string $title
     * @param string $message
     * @return RepairNotification
     */
    protected function notifySupervisor(
        RepairRequest $repairRequest,
        User $supervisor,
        string $title,
        string $message
    ): RepairNotification {
        return RepairNotification::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $supervisor->id,
            'type' => RepairNotification::TYPE_NEW_REQUEST,
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Notify all fleet managers in organization.
     *
     * @param RepairRequest $repairRequest
     * @param string $title
     * @param string $message
     * @return void
     */
    protected function notifyFleetManagers(RepairRequest $repairRequest, string $title, string $message): void
    {
        // Get all fleet managers in the same organization
        $fleetManagers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Gestionnaire Flotte');
        })->where('organization_id', $repairRequest->organization_id)->get();

        foreach ($fleetManagers as $fleetManager) {
            RepairNotification::create([
                'repair_request_id' => $repairRequest->id,
                'user_id' => $fleetManager->id,
                'type' => RepairNotification::TYPE_STATUS_CHANGED,
                'title' => $title,
                'message' => $message,
            ]);
        }
    }

    /**
     * Notify driver about repair request status.
     *
     * @param RepairRequest $repairRequest
     * @param string $title
     * @param string $message
     * @return RepairNotification|null
     */
    protected function notifyDriver(RepairRequest $repairRequest, string $title, string $message): ?RepairNotification
    {
        $driverUserId = $repairRequest->driver?->user_id;

        if (!$driverUserId) {
            $driverUserId = Driver::query()
                ->withoutGlobalScopes()
                ->whereKey($repairRequest->driver_id)
                ->where('organization_id', $repairRequest->organization_id)
                ->value('user_id');
        }

        if (!$driverUserId) {
            return null;
        }

        $type = match (true) {
            str_contains(strtolower($title), 'approuvée') => RepairNotification::TYPE_APPROVED,
            str_contains(strtolower($title), 'rejetée') => RepairNotification::TYPE_REJECTED,
            default => RepairNotification::TYPE_STATUS_CHANGED,
        };

        return RepairNotification::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $driverUserId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ]);
    }

    /**
     * Resolve a safe vehicle label for logs and notifications.
     * Avoids null relations when vehicle access scopes hide the relation.
     */
    protected function resolveVehicleLabel(RepairRequest $repairRequest): string
    {
        $plate = $repairRequest->vehicle?->registration_plate;

        if (!empty($plate)) {
            return $plate;
        }

        $plate = Vehicle::query()
            ->withoutGlobalScopes()
            ->whereKey($repairRequest->vehicle_id)
            ->where('organization_id', $repairRequest->organization_id)
            ->value('registration_plate');

        if (!empty($plate)) {
            return $plate;
        }

        return 'ID #' . $repairRequest->vehicle_id;
    }

    /**
     * Create maintenance operation from approved repair request.
     *
     * @param RepairRequest $repairRequest
     * @param User $approver
     * @return MaintenanceOperation|null
     */
    protected function createMaintenanceOperation(RepairRequest $repairRequest, User $approver): ?MaintenanceOperation
    {
        // Check if MaintenanceOperation model exists
        if (!class_exists(MaintenanceOperation::class)) {
            return null;
        }

        try {
            return MaintenanceOperation::create([
                'organization_id' => $repairRequest->organization_id,
                'vehicle_id' => $repairRequest->vehicle_id,
                'type' => 'repair',
                'title' => $repairRequest->title,
                'description' => $repairRequest->description,
                'status' => 'planned',
                'scheduled_date' => now()->addDays(1),
                'estimated_cost' => $repairRequest->estimated_cost,
                'current_mileage' => $repairRequest->current_mileage,
                'created_by' => $approver->id,
                'notes' => "Créé automatiquement depuis la demande de réparation #{$repairRequest->id}",
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create maintenance operation', [
                'repair_request_id' => $repairRequest->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete photos from storage when repair request is deleted.
     *
     * @param RepairRequest $repairRequest
     * @return void
     */
    public function deletePhotos(RepairRequest $repairRequest): void
    {
        if ($repairRequest->photos) {
            foreach ($repairRequest->photos as $photo) {
                if (isset($photo['path']) && Storage::disk('public')->exists($photo['path'])) {
                    Storage::disk('public')->delete($photo['path']);
                }
            }
        }

        if ($repairRequest->attachments) {
            foreach ($repairRequest->attachments as $attachment) {
                if (isset($attachment['path']) && Storage::disk('public')->exists($attachment['path'])) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }
    }
}
