<?php

namespace App\Http\Controllers;

use App\Http\Requests\DateTimeRequest;
use App\Models\TimeKeeping;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;

class TimeKeepingController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth:api");
    }
    public function checkIn(DateTimeRequest $request)
    {
        try {
            if ($this->authorize('create', TimeKeeping::class)) {
                TimeKeeping::create([
                    'user_id' => auth()->id(),
                    'time_check_in' => $request->time
                ]);
                return response()->json(['message' => 'Check In Thành Công']);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function checkOut(Request $request, TimeKeeping $timeKeeping)
    {
        try {
            if ($this->authorize('update', $timeKeeping)) {
                $timezone = 'Asia/Ho_Chi_Minh';
                $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
                $checkTimeKeeping = TimeKeeping::where('user_id', auth()->id())
                    ->whereDate('time_check_in', $currentDate)->first();
                if ($checkTimeKeeping && Carbon::createFromFormat('Y-m-d H:i:s', $request->time, $timezone)->isAfter($checkTimeKeeping->time_check_in)) {
                    $checkTimeKeeping->time_check_out = $request->time;
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
                    return response()->json(['message' => 'Thời gian checkout không hợp lệ']);
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
}