<?php

namespace App\Repositories\Department;

use App\Models\Department;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{

    public function getModel()
    {
        return Department::class;
    }
}
