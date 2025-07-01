@extends('admin.layouts.app')

@section('page_title', 'Edit Kelas: ' . $class->name)

@section('content_body')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kelas</h3>
        </div>
        <form method="POST" action="{{ route('admin.classes.update', $class->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nama Kelas:</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $class->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="grade_level">Tingkat Kelas:</label>
                    <input type="number" name="grade_level" id="grade_level" class="form-control @error('grade_level') is-invalid @enderror" value="{{ old('grade_level', $class->grade_level) }}" required min="1" max="12">
                    @error('grade_level') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Perbarui</button>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop
