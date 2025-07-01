@extends('admin.layouts.app')

@section('page_title', 'Edit Jadwal Guru')

@section('content_body')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Form Edit Jadwal</h3>
        </div>
        <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}">
            @csrf
            @method('PUT') {{-- Penting untuk update --}}
            <div class="card-body">
                <div class="form-group">
                    <label for="user_id">Guru:</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                        <option value="">Pilih Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('user_id', $schedule->user_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="class_id">Kelas:</label>
                    <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                        <option value="">Pilih Kelas</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $schedule->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="subject_id">Mata Pelajaran:</label>
                    <select name="subject_id" id="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="day_of_week">Hari:</label>
                    <select name="day_of_week" id="day_of_week" class="form-control @error('day_of_week') is-invalid @enderror" required>
                        <option value="">Pilih Hari</option>
                        @foreach ($daysOfWeek as $day)
                            <option value="{{ $day }}" {{ old('day_of_week', $schedule->day_of_week) == $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="start_time">Jam Mulai:</label>
                    <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" required>
                    @error('start_time') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="end_time">Jam Selesai:</label>
                    <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}" required>
                    @error('end_time') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="academic_year">Tahun Ajaran (Opsional):</label>
                    <input type="text" name="academic_year" id="academic_year" class="form-control @error('academic_year') is-invalid @enderror" value="{{ old('academic_year', $schedule->academic_year) }}" placeholder="Contoh: 2024/2025">
                    @error('academic_year') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="semester">Semester (Opsional):</label>
                    <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror">
                        <option value="">Pilih Semester</option>
                        <option value="1" {{ old('semester', $schedule->semester) == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('semester', $schedule->semester) == '2' ? 'selected' : '' }}>2</option>
                    </select>
                    @error('semester') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Perbarui Jadwal</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop