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
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalSchedules }}</h3>
                    <p>Total Jadwal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="{{ route('admin.schedules.index') }}" class="small-box-footer">Lihat Jadwal <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        {{-- Akhir Total Jadwal --}}
    </div>

    <div class="row">
        <div class="col-md-6">
            {{-- Laporan: Jumlah Siswa per Kelas --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Jumlah Siswa per Kelas</h3>
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
            {{-- Laporan: Jumlah Guru per Mata Pelajaran (menggantikan Aktivitas Terakhir) --}}
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Jumlah Guru per Mata Pelajaran</h3>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th style="width: 100px">Jumlah Guru</th>
                                <th>Nama Guru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($teachersPerSubject as $data)
                                <tr>
                                    <td>{{ $data->subject_name }}</td>
                                    <td><span class="badge bg-secondary">{{ $data->total_teachers }}</span></td>
                                    <td>{{ $data->teacher_names }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data guru per mata pelajaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Daftar Guru <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Laporan: Jumlah Guru per Mata Pelajaran --}}
        </div>

       
    </div>

    <div class="row">
        <div class="col-md-6">
            {{-- Chart: Gender Siswa --}}
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Distribusi Gender Siswa</h3>
                </div>
                <div class="card-body">
                    <canvas id="genderChart" style="height: 300px;"></canvas>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-warning">Lihat Data Siswa <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Chart: Gender Siswa --}}
        </div>

         <div class="col-md-6">
            {{-- Chart: Status Kehadiran --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Status Kehadiran Siswa</h3>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" style="height: 300px;"></canvas>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.reports.attendance') }}" class="btn btn-sm btn-outline-info">Lihat Detail Absensi <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Chart: Status Kehadiran --}}
        </div>

    
    </div>

    <div class="row">
        <div class="col-md-8">
            {{-- Chart: Rata-rata Nilai per Mata Pelajaran --}}
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Rata-rata Nilai per Mata Pelajaran</h3>
                </div>
                <div class="card-body">
                    <canvas id="scoresChart" style="height: 300px;"></canvas>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.reports.assessment') }}" class="btn btn-sm btn-outline-success">Lihat Detail Penilaian <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Chart: Rata-rata Nilai --}}
        </div>

        <div class="col-md-4">
            {{-- Chart: Jurnal per Guru --}}
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Jurnal per Guru</h3>
                </div>
                <div class="card-body">
                    <canvas id="journalChart" style="height: 300px;"></canvas>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('admin.reports.journal') }}" class="btn btn-sm btn-outline-danger">Lihat Jurnal <i class="fas fa-angle-right"></i></a>
                </div>
            </div>
            {{-- Akhir Chart: Jurnal per Guru --}}
        </div>
    </div>
@stop

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart Status Kehadiran (Pie Chart)
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($attendanceLabels) !!},
            datasets: [{
                data: {!! json_encode($attendanceData) !!},
                backgroundColor: {!! json_encode($attendanceBackgroundColors) !!},
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // 2. Chart Gender Siswa (Doughnut Chart)
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    const genderChart = new Chart(genderCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($genderLabels) !!},
            datasets: [{
                data: {!! json_encode($genderData) !!},
                backgroundColor: {!! json_encode($genderChartColors) !!},
                borderWidth: 3,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ' + context.raw + ' siswa (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // 3. Chart Rata-rata Nilai (Bar Chart)
    const scoresCtx = document.getElementById('scoresChart').getContext('2d');
    const scoresChart = new Chart(scoresCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($scoreLabels) !!},
            datasets: [{
                label: 'Rata-rata Nilai',
                data: {!! json_encode($scoreData) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rata-rata: ' + context.raw.toFixed(1);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '';
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 4. Chart Jurnal per Guru (Horizontal Bar Chart)
    const journalCtx = document.getElementById('journalChart').getContext('2d');
    const journalChart = new Chart(journalCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($journalTeacherLabels) !!},
            datasets: [{
                label: 'Jumlah Jurnal',
                data: {!! json_encode($journalTeacherData) !!},
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Jurnal: ' + context.raw + ' entry';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Smooth animation untuk semua chart
    Chart.defaults.animation.duration = 1000;
    Chart.defaults.animation.easing = 'easeInOutQuart';
});
</script>
@endpush