<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MeetingController;
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

    //Timekeeping
    Route::group(['prefix' => '/timekeeping'], function () {
        Route::middleware('checkip')->group(function () {
            Route::post('/check-in', [TimeKeepingController::class, 'checkIn']);
            Route::put('/check-out', [TimeKeepingController::class, 'checkOut']);
        });
        Route::get('/me', [TimeKeepingController::class, 'getTimeKeeping']);
        Route::get('/list/{from}/{to}', [TimeKeepingController::class, 'getListTimeKeeping']);
        Route::get('/list/{from}/{to}/{user_id}', [TimeKeepingController::class, 'getListTimeKeeping']);
        Route::post('/search/around-time', [TimeKeepingController::class, 'searchByAroundTime']);
        Route::post('/search/month-year', [TimeKeepingController::class, 'searchByMonth']);
        Route::post('/update', [TimeKeepingController::class, 'updateTimeKeeping']);
        Route::get('/export/{fromMonth}/{toMonth}/{userId}', [TimeKeepingController::class, 'getTimeKeepingExport']);
    });

    //User
    Route::group(['prefix' => '/user'], function () {
        Route::get('/{id}', [UserController::class, 'user']);
        Route::get('/', [UserController::class, 'user']);
    });

    //Message
    Route::post('message/send', [MessageController::class, 'createRequest']);

    //Admin
    Route::group(['middleware' => ['admin']], function () {
        Route::group(['prefix' => '/user'], function () {
            Route::post('/list/{id}', [AdminController::class, 'listUserInDepartment']);
            Route::post('/create', [AdminController::class, 'createUser']);
            Route::put('/update/{user}', [AdminController::class, 'updateUser']);
            Route::delete('/delete/{id}', [AdminController::class, 'deleteUser']);
        });

        Route::group(['prefix' => '/department'], function () {
            Route::post('/list/{id}', [AdminController::class, 'listDepartment']);
            Route::post('/search', [AdminController::class, 'searchDepartment']);
            Route::post('/create', [AdminController::class, 'createDepartment']);
            Route::get('/user/{department}', [AdminController::class, 'getUserDepartment']);
            Route::put('/update/{department}', [AdminController::class, 'updateDepartment']);
            Route::delete('/delete/{department}', [AdminController::class, 'deleteDepartment']);
        });


        Route::group(['prefix' => '/shift'], function () {
            Route::post('list/{id}', [AdminController::class, 'listShift']);
            Route::post('create', [AdminController::class, 'createShift']);
            Route::put('update/{id}', [AdminController::class, 'updateShift']);
            Route::delete('delete/{id}', [AdminController::class, 'deleteShift']);
        });

        Route::group(['prefix' => '/timekeeping'], function () {
            Route::post('/export-statistic', [AdminController::class, 'ExportTimeKeepingStatistic']);
            Route::post('/manage/{skip}', [AdminController::class, 'manageTimeKeeping']);
        });


        Route::group(['prefix' => '/message'], function () {
            Route::get('/all', [MessageController::class, 'getAllMessage']);
            Route::get('/limit', [MessageController::class, 'getLimitMessage']);
            Route::get('/limit-unread', [MessageController::class, 'getLimitUnreadMessage']);
            Route::post('/read', [MessageController::class, 'markAsRead']);
            Route::post('/pass', [MessageController::class, 'markAsConfirmed']);
        });
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
Route::group(['prefix' => '/department'], function () {
    Route::get('/', [DepartmentController::class, 'index']);
    Route::get('/{id}', [DepartmentController::class, 'index']);
    Route::get('/all-user/{department_id}', [DepartmentController::class, 'getAllUserInDepartment']);
});

//Shift
Route::group(['prefix' => '/shift'], function () {
    Route::get('/', [ShiftController::class, 'index']);
    Route::get('/{id}', [ShiftController::class, 'index']);
});

//Meeting
Route::group(['prefix' => '/meeting'], function () {
    Route::get('/me', [MeetingController::class, 'getMyScheduleMeeting']);
    Route::post('/create', [MeetingController::class, 'createNewScheduleMeeting']);
    Route::post('/delete', [MeetingController::class, 'deleteScheduleMeeting']);
    Route::post('/read', [MeetingController::class, 'markAsRead']);
});
