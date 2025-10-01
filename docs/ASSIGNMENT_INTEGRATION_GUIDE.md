# 🔧 Guide d'Intégration - Module Affectations

## Vue d'ensemble Technique

Ce guide détaille l'intégration du module Affectations dans l'écosystème ZenFleet existant.

## 🗄️ Schéma de Base de Données

### Table Principale : `assignments`

```sql
CREATE TABLE assignments (
    id BIGSERIAL PRIMARY KEY,
    organization_id BIGINT NOT NULL,
    vehicle_id BIGINT NOT NULL,
    driver_id BIGINT NOT NULL,
    start_datetime TIMESTAMP WITH TIME ZONE NOT NULL,
    end_datetime TIMESTAMP WITH TIME ZONE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    reason TEXT NULL,
    notes TEXT NULL,
    start_mileage INTEGER NULL,
    end_mileage INTEGER NULL,
    estimated_duration_hours DECIMAL(8,2) NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    -- Contraintes
    CONSTRAINT fk_assignments_organization
        FOREIGN KEY (organization_id) REFERENCES organizations(id),
    CONSTRAINT fk_assignments_vehicle
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    CONSTRAINT fk_assignments_driver
        FOREIGN KEY (driver_id) REFERENCES drivers(id),
    CONSTRAINT fk_assignments_created_by
        FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT fk_assignments_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id),

    -- Contrainte anti-chevauchement (PostgreSQL GIST)
    EXCLUDE USING GIST (
        vehicle_id WITH =,
        organization_id WITH =,
        tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp), '[)') WITH &&
    ) WHERE (deleted_at IS NULL),

    EXCLUDE USING GIST (
        driver_id WITH =,
        organization_id WITH =,
        tsrange(start_datetime, COALESCE(end_datetime, 'infinity'::timestamp), '[)') WITH &&
    ) WHERE (deleted_at IS NULL)
);

-- Index pour performances
CREATE INDEX idx_assignments_organization_id ON assignments(organization_id);
CREATE INDEX idx_assignments_status ON assignments(status);
CREATE INDEX idx_assignments_start_datetime ON assignments(start_datetime);
CREATE INDEX idx_assignments_vehicle_driver ON assignments(vehicle_id, driver_id);
CREATE INDEX idx_assignments_temporal ON assignments USING GIST (
    start_datetime,
    COALESCE(end_datetime, 'infinity'::timestamp)
);
```

### Triggers de Fallback (si GIST non disponible)

```sql
-- Fonction de validation anti-chevauchement
CREATE OR REPLACE FUNCTION check_assignment_overlap()
RETURNS TRIGGER AS $$
BEGIN
    -- Vérification chevauchement véhicule
    IF EXISTS (
        SELECT 1 FROM assignments
        WHERE vehicle_id = NEW.vehicle_id
        AND organization_id = NEW.organization_id
        AND id != COALESCE(NEW.id, 0)
        AND deleted_at IS NULL
        AND status NOT IN ('cancelled')
        AND (
            (start_datetime, COALESCE(end_datetime, 'infinity'::timestamp))
            OVERLAPS
            (NEW.start_datetime, COALESCE(NEW.end_datetime, 'infinity'::timestamp))
        )
    ) THEN
        RAISE EXCEPTION 'Conflit d''affectation: véhicule déjà assigné sur cette période';
    END IF;

    -- Vérification chevauchement chauffeur
    IF EXISTS (
        SELECT 1 FROM assignments
        WHERE driver_id = NEW.driver_id
        AND organization_id = NEW.organization_id
        AND id != COALESCE(NEW.id, 0)
        AND deleted_at IS NULL
        AND status NOT IN ('cancelled')
        AND (
            (start_datetime, COALESCE(end_datetime, 'infinity'::timestamp))
            OVERLAPS
            (NEW.start_datetime, COALESCE(NEW.end_datetime, 'infinity'::timestamp))
        )
    ) THEN
        RAISE EXCEPTION 'Conflit d''affectation: chauffeur déjà assigné sur cette période';
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger sur INSERT/UPDATE
CREATE TRIGGER trg_assignment_overlap_check
    BEFORE INSERT OR UPDATE ON assignments
    FOR EACH ROW
    EXECUTE FUNCTION check_assignment_overlap();
```

## 🔗 Relations et Modèles

