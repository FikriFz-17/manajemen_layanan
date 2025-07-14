<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\LaporanApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication & user functionality
Route::post('/login', [AuthApiController::class, "login"]);

Route::post('/register', [AuthApiController::class, "register"]);

Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('update-profile', [UserApiController::class, "update"]);

// Laporan
Route::get('all-laporans', [LaporanApiController::class, "getAllData"]);

Route::middleware('auth:sanctum')->get('/user-laporans', [LaporanApiController::class, 'getUserData']);

Route::get('all-users', [UserApiController::class, "getAllUser"]);

Route::middleware('auth:sanctum')->post('/ajukanLaporan', [LaporanApiController::class, "createLaporan"]);

Route::get('public/data', [LaporanApiController::class, "getPublicData"]);

// admin functionality
Route::post('kirim-verifikasi', function (Request $request) {
    $request->validate([
        'email' => 'required|email|exists:users,email'
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email sudah diverifikasi'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Email verifikasi telah dikirim.']);
});


