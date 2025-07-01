@extends('admin.layouts.app')

@section('page_title', 'Edit Mata Pelajaran')

@section('content_body')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Edit Mata Pelajaran</h3>
        </div>
        <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}">
            @csrf
            @method('PUT') {{-- Penting untuk metode PUT/PATCH --}}
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nama Mata Pelajaran:</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                
                {{-- Field Tipe Mata Pelajaran --}}
                <div class="form-group">
                    <label for="type">Tipe Mata Pelajaran:</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe --</option>
                        @foreach($subjectTypes as $typeOption)
                            <option value="{{ $typeOption }}" {{ old('type', $subject->type) == $typeOption ? 'selected' : '' }}>
                                {{ ucfirst($typeOption) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                {{-- Field KTTP --}}
                <div class="form-group">
                    <label for="kktp">Nilai KTTP :</label>
                    <input type="number" name="kktp" id="kktp" class="form-control @error('kktp') is-invalid @enderror" value="{{ old('kktp', $subject->kktp) }}" min="0" max="100">
                    @error('kktp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengisi nilai KTTP.</small>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi (Opsional):</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $subject->description) }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
{{-- Script untuk mengatur default KTTP dihapus --}}
@stop