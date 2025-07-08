<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('laporans', [LaporanController::class, "getAllData"]);

Route::get('users', [UserController::class, "getAllUser"]);

Route::middleware('auth:sanctum')->get('/user-laporans', [LaporanController::class, 'getUserData']);

Route::post('/login', [AuthApiController::class, "login"]);


