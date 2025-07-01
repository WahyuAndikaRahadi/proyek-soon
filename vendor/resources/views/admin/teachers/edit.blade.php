@extends('admin.layouts.app')

@section('page_title', 'Edit Guru: ' . $teacher->name)

@section('content_body')
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Form Edit Guru</h3>
        </div>
        <form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Nama:</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $teacher->name) }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->email) }}" required>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password (kosongkan jika tidak ingin mengubah):</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nip">NIP (Nomor Induk Pegawai):</label>
                    <input type="text" name="nip" id="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $teacher->nip) }}">
                    @error('nip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran yang Diampu:</label>
                    <div class="row">
                        @foreach ($subjects as $subject)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="subject_{{ $subject->id }}" name="subjects[]" value="{{ $subject->id }}" {{ in_array($subject->id, old('subjects', $teacherSubjects)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="subject_{{ $subject->id }}">{{ $subject->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('subjects') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Kelas yang Diampu (Wali Kelas):</label>
                    <div class="row">
                        @foreach ($classes as $class)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="class_homeroom_{{ $class->id }}" name="classes[{{ $class->id }}][is_homeroom_teacher]" value="1" {{ old('classes.' . $class->id . '.is_homeroom_teacher', $teacherClasses[$class->id]['is_homeroom_teacher'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="class_homeroom_{{ $class->id }}">Wali Kelas {{ $class->name }}</label>
                                    <input type="hidden" name="classes[{{ $class->id }}][id]" value="{{ $class->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('classes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Perbarui</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop
