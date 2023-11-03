<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\CreateShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\Shift;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }
    /**
     * @param CreateUserRequest $request
     *
     * @return object
     */
    public function createUser(CreateUserRequest $request)
    {
        $this->authorize('create', User::class);
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'department_id' => $request->department_id,
                'address' => $request->address,
                'DOB' => $request->DOB,
                'phone_number' => $request->phone_number,
                'salary' => $request->salary,
                'position' => $request->position,
                'role' => $request->role
            ]);
            return response()->json(['message' => 'Create user successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @param int $id
     *
     * @return object
     */
    public function updateUser(UpdateUserRequest $request, User $user, $id)
    {
        $this->authorize('update', $user);
        try {
            $user = User::find($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'department_id' => $request->department_id,
                'address' => $request->address,
                'DOB' => $request->DOB,
                'phone_number' => $request->phone_number,
                'salary' => $request->salary,
                'position' => $request->position,
                'role' => $request->role
            ]);
            return response()->json(['message' => 'Update user successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param User $user
     * @param int $id
     *
     * @return object
     */
    public function deleteUser(User $user, $id)
    {
        $this->authorize('delete', $user);
        try {
            $user = User::find($id);
            if ($user) {
                $user->delete();
            }
            return response()->json(['message' => 'Delete user successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param CreateDepartmentRequest $request
     *
     * @return object
     */
    public function createDepartment(CreateDepartmentRequest $request)
    {
        $this->authorize('create', Department::class);
        try {
            Department::create([
                'department_name' => $request->departmentName,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phoneNumber
            ]);
            return response()->json(['message' => 'Create department successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param UpdateDepartmentRequest $request
     * @param Department $department
     * @param int $id
     *
     * @return object
     */
    public function updateDepartment(UpdateDepartmentRequest $request, Department $department, $id)
    {
        $this->authorize('update', $department);
        try {
            Department::find($id)->update([
                'department_name' => $request->departmentName,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phoneNumber
            ]);
            return response()->json(['message' => 'Update department successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param int $id
     * @param Department $department
     *
     * @return object
     */
    public function deleteDepartment($id, Department $department)
    {
        $this->authorize('delete', $department);
        try {
            $department = Department::find($id);
            if ($department)
                $department->delete();
            return response()->json(['message' => 'Delete department successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param mixed $name
     *
     * @return object
     */
    public function getUserDepartment($name, Department $department)
    {
        $this->authorize('viewUser', $department);
        try {
            $department = Department::where('department_name', $name)->first();
            $user = User::where('department_id', $department->id)->get();
            if ($user) {
                return UserResource::collection($user);
            } else {
                return response()->json(['message'=> 'Không có user nào']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param CreateShiftRequest $request
     *
     * @return object
     */
    public function createShift(CreateShiftRequest $request)
    {
        $this->authorize('create', Shift::class);
        try {
            Shift::create([
                'name' => $request->name,
                'time_valid_check_in' => $request->timeValidCheckIn,
                'time_valid_check_out' => $request->timeValidCheckOut,
                'amount' => $request->amount
            ]);
            return response()->json(['message' => 'Create shift successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    /**
     * @param UpdateShiftRequest $request
     * @param Shift $shift
     * @param int $id
     *
     * @return object
     */
    public function updateShift(UpdateShiftRequest $request, Shift $shift, $id)
    {
        $this->authorize('update', $shift);
        try {
            Shift::find($id)->update([
                'name' => $request->name,
                'time_valid_check_in' => $request->timeValidCheckIn,
                'time_valid_check_out' => $request->timeValidCheckOut,
                'amount' => $request->amount
            ]);
            return response()->json(['message' => 'Update shift successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
