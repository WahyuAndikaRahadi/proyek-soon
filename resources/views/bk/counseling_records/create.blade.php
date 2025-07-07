@extends('bk.layouts.app')

@section('page_title', 'Tambah Catatan Konseling Baru')

@section('content')
    <div class="card rounded">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Catatan Konseling</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('bk.counseling_records.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="record_date">Tanggal Catatan:</label>
                    <input type="date" name="record_date" id="record_date" class="form-control rounded @error('record_date') is-invalid @enderror" value="{{ old('record_date', $recordDate) }}" readonly>
                    @error('record_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="student_id">Nama Siswa:</label>
                    <select name="student_id" id="student_id" class="form-control rounded @error('student_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->name }} ({{ $student->class->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="notes">Catatan Konseling:</label>
                    <textarea name="notes" id="notes" class="form-control rounded @error('notes') is-invalid @enderror" rows="5" placeholder="Masukkan catatan konseling..." required>{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary rounded"><i class="fas fa-save"></i> Simpan</button>
                <a href="{{ route('bk.counseling_records.index') }}" class="btn btn-secondary rounded ml-2"><i class="fas fa-arrow-left"></i> Kembali</a>
            </form>
        </div>
    </div>
@endsection

