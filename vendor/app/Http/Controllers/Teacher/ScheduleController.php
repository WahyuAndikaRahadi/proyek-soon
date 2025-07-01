<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id(); // Mengambil ID guru yang sedang login

        // Mengambil jadwal guru yang sedang login
        $schedules = Schedule::where('user_id', $teacherId)
                             ->with(['class', 'subject'])
                             ->orderBy('day_of_week')
                             ->orderBy('start_time')
                             ->get(); // Ambil semua jadwal, atau gunakan paginate jika banyak

        // Anda bisa mengelompokkan jadwal berdasarkan hari untuk tampilan yang lebih rapi
        $groupedSchedules = $schedules->groupBy('day_of_week');

        return view('teacher.schedules.index', compact('groupedSchedules'));
    }
}