### Modèle Assignment

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Assignment extends Model
{
    use SoftDeletes;

    // Statuts possibles
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_SCHEDULED => 'Programmée',
        self::STATUS_ACTIVE => 'En cours',
        self::STATUS_COMPLETED => 'Terminée',
        self::STATUS_CANCELLED => 'Annulée'
    ];

    protected $fillable = [
        'organization_id', 'vehicle_id', 'driver_id',
        'start_datetime', 'end_datetime', 'status',
        'reason', 'notes', 'start_mileage', 'end_mileage',
        'estimated_duration_hours'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'estimated_duration_hours' => 'decimal:2'
    ];

    // Relations
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accesseurs calculés
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? 'Inconnu';
    }

    public function getVehicleDisplayAttribute(): string
    {
        return $this->vehicle->registration_plate ??
               ($this->vehicle->brand . ' ' . $this->vehicle->model);
    }

    public function getDriverDisplayAttribute(): string
    {
        return $this->driver->first_name . ' ' . $this->driver->last_name;
    }

    public function getDurationHoursAttribute(): ?float
    {
        if (!$this->end_datetime || !$this->start_datetime) {
            return null;
        }
        return $this->start_datetime->diffInHours($this->end_datetime, true);
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = $this->duration_hours;
        if (!$hours) return 'En cours';

        $fullHours = floor($hours);
        $minutes = ($hours - $fullHours) * 60;

        return $fullHours . 'h' . ($minutes > 0 ? ' ' . round($minutes) . 'min' : '');
    }

    public function getIsOngoingAttribute(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->end_datetime === null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeInPeriod($query, Carbon $start, Carbon $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where(function ($subQ) use ($start, $end) {
                // Affectations avec fin définie qui intersectent
                $subQ->whereNotNull('end_datetime')
                     ->where('start_datetime', '<', $end)
                     ->where('end_datetime', '>', $start);
            })->orWhere(function ($subQ) use ($start) {
                // Affectations sans fin qui commencent avant la fin de période
                $subQ->whereNull('end_datetime')
                     ->where('start_datetime', '<=', $start->copy()->addDays(30));
            });
        });
    }

    // Business Logic
    public function calculateStatus(): string
    {
        $now = now();

        if ($this->start_datetime > $now) {
            return self::STATUS_SCHEDULED;
        }

        if ($this->end_datetime === null || $this->end_datetime > $now) {
            return self::STATUS_ACTIVE;
        }

        return self::STATUS_COMPLETED;
    }

    public function terminate(int $endMileage, Carbon $endDateTime): bool
    {
        $this->update([
            'end_datetime' => $endDateTime,
            'end_mileage' => $endMileage,
            'status' => self::STATUS_COMPLETED
        ]);

        return true;
    }

    // Audit automatique
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            $assignment->created_by = auth()->id();
            $assignment->updated_by = auth()->id();
        });

        static::updating(function ($assignment) {
            $assignment->updated_by = auth()->id();
        });
    }
}
```

### Extensions des Modèles Existants

#### Vehicle.php - Ajouter la relation
```php
public function assignments(): HasMany
{
    return $this->hasMany(Assignment::class);
}

public function currentAssignment(): HasOne
{
    return $this->hasOne(Assignment::class)
                ->where('status', Assignment::STATUS_ACTIVE)
                ->whereNull('end_datetime');
}

public function isAvailable(Carbon $start, Carbon $end = null): bool
{
    return !$this->assignments()
                 ->where('status', '!=', Assignment::STATUS_CANCELLED)
                 ->where(function($q) use ($start, $end) {
                     $endTime = $end ?? $start->copy()->addYear();
                     $q->where(function($subQ) use ($start, $endTime) {
                         $subQ->whereNotNull('end_datetime')
                              ->where('start_datetime', '<', $endTime)
                              ->where('end_datetime', '>', $start);
                     })->orWhere(function($subQ) use ($start) {
                         $subQ->whereNull('end_datetime')
                              ->where('start_datetime', '<=', $start);
                     });
                 })->exists();
}
```

#### Driver.php - Ajouter la relation
```php
public function assignments(): HasMany
{
    return $this->hasMany(Assignment::class);
}

public function currentAssignment(): HasOne
{
    return $this->hasOne(Assignment::class)
                ->where('status', Assignment::STATUS_ACTIVE)
                ->whereNull('end_datetime');
}

