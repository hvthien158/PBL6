<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'name' => $this->name,
            'timeValidCheckIn' => $this->time_valid_check_in,
            'timeValidCheckOut' => $this->time_valid_check_out
        ];
    }
}
