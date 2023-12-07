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

    public function getAllFiltered($skip, $request)
    {
        $itemsPerPage = 10;
        $department = Department::orderBy('id', 'asc')->with('users')->with('manager');
        if ($request->name != '') {
            $department->whereRaw('LOWER(department_name) like ?', ['%' . $request->name . '%']);
        }
        if ($request->manager != '') {
            $department->whereHas('manager', function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->manager . '%');
            });
        }
        if ($request->address != '') {
            $department->whereRaw('LOWER(address) like ?', ['%' . $request->address . '%']);
        }
        if ($request->email != '') {
            $department->whereRaw('LOWER(email) like ?', ['%' . $request->email . '%']);
        }
        if ($request->phoneNumber != '') {
            $department->whereRaw('LOWER(phone_number) like ?', ['%' . $request->phoneNumber . '%']);
        }
        if ($request->minStaff != 0) {
            $department->has('users', '>=', $request->minStaff);
        }
        if ($request->maxStaff != 0) {
            $department->has('users', '<=', $request->maxStaff);
        }
        $totalDepartment = $department->count();
        $departments = $department->skip($skip * $itemsPerPage)->take($itemsPerPage)->get();
        return [
            'totalDepartment' => $totalDepartment,
            'department' => DepartmentResource::collection($departments),
        ];
    }
    /**
     * @param mixed $id
     * 
     * @return boolean
     */
    public function checkManager($id) {
        return Department::whereIn('department_manager_id', [$id])->first();
    }
}
