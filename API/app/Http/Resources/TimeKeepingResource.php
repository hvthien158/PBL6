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
            $shift = 'OFF';
        } else {
            $shift = new ShiftResource($this->shift);
            $shift = $shift->name;
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
            $timeCheckIn = Carbon::createFromFormat('H:i:s', $this->time_check_in)->format('H:i');
            $systemCheckIn = Carbon::createFromFormat('H:i:s', $systemCheck->time_check_in)->format('H:i');
        }
        if ($this->time_check_out) {
            $timeCheckOut = Carbon::createFromFormat('H:i:s', $this->time_check_out)->format('H:i');
            $systemCheckOut = Carbon::createFromFormat('H:i:s', $systemCheck->time_check_out)->format('H:i');
        }

        if($timeCheckIn && $timeCheckOut){
            $carbonCheckIn = Carbon::createFromFormat('H:i', $timeCheckIn);
            $carbonCheckOut = Carbon::createFromFormat('H:i', $timeCheckOut);
            $timeWorkHours = $carbonCheckOut->diffInHours($carbonCheckIn);
            $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn) - $timeWorkHours*60;
            $timeWork = str_pad($timeWorkHours, 2, '0', STR_PAD_LEFT)
                .':'
                .str_pad($timeWorkMinutes, 2, '0', STR_PAD_LEFT);
        }
        return [
            'id' => $this->id,
            'date' => $this->_date,
            'dayOfWeek' => $weekMap[Carbon::createFromFormat('Y-m-d', $this->_date)->dayOfWeek],
            'timeCheckIn' => $timeCheckIn ? $timeCheckIn.' ('.$systemCheckIn.')' : '',
            'timeCheckOut' => $timeCheckOut ? $timeCheckOut.' ('.$systemCheckOut.')' : '',
            'timeWork' => $timeCheckOut ? $timeWork : '',
            'status_AM' => $this->status_am,
            'status_PM' => $this->status_pm,
            'user' => $this->user->name,
            'shift' => $timeCheckOut ? $shift : '',
            'adminAcceptStatus' => $this->admin_accept_status,
            'adminAcceptTime' => $this->admin_accept_time,
        ];
    }
}
