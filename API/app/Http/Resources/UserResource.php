<?php

namespace App\Http\Resources;

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name'=> $this->name,
            'email'=> $this->email,
            'avatar'=> $this->avatar,
            'address' => $this->address,
            'DOB' => $this->DOB,
            'role' => $this->role,
            'phoneNumber' => $this->phone_number,
            'salary' => $this->salary,
            'position' => $this->position,
            'department' => new DepartmentResource($this->department),
        ];
    }
}
