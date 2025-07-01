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

    .journal-card {
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
    
    .time-inputs {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .mobile-stack {
            flex-direction: column;
        }
        
        .mobile-stack > div {
            width: 100%;
        }
    }
</style>

<script>
    const { animate, stagger } = Motion;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.schedule-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });
        
        // Animate form if visible
        if (document.querySelector('.journal-form')) {
            animate('.journal-form', 
                { opacity: [0, 1], y: [20, 0] }, 
                { duration: 0.5, delay: 0.2 }
            );
            
            // Stagger animations for form elements
            animate('.journal-form .form-group', 
                { opacity: [0, 1], x: [-10, 0] },
                { delay: stagger(0.1, { startDelay: 0.3 }), duration: 0.3 }
            );
        }
        
        // Button hover effects
        const buttons = document.querySelectorAll('button, a.btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                animate(button, { scale: 1.03 }, { duration: 0.2 });
            });
            button.addEventListener('mouseleave', () => {
                animate(button, { scale: 1 }, { duration: 0.2 });
            });
        });
        
        // Time inputs interaction
        const timeInputs = document.querySelectorAll('input[type="time"]');
        timeInputs.forEach(input => {
            input.addEventListener('focus', () => {
                animate(input.parentElement, { scale: 1.02 }, { duration: 0.2 });
            });
            input.addEventListener('blur', () => {
                animate(input.parentElement, { scale: 1 }, { duration: 0.2 });
            });
        });
    });
</script>

<div class="container mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-book-open text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Buat Jurnal Harian</h1>
            <p class="text-gray-600">Catat kegiatan pembelajaran Anda hari ini</p>
        </div>
    </div>

    <!-- Pilihan Jadwal -->
    <div class="journal-card bg-white mb-6 schedule-card">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Jadwal untuk Jurnal</h3>
        </div>
        
        <div class="p-6">
            <form action="{{ route('teacher.journals.create') }}" method="GET">
                <div class="form-group mb-4">
                    <label for="schedule_id" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> 
                        Jadwal Hari Ini ({{ $today->isoFormat('dddd, D MMMM Y') }})
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
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                        </p> 
                    @enderror
                </div>
            </form>
        </div>
    </div>

    {{-- Form Jurnal --}}
    @if ($selectedSchedule)
        <div class="journal-card bg-white journal-form">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">
                    Jurnal Kelas {{ $selectedSchedule->class->name ?? 'N/A' }} - {{ $selectedSchedule->subject->name ?? 'N/A' }}
                </h3>
            </div>
            
            <form method="POST" action="{{ route('teacher.journals.store') }}">
                @csrf
                <div class="p-6">
                    <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
                    <input type="hidden" name="date" value="{{ old('date', $journal->date ?? \Carbon\Carbon::now()->toDateString()) }}">

                    <div class="form-group mb-4">
                        <label for="date_display" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar mr-2 text-indigo-500"></i> Tanggal
                        </label>
                        <input type="text" id="date_display" 
                            class="input-focus w-full p-3 border border-gray-300 bg-gray-50 rounded-lg focus:outline-none" 
                            value="{{ Carbon\Carbon::parse($journal->date ?? \Carbon\Carbon::now()->toDateString())->isoFormat('dddd, D MMMM Y') }}" 
                            readonly>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group time-inputs">
                            <label for="start_time" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-clock mr-2 text-indigo-500"></i> Jam Mulai
                            </label>
                            <input type="time" name="start_time" id="start_time" 
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('start_time') border-red-500 @enderror" 
                                value="{{ old('start_time', $journal->start_time ?? '') }}" required>
                            @error('start_time') 
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p> 
                            @enderror
                        </div>
                        <div class="form-group time-inputs">
                            <label for="end_time" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-clock mr-2 text-indigo-500"></i> Jam Selesai
                            </label>
                            <input type="time" name="end_time" id="end_time" 
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('end_time') border-red-500 @enderror" 
                                value="{{ old('end_time', $journal->end_time ?? '') }}" required>
                            @error('end_time') 
                                <p class="text-red-500 text-xs italic mt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                                </p> 
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="title" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-heading mr-2 text-indigo-500"></i> Materi/Judul Kegiatan
                        </label>
                        <input type="text" name="title" id="title" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('title') border-red-500 @enderror" 
                            value="{{ old('title', $journal->title ?? '') }}" required>
                        @error('title') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                            </p> 
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-align-left mr-2 text-indigo-500"></i> Deskripsi Kegiatan
                        </label>
                        <textarea name="description" id="description" rows="5"
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('description') border-red-500 @enderror" 
                            required>{{ old('description', $journal->description ?? '') }}</textarea>
                        @error('description') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                            </p> 
                        @enderror
                    </div>

                    <div class="form-group mb-6">
                        <label for="notes" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-sticky-note mr-2 text-indigo-500"></i> Catatan Tambahan (Opsional)
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('notes') border-red-500 @enderror">{{ old('notes', $journal->notes ?? '') }}</textarea>
                        @error('notes') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                            </p> 
                        @enderror
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="submit" 
                        class="btn flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-md transition">
                        <i class="fas fa-save mr-2"></i> Simpan Jurnal
                    </button>
                    <a href="{{ route('teacher.journals.index') }}" 
                        class="btn flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-md transition">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    @elseif ($schedulesToday->isEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Info!</strong>
                <span class="block">Tidak ada jadwal mengajar untuk Anda pada hari ini.</span>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Perhatian!</strong>
                <span class="block">Silakan pilih jadwal di atas untuk membuat jurnal.</span>
            </div>
        </div>
    @endif
</div>
@endsection