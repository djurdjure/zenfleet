<?php

namespace App\Repositories\Eloquent;

use App\Models\Assignment;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AssignmentRepository implements AssignmentRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $query = Assignment::query()->with(['vehicle', 'driver']);

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->whereNull('end_datetime');
            } elseif ($filters['status'] === 'ended') {
                $query->whereNotNull('end_datetime');
            }
        }

        if (!empty($filters['search'])) {
            $searchTerm = strtolower($filters['search']);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('vehicle', function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(registration_plate) LIKE ?', ["%{$searchTerm}%"]);
                })->orWhereHas('driver', function ($subQuery) use ($searchTerm) {
                    $subQuery->whereRaw('LOWER(first_name) LIKE ?', ["%{$searchTerm}%"])
                             ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$searchTerm}%"]);
                });
            });
        }

        return $query->orderBy('start_datetime', 'desc')->paginate($perPage)->withQueryString();
    }

    public function find(int $id): ?Assignment
    {
        return Assignment::find($id);
    }

    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }

    public function update(Assignment $assignment, array $data): bool
    {
        return $assignment->update($data);
    }
}
