@extends('adminlte::page')

{{-- Judul Halaman --}}
@section('title', 'Jurnal Sekolah BK')

{{-- Tambahkan Favicon --}}
@section('meta_tags')
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/x-icon">
@endsection

{{-- Header Halaman --}}
@section('content_header')
    <h1>@yield('page_title')</h1>
@stop

{{-- Konten Utama --}}
@section('content')

    {{-- Flash Message: Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Flash Message: Error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Flash Message: Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Konten Body Spesifik --}}
    @yield('content_body')

@stop

{{-- Tambahan CSS --}}
@section('css')
    {{-- Tambahkan CSS kustom di sini jika diperlukan --}}
@stop

{{-- Tambahan JavaScript --}}
@section('js')
    {{-- Tambahkan JS kustom di sini jika diperlukan --}}
@stop

{{-- Sidebar Khusus BK --}}
@section('sidebar_menu')

    {{-- Dashboard --}}
    <li class="nav-item">
        <a href="{{ route('bk.dashboard') }}" class="nav-link {{ Request::is('bk/dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    {{-- Header: Fitur BK --}}
    <li class="nav-header">FITUR BK</li>

    {{-- Catatan Sikap --}}
    <li class="nav-item">
        <a href="{{ route('bk.attitude_records.index') }}" class="nav-link {{ Request::is('bk/attitude-records*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <p>Catatan Sikap</p>
        </a>
    </li>

    {{-- Catatan Konseling --}}
    <li class="nav-item">
        <a href="{{ route('bk.counseling_records.index') }}" class="nav-link {{ Request::is('bk/counseling-records*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-comments"></i>
            <p>Catatan Konseling</p>
        </a>
    </li>

    {{-- Header: Akun --}}
    <li class="nav-header">AKUN</li>

    {{-- Ubah Password --}}
    <li class="nav-item">
        <a href="{{ route('bk.password.edit') }}" class="nav-link {{ Request::is('bk/change-password') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>Ubah Password</p>
        </a>
    </li>

    {{-- Logout --}}
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST" class="form-inline">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-danger">
                <i class="nav-icon fas fa-sign-out-alt"></i>
                <p>Logout</p>
            </button>
        </form>
    </li>

@stop

{{-- Footer --}}
@section('footer')
    <strong>Hak Cipta &copy; {{ date('Y') }} <a href="#">SMK Negeri 69 Jakarta</a>.</strong> Semua Hak Dilindungi.
@stop

