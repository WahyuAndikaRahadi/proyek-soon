@extends('admin.layouts.app')

@section('page_title', 'Dashboard Admin')

@section('content_body')
    <div class="row">
        {{-- Total Guru --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalTeachers }}</h3>
                    <p>Total Guru</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="{{ route('admin.teachers.index') }}" class="small-box-footer">Lihat Guru <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Total Kelas --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalClasses }}</h3>
                    <p>Total Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
                <a href="{{ route('admin.classes.index') }}" class="small-box-footer">Lihat Kelas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Total Siswa --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalStudents }}</h3>
                    <p>Total Siswa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="{{ route('admin.students.index') }}" class="small-box-footer">Lihat Siswa <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Total Mata Pelajaran --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalSubjects }}</h3>
                    <p>Total Mata Pelajaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
                <a href="{{ route('admin.subjects.index') }}" class="small-box-footer">Lihat Mapel <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        {{-- Total Jadwal --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary"> {{-- Menggunakan warna primer atau warna lain yang Anda inginkan --}}
                <div class="inner">
                    <h3>{{ $totalSchedules }}</h3>
                    <p>Total Jadwal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i> {{-- Icon kalender --}}
                </div>
                <a href="{{ route('admin.schedules.index') }}" class="small-box-footer">Lihat Jadwal <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Akhir Total Jadwal --}}

    </div>

    ---

    <div class="row">
        <div class="col-md-6">
            {{-- Laporan: Jumlah Siswa per Kelas --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Jumlah Siswa per Kelas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Kelas</th>
                                <th style="width: 100px">Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentsPerClass as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $data['class_name'] }}</td>
                                    <td><span class="badge bg-primary">{{ $data['total_students'] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data siswa per kelas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Detail Siswa <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Laporan: Jumlah Siswa per Kelas --}}
        </div>

        <div class="col-md-6">
            {{-- Laporan: Aktivitas Terakhir (Placeholder) --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Terakhir</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Ini adalah area di mana Anda bisa menampilkan log aktivitas terbaru, seperti penilaian yang baru dicatat, siswa baru terdaftar, atau perubahan data penting lainnya.</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>25 Mei 2025:</b> Penilaian Matematika Kelas 10A dicatat oleh Guru A.
                        </li>
                        <li class="list-group-item">
                            <b>24 Mei 2025:</b> Siswa baru 'Budi Santoso' terdaftar di Kelas 7B.
                        </li>
                        <li class="list-group-item">
                            <b>23 Mei 2025:</b> Data kelas '11 IPA 2' diperbarui.
                        </li>
                    </ul>
                    {{-- Menghubungkan ke halaman utama laporan --}}
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-info float-right">Lihat Semua Aktivitas <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Laporan: Aktivitas Terakhir --}}
        </div>
    </div>

    ---

    <div class="row">
        <div class="col-12">
            {{-- Laporan: Grafik Statistik (Placeholder) --}}
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Grafik Statistik</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Bagian ini dapat digunakan untuk menampilkan grafik visual dari data Anda (misalnya: jumlah siswa per tingkat, distribusi gender, atau tren pendaftaran siswa). Anda bisa menggunakan pustaka seperti **Chart.js** atau **D3.js** dan menyematkan canvas grafik di sini.</p>
                    <div style="height: 250px; background-color: #f8f9fa; border: 1px dashed #ced4da; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                        <i class="fas fa-chart-bar fa-3x"></i> &nbsp; Area Grafik Anda
                    </div>
                </div>
                <div class="card-footer text-right">
                    {{-- Menghubungkan ke halaman utama laporan --}}
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-success">Lihat Laporan Lengkap <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Laporan: Grafik Statistik --}}
        </div>
    </div>
@stop