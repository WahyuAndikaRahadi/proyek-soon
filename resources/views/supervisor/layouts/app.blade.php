@extends('adminlte::page')

{{-- Judul Halaman --}}
@section('title', 'Jurnal Sekolah Supervisor')

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

{{-- Sidebar Khusus Supervisor --}}
@section('sidebar_menu')

    {{-- Dashboard --}}
    <li class="nav-item">
        <a href="{{ route('supervisor.dashboard') }}" class="nav-link {{ Request::is('supervisor/dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    {{-- Header: Laporan --}}
    <li class="nav-header">LAPORAN</li>

    <li class="nav-item has-treeview {{ Request::is('supervisor/reports*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('supervisor/reports*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
                Laporan
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('supervisor.reports.attendance') }}" class="nav-link {{ Request::is('supervisor/reports/attendance*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Absensi</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('supervisor.reports.journal') }}" class="nav-link {{ Request::is('supervisor/reports/journal*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Jurnal Harian</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('supervisor.reports.assessment') }}" class="nav-link {{ Request::is('supervisor/reports/assessment*') ? 'active' : '' }}">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Penilaian Siswa</p>
                </a>
            </li>
        </ul>
    </li>

    {{-- Header: Akun --}}
    <li class="nav-header">AKUN</li>

    <li class="nav-item">
        <a href="{{ route('supervisor.password.change.form') }}" class="nav-link {{ Request::is('supervisor/change-password') ? 'active' : '' }}">
            <i class="nav-icon fas fa-key"></i>
            <p>Ubah Password</p>
        </a>
    </li>

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
