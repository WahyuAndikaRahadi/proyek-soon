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
            /* Changed to yellow/orange gradient */
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
            border-color: #f59e0b;
            /* Changed focus border color to yellow */
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
            /* Changed focus shadow to yellow */
        }

        /* Styles for custom select dropdown (if applied) - keeping consistent with create page */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23f59e0b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        select:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23f97316'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        }

        /* Button hover effects for ripple, consistent with create page */
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

        // SweetAlert confirmation function for edit - Moved outside DOMContentLoaded for global access
        function confirmEdit() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah data penilaian ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('editAssessmentForm').submit();
                }
            })
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Animate page elements
            animate('#page-title', {
                opacity: [0, 1],
                y: [-20, 0]
            }, {
                duration: 0.4
            });
            animate('.assessment-card', {
                opacity: [0, 1],
                scale: [0.98, 1]
            }, {
                duration: 0.4
            });

            // Stagger animations for form elements
            animate('.form-section > *', {
                opacity: [0, 1],
                x: [-10, 0]
            }, {
                delay: stagger(0.1),
                duration: 0.3
            });

            // Button hover effects
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

            // SweetAlert for update error
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
                <i class="fas fa-edit text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Penilaian:
                    {{ $assessment->student->name ?? 'N/A' }}</h1>
                <p class="text-gray-600">Perbarui nilai untuk {{ $assessment->type }}</p>
            </div>
        </div>

        <div class="assessment-card bg-white mb-8">
            <div class="gradient-header px-6 py-4">
                <h3 class="text-xl font-semibold text-white">Form Edit Penilaian</h3>
            </div>

            <form method="POST" action="{{ route('teacher.assessments.update', $assessment->id) }}"
                id="editAssessmentForm">
                @csrf
                @method('PUT')
                <div class="p-6 form-section">
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="type" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-tasks mr-2 text-yellow-500"></i> Tipe Penilaian
                            </label>
                            {{-- Tipe penilaian tidak bisa diubah di sini karena bagian dari unique key --}}
                            <p
                                class="text-gray-800 font-semibold p-3 bg-gray-100 rounded-lg border border-gray-200 cursor-not-allowed">
                                {{ $assessment->type }}
                            </p>
                            <input type="hidden" name="type" value="{{ $assessment->type }}">
                            {{-- Keep hidden input for form submission --}}
                        </div>
                        <div>
                            <label for="date" class="block text-gray-700 text-sm font-medium mb-2">
                                <i class="fas fa-calendar-day mr-2 text-yellow-500"></i> Tanggal Penilaian
                            </label>
                            <input type="date" name="date" id="date"
                                class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('date') border-red-500 @enderror"
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
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('score') border-red-500 @enderror"
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
                            class="input-focus w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition @error('notes') border-red-500 @enderror">{{ old('notes', $assessment->notes) }}</textarea>
                        @error('notes')
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
                        <i class="fas fa-sync-alt mr-2"></i> Perbarui Penilaian
                    </button>

                    <a href="{{ route('teacher.assessments.index') }}"
                        class="btn bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection