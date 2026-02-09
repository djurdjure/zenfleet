<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\AssignmentPresenceService;

class AssignmentService
{
    protected AssignmentRepositoryInterface $assignmentRepository;

    public function __construct(AssignmentRepositoryInterface $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    public function getFilteredAssignments(array $filters): LengthAwarePaginator
    {
        return $this->assignmentRepository->getFiltered($filters);
    }

    public function getDataForCreateForm(): array
    {
        $availableVehicles = Vehicle::whereHas('vehicleStatus', function ($query) {
            $query->where('name', 'Parking');
        })->orderBy('brand')->get();

        $availableDrivers = Driver::whereHas('driverStatus', function ($query) {
            $query->where('name', 'Disponible');
        })->orderBy('last_name')->get();

        return compact('availableVehicles', 'availableDrivers');
    }

    public function createAssignment(array $data): Assignment
    {
        return $this->assignmentRepository->create($data);
    }

    public function updateAssignment(Assignment $assignment, array $data): bool
    {
        return $this->assignmentRepository->update($assignment, $data);
    }

   
    public function endAssignment(Assignment $assignment, int $endMileage, string $endDateTime): bool
    {
        // Mettre à jour l'affectation elle-même
        $updated = $this->assignmentRepository->update($assignment, [
            'end_datetime' => $endDateTime,
            'end_mileage' => $endMileage,
        ]);

        if ($updated) {
            $presence = app(AssignmentPresenceService::class);
            $presence->syncForAssignment($assignment->fresh(), now(), Carbon::parse($endDateTime));
        }

        return $updated;
    }
}
