@extends('teacher.layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
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

    /* Gaya Dropdown Pilihan yang Ditingkatkan */
    select {
        appearance: none;
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
        padding-right: 2.5rem;
        transition: all 0.3s ease;
    }

    .select-default {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        background-color: white;
        color: #374151;
        border-color: #d1d5db;
    }

    /* Gaya Tabel */
    .attitude-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attitude-table thead th {
        background-color: #4f46e5;
        color: white;
        padding: 12px 16px;
        text-align: left;
        font-weight: 500;
    }

    .attitude-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .attitude-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .attitude-table td {
        padding: 12px 16px;
        vertical-align: middle;
    }

    .attitude-table .student-info {
        display: flex;
        flex-direction: column;
    }

    .attitude-table .student-name {
        font-weight: 500;
        color: #111827;
    }

    .attitude-table .student-nis {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .attitude-table .notes-cell {
        min-width: 250px;
    }

    /* Gaya untuk checkbox */
    input[type="checkbox"] {
        @apply h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .attitude-table thead {
            display: none;
        }

        .attitude-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .attitude-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .attitude-table td:last-child {
            border-bottom: none;
        }

        .attitude-table td::before {
            content: attr(data-label);
            font-weight: 500;
            color: #374151;
            margin-right: 1rem;
        }
    }

    /* Gaya Kartu Catatan Sikap */
    .attitude-card {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    /* Gaya fokus kustom */
    .select-focus:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const { animate, stagger } = Motion;

        // Animasikan elemen halaman
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.attitude-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4, delay: 0.1 });

        // Animasikan baris tabel catatan sikap jika terlihat
        if (document.querySelector('.attitude-table')) {
            animate('.attitude-table tbody tr',
                { opacity: [0, 1], x: [-10, 0] },
                { delay: stagger(0.05, { startDelay: 0.3 }), duration: 0.3 }
            );
        }

        // Efek hover tombol
        const buttons = document.querySelectorAll('button[type="submit"], a[href]');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                animate(button, { scale: 1.03 }, { duration: 0.2 });
            });
            button.addEventListener('mouseleave', () => {
                animate(button, { scale: 1 }, { duration: 0.2 });
            });
        });

        // Efek fokus dropdown pilihan
        const selectInputs = document.querySelectorAll('select');
        selectInputs.forEach(input => {
            input.addEventListener('focus', () => {
                animate(input, { scale: 1.01, borderColor: '#6366f1' }, { duration: 0.2 });
            });
            input.addEventListener('blur', () => {
                animate(input, { scale: 1, borderColor: '#d1d5db' }, { duration: 0.2 });
            });
        });

        // Efek fokus textarea
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('focus', () => {
                animate(textarea, { scale: 1.01, borderColor: '#6366f1' }, { duration: 0.2 });
            });
            textarea.addEventListener('blur', () => {
                animate(textarea, { scale: 1, borderColor: '#d1d5db' }, { duration: 0.2 });
            });
        });

        // Otomatis kirim untuk filter
        document.getElementById('record_date_selector')?.addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });

        document.getElementById('schedule_id')?.addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });

        // Logika untuk mengaktifkan/menonaktifkan textarea berdasarkan checkbox
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        studentCheckboxes.forEach(checkbox => {
            const textarea = document.getElementById('attitude_notes_' + checkbox.value);
            // Nonaktifkan textarea jika checkbox tidak dicentang saat halaman dimuat
            if (textarea) {
                textarea.disabled = !checkbox.checked;
            }

            checkbox.addEventListener('change', function() {
                if (textarea) {
                    textarea.disabled = !this.checked;
                    if (!this.checked) {
                        textarea.value = ''; // Kosongkan textarea jika tidak dicentang
                    }
                }
            });
        });

        // Konfirmasi SweetAlert untuk pengiriman formulir
        document.getElementById('attitudeRecordForm')?.addEventListener('submit', function(event) {
            event.preventDefault();

            // Cek apakah ada setidaknya satu siswa yang dipilih dan catatannya tidak kosong
            let isAnyStudentSelectedAndNotesFilled = false;
            studentCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const textarea = document.getElementById('attitude_notes_' + checkbox.value);
                    if (textarea && textarea.value.trim() !== '') {
                        isAnyStudentSelectedAndNotesFilled = true;
                    }
                }
            });

            if (!isAnyStudentSelectedAndNotesFilled) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan!',
                    text: 'Pilih setidaknya satu siswa dan isi catatan sikapnya.',
                    confirmButtonColor: '#dc3545',
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Catatan Sikap?',
                text: "Pastikan data yang dimasukkan sudah benar",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });

        // SweetAlert untuk menampilkan error dari server
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        @endif
    });
</script>

