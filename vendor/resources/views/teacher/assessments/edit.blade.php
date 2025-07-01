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
    
    .assessment-card {
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
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.assessment-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });
        
        // Stagger animations for form elements
        animate('.form-section > *', 
            { opacity: [0, 1], x: [-10, 0] },
            { delay: stagger(0.1), duration: 0.3 }
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
    });
</script>

<div class="container mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8">
        <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
            <i class="fas fa-edit text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Penilaian: {{ $assessment->student->name ?? 'N/A' }}</h1>
            <p class="text-gray-600">Perbarui nilai untuk {{ $assessment->type }}</p>
        </div>
    </div>

    <div class="assessment-card bg-white mb-8">
        <!-- Heading -->
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Form Edit Penilaian</h3>
        </div>
        
        <!-- Edit -->
        <form method="POST" action="{{ route('teacher.assessments.update', $assessment->id) }}">
            @csrf
            @method('PUT')
            <div class="p-6 form-section">
                <!-- Info Murid -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-500 text-sm font-medium mb-1">Siswa</label>
                            <p class="text-gray-800 font-semibold">{{ $assessment->student->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm font-medium mb-1">Kelas</label>
                            <p class="text-gray-800 font-semibold">{{ $assessment->student->class->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm font-medium mb-1">Mata Pelajaran</label>
                            <p class="text-gray-800 font-semibold">{{ $assessment->subject->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm font-medium mb-1">Semester</label>
                            <p class="text-gray-800 font-semibold">{{ $assessment->semester }}</p>
                        </div>
                    </div>
                </div>

                <!-- Label -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-tasks mr-2 text-yellow-500"></i> Tipe Penilaian
                        </label>
                        <p class="text-gray-800 font-semibold p-3 bg-gray-50 rounded-lg">{{ $assessment->type }}</p>
                    </div>
                    <div>
                        <label for="date" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-day mr-2 text-yellow-500"></i> Tanggal Penilaian
                        </label>
                        <input type="date" name="date" id="date" 
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('date') border-red-500 @enderror"
                            value="{{ old('date', $assessment->date) }}" required>
                        @error('date') 
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p> 
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="score" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-star mr-2 text-yellow-500"></i> Nilai
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="score" id="score" 
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('score') border-red-500 @enderror"
                        value="{{ old('score', $assessment->score) }}" required>
                    @error('score') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="notes" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Catatan
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition @error('notes') border-red-500 @enderror">{{ old('notes', $assessment->notes) }}</textarea>
                    @error('notes') 
                        <p class="text-red-500 text-xs italic mt-2">
                            <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                        </p> 
                    @enderror
                </div>
            </div>
            
            <!-- Tombol -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <button type="submit" 
                    class="btn flex items-center justify-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg shadow-md transition">
                    <i class="fas fa-sync-alt mr-2"></i> Perbarui Penilaian
                </button>
                <a href="{{ route('teacher.assessments.index') }}" 
                    class="btn flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-md transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection