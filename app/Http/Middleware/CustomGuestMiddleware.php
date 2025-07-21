<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()){
            $user = Auth::user();

            // Redirect user ke dashboard sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('adminDashboard');
            }

            if ($user->role === 'user') {
                return redirect()->route('dashboard');
            }
        }
        return $next($request);
    }
}
