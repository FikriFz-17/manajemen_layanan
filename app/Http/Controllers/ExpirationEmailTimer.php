<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpirationEmailTimer extends Controller
{
    public function showVerifyPage(Request $request)
    {
        $verificationExpireMinutes = config('auth.verification.expire');
        $email = session('unverified_email') ?? $request->get('email') ?? old('email');
        return response()->json([
            'email' => $email,
            'time' => $verificationExpireMinutes
        ]);
    }
}
