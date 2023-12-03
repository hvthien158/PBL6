<?php

namespace App\Traits;

use App\Providers\FCMService;

trait PushNotificationTrait
{
    public function pushMessage(string $deviceToken, array $notification, array $data)
    {
        $pushNotificationService = new FCMService();
        
        return $pushNotificationService->send($deviceToken, $notification, $data);
    }

    public function pushMessages(array $deviceTokens, array $notification, array $data)
    {
        $pushNotificationService = new FCMService();
        return $pushNotificationService->sendMultiple($deviceTokens, $notification, $data);
    }
}