@extends('teacher.layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
{{-- Tambahkan SweetAlert2 CDN jika belum ada --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8fafc;
    }

    .gradient-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .assessment-card {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    /* Style untuk dropdown (select) */
    select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
        padding-right: 2.5rem;
    }

    select:focus {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234338ca'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
    }

    .btn {
        position: relative;
        overflow: hidden;
    }

    .btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s, height 0.3s;
    }

    .btn:active::after {
        width: 200px;
        height: 200px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .input-focus {
        transition: all 0.2s ease;
    }

    .input-focus:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .student-row:hover {
        background-color: #f9fafb;
    }

    @media (max-width: 640px) {
        .mobile-stack {
            display: flex;
            flex-direction: column;
        }

        .mobile-stack .mobile-full {
            width: 100%;
            margin-bottom: 1rem;
        }
    }
</style>
<script>
    const { animate, stagger } = Motion;

    // Define assessment types based on semester
    const assessmentTypes = {
        '1': ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS1', 'SAS'], // Tipe untuk Semester 1
        '2': ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS2', 'SAT']  // Tipe untuk Semester 2
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.assessment-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });

        // Stagger animations for form elements
        animate('.form-section > *',
            { opacity: [0, 1], x: [-10, 0] },
            { delay: stagger(0.1), duration: 0.3 }
        );

        // Animate table rows
        animate('.student-row',
            { opacity: [0, 1], y: [10, 0] },
            { delay: stagger(0.05, { startDelay: 0.3 }), duration: 0.3 }
        );

        // Button hover effects
        const buttons = document.querySelectorAll('button, a.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                animate(button, { scale: 1.02 }, { duration: 0.2 });
            });
            button.addEventListener('mouseleave', () => {
                animate(button, { scale: 1 }, { duration: 0.2 });
            });
        });

        // --- Logic untuk Tipe Penilaian Dinamis ---
        const semesterSelect = document.getElementById('semester');
        const typeSelect = document.getElementById('type');

        function updateAssessmentTypes() {
            const selectedSemester = semesterSelect.value;
            const typesForSemester = assessmentTypes[selectedSemester] || [];
            
            // Clear existing options
            typeSelect.innerHTML = '<option value="">Pilih Tipe Penilaian</option>';

            // Add new options
            typesForSemester.forEach(type => {
                const option = document.createElement('option');
                option.value = type;
                option.textContent = type;
                
                // Keep previously selected value if it exists and matches
                if ("{{ old('type') }}" === type) {
                    option.selected = true;
                }
                typeSelect.appendChild(option);
            });

            // If old('type') is set but not in the current semester's options, 
            // ensure it's reset or handled appropriately.
            if (typeSelect.selectedIndex === 0 && "{{ old('type') }}" !== '') {
                 // Option: Reset to default or add old value as selected if it was valid before
                 // For now, it will just default to "Pilih Tipe Penilaian" if old value isn't in new list.
            }
        }

        // Add event listener to semester select
        if (semesterSelect && typeSelect) {
            semesterSelect.addEventListener('change', updateAssessmentTypes);
            
            // Initial call to populate on page load if a semester is already selected
            if (semesterSelect.value) {
                updateAssessmentTypes();
            }
        }
        // --- Akhir Logic Tipe Penilaian Dinamis ---
    });
