<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TimeKeepingController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/update-profile', [AuthController::class, 'updateProfile']);

//Forgot password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', function (string $token) {
    return Redirect::to('http://localhost:5173/reset-pass')->with('token', $token);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')->name('password.update');

//Email Verify
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

//Time Keeping
Route::post('check-in', [TimeKeepingController::class,'checkIn']);
Route::put('check-out/', [TimeKeepingController::class,'checkOut']);
Route::get('time-keeping', [TimeKeepingController::class, 'getTimeKeeping']);
Route::get('get-list-timekeeping', [TimeKeepingController::class,'getListTimeKeeping']);
Route::post('search-by-around-time', [TimeKeepingController::class,'searchByAroundTime']);
Route::post('search-by-month-year', [TimeKeepingController::class,'searchByMonth']);

