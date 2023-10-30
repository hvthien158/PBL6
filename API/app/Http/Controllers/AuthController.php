<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Laravel\Prompts\table;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'forgotPassword', 'resetPassword']]);
    }

    public function login(LoginRequest $request)
    {
        $info = array_merge(['email' => $request->email], ['password' => $request->password]);

        if (!$token = auth()->attempt($info)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            auth()->user()->sendEmailVerificationNotification();
            return response()->json(['verify_quest' => 'Please verify email'],);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'department_id' => 'required',
            'address' => 'string|nullable',
            'DOB' => 'nullable|date',
            'phone_number' => 'nullable',
            'avatar' => 'nullable',
            'salary' => 'nullable',
            'position' => 'nullable',
            'role' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));


        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => Carbon::now('Asia/Ho_Chi_Minh')->add('second', 3600)->toDateTimeString(),
            'user' => auth()->user()
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }

    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'address' => 'string|nullable',
            'DOB' => 'nullable',
            'phone_number' => 'nullable',
            'avatar' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = DB::table('users')->where('id', '=', auth()->id());
        if($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $path = Storage::disk('public')->put('avatar/'.auth()->id(), $avatar);
            $user->update(array_merge($validator->validated(), ['avatar' => $path]));
        } else {
            $user->update(array_merge($validator->validated()));
        }

        return response()->json([
            'message' => 'Successfully updated profile',
            'user' => $user->get()
        ], 201);
    }

    public function forgotPassword(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link has been sent'])
            : response()->json(['error' => $status], 400);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

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
