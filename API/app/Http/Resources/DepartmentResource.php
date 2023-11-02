<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->department_name,
            'address' => $this->address,
            'email' => $this->email,
            'phoneNumber' => $this->phone_number,
            'quantityUser' => User::where('department_id',$this->id)->count(),
        ];
    }
}
