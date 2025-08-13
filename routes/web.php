<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExpirationEmailTimer;
use App\Http\Controllers\LaporanExportController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/detail/{resi}', function($resi){
    return view('detail-laporan');
})->name('detail-laporan');

Route::get('/detail/{resi}', [LaporanController::class, 'detailLaporan'])->name('detail-laporan');

Route::get('/public/data', [LaporanController::class, "getPublicData"]);

Route::get('/semua/laporan', function(){
    return view('semua-laporan');
})->name('semua-laporan');

// Auth Routes (Login, Register)
Route::middleware('custom_guest')->group(function () {
    // view login
    Route::get('/login', function(){
        return view('auth.login');
    })->name('login');

    // login controller
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // view register
    Route::get('/register', function(){
        return view('auth.register');
    })->name('register');

    // register controller
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Forgot password route
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['status' => 'Link reset password berhasil dikirim ke email anda'])
            : back()->withErrors(['email' => 'Gagal mengirim link reset password']);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token, Request $request) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus memiliki huruf besar, kecil, angka, dan simbol.',
            'password.min' => 'Password harus memiliki minimal 8 karakter.'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
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

// Email verification timer
Route::get('/expiration', [ExpirationEmailTimer::class, "showVerifyPage"]);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.submit');

// User Route
Route::middleware(['auth', 'verified', 'user'])->group(function () {
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

    Route::put('/setProfile', [UserController::class, 'update'])->name('update.submit');

    Route::put('/upload-photo', [UserController::class, 'updatePhotoProfile'])->name('uploadPhoto.submit');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function(){
    Route::get('/adminDash', function () {
        return view('adminDashboard');
    })->name('adminDashboard');

    Route::get('/userManagement', function(){
        return view('userManagement');
    })->name('userManagement');

    Route::get('/resetPassAdmin', function(){
        return view('adminPasswordReset');
    })->name('adminResetPassword');

    Route::get('/user/all', [UserController::class, "getAllUser"]);

    Route::get('/laporan/all', [LaporanController::class, "getAllData"]);

    Route::put('/admin/laporan/{id}/update', [LaporanController::class, 'tanganiLaporan'])->name('admin.laporan.update');

    Route::get('/export-laporan/All', [LaporanExportController::class, 'exportAll']);

    Route::get('/export-laporan/perTahun', [LaporanExportController::class, 'exportPerTahun']);

    Route::get('/export-laporan/perBulan', [LaporanExportController::class, 'exportPerBulan']);

    Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser'])->name('users.destroy');

    Route::put('/update-admin-pass', [UserController::class, 'updateAdminPass'])->name('updateAdminPass.submit');
});

// import route
Route::post('/import/kecamatan', [ImportController::class, 'importKecamatan'])->name('import.kecamatan');

Route::post('/import/desa', [ImportController::class, 'importDesa'])->name('import.desa');

Route::post('/import/pemda', [ImportController::class, 'importPemda'])->name('import.pemda');



