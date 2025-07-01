<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\Journal;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Classes;
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
        $user = Auth::user();
        $today = Carbon::now('Asia/Jakarta');
        $dayOfWeek = $today->isoFormat('dddd');

        // Data utama guru (tetap dipertahankan karena ini spesifik dashboard)
        $subjects = $user->subjects;
        $classes = $user->classes;

        // Jadwal pelajaran hari ini (tetap dipertahankan jika ditampilkan di dashboard)
        $schedulesToday = Schedule::where('user_id', $user->id)
                                  ->where('day_of_week', $dayOfWeek)
                                  ->with(['class', 'subject'])
                                  ->orderBy('start_time')
                                  ->get();

        // Jurnal harian terbaru
        $latestJournals = Journal::where('user_id', $user->id)
                                 ->orderBy('date', 'desc')
                                 ->orderBy('created_at', 'desc')
                                 ->take(5)
                                 // PENTING: eager load relasi 'schedule'
                                 ->with(['class', 'subject', 'schedule']) // Tambahkan 'schedule' di sini
                                 ->get();

        // Data untuk Statistik Absensi & Penilaian
        $homeroomClass = $user->classes()->wherePivot('is_homeroom_teacher', true)->first();
        $totalStudentsInHomeroomClass = 0;
        if ($homeroomClass) {
            $totalStudentsInHomeroomClass = $homeroomClass->students()->count();
        }

        $attendancesRecordedToday = Attendance::where('user_id', $user->id)
                                             ->whereDate('date', Carbon::today('Asia/Jakarta'))
                                             ->count();

        $latestAssessments = Assessment::where('user_id', $user->id)
                                       ->orderBy('date', 'desc')
                                       ->orderBy('created_at', 'desc')
                                       ->take(5)
                                       ->with(['student', 'class', 'subject'])
                                       ->get();

        $totalAssessmentsRecorded = Assessment::where('user_id', $user->id)->count();


        return view('teacher.dashboard', compact(
            'user',
            'subjects',
            'classes',
            'schedulesToday',
            'latestJournals',
            'homeroomClass',
            'totalStudentsInHomeroomClass',
            'attendancesRecordedToday',
            'latestAssessments',
            'totalAssessmentsRecorded'
        ));
    }
}




