<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Services\Department\DepartmentServiceInterface;
use Illuminate\Http\Request;

/**
 * [DepartmentController]
 */
class DepartmentController extends Controller
{

    public function __construct(protected DepartmentServiceInterface $departmentService)
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
            return DepartmentResource::collection($this->departmentService->getAll());
        }
        return new DepartmentResource($this->departmentService->find($id));
    }
}
