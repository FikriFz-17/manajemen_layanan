<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\InstansiApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public api data
Route::get('public/data', [LaporanApiController::class, "getPublicData"]);

// Authentication api
Route::post('/login', [AuthApiController::class, "login"]);

Route::post('/register', [AuthApiController::class, "register"]);

Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

// User api functionality
Route::middleware(['auth:sanctum', 'user'])->group(function(){
    Route::put('update-profile', [UserApiController::class, "update"]);

    Route::get('/user-laporans', [LaporanApiController::class, 'getUserData']);

    Route::post('/ajukanLaporan', [LaporanApiController::class, "createLaporan"]);

    Route::post('/update-photo', [UserApiController::class, 'updatePhotoProfile']);
});

// Admin api functionality
Route::middleware(['auth:sanctum', 'admin'])->group(function(){
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

    Route::get('all-users', [UserApiController::class, "getAllUser"]);

    Route::get('all-laporans', [LaporanApiController::class, "getAllData"]);

    Route::put('/laporan/{id}/tangani', [LaporanApiController::class, 'tanganiLaporan']);

    Route::delete('/delete-user/{id}', [UserApiController::class, 'deleteUserApi']);
});

// wilayah API
Route::get('/kecamatan', [InstansiApiController::class, "getKecamatan"]);

Route::get('/desa', [InstansiApiController::class, "getDesa"]);

Route::get('/pemda', [InstansiApiController::class, "getPemda"]);


