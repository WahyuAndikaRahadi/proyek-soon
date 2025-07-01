<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import untuk hash password

class PasswordChangeController extends Controller
{
    /**
     * Tampilkan formulir untuk mengubah password.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('auth.change-password'); // Menggunakan view baru: password/change.blade.php
    }

    /**
     * Perbarui password pengguna yang login.
     * Tidak memerlukan password lama untuk validasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validasi password baru
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Password baru harus diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Opsional: Re-authenticate user setelah perubahan password
        Auth::login($user); 

        return redirect()->route('password.change.form')->with('success', 'Password berhasil diperbarui!');
    }
}