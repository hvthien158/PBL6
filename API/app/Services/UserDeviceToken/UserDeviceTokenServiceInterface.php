<?php

namespace App\Services\UserDeviceToken;

interface UserDeviceTokenServiceInterface
{
    /**
     * Find model via token
     * @param $deviceToken
     * @return mixed
     */
    public function findByToken($deviceToken);

    /**
     * Delete model via token
     * @param $deviceToken
     * @return mixed
     */
    public function deleteToken($deviceToken);

    /**
     * Get list model in user list user id request
     * @param $listUserId
     * @return mixed
     */
    public function getListDeviceToken($listUserId = []);

    /**
     * Create new token for 1 user
     * @param array $attribute
     * @return mixed
     */
    public function createToken(array $attribute = []);
}
