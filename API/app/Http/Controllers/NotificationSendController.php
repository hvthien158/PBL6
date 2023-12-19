<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Message;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Services\Department\DepartmentServiceInterface;
use App\Services\Message\MessageServiceInterface;
use App\Services\User\UserServiceInterface;
use App\Services\UserDeviceToken\UserDeviceTokenServiceInterface;
use App\Traits\PushNotificationTrait;
use App\Common\Role;

class NotificationSendController extends Controller
{

    use PushNotificationTrait;

    public function __construct(
        protected DepartmentServiceInterface $departmentService,
        protected UserServiceInterface $userService,
        protected UserDeviceTokenServiceInterface $userDeviceTokenService,
        protected MessageServiceInterface $messageService)
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
            $manager = $this->departmentService->find(auth()->user()->department_id)->department_manager_id;
            if($manager) {
                $data = array_merge($data, ['user' => auth()->user()]);
                $data = array_merge($data, ['message' => 'User: ' . auth()->user()->id . '-' . auth()->user()->name . ' was send request']);
                $deviceToken = $this->userDeviceTokenService->getListDeviceToken([$manager])->pluck('device_token')->toArray();
            }
        } else {
            $userId = $this->messageService->findMessage($dataRequest['id'])->user_id;
            $data = array_merge($data, ['message' => 'Manager: ' . auth()->user()->id . '-' . auth()->user()->name . ' was confirmed']);
            $deviceToken = $this->userDeviceTokenService->getListDeviceToken([$userId])->pluck('device_token')->toArray();
        }
        // Push notification
        $this->pushMessages($deviceToken, $content, $data);
    }
}
