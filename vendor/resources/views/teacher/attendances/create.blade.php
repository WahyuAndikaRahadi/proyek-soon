@extends('teacher.layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>

<style>
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

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
        
        .assessment-card {
            margin: 0;
        }

        .gradient-header {
            padding: 1rem;
        }

        .btn {
            width: 100%;
            margin: 0.25rem 0;
        }
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .attendance-card {
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
    
    .status-select {
        transition: all 0.2s ease;
    }
    
    .status-select.Hadir { background-color: #f0fdf4; color: #166534; }
    .status-select.Sakit { background-color: #eff6ff; color: #1e40af; }
    .status-select.Izin { background-color: #fef9c3; color: #854d0e; }
    .status-select.Alpha { background-color: #fee2e2; color: #991b1b; }
    
    @media (max-width: 768px) {
        .responsive-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>

<script>
    const { animate, stagger } = Motion;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.schedule-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });
        
        // Animate attendance form if visible
        if (document.querySelector('.attendance-form')) {
            animate('.attendance-form', 
                { opacity: [0, 1], y: [20, 0] }, 
                { duration: 0.5, delay: 0.2 }
            );
            
            // Stagger animations for table rows
            animate('.attendance-row', 
                { opacity: [0, 1], x: [-10, 0] },
                { delay: stagger(0.05, { startDelay: 0.3 }), duration: 0.3 }
            );
        }
        
        // Status select change effect
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                // Remove all classes first
                this.classList.remove('Hadir', 'Sakit', 'Izin', 'Alpha');
                // Add the appropriate class
                this.classList.add(this.value);
                
                // Add animation
                animate(this, 
                    { scale: [1, 1.05, 1] },
                    { duration: 0.3 }
                );
            });
            
            // Initialize with correct class
            select.classList.add(select.value);
        });
    });
</script>

<div class="container mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-user-check text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Catat Absensi Siswa</h1>
            <p class="text-gray-600">Rekam kehadiran siswa untuk jadwal mengajar Anda</p>
        </div>
    </div>

    <!-- Pilihan Jadwal -->
    <div class="attendance-card bg-white mb-6 schedule-card">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Jadwal untuk Absensi</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('teacher.attendances.create') }}" method="GET">
                <div class="form-group mb-4">
                    <label for="schedule_id" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> 
                        Jadwal Hari Ini ({{ $localTime->isoFormat('dddd, D MMMM Y') }})
                    </label>
                    <select name="schedule_id" id="schedule_id" 
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('schedule_id') border-red-500 @enderror"
                        onchange="this.form.submit()">
                        <option value="">-- Pilih Jadwal --</option>
                        @forelse ($schedulesToday as $schedule)
                            <option value="{{ $schedule->id }}" {{ $selectedSchedule && $selectedSchedule->id == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->class->name ?? 'N/A' }} - {{ $schedule->subject->name ?? 'N/A' }} 
                                ({{ Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($schedule->end_time)->format('H:i') }})
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada jadwal untuk hari ini.</option>
                        @endforelse
                    </select>
                    @error('schedule_id') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>
            </form>
        </div>
    </div>

    {{-- Absensi --}}
    @if ($selectedSchedule && $students->isNotEmpty())
        <div class="attendance-card bg-white attendance-form">
            <div class="gradient-header px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-white">
                    Absensi Kelas {{ $selectedSchedule->class->name ?? 'N/A' }} - {{ $selectedSchedule->subject->name ?? 'N/A' }}
                </h3>
                <a href="{{ route('teacher.attendances.history') }}" 
                    class="btn flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-100 text-indigo-600 font-medium rounded-lg shadow-sm transition">
                    <i class="fas fa-history mr-2"></i> Riwayat
                </a>
            </div>
            
            <form method="POST" action="{{ route('teacher.attendances.store') }}">
                @csrf
                <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
                <input type="hidden" name="date" value="{{ $todayDateString }}">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 responsive-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($students as $student)
                                <tr class="attendance-row hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->nis }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="attendances[{{ $student->id }}][status]" 
                                            class="status-select w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('attendances.'.$student->id.'.status') border-red-500 @enderror">
                                            <option value="Hadir" {{ old('attendances.'.$student->id.'.status', $existingAttendances[$student->id] ?? 'Hadir') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                            <option value="Sakit" {{ old('attendances.'.$student->id.'.status', $existingAttendances[$student->id] ?? '') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                            <option value="Izin" {{ old('attendances.'.$student->id.'.status', $existingAttendances[$student->id] ?? '') == 'Izin' ? 'selected' : '' }}>Izin</option>
                                            <option value="Alpha" {{ old('attendances.'.$student->id.'.status', $existingAttendances[$student->id] ?? '') == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                                        </select>
                                        <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                        @error('attendances.'.$student->id.'.status') 
                                            <p class="text-red-500 text-xs italic mt-1">
                                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                            </p> 
                                        @enderror
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" name="attendances[{{ $student->id }}][notes]" 
                                            class="input-focus w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-200 transition" 
                                            value="{{ old('attendances.'.$student->id.'.notes', $existingAttendances[$student->id]['notes'] ?? '') }}" 
                                            placeholder="Catatan (opsional)">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button type="submit" 
                        class="btn flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md transition">
                        <i class="fas fa-save mr-2"></i> Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    @elseif ($selectedSchedule && $students->isEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Info!</strong>
                <span class="block">Tidak ada siswa di kelas ini.</span>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Perhatian!</strong>
                <span class="block">Silakan pilih jadwal di atas untuk mencatat absensi.</span>
            </div>
        </div>
    @endif
</div>
@endsection