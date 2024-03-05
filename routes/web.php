<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AppointmentStatusController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware'=>'auth','prefix'=>'api'],function () {
    Route::get('clients', [ClientController::class, 'index']);

    Route::prefix('stats')->group(function (){
        Route::get('appointments', [DashboardStatController::class, 'appointments']);
        Route::get('users', [DashboardStatController::class, 'users']);
    });

    Route::prefix('settings')->group(function (){
        Route::get('/', [SettingController::class, 'index']);
        Route::post('/', [SettingController::class, 'update']);
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/', [ProfileController::class, 'update']);
    });
    Route::post('upload-profile-image', [ProfileController::class, 'uploadImage']);

    Route::post('change-user-password', [ProfileController::class, 'changePassword']);


    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::patch('/{user}/change-role', [UserController::class, 'changeRole']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
        Route::delete('/', [UserController::class, 'bulkDelete']);
    });

    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/create', [AppointmentController::class, 'store']);
        Route::get('{appointment}/edit', [AppointmentController::class, 'edit']);
        Route::put('{appointment}/edit', [AppointmentController::class, 'update']);
        Route::delete('{appointment}', [AppointmentController::class, 'destroy']);

    });
    Route::get('appointments-status', [AppointmentStatusController::class, 'getStatusWithCount']);
});

Route::get('{view}',ApplicationController::class)->where('view','(.*)')->middleware('auth');




