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
    
    .schedule-card {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .schedule-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .schedule-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .schedule-table th {
        background-color: #f3f4f6;
        color: #374151;
        font-weight: 600;
        text-align: left;
        padding: 0.75rem;
    }
    
    .schedule-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .schedule-table tr:last-child td {
        border-bottom: none;
    }
    
    .collapse-btn {
        transition: all 0.3s ease;
    }
    
    .collapse-btn.collapsed i {
        transform: rotate(0deg);
    }
    
    .collapse-btn i {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    
    @media (max-width: 768px) {
        .schedule-table {
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
        
        // Stagger animations for schedule cards
        animate('.schedule-card', 
            { opacity: [0, 1], y: [20, 0] },
            { delay: stagger(0.1), duration: 0.3 }
        );
        
        // Collapse functionality
        document.querySelectorAll('.collapse-btn').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('.schedule-card');
                const body = card.querySelector('.card-body-collapse');
                const icon = this.querySelector('i');
                
                if (body.classList.contains('hidden')) {
                    animate(body, 
                        { opacity: [0, 1], height: ['0px', 'auto'] }, 
                        { duration: 0.3 }
                    );
                    body.classList.remove('hidden');
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    animate(body, 
                        { opacity: [1, 0], height: ['auto', '0px'] }, 
                        { duration: 0.3 }
                    ).then(() => {
                        body.classList.add('hidden');
                        icon.classList.remove('fa-minus');
                        icon.classList.add('fa-plus');
                    });
                }
            });
        });
    });
</script>

<div class="mx-auto py-2">
    <div id="page-title" class="flex items-center mb-8 border-b-2 border-indigo-500">
        <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
            <i class="fas fa-calendar-alt text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Jadwal Mengajar Saya</h1>
            <p class="text-gray-600">Daftar lengkap jadwal mengajar Anda</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="gradient-header px-6 py-4">
            <h3 class="text-xl font-semibold text-white">Jadwal Mengajar Anda</h3>
        </div>
        
        <div class="p-6">
            @if ($groupedSchedules->isEmpty())
                <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 rounded-lg flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                    <div>
                        <strong class="font-bold">Info!</strong>
                        <span class="block">Anda belum memiliki jadwal mengajar.</span>
                    </div>
                </div>
            @else
                @php
                    $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                @endphp

                @foreach ($dayOrder as $day)
                    @if ($groupedSchedules->has($day))
                        <div class="schedule-card bg-white mb-4">
                            <div class="gradient-header px-6 py-3 flex justify-between items-center cursor-pointer collapse-btn">
                                <h3 class="text-lg font-semibold text-white">{{ $day }}</h3>
                                <button type="button" class="text-white focus:outline-none">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            
                            <div class="card-body-collapse">
                                <div class="overflow-x-auto">
                                    <table class="schedule-table">
                                        <thead>
                                            <tr>
                                                <th>Jam Ke-</th> {{-- Updated header --}}
                                                <th>Waktu</th> {{-- New header for time range --}}
                                                <th>Kelas</th>
                                                <th>Mata Pelajaran</th>
                                                <th>Tahun Ajaran / Semester</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedSchedules[$day]->sortBy('start_time') as $schedule)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="font-medium text-gray-900">
                                                        {{ $schedule->at_time ?? '-' }} {{-- Displaying at_time --}}
                                                    </td>
                                                    <td class="text-gray-700">
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </td>
                                                    <td class="text-gray-700">
                                                        {{ $schedule->class->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="text-gray-700">
                                                        {{ $schedule->subject->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="text-gray-700">
                                                        {{ $schedule->academic_year ?? '-' }} / {{ $schedule->semester ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
