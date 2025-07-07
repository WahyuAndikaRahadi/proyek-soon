<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttitudeRecord; // Import model AttitudeRecord
use App\Models\Student; // Import model Student
use App\Models\Classes; // Import model Classes
use App\Models\Subject; // Import model Subject
use App\Models\User; // Import model User (untuk guru)
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login

class BkAttitudeRecordController extends Controller
{
    /**
     * Menampilkan daftar catatan sikap siswa untuk role BK.
     * BK dapat melihat semua catatan sikap, tidak hanya yang dibuat oleh guru tertentu.
     */
    public function index(Request $request)
    {
        // Query dasar untuk mengambil catatan sikap
        $query = AttitudeRecord::with(['student.class', 'schedule.subject', 'schedule.class', 'teacher']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }

        // Filter berdasarkan kelas
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter berdasarkan mata pelajaran
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter berdasarkan guru (user_id yang mencatat)
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }

        // Filter berdasarkan siswa
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Urutkan berdasarkan tanggal dan waktu
        $attitudeRecords = $query->orderBy('record_date', 'desc')
                                 ->orderBy('start_time', 'desc')
                                 ->paginate(10); // Sesuaikan pagination

        // Data untuk filter dropdown
        $classes = Classes::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $students = Student::orderBy('name')->get(); // Semua siswa
        $teachers = User::where('role', 'guru')->orderBy('name')->get(); // Semua guru

        return view('bk.attitude_records.index', compact(
            'attitudeRecords',
            'classes',
            'subjects',
            'students',
            'teachers',
            'request' // Teruskan request untuk mempertahankan nilai filter
        ));
    }
}

