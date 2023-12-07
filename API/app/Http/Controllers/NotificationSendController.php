<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Traits\PushNotificationTrait;
use App\Common\Role;

class NotificationSendController extends Controller
{

    use PushNotificationTrait;
    /**
     * @param protected $departmentRepo
     * @param protected $userRepo
     * @param protected $userDeviceTokenRepo
     * @param protected $messageRepo
     */
    public function __construct(protected $departmentRepo, protected $userRepo, protected $userDeviceTokenRepo, protected $messageRepo)
    {
    }
    /**
     * @param mixed $dataRequest
     * 
     * @return 
     */
    public function sendNotification($dataRequest)
    {
        $data = [
            'func_name' => config('firebase.notification.func'),
            'screen' => config('firebase.notification.screen'),
            'device_type' => 'web',
            "title" => $dataRequest['title'],
            "content" => $dataRequest['content'],
            'type' => $dataRequest['type']
        ];
        $content = [
            'sound' => config('firebase.sound')
        ];
        if ($dataRequest['type'] == 1) {
            $manager = $this->departmentRepo->find(auth()->user()->department_id)->department_manager_id;
            if($manager) {
                $data = array_merge($data, ['user' => auth()->user()]);
                $data = array_merge($data, ['message' => 'User: ' . auth()->user()->id . '-' . auth()->user()->name . ' was send request']);
                $deviceToken = $this->userDeviceTokenRepo->getListDeviceToken([$manager])->pluck('device_token')->toArray();
            }
        } else {
            $userId = $this->messageRepo->find($dataRequest['id'])->user_id;
            $data = array_merge($data, ['message' => 'Manager: ' . auth()->user()->id . '-' . auth()->user()->name . ' was confirmed']);
            $deviceToken = $this->userDeviceTokenRepo->getListDeviceToken([$userId])->pluck('device_token')->toArray();
        }
        // Push notification
        $this->pushMessages($deviceToken, $content, $data);
    }
}
