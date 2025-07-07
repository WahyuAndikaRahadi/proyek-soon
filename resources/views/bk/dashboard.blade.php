@extends('bk.layouts.app')

@section('page_title', 'Dashboard BK')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info rounded">
                <div class="inner">
                    <h3>{{ $totalTeachers }}</h3>
                    <p>Total Guru</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
              
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success rounded">
                <div class="inner">
                    <h3>{{ $totalStudents }}</h3>
                    <p>Total Siswa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
              
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning rounded">
                <div class="inner">
                    <h3>{{ $totalClasses }}</h3>
                    <p>Total Kelas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-school"></i>
                </div>
              
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger rounded">
                <div class="inner">
                    <h3>{{ $totalSubjects }}</h3>
                    <p>Total Mata Pelajaran</p>
                </div>
                <div class="icon">
                    <i class="fas fa-book"></i>
                </div>
              
            </div>
        </div>
    </div>

    

    <div class="row">
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header">
                    <h3 class="card-title">Catatan Sikap Siswa Terbaru</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Untuk melihat semua catatan sikap, silakan kunjungi halaman <a href="{{ route('bk.attitude_records.index') }}">Catatan Sikap</a>.</p>
                    {{-- Anda bisa menambahkan tabel catatan sikap terbaru di sini jika diinginkan,
                         tetapi untuk saat ini, saya hanya akan memberikan link ke halaman index --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card rounded">
                <div class="card-header">
                    <h3 class="card-title">Catatan Konseling Siswa Terbaru</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Untuk melihat dan mengelola catatan konseling, silakan kunjungi halaman <a href="{{ route('bk.counseling_records.index') }}">Catatan Konseling</a>.</p>
                    {{-- Anda bisa menambahkan tabel catatan konseling terbaru di sini jika diinginkan,
                         tetapi untuk saat ini, saya hanya akan memberikan link ke halaman index --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    $(function () {
        // Gender Chart
        var genderChartCanvas = $('#genderChart').get(0).getContext('2d');
        var genderChartData = {
            labels: @json($genderLabels),
            datasets: [{
                data: @json($genderData),
                backgroundColor: @json($actualGenderColors),
            }]
        };
        var genderChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        };
        new Chart(genderChartCanvas, {
            type: 'doughnut',
            data: genderChartData,
            options: genderChartOptions
        });

        // Attendance Chart
        var attendanceChartCanvas = $('#attendanceChart').get(0).getContext('2d');
        var attendanceChartData = {
            labels: @json($attendanceLabels),
            datasets: [{
                data: @json($attendanceData),
                backgroundColor: @json($actualAttendanceColors),
            }]
        };
        var attendanceChartOptions = {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        };
        new Chart(attendanceChartCanvas, {
            type: 'pie',
            data: attendanceChartData,
            options: attendanceChartOptions
        });
    });
</script>
@endpush

