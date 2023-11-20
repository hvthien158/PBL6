<?php

namespace App\Http\Resources;

use App\Common\Role;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

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

        $timezone = 'Asia/Ho_Chi_Minh';
        $now = Carbon::now($timezone)->format('Y-m-d');
        $dayOfWeek = Carbon::now()->dayOfWeek;

        $status = DB::table('time_keepings')
            ->where('user_id', $this->id)
            ->where('_date', '=', $now)
            ->first();
        if($status){
            $status_AM = $status->status_am;
            $status_PM = $status->status_pm;
        } else {
            if($dayOfWeek == 6 || $dayOfWeek == 7){
                $status_AM = 2;
                $status_PM = 2;
            } else {
                $status_AM = 0;
                $status_PM = 0;
            }
        }

        return [
            'id' => $this->id,
            'name'=> $this->name,
            'email'=> $this->email,
            'avatar'=> $avatar,
            'address' => $this->address ?: 'none',
            'DOB' => $DOB_format,
            'role' => $this->role ?: Role::USER,
            'phone_number' => $this->phone_number ?: 'none',
            'salary' => '$'.$this->salary ?: 'none',
            'position' => $this->position ?: 'none',
            'department' => new DepartmentResource($this->department),
            'status_AM' => $status_AM,
            'status_PM' => $status_PM,
        ];
    }
}
