<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\TimeKeepingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use App\Models\Department;
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
Route::post('/change-password', [AuthController::class, 'changePassword']);

//Forgot password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('guest')->name('password.email');
Route::post('/check-email', [AuthController::class, 'checkEmail']);
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
Route::post('/timekeeping/update', [TimeKeepingController::class,'updateTimeKeeping']);

//User
Route::get('user/{id}', [UserController::class, 'user']);
Route::get('user/', [UserController::class, 'user']);

//Department
Route::get('department', [DepartmentController::class,'index']);
Route::get('department/{id}', [DepartmentController::class,'index']);

//Admin
Route::post('create-user', [AdminController::class,'createUser']);
Route::put('update-user/{id}', [AdminController::class,'updateUser']);
Route::delete('delete-user/{id}', [AdminController::class,'deleteUser']);
Route::post('create-department', [AdminController::class,'createDepartment']);
Route::get('user-department/{name}', [AdminController::class,'getUserDepartment']);
Route::put('update-department/{id}', [AdminController::class,'updateDepartment']);
Route::delete('delete-department/{id}', [AdminController::class,'deleteDepartment']);
Route::post('create-shift', [AdminController::class,'createShift']);
Route::put('update-shift/{id}',[AdminController::class,'updateShift']);

//Shift
Route::get('shift', [ShiftController::class,'index']);
Route::get('shift/{id}', [ShiftController::class,'index']);
Route::delete('delete-shift/{id}',[AdminController::class,'deleteShift']);
