@extends('teacher.layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');


        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .gradient-header {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        }

        .attitude-card {
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

        /* Efek hover tombol untuk riak */
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
        const {
            animate,
            stagger
        } = Motion;

        // Fungsi konfirmasi SweetAlert untuk edit
        function confirmEdit() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah catatan sikap ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('editAttitudeRecordForm').submit();
                }
            })
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Animasikan elemen halaman
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
                duration: 0.4
            });

            // Animasikan elemen formulir secara berurutan
            animate('.form-section > *', {
                opacity: [0, 1],
                x: [-10, 0]
            }, {
                delay: stagger(0.1),
                duration: 0.3
            });

            // Efek hover tombol
            const buttons = document.querySelectorAll('button, a.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', () => {
                    animate(button, {
                        scale: 1.02
                    }, {
                        duration: 0.2
                    });
                });
                button.addEventListener('mouseleave', () => {
                    animate(button, {
                        scale: 1
                    }, {
                        duration: 0.2
                    });
                });
            });

            // SweetAlert untuk kesalahan pembaruan
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            @endif
        });
    </script>

    <div class="mx-auto py-2">
        <div id="page-title" class="flex items-center mb-8 border-b-2 border-yellow-500">
            <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                <i class="fas fa-clipboard-list text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Catatan Sikap:
                    {{ $attitudeRecord->student->name ?? 'N/A' }}</h1>
                <p class="text-gray-600">Perbarui catatan sikap untuk {{ $attitudeRecord->student->name ?? 'N/A' }} pada
                    mata pelajaran {{ $attitudeRecord->subject->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="attitude-card bg-white mb-8">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">Form Edit Catatan Sikap</h3>
            </div>

            <form method="POST" action="{{ route('teacher.attitude_records.update', $attitudeRecord->id) }}"
                id="editAttitudeRecordForm">
                @csrf
                @method('PUT')
                <div class="p-6 form-section">
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Guru</label>
                                <p class="text-gray-800 font-semibold">{{ $attitudeRecord->teacher->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Siswa</label>
                                <p class="text-gray-800 font-semibold">{{ $attitudeRecord->student->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Kelas</label>
                                <p class="text-gray-800 font-semibold">{{ $attitudeRecord->class->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Mata Pelajaran</label>
                                <p class="text-gray-800 font-semibold">{{ $attitudeRecord->subject->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Tanggal</label>
                                <p class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($attitudeRecord->record_date)->isoFormat('D MMMM Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Jam Ke-</label>
                                <p class="text-gray-800 font-semibold">{{ $attitudeRecord->at_time ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="block text-gray-500 text-sm font-medium mb-1">Waktu</label>
                                <p class="text-gray-800 font-semibold">
                                    {{ \Carbon\Carbon::parse($attitudeRecord->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($attitudeRecord->end_time)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="attitude_notes" class="block text-gray-700 text-sm font-medium mb-2">
                            <i class="fas fa-sticky-note mr-2 text-yellow-500"></i> Catatan Sikap
                        </label>
                        <textarea name="attitude_notes" id="attitude_notes" rows="5"
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('attitude_notes') border-red-500 @enderror"
                            placeholder="Masukkan catatan sikap siswa">{{ old('attitude_notes', $attitudeRecord->attitude_notes) }}</textarea>
                        @error('attitude_notes')
                            <p class="text-red-500 text-xs italic mt-2">
                                <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div
                    class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="confirmEdit()"
                        class="btn bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-sync-alt mr-2"></i> Perbarui Catatan Sikap
                    </button>

                    <a href="{{ route('teacher.attitude_records.history', ['schedule_id' => $attitudeRecord->schedule_id, 'record_date' => $attitudeRecord->record_date->toDateString()]) }}"
                        class="btn bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

