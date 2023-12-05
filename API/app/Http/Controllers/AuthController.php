<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Models\User;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Http\Resources\UserResource;
use Illuminate\Support\Str;
use App\Models\UserDeviceToken;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepository;
use App\Repositories\UserDeviceToken\UserDeviceTokenRepositoryInterface;

class AuthController extends Controller
{

    public function __construct(protected UserRepositoryInterface $userRepo, protected UserDeviceTokenRepositoryInterface $userDeviceTokenRepo)
    {
    }

    /**
     * @param LoginRequest $request
     *
     * @return object
     */
    public function login(LoginRequest $request)
    {
        $info = array_merge(['email' => $request->email], ['password' => $request->password]);

        if (!$token = auth()->attempt($info)) {
            return response()->json(['error' => ResponseMessage::AUTHORIZATION_ERROR], 401);
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            auth()->user()->sendEmailVerificationNotification();
            return response()->json(['verify_quest' => 'Please verify email'], 200);
        }
        if ($this->userDeviceTokenRepo->deleteToken($request->deviceToken)) {
            $this->userDeviceTokenRepo->create([
                'user_id' => auth()->id(),
                'device_token' => $request->deviceToken,
                'device' => 'web'
            ]);
        } 
        return $this->createNewToken($token);
    }

    /**
     * @return object
     */
    public function logout(Request $request)
    {
        $logout = $this->userDeviceTokenRepo->deleteToken($request->device_token);
        if($logout){
            auth()->logout();
            return response()->json(['message' => ResponseMessage::DELETE_SUCCESS]);
        }
        return response()->json(['message' => ResponseMessage::AUTH_ERROR]);
    }

    /**
     * @return object
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * @param string $token
     *
     * @return object
     */
    protected function createNewToken($token)
    {
        $user = $this->userRepo->find(auth()->id());
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => Carbon::now('Asia/Ho_Chi_Minh')->add('second', 3600)->toDateTimeString(),
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!Hash::check($request->old_password, auth()->user()->getAuthPassword())) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $userId = auth()->id();

        $check = $this->userRepo->updateByID($userId, [
            'password' => bcrypt($request->new_password)]
        );

        if ($check != false) {
            return response()->json([
                'message' => ResponseMessage::OK,
            ], 201);
        }
        return response()->json([
            'error' => 'Error change password',
        ], 400);
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->userRepo->find(auth()->id());
        $this->userRepo->updateProfile($user, $request);
        return response()->json([
            'message' => ResponseMessage::UPDATE_SUCCESS,
            'user' => new UserResource($user)
        ], 201);
    }
    /**
     * @param Request $request
     *
     * @return object
     */
    public function forgotPassword(EmailRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link has been sent'])
            : response()->json(['error' => $status], 400);
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function checkEmail(EmailRequest $request)
    {
        $email = $this->userRepo->checkEmail($request->input('email'));
        if ($email) {
            return response()->json(['message' => ResponseMessage::OK], 200);
        } else {
            return response()->json(['error' => ResponseMessage::NOT_FOUND_ERROR], 404);
        }
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Reset password successfully'])
            : response()->json(['error' => $status], 400);
    }
}
