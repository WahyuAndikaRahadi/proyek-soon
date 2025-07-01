@extends('admin.layouts.app')

@section('page_title', 'Edit Guru: ' . $teacher->name)

@section('content_body')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Edit Guru</h3>
        </div>
        <form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}">
            @csrf
            @method('PUT') {{-- Use PUT method for update --}}
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
                    <label for="password">Password (Biarkan kosong jika tidak ingin mengubah):</label>
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
                    <label for="photo_url">URL Foto (Opsional):</label>
                    <input type="url" name="photo_url" id="photo_url" class="form-control @error('photo_url') is-invalid @enderror" value="{{ old('photo_url', $teacher->photo_url) }}" placeholder="Contoh: https://example.com/foto_guru.jpg">
                    @error('photo_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
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
                    <label for="homeroom_class_id">Wali Kelas (Opsional):</label>
                    <select name="homeroom_class_id" id="homeroom_class_id" class="form-control @error('homeroom_class_id') is-invalid @enderror">
                        <option value="">-- Pilih Kelas Wali --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('homeroom_class_id', $teacherHomeroomClassId) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('homeroom_class_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Kelas yang Diajarkan:</label>
                    <div class="row">
                        @foreach ($classes as $class)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="taught_class_{{ $class->id }}" name="taught_classes[]" value="{{ $class->id }}" {{ in_array($class->id, old('taught_classes', $teacherTaughtClasses)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="taught_class_{{ $class->id }}">{{ $class->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('taught_classes') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui</button>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop