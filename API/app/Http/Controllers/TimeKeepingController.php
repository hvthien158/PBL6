<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Common\Role;
use App\Http\Requests\MonthYearRequest;
use App\Http\Requests\TimeRequest;
use App\Http\Requests\UpdateTimeKeepingRequest;
use App\Http\Resources\TimeKeepingResource;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use App\Models\User;
use DateTimeZone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Parser\Block\ParagraphParser;

class TimeKeepingController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkIn()
    {
        try {
            $timezone = 'Asia/Ho_Chi_Minh';
            $date = Carbon::now(new DateTimeZone($timezone))->format('Y-m-d');
            $now = Carbon::now(new DateTimeZone($timezone))->format('H:i:s');

            $checkTimeKeeping = TimeKeeping::where('user_id', auth()->id())
                ->whereDate('_date', Carbon::now()->setTimezone($timezone)->toDateString())->first();
            if ($checkTimeKeeping) {
                $checkTimeKeeping->time_check_in = $now;
                $checkTimeKeeping->save();

                DB::table('systemtimes')
                    ->where('id', '=', $checkTimeKeeping->id)
                    ->update(['time_check_in' => $now]);
            } else {
                $timekeep = DB::table('time_keepings')->insertGetId([
                    'user_id' => auth()->id(),
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
            return response()->json(['message' => ResponseMessage::OK]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkOut()
    {
        try {
            $timezone = 'Asia/Ho_Chi_Minh';
            $now = Carbon::now()->setTimezone($timezone);
            $checkTimeKeeping = TimeKeeping::where('user_id', auth()->id())
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
                return response()->json(['message' => ResponseMessage::OK]);
            } else {
                return response()->json(['message' => 'Invalid time checkout'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @return object
     */
    public function getTimeKeeping()
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->whereDate('_date', $currentDate)->first();
        if ($timeKeeping) {
            $systemTime = Systemtime::where('id', $timeKeeping->id)->first();
            return response()->json(
                [
                    'data' => $systemTime,
                    'status_AM' => $timeKeeping->status_am,
                    'status_PM' => $timeKeeping->status_pm
                ]
            );
        } else {
            $dayOfWeek = Carbon::now('Asia/Ho_Chi_Minh')->dayOfWeek;
            if ($dayOfWeek == 5 || $dayOfWeek == 6) {
                $status_AM = 2;
                $status_PM = 2;
                return response()->json([
                    'weekend' => 1,
                    'status_AM' => $status_AM,
                    'status_PM' => $status_PM,
                ]);
            }
            $status_AM = 0;
            $status_PM = 0;
            return response()->json([
                'status_AM' => $status_AM,
                'status_PM' => $status_PM,
            ]);
        }
    }
    /**
     * @return object
     */
    public function getListTimeKeeping($from, $to, $user_id = null)
    {
        $validator = Validator::make(['from' => $from, 'to' => $to], [
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['failed_data' => $validator->failed()], 400);
        }

        if (!$user_id) {
            $timeKeeping = TimeKeeping::where('user_id', auth()->id())
                ->whereBetween('_date', [$from, $to])
                ->orderBy('_date', 'desc')->get();
            return TimeKeepingResource::collection($timeKeeping);
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
            return TimeKeepingResource::collection($timeKeeping);
        }
    }
    /**
     * @param mixed $userId
     * @param mixed $fromMonth
     * @param mixed $toMonth
     *
     * @return object
     */
    function getTimeKeepingExport($fromMonth, $toMonth, $userId)
    {
        if ($userId && $fromMonth && $toMonth) {
            try {
                $timekeeping = TimeKeeping::where('user_id', $userId)->whereBetween('_date', [$fromMonth, $toMonth])->get();
                if (count($timekeeping) != 0) {
                    return TimeKeepingResource::collection($timekeeping);
                } else {
                    return response()->json(['message' => ResponseMessage::NOT_FOUND_ERROR], 404);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 400);
            }
        }
    }
    /**
     * @param TimeRequest $request
     *
     * @return object
     */
    public function searchByAroundTime(TimeRequest $request)
    {
        $startDate = $request->startTime;
        $endDate = $request->endTime;
        $startDateTime = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDateTime = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

        $timekeepingRecords = Timekeeping::whereBetween('_date', [$startDateTime, $endDateTime])->get();

        return TimeKeepingResource::collection($timekeepingRecords);
    }

    /**
     * @param MonthYearRequest $request
     *
     * @return object
     */
    public function searchByMonth(MonthYearRequest $request)
    {
        $month = $request->month;
        $year = $request->year;
        if ($month && $year) {
            $timekeepingRecords = Timekeeping::where('user_id', auth()->id())->whereMonth('_date', $month)->whereYear('_date', $year)->get();
        } else if ($year) {
            $timekeepingRecords = Timekeeping::where('user_id', auth()->id())->whereYear('_date', $year)->get();
        }
        return $timekeepingRecords;
    }

    public function updateTimeKeeping(UpdateTimeKeepingRequest $request)
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

        return response()->json(['message' => ResponseMessage::UPDATE_SUCCESS]);
    }
}
