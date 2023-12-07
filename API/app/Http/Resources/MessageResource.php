<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
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
            $avatar = $user->avatar;
        } else {
            $avatar = 'https://thumbs.dreamstime.com/b/default-avatar-profile-icon-vector-social-media-user-image-182145777.jpg';
        }
        $create = ($this->create_at) ? Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at, 'UTC')
                ->setTimezone('Asia/Ho_Chi_Minh')->format('Y-m-d H:i:s') : '2023-02-02 05:00:00';
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
            'created_at' => $create
        ];
    }
}
