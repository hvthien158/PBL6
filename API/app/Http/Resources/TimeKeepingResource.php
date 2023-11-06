<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ShiftResource;

class TimeKeepingResource extends JsonResource
{
    public function toArray($request): array
    {
        if (!$this->shift) {
            $shift = [
                'id' => 0,
                'name' => 'OFF',
                'amount' => 0,
                'TimeValidCheckIn' => '00:00:00',
                'TimeValidCheckOut' => '00:00:00',
            ];
        } else {
            $shift = new ShiftResource($this->shift);
        }

        $timezone = 'Asia/Ho_Chi_Minh';
        $timeCheckIn = '';
        $timeCheckOut = '';
        $timeWorkMinutes = 0;
        $timeWorkHours = 0;
        $timeWork = '0:00';
        if ($this->time_check_in) {
            $timeCheckIn = Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('H:i:s');
            $now = Carbon::now()->setTimezone($timezone);
        }

        if ($this->time_check_out) {
            $timeCheckOut = Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_out)->format('H:i:s');
            $carbonCheckIn = Carbon::createFromFormat('H:i:s', $timeCheckIn);
            $carbonCheckOut = Carbon::createFromFormat('H:i:s', $timeCheckOut);
            $timeWorkHours = $carbonCheckOut->diffInHours($carbonCheckIn);
            if($timeWorkHours != 0){
                $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn) - $timeWorkHours*60;
            } else {
                $timeWorkMinutes = $carbonCheckOut->diffInMinutes($carbonCheckIn);
            }
            $timeWork = $timeWorkHours.':'.$timeWorkMinutes;
        }

        return [
            'id' => $this->id,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('d-m-Y'),
            'timeCheckIn' => $timeCheckIn,
            'timeCheckOut' => $timeCheckOut,
            'timeWork' => $timeWork,
            'user' => new UserResource($this->user),
            'shift' => $shift
        ];
    }
}
