<?php

namespace App\Http\Controllers;

use App\Common\ResponseMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\GoogleDriveController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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

        return $this->createNewToken($token);
    }

    /**
     * @param Request $request
     *
     * @return object
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create(array_merge(
            $request->validated(),
            ['password' => bcrypt($request->password)]
        ));


        return response()->json([
            'message' => ResponseMessage::CREATE_SUCCESS,
            'user' => $user
        ], 201);
    }

    /**
     * @return object
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => ResponseMessage::OK]);
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
        $user = User::where('id', auth()->id())->get();
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => Carbon::now('Asia/Ho_Chi_Minh')->add('second', 3600)->toDateTimeString(),
            'user' => UserResource::collection($user)
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

        $userId = auth()->user()->id;

        $check = User::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );

        if ($check == 1) {
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
        $user = User::where('id', auth()->id());
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $googleDriver = new GoogleDriveController;
            $path = $googleDriver->googleDriveFileUpload($avatar);
            $user->update(array_merge($request->validated(), ['avatar' => $path]));
        } else {
            $user->update(array_merge($request->validated()));
        }
        $user = User::where('id', auth()->id())->get();
        return response()->json([
            'message' => ResponseMessage::UPDATE_SUCCESS,
            'user' => UserResource::collection($user)
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
        $email = DB::table('users')->where('email', '=', $request->input('email'))->first();
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
