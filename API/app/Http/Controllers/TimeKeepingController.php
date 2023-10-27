<?php

namespace App\Http\Controllers;

use App\Http\Requests\DateTimeRequest;
use App\Models\TimeKeeping;
use Carbon\Carbon;

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
    public function checkOut(DateTimeRequest $request, TimeKeeping $timeKeeping)
    {
        try {
            if ($this->authorize('update', $timeKeeping)) {
                $timezone = 'Asia/Ho_Chi_Minh';
                $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
                $checkout = TimeKeeping::where('user_id', auth()->id())
                    ->whereDate('time_check_in', $currentDate)->first();
                if ($checkout && Carbon::createFromFormat('Y-m-d H:i:s', $request->time, $timezone)->isAfter($checkout->time_check_in)) {
                    $checkout->time_check_out = $request->time;
                    $checkout->save();
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