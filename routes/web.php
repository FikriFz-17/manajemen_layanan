<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/laporan/all', [LaporanController::class, "getAllData"]);

// Auth Routes (Login, Register)
Route::middleware('guest')->group(function () {
    // view login
    Route::get('/login', function(){
        return view('auth.login');
    })->name('login');

    // Google Login
    Route::get('/login/google', function(){
        return 'Maintenance';
    })->name('login.google');

    // login controller
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // view register
    Route::get('/register', function(){
        return view('auth.register');
    })->name('register');

    // register controller
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Email Verification Routes
Route::get('/email/verify', function (Request $request) {
    $email = session('unverified_email') ?? $request->get('email') ?? old('email');
    return view('auth.verify-email', compact('email'));
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $email = $request->email ?? session('unverified_email');

    if (!$email) {
        return back()->with('error', 'Email tidak ditemukan');
    }

    if ($request->email) {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        return back()->with('error', 'Email tidak ditemukan.');
    }

    if ($user && !$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        return redirect()->back()->with('success', 'Email verifikasi berhasil dikirim!');
    }

    if ($user->hasVerifiedEmail()) {
        session()->forget('unverified_email');
        return redirect()->route('login')->with('success', 'Email sudah terverifikasi. Silakan login.');
    }

    $user->sendEmailVerificationNotification();
    return back()->with('success', 'Link verifikasi telah dikirim, Silahkan check kembali email anda');
})->middleware('throttle:6,1')->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        session()->forget('unverified_email');
    }

    return redirect()->route('login')->with('success', 'Email berhasil diverifikasi! Silahkan login.');
})->middleware(['signed'])->name('verification.verify');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.submit');

// Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('userDashboard');
    })->name('dashboard');

    Route::get('/laporan/user', [LaporanController::class, "getUserData"]);

    Route::get('/ajukanLaporan', function () {
        return view('userAjukanLaporan');
    })->name('ajukanLaporan');

    Route::post('ajukanLaporan', [LaporanController::class, 'createLaporan'])->name('laporan.submit');

    Route::get('/setProfile', function () {
        return view('setProfile');
    })->name('setProfile');
    Route::post('/setProfile', [UserController::class, 'update'])->name('update.submit');
});

Route::get('/adminDash', function () {
    return view('adminDashboard');
})->name('adminDashboard');

Route::get('/userManagement', function(){
    return view('userManagement');
})->name('userManagement');

Route::get('/user/all', [UserController::class, "getAllUser"]);

Route::post('/admin/laporan/{id}/update', [LaporanController::class, 'tanganiLaporan'])->name('admin.laporan.update');

