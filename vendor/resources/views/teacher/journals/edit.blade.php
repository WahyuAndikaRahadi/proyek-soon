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
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
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
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
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
        animate('.journal-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });
        
        // Stagger animations for form elements
        animate('.form-group', 
            { opacity: [0, 1], x: [-10, 0] },
            { delay: stagger(0.1), duration: 0.3 }
        );
        
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
        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
            <i class="fas fa-edit text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Jurnal: {{ $journal->title }}</h1>
            <p class="text-gray-600">Perbarui catatan kegiatan pembelajaran</p>
        </div>
    </div>

    <div class="journal-card bg-white">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Form Edit Jurnal</h3>
        </div>
        
        <form method="POST" action="{{ route('teacher.journals.update', $journal->id) }}">
            @csrf
            @method('PUT')
            <div class="p-6">
                <div class="form-group mb-4">
                    <label for="date" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-calendar-day mr-2 text-yellow-500"></i> Tanggal
                    </label>
                    <input type="date" name="date" id="date" 
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('date') border-red-500 @enderror" 
                        value="{{ old('date', $journal->date) }}" required>
                    @error('date') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="class_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-users mr-2 text-yellow-500"></i> Kelas
                        </label>
                        <select name="class_id" id="class_id" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('class_id') border-red-500 @enderror" 
                            required>
                            <option value="">Pilih Kelas</option>
                            @foreach ($teacherClasses as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $journal->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-book mr-2 text-yellow-500"></i> Mata Pelajaran
                        </label>
                        <select name="subject_id" id="subject_id" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('subject_id') border-red-500 @enderror" 
                            required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach ($teacherSubjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $journal->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-group">
                        <label for="start_time" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-clock mr-2 text-yellow-500"></i> Jam Mulai
                        </label>
                        <input type="time" name="start_time" id="start_time" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('start_time') border-red-500 @enderror" 
                            value="{{ old('start_time', \Carbon\Carbon::parse($journal->start_time)->format('H:i')) }}" required>
                        @error('start_time') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_time" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-clock mr-2 text-yellow-500"></i> Jam Selesai
                        </label>
                        <input type="time" name="end_time" id="end_time" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('end_time') border-red-500 @enderror" 
                            value="{{ old('end_time', \Carbon\Carbon::parse($journal->end_time)->format('H:i')) }}" required>
                        @error('end_time') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-heading mr-2 text-yellow-500"></i> Materi/Judul Kegiatan
                    </label>
                    <input type="text" name="title" id="title" 
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('title') border-red-500 @enderror" 
                        value="{{ old('title', $journal->title) }}" required>
                    @error('title') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-align-left mr-2 text-yellow-500"></i> Deskripsi Kegiatan
                    </label>
                    <textarea name="description" id="description" rows="5"
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('description') border-red-500 @enderror" 
                        required>{{ old('description', $journal->description) }}</textarea>
                    @error('description') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="form-group mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Catatan Tambahan (Opsional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('notes') border-red-500 @enderror">{{ old('notes', $journal->notes) }}</textarea>
                    @error('notes') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <button type="submit" 
                    class="btn flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg shadow-md transition">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui Jurnal
                </button>
                <a href="{{ route('teacher.journals.index') }}" 
                    class="btn flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-md transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection