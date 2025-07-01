@extends('teacher.layouts.app')

@section('title', 'Ubah Password')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
{{-- Import SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8fafc;
    }
    
    .gradient-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    
    .password-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .password-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .input-focus:focus {
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        motion.animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        motion.animate('.password-card', { opacity: [0, 1], y: [20, 0] }, { duration: 0.3 });

        // SweetAlert for success message
        @if (session('status'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('status') }}',
                showConfirmButton: false,
                timer: 3000
            });
        @endif
    });

    // Fungsi untuk menampilkan SweetAlert konfirmasi
    function confirmPasswordChange(event) {
        // Karena ini dipanggil dari onclick pada tombol submit, kita harus mencegah submit default
        event.preventDefault(); 

        Swal.fire({
            title: 'Konfirmasi Perubahan Password',
            text: "Apakah Anda yakin ingin mengubah password Anda?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5', // Warna indigo
            cancelButtonColor: '#ef4444', // Warna merah
            confirmButtonText: 'Ya, Ubah Password!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika dikonfirmasi, submit form secara manual
                document.getElementById('change-password-form').submit(); 
            }
        });
    }
</script>

<div class="mx-auto py-8 px-4">
    <div id="page-title" class="flex items-center mb-8">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-lock text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Ubah Password</h1>
            <p class="text-gray-600">Ganti password akun Anda dengan yang baru</p>
        </div>
    </div>

    <div class="max-w-md mx-auto">
        <div class="password-card bg-white">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">Formulir Ubah Password</h3>
            </div>
            
            <div class="p-6">
                {{-- Session status (success message) handled by SweetAlert in JS --}}

                <form id="change-password-form" method="POST" action="{{ route('teacher.password.change.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            Password Baru
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password" 
                                   class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline input-focus @error('password') border-red-500 @enderror"
                                   name="password" required autocomplete="new-password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs italic mt-2" role="alert">
                                <strong>{{ $message }}</strong>
                            </p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                            Konfirmasi Password Baru
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" 
                                   class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline input-focus"
                                   name="password_confirmation" required autocomplete="new-password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs italic mt-2" role="alert">
                                <strong>{{ $message }}</strong>
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" 
                                onclick="confirmPasswordChange(event)" {{-- Panggil fungsi di sini --}}
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Simpan Password Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection