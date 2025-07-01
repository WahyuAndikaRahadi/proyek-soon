@extends('adminlte::page')

{{-- Judul Halaman --}}
@section('title', 'Ubah Password Admin')

{{-- Header Konten Halaman --}}
@section('content_header')
    <h1>Ubah Password</h1>
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
        <form action="{{ route('admin.password.change.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="card-body">
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Masukkan password baru" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Konfirmasi password baru" required>
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
    {{-- Menambahkan item menu untuk ubah password --}}
    <li class="nav-item">
        <a href="{{ route('admin.password.change.form') }}" class="nav-link {{ Request::is('admin/change-password') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>Ubah Password</p>
        </a>
    </li>
@stop