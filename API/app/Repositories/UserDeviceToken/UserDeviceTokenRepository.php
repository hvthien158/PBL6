<?php

namespace App\Repositories\UserDeviceToken;

use App\Models\UserDeviceToken;
use App\Repositories\BaseRepository;

class UserDeviceTokenRepository extends BaseRepository implements UserDeviceTokenRepositoryInterface
{
    public function getModel()
    {
        return UserDeviceToken::class;
    }
}
