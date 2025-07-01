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

    /* Enhanced Select Dropdown Styles */
    select {
        appearance: none;
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1em;
        padding-right: 2.5rem;
        transition: all 0.3s ease;
    }

    /* Status-specific dropdown styles */
    .select-hadir {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23166534'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        background-color: #f0fdf4;
        color: #166534;
        border-color: #4ade80;
    }

    .select-sakit {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23854d0e'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        background-color: #fefce8;
        color: #854d0e;
        border-color: #facc15;
    }

    .select-izin {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%231e40af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C%2Fsvg%3E");
        background-color: #eff6ff;
        color: #1e40af;
        border-color: #60a5fa;
    }

    .select-alpha {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23991b1b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        background-color: #fef2f2;
        color: #991b1b;
        border-color: #f87171;
    }

    .select-default {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        background-color: white;
        color: #374151;
        border-color: #d1d5db;
    }

    /* Table Styles */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead th {
        background-color: #4f46e5;
        color: white;
        padding: 12px 16px;
        text-align: left;
        font-weight: 500;
    }

    .attendance-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .attendance-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .attendance-table td {
        padding: 12px 16px;
        vertical-align: middle;
    }

    .attendance-table .student-info {
        display: flex;
        flex-direction: column;
    }

    .attendance-table .student-name {
        font-weight: 500;
        color: #111827;
    }

    .attendance-table .student-nis {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .attendance-table .status-cell {
        min-width: 150px;
    }

    .attendance-table .notes-cell {
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .attendance-table thead {
            display: none;
        }

        .attendance-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }

        .attendance-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .attendance-table td:last-child {
            border-bottom: none;
        }

        .attendance-table td::before {
            content: attr(data-label);
            font-weight: 500;
            color: #374151;
            margin-right: 1rem;
        }
    }

    /* Attendance Card Styles */
    .attendance-card {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    /* Custom focus styles */
    .select-focus:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const { animate, stagger } = Motion; // Destructure animate and stagger from Motion.js

        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.attendance-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4, delay: 0.1 });

        // Animate the attendance table rows if it's visible
        if (document.querySelector('.attendance-table')) {
            animate('.attendance-table tbody tr',
                { opacity: [0, 1], x: [-10, 0] },
                { delay: stagger(0.05, { startDelay: 0.3 }), duration: 0.3 }
            );
        }

        // Button hover effects
        const buttons = document.querySelectorAll('button[type="submit"], a[href]'); // Target submit buttons and links with href
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                animate(button, { scale: 1.03 }, { duration: 0.2 });
            });
            button.addEventListener('mouseleave', () => {
                animate(button, { scale: 1 }, { duration: 0.2 });
            });
        });

        // Select dropdown focus effects (using similar logic to time inputs)
        const selectInputs = document.querySelectorAll('select');
        selectInputs.forEach(input => {
            input.addEventListener('focus', () => {
                animate(input, { scale: 1.01, borderColor: '#6366f1' }, { duration: 0.2 });
            });
            input.addEventListener('blur', () => {
                animate(input, { scale: 1, borderColor: '#d1d5db' }, { duration: 0.2 });
            });
        });

        // Textarea focus effects
        const textareas = document.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('focus', () => {
                animate(textarea, { scale: 1.01, borderColor: '#6366f1' }, { duration: 0.2 });
            });
            textarea.addEventListener('blur', () => {
                animate(textarea, { scale: 1, borderColor: '#d1d5db' }, { duration: 0.2 });
            });
        });


        // Function to update select dropdown appearance based on selected value
        function updateSelectAppearance(selectElement) {
            // Remove all status classes
            selectElement.classList.remove(
                'select-default',
                'select-hadir',
                'select-sakit',
                'select-izin',
                'select-alpha'
            );

            // Add appropriate class based on selected value
            switch(selectElement.value.toLowerCase()) {
                case 'hadir':
                    selectElement.classList.add('select-hadir');
                    break;
                case 'sakit':
                    selectElement.classList.add('select-sakit');
                    break;
                case 'izin':
                    selectElement.classList.add('select-izin');
                    break;
                case 'alpha':
                    selectElement.classList.add('select-alpha');
                    break;
                default:
                    selectElement.classList.add('select-default');
            }

            // Toggle notes visibility
            const studentId = selectElement.dataset.studentId;
            const notesContainer = document.getElementById(`notes_container_${studentId}`);
            const notesInput = document.getElementById(`notes_${studentId}`);

            if (selectElement.value === 'Hadir') {
                notesContainer.classList.add('hidden');
                notesInput.value = ''; // Clear notes if status is Hadir
            } else {
                notesContainer.classList.remove('hidden');
            }
        }

        // Initialize all select dropdowns
        document.querySelectorAll('.attendance-status-select').forEach(select => {
            // Set initial appearance
            updateSelectAppearance(select);

            // Add change event listener
            select.addEventListener('change', function() {
                updateSelectAppearance(this);
            });
        });

        // Auto-submit for filters
        document.getElementById('attendance_date_selector')?.addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });

        document.getElementById('schedule_id')?.addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });

        // SweetAlert confirmation for form submission
        document.getElementById('attendanceForm')?.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Simpan Absensi?',
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
    });
