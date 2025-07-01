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
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Tahun/Semester</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $schedule->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $schedule->class->name ?? 'N/A' }}</td>
                            <td>{{ $schedule->subject->name ?? 'N/A' }}</td>
                            <td>{{ $schedule->day_of_week }}</td>
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
                            <td colspan="8" class="text-center">Tidak ada jadwal yang tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $schedules->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@stop