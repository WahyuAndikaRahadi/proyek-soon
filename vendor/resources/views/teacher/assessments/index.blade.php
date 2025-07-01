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
    
    .action-btn {
        transition: all 0.2s ease;
    }
    
    @media (max-width: 768px) {
        .responsive-table th, 
        .responsive-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .action-btns {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn {
            width: 100%;
            text-align: center;
        }
    }
</style>

<script>
    const { animate, stagger } = Motion;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animate page elements
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        animate('.filter-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4 });
        animate('.list-card', { opacity: [0, 1], scale: [0.98, 1] }, { duration: 0.4, delay: 0.1 });
        
        // Stagger animations for table rows
        animate('.assessment-row', 
            { opacity: [0, 1], y: [10, 0] },
            { delay: stagger(0.05, { startDelay: 0.3 }), duration: 0.3 }
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
    });
</script>

<div class="container mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-clipboard-list text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Penilaian Siswa Saya</h1>
            <p class="text-gray-600">Daftar lengkap penilaian siswa yang telah dicatat</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="assessment-card bg-white mb-6 filter-card">
        <div class="gradient-header px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-white">Filter Penilaian</h3>
            <a href="{{ route('teacher.assessments.create') }}" 
               class="btn flex items-center justify-center px-4 py-2 bg-white hover:bg-gray-100 text-indigo-600 font-medium rounded-lg shadow-sm transition">
                <i class="fas fa-plus mr-2"></i> Catat Baru
            </a>
        </div>
        
        <div class="p-6">
            <form action="{{ route('teacher.assessments.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label for="semester" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Semester
                        </label>
                        <select name="semester" id="semester" 
                            class="input-focus w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                            <option value="">Semua Semester</option>
                            <option value="1" {{ $request->semester == '1' ? 'selected' : '' }}>Semester 1</option>
                            <option value="2" {{ $request->semester == '2' ? 'selected' : '' }}>Semester 2</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="class_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-users mr-2 text-indigo-500"></i> Kelas
                        </label>
                        <select name="class_id" id="class_id" 
                            class="input-focus w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                            <option value="">Semua Kelas</option>
                            @foreach ($teacherClasses as $class)
                                <option value="{{ $class->id }}" {{ $request->class_id == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="subject_id" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-book mr-2 text-indigo-500"></i> Mata Pelajaran
                        </label>
                        <select name="subject_id" id="subject_id" 
                            class="input-focus w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                            <option value="">Semua Mapel</option>
                            @foreach ($teacherSubjects as $subject)
                                <option value="{{ $subject->id }}" {{ $request->subject_id == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="type" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-tasks mr-2 text-indigo-500"></i> Tipe Penilaian
                        </label>
                        <select name="type" id="type" 
                            class="input-focus w-full p-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-200 transition">
                            <option value="">Semua Tipe</option>
                            @foreach ($assessmentTypes as $type)
                                <option value="{{ $type }}" {{ $request->type == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <button type="submit" 
                        class="btn flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition">
                        <i class="fas fa-filter mr-2"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('teacher.assessments.index') }}" 
                        class="btn flex items-center justify-center px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow-sm transition">
                        <i class="fas fa-sync mr-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- List Nilai -->
    <div class="assessment-card bg-white list-card">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Daftar Penilaian</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 responsive-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($assessments as $assessment)
                        <tr class="assessment-row hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $loop->iteration + ($assessments->currentPage() - 1) * $assessments->perPage() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $assessment->student->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $assessment->student->class->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $assessment->subject->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $assessment->semester }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                                    {{ $assessment->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold 
                                @if($assessment->score >= 85) text-green-600
                                @elseif($assessment->score >= 70) text-blue-600
                                @elseif($assessment->score >= 60) text-yellow-600
                                @else text-red-600
                                @endif">
                                {{ $assessment->score }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($assessment->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium action-btns">
                                <div class="flex space-x-2">
                                    <a href="{{ route('teacher.assessments.edit', $assessment->id) }}" 
                                        class="action-btn flex items-center justify-center px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md shadow-sm">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('teacher.assessments.destroy', $assessment->id) }}" method="POST" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus penilaian ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="action-btn flex items-center justify-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-md shadow-sm">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <i class="fas fa-info-circle text-gray-400 text-xl mb-2"></i>
                                    <p>Tidak ada data penilaian yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($assessments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $assessments->appends($request->except('page'))->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
</div>
@endsection