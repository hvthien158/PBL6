<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

/**
 * [DepartmentController]
 */
class DepartmentController extends Controller
{   
    /**
     * @param null|int $id
     * 
     * @return object
     */
    public function index($id = null)
    {
        $department = ($id == null) ? Department::all() : Department::where('id',$id)->get();
        return DepartmentResource::collection($department);
    }
}
