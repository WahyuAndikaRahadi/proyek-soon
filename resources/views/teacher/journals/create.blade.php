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
    
    /* New style for schedule info card */
    .schedule-info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .schedule-info-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .info-label {
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .info-value {
        font-size: 1rem;
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

        // Handle form submission for date and schedule
        document.getElementById('journal_date_selector').addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });

        document.getElementById('schedule_id').addEventListener('change', function() {
            document.getElementById('schedule_selection_form').submit();
        });
        
    });

    function confirmEdit() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda dapat mengedit data jurnal ini jika ada salah!",
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('editForm').submit();
            }
        })
    }
</script>

<div class="mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-book-open text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Buat Jurnal Harian</h1>
            <p class="text-gray-600">Catat kegiatan pembelajaran Anda</p>
        </div>
    </div>

    <div class="journal-card bg-white mb-6 schedule-card">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Pilih Tanggal dan Jadwal untuk Jurnal</h3>
        </div>
        
        <div class="p-6">
            <form id="schedule_selection_form" action="{{ route('teacher.journals.create') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group mb-4">
                        <label for="journal_date_selector" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Tanggal Jurnal
                        </label>
                        <select name="journal_date" id="journal_date_selector"
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('journal_date') border-red-500 @enderror">
                            @foreach ($availableDates as $value => $label)
                                <option value="{{ $value }}" {{ $selectedJournalDateString == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('journal_date')
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="schedule_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-day mr-2 text-indigo-500"></i> Jadwal pada Hari Terpilih
                        </label>
                        <select name="schedule_id" id="schedule_id"
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('schedule_id') border-red-500 @enderror">
                            <option value="">-- Pilih Jadwal --</option>
                            @forelse ($schedulesOnSelectedDay as $schedule)
                                <option value="{{ $schedule->id }}" {{ $selectedSchedule && $selectedSchedule->id == $schedule->id ? 'selected' : '' }}>
                                   ( {{ $schedule->at_time ? 'Jam ke-' . $schedule->at_time . ' ' : '' }} ) {{-- Added Jam Ke- --}}
                                    {{ $schedule->class->name ?? 'N/A' }}
                                    ({{ Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($schedule->end_time)->format('H:i') }})
                                </option>
                            @empty
                                <option value="" disabled>Tidak ada jadwal untuk hari yang dipilih.</option>
                            @endforelse
                        </select>
                        @error('schedule_id') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                            </p> 
                        @enderror
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedSchedule)
    <div class="schedule-info-card mb-6 p-6 schedule-card">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4"> {{-- Changed to 4 columns --}}
            <div class="p-4">
                <p class="info-label"><i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran</p>
                <p class="info-value mt-1">{{ $selectedSchedule->subject->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-users mr-2 text-indigo-500"></i> Kelas</p>
                <p class="info-value mt-1">{{ $selectedSchedule->class->name ?? 'N/A' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Jam Ke-</p> {{-- Added Jam Ke- display --}}
                <p class="info-value mt-1">{{ $selectedSchedule->at_time ?? '-' }}</p>
            </div>
            <div class="p-4">
                <p class="info-label"><i class="fas fa-clock mr-2 text-indigo-500"></i> Waktu Jadwal</p>
                <p class="info-value mt-1">
                    {{ Carbon\Carbon::parse($selectedSchedule->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($selectedSchedule->end_time)->format('H:i') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Jurnal --}}
    @if ($selectedSchedule)
        <div class="journal-card bg-white journal-form">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">
                    Jurnal Kelas {{ $selectedSchedule->class->name ?? 'N/A' }} - {{ $selectedSchedule->subject->name ?? 'N/A' }}
                </h3>
            </div>
            
            <form method="POST" action="{{ route('teacher.journals.store') }}" id="editForm">
                @csrf
                <div class="p-6">
                    <input type="hidden" name="schedule_id" value="{{ $selectedSchedule->id }}">
                    <input type="hidden" name="journal_date" value="{{ old('journal_date', $selectedJournalDateString) }}">
                    <input type="hidden" name="at_time" value="{{ old('at_time', $selectedSchedule->at_time ?? '') }}"> {{-- Hidden input for at_time --}}


                    <div class="form-group mb-4">
                        <label for="date_display" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar mr-2 text-indigo-500"></i> Tanggal Jurnal
                        </label>
                        <input type="text" id="date_display" 
                            class="input-focus w-full p-3 border border-gray-300 bg-gray-50 rounded-lg focus:outline-none" 
                            value="{{ Carbon\Carbon::parse($selectedJournalDateString)->isoFormat('dddd, D MMMM Y') }}" 
                            readonly>
                        @error('journal_date') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message ?? '' }}
                            </p> 
                        @enderror
                    </div>

                    {{-- Menampilkan error duplikasi jurnal --}}
                    @if (session('errors') && session('errors')->has('message'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('errors')->first('message') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4"> {{-- Changed to 3 columns --}}
                        <div class="form-group time-inputs">
                            <label for="journal_at_time_display" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-sort-numeric-up mr-2 text-indigo-500"></i> Jam Ke-
                            </label>
                            <input type="text" id="journal_at_time_display"
                                class="input-focus w-full p-3 border border-gray-300 bg-gray-50 rounded-lg focus:outline-none"
                                value="{{ old('at_time', $selectedSchedule->at_time ?? '-') }}" readonly>
                        </div>
                        <div class="form-group time-inputs">
                            <label for="start_time" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-clock mr-2 text-indigo-500"></i> Jam Mulai
                            </label>
                            <input type="time" name="start_time" id="start_time" 
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('start_time') border-red-500 @enderror" 
                                value="{{ old('start_time', $journal->start_time ?? Carbon\Carbon::parse($selectedSchedule->start_time)->format('H:i')) }}" required>
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
                                value="{{ old('end_time', $journal->end_time ?? Carbon\Carbon::parse($selectedSchedule->end_time)->format('H:i')) }}" required>
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
                    <button type="button" onclick="confirmEdit()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Simpan Jurnal
                    </button>
                    <a href="{{ route('teacher.journals.index') }}" 
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    @elseif ($schedulesOnSelectedDay->isEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Info!</strong>
                <span class="block">Tidak ada jadwal mengajar untuk Anda pada tanggal yang dipilih ({{ Carbon\Carbon::parse($selectedJournalDateString)->isoFormat('dddd, D MMMM Y') }}).</span>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 rounded-lg flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
            <div>
                <strong class="font-bold">Perhatian!</strong>
                <span class="block">Silakan pilih tanggal dan jadwal di atas untuk membuat jurnal.</span>
            </div>
        </div>
    @endif
</div>
@endsection
