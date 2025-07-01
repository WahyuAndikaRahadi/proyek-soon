<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

   <div class="sidebar">
    <nav class="pt-2">
        <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
            data-widget="treeview" role="menu"
            @if(config('adminlte.sidebar_nav_animation_speed') != 300)
                data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}"
            @endif
            @if(!config('adminlte.sidebar_nav_accordion'))
                data-accordion="false"
            @endif>

            {{-- Logika Sidebar Dinamis Berdasarkan Peran Pengguna --}}
            @if (Auth::check())
                @if (Auth::user()->role === 'admin')
                    {{-- Menu untuk Admin --}}
                    <li class="nav-header">NAVIGASI UTAMA</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
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
                    <li class="nav-item">
                        <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ Request::is('admin/schedules*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('admin.change-user-password.form') }}" class="nav-link {{ Request::is('admin/change-user-password*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-lock"></i> {{-- Icon yang sesuai untuk manajemen user --}}
                            <p>Ubah Password User</p>
                        </a>
                    </li>
                    {{-- --- AKHIR BARIS BARU --- --}}
                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item has-treeview {{ Request::is('admin/reports*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('admin/reports*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Laporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.attendance') }}" class="nav-link {{ Request::is('admin/reports/attendance*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.journal') }}" class="nav-link {{ Request::is('admin/reports/journal*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Jurnal Harian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.assessment') }}" class="nav-link {{ Request::is('admin/reports/assessment*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Penilaian Siswa</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-header">AKUN</li>
                    {{-- Change Password for Admin (akun admin sendiri) --}}
                    <li class="nav-item">
                        <a href="{{ route('password.change.form') }}" class="nav-link {{ Request::is('change-password') ? 'active' : '' }}">
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
                @elseif (Auth::user()->role === 'guru')
                    {{-- Menu untuk Guru --}}
                    <li class="nav-header">NAVIGASI UTAMA</li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ Request::is('teacher/dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-header">AKTIVITAS GURU</li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.schedules.index') }}" class="nav-link {{ Request::is('teacher/schedules*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.journals.index') }}" class="nav-link {{ Request::is('teacher/journals*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book-open"></i>
                            <p>Jurnal Harian</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ Request::is('teacher/attendances*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ Request::is('teacher/attendances*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>
                                Absensi Siswa
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('teacher.attendances.create') }}" class="nav-link {{ Request::is('teacher/attendances/create*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Catat Absensi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('teacher.attendances.history') }}" class="nav-link {{ Request::is('teacher/attendances/history*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Riwayat Absensi</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.assessments.index') }}" class="nav-link {{ Request::is('teacher/assessments*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Penilaian Siswa</p>
                        </a>
                    </li>
                    <li class="nav-header">AKUN</li>
                    {{-- Change Password for Guru (akun guru sendiri) --}}
                    <li class="nav-item">
                        <a href="{{ route('password.change.form') }}" class="nav-link {{ Request::is('change-password') ? 'active' : '' }}">
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
                @endif
            @endif
        </ul>
    </nav>
</div>
</aside>