<div class="mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-clipboard-list text-xl"></i> {{-- Ikon catatan sikap --}}
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Catatan Sikap Siswa</h1>
            <p class="text-gray-600">Pilih tanggal dan jadwal untuk mencatat sikap siswa</p>
        </div>
    </div>

    <div class="attitude-card bg-white mb-6">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Tanggal dan Jadwal</h3>
        </div>
        <div class="p-6">
            <form id="schedule_selection_form" action="{{ route('teacher.attitude_records.create') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="record_date_selector" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Tanggal Catatan
                        </label>
                        <select name="record_date" id="record_date_selector"
                            class="w-full p-3 border border-gray-300 rounded-lg select-default select-focus">
                            @foreach ($availableDates as $value => $label)
                                <option value="{{ $value }}" {{ $selectedRecordDateString == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="schedule_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> Jadwal pada Hari Terpilih
                        </label>
                        <select name="schedule_id" id="schedule_id"
                            class="w-full p-3 border border-gray-300 rounded-lg select-default select-focus">
                            <option value="">-- Pilih Jadwal --</option>
                            @forelse ($schedulesOnSelectedDay as $schedule)
                                <option value="{{ $schedule->id }}" {{ $selectedSchedule && $selectedSchedule->id == $schedule->id ? 'selected' : '' }}>
                                    ( Jam ke-{{ $schedule->at_time }} ) {{ $schedule->class->name ?? 'N/A' }}
                                    ({{ Carbon\Carbon::parse($schedule->start_time)->format('H:i') }})
                                </option>
                            @empty
                                <option disabled>Tidak ada jadwal.
                                    </option>
                            @endforelse
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedSchedule)
    <div class="attitude-card bg-white mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4">
                <p class="info-label"><i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran</p>
                <p class="info-value mt-1">{{ $selectedSchedule->subject->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-users mr-2 text-indigo-500"></i> Kelas</p>
                <p class="info-value mt-1">{{ $selectedSchedule->class->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Jam Ke-</p>
                <p class="info-value mt-1">{{ $selectedSchedule->at_time ?? '-' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-clock mr-2 text-indigo-500"></i> Waktu</p>
                <p class="info-value mt-1">
                    {{ Carbon\Carbon::parse($selectedSchedule->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($selectedSchedule->end_time)->format('H:i') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    @if ($selectedSchedule)
    <div class="attitude-card bg-white">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">
                Catatan Sikap Siswa Kelas {{ $selectedSchedule->class->name ?? 'N/A' }}
                <span class="block text-base font-normal">({{ Carbon\Carbon::parse($selectedRecordDateString)->isoFormat('dddd, D MMMM Y') }})</span>
            </h3>
        </div>

        <form method="POST" action="{{ route('teacher.attitude_records.store') }}" id="attitudeRecordForm">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
            <input type="hidden" name="record_date" value="{{ $selectedRecordDateString }}">

            <div class="p-6">
                @if ($students->isEmpty())
                    <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                        <div>
                            <strong class="font-bold">Info!</strong>
                            <span class="block">Tidak ada siswa yang terdaftar di kelas ini.</span>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="attitude-table">
                            <thead>
                                <tr>
                                    <th>Pilih</th>
                                    <th>Siswa</th>
                                    <th class="notes-cell">Catatan Sikap</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        // Cek apakah ada catatan sikap yang sudah ada untuk siswa ini
                                        $existingRecord = $existingAttitudeRecords->get($student->id);
                                        // Tentukan apakah checkbox harus dicentang (jika ada catatan yang sudah ada atau ada old input)
                                        $isChecked = old('selected_students.' . $student->id, $existingRecord ? true : false);
                                        // Ambil catatan yang sudah ada atau old input
                                        $currentNotes = old('attitude_records.' . $student->id . '.attitude_notes', $existingRecord ? $existingRecord->attitude_notes : '');
                                    @endphp
                                    <tr>
                                        <td data-label="Pilih">
                                            <input type="checkbox"
                                                   name="selected_students[{{ $student->id }}]"
                                                   value="{{ $student->id }}"
                                                   class="student-checkbox"
                                                   {{ $isChecked ? 'checked' : '' }}>
                                            <input type="hidden" name="student_names[{{ $student->id }}]" value="{{ $student->name }}">
                                        </td>
                                        <td data-label="Siswa">
                                            <div class="student-info">
                                                <span class="student-name">{{ $student->name }}</span>
                                                <span class="student-nis">NIS: {{ $student->nis }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Catatan Sikap" class="notes-cell">
                                            <textarea id="attitude_notes_{{ $student->id }}"
                                                      name="attitude_records[{{ $student->id }}][attitude_notes]"
                                                      rows="2"
                                                      class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                                                      placeholder="Catatan sikap siswa">{{ $currentNotes }}</textarea>
                                            @error('attitude_records.' . $student->id . '.attitude_notes')
                                                <p class="text-red-500 text-xs italic mt-1">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                                </p>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            @if (!$students->isEmpty())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-5 rounded-lg shadow-md transition duration-200">
                        <i class="fas fa-save mr-2"></i> Simpan Catatan Sikap
                    </button>
                    <a href="{{ route('teacher.attitude_records.history') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-5 rounded-lg shadow-md transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            @endif
        </form>
    </div>
    @elseif ($schedulesOnSelectedDay->isEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Info!</strong>
                <span class="block">Tidak ada jadwal mengajar untuk tanggal yang dipilih.</span>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Perhatian!</strong>
                <span class="block">Silakan pilih tanggal dan jadwal di atas.</span>
            </div>
        </div>
    @endif
</div>
@endsection

