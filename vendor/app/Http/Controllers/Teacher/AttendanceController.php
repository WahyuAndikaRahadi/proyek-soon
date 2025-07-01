<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Menampilkan form untuk mencatat absensi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $teacherId = Auth::id();

        $localTime = Carbon::now()->setTimezone('Asia/Jakarta');
        $todayDateString = $localTime->toDateString();
        $dayOfWeek = $localTime->isoFormat('dddd');

        $schedulesToday = Schedule::where('user_id', $teacherId)
                                  ->where('day_of_week', $dayOfWeek)
                                  ->with(['class', 'subject'])
                                  ->orderBy('start_time')
                                  ->get();

        $selectedSchedule = null;
        $students = collect();
        $existingAttendances = collect();

        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::where('id', $request->schedule_id)
                                        ->where('user_id', $teacherId)
                                        ->firstOrFail();
            $students = Student::where('class_id', $selectedSchedule->class_id)->get();

            $existingAttendances = Attendance::where('schedule_id', $selectedSchedule->id)
                                             ->whereDate('date', $todayDateString)
                                             ->pluck('status', 'student_id');
        }

        return view('teacher.attendances.create', compact('schedulesToday', 'selectedSchedule', 'students', 'existingAttendances', 'localTime', 'todayDateString'));
    }

    /**
     * Menyimpan absensi siswa ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        foreach ($request->attendances as $data) {
            // Cek apakah absensi sudah ada untuk siswa, jadwal, dan tanggal ini
            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'schedule_id' => $request->schedule_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'recorded_by' => Auth::id(), // Ini sudah ada, tapi mungkin bukan user_id yang dimaksud error
                    'user_id' => Auth::id(),     // <--- TAMBAHKAN BARIS INI
                ]
            );
        }

        return redirect()->route('teacher.attendances.create', ['schedule_id' => $request->schedule_id])->with('success', 'Absensi berhasil dicatat.');
    }

    /**
     * Menampilkan riwayat absensi guru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function history(Request $request)
    {
        $teacherId = Auth::id();

        $query = Attendance::whereHas('schedule', function ($q) use ($teacherId) {
                                $q->where('user_id', $teacherId);
                            })
                            ->with(['student.class', 'schedule.subject']);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('student.class', function ($q) use ($request) {
                $q->where('id', $request->class_id);
            });
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('schedule.subject', function ($q) use ($request) {
                $q->where('id', $request->subject_id);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        return view('teacher.attendances.history', compact('attendances', 'teacherSubjects', 'teacherClasses', 'request'));
    }
}