</script>

<div class="mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-user-check text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Absensi Siswa</h1>
            <p class="text-gray-600">Pilih tanggal dan jadwal untuk mencatat kehadiran siswa</p>
        </div>
    </div>

    <div class="attendance-card bg-white mb-6">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Tanggal dan Jadwal</h3>
        </div>
        <div class="p-6">
            <form id="schedule_selection_form" action="{{ route('teacher.attendances.create') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="attendance_date_selector" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Tanggal Absensi
                        </label>
                        <select name="attendance_date" id="attendance_date_selector"
                            class="w-full p-3 border border-gray-300 rounded-lg select-default select-focus">
                            @foreach ($availableDates as $value => $label)
                                <option value="{{ $value }}" {{ $selectedAttendanceDateString == $value ? 'selected' : '' }}>
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
                                <option disabled>Tidak ada jadwal.</option>
                            @endforelse
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedSchedule)
    <div class="attendance-card bg-white mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4"> {{-- Changed to 4 columns --}}
            <div class="p-4">
                <p class="info-label"><i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran</p>
                <p class="info-value mt-1">{{ $selectedSchedule->subject->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-users mr-2 text-indigo-500"></i> Kelas</p>
                <p class="info-value mt-1">{{ $selectedSchedule->class->name ?? 'N/A' }}</p>
            </div>
            {{-- Added Jam Ke- display block --}}
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
    <div class="attendance-card bg-white">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">
                Absensi Siswa Kelas {{ $selectedSchedule->class->name ?? 'N/A' }}
                <span class="block text-base font-normal">({{ Carbon\Carbon::parse($selectedAttendanceDateString)->isoFormat('dddd, D MMMM Y') }})</span>
            </h3>
        </div>

        <form method="POST" action="{{ route('teacher.attendances.store') }}" id="attendanceForm">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
            <input type="hidden" name="attendance_date" value="{{ $selectedAttendanceDateString }}">

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
                    @error('attendances')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                            <strong class="font-bold">Error!</strong>
                            <span>Harap isi status absensi untuk setiap siswa.</span>
                        </div>
                    @enderror

                    <div class="overflow-x-auto">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th class="status-cell">Status</th>
                                    <th class="notes-cell">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $student)
                                    @php
                                        // Retrieve existing attendance for this student if available
                                        $currentAttendance = $existingAttendances->get($student->id);
                                        // Set current status: check old input first, then existing, then default to 'Hadir'
                                        $currentStatus = old('attendances.' . $student->id . '.status', $currentAttendance ? $currentAttendance->status : 'Hadir');
                                        // Set current notes: check old input first, then existing
                                        $currentNotes = old('attendances.' . $student->id . '.notes', $currentAttendance ? $currentAttendance->notes : '');
                                    @endphp
                                    <tr>
                                        <td data-label="Siswa">
                                            <div class="student-info">
                                                <span class="student-name">{{ $student->name }}</span>
                                                <span class="student-nis">NIS: {{ $student->nis }}</span>
                                            </div>
                                        </td>
                                        <td data-label="Status Kehadiran" class="status-cell">
                                            <select id="status_{{ $student->id }}"
                                                    name="attendances[{{ $student->id }}][status]"
                                                    data-student-id="{{ $student->id }}"
                                                    class="attendance-status-select w-full p-2.5 border rounded-lg text-sm font-medium select-focus"
                                                    required>
                                                <option value="">Pilih Status</option>
                                                <option value="Hadir" {{ $currentStatus == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="Sakit" {{ $currentStatus == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                                <option value="Izin" {{ $currentStatus == 'Izin' ? 'selected' : '' }}>Izin</option>
                                                <option value="Alpha" {{ $currentStatus == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                                            </select>
                                            @error('attendances.' . $student->id . '.status')
                                                <p class="text-red-500 text-xs italic mt-1">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                                </p>
                                            @enderror
                                        </td>
                                        <td data-label="Catatan" id="notes_container_{{ $student->id }}" class="notes-cell">
                                            <textarea id="notes_{{ $student->id }}"
                                                      name="attendances[{{ $student->id }}][notes]"
                                                      rows="1"
                                                      class="w-full p-2 border border-gray-300 rounded-lg text-sm"
                                                      placeholder="Catatan (opsional)">{{ $currentNotes }}</textarea>
                                            @error('attendances.' . $student->id . '.notes')
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
                        <i class="fas fa-save mr-2"></i> Simpan Absensi
                    </button>
                    <a href="{{ route('teacher.attendances.history') }}"
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
