@extends('adminlte::page') {{-- Sesuaikan ini dengan layout dasar supervisor Anda. Jika Anda memiliki layout khusus supervisor, seperti 'supervisor.layout.app', gunakan itu. --}}

{{-- Judul Halaman --}}
@section('title', 'Ubah Password Supervisor')

{{-- Header Konten Halaman --}}
@section('content_header')
    <h1>Ubah Password Supervisor</h1>
@stop

{{-- Konten Utama Halaman --}}
@section('content')
    {{-- Pesan Sukses/Error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Ubah Password</h3>
        </div>
        {{-- Pastikan action mengarah ke rute supervisor --}}
        <form action="{{ route('supervisor.password.change.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="card-body">
                {{-- Kolom 'Password Saat Ini' dihapus --}}
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="text" name="new_password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" placeholder="Masukkan password baru" required>
                    @error('new_password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input type="text" name="new_password_confirmation" class="form-control" id="new_password_confirmation" placeholder="Konfirmasi password baru" required>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Ubah Password</button>
            </div>
        </form>
    </div>
@stop

{{-- CSS Tambahan --}}
@section('css')
    {{-- Anda bisa menambahkan CSS kustom di sini --}}
@stop

{{-- JavaScript Tambahan --}}
@section('js')
    {{-- Anda bisa menambahkan JavaScript kustom di sini --}}
@stop

{{-- Konfigurasi Menu Sidebar AdminLTE --}}
@section('sidebar_menu')
    {{-- Memanggil kembali sidebar menu yang sudah ada di layout utama --}}
    @parent
    {{-- Menambahkan item menu untuk ubah password supervisor --}}
    <li class="nav-item">
        <a href="{{ route('supervisor.password.change.form') }}" class="nav-link {{ Request::is('supervisor/change-password') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>Ubah Password</p>
        </a>
    </li>
@stop
