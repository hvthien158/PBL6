<?php

namespace App\Repositories\UserDeviceToken;

use App\Repositories\RepositoryInterface;

interface UserDeviceTokenRepositoryInterface extends RepositoryInterface
{
    public function findByToken($deviceToken);
    public function deleteToken($deviceToken);
    public function getListDeviceToken($listUserId = []);
}
