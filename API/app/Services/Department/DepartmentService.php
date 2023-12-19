<?php

namespace App\Services\Department;

use App\Common\SQLOperator;
use App\Http\Resources\DepartmentResource;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

class DepartmentService implements DepartmentServiceInterface
{
    public function __construct(
        protected DepartmentRepositoryInterface $departmentRepository,
        protected UserRepositoryInterface $userRepository,
    )
    {}

    public function getAll()
    {
        return $this->departmentRepository->selectAll();
    }

    public function search($skip, $request)
    {
        $itemsPerPage = 10;
        $department = $this->departmentRepository->getModelByMultiKeys([
            ['LOWER(department_name) like ?', SQLOperator::RAW_SQL, '%' . $request->name . '%'],
            ['LOWER(address) like ?', SQLOperator::RAW_SQL, '%' . $request->address . '%'],
            ['LOWER(email) like ?', SQLOperator::RAW_SQL, '%' . $request->email . '%'],
            ['LOWER(phone_number) like ?', SQLOperator::RAW_SQL, '%' . $request->phoneNumber . '%'],
        ]);
        $department = $this->departmentRepository->getOrderAscWithUserAndManager($department);
        if ($request->manager != '') {
            $department->whereHas('manager', function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->manager . '%');
            });
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

    public function find($user_id)
    {
        return $this->departmentRepository->find($user_id);
    }

    public function getAllUserInDepartment($departmentID)
    {
        $user = $this->userRepository->selectByMultiKeys([
            ['department_id', SQLOperator::EQUAL, $departmentID]
        ]);
        return $user;
    }

    public function create($attribute = [])
    {
        return $this->departmentRepository->create($attribute);
    }

    public function update($department, $attribute = [])
    {
        return $this->departmentRepository->update($department, $attribute);
    }

    public function checkManager()
    {
        return $this->departmentRepository->selectByMultiKeys([
           ['department_manager_id', SQLOperator::EQUAL, auth()->id()]
        ]);
    }
}
