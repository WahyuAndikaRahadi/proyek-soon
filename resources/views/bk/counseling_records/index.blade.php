@extends('bk.layouts.app')

@section('page_title', 'Catatan Konseling Siswa')

@section('content')
    <div class="card rounded">
        <div class="card-header">
            <h3 class="card-title">Daftar Catatan Konseling</h3>
            <div class="card-tools">
                <a href="{{ route('bk.counseling_records.create') }}" class="btn btn-primary btn-sm rounded">
                    <i class="fas fa-plus"></i> Tambah Catatan Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.counseling_records.index') }}" method="GET" class="form-inline mb-4">
                <div class="form-group mr-2 mb-2">
                    <label for="start_date" class="mr-1">Dari Tanggal:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control rounded" value="{{ $request->start_date }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="end_date" class="mr-1">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control rounded" value="{{ $request->end_date }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="student_id" class="mr-1">Siswa:</label>
                    <select name="student_id" id="student_id" class="form-control rounded">
                        <option value="">-- Semua Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $request->student_id == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="bk_teacher_id" class="mr-1">Guru BK:</label>
                    <select name="bk_teacher_id" id="bk_teacher_id" class="form-control rounded">
                        <option value="">-- Semua Guru BK --</option>
                        @foreach($bkTeachers as $bkTeacher)
                            <option value="{{ $bkTeacher->id }}" {{ $request->bk_teacher_id == $bkTeacher->id ? 'selected' : '' }}>{{ $bkTeacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary rounded mb-2"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('bk.counseling_records.index') }}" class="btn btn-secondary rounded ml-2 mb-2"><i class="fas fa-sync-alt"></i> Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped rounded">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Siswa</th>
                            <th>Guru BK</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($counselingRecords as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->record_date)->isoFormat('D MMMM Y') }}</td>
                                <td>{{ $record->student->name ?? 'N/A' }}</td>
                                <td>{{ $record->bkTeacher->name ?? 'N/A' }}</td>
                                <td>{{ Str::limit($record->notes, 100) }}</td>
                                <td>
                                    <a href="{{ route('bk.counseling_records.edit', $record->id) }}" class="btn btn-warning btn-sm rounded mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('bk.counseling_records.destroy', $record->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada catatan konseling ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $counselingRecords->links() }}
            </div>
        </div>
    </div>
@endsection

