<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
        ],[
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password terdiri dari huruf besar, huruf kecil, angka, dan simbol'
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'is_active' => false,
            'profile_url' => 'default.jpg',
            'remember_token' => Str::random(60),
        ]);

        event(new Registered($user));
        session(['unverified_email' => $user->email]);
        return redirect()->route('verification.notice', ['email' => $user->email]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (is_null($user)) {
            return back()->withErrors([
                'login' => 'Akun anda belum terdaftar'
            ]);
        }

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'login' => 'Email atau password salah'
            ])->withInput($request->only('email'));
        }

        if (is_null($user->email_verified_at)) {
            return back()->withErrors([
                'login' => 'Akun anda belum diaktivasi. Silahkan cek email anda untuk verifikasi.'
            ])->withInput($request->only('email'));
        }

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
