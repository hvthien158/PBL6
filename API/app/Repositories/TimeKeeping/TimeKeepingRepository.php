<?php

namespace App\Repositories\TimeKeeping;

use App\Common\ResponseMessage;
use App\Common\Role;
use App\Common\TimeByShift;
use App\Http\Resources\TimeKeepingResource;
use App\Models\Department;
use App\Models\Shift;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\RepositoryInterface;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class TimeKeepingRepository extends BaseRepository implements TimeKeepingRepositoryInterface
{

    public function getModel()
    {
        return TimeKeeping::class;
    }

    /**
     * @param $userID
     * @return \Illuminate\Http\JsonResponse|void
     * @throws \Exception
     */
    public function checkIn($userID)
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $date = Carbon::now(new DateTimeZone($timezone))->format('Y-m-d');
        $now = Carbon::now(new DateTimeZone($timezone))->format('H:i:s');

        $checkTimeKeeping = TimeKeeping::where('user_id', $userID)
            ->whereDate('_date', Carbon::now()->setTimezone($timezone)->toDateString())->first();
        if ($checkTimeKeeping) {
            $checkTimeKeeping->time_check_in = $now;
            $checkTimeKeeping->save();

            DB::table('systemtimes')
                ->where('id', '=', $checkTimeKeeping->id)
                ->update(['time_check_in' => $now]);
        } else {
            $timekeep = DB::table('time_keepings')->insertGetId([
                'user_id' => $userID,
                '_date' => $date,
                'time_check_in' => $now
            ]);
            $check = DB::table('systemtimes')->insert([
                'id' => $timekeep,
                '_date' => $date,
                'time_check_in' => $now
            ]);
            if (!$check) {
                return response()->json(['message' => 'Cannot create system time'], 400);
            }
        }
    }

    public function checkOut($userID)
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now()->setTimezone($timezone);
        $checkTimeKeeping = TimeKeeping::where('user_id', $userID)
            ->whereDate('_date', $now->toDateString())->first();
        if ($checkTimeKeeping && $now->isAfter($checkTimeKeeping->_date)) {
            $checkTimeKeeping->time_check_out = $now->toTimeString();
            $checkTimeKeeping->save();

            DB::table('systemtimes')
                ->where('id', '=', $checkTimeKeeping->id)
                ->update([
                    'time_check_out' => $now->format('H:i:s')
                ]);

            $shifts = Shift::all();
            $timeCheckIn = Carbon::createFromFormat('H:i:s', $checkTimeKeeping->time_check_in, $timezone);
            $timeCheckOut = Carbon::createFromFormat('H:i:s', $checkTimeKeeping->time_check_out, $timezone);
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

    public function getTimeKeepingToday()
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->whereDate('_date', $currentDate)->first();
        if ($timeKeeping) {
            $systemTime = Systemtime::where('id', $timeKeeping->id)->first();
            return [
                'data' => $systemTime,
                'status_AM' => $timeKeeping->status_am,
                'status_PM' => $timeKeeping->status_pm
            ];
        } else {
            $dayOfWeek = Carbon::now('Asia/Ho_Chi_Minh')->dayOfWeek;
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

    public function getListTimeKeepingFiltered($from, $to, $role, $user_id = null)
    {
        if (!$user_id) {
            $timeKeeping = TimeKeeping::where('user_id', auth()->id())
                ->whereBetween('_date', [$from, $to])
                ->orderBy('_date', 'desc')->get();
            return $timeKeeping;
        }
        if (auth()->user()->role == Role::ADMIN) {
            $timeKeeping = TimeKeeping::where('user_id', $user_id)
                ->whereBetween('_date', [$from, $to])
                ->orderBy('_date', 'desc')->get();
            if (count($timeKeeping) == 0) {
                return response()->json([
                    'data' => [
                        [
                            'user' => DB::table('users')
                                ->where('id', '=', $user_id)
                                ->first()->name
                        ]
                    ]
                ]);
            }
            return $timeKeeping;
        }
        return [];
    }

    public function getListTimeKeepingBetween($userID, $fromMonth, $toMonth)
    {
        return TimeKeeping::where('user_id', $userID)->whereBetween('_date', [$fromMonth, $toMonth])->get();
    }

    public function getListTimeKeepingAround($request)
    {
        $startDate = $request->startTime;
        $endDate = $request->endTime;
        $startDateTime = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDateTime = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

        return Timekeeping::whereBetween('_date', [$startDateTime, $endDateTime])->get();
    }

    public function getListTimeKeepingInMonth($request)
    {
        $month = $request->month;
        $year = $request->year;
        if ($month && $year) {
            return Timekeeping::where('user_id', auth()->id())->whereMonth('_date', $month)->whereYear('_date', $year)->get();
        } else if ($year) {
            return Timekeeping::where('user_id', auth()->id())->whereYear('_date', $year)->get();
        }
        return [];
    }

    public static function handleUpdate($status_am, $status_pm, $checkin, $checkout, $timekeep, $date, $userID){
        $need_request_status = ($status_am !== 0 && $status_am !== $timekeep->status_am)
            || ($status_pm !== 0 && $status_pm !== $timekeep->status_pm);
        //Situation: User change status to default (0, 0)
        $default_status = $status_am == 0 && $status_pm == 0;
        $need_request_time = ($checkin && $checkin->format('H:i:s') !== $timekeep->time_check_in)
            || ($checkout && $checkout->format('H:i:s') !== $timekeep->time_check_out);

        DB::table('time_keepings')->where('user_id', '=', $userID)
            ->where('_date', '=', $date)
            ->update([
                'time_check_in' => $checkin ? $checkin->format('H:i:s') : $timekeep->time_check_in,
                'time_check_out' => $checkout ? $checkout->format('H:i:s') : $timekeep->time_check_out,
                'status_am' => $status_am >= 0 ? $status_am : $timekeep->status_am,
                'status_pm' => $status_pm >= 0 ? $status_pm : $timekeep->status_pm,
                'admin_accept_status' => $default_status ? null : ($need_request_status ? 0 : $timekeep->admin_accept_status),
                'admin_accept_time' => $need_request_time ? 0 : $timekeep->admin_accept_time,
            ]);
    }

    public static function handleUpdateShift($checkin, $checkout, $date, $userID){
        $shifts = Shift::all();
        foreach ($shifts as $shift) {
            if (
                $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
            ) {
                DB::table('time_keepings')->where('user_id', '=', $userID)
                    ->where('_date', '=', $date)->update(['shift_id' => $shift->id]);
                break;
            }
        }
    }

    public static function handleCreate($status_am, $status_pm, $checkin, $checkout, $date, $userID){
        $newtimekeep = DB::table('time_keepings')->insertGetId([
            'user_id' => $userID,
            '_date' => $date,
            'time_check_in' => $checkin ? $checkin->format('H:i:s') : null,
            'time_check_out' => $checkout ? $checkout->format('H:i:s') : null,
            'status_am' => $status_am ?: 0,
            'status_pm' => $status_pm ?: 0,
            'admin_accept_status' => ($status_am || $status_pm) ? 0 : null,
            'admin_accept_time' => ($checkin || $checkout) ? 0 : null,
        ]);
        return DB::table('systemtimes')->insert([
            'id' => $newtimekeep,
            '_date' => $date,
            'time_check_in' => '00:00:00',
            'time_check_out' => '00:00:00',
        ]);
    }

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
            ->where('_date', '=', $date)->first();

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
            self::handleUpdate($status_am, $status_pm, $checkin, $checkout, $timekeep, $date, $request->user_id);
            if ($timekeep->time_check_in && $checkout) {
                $timekeep_checkin = Carbon::createFromFormat('H:i:s', $timekeep->time_check_in);
                self::handleUpdateShift($timekeep_checkin, $checkout, $date, $request->user_id);
            } else if ($timekeep->time_check_out && $checkin) {
                $timekeep_checkout = Carbon::createFromFormat('H:i:s', $timekeep->time_check_out);
                self::handleUpdateShift($checkin, $timekeep_checkout, $date, $request->user_id);
            }
        } else {
            if (!self::handleCreate($status_am, $status_pm, $checkin, $checkout, $date, $request->user_id)) {
                return response()->json(['error' => 'Cannot create systemtime'], 400);
            }
            if ($checkin && $checkout) {
                self::handleUpdateShift($checkin, $checkout, $date, $request->user_id);
            }
        }
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

    public static function handleStatisticSingleUser($from, $to, $user, $shiftMorning, $shiftAfternoon){
        $timeKeepings = TimeKeeping::where('user_id', $user->id)
            ->whereBetween('_date', [$from, $to])
            ->orderBy('_date', 'desc')->get();

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
            $systemtimes = DB::table('systemtimes')->find($timeKeeping->id);

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
            'department' => Department::where('id', $user->department_id)->first()->department_name,
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
                $users = DB::table('users')
                    ->where('name', 'like', '%' . $name . '%')
                    ->where('department_id', '=', $department);
                $count_user = count($users->get());
                $users = $users->limit($pageSize)
                    ->offset($skip * $pageSize)
                    ->get();
            } else {
                $users = DB::table('users')
                    ->where('department_id', '=', $department);
                $count_user = count($users->get());
                $users = $users
                    ->limit($pageSize)
                    ->offset($skip * $pageSize)
                    ->get();
            }
        } else {
            if ($name) {
                $users = DB::table('users')
                    ->where('name', 'like', '%' . $name . '%');
                $count_user = count($users->get());
                $users = $users
                    ->limit($pageSize)
                    ->offset($skip * $pageSize)
                    ->get();
            } else {
                $count_user = User::count();
                $users = User::limit($pageSize)->offset($skip * $pageSize)->get(['id', 'name', 'department_id']);
            }
        }
        $result = [];
        foreach ($users as $user) {
            $result[] = array_merge(
                self::handleStatisticSingleUser($from, $to, $user, $shiftMorning, $shiftAfternoon),
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
     * @param mixed $fromMonth
     * @param mixed $toMonth
     *
     * @return object
     */
    public function findTimeKeepingWaitingAccept($fromMonth, $toMonth)
    {
        return TimeKeeping::whereBetween('_date', [$fromMonth, $toMonth])
            ->where('admin_accept_status', 1)
            ->orWhere('admin_accept_time', 1)
            ->get();
    }
}
