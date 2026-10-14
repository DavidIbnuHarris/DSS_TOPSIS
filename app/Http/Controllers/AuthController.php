<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses data login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek kecocokan email dan password
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // CEK ROLE DISINI DAN PAKSA REDIRECT SESUAI ROLE
            if (Auth::user()->role === 'admin') {
                return redirect('/admin'); // Paksa admin ke /admin
            }

            return redirect('/'); // Paksa user biasa ke / (form kuesioner)
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}