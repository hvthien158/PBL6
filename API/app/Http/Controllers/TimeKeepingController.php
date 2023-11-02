<?php

namespace App\Http\Controllers;

use App\Http\Requests\DateTimeRequest;
use App\Http\Requests\MonthYearRequest;
use App\Http\Requests\TimeRequest;
use App\Http\Resources\TimeKeepingResource;
use App\Models\TimeKeeping;
use DateTimeZone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;

class TimeKeepingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function checkIn()
    {
        try {
            $timezone = 'Asia/Ho_Chi_Minh';
            $today = Carbon::now(new DateTimeZone($timezone));
            if ($this->authorize('create', TimeKeeping::class)) {
                TimeKeeping::create([
                    'user_id' => auth()->id(),
                    'time_check_in' => $today
                ]);
                return response()->json(['message' => 'Check In Thành Công']);
            }
            return response()->json(['error' => 'No role'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function checkOut(TimeKeeping $timeKeeping)
    {
        try {
            if ($this->authorize('update', $timeKeeping)) {
                $timezone = 'Asia/Ho_Chi_Minh';
                $currentDate = Carbon::now()->setTimezone($timezone);
                $checkTimeKeeping = TimeKeeping::where('user_id', auth()->id())
                    ->whereDate('time_check_in', Carbon::now()->setTimezone($timezone)->toDateString())->first();
                if ($checkTimeKeeping && $currentDate->isAfter($checkTimeKeeping->time_check_in)) {
                    $checkTimeKeeping->time_check_out = $currentDate;
                    $checkTimeKeeping->save();
                    $shift = Shift::orderBy('amount', 'desc')->get();
                    $timeCheckIn = Carbon::createFromFormat('Y-m-d H:i:s', $checkTimeKeeping->time_check_in, $timezone);
                    $timeCheckOut = Carbon::createFromFormat('Y-m-d H:i:s', $checkTimeKeeping->time_check_out, $timezone);
                    foreach ($shift as $shift) {
                        if (
                            $timeCheckIn->isBefore($shift->time_valid_check_in)
                            && $timeCheckOut->isAfter($shift->time_valid_check_out)
                        ) {
                            $checkTimeKeeping->shift_id = $shift->id;
                            $checkTimeKeeping->save();
                            break;
                        }
                    }
                    return response()->json(['message' => 'Check Out Thành Công']);
                } else {
                    return response()->json(['message' => 'Thời gian checkout không hợp lệ'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function getTimeKeeping()
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->whereDate('time_check_in', $currentDate)->first();
        return response()->json($timeKeeping);
    }
    public function getListTimeKeeping()
    {
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->orderBy('time_check_in', 'desc')->get();
        return TimeKeepingResource::collection($timeKeeping);
    }
    public function searchByAroundTime(TimeRequest $request)
    {
        $startDate = $request->startTime;
        $endDate = $request->endTime;
        $startDateTime = date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00'));
        $endDateTime = date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59'));

        $timekeepingRecords = Timekeeping::whereBetween('time_check_in', [$startDateTime, $endDateTime])->get();

        return TimeKeepingResource::collection($timekeepingRecords);
    }
    public function searchByMonth(MonthYearRequest $request)
    {
        $month = $request->month;
        $year = $request->year;
        if ($month) {
            $timekeepingRecords = Timekeeping::whereMonth('time_check_in', $month)->whereYear('time_check_in', $year)->get();
        } else {
            $timekeepingRecords = Timekeeping::whereYear('time_check_in', $year)->get();
        }
        return TimeKeepingResource::collection($timekeepingRecords);
    }
}