</script>
<div class="mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500 pb-4">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-clipboard-check text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Penilaian Siswa</h1>
            <p class="text-gray-600">Input nilai siswa untuk kelas yang diajar</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">Ada beberapa masalah dengan input Anda atau saat mengunggah template.</span>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="assessment-card bg-white mb-8">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Kelas Penilaian</h3>
        </div>

        {{-- Form untuk memilih kelas --}}
        <form method="GET" action="{{ route('teacher.assessments.create') }}" id="selectClassForm">
            <div class="p-6 form-section">
                <div class="mb-6">
                    <label for="class_id" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-users mr-2 text-indigo-500"></i> Pilih Kelas
                    </label>
                    <select name="class_id" id="class_id"
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                        onchange="document.getElementById('selectClassForm').submit()">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($teacherClasses as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                        </p>
                    @enderror
                </div>
            </div>
        </form>
    </div>
    
    <div class="assessment-card bg-white mb-8">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Download dan Unggah Template Excel</h3>
        </div>

        {{-- Bagian untuk download template Excel --}}
        @if ($selectedClass)
            <div class="p-6 border-t border-gray-100 form-section">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i> Download Template Excel
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <a href="{{ route('teacher.assessments.downloadTemplate', ['class_id' => $selectedClass->id, 'semester' => 1]) }}"
                       class="btn bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i> Template Semester 1 (SAS)
                    </a>
                    <a href="{{ route('teacher.assessments.downloadTemplate', ['class_id' => $selectedClass->id, 'semester' => 2]) }}"
                       class="btn bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i> Template Semester 2 (SAT)
                    </a>
                </div>
            </div>

            {{-- Bagian untuk Unggah File Excel Nilai --}}
            <div class="p-6 border-t border-gray-100 form-section">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-upload mr-2 text-blue-600"></i> Unggah File Excel Nilai
                </h4>
                <p class="text-sm text-gray-600 mb-4">
                    Unggah file Excel yang sudah Anda unduh dan isi nilai-nilainya.
                    Pastikan <b>nama kolom penilaian</b> (misalnya, Tugas 1, SAS) di Excel sama persis dengan yang Anda ingin simpan.
                </p>

                <form action="{{ route('teacher.assessments.importExcel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label for="upload_subject_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran
                            </label>
                            <select name="subject_id" id="upload_subject_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach ($teacherSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="upload_semester" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Semester
                            </label>
                            <select name="semester" id="upload_semester"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                required>
                                <option value="">Pilih Semester</option>
                                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                            </select>
                            @error('semester')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>
                        <div class="md:col-span-2 lg:col-span-2">
                            <label for="upload_file" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-file-upload mr-2 text-blue-500"></i> Pilih File Excel (.xlsx)
                            </label>
                            <div id="dropzone" class="flex items-center justify-center w-full">
                                <label for="upload_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-blue-400 rounded-lg cursor-pointer bg-blue-50 hover:bg-blue-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg aria-hidden="true" class="w-8 h-8 mb-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4a1 1 0 011-1h8a1 1 0 011 1v12m-4 4h-4a1 1 0 01-1-1v-4h6v4a1 1 0 01-1 1z" />
                                        </svg>
                                        <p class="mb-2 text-sm text-blue-600"><span class="font-semibold">Klik untuk pilih</span> atau drag & drop</p>
                                        <p class="text-xs text-gray-500">.xlsx (max 2MB)</p>
                                        <span id="file-name" class="text-xs text-gray-700 mt-2"></span>
                                    </div>
                                    <input id="upload_file" type="file" name="excel_file" accept=".xlsx" class="hidden" required>
                                </label>
                            </div>
                            @error('excel_file')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>
                        <div class="md:col-span-2 lg:col-span-4">
                            <label for="upload_date" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> Tanggal Penilaian
                            </label>
                            <input type="date" name="date" id="upload_date"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                value="{{ old('date', \Carbon\Carbon::now()->toDateString()) }}" required>
                            @error('date')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-file-import mr-2"></i> Unggah & Proses Nilai
                    </button>
                </form>
            </div>
            <script>
                // Drag and drop logic
                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('upload_file');
                const fileName = document.getElementById('file-name');

                dropzone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    dropzone.classList.add('bg-blue-100', 'border-blue-600');
                });

                dropzone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('bg-blue-100', 'border-blue-600');
                });

                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    dropzone.classList.remove('bg-blue-100', 'border-blue-600');
                    if (e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        fileName.textContent = e.dataTransfer.files[0].name;
                    }
                });

                fileInput.addEventListener('change', function(e) {
                    if (fileInput.files.length) {
                        fileName.textContent = fileInput.files[0].name;
                    } else {
                        fileName.textContent = '';
                    }
                });
            </script>
        @endif
    </div>
    <div class="assessment-card bg-white mb-8">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Form Penilaian</h3>
        </div>

        {{-- Form utama untuk menginput nilai siswa (manual) --}}
        @if ($selectedClass && $students->isNotEmpty())
            <form method="POST" action="{{ route('teacher.assessments.store') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">

                <div class="p-6 border-t border-gray-100 form-section">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="subject_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran
                            </label>
                            <select name="subject_id" id="subject_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                required>
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach ($teacherSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="semester" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Semester
                            </label>
                            {{-- Tambahkan ID untuk JavaScript --}}
                            <select name="semester" id="semester"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                required>
                                <option value="">Pilih Semester</option>
                                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                            </select>
                            @error('semester')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-tasks mr-2 text-indigo-500"></i> Tipe Penilaian
                            </label>
                            {{-- Ubah ini menjadi select dengan ID untuk JavaScript --}}
                            <select name="type" id="type"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('type') border-red-500 @enderror"
                                required>
                                <option value="">Pilih Tipe Penilaian</option>
                                {{-- Opsi akan diisi oleh JavaScript --}}
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="date" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> Tanggal
                            </label>
                            <input type="date" name="date" id="date"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                value="{{ old('date', \Carbon\Carbon::now()->toDateString()) }}" required>
                            @error('date')
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-user-graduate mr-2 text-indigo-500"></i>
                            Daftar Siswa Kelas {{ $selectedClass->name }}
                        </label>

                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($students as $student)
                                        <tr class="student-row transition duration-150">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $student->nis }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="number" step="0.01" min="0" max="100"
                                                    name="scores[{{ $student->id }}][score]"
                                                    class="input-focus w-20 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('scores.'.$student->id.'.score') border-red-500 @enderror"
                                                    value="{{ old('scores.'.$student->id.'.score') }}" required>
                                                <input type="hidden" name="scores[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                                @error('scores.'.$student->id.'.score')
                                                    <p class="text-red-500 text-xs italic mt-1">{{ $message ?? '' }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <input type="text" name="scores[{{ $student->id }}][notes]"
                                                    class="input-focus w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                                    value="{{ old('scores.'.$student->id.'.notes') }}"
                                                    placeholder="Catatan">
                                                @error('scores.'.$student->id.'.notes')
                                                    <p class="text-red-500 text-xs italic mt-1">{{ $message ?? '' }}</p>
                                                @enderror
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Simpan Penilaian
                    </button>
                    <a href="{{ route('teacher.assessments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </form>
        @elseif ($selectedClass && $students->isEmpty())
            <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 m-6 rounded-lg flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                <div>
                    <strong class="font-bold">Info!</strong>
                <span class="block">Tidak ada siswa di kelas ini.</span>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 m-6 rounded-lg flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
                <div>
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block">Silakan pilih kelas terlebih dahulu untuk mencatat penilaian.</span>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection