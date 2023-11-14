<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthYearRequest;
use App\Http\Requests\TimeRequest;
use App\Http\Resources\TimeKeepingResource;
use App\Models\Systemtime;
use App\Models\TimeKeeping;
use DateTimeZone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TimeKeepingController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return object
     */
    public function checkIn()
    {
        try {
            $timezone = 'Asia/Ho_Chi_Minh';
            $today = Carbon::now(new DateTimeZone($timezone));
            if ($this->authorize('create', TimeKeeping::class)) {
                $timekeep = TimeKeeping::create([
                    'user_id' => auth()->id(),
                    'time_check_in' => $today
                ]);
                Systemtime::create([
                    'id' => $timekeep->id,
                    'time_check_in' => $today
                ]);
                return response()->json(['message' => 'Check In Thành Công']);
            }
            return response()->json(['error' => 'No role'], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * @param TimeKeeping $timeKeeping
     *
     * @return mixed
     */
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

                    DB::table('systemtimes')
                        ->where('id', '=', $checkTimeKeeping->id)
                        ->update(['time_check_out' => $currentDate]);

                    $shifts = Shift::all();
                    $timeCheckIn = Carbon::createFromFormat('Y-m-d H:i:s', $checkTimeKeeping->time_check_in, $timezone);
                    $timeCheckOut = Carbon::createFromFormat('Y-m-d H:i:s', $checkTimeKeeping->time_check_out, $timezone);
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
                    return response()->json(['message' => 'Check Out Thành Công']);
                } else {
                    return response()->json(['message' => 'Thời gian checkout không hợp lệ'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * @return object
     */
    public function getTimeKeeping()
    {
        $timezone = 'Asia/Ho_Chi_Minh';
        $currentDate = Carbon::now()->setTimezone($timezone)->toDateString();
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->whereDate('time_check_in', $currentDate)->first();
        return response()->json($timeKeeping);
    }

    /**
     * @return object
     */
    public function getListTimeKeeping()
    {
        $timeKeeping = TimeKeeping::where('user_id', auth()->id())->orderBy('time_check_in', 'desc')->get();
        return TimeKeepingResource::collection($timeKeeping);
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

        $timekeepingRecords = Timekeeping::whereBetween('time_check_in', [$startDateTime, $endDateTime])->get();

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
            $timekeepingRecords = Timekeeping::where('user_id', auth()->id())->whereMonth('time_check_in', $month)->whereYear('time_check_in', $year)->get();
        } else if($year) {
            $timekeepingRecords = Timekeeping::where('user_id', auth()->id())->whereYear('time_check_in', $year)->get();
        }
        return $timekeepingRecords;
    }

    public function updateTimeKeeping(Request $request){
        $validator = Validator::make($request->all(), [
            'date' =>'required',
            'time_check_in' => 'nullable',
            'time_check_out' => 'nullable',
        ]);

        if($validator->fails()){
            return response()->json(['data' => $validator->failed(), 'message' => 'Invalid data request'], 422);
        }

        $date = Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d');
        $checkin = Carbon::createFromFormat('H:i', $request->get('time_check_in'));
        $checkout = null;
        if($request->get('time_check_out')){
            $checkout = Carbon::createFromFormat('H:i', $request->get('time_check_out'));
        }

        $timekeep = TimeKeeping::where('user_id', '=', auth()->id())
            ->where('time_check_in', 'like', $date.'%')->first();

        if($timekeep){
            $timekeep->update(['time_check_in' =>  $date.' '.$checkin->format('H:i:s')]);

            if($request->get('time_check_out')){
                $timekeep->update(['time_check_out' => $date.' '.$checkout->format('H:i:s')]);
            }

            if($checkout){
                $shifts = Shift::all();
                foreach ($shifts as $shift) {
                    if (
                        $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                        $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                    ) {
                        $timekeep->shift_id = $shift->id;
                        $timekeep->save();
                        break;
                    }
                }
            }
        } else {
            if($checkout){
                $newtimekeep = TimeKeeping::create([
                    'user_id' => auth()->id(),
                    'time_check_in' =>  $date.' '.$checkin->format('H:i:s'),
                    'time_check_out' => $date.' '.$checkout->format('H:i:s')
                ]);
                $shifts = Shift::all();
                foreach ($shifts as $shift) {
                    if (
                        $checkin->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out) &&
                        $checkout->isBetween($shift->time_valid_check_in, $shift->time_valid_check_out)
                    ) {
                        $newtimekeep->shift_id = $shift->id;
                        $newtimekeep->save();
                        break;
                    }
                }
                Systemtime::create([
                    'id' => $newtimekeep->id,
                    'time_check_in' => $date.' 00:00:00',
                    'time_check_out' => $date.' 00:00:00',
                ]);
            } else {
                $newtimekeep = TimeKeeping::create([
                    'user_id' => auth()->id(),
                    'time_check_in' =>  $date.' '.$checkin->format('H:i:s'),
                ]);
                Systemtime::create([
                    'id' => $newtimekeep->id,
                    'time_check_in' => $date.' 00:00:00',
                    'time_check_out' => $date.' 00:00:00',
                ]);
            }
        }

        return response()->json(['message' => 'Update successfully']);
    }
}
