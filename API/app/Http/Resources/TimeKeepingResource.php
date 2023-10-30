<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ShiftResource;
class TimeKeepingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if(!$this->shift){
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
        return [
            'id' => $this->id,
            'date' => Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('d-m-Y'),
            'timeCheckIn' => Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_in)->format('H:i:s'),
            'timeCheckOut' => Carbon::createFromFormat('Y-m-d H:i:s', $this->time_check_out)->format('H:i:s'),
            'user' => new UserResource($this->user),
            'shift' => $shift
        ];
    }
}
