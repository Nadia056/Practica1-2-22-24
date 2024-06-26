<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\registroController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[ApiController::class,'login'])->middleware('vpn');
Route::middleware(['auth:sanctum','vpn'])->post('logout',[ApiController::class,'logout']);
Route::middleware(['auth:sanctum','vpn'])->post('verify-code',[ApiController::class,'verifyCode']);
Route::post('register',[AuthController::class,'create'])->middleware('vpn');
Route::middleware(['auth:sanctum','vpn'])->post('regenerate-code',[ApiController::class,'regenerateCode']);