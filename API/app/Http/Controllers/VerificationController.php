<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;

class VerificationController extends Controller
{
    /**
     * @param mixed $user_id
     * @param Request $request
     * 
     * @return mixed
     */
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }

        $user = User::where('id', $user_id)->firstOrFail();

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        return Redirect::to('http://localhost:5173/login');
    }

    /**
     * @return object
     */
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
