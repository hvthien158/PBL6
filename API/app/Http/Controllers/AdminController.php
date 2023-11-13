<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\CreateShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\TimeKeepingResource;
use App\Http\Resources\ShiftResource;
use App\Models\Department;
use App\Models\Shift;
use App\Models\User;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Http\Requests\DateTimeRequest;
use App\Http\Resources\DepartmentResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $e->getMessage()], 400);
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
            $user = DB::table('users')->delete($id);
            if ($user == 1) {
                return response()->json(['message' => 'Delete user successfully']);
            } else {
                return response()->json(['message' => 'User not found'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param mixed $id
     * 
     * @return object
     */
    public function listDepartment($id, Request $request)
    {
        try {
            $itemsPerPage = 10;
            if ($request->name != '') {
                $department = Department::whereRaw('LOWER(department_name) like ?', ['%' . $request->name . '%'])->skip($id * $itemsPerPage)->take($itemsPerPage)->get();
                $totalPage = ceil(Department::whereRaw('LOWER(department_name) like ?', ['%' . $request->name . '%'])->count() / $itemsPerPage);
            } else {
                $totalPage = ceil(Department::count() / $itemsPerPage);
                $department = Department::skip($id * $itemsPerPage)->take($itemsPerPage)->get();
            }
            $response = [
                'totalPage' => $totalPage,
                'department' => DepartmentResource::collection($department),
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
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
                'phone_number' => $request->phoneNumber,
                'department_manager_id' => $request->manager
            ]);
            return response()->json(['message' => 'Create department successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
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
                'phone_number' => $request->phoneNumber,
                'department_manager_id' => $request->manager
            ]);
            return response()->json(['message' => 'Update department successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $e->getMessage()], 400);
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
                return response()->json(['message' => 'Không có user nào']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param mixed $id
     * @param Request $request
     * 
     * @return object
     */
    public function listShift($id, Request $request)
    {
        try {
            $itemsPerPage = 10;
            if ($request->name != '') {
                $shift = Shift::whereRaw('LOWER(name) like ?', ['%' . $request->name . '%'])->skip($id * 10)->take($itemsPerPage)->get();
                $totalPage = floor(Shift::whereRaw('LOWER(name) like ?', ['%' . $request->name . '%'])->count() / $itemsPerPage) + 1;
            } else {
                $totalPage = floor(Shift::count() / $itemsPerPage) + 1;
                $shift = Shift::skip($id * $itemsPerPage)->take($itemsPerPage)->get();
            }
            $response = [
                'totalPage' => $totalPage,
                'shift' => ShiftResource::collection($shift),
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $e->getMessage()], 400);
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
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param mixed $id
     * @param Shift $shift
     * 
     * @return object
     */
    public function deleteShift($id, Shift $shift)
    {
        $this->authorize('delete', $shift);
        try {
            $shift = Shift::find($id);
            if ($shift)
                $shift->delete();
            return response()->json(['message' => 'Delete shift successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param null $id
     * 
     * @return object
     */
    public function manageTimeKeeping($id, Request $request)
    {
        $this->authorize('viewAny',  TimeKeeping::class);
        try {
            $itemsPerPage = 10;
            $query = TimeKeeping::orderBy('time_check_in', 'desc')->with('user')->with('user.department');
            if ($request->name != '') {
                $query->whereHas('user', function ($subQuery) use ($request) {
                    $subQuery->where('name', 'like', '%' . $request->name . '%');
                });
            }
            if ($request->startDate != '' && $request->endDate != '') {
                $query->whereRaw('DATE(time_check_in) BETWEEN ? AND ?', [$request->startDate, $request->endDate]);
            }
            if ($request->department != '' ) {
                $query->whereHas('user.department', function ($subQuery) use ($request) {
                    $subQuery->where('department_name', '=', $request->department);
                });
            }
            $totalPage = floor($query->count() / $itemsPerPage) + 1;
            $timekeeping = $query->skip($id * $itemsPerPage)->take($itemsPerPage)->get();
            if ($timekeeping) {
                $response = [
                    'totalPage' => $totalPage,
                    'timekeeping' => TimeKeepingResource::collection($timekeeping),
                ];
                return response()->json($response);
            } else {
                return response()->json(['message' => 'Not found timekeeping'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @param null $id
     * 
     * @return object
     */
    public function updateTimeKeeping(DateTimeRequest $request, TimeKeeping $timekeeping, $id)
    {
        $this->authorize('update', $timekeeping);
        try {
            $timekeeping = TimeKeeping::find($id);
            if ($timekeeping) {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', $timekeeping->time_check_in)->format('Y-m-d');
                $timekeeping->update([
                    'time_check_in' => $date . ' ' . $request->timeCheckIn,
                    'time_check_out' => $date . ' ' . $request->timeCheckOut
                ]);
                if ($timekeeping->time_check_out) {
                    $checkin = Carbon::createFromFormat('H:i:s', $request->timeCheckIn);
                    $checkout = Carbon::createFromFormat('H:i:s', $request->timeCheckOut);
                    $shifts = Shift::all();
                    foreach ($shifts as $shift) {
                        if (
                            $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                            $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                        ) {
                            $timekeeping->shift_id = $shift->id;
                            $timekeeping->save();
                            break;
                        }
                    }
                }
                return response()->json(['message' => 'Update successfully']);
            } else {
                return response()->json(['message' =>  'Not found time keeping'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function deleteTimeKeeping($id, TimeKeeping $timekeeping)
    {
        $this->authorize('delete', $timekeeping);
        try {
            $timekeeping = TimeKeeping::find($id);
            if ($timekeeping) {
                $systemtime = SystemTime::find($id);
                $systemtime->delete();
                $timekeeping->delete();
                return response()->json(['message' => 'Delete successfully']);
            } else {
                return response()->json(['message' =>  'Not found time keeping'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
