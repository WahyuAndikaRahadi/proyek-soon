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
    
    .dashboard-card {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .badge-walikelas {
        display: inline-flex;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        line-height: 1;
        background-color: #d1fae5;
        color: #065f46;
    }

    /* Tab styles */
    .tab-container {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    
    .tab-button {
        padding: 0.75rem 1.5rem;
        margin-right: 0.5rem;
        font-weight: 600;
        color: #64748b;
        background: none;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .tab-button.active {
        color: #4f46e5;
    }
    
    .tab-button.active:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #4f46e5;
    }
    
    .tab-button:hover:not(.active) {
        color: #334155;
        background-color: #f1f5f9;
        border-radius: 6px 6px 0 0;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Animation styles */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }
    
    /* Grid layout for tab content */
    .tab-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
</style>

<div class="container mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 animate-fade-in" style="animation-delay: 0.1s">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-th-large text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Guru</h1>
            <p class="text-gray-600">Ringkasan informasi dan aktivitas Anda</p>
        </div>
    </div>

    <div class="dashboard-card bg-white mb-6 animate-fade-in" style="animation-delay: 0.2s">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Selamat Datang, {{ $user->name }}!</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-4">Anda login sebagai guru. Berikut adalah ringkasan informasi Anda:</p>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="block text-gray-500 text-sm font-medium mb-1">Email:</dt>
                    <dd class="text-gray-900 font-semibold">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="block text-gray-500 text-sm font-medium mb-1">NIP:</dt>
                    <dd class="text-gray-900 font-semibold">{{ $user->nip ?? '-' }}</dd>
                </div>
                @if ($homeroomClass)
                <div>
                    <dt class="block text-gray-500 text-sm font-medium mb-1">Wali Kelas:</dt>
                    <dd class="text-gray-900 font-semibold">{{ $homeroomClass->name }} (Total Siswa: {{ $totalStudentsInHomeroomClass }})</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Tab Navigasi -->
    <div class="tab-container animate-fade-in" style="animation-delay: 0.3s">
        <button class="tab-button active" onclick="openTab('teaching')">
            <i class="fas fa-chalkboard-teacher mr-2"></i>Pengajaran
        </button>
        <button class="tab-button" onclick="openTab('evaluation')">
            <i class="fas fa-clipboard-check mr-2"></i>Evaluasi
        </button>
    </div>

    <!-- Tab Pelajaran -->
    <div id="teaching" class="tab-content active">
        <div class="tab-grid">
            <!-- Mata Pelajaran -->
            <div class="dashboard-card bg-white">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-book mr-2 text-indigo-500"></i>
                        Mata Pelajaran
                    </h3>
                </div>
                <div class="p-0">
                    @if ($subjects->isEmpty())
                        <p class="p-6 text-gray-500">Belum ada mata pelajaran yang diampu.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($subjects as $subject)
                                <li class="py-3 px-6 text-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                                    {{ $subject->name }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Kelas -->
            <div class="dashboard-card bg-white">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-users mr-2 text-indigo-500"></i>
                        Kelas yang Diampu
                    </h3>
                </div>
                <div class="p-0">
                    @if ($classes->isEmpty())
                        <p class="p-6 text-gray-500">Belum ada kelas yang diampu.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($classes as $class)
                                <li class="py-3 px-6 text-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                                    {{ $class->name }} 
                                    @if($class->pivot->is_homeroom_teacher) 
                                        <span class="badge-walikelas ml-2">Wali Kelas</span> 
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="dashboard-card bg-white mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calendar-day mr-2 text-indigo-500"></i>
                    Jadwal Hari Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM Y') }})
                </h3>
            </div>
            <div class="p-0 overflow-x-auto rounded-b-xl">
                @if ($schedulesToday->isEmpty())
                    <p class="p-6 text-gray-500">Tidak ada jadwal pelajaran hari ini.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($schedulesToday as $schedule)
                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->class->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->subject->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <a href="{{ route('teacher.schedules.index') }}" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Lihat Semua Jadwal <i class="fas fa-arrow-circle-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Jurnal Terbaru -->
        <div class="dashboard-card bg-white mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-book-open mr-2 text-indigo-500"></i>
                    Jurnal Terbaru
                </h3>
            </div>
            <div class="p-0">
                @if ($latestJournals->isEmpty())
                    <p class="p-6 text-gray-500">Belum ada jurnal harian terbaru.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach ($latestJournals as $journal)
                            <li class="py-3 px-6 text-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <strong>{{ \Carbon\Carbon::parse($journal->date)->format('d M Y') }}:</strong>
                                Mengajar {{ $journal->subject->name ?? 'N/A' }} di kelas {{ $journal->class->name ?? 'N/A' }}<br>
                                <small class="text-gray-500">{{ Str::limit($journal->activity_description, 70) }}</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <a href="{{ route('teacher.journals.index') }}" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Lihat Semua Jurnal <i class="fas fa-arrow-circle-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Evaluasi -->
    <div id="evaluation" class="tab-content">
        <div class="tab-grid">
            <!-- Absensi -->
            <div class="dashboard-card bg-white">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-indigo-500"></i>
                        Ringkasan Absensi
                    </h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-200">
                        <li class="py-3 px-6 text-gray-700 flex justify-between items-center hover:bg-gray-50 transition duration-150 ease-in-out">
                            <span>Absensi hari ini:</span>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-800">
                                {{ $attendancesRecordedToday }}
                            </span>
                        </li>
                        @if($homeroomClass)
                        <li class="py-3 px-6 text-gray-700 flex justify-between items-center hover:bg-gray-50 transition duration-150 ease-in-out">
                            <span>Siswa di kelas wali:</span>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $totalStudentsInHomeroomClass }}
                            </span>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('teacher.attendances.history') }}" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        Lihat Detail <i class="fas fa-arrow-circle-right ml-2"></i>
                    </a>
                </div>
            </div>

            <!-- Penilaian -->
            <div class="dashboard-card bg-white">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
                        Ringkasan Penilaian
                    </h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-200">
                        <li class="py-3 px-6 text-gray-700 flex justify-between items-center hover:bg-gray-50 transition duration-150 ease-in-out">
                            <span>Total penilaian:</span>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $totalAssessmentsRecorded }}
                            </span>
                        </li>
                        @forelse ($latestAssessments as $assessment)
                            <li class="py-3 px-6 text-gray-700 hover:bg-gray-50 transition duration-150 ease-in-out">
                                <strong>{{ \Carbon\Carbon::parse($assessment->date)->format('d M Y') }}:</strong>
                                {{ $assessment->student->name ?? 'N/A' }} ({{ $assessment->class->name ?? 'N/A' }})<br>
                                {{ $assessment->subject->name ?? 'N/A' }}: <strong class="text-blue-600">{{ $assessment->score }}</strong>
                            </li>
                        @empty
                            <li class="py-3 px-6 text-gray-500">Belum ada penilaian terbaru</li>
                        @endforelse
                    </ul>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('teacher.assessments.index') }}" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        Lihat Semua <i class="fas fa-arrow-circle-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Framer Motion
    const { animate, stagger } = Motion;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi page-title
        animate('#page-title', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.4 });
        
        // Animasi dashboard-card
        animate('.dashboard-card', 
            { opacity: [0, 1], y: [-10, 0] }, 
            { delay: stagger(0.1, { startDelay: 0.3 }) }
        );
    });
    
    // Tab functionality
    function openTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active class from all buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // tab tombol
        document.getElementById(tabName).classList.add('active');
        event.currentTarget.classList.add('active');
        
        // Animasi
        animate(document.getElementById(tabName), 
            { opacity: [0, 1] }, 
            { duration: 0.3 }
        );
    }
</script>
@endsection