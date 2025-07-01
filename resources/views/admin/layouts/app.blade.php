@extends('adminlte::page')

{{-- Judul Halaman --}}
@section('title', 'Admin Jurnal Sekolah')

{{-- Tambahan Favicon --}}
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

    {{-- Konten Body Dinamis --}}
    @yield('content_body')

@stop

{{-- Tambahan CSS --}}
@section('css')
    {{-- Tambahkan CSS kustom di sini --}}
@stop

{{-- Tambahan JS --}}
@section('js')
    {{-- Tambahkan JavaScript kustom di sini --}}
@stop

{{-- Sidebar Kustom --}}
@section('sidebar_menu')

    {{-- Dashboard --}}
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    {{-- Header: Manajemen Data --}}
    <li class="nav-header">MANAJEMEN DATA</li>

    <li class="nav-item">
        <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ Request::is('admin/teachers*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chalkboard-teacher"></i>
            <p>Guru</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.classes.index') }}" class="nav-link {{ Request::is('admin/classes*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-school"></i>
            <p>Kelas</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ Request::is('admin/subjects*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-book"></i>
            <p>Mata Pelajaran</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('admin.students.index') }}" class="nav-link {{ Request::is('admin/students*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-graduate"></i>
            <p>Siswa</p>
        </a>
    </li>

    {{-- Header: Laporan --}}
    <li class="nav-header">LAPORAN</li>

    <li class="nav-item">
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Laporan</p>
        </a>
    </li>

    {{-- Header: Akun --}}
    <li class="nav-header">AKUN</li>

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
