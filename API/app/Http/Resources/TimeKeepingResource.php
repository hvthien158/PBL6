<?php

namespace App\Http\Resources;

use App\Models\Systemtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ShiftResource;
use Illuminate\Support\Facades\DB;

class TimeKeepingResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this->shift) {
            $shift = [
                'id' => 0,
                'name' => 'OFF',
                'amount' => 0,
                'TimeValidCheckIn' => '00:00/00:00',
                'TimeValidCheckOut' => '00:00/00:00',
            ];
        } else {
            $shift = new ShiftResource($this->shift);
        }

        $timezone = 'Asia/Ho_Chi_Minh';
        $timeCheckIn = '';
        $timeCheckOut = '';
        $systemCheckIn = '';
        $systemCheckOut = '';
        $timeWork = '00:00';
        $weekMap = [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
        $systemCheck = DB::table('systemtimes')->where('id', '=', $this->id)->first();
        if ($this->time_check_in) {
            $timeCheckIn = Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('H:i');
            $systemCheckIn = Carbon::createFromFormat('Y-m-d H:i:s', $systemCheck->time_check_in)->format('H:i');
        }

        if ($this->time_check_out) {
            $timeCheckOut = Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_out)->format('H:i');
            $systemCheckOut = Carbon::createFromFormat('Y-m-d H:i:s', $systemCheck->time_check_out)->format('H:i');

            $carbonCheckIn = Carbon::createFromFormat('H:i', $timeCheckIn);
            $carbonCheckOut = Carbon::createFromFormat('H:i', $timeCheckOut);
            $timeWorkHours = $carbonCheckOut->diffInHours($carbonCheckIn);
            if($timeWorkHours != 0){
                $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn) - $timeWorkHours*60;
            } else {
                $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn);
            }
            $timeWork = str_pad($timeWorkHours, 2, '0', STR_PAD_LEFT).':'.str_pad($timeWorkMinutes, 2, '0', STR_PAD_LEFT);
        }
        return [
            'id' => $this->id,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('d/m/Y'),
            'dayOfWeek' => $weekMap[Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->dayOfWeek],
            'timeCheckIn' => $timeCheckIn.' ('.$systemCheckIn.')',
            'timeCheckOut' => $timeCheckOut ? $timeCheckOut.' ('.$systemCheckOut.')' : '',
            'timeWork' => $timeWork,
            'user' => new UserResource($this->user),
            'shift' => $shift
        ];
    }
}
