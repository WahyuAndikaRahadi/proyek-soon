@extends('bk.layouts.app')

@section('page_title', 'Edit Catatan Konseling')

@section('content')
    <div class="card rounded shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top">
            <h3 class="card-title mb-0">Form Edit Catatan Konseling</h3>
            <a href="{{ route('bk.counseling_records.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.counseling_records.update', $counselingRecord->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="record_date" class="form-label">Tanggal Catatan:</label>
                    <input type="date" name="record_date" id="record_date"
                           class="form-control rounded @error('record_date') is-invalid @enderror"
                           value="{{ old('record_date', $counselingRecord->record_date->format('Y-m-d')) }}" required>
                    @error('record_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="student_id" class="form-label">Nama Siswa:</label>
                    <select name="student_id" id="student_id"
                            class="form-control rounded @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                {{ old('student_id', $counselingRecord->student_id) == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->class->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">Catatan Konseling:</label>
                    <textarea name="notes" id="notes"
                              class="form-control rounded @error('notes') is-invalid @enderror"
                              rows="7" placeholder="Masukkan catatan konseling secara detail..." required>{{ old('notes', $counselingRecord->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success rounded-pill px-4 mt-4">
                    <i class="fas fa-save mr-1"></i> Perbarui Catatan
                </button>
            </form>
        </div>
    </div>
@endsection

