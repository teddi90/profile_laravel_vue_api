<?php

use App\Http\Controllers\Admin\AppointmentController;
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

Route::get('users',[UserController::class,'index']);
Route::get('users/search',[UserController::class,'search']);
Route::post('users',[UserController::class,'store']);
Route::put('users/{user}',[UserController::class,'update']);
Route::patch('users/{user}/change-role',[UserController::class,'changeRole']);
Route::delete('users/{user}',[UserController::class,'destroy']);
Route::delete('users/',[UserController::class,'bulkDelete']);

Route::prefix('appointments')->group(function (){
   Route::get('/',[AppointmentController::class,'index']);
});
