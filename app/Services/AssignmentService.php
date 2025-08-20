<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\DriverStatus;
use App\Models\Vehicle;
use App\Models\VehicleStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AssignmentService
{
    /**
     * Récupère les affectations filtrées avec pagination.
     */
    public function getFilteredAssignments(array $filters): LengthAwarePaginator
    {
        $query = Assignment::query()
            ->with(['vehicle', 'driver', 'creator', 'handoverForm'])
            ->where('organization_id', Auth::user()->organization_id)
            ->orderBy('start_datetime', 'desc');

        // Filtre de recherche
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('vehicle', function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('registration_plate', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                })
                ->orWhereHas('driver', function ($driverQuery) use ($search) {
                    $driverQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('personal_phone', 'like', "%{$search}%");
                });
            });
        }

        // Filtre de statut
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNull('end_datetime');
            } elseif ($filters['status'] === 'completed') {
                $query->whereNotNull('end_datetime');
            }
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Récupère toutes les affectations pour l'affichage calendaire.
     */
    public function getAssignmentsForCalendar(): Collection
    {
        return Assignment::with(['vehicle', 'driver'])
            ->where('organization_id', Auth::user()->organization_id)
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    /**
     * Récupère les affectations pour une période donnée (calendaire).
     */
    public function getAssignmentsForCalendarPeriod(string $date, string $period = 'month'): Collection
    {
        $targetDate = Carbon::parse($date);
        
        switch ($period) {
            case 'day':
                $startDate = $targetDate->copy()->startOfDay();
                $endDate = $targetDate->copy()->endOfDay();
                break;
            case 'week':
                $startDate = $targetDate->copy()->startOfWeek();
                $endDate = $targetDate->copy()->endOfWeek();
                break;
            case 'month':
            default:
                $startDate = $targetDate->copy()->startOfMonth();
                $endDate = $targetDate->copy()->endOfMonth();
                break;
        }

        return Assignment::with(['vehicle', 'driver', 'creator'])
            ->where('organization_id', Auth::user()->organization_id)
            ->where(function ($query) use ($startDate, $endDate) {
                // Affectations qui commencent dans la période
                $query->whereBetween('start_datetime', [$startDate, $endDate])
                    // Ou affectations qui se terminent dans la période
                    ->orWhereBetween('end_datetime', [$startDate, $endDate])
                    // Ou affectations qui englobent toute la période
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_datetime', '<=', $startDate)
                            ->where(function ($endQuery) use ($endDate) {
                                $endQuery->where('end_datetime', '>=', $endDate)
                                    ->orWhereNull('end_datetime');
                            });
                    });
            })
            ->orderBy('start_datetime', 'asc')
            ->get();
    }

    /**
     * Compte le nombre total d'affectations.
     */
    public function getTotalAssignmentsCount(): int
    {
        return Assignment::where('organization_id', Auth::user()->organization_id)->count();
    }

    /**
     * Récupère les données nécessaires pour le formulaire de création.
     */
    /**
     * Récupère les données nécessaires pour le formulaire de création.
     */
    public function getDataForCreateForm(): array
    {
        $availableVehicles = Vehicle::whereHas('vehicleStatus', function ($query) {
            $query->where('name', 'Parking');
        })->get();

        $availableDrivers = Driver::whereHas('driverStatus', function ($query) {
            $query->where('name', 'Disponible');
        })->get();

        return compact('availableVehicles', 'availableDrivers');
    }

    /**
     * Crée une nouvelle affectation.
     */
    public function createAssignment(array $data): Assignment
    {
        $data['created_by_user_id'] = Auth::id();
        $data['organization_id'] = Auth::user()->organization_id;

        $assignment = Assignment::create($data);

        // Mise à jour du statut du véhicule et du conducteur
        $this->updateVehicleStatus($assignment->vehicle_id, 'En mission');
        $this->updateDriverStatus($assignment->driver_id, 'En mission');

        return $assignment->load(['vehicle', 'driver']);
    }

    /**
     * Met à jour une affectation existante.
     */
    public function updateAssignment(Assignment $assignment, array $data): Assignment
    {
        $assignment->update($data);
        return $assignment->load(['vehicle', 'driver']);
    }

    /**
     * Termine une affectation.
     */
    public function endAssignment(Assignment $assignment, int $endMileage, string $endDateTime): bool
    {
        try {
            $assignment->update([
                'end_datetime' => $endDateTime,
                'end_mileage' => $endMileage,
            ]);

            // Mise à jour du kilométrage du véhicule
            $assignment->vehicle->update([
                'current_mileage' => $endMileage,
            ]);

            // Mise à jour du statut du véhicule et du conducteur
            $this->updateVehicleStatus($assignment->vehicle_id, 'Parking');
            $this->updateDriverStatus($assignment->driver_id, 'Disponible');

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la clôture de l\'affectation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour le statut d'un véhicule.
     */
    private function updateVehicleStatus(int $vehicleId, string $statusName): void
    {
        $status = VehicleStatus::where('name', $statusName)->first();
        if ($status) {
            Vehicle::where('id', $vehicleId)->update(['status_id' => $status->id]);
        }
    }

    /**
     * Met à jour le statut d'un conducteur.
     */
    private function updateDriverStatus(int $driverId, string $statusName): void
    {
        $status = DriverStatus::where('name', $statusName)->first();
        if ($status) {
            Driver::where('id', $driverId)->update(['status_id' => $status->id]);
        }
    }

    /**
     * Récupère les statistiques des affectations.
     */
    public function getAssignmentStats(): array
    {
        $organizationId = Auth::user()->organization_id;

        return [
            'total' => Assignment::where('organization_id', $organizationId)->count(),
            'active' => Assignment::where('organization_id', $organizationId)
                ->whereNull('end_datetime')->count(),
            'completed_this_month' => Assignment::where('organization_id', $organizationId)
                ->whereNotNull('end_datetime')
                ->whereMonth('end_datetime', now()->month)
                ->whereYear('end_datetime', now()->year)
                ->count(),
            'average_duration' => $this->getAverageDuration(),
        ];
    }

    /**
     * Calcule la durée moyenne des affectations terminées.
     */
    private function getAverageDuration(): float
    {
        $completedAssignments = Assignment::where('organization_id', Auth::user()->organization_id)
            ->whereNotNull('end_datetime')
            ->get(['start_datetime', 'end_datetime']);

        if ($completedAssignments->isEmpty()) {
            return 0;
        }

        $totalDuration = $completedAssignments->sum(function ($assignment) {
            return Carbon::parse($assignment->end_datetime)
                ->diffInDays(Carbon::parse($assignment->start_datetime));
        });

        return round($totalDuration / $completedAssignments->count(), 1);
    }

    /**
     * Récupère les affectations en conflit pour une période donnée.
     */
    public function getConflictingAssignments(int $vehicleId, int $driverId, string $startDateTime, ?string $endDateTime = null, ?int $excludeAssignmentId = null): Collection
    {
        $query = Assignment::where('organization_id', Auth::user()->organization_id)
            ->where(function ($q) use ($vehicleId, $driverId) {
                $q->where('vehicle_id', $vehicleId)
                    ->orWhere('driver_id', $driverId);
            });

        if ($excludeAssignmentId) {
            $query->where('id', '!=', $excludeAssignmentId);
        }

        // Vérification des conflits de dates
        $query->where(function ($dateQuery) use ($startDateTime, $endDateTime) {
            if ($endDateTime) {
                // Affectation avec date de fin définie
                $dateQuery->where(function ($q) use ($startDateTime, $endDateTime) {
                    $q->whereBetween('start_datetime', [$startDateTime, $endDateTime])
                        ->orWhereBetween('end_datetime', [$startDateTime, $endDateTime])
                        ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                            $overlap->where('start_datetime', '<=', $startDateTime)
                                ->where('end_datetime', '>=', $endDateTime);
                        });
                });
            } else {
                // Affectation sans date de fin (en cours)
                $dateQuery->where(function ($q) use ($startDateTime) {
                    $q->where('start_datetime', '<=', $startDateTime)
                        ->whereNull('end_datetime');
                })
                ->orWhere('start_datetime', '>=', $startDateTime);
            }
        });

        return $query->with(['vehicle', 'driver'])->get();
    }
}