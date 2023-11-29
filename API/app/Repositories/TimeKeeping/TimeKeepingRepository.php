<?php

namespace App\Repositories\TimeKeeping;

use App\Common\Role;
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
            if ($dayOfWeek == 5 || $dayOfWeek == 6) {
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

        if ($timekeep) {
            $need_request_status = ($status_am !== 0 && $status_am !== $timekeep->status_am)
                || ($status_pm !== 0 && $status_pm !== $timekeep->status_pm);
            //Situation: User change status to default (0, 0)
            $default_status = $status_am == 0 && $status_pm == 0;
            $need_request_time = ($checkin && $checkin->format('H:i:s') !== $timekeep->time_check_in)
                || ($checkout && $checkout->format('H:i:s') !== $timekeep->time_check_out);

            DB::table('time_keepings')->where('user_id', '=', $request->user_id)
                ->where('_date', '=', $date)
                ->update([
                    'time_check_in' => $checkin ? $checkin->format('H:i:s') : $timekeep->time_check_in,
                    'time_check_out' => $checkout ? $checkout->format('H:i:s') : $timekeep->time_check_out,
                    'status_am' => $status_am >= 0 ? $status_am : $timekeep->status_am,
                    'status_pm' => $status_pm >= 0 ? $status_pm : $timekeep->status_pm,
                    'admin_accept_status' => $default_status ? null : ($need_request_status ? 0 : $timekeep->admin_accept_status),
                    'admin_accept_time' => $need_request_time ? 0 : $timekeep->admin_accept_time,
                ]);


            $shifts = Shift::all();
            if ($timekeep->time_check_in && $checkout) {
                $timekeep_checkin = Carbon::createFromFormat('H:i:s', $timekeep->time_check_in);
                foreach ($shifts as $shift) {
                    if (
                        $timekeep_checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                        $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                    ) {
                        DB::table('time_keepings')->where('user_id', '=', $request->user_id)
                            ->where('_date', '=', $date)->update(['shift_id' => $shift->id]);
                        break;
                    }
                }
            } else if ($timekeep->time_check_out && $checkin) {
                $timekeep_checkout = Carbon::createFromFormat('H:i:s', $timekeep->time_check_out);
                foreach ($shifts as $shift) {
                    if (
                        $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                        $timekeep_checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                    ) {
                        DB::table('time_keepings')->where('user_id', '=', $request->user_id)
                            ->where('_date', '=', $date)->update(['shift_id' => $shift->id]);
                        break;
                    }
                }
            }
        } else {
            $newtimekeep = DB::table('time_keepings')->insertGetId([
                'user_id' => $request->user_id,
                '_date' => $date,
                'time_check_in' => $checkin ? $checkin->format('H:i:s') : null,
                'time_check_out' => $checkout ? $checkout->format('H:i:s') : null,
                'status_am' => $status_am ?: 0,
                'status_pm' => $status_pm ?: 0,
                'admin_accept_status' => ($status_am || $status_pm) ? 0 : null,
                'admin_accept_time' => ($checkin || $checkout) ? 0 : null,
            ]);
            $check = DB::table('systemtimes')->insert([
                'id' => $newtimekeep,
                '_date' => $date,
                'time_check_in' => '00:00:00',
                'time_check_out' => '00:00:00',
            ]);
            if (!$check) {
                return response()->json(['error' => 'Cannot create systemtime'], 400);
            }
            if ($checkin && $checkout) {
                $shifts = Shift::all();
                foreach ($shifts as $shift) {
                    if (
                        $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                        $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                    ) {
                        DB::table('time_keepings')->where('id', '=', $newtimekeep)->update(['shift_id' => $shift->id]);
                        break;
                    }
                }
            }
        }
    }

    public function timeKeepingStatistic($skip, $request)
    {
        $from = $request->from;
        $to = $request->to;
        $name = $request->name;
        $department = $request->department;
        $count_user = 0;
        if ($department) {
            if ($name) {
                $users = DB::table('users')
                    ->where('name', 'like', '%' . $name . '%')
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
            if ($name) {
                $users = DB::table('users')
                    ->where('name', 'like', '%' . $name . '%');
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
        foreach ($users as $user) {
            $timeKeepings = TimeKeeping::where('user_id', $user->id)
                ->whereBetween('_date', [$from, $to])
                ->orderBy('_date', 'desc')->get();
            $sumWorkingDays = 0;
            $sumWorkingTime = '';
            $sumWorkingHours = 0;
            $sumWorkingMinutes = 0;
            $averageWorkingHours = 0;
            $lateDays = 0;
            foreach ($timeKeepings as $timeKeeping) {
                if ($timeKeeping->time_check_in) {
                    $sumWorkingDays += 1;
                    if (Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_in)
                        ->isAfter(Carbon::createFromFormat('H:i:s', '08:30:00'))
                    ) {
                        $lateDays += 1;
                    }
                }

                if ($timeKeeping->time_check_in && $timeKeeping->time_check_out) {
                    $carbonCheckIn = Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_in);
                    $carbonCheckOut = Carbon::createFromFormat('H:i:s', $timeKeeping->time_check_out);
                    $timeWorkHours = $carbonCheckOut->diffInHours($carbonCheckIn);
                    $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn) - $timeWorkHours * 60;
                    $sumWorkingHours += $timeWorkHours;
                    $sumWorkingMinutes += $timeWorkMinutes;
                }
            }
            $sumWorkingHours += intdiv($sumWorkingMinutes, 60);
            $sumWorkingMinutes = $sumWorkingMinutes % 60;

            if ($sumWorkingDays != 0) {
                $sumWorkingByMinutes = intdiv($sumWorkingHours * 60 + $sumWorkingMinutes, $sumWorkingDays);
                $averageWorkingHours = ($sumWorkingByMinutes < 60 ? '00' : str_pad(intdiv($sumWorkingByMinutes, 60), 2, '0', STR_PAD_LEFT))
                    . ':'
                    . str_pad($sumWorkingByMinutes % 60, 2, '0', STR_PAD_LEFT);
            } else {
                $averageWorkingHours = '00:00';
            }


            $sumWorkingTime = str_pad($sumWorkingHours, 2, '0', STR_PAD_LEFT)
                . ':'
                . str_pad($sumWorkingMinutes, 2, '0', STR_PAD_LEFT);

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
        return [
            'count_user' => $count_user,
            'result' => $result,
        ];
    }
}
