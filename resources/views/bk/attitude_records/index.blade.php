@extends('bk.layouts.app')

@section('page_title', 'Catatan Sikap Siswa')

@section('content')
    <div class="card rounded">
        <div class="card-header">
            <h3 class="card-title">Daftar Catatan Sikap Siswa</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.attitude_records.index') }}" method="GET" class="form-inline mb-4">
                <div class="form-group mr-2 mb-2">
                    <label for="start_date" class="mr-1">Dari Tanggal:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control rounded" value="{{ $request->start_date }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="end_date" class="mr-1">Sampai Tanggal:</label>
                    <input type="date" name="end_date" id="end_date" class="form-control rounded" value="{{ $request->end_date }}">
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="class_id" class="mr-1">Kelas:</label>
                    <select name="class_id" id="class_id" class="form-control rounded">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $request->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="subject_id" class="mr-1">Mata Pelajaran:</label>
                    <select name="subject_id" id="subject_id" class="form-control rounded">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mr-2 mb-2">
                    <label for="teacher_id" class="mr-1">Guru Pencatat:</label>
                    <select name="teacher_id" id="teacher_id" class="form-control rounded">
                        <option value="">-- Semua Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $request->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
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
                <button type="submit" class="btn btn-primary rounded mb-2"><i class="fas fa-filter"></i> Filter</button>
                <a href="{{ route('bk.attitude_records.index') }}" class="btn btn-secondary rounded ml-2 mb-2"><i class="fas fa-sync-alt"></i> Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped rounded">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Guru Pencatat</th>
                            <th>Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Catatan Sikap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attitudeRecords as $record)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->record_date)->isoFormat('D MMMM Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($record->end_time)->format('H:i') }}</td>
                                <td>{{ $record->teacher->name ?? 'N/A' }}</td>
                                <td>{{ $record->student->name ?? 'N/A' }}</td>
                                <td>{{ $record->schedule->class->name ?? 'N/A' }}</td>
                                <td>{{ $record->schedule->subject->name ?? 'N/A' }}</td>
                                <td>{{ $record->attitude_notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada catatan sikap ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $attitudeRecords->links() }}
            </div>
        </div>
    </div>
@endsection

