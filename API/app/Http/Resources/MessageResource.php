<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('id', $this->user_id)->first();
        if($user->avatar){
            $avatar = env('STORAGE_URL').$user->avatar;
        } else {
            $avatar = 'https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg';
        }

        $timekeeping = DB::table('time_keepings')
            ->where('id', '=', $this->time_keeping_id)->first();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'time_keeping' => $timekeeping,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $avatar,
            ],
            'is_read' => $this->is_read,
            'is_check' => $this->is_check,
        ];
    }
}
