@extends('admin.layouts.app')

@section('page_title', 'Manajemen Jadwal Guru')

@section('content_body')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Daftar Jadwal</h3>
            <div class="card-tools">
                <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Form Dropdown untuk Filter Guru --}}
            <form action="{{ route('admin.schedules.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="teacher_filter">Filter berdasarkan Guru:</label>
                            <select name="teacher_id" id="teacher_filter" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Semua Guru --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ $selectedTeacherId == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive p-0">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Guru</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Hari</th>
                            <th>Jam Ke / Sesi</th> {{-- Kolom baru --}}
                            <th>Jam Mulai - Selesai</th> {{-- Ubah nama kolom untuk kejelasan --}}
                            <th>Tahun/Semester</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td>{{ $loop->iteration + ($schedules->currentPage() - 1) * $schedules->perPage() }}</td>
                                <td>{{ $schedule->teacher->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->class->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                                <td>{{ $schedule->day_of_week }}</td>
                                <td>{{ $schedule->at_time ?? '-' }}</td> {{-- Menampilkan data at_time --}}
                                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                <td>{{ $schedule->academic_year ?? '-' }} / {{ $schedule->semester ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada jadwal yang tersedia.</td> {{-- Sesuaikan colspan --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{-- Pastikan pagination links tetap mempertahankan filter guru --}}
            {{ $schedules->appends(request()->query())->links('vendor.pagination.custom-pagination-simple') }}
        </div>
    </div>
@stop

