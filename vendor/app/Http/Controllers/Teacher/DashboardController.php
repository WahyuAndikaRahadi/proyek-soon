<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance; // Pastikan ini sudah di-import
use App\Models\Assessment; // Pastikan ini sudah di-import
use App\Models\Classes; // Untuk menghitung siswa di kelas wali
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama untuk guru.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user(); // Guru yang sedang login

        // Data utama guru
        $subjects = $user->subjects;
        $classes = $user->classes;

        // Jadwal pelajaran hari ini
        $today = Carbon::now('Asia/Jakarta')->isoFormat('dddd');
        $schedulesToday = Schedule::where('user_id', $user->id)
                                  ->where('day_of_week', $today)
                                  ->with(['class', 'subject'])
                                  ->orderBy('start_time')
                                  ->get();

        // Jurnal harian terbaru
        $latestJournals = Journal::where('user_id', $user->id)
                                 ->orderBy('date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 ->with(['class', 'subject'])
                                 ->get();

        // --- Data untuk Statistik Absensi & Penilaian ---

        // Contoh 1: Jumlah Siswa di Kelas Wali (jika guru adalah wali kelas)
        $homeroomClass = $user->classes()->wherePivot('is_homeroom_teacher', true)->first();
        $totalStudentsInHomeroomClass = 0;
        if ($homeroomClass) {
            $totalStudentsInHomeroomClass = $homeroomClass->students()->count();
        }

        // Contoh 2: Absensi Hari Ini (contoh sederhana: berapa banyak absen yang sudah dicatat guru hari ini)
        $attendancesRecordedToday = Attendance::where('user_id', $user->id)
                                             ->whereDate('date', Carbon::today('Asia/Jakarta'))
                                             ->count(); // Menghitung jumlah record absensi yang dicatat

        // Contoh 3: Penilaian Terbaru (ambil 5 penilaian terbaru yang dicatat guru)
        $latestAssessments = Assessment::where('user_id', $user->id)
                                       ->orderBy('date', 'desc')
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->with(['student', 'class', 'subject']) // Load relasi yang diperlukan
                                       ->get();

        // Contoh 4: Total Penilaian yang Sudah Dicatat Guru
        $totalAssessmentsRecorded = Assessment::where('user_id', $user->id)->count();


        return view('teacher.dashboard', compact(
            'user',
            'subjects',
            'classes',
            'schedulesToday',
            'latestJournals',
            'homeroomClass', // Tambahkan ini jika ingin menampilkan kelas wali
            'totalStudentsInHomeroomClass', // Tambahkan ini
            'attendancesRecordedToday', // Tambahkan ini
            'latestAssessments', // Tambahkan ini
            'totalAssessmentsRecorded' // Tambahkan ini
        ));
    }
}