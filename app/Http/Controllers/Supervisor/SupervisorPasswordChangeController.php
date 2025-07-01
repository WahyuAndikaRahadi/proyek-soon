<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SupervisorPasswordChangeController extends Controller
{
    public function edit()
    {
        return view('supervisor.auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            // 'current_password' => ['required', 'string', 'current_password'], // BARIS INI DIHAPUS
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('supervisor.dashboard')->with('success', 'Kata sandi berhasil diubah!');
    }
}