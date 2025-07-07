<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani proses login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Set flash message untuk SweetAlert sukses login
            $request->session()->flash('success', 'Selamat datang kembali!');

            // Redirect berdasarkan peran pengguna
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif (Auth::user()->role === 'guru') {
                return redirect()->intended('/teacher/dashboard');
            }
            elseif (Auth::user()->role === 'supervisor') {
                return redirect()->intended('/supervisor/dashboard');
            }
            elseif (Auth::user()->role === 'bk') {
                return redirect()->intended('/bk/dashboard');
            }
             else {
                // Default redirect jika peran tidak ditentukan
                return redirect()->intended('/dashboard');
            }
        }

        // Jika autentikasi gagal
        throw ValidationException::withMessages([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ]);
    }

    /**
     * Menangani proses logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Set flash message untuk SweetAlert sukses logout
        $request->session()->flash('success', 'Anda telah berhasil keluar. Sampai jumpa!');

        return redirect('/login'); // Arahkan kembali ke halaman login
    }
}