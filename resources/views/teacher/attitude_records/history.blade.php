@extends('teacher.layouts.app')

@section('content')
    {{-- Load Motion.js if not already loaded in main layout --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Tambahkan SweetAlert2 --}}

    <style>
        /* Custom styles from the provided example */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .gradient-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

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

        .attitude-card { /* Changed from assessment-card to attitude-card */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }

        .input-focus {
            transition: all 0.2s ease;
        }

        .input-focus:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        @media (max-width: 768px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Keyframe animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Apply animations */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .animate-slide-up-item {
            animation: slideUp 0.6s ease-out forwards;
        }

        /* Ensure elements are hidden before animation starts */
        .animate-fade-in,
        .animate-slide-up-item {
            opacity: 0;
        }

        /* Tailwind-like pagination styles */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            /* gray-200 */
            border-radius: 0.375rem;
            /* rounded-md */
            margin: 0 0.25rem;
            color: #4a5568;
            /* gray-700 */
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        .pagination a:hover {
            background-color: #f7fafc;
            /* gray-50 */
            color: #2b6cb0;
            /* blue-700 */
        }

        .pagination .active span {
            background-color: #4299e1;
            /* blue-500 */
            color: white;
            border-color: #4299e1;
        }

        .pagination .disabled span {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <div class="mx-auto py-2">
        <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500">
            <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
                <i class="fas fa-history text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Riwayat Catatan Sikap Siswa</h1>
                <p class="text-gray-600">Lihat dan filter riwayat catatan sikap siswa</p>
            </div>
        </div>

        {{-- Filter Riwayat Catatan Sikap --}}
        <div class="attitude-card bg-white mb-6 animate-fade-in">
            <div class="gradient-header px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-white">Filter Riwayat Catatan Sikap</h3>
                <a href="{{ route('teacher.attitude_records.create') }}"
                    class="bg-white text-indigo-600 font-medium py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>Catatan Baru
                </a>
            </div>
            <div class="p-6">
                <form action="{{ route('teacher.attitude_records.history') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="start_date" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Dari Tanggal:
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                value="{{ $request->start_date }}">
                        </div>
                        <div>
                            <label for="end_date" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Sampai Tanggal:
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition"
                                value="{{ $request->end_date }}">
                        </div>
                        <div>
                            <label for="schedule_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> Jadwal:
                            </label>
                            <select name="schedule_id" id="schedule_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                                <option value="">Semua Jadwal</option>
                                @foreach ($teacherSchedules as $schedule)
                                    <option value="{{ $schedule->id }}"
                                        {{ $request->schedule_id == $schedule->id ? 'selected' : '' }}>
                                        {{ $schedule->day_of_week }} ({{ Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}) - {{ $schedule->subject->name ?? 'N/A' }} ({{ $schedule->class->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="class_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-school mr-2 text-indigo-500"></i> Kelas:
                            </label>
                            <select name="class_id" id="class_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                                <option value="">Semua Kelas</option>
                                @foreach ($teacherClasses as $class)
                                    <option value="{{ $class->id }}"
                                        {{ $request->class_id == $class->id ? 'selected' : '' }}>{{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="subject_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran:
                            </label>
                            <select name="subject_id" id="subject_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach ($teacherSubjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        {{ $request->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="student_id" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-user-graduate mr-2 text-indigo-500"></i> Siswa:
                            </label>
                            <select name="student_id" id="student_id"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                                <option value="">Semua Siswa</option>
                                @foreach ($studentsInTeacherClasses as $student)
                                    <option value="{{ $student->id }}"
                                        {{ $request->student_id == $student->id ? 'selected' : '' }}>{{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex space-x-3 justify-end">
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                            <i class="fas fa-filter mr-2"></i>Terapkan Filter
                        </button>
                        <a href="{{ route('teacher.attitude_records.history') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                            <i class="fas fa-sync mr-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Catatan Sikap --}}
        <div class="attitude-card bg-white animate-fade-in delay-100">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">Daftar Riwayat Catatan Sikap</h3>
            </div>
            <div class="overflow-x-auto rounded-b-xl">
                <table class="min-w-full divide-y divide-gray-200 responsive-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Ke-
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Catatan Sikap</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($attitudeRecords as $record)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out animate-slide-up-item">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $loop->iteration + ($attitudeRecords->currentPage() - 1) * $attitudeRecords->perPage() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->record_date)->isoFormat('D MMMM Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->teacher->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->subject->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->student->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->class->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $record->at_time ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($record->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($record->end_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $record->attitude_notes ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('teacher.attitude_records.edit', $record->id) }}"
                                            class="action-btn flex items-center justify-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <form action="{{ route('teacher.attitude_records.destroy', $record->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event, '{{ $record->student->name ?? 'siswa ini' }}');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="action-btn flex items-center justify-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-md shadow-sm">
                                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <i class="fas fa-info-circle text-gray-400 text-xl mb-2"></i>
                                        <p>Tidak ada catatan sikap yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{-- Custom pagination styling for Tailwind --}}
                {{ $attitudeRecords->appends($request->except('page'))->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>

    <script>
        const {
            animate,
            stagger
        } = Motion;

        document.addEventListener('DOMContentLoaded', function() {
            // Animate page title and filter card
            animate('#page-title', {
                opacity: [0, 1],
                y: [-20, 0]
            }, {
                duration: 0.4
            });
            animate('.attitude-card', {
                opacity: [0, 1],
                scale: [0.98, 1]
            }, {
                duration: 0.4,
                delay: 0.1
            });

            // Stagger animations for table rows
            animate('.animate-slide-up-item', {
                opacity: [0, 1],
                x: [-10, 0]
            }, {
                delay: stagger(0.05, {
                    startDelay: 0.3
                }),
                duration: 0.3
            });
        });

        function confirmDelete(event, studentName) {
            event.preventDefault(); // Mencegah form terkirim secara default

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus catatan sikap untuk " + studentName + ". Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit(); // Lanjutkan pengiriman form jika dikonfirmasi
                }
            });
            return false; // Pastikan form tidak terkirim jika SweetAlert muncul
        }

        // SweetAlert untuk menampilkan pesan sukses/error dari server
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        @endif
    </script>
@endsection
