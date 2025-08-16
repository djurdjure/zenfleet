<?php

namespace App\Repositories\Interfaces;

use App\Models\Assignment;
use Illuminate\Pagination\LengthAwarePaginator;

interface AssignmentRepositoryInterface
{
    public function getFiltered(array $filters): LengthAwarePaginator;
    public function find(int $id): ?Assignment;
    public function create(array $data): Assignment;
    public function update(Assignment $assignment, array $data): bool;
}
