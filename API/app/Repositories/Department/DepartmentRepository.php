<?php

namespace App\Repositories\Department;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{

    public function getModel()
    {
        return Department::class;
    }

    public function getOrderAscWithUserAndManager($modelParam = null)
    {
        if ($modelParam){
            return $modelParam->orderBy('id', 'asc')->with('users')->with('manager');
        }
        return $this->model->orderBy('id', 'asc')->with('users')->with('manager');
    }

    public function getAllUserInDepartmentOfManager()
    {
        return $this->model->where('department_manager_id', auth()->id())->first()->users()->get()->pluck('id')->toArray();
    }
}
