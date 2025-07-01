@extends('admin.layouts.app')

@section('page_title', 'Tambah Jadwal Guru')

@section('content_body')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Jadwal</h3>
        </div>
        <form method="POST" action="{{ route('admin.schedules.store') }}" id="scheduleForm">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="user_id">Guru:</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                        <option value="">Pilih Guru</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('user_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="class_id">Kelas Diajarkan:</label>
                    <select name="class_id" id="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                        <option value="">Pilih Kelas (Pilih Guru Dulu)</option>
                        {{-- Options will be loaded dynamically by JS --}}
                    </select>
                    @error('class_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="subject_id">Mata Pelajaran Diampu:</label>
                    <select name="subject_id" id="subject_id" class="form-control @error('subject_id') is-invalid @enderror" required>
                        <option value="">Pilih Mata Pelajaran (Pilih Guru Dulu)</option>
                        {{-- Options will be loaded dynamically by JS --}}
                    </select>
                    @error('subject_id') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="day_of_week">Hari:</label>
                    <select name="day_of_week" id="day_of_week" class="form-control @error('day_of_week') is-invalid @enderror" required>
                        <option value="">Pilih Hari</option>
                        @foreach ($daysOfWeek as $day)
                            <option value="{{ $day }}" {{ old('day_of_week') == $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="at_time">Jam Ke / Sesi (Opsional):</label> {{-- Kolom baru --}}
                    <input type="text" name="at_time" id="at_time" class="form-control @error('at_time') is-invalid @enderror" value="{{ old('at_time') }}" placeholder="Contoh: Jam ke-1, Sesi Pagi">
                    @error('at_time') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="start_time">Jam Mulai:</label>
                    <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                    @error('start_time') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="end_time">Jam Selesai:</label>
                    <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                    @error('end_time') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="academic_year">Tahun Ajaran (Opsional):</label>
                    <input type="text" name="academic_year" id="academic_year" class="form-control @error('academic_year') is-invalid @enderror" value="{{ old('academic_year') }}" placeholder="Contoh: 2024/2025">
                    @error('academic_year') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="semester">Semester (Opsional):</label>
                    <select name="semester" id="semester" class="form-control @error('semester') is-invalid @enderror">
                        <option value="">Pilih Semester</option>
                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>2</option>
                    </select>
                    @error('semester') <span class="invalid-feedback">{{ $message ?? '' }}</span> @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Jadwal</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userIdSelect = document.getElementById('user_id');
        const classIdSelect = document.getElementById('class_id');
        const subjectIdSelect = document.getElementById('subject_id');

        // Mengubah data teachers dari PHP ke JavaScript
        const allTeachersData = @json($teachers->keyBy('id')); // Mengubah koleksi menjadi objek JavaScript dengan ID sebagai kunci

        function populateTeacherRelations(teacherId, selectedClassId = null, selectedSubjectId = null) {
            classIdSelect.innerHTML = '<option value="">Pilih Kelas (Pilih Guru Dulu)</option>';
            subjectIdSelect.innerHTML = '<option value="">Pilih Mata Pelajaran (Pilih Guru Dulu)</option>';
            classIdSelect.disabled = true;
            subjectIdSelect.disabled = true;

            if (!teacherId) {
                return;
            }

            const teacher = allTeachersData[teacherId];

            if (teacher) {
                // Populate Classes
                classIdSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                teacher.classes.forEach(classItem => {
                    const option = document.createElement('option');
                    option.value = classItem.id;
                    option.textContent = classItem.name;
                    if (selectedClassId && selectedClassId == classItem.id) {
                        option.selected = true;
                    }
                    classIdSelect.appendChild(option);
                });
                classIdSelect.disabled = false;

                // Populate Subjects
                subjectIdSelect.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';
                teacher.subjects.forEach(subjectItem => {
                    const option = document.createElement('option');
                    option.value = subjectItem.id;
                    option.textContent = subjectItem.name;
                    if (selectedSubjectId && selectedSubjectId == subjectItem.id) {
                        option.selected = true;
                    }
                    subjectIdSelect.appendChild(option);
                });
                subjectIdSelect.disabled = false;
            } else {
                console.warn('Teacher data not found for ID:', teacherId);
            }
        }

        userIdSelect.addEventListener('change', function() {
            const selectedTeacherId = this.value;
            populateTeacherRelations(selectedTeacherId);
        });

        // Pemuatan awal untuk halaman create jika ada old input
        @if(old('user_id'))
            populateTeacherRelations('{{ old('user_id') }}', '{{ old('class_id') }}', '{{ old('subject_id') }}');
        @endif
        // Inisialisasi awal jika tidak ada old input
        if (!userIdSelect.value) {
            classIdSelect.innerHTML = '<option value="">Pilih Kelas (Pilih Guru Dulu)</option>';
            subjectIdSelect.innerHTML = '<option value="">Pilih Mata Pelajaran (Pilih Guru Dulu)</option>';
            classIdSelect.disabled = true;
            subjectIdSelect.disabled = true;
        }
    });
</script>
@endpush
