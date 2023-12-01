<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeKeepingResource;
use App\Models\Department;
use App\Models\TimeKeeping;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Traits\PushNotificationTrait;

class NotificationSendController extends Controller
{

    use PushNotificationTrait;

    public function sendNotification($dataRequest)
    {
        $manager = Department::find(auth()->user()->department_id)->department_manager_id;
        $deviceToken = UserDeviceToken::where('user_id', $manager)->pluck('device_token')->toArray();
        $timekeeping = TimeKeeping::where('id', $dataRequest['time_keeping_id'])->get();
        $data = [
            'func_name' => config('firebase.notification.func'),
            'screen' => config('firebase.notification.screen'),
            'device_type' => 'web',
            "title" => $dataRequest['title'], 
            "content" => $dataRequest['content'], 
            'user' => auth()->user(), 
            'time_keeping' => TimeKeepingResource::collection($timekeeping),
        ];
        $content = [
            'sound' => config('firebase.sound') 
        ];
        // Push notification
        $this->pushMessages($deviceToken, $content, $data);
    }
}