public function isAvailable(Carbon $start, Carbon $end = null): bool
{
    return !$this->assignments()
                 ->where('status', '!=', Assignment::STATUS_CANCELLED)
                 ->where(function($q) use ($start, $end) {
                     $endTime = $end ?? $start->copy()->addYear();
                     $q->where(function($subQ) use ($start, $endTime) {
                         $subQ->whereNotNull('end_datetime')
                              ->where('start_datetime', '<', $endTime)
                              ->where('end_datetime', '>', $start);
                     })->orWhere(function($subQ) use ($start) {
                         $subQ->whereNull('end_datetime')
                              ->where('start_datetime', '<=', $start);
                     });
                 })->exists();
}
```

## 🛡️ Sécurité et Permissions

### Policy Configuration

```php
<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view assignments');
    }

    public function view(User $user, Assignment $assignment): bool
    {
        return $user->hasPermissionTo('view assignments') &&
               $user->organization_id === $assignment->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create assignments');
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->hasPermissionTo('edit assignments') &&
               $user->organization_id === $assignment->organization_id &&
               $assignment->status !== Assignment::STATUS_COMPLETED;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasPermissionTo('delete assignments') &&
               $user->organization_id === $assignment->organization_id &&
               $assignment->status !== Assignment::STATUS_ACTIVE;
    }

    public function terminate(User $user, Assignment $assignment): bool
    {
        return $user->hasPermissionTo('end assignments') &&
               $user->organization_id === $assignment->organization_id &&
               $assignment->status === Assignment::STATUS_ACTIVE;
    }

    public function viewGantt(User $user): bool
    {
        return $user->hasPermissionTo('view assignments');
    }

    public function export(User $user): bool
    {
        return $user->hasPermissionTo('view assignments');
    }

    public function viewStats(User $user): bool
    {
        return $user->hasPermissionTo('view assignments');
    }
}
```

### Middleware Enterprise

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnterprisePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Vérification permission
        if (!$user->hasPermissionTo($permission)) {
            abort(403, 'Permission insuffisante: ' . $permission);
        }

        // Vérification organisation active
        if (!$user->organization_id) {
            abort(403, 'Aucune organisation associée');
        }

        // Log d'audit
        \Log::info('Permission validée', [
            'user_id' => $user->id,
            'permission' => $permission,
            'organization_id' => $user->organization_id,
            'ip' => $request->ip(),
            'route' => $request->route()->getName()
        ]);

        return $next($request);
    }
}
```

## 🔧 Configuration Livewire

### Service Provider (optionnel)

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AssignmentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Enregistrer les composants Livewire
        Livewire::component('assignments.assignment-table',
            \App\Livewire\Assignments\AssignmentTable::class);
        Livewire::component('assignments.assignment-form',
            \App\Livewire\Assignments\AssignmentForm::class);
        Livewire::component('assignments.assignment-gantt',
            \App\Livewire\Assignments\AssignmentGantt::class);

        // Middleware pour les composants
        Livewire::addPersistentMiddleware([
            \App\Http\Middleware\EnterprisePermissionMiddleware::class . ':view assignments'
        ]);
    }

    public function register()
    {
        // Binding du service
        $this->app->singleton(\App\Services\AssignmentOverlapService::class);
    }
}
```

## 📡 API Integration

### Events Système

```php
<?php

namespace App\Events\Assignments;

use App\Models\Assignment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Assignment $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
}

class AssignmentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Assignment $assignment;
    public array $changes;

    public function __construct(Assignment $assignment, array $changes)
    {
        $this->assignment = $assignment;
        $this->changes = $changes;
    }
}

class AssignmentTerminated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Assignment $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }
}

class AssignmentConflictDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $conflictData;

    public function __construct(array $conflictData)
    {
        $this->conflictData = $conflictData;
    }
}
```

### Listeners d'Exemple

```php
<?php

namespace App\Listeners\Assignments;

use App\Events\Assignments\AssignmentCreated;
use App\Models\Notification;

class SendAssignmentNotification
{
    public function handle(AssignmentCreated $event)
    {
        $assignment = $event->assignment;

        // Notifier le chauffeur
        Notification::create([
            'user_id' => $assignment->driver->user_id,
            'title' => 'Nouvelle affectation véhicule',
            'message' => "Véhicule {$assignment->vehicle_display} assigné du {$assignment->start_datetime->format('d/m/Y H:i')}",
            'type' => 'assignment',
            'data' => ['assignment_id' => $assignment->id]
        ]);

        // Notifier gestionnaire flotte
        $managers = User::role('Gestionnaire Flotte')
                       ->where('organization_id', $assignment->organization_id)
                       ->get();

        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'Affectation créée',
                'message' => "Nouvelle affectation: {$assignment->vehicle_display} → {$assignment->driver_display}",
                'type' => 'assignment_management',
                'data' => ['assignment_id' => $assignment->id]
            ]);
        }
    }
}
```

## 🔍 Monitoring et Logs

### Logs d'Audit

```php
<?php

