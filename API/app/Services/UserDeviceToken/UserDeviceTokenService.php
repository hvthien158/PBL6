<?php

namespace App\Services\UserDeviceToken;

use App\Common\SQLOperator;
use App\Models\UserDeviceToken;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;

class UserDeviceTokenService implements UserDeviceTokenServiceInterface
{
    public function __construct(protected UserDeviceTokenRepositoryInterface $userDeviceTokenRepository)
    {}

    public function findByToken($deviceToken)
    {
        return $this->userDeviceTokenRepository->selectByMultiKeys([
           ['device_token', SQLOperator::EQUAL, $deviceToken]
        ]);
    }

    public function deleteToken($deviceToken)
    {
        try {
            $tokens = $this->findByToken($deviceToken);
            if (isset($tokens[0])) {
                foreach ($tokens as $token) {
                    $this->userDeviceTokenRepository->delete($token->id);
                }
                return true;
            }
            return true;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getListDeviceToken($listUserId = []) {
        return $this->userDeviceTokenRepository->selectByMultiKeys([
           ['user_id', SQLOperator::IN, $listUserId]
        ]);
    }

    public function createToken(array $attribute = [])
    {
        $this->userDeviceTokenRepository->create($attribute);
    }
}
