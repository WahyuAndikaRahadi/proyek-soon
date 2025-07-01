@extends('admin.layouts.app')

@section('page_title', 'Laporan Absensi')

@section('content_body')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Absensi</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.attendance') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="start_date">Dari Tanggal:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $request->start_date }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="end_date">Sampai Tanggal:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $request->end_date }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="class_id">Kelas:</label>
                        <select name="class_id" id="class_id" class="form-control">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ $request->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="subject_id">Mata Pelajaran:</label>
                        <select name="subject_id" id="subject_id" class="form-control">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="user_id">Guru:</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Semua Guru</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $request->user_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                        {{-- Form terpisah untuk export agar filter tetap terbawa --}}
                        <button type="submit" class="btn btn-success float-right" form="exportAttendanceForm"><i class="fas fa-file-excel"></i> Ekspor Excel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Absensi</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        {{-- UBAH INI: Kolom sekarang untuk Guru Pengajar --}}
                        <th>Guru Pengajar</th>
                    </tr>
                </thead>
<tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->student->name ?? 'N/A' }}</td>
                            <td>{{ $attendance->student->class->name ?? 'N/A' }}</td>
                            <td>{{ $attendance->schedule->subject->name ?? 'N/A' }}</td>
                            <td>{{ $attendance->schedule->start_time ?? 'N/A' }}</td>
                            <td>{{ $attendance->schedule->end_time ?? 'N/A' }}</td>
                            <td>{{ $attendance->status }}</td>
                            {{-- UBAH DI SINI UNTUK CATATAN --}}
                            <td>{{ $attendance->notes ?? '-' }}</td> 
                            <td>{{ $attendance->schedule->teacher->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $attendances->appends($request->except('page'))->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    {{-- Form terpisah untuk export agar filter tetap terbawa --}}
    <form id="exportAttendanceForm" action="{{ route('admin.reports.attendance.export') }}" method="GET" style="display:none;">
        @foreach($request->all() as $key => $value)
            @if(!in_array($key, ['_token', 'page']))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
    </form>
@stop