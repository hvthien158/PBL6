<?php

namespace App\Services\TimeKeeping;

use App\Common\ResponseMessage;
use App\Common\Role;
use App\Common\SQLOperator;
use App\Common\StatusWorking;
use App\Common\TimeByShift;
use App\Common\TimeZone;
use App\Models\Department;
use App\Models\Shift;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Models\User;
use App\Repositories\Department\DepartmentRepositoryInterface;
use App\Repositories\Shift\ShiftRepositoryInterface;
use App\Repositories\Systemtime\SystemtimeRepositoryInterface;
use App\Repositories\TimeKeeping\TimeKeepingRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TimekeepingService implements TimekeepingServiceInterface
{
    public function __construct(
        protected TimeKeepingRepositoryInterface $timeKeepingRepository,
        protected SystemtimeRepositoryInterface $systemtimeRepository,
        protected ShiftRepositoryInterface $shiftRepository,
        protected UserRepositoryInterface $userRepository,
        protected DepartmentRepositoryInterface $departmentRepository,
    )
    {}

    public function checkIn()
    {
        $date = Carbon::now(new DateTimeZone(TimeZone::ASIA_HO_CHI_MINH))->format('Y-m-d');
        $now = Carbon::now(new DateTimeZone(TimeZone::ASIA_HO_CHI_MINH))->format('H:i:s');

        $checkTimeKeeping = $this->timeKeepingRepository->getModelByMultiKeys([
            ['_date', SQLOperator::EQUAL, Carbon::now()->setTimezone(TimeZone::ASIA_HO_CHI_MINH)->toDateString()],
            ['user_id', SQLOperator::EQUAL, auth()->id()]
        ])->first();

        if ($checkTimeKeeping) {
            $checkTimeKeeping->time_check_in = $now;
            $checkTimeKeeping->save();

            $this->systemtimeRepository->updateByID($checkTimeKeeping->id, [
                'time_check_in' => $now
            ]);
        } else {
            $timekeepID = $this->timeKeepingRepository->insertGetId([
                'user_id' => auth()->id(),
                '_date' => $date,
                'time_check_in' => $now
            ]);
            return $this->systemtimeRepository->create([
                'id' => $timekeepID,
                '_date' => $date,
                'time_check_in' => $now
            ]);
        }
    }

    public function checkOut()
    {
        $now = Carbon::now()->setTimezone(TimeZone::ASIA_HO_CHI_MINH);
        $checkTimeKeeping = $this->timeKeepingRepository->getModelByMultiKeys([
            ['_date', SQLOperator::EQUAL, $now->toDateString()],
            ['user_id', SQLOperator::EQUAL, auth()->id()]
        ])->first();
        if ($checkTimeKeeping && $now->isAfter($checkTimeKeeping->_date)) {
            $checkTimeKeeping->time_check_out = $now->toTimeString();
            $checkTimeKeeping->save();

            $this->systemtimeRepository->updateByID($checkTimeKeeping->id, [
                'time_check_out' => $now->format('H:i:s')
            ]);

            $shifts = $this->shiftRepository->getAll();
            $timeCheckIn = Carbon::createFromFormat('H:i:s', $checkTimeKeeping->time_check_in, TimeZone::ASIA_HO_CHI_MINH);
            $timeCheckOut = Carbon::createFromFormat('H:i:s', $checkTimeKeeping->time_check_out, TimeZone::ASIA_HO_CHI_MINH);
            foreach ($shifts as $shift) {
                if (
                    $timeCheckIn->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                    $timeCheckOut->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                ) {
                    $checkTimeKeeping->shift_id = $shift->id;
                    $checkTimeKeeping->save();
                    break;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function getToday()
    {
        $timeKeeping = $this->timeKeepingRepository->getModelByMultiKeys([
            ['_date', SQLOperator::EQUAL, Carbon::now()->setTimezone(TimeZone::ASIA_HO_CHI_MINH)->toDateString()],
            ['user_id', SQLOperator::EQUAL, auth()->id()]
        ])->first();

        if ($timeKeeping) {
            $systemTime = $this->systemtimeRepository->find($timeKeeping->id);
            return [
                'data' => $systemTime,
                'status_AM' => $timeKeeping->status_am,
                'status_PM' => $timeKeeping->status_pm
            ];
        } else {
            $dayOfWeek = Carbon::now(TimeZone::ASIA_HO_CHI_MINH)->dayOfWeek;
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                $status_AM = 2;
                $status_PM = 2;
                return [
                    'weekend' => 1,
                    'status_AM' => $status_AM,
                    'status_PM' => $status_PM,
                ];
            }
            $status_AM = 0;
            $status_PM = 0;
            return [
                'status_AM' => $status_AM,
                'status_PM' => $status_PM,
            ];
        }
    }

    public function search($from, $to, $role, $user_id = null)
    {
        if (!$user_id) {
            return $this->timeKeepingRepository->getByDateBetweenAndOrderByDesc($from, $to, auth()->id());
        }
        if ($role == Role::ADMIN) {
            $timeKeeping = $this->timeKeepingRepository->getByDateBetweenAndOrderByDesc($from, $to, $user_id);
            if (count($timeKeeping) == 0) {
                return response()->json([
                    'data' => [
                        [
                            'user' => $this->userRepository->find($user_id)->name
                        ]
                    ]
                ]);
            }
            return $timeKeeping;
        }
        return [];
    }

    public function getBetweenMonth($fromMonth, $toMonth, $userID)
    {
        return $this->timeKeepingRepository->getByDateBetweenAndOrderByDesc($fromMonth, $toMonth, $userID);
    }

    public function getBetweenDate($request)
    {
        $startDate = $request->startTime;
        $endDate = $request->endTime;
        $startDateTime = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDateTime = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

        return $this->timeKeepingRepository->getModelByMultiKeys([
           ['_date', SQLOperator::BETWEEN, [$startDateTime, $endDateTime]]
        ]);
    }

    public function getInMonth($request)
    {
        $month = $request->month;
        $year = $request->year;
        return $this->timeKeepingRepository->getInMonthYear($month, $year, auth()->id());
    }

    public static function handleUpdate(
        TimeKeepingRepositoryInterface $timeKeepingRepository,
        $status_am,
        $status_pm,
        $checkin,
        $checkout,
        $timekeep,
        $date,
        $userID
    )
    {
        $need_request_status = ($status_am !== StatusWorking::WORK && $status_am !== $timekeep->status_am)
            || ($status_pm !== StatusWorking::WORK && $status_pm !== $timekeep->status_pm);
        //Situation: User change status to default AM: work, PM: work
        $default_status = $status_am == StatusWorking::WORK && $status_pm == StatusWorking::WORK;
        $need_request_time = ($checkin && $checkin->format('H:i:s') !== $timekeep->time_check_in)
            || ($checkout && $checkout->format('H:i:s') !== $timekeep->time_check_out);

        $timeKeepingRepository->updateByDateAndUserID($date, $userID, [
                'time_check_in' => $checkin ? $checkin->format('H:i:s') : $timekeep->time_check_in,
                'time_check_out' => $checkout ? $checkout->format('H:i:s') : $timekeep->time_check_out,
                'status_am' => $status_am >= 0 ? $status_am : $timekeep->status_am,
                'status_pm' => $status_pm >= 0 ? $status_pm : $timekeep->status_pm,
                'admin_accept_status' => $default_status ? null : ($need_request_status ? 0 : $timekeep->admin_accept_status),
                'admin_accept_time' => $need_request_time ? 0 : $timekeep->admin_accept_time,
            ]);
    }

    public static function handleUpdateShift(
        ShiftRepositoryInterface $shiftRepository,
        TimeKeepingRepositoryInterface $timeKeepingRepository,
        $checkin,
        $checkout,
        $date,
        $userID
    )
    {
        $shifts = $shiftRepository->getAll();
        foreach ($shifts as $shift) {
            if (
                $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
            ) {
                $timeKeepingRepository->updateByDateAndUserID($date, $userID, ['shift_id' => $shift->id]);
                break;
            }
        }
    }

    public static function handleCreate(
        TimeKeepingRepositoryInterface $timeKeepingRepository,
        SystemtimeRepositoryInterface $systemtimeRepository,
        $status_am,
        $status_pm,
        $checkin,
        $checkout,
        $date,
        $userID
    )
    {
        $newtimekeepID = $timeKeepingRepository->insertGetId([
            'user_id' => $userID,
            '_date' => $date,
            'time_check_in' => $checkin ? $checkin->format('H:i:s') : null,
            'time_check_out' => $checkout ? $checkout->format('H:i:s') : null,
            'status_am' => $status_am ?: 0,
            'status_pm' => $status_pm ?: 0,
            'admin_accept_status' => ($status_am || $status_pm) ? 0 : null,
            'admin_accept_time' => ($checkin || $checkout) ? 0 : null,
        ]);
        return $systemtimeRepository->create([
            'id' => $newtimekeepID,
            '_date' => $date,
            'time_check_in' => '00:00:00',
            'time_check_out' => '00:00:00',
        ]);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function customUpdate($request)
    {
        $status_am = $request->get('status_am');
        $status_pm = $request->get('status_pm');

        $date = $request->get('date');
        $checkin = null;
        $checkout = null;

        if ($request->get('time_check_in')) {
            $checkin = Carbon::createFromFormat('H:i', $request->get('time_check_in'));
        }
        if ($request->get('time_check_out')) {
            $checkout = Carbon::createFromFormat('H:i', $request->get('time_check_out'));
        }

        $timekeep = DB::table('time_keepings')->where('user_id', '=', $request->user_id)
            ->where('_date', SQLOperator::EQUAL, $date)->first();

        if($checkin && $checkout){
            if($checkout->isBefore($checkin)){
                return response()->json(['error' => 'Checkout not allow before checkin'], 400);
            }
        } else if($checkin){
            if($checkin->isAfter($timekeep->time_check_out)){
                return response()->json(['error' => 'Checkin not allow after checkout'], 400);
            }
        } else if($checkout) {
            if($checkout->isBefore($timekeep->time_check_in)){
                return response()->json(['error' => 'Checkout not allow before checkin'], 400);
            }
        }

        if ($timekeep) {
            self::handleUpdate($this->timeKeepingRepository, $status_am, $status_pm, $checkin, $checkout, $timekeep, $date, $request->user_id);
            if ($timekeep->time_check_in && $checkout) {
                $timekeep_checkin = Carbon::createFromFormat('H:i:s', $timekeep->time_check_in);
                self::handleUpdateShift($this->shiftRepository, $this->timeKeepingRepository, $timekeep_checkin, $checkout, $date, $request->user_id);
            } else if ($timekeep->time_check_out && $checkin) {
                $timekeep_checkout = Carbon::createFromFormat('H:i:s', $timekeep->time_check_out);
                self::handleUpdateShift($this->shiftRepository, $this->timeKeepingRepository, $checkin, $timekeep_checkout, $date, $request->user_id);
            }
        } else {
            if (!self::handleCreate($this->timeKeepingRepository, $this->systemtimeRepository, $status_am, $status_pm, $checkin, $checkout, $date, $request->user_id)) {
                return response()->json(['error' => 'Cannot create systemtime'], 400);
            }
            if ($checkin && $checkout) {
                self::handleUpdateShift($this->shiftRepository, $this->timeKeepingRepository, $checkin, $checkout, $date, $request->user_id);
            }
        }
        return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
    }

    public static function getRegularDaysBetween2Days($from, $to){
        $date_from = Carbon::createFromFormat('Y-m-d', $from);
        $date_to = Carbon::createFromFormat('Y-m-d', $to);
        return $date_from->diffInDaysFiltered(function (Carbon $date){
            return !$date->isSunday() && !$date->isSaturday();
        }, $date_to->addSecond()); //add second to take last day
    }

    public static function formatTime($hour, $minute){
        return str_pad($hour, 2, '0', STR_PAD_LEFT)
            . ':'
            . str_pad($minute, 2, '0', STR_PAD_LEFT);
    }

    public static function calcSumWorkingMinutes($carbonCheckIn, $carbonCheckOut, $shiftMorning, $shiftAfternoon){
        if($carbonCheckOut->isBefore($shiftAfternoon['start'])){
            if(!$carbonCheckIn->isAfter($shiftMorning['end'])){
                if($carbonCheckOut->isBefore($shiftMorning['end'])){
                    return $carbonCheckOut->diffInMinutes($carbonCheckIn);
                } else {
                    return $carbonCheckIn->diffInMinutes($shiftMorning['end']);
                }
            }
        } else {
            if($carbonCheckIn->isAfter($shiftAfternoon['start'])){
                return $carbonCheckOut->diffInMinutes($carbonCheckIn);
            } else if ($carbonCheckIn->isBefore($shiftMorning['end'])){
                return $carbonCheckOut->diffInMinutes($carbonCheckIn) - 75;
            } else {
                return $carbonCheckOut->diffInMinutes($shiftAfternoon['start']);
            }
        }
        return 0;
    }

    public static function calcOvertimeWorking($carbonCheckIn, $carbonCheckOut, $shiftMorning, $shiftAfternoon){
        $result = 0;
        if($carbonCheckIn->isBefore($shiftMorning['start'])){
            if($carbonCheckOut->isBefore($shiftMorning['start'])){
                $result += $carbonCheckIn->diffInMinutes($carbonCheckOut);
            } else {
                $result += $carbonCheckIn->diffInMinutes($shiftMorning['start']);
            }
        }
        if($carbonCheckOut->isAfter($shiftAfternoon['end'])){
            if($carbonCheckIn->isAfter($shiftAfternoon['end'])){
                $result += $carbonCheckOut->diffInMinutes($carbonCheckIn);
            } else {
                $result += $carbonCheckOut->diffInMinutes($shiftAfternoon['end']);
            }
        }
        return $result;
    }

    public static function handleStatisticSingleUser(
        TimeKeepingRepositoryInterface $timeKeepingRepository,
        SystemtimeRepositoryInterface $systemtimeRepository,
        DepartmentRepositoryInterface $departmentRepository,
        $from,
        $to,
        $user,
        $shiftMorning,
        $shiftAfternoon
    )
    {
        $timeKeepings = $timeKeepingRepository->getByDateBetweenAndOrderByDesc($from, $to, $user->id);

        $sumWorkingDays = 0;

        $sumWorkingHours = 0;
        $sumWorkingMinutes = 0;

        $scheduledWorkingHours = 0;
        $scheduledWorkingMinutes = 0;

        $overtimeWorkingHours = 0;
        $overtimeWorkingMinutes = 0;

        $averageWorkingHours = 0;
        $lateDays = 0;
        $remoteDays = 0;
        $leaveDays = 0;

        foreach ($timeKeepings as $timeKeeping) {
            $systemtimes = $systemtimeRepository->find($timeKeeping->id);

            if ($systemtimes->time_check_in !== '00:00:00') {
                $sumWorkingDays += 1;
                if (Carbon::createFromFormat('H:i:s', $systemtimes->time_check_in)
                    ->isAfter(Carbon::createFromFormat('H:i:s', '08:30:00'))
                ) {
                    $lateDays += 1;
                }
            }

            if($timeKeeping->status_am == 1){
                $remoteDays += 0.5;
            } else if($timeKeeping->status_am == 2){
                $leaveDays += 0.5;
            }
            if($timeKeeping->status_pm == 1){
                $remoteDays += 0.5;
            } else if($timeKeeping->status_pm == 2){
                $leaveDays += 0.5;
            }

            if ($systemtimes->time_check_in != '00:00:00' && $systemtimes->time_check_out != '00:00:00') {
                $carbonCheckIn = Carbon::createFromFormat('H:i:s', $systemtimes->time_check_in);
                $carbonCheckOut = Carbon::createFromFormat('H:i:s', $systemtimes->time_check_out);

                $sumWorkingMinutes += self::calcSumWorkingMinutes($carbonCheckIn, $carbonCheckOut, $shiftMorning, $shiftAfternoon);
                $overtimeWorkingMinutes += self::calcOvertimeWorking($carbonCheckIn, $carbonCheckOut, $shiftMorning, $shiftAfternoon);
            }
        }
        $scheduledWorkingMinutes = $sumWorkingMinutes - $overtimeWorkingMinutes;

        $sumWorkingHours += intdiv($sumWorkingMinutes, 60);
        $sumWorkingMinutes %= 60;

        $scheduledWorkingHours += intdiv($scheduledWorkingMinutes, 60);
        $scheduledWorkingMinutes %= 60;

        $overtimeWorkingHours += intdiv($overtimeWorkingMinutes, 60);
        $overtimeWorkingMinutes %= 60;

        if ($sumWorkingDays != 0) {
            $sumWorkingByMinutes = intdiv($sumWorkingHours * 60 + $sumWorkingMinutes, $sumWorkingDays);
            $averageWorkingHours = ($sumWorkingByMinutes < 60 ? '00' : str_pad(intdiv($sumWorkingByMinutes, 60), 2, '0', STR_PAD_LEFT))
                . ':'
                . str_pad($sumWorkingByMinutes % 60, 2, '0', STR_PAD_LEFT);
        } else {
            $averageWorkingHours = '00:00';
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'sumWorkingDays' => $sumWorkingDays.' days',
            'sumWorkingTime' => self::formatTime($sumWorkingHours, $sumWorkingMinutes),
            'scheduledWorkingTime' => self::formatTime($scheduledWorkingHours, $scheduledWorkingMinutes),
            'overtimeWorkingTime' => self::formatTime($overtimeWorkingHours, $overtimeWorkingMinutes),
            'averageWorkingHours' => $averageWorkingHours,
            'lateDays' => $lateDays.' days',
            'remoteDays' => $remoteDays.' days',
            'leaveDays' => $leaveDays.' days',
            'department' => $departmentRepository->selectByMultiKeys(['id' => $user->department_id])->first()->department_name,
        ];
    }

    public function timeKeepingStatistic($skip, $request)
    {
        $pageSize = 10;
        if($request->limit){
            $pageSize = $request->limit;
        }
        $from = $request->from;
        $to = $request->to;
        $name = $request->name;
        $department = $request->department;
        $count_user = 0;
        $shiftMorning = [
            'start' => TimeByShift::carbonize(TimeByShift::MORNING['start']),
            'end' => TimeByShift::carbonize(TimeByShift::MORNING['end']),
        ];
        $shiftAfternoon = [
            'start' => TimeByShift::carbonize(TimeByShift::AFTERNOON['start']),
            'end' => TimeByShift::carbonize(TimeByShift::AFTERNOON['end']),
        ];
        $regularWorkingDays = self::getRegularDaysBetween2Days($from, $to);

        if ($department) {
            if ($name) {
                $users = $this->userRepository->getModelByMultiKeys([
                    ['name', SQLOperator::LIKE, '%' . $name . '%'],
                    ['department_id', SQLOperator::EQUAL, $department]
                ]);
            } else {
                $users = $this->userRepository->getModelByMultiKeys([
                   ['department_id', SQLOperator::EQUAL, $department],
                ]);
            }
            $count_user = count($users->get());
            $users = $users->limit($pageSize)
                ->offset($skip * $pageSize)
                ->get();
        } else {
            if ($name) {
                $users = $this->userRepository->getModelByMultiKeys([
                   ['name', SQLOperator::LIKE, '%' . $name . '%']
                ]);
                $count_user = count($users->get());
                $users = $this->userRepository->selectLimit($skip, $pageSize, $users);
            } else {
                $count_user = User::count();
                $users = User::limit($pageSize)->offset($skip * $pageSize)->get(['id', 'name', 'department_id']);
            }
        }
        $result = [];
        foreach ($users as $user) {
            $result[] = array_merge(
                self::handleStatisticSingleUser(
                    $this->timeKeepingRepository,
                    $this->systemtimeRepository,
                    $this->departmentRepository,
                    $from,
                    $to,
                    $user,
                    $shiftMorning,
                    $shiftAfternoon),
                [
                    'regularWorkingDays' => $regularWorkingDays.' days',
                    'regularWorkingTime' => ($regularWorkingDays * 8).':00',
                ]
            );
        }
        return [
            'count_user' => $count_user,
            'result' => $result,
        ];
    }

    /**
     * @param $fromMonth
     * @param $toMonth
     * @return mixed
     */
    public function getByWaitingAccept($fromMonth, $toMonth): mixed
    {
        return $this->timeKeepingRepository->selectByMultiKeys([
            ['_date', SQLOperator::BETWEEN, [$fromMonth, $toMonth]],
            ['admin_accept_status', SQLOperator::EQUAL, 1],
            ['admin_accept_time', SQLOperator::OR_EQUAL, 1]
        ]);
    }
}
