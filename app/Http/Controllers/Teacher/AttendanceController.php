<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classes; // Pastikan ini diimpor jika digunakan
use App\Models\Subject; // Pastikan ini diimpor jika digunakan
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule; // Add this for validation rules

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

        // Mengambil tanggal yang dipilih dari request, atau default ke hari ini
        // Ini adalah TANGGAL ABSENSI yang ingin DICATAT, BUKAN TANGGAL SAAT INI
        $selectedAttendanceDateString = $request->input('attendance_date', $localTime->toDateString());
        $selectedAttendanceDate = Carbon::parse($selectedAttendanceDateString)->setTimezone('Asia/Jakarta');

        // Hari dalam seminggu berdasarkan TANGGAL ABSENSI yang dipilih
        $dayOfWeekForSchedule = $selectedAttendanceDate->isoFormat('dddd');

        // Mengambil jadwal guru yang sesuai dengan hari dari 'selectedAttendanceDate'
        $schedulesOnSelectedDay = Schedule::where('user_id', $teacherId)
                                          ->where('day_of_week', $dayOfWeekForSchedule)
                                          ->with(['class', 'subject'])
                                          ->orderBy('start_time')
                                          ->get();

        $selectedSchedule = null;
        $students = collect();
        $existingAttendances = collect(); // Change to an associative array for easier lookup

        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::where('id', $request->schedule_id)
                                          ->where('user_id', $teacherId)
                                          ->firstOrFail();
            $students = Student::where('class_id', $selectedSchedule->class_id)->get();

            // Ambil absensi yang sudah ada berdasarkan schedule_id dan selectedAttendanceDate
            // Fetch both status and notes
            $existingAttendances = Attendance::where('schedule_id', $selectedSchedule->id)
                                             ->whereDate('date', $selectedAttendanceDateString)
                                             ->get()
                                             ->keyBy('student_id'); // Key by student_id for easy access
        }

        // Generate tanggal yang tersedia untuk dipilih:
        // Rentang 7 hari ke belakang (dari hari ini) hingga hari ini.
        // Contoh: Jika hari ini Rabu, 17 Juni, maka bisa pilih dari Kamis, 10 Juni hingga Rabu, 17 Juni.
        $availableDates = [];
        // Loop from today backwards for 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = $localTime->copy()->subDays($i);
            $availableDates[$date->toDateString()] = $date->isoFormat('dddd, D MMMM Y');
        }
        // No need for krsort if you loop backwards from today, it will naturally be latest to oldest.

        return view('teacher.attendances.create', compact('schedulesOnSelectedDay', 'selectedSchedule', 'students', 'existingAttendances', 'localTime', 'selectedAttendanceDateString', 'availableDates'));
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
            'attendance_date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        // Validasi rentang tanggal absensi:
        // Harus hari ini atau 7 hari ke belakang (termasuk hari ini)
        $inputAttendanceDate = Carbon::parse($request->attendance_date)->setTimezone('Asia/Jakarta')->startOfDay();
        $today = Carbon::now()->setTimezone('Asia/Jakarta')->startOfDay();
        $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

        if ($inputAttendanceDate->gt($today) || $inputAttendanceDate->lt($sevenDaysAgo)) {
            return redirect()->back()->withErrors(['attendance_date' => 'Tanggal absensi harus hari ini atau maksimal 7 hari ke belakang.'])->withInput();
        }

        foreach ($request->attendances as $studentId => $data) { // Ensure studentId is correctly retrieved from the array key
            // Cek apakah absensi sudah ada untuk siswa, jadwal, dan tanggal ini
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId, // Use $studentId directly
                    'schedule_id' => $request->schedule_id,
                    'date' => $request->attendance_date,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'],
                    'recorded_by' => Auth::id(),
                    'user_id' => Auth::id(), // Consider if 'user_id' is redundant if 'recorded_by' exists
                ]
            );
        }

        // Redirect kembali dengan parameter tanggal dan jadwal yang dipilih
        return redirect()->route('teacher.attendances.history', [
            'schedule_id' => $request->schedule_id,
            'attendance_date' => $request->attendance_date
        ])->with('success', 'Absensi berhasil dicatat.');
    }



    /**
     * Menampilkan form untuk mengedit absensi tunggal.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\View\View
     */
    public function edit(Attendance $attendance)
    {
        // Authorization check: Ensure only the teacher who recorded it (or associated with the schedule) can edit.
        // Assuming 'user_id' in Attendance refers to the teacher who recorded it.
        // Or, we can check if the schedule's user_id matches the authenticated user.
        if ($attendance->schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk mengedit absensi ini.');
        }

        // We also need the available statuses for the dropdown
        $attendanceStatuses = ['Hadir', 'Sakit', 'Izin', 'Alpha'];

        return view('teacher.attendances.edit', compact('attendance', 'attendanceStatuses'));
    }




    /**
     * Memperbarui absensi siswa di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Authorization check: Ensure only the teacher who recorded it (or associated with the schedule) can update.
        if ($attendance->schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk memperbarui absensi ini.');
        }

        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'notes' => 'nullable|string|max:255',
            'date' => 'required|date', // Ensure date is validated
        ]);

        // Validate the date range again, similar to the store method
        $inputAttendanceDate = Carbon::parse($request->date)->setTimezone('Asia/Jakarta')->startOfDay();
        $today = Carbon::now()->setTimezone('Asia/Jakarta')->startOfDay();
        $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

        if ($inputAttendanceDate->gt($today) || $inputAttendanceDate->lt($sevenDaysAgo)) {
            return redirect()->back()->withErrors(['date' => 'Tanggal absensi harus hari ini atau maksimal 7 hari ke belakang.'])->withInput();
        }

        try {
            $attendance->update([
                'status' => $request->status,
                'notes' => $request->notes,
                'date' => $request->date, // Update the date if changed
            ]);

            // Redirect back to history with the specific date and schedule if needed
            return redirect()->route('teacher.attendances.history', [
                'schedule_id' => $attendance->schedule_id,
                'attendance_date' => $attendance->date->toDateString() // Use the updated date
            ])->with('success', 'Absensi berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Error updating attendance: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors('Gagal memperbarui absensi: ' . $e->getMessage())->withInput();
        }
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
                            // Eager load schedule and its at_time
                            ->with(['student.class', 'schedule.subject', 'schedule']);

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
        // Add filter for at_time
        if ($request->filled('at_time')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('at_time', $request->at_time);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(36);

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        return view('teacher.attendances.history', compact('attendances', 'teacherSubjects', 'teacherClasses', 'request'));
    }
}
