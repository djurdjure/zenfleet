<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Notifications\RepairRequestApproved;
use App\Notifications\RepairRequestRejected;
use App\Notifications\RepairRequestValidated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RepairRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    // Constantes pour les énumérations
    public const PRIORITY_URGENT = 'urgente';
    public const PRIORITY_SCHEDULED = 'a_prevoir';
    public const PRIORITY_NON_URGENT = 'non_urgente';

    public const STATUS_PENDING = 'en_attente';
    public const STATUS_INITIAL_APPROVAL = 'accord_initial';
    public const STATUS_APPROVED = 'accordee';
    public const STATUS_REJECTED = 'refusee';
    public const STATUS_IN_PROGRESS = 'en_cours';
    public const STATUS_COMPLETED = 'terminee';
    public const STATUS_CANCELLED = 'annulee';

    public const SUPERVISOR_ACCEPT = 'accepte';
    public const SUPERVISOR_REJECT = 'refuse';

    public const MANAGER_VALIDATE = 'valide';
    public const MANAGER_REJECT = 'refuse';

    protected $fillable = [
        'organization_id',
        'vehicle_id',
        'requested_by',
        'priority',
        'description',
        'location_description',
        'photos',
        'estimated_cost',
        'actual_cost',
        'assigned_supplier_id',
        'attachments',
        'work_photos',
        'completion_notes',
        'final_rating'
    ];

    protected $casts = [
        'photos' => 'array',
        'attachments' => 'array',
        'work_photos' => 'array',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'final_rating' => 'decimal:2',
        'requested_at' => 'datetime',
        'supervisor_decided_at' => 'datetime',
        'manager_decided_at' => 'datetime',
        'work_started_at' => 'datetime',
        'work_completed_at' => 'datetime'
    ];

    protected $dates = [
        'requested_at',
        'supervisor_decided_at',
        'manager_decided_at',
        'work_started_at',
        'work_completed_at'
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function assignedSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'assigned_supplier_id');
    }

    // Scopes pour filtrer les demandes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_URGENT);
    }

    public function scopeAwaitingSupervisorApproval($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->whereNull('supervisor_decision');
    }

    public function scopeAwaitingManagerValidation($query)
    {
        return $query->where('status', self::STATUS_INITIAL_APPROVAL)
                    ->whereNull('manager_decision');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Méthodes de workflow - Niveau 1 : Superviseur
    public function approveBySupervisor(User $supervisor, ?string $comments = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException('Cette demande ne peut plus être approuvée par un superviseur');
        }

        DB::beginTransaction();
        try {
            $this->update([
                'supervisor_decision' => self::SUPERVISOR_ACCEPT,
                'supervisor_id' => $supervisor->id,
                'supervisor_comments' => $comments,
                'supervisor_decided_at' => now(),
                'status' => self::STATUS_INITIAL_APPROVAL
            ]);

            // Notifier le manager pour validation
            $this->notifyManagerForValidation();

            // Notifier le demandeur
            $this->requester->notify(new RepairRequestApproved($this));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function rejectBySupervisor(User $supervisor, string $comments): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            throw new \InvalidArgumentException('Cette demande ne peut plus être rejetée par un superviseur');
        }

        DB::beginTransaction();
        try {
            $this->update([
                'supervisor_decision' => self::SUPERVISOR_REJECT,
                'supervisor_id' => $supervisor->id,
                'supervisor_comments' => $comments,
                'supervisor_decided_at' => now(),
                'status' => self::STATUS_REJECTED
            ]);

            // Notifier le demandeur
            $this->requester->notify(new RepairRequestRejected($this));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // Méthodes de workflow - Niveau 2 : Manager
    public function validateByManager(User $manager, ?string $comments = null): bool
    {
        if ($this->status !== self::STATUS_INITIAL_APPROVAL) {
            throw new \InvalidArgumentException('Cette demande ne peut pas être validée par un manager');
        }

        DB::beginTransaction();
        try {
            $this->update([
                'manager_decision' => self::MANAGER_VALIDATE,
                'manager_id' => $manager->id,
                'manager_comments' => $comments,
                'manager_decided_at' => now(),
                'status' => self::STATUS_APPROVED
            ]);

            // Déclencher le processus de réparation
            $this->startRepairProcess();

            // Notifier le demandeur
            $this->requester->notify(new RepairRequestValidated($this));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function rejectByManager(User $manager, string $comments): bool
    {
        if ($this->status !== self::STATUS_INITIAL_APPROVAL) {
            throw new \InvalidArgumentException('Cette demande ne peut pas être rejetée par un manager');
        }

        DB::beginTransaction();
        try {
            $this->update([
                'manager_decision' => self::MANAGER_REJECT,
                'manager_id' => $manager->id,
                'manager_comments' => $comments,
                'manager_decided_at' => now(),
                'status' => self::STATUS_REJECTED
            ]);

            // Notifier le demandeur
            $this->requester->notify(new RepairRequestRejected($this));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // Méthodes de gestion des travaux
    public function assignToSupplier($supplierId): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            throw new \InvalidArgumentException('Cette demande doit être approuvée avant assignation');
        }

        return $this->update([
            'assigned_supplier_id' => $supplierId
        ]);
    }

    public function startWork(): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            throw new \InvalidArgumentException('Les travaux ne peuvent commencer que sur une demande approuvée');
        }

        return $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'work_started_at' => now()
        ]);
    }

    public function completeWork(float $actualCost, ?string $completionNotes = null, ?array $workPhotos = null, ?float $rating = null): bool
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            throw new \InvalidArgumentException('Les travaux doivent être en cours pour être complétés');
        }

        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'work_completed_at' => now(),
            'actual_cost' => $actualCost,
            'completion_notes' => $completionNotes,
            'work_photos' => $workPhotos,
            'final_rating' => $rating
        ]);
    }

    public function cancel(?string $reason = null): bool
    {
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            throw new \InvalidArgumentException('Cette demande ne peut plus être annulée');
        }

        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'supervisor_comments' => $reason ? "Annulé: {$reason}" : 'Demande annulée'
        ]);
    }

    // Méthodes utilitaires
    public function canBeApprovedBy(User $user): bool
    {
        return $this->status === self::STATUS_PENDING &&
               $user->hasRole(['supervisor', 'fleet_manager']);
    }

    public function canBeValidatedBy(User $user): bool
    {
        return $this->status === self::STATUS_INITIAL_APPROVAL &&
               $user->hasRole('fleet_manager');
    }

    public function isUrgent(): bool
    {
        return $this->priority === self::PRIORITY_URGENT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getDurationInHours(): ?int
    {
        if (!$this->work_started_at || !$this->work_completed_at) {
            return null;
        }

        return $this->work_started_at->diffInHours($this->work_completed_at);
    }

    public function getCostVariation(): ?float
    {
        if (!$this->estimated_cost || !$this->actual_cost) {
            return null;
        }

        return (($this->actual_cost - $this->estimated_cost) / $this->estimated_cost) * 100;
    }

    // Méthodes privées
    private function notifyManagerForValidation(): void
    {
        // Récupérer tous les managers de l'organisation
        $managers = User::where('organization_id', $this->organization_id)
                       ->whereHas('roles', function ($query) {
                           $query->where('name', 'fleet_manager');
                       })
                       ->get();

        foreach ($managers as $manager) {
            // TODO: Implémenter la notification manager
        }
    }

    private function startRepairProcess(): void
    {
        // Logique pour démarrer automatiquement le processus de réparation
        // selon la priorité et les fournisseurs disponibles
        if ($this->isUrgent() && $this->assigned_supplier_id) {
            $this->startWork();
        }
    }

    // Accesseurs
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_URGENT => 'Urgente',
            self::PRIORITY_SCHEDULED => 'À prévoir',
            self::PRIORITY_NON_URGENT => 'Non urgente',
            default => 'Non définie'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_INITIAL_APPROVAL => 'Accord initial',
            self::STATUS_APPROVED => 'Accordée',
            self::STATUS_REJECTED => 'Refusée',
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_COMPLETED => 'Terminée',
            self::STATUS_CANCELLED => 'Annulée',
            default => 'Statut inconnu'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_INITIAL_APPROVAL => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_IN_PROGRESS => 'purple',
            self::STATUS_COMPLETED => 'emerald',
            self::STATUS_CANCELLED => 'gray',
            default => 'gray'
        };
    }
}