<?php

use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\AppointmentStatusController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardStatController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
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

//Route::middleware('auth')->group(function (){
//    Route::get('clients',[ClientController::class,'index']);
//    Route::get('stats/appointments',[DashboardStatController::class,'appointments']);
//    Route::get('stats/users',[DashboardStatController::class,'users']);
//    Route::get('settings',[SettingController::class,'index']);
//    Route::post('settings',[SettingController::class,'update']);
//    Route::get('profile',[ProfileController::class,'index']);
//
//    Route::prefix('users')->group(function (){
//        Route::get('/',[UserController::class,'index']);
//        Route::post('/',[UserController::class,'store']);
//        Route::put('/{user}',[UserController::class,'update']);
//        Route::patch('/{user}/change-role',[UserController::class,'changeRole']);
//        Route::delete('/{user}',[UserController::class,'destroy']);
//        Route::delete('/',[UserController::class,'bulkDelete']);
//    });
//
//    Route::prefix('appointments')->group(function (){
//        Route::get('/',[AppointmentController::class,'index']);
//        Route::post('/create',[AppointmentController::class,'store']);
//        Route::get('{appointment}/edit',[AppointmentController::class,'edit']);
//        Route::put('{appointment}/edit',[AppointmentController::class,'update']);
//        Route::delete('{appointment}',[AppointmentController::class,'destroy']);
//
//    });
//    Route::get('appointments-status',[AppointmentStatusController::class,'getStatusWithCount']);
//});



