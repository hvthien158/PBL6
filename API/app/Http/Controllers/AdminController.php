<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\CreateShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ManageTimeKeepingRequest;
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
use App\Repositories\Department\DepartmentRepository;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Message\MessageRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Shift\ShiftRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepo,
        protected DepartmentRepositoryInterface $departmentRepo,
        protected ShiftRepositoryInterface $shiftRepo,
        protected TimeKeepingRepositoryInterface $timeKeepRepo,
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function listUser(Request $request, $id)
    {
        $this->authorize('adminView', User::class);
        try {
            $result = $this->userRepo->getAllFiltered($request, $id);
            $response = [
                'totalUser' => $result['total'],
                'user' => UserResource::collection($result['user'])
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
            $this->userRepo->create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'password' => bcrypt($request->password),
                'department_id' => $request->department_id,
                'address' => $request->address,
                'DOB' => $request->DOB,
                'phone_number' => $request->phone_number,
                'salary' => $request->salary,
                'position' => $request->position,
                'role' => $request->role
            ]);
            return response()->json(['message' => ResponseMessage::CREATE_SUCCESS], 201);
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
    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', User::class);
        try {
            $this->userRepo->update($user, [
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
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
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
    public function deleteUser(User $user)
    {
        $this->authorize('delete', $user);
        try {
            $user->delete();
            return response()->json(['message' => ResponseMessage::DELETE_SUCCESS]);
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
        $this->authorize('viewDepartment', Department::class);
        try {
            return response()->json($this->departmentRepo->getAllFiltered($id, $request));
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
            $this->departmentRepo->create([
                'department_name' => $request->departmentName,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phoneNumber,
                'department_manager_id' => $request->manager
            ]);
            return response()->json(['message' => ResponseMessage::CREATE_SUCCESS], 201);
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
    public function updateDepartment(UpdateDepartmentRequest $request, Department $department)
    {
        $this->authorize('update', Department::class);
        try {
            $this->departmentRepo->update($department, [
                'department_name' => $request->departmentName,
                'address' => $request->address,
                'email' => $request->email,
                'phone_number' => $request->phoneNumber,
                'department_manager_id' => $request->manager
            ]);
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
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
    public function deleteDepartment(Department $department)
    {
        $this->authorize('delete', $department);
        try {
            $department->delete();
            return response()->json(['message' => ResponseMessage::DELETE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param mixed $name
     *
     * @return object
     */
    public function getUserDepartment(Department $department)
    {
        $this->authorize('viewUser', $department);
        try {
            $userDepartment = $department->users()->get();
            if ($userDepartment) {
                return UserResource::collection($userDepartment);
            } else {
                return response()->json(['message' => ResponseMessage::NOT_FOUND_ERROR], 404);
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
            return response()->json($this->shiftRepo->getAllFiltered($id, $request));
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
        try {
            $this->shiftRepo->create([
                'name' => $request->name,
                'time_valid_check_in' => $request->timeValidCheckIn,
                'time_valid_check_out' => $request->timeValidCheckOut,
                'amount' => $request->amount
            ]);
            return response()->json(['message' => ResponseMessage::CREATE_SUCCESS], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param UpdateShiftRequest $request
     * @param Shift $shift
     *
     * @return object
     */
    public function updateShift(UpdateShiftRequest $request, Shift $shift)
    {
        try {
            $this->shiftRepo->update($shift, [
                'name' => $request->name,
                'time_valid_check_in' => $request->timeValidCheckIn,
                'time_valid_check_out' => $request->timeValidCheckOut,
                'amount' => $request->amount
            ]);
            return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
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
    public function deleteShift(Shift $shift)
    {
        try {
            $shift->delete();
            return response()->json(['message' => ResponseMessage::DELETE_SUCCESS]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @param $skip
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function manageTimeKeeping($skip, ManageTimeKeepingRequest $request)
    {
        try {
            $this->authorize('viewAny', TimeKeeping::class);
            $result = $this->timeKeepRepo->timeKeepingStatistic($skip, $request);
            return response()->json([
                'quantity' => $result['count_user'],
                'data' => $result['result']
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    /**
     * @param ManageTimeKeepingRequest $request
     *
     * @return object
     */
    public function ExportTimeKeepingStatistic(ManageTimeKeepingRequest $request)
    {
        try {
            $this->authorize('viewAny', TimeKeeping::class);
            $fromMonth = Carbon::parse($request->from)->startOfMonth();
            $toMonth = Carbon::parse($request->to)->endOfMonth();
            $timekeeping = $this->timeKeepRepo->findTimeKeepingWaitingAccept($fromMonth, $toMonth);
            $userWaitingAccept = array_unique($timekeeping->pluck('user_id')->toArray());
            $month = [];

            $startDate = $fromMonth;
            while ($startDate <= $toMonth) {
                $endDate = $startDate->copy()->endOfMonth();
                $month[] = [
                    'from' => $startDate->toDateString(),
                    'to' => $endDate->toDateString()
                ];
                $startDate = $startDate->addMonth();
            }
            $data = [];
            foreach ($month as $months) {
                $dataRequest = array_merge($months, ['limit' => 0, 'name' => null, 'department' => null]);
                $result = $this->timeKeepRepo->timeKeepingStatistic(0, json_decode(json_encode($dataRequest)));
                $filteredResult = array_filter($result['result'], function ($item) use ($userWaitingAccept) {
                    return !in_array($item['id'], $userWaitingAccept);
                });
                $data[] = [
                    'month' => Carbon::parse($months['from'])->format('Y-m'),
                    'data' => array_values($filteredResult)
                ];
            }
            return response()->json([
                'userWaitingAccept' => array_values($userWaitingAccept),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
