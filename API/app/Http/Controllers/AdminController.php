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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }
    public function listUser(Request $request, $id)
    {
        try {
            $itemsPerPage = 8;
            $user = User::orderBy('id', 'asc');
            if($request->name != '') {
                $user->whereRaw('LOWER(name) like ?' , ['%'.$request->name.'%']);
            } 
            if($request->email != '') {
                $user->whereRaw('LOWER(email) like ?' , ['%'.$request->email.'%']);
            }
            if($request->address != '') {
                $user->whereRaw('LOWER(address) like ?' , ['%'.$request->address.'%']);
            }
            if($request->phoneNumber != '') {
                $user->whereRaw('LOWER(phone_number) like ?' , ['%'.$request->phoneNumber.'%']);
            } 
            if($request->position != '') {
                $user->where('position', $request->position);
            }
            if($request->role != '') {
                $user->where('role', $request->role);
            }
            if($request->department != '') {
                $user->where('department_id', $request->department);
            }
            $totalPage = ceil($user->count() / $itemsPerPage);
            $user = $user->skip($id * $itemsPerPage)->take($itemsPerPage)->get();
            $response = [
                'totalPage' => $totalPage,
                'user' => UserResource::collection($user)
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);

        }
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
            $totalPage = ceil($department->count() / $itemsPerPage);
            $departments = $department->skip($id * $itemsPerPage)->take($itemsPerPage)->get();
            $response = [
                'totalPage' => $totalPage,
                'department' => DepartmentResource::collection($departments),
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
    public function getUserDepartment($id, Department $department)
    {
        $this->authorize('viewUser', $department);
        try {
            $userDepartment = Department::find($id)->users()->get();
            if ($userDepartment) {
                return UserResource::collection($userDepartment);
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
     * @param $skip
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function manageTimeKeeping($skip, Request $request)
    {
        $this->authorize('viewAny',  TimeKeeping::class);
        $validator = Validator::make($request->all(), [
            'from' => 'required',
            'to' => 'required',
            'name' => 'nullable',
            'department' => 'nullable'
        ]);

        if($validator->fails()){
            return response()->json(['failed_data' => $validator->failed()], 422);
        }

        $from = $request->from;
        $to = $request->to;
        $name = $request->name;
        $department = $request->department;
        $count_user = 0;

        try {
            if($department){
                if($name){
                    $users = DB::table('users')
                        ->where('name', 'like',  '%'.$name.'%')
                        ->where('department_id', '=', $department);
                    $count_user = count($users->get());
                    $users = $users->limit(10)
                        ->offset($skip * 10)
                        ->get();
                } else {
                    $users = DB::table('users')
                        ->where('department_id', '=', $department);
                    $count_user = count($users->get());
                    $users = $users
                        ->limit(10)
                        ->offset($skip * 10)
                        ->get();
                }
            } else {
                if($name){
                    $users = DB::table('users')
                        ->where('name', 'like',  '%'.$name.'%');
                    $count_user = count($users->get());
                    $users = $users
                        ->limit(10)
                        ->offset($skip * 10)
                        ->get();
                } else {
                    $count_user = User::count();
                    $users = User::limit(10)->offset($skip * 10)->get(['id', 'name', 'department_id']);
                }
            }
            $result = [];
            foreach ($users as $user){
                $timeKeepings = TimeKeeping::where('user_id', $user->id)
                    ->whereBetween('_date', [$from, $to])
                    ->orderBy('_date', 'desc')->get();
                $sumWorkingDays = 0;
                $sumWorkingTime = '';
                $sumWorkingHours = 0;
                $sumWorkingMinutes = 0;
                $averageWorkingHours = 0;
                $lateDays = 0;
                foreach ($timeKeepings as $timeKeeping){
                    if($timeKeeping->time_check_in){
                        $sumWorkingDays += 1;
                        if(Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_in)
                            ->isAfter(Carbon::createFromFormat('H:i:s', '08:30:00'))
                        ){
                            $lateDays += 1;
                        }
                    }

                    if($timeKeeping->time_check_in && $timeKeeping->time_check_out){
                        $carbonCheckIn = Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_in);
                        $carbonCheckOut = Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_out);
                        $timeWorkHours = $carbonCheckOut->diffInHours($carbonCheckIn);
                        $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn) - $timeWorkHours*60;
                        $sumWorkingHours += $timeWorkHours;
                        $sumWorkingMinutes += $timeWorkMinutes;
                    }
                }
                $sumWorkingHours += intdiv($sumWorkingMinutes, 60);
                $sumWorkingMinutes = $sumWorkingMinutes % 60;
                $averageWorkingHours = $sumWorkingHours == 0 ? 0 : str_pad(intdiv($sumWorkingHours, $sumWorkingDays), 2, '0', STR_PAD_LEFT)
                    .':'
                    .str_pad(intdiv($sumWorkingMinutes, $sumWorkingDays), 2, '0', STR_PAD_LEFT);
                $sumWorkingTime = str_pad($sumWorkingHours, 2, '0', STR_PAD_LEFT)
                    .':'
                    .str_pad($sumWorkingMinutes, 2, '0', STR_PAD_LEFT);

                $result[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'sumWorkingDays' => $sumWorkingDays,
                    'sumWorkingTime' => $sumWorkingTime,
                    'averageWorkingHours' => $averageWorkingHours,
                    'lateDays' => $lateDays,
                    'department' => Department::where('id', $user->department_id)->first()->department_name,
                ];
            }

            return response()->json(['quantity' => $count_user, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
