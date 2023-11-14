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
        if($this->DOB){
            $DOB_format = $this->DOB;
        } else {
            $DOB_format = 'none';
        }
        if($this->avatar){
            $avatar = env('STORAGE_URL').$this->avatar;
        } else {
            $avatar = 'https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg';
        }
        return [
            'id' => $this->id,
            'name'=> $this->name,
            'email'=> $this->email,
            'avatar'=> $avatar,
            'address' => $this->address ?: 'none',
            'DOB' => $DOB_format,
            'role' => $this->role ?: 'user',
            'phone_number' => $this->phone_number ?: 'none',
            'salary' => $this->salary ?: 'none',
            'position' => $this->position ?: 'none',
            'department' => new DepartmentResource($this->department),  
        ];
    }
}
