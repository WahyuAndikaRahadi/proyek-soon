@extends('admin.layouts.app')

@section('page_title', 'Edit Mata Pelajaran: ' . $subject->name)

@section('content_body')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Form Edit Mata Pelajaran</h3>
        </div>
        <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nama Mata Pelajaran:</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $subject->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi (Opsional):</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $subject->description) }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Perbarui</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop
