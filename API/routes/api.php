<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TimeKeepingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationSendController;
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

//Auth middleware
Route::middleware('auth:api')->group(function () {
    //Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    //Time keeping
    Route::middleware('checkip')->group(function () {
        Route::post('check-in', [TimeKeepingController::class, 'checkIn']);
        Route::put('check-out', [TimeKeepingController::class, 'checkOut']);
    });
    Route::get('time-keeping', [TimeKeepingController::class, 'getTimeKeeping']);
    Route::get('get-list-timekeeping/{from}/{to}', [TimeKeepingController::class, 'getListTimeKeeping']);
    Route::get('get-list-timekeeping/{from}/{to}/{user_id}', [TimeKeepingController::class, 'getListTimeKeeping']);
    Route::post('search-by-around-time', [TimeKeepingController::class, 'searchByAroundTime']);
    Route::post('search-by-month-year', [TimeKeepingController::class, 'searchByMonth']);
    Route::post('/timekeeping/update', [TimeKeepingController::class, 'updateTimeKeeping']);
    Route::get('get-timekeeping-export/{fromMonth}/{toMonth}/{userId}', [TimeKeepingController::class, 'getTimeKeepingExport']);
    Route::get('user/{id}', [UserController::class, 'user']);
    Route::get('user/', [UserController::class, 'user']);

    //Message
    Route::post('message/send', [MessageController::class, 'createRequest']);

    //Admin
    Route::middleware('admin')->group(function () {
        Route::post('list-user/{id}', [AdminController::class, 'listUser']);
        Route::post('create-user', [AdminController::class, 'createUser']);
        Route::put('update-user/{user}', [AdminController::class, 'updateUser']);
        Route::delete('delete-user/{id}', [AdminController::class, 'deleteUser']);

        Route::post('list-department/{id}', [AdminController::class, 'listDepartment']);
        Route::post('search-department', [AdminController::class, 'searchDepartment']);
        Route::post('create-department', [AdminController::class, 'createDepartment']);
        Route::get('user-department/{department}', [AdminController::class, 'getUserDepartment']);
        Route::put('update-department/{department}', [AdminController::class, 'updateDepartment']);
        Route::delete('delete-department/{department}', [AdminController::class, 'deleteDepartment']);

        Route::post('list-shift/{id}', [AdminController::class, 'listShift']);
        Route::post('create-shift', [AdminController::class, 'createShift']);
        Route::put('update-shift/{id}', [AdminController::class, 'updateShift']);
        Route::delete('delete-shift/{id}', [AdminController::class, 'deleteShift']);

        Route::post('manage-timekeeping/{skip}', [AdminController::class, 'manageTimeKeeping']);
        Route::put('update-timekeeping/{id}', [AdminController::class, 'updateTimeKeeping']);
        Route::delete('delete-timekeeping/{id}', [AdminController::class, 'deleteTimeKeeping']);

        Route::get('message/all', [MessageController::class, 'getAllMessage']);
        Route::get('message/limit', [MessageController::class, 'getLimitMessage']);
        Route::get('message/limit-unread', [MessageController::class, 'getLimitUnreadMessage']);
        Route::post('message/read', [MessageController::class, 'checkReadMessage']);
        Route::post('message/pass', [MessageController::class, 'checkPassMassage']);
    });
});

//User
Route::post('/login', [AuthController::class, 'login']);


//Forgot password
Route::middleware('guest')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});
Route::post('/check-email', [AuthController::class, 'checkEmail']);

//Email Verify
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

//Department
Route::get('department', [DepartmentController::class, 'index']);
Route::get('department/{id}', [DepartmentController::class, 'index']);

//Shift
Route::get('shift', [ShiftController::class, 'index']);
Route::get('shift/{id}', [ShiftController::class, 'index']);

Route::post('/', [NotificationSendController::class, 'sendNotification']);