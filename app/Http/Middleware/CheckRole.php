<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika sudah login dan rolenya sesuai, boleh lewat
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Jika salah kamar (misal user maksa masuk url admin, atau admin maksa masuk url user)
        // Maka lemparkan kembali ke halamannya masing-masing
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/');
    }
}