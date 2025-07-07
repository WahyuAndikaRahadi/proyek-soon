<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BkPasswordChangeController extends Controller
{
    /**
     * Menampilkan formulir perubahan password untuk petugas BK.
     */
    public function edit()
    {
        return view('bk.auth.change-password');
    }

    /**
     * Memperbarui password petugas BK.
     */
    public function update(Request $request)
    {
        $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('bk.dashboard')->with('success', 'Kata sandi berhasil diubah!');
    }
}

