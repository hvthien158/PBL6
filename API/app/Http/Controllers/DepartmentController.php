<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use Illuminate\Http\Request;

/**
 * [DepartmentController]
 */
class DepartmentController extends Controller
{

    public function __construct(protected DepartmentRepositoryInterface $departmentRepo)
    {
    }

    /**
     * @param null|int $id
     *
     * @return object
     */
    public function index($id = null)
    {
        if($id == null){
            return DepartmentResource::collection($this->departmentRepo->getAll());
        }
        return new DepartmentResource($this->departmentRepo->find($id));
    }
}