namespace App\Observers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Log;

class AssignmentObserver
{
    public function created(Assignment $assignment)
    {
        Log::info('Assignment created', [
            'assignment_id' => $assignment->id,
            'organization_id' => $assignment->organization_id,
            'vehicle_id' => $assignment->vehicle_id,
            'driver_id' => $assignment->driver_id,
            'created_by' => $assignment->created_by,
            'start_datetime' => $assignment->start_datetime,
            'end_datetime' => $assignment->end_datetime
        ]);
    }

    public function updated(Assignment $assignment)
    {
        Log::info('Assignment updated', [
            'assignment_id' => $assignment->id,
            'organization_id' => $assignment->organization_id,
            'updated_by' => $assignment->updated_by,
            'changes' => $assignment->getChanges()
        ]);
    }

    public function deleted(Assignment $assignment)
    {
        Log::warning('Assignment deleted', [
            'assignment_id' => $assignment->id,
            'organization_id' => $assignment->organization_id,
            'deleted_by' => auth()->id()
        ]);
    }
}
```

### Métriques de Performance

```php
<?php

namespace App\Services;

use App\Models\Assignment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AssignmentMetricsService
{
    public function getDashboardMetrics(int $organizationId): array
    {
        return Cache::remember("assignment_metrics_{$organizationId}", 300, function() use ($organizationId) {
            return [
                'active_assignments' => Assignment::where('organization_id', $organizationId)
                                                 ->where('status', Assignment::STATUS_ACTIVE)
                                                 ->count(),

                'scheduled_assignments' => Assignment::where('organization_id', $organizationId)
                                                    ->where('status', Assignment::STATUS_SCHEDULED)
                                                    ->where('start_datetime', '>', now())
                                                    ->count(),

                'vehicles_utilization' => $this->calculateVehicleUtilization($organizationId),
                'drivers_utilization' => $this->calculateDriverUtilization($organizationId),

                'avg_assignment_duration' => Assignment::where('organization_id', $organizationId)
                                                      ->where('status', Assignment::STATUS_COMPLETED)
                                                      ->whereNotNull('end_datetime')
                                                      ->avg(DB::raw('EXTRACT(EPOCH FROM (end_datetime - start_datetime))/3600')),

                'conflicts_today' => $this->getConflictsCount($organizationId),
            ];
        });
    }

    private function calculateVehicleUtilization(int $organizationId): float
    {
        // Logique de calcul d'utilisation véhicules
        $totalVehicles = \App\Models\Vehicle::where('organization_id', $organizationId)->count();
        $activeAssignments = Assignment::where('organization_id', $organizationId)
                                      ->where('status', Assignment::STATUS_ACTIVE)
                                      ->distinct('vehicle_id')
                                      ->count();

        return $totalVehicles > 0 ? round(($activeAssignments / $totalVehicles) * 100, 2) : 0;
    }

    private function calculateDriverUtilization(int $organizationId): float
    {
        // Logique de calcul d'utilisation chauffeurs
        $totalDrivers = \App\Models\Driver::where('organization_id', $organizationId)->count();
        $activeAssignments = Assignment::where('organization_id', $organizationId)
                                      ->where('status', Assignment::STATUS_ACTIVE)
                                      ->distinct('driver_id')
                                      ->count();

        return $totalDrivers > 0 ? round(($activeAssignments / $totalDrivers) * 100, 2) : 0;
    }

    private function getConflictsCount(int $organizationId): int
    {
        // Détection conflits potentiels aujourd'hui
        return DB::select("
            SELECT COUNT(*) as conflicts
            FROM assignments a1
            JOIN assignments a2 ON (
                a1.organization_id = a2.organization_id
                AND a1.id != a2.id
                AND (a1.vehicle_id = a2.vehicle_id OR a1.driver_id = a2.driver_id)
                AND a1.status NOT IN ('cancelled', 'completed')
                AND a2.status NOT IN ('cancelled', 'completed')
                AND tsrange(a1.start_datetime, COALESCE(a1.end_datetime, 'infinity'::timestamp), '[)')
                    &&
                    tsrange(a2.start_datetime, COALESCE(a2.end_datetime, 'infinity'::timestamp), '[)')
            )
            WHERE a1.organization_id = ?
            AND DATE(a1.start_datetime) = CURRENT_DATE
        ", [$organizationId])[0]->conflicts ?? 0;
    }
}
```

---

Cette documentation technique fournit tous les éléments nécessaires pour intégrer et maintenir le module Affectations dans l'écosystème ZenFleet.