<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('all-laporans', [LaporanApiController::class, "getAllData"]);

Route::middleware('auth:sanctum')->get('/user-laporans', [LaporanApiController::class, 'getUserData']);

Route::get('all-users', [UserController::class, "getAllUser"]);

Route::post('/login', [AuthApiController::class, "login"]);

Route::post('/register', [AuthApiController::class, "register"]);

Route::middleware('auth:sanctum')->post('/ajukanLaporan', [LaporanApiController::class, "createLaporan"]);
