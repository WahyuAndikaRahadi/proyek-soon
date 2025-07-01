<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Session;

class ManageUserPasswordController extends Controller
{
    /**
     * Tampilkan formulir dengan dropdown untuk memilih user,
     * dan juga formulir untuk mengubah password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm(Request $request)
    {
        // Ambil semua user kecuali admin yang sedang login
        // Sesuaikan jika Anda hanya ingin guru dan siswa
        $users = User::where('id', '!=', auth()->id())
                     ->orderBy('name')
                     ->get();

        // Ambil user yang dipilih jika ada di request (setelah submit dropdown)
        $selectedUser = null;
        if ($request->has('user_id')) {
            $selectedUser = User::find($request->input('user_id'));
        }

        return view('admin.change_user_password_dropdown', compact('users', 'selectedUser'));
    }

    /**
     * Perbarui password pengguna lain oleh admin.
     * Metode ini akan dipanggil saat form ubah password di-submit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Kata sandi baru harus diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Redirect kembali ke halaman yang sama dengan user yang baru diubah passwordnya
        return redirect()->route('admin.change-user-password.form', ['user_id' => $user->id])
                         ->with('success', 'Kata sandi untuk ' . $user->name . ' berhasil diperbarui!');
    }
}