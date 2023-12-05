<?php

namespace App\Repositories\UserDeviceToken;

use App\Models\UserDeviceToken;
use App\Repositories\BaseRepository;

class UserDeviceTokenRepository extends BaseRepository implements UserDeviceTokenRepositoryInterface
{

    /**
     * @return object
     */
    public function getModel()
    {
        return UserDeviceToken::class;
    }
    public function findByToken($deviceToken)
    {
        return UserDeviceToken::where('device_token', $deviceToken)->get();
    }
    public function deleteToken($deviceToken)
    {
        try {
            $token = $this->findByToken($deviceToken);
            if (isset($token[0])) {
                foreach ($token as $token) {
                    $token->delete();
                }
                return true;
            }
            return true;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function getListDeviceToken($listUserId = []) {
        return UserDeviceToken::whereIn('user_id', $listUserId)->get();
    }
}
