<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes; // Menggunakan "Classes" sesuai dengan seeder Anda
use App\Models\Student;
use App\Models\Subject;
use App\Models\User; // Untuk guru
use App\Models\Schedule; // <-- Tambahkan ini

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard admin.
     */
    public function index()
    {
        // Menghitung total data
        $totalTeachers = User::where('role', 'guru')->count();
        $totalClasses = Classes::count(); // Menggunakan model "Classes"
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSchedules = Schedule::count(); // <-- Tambahkan ini

        // Data untuk laporan siswa per kelas
        $studentsPerClass = Classes::withCount('students')
                                    ->orderBy('name')
                                    ->get()
                                    ->map(function ($class) {
                                        return [
                                            'class_name' => $class->name,
                                            'total_students' => $class->students_count,
                                        ];
                                    });

        return view('admin.dashboard', compact(
            'totalTeachers',
            'totalClasses',
            'totalStudents',
            'totalSubjects',
            'totalSchedules', // <-- Tambahkan ini ke compact
            'studentsPerClass'
        ));
    }
}