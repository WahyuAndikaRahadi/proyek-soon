<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\AttitudeRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; // Import Validator facade

class TeacherAttitudeRecordController extends Controller
{
    /**
     * Menampilkan formulir untuk mencatat sikap siswa.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $teacherSchedules = $user->schedules()
                                 ->with(['class', 'subject'])
                                 ->orderBy('day_of_week')
                                 ->orderBy('start_time')
                                 ->get();

        // Ambil semua tanggal unik dari jadwal guru
        // Anda mungkin ingin menyesuaikan ini untuk mengambil tanggal spesifik dari database jika ada
        $availableDates = collect([Carbon::now()->format('Y-m-d')])->merge(
            AttitudeRecord::where('user_id', $user->id)
                          ->distinct('record_date')
                          ->pluck('record_date')
                          ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                          ->unique()
        )->sort()->mapWithKeys(function ($date) {
            return [$date => Carbon::parse($date)->isoFormat('dddd, D MMMM Y')];
        })->prepend('-- Pilih Tanggal --', ''); // Tambahkan opsi default

        $selectedRecordDateString = $request->input('record_date', Carbon::now()->format('Y-m-d'));
        $selectedScheduleId = $request->input('schedule_id');

        $schedulesOnSelectedDay = collect();
        $selectedSchedule = null;
        $students = collect();
        $existingAttitudeRecords = collect();

        if ($selectedRecordDateString) {
            $dayOfWeek = Carbon::parse($selectedRecordDateString)->dayOfWeekIso; // 1 (Senin) sampai 7 (Minggu)

            $schedulesOnSelectedDay = $teacherSchedules->filter(function ($schedule) use ($dayOfWeek) {
                // Konversi nama hari ke angka (1=Senin, 2=Selasa, Rabu=3, dst.) untuk perbandingan
                $dayMap = [
                    'Minggu' => 7, 'Senin' => 1, 'Selasa' => 2, 'Rabu' => 3,
                    'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6
                ];
                return ($dayMap[$schedule->day_of_week] ?? null) == $dayOfWeek;
            })->sortBy('start_time');

            if ($selectedScheduleId) {
                $selectedSchedule = $schedulesOnSelectedDay->firstWhere('id', $selectedScheduleId);

                if ($selectedSchedule) {
                    $students = $selectedSchedule->class->students()->orderBy('name')->get();

                    // Ambil catatan sikap yang sudah ada untuk jadwal dan tanggal ini
                    $existingAttitudeRecords = AttitudeRecord::where('schedule_id', $selectedSchedule->id)
                                                              ->where('record_date', $selectedRecordDateString)
                                                              ->get()
                                                              ->keyBy('student_id');
                }
            }
        }

        return view('teacher.attitude_records.create', compact(
            'availableDates',
            'selectedRecordDateString',
            'schedulesOnSelectedDay',
            'selectedSchedule',
            'students',
            'existingAttitudeRecords'
        ));
    }

    /**
     * Menyimpan catatan sikap siswa yang baru.
     */
    public function store(Request $request)
    {
        // Validasi awal untuk jadwal dan tanggal
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'record_date' => 'required|date',
            'selected_students' => 'nullable|array', // selected_students bisa kosong jika tidak ada yang dipilih
            'selected_students.*' => 'exists:students,id', // Pastikan ID siswa yang dipilih valid
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Jika tidak ada siswa yang dipilih, kembalikan error
        if (empty($request->selected_students)) {
            return back()->withInput()->with('error', 'Pilih setidaknya satu siswa untuk mencatat sikap.');
        }

        $schedule = Schedule::with(['class', 'subject'])->findOrFail($request->schedule_id);
        $teacherId = Auth::id();
        $recordDate = $request->record_date;

        DB::beginTransaction();
        try {
            foreach ($request->selected_students as $studentId) {
                $attitudeNotes = $request->input('attitude_records.' . $studentId . '.attitude_notes');

                // Validasi catatan sikap untuk setiap siswa yang dipilih
                // Catatan sikap harus diisi jika siswa tersebut dipilih
                if (empty($attitudeNotes)) {
                    DB::rollBack();
                    // Menggunakan nama siswa dari hidden input untuk pesan error yang lebih jelas
                    $studentName = $request->input('student_names.' . $studentId, 'Siswa Tidak Dikenal');
                    return back()->withInput()->with('error', "Catatan sikap untuk siswa '{$studentName}' tidak boleh kosong jika siswa dipilih.");
                }

                AttitudeRecord::updateOrCreate(
                    [
                        'user_id' => $teacherId,
                        'student_id' => $studentId,
                        'schedule_id' => $schedule->id,
                        'record_date' => $recordDate,
                    ],
                    [
                        'class_id' => $schedule->class_id,
                        'subject_id' => $schedule->subject_id,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'at_time' => $schedule->at_time,
                        'attitude_notes' => $attitudeNotes,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('teacher.attitude_records.history', [
                'schedule_id' => $schedule->id,
                'record_date' => $recordDate
            ])->with('success', 'Catatan sikap siswa berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan catatan sikap siswa: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan formulir untuk mengedit catatan sikap siswa.
     */
    public function edit(AttitudeRecord $attitudeRecord)
    {
        // Pastikan guru yang login memiliki hak untuk mengedit catatan ini
        if ($attitudeRecord->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit catatan sikap ini.');
        }

        return view('teacher.attitude_records.edit', compact('attitudeRecord'));
    }

    /**
     * Memperbarui catatan sikap siswa yang ada.
     */
    public function update(Request $request, AttitudeRecord $attitudeRecord)
    {
        // Pastikan guru yang login memiliki hak untuk mengedit catatan ini
        if ($attitudeRecord->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui catatan sikap ini.');
        }

        $request->validate([
            'attitude_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $attitudeRecord->update([
                'attitude_notes' => $request->attitude_notes,
            ]);

            return redirect()->route('teacher.attitude_records.history', [
                'schedule_id' => $attitudeRecord->schedule_id,
                // record_date sudah menjadi objek Carbon karena cast di model
                'record_date' => $attitudeRecord->record_date->toDateString()
            ])->with('success', 'Catatan sikap siswa berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui catatan sikap siswa: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus catatan sikap siswa.
     */
    public function destroy(AttitudeRecord $attitudeRecord)
    {
        // Pastikan guru yang login memiliki hak untuk menghapus catatan ini
        if ($attitudeRecord->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus catatan sikap ini.');
        }

        try {
            $attitudeRecord->delete();
            return back()->with('success', 'Catatan sikap siswa berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus catatan sikap siswa: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat catatan sikap siswa.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $teacherSchedules = $user->schedules()->with(['class', 'subject'])->get();
        $teacherClasses = $user->classes()->get();
        $teacherSubjects = $user->subjects()->get();

        $query = AttitudeRecord::where('user_id', Auth::id())
                               ->with(['student.class', 'schedule.subject', 'schedule.class', 'teacher']); // Tambahkan 'teacher' eager loading

        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        // Filter berdasarkan schedule_id
        if ($request->filled('schedule_id')) {
            $query->where('schedule_id', $request->schedule_id);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $attitudeRecords = $query->orderBy('record_date', 'desc')
                                 ->orderBy('start_time', 'desc')
                                 ->paginate(10); // Sesuaikan pagination

        // Dapatkan siswa yang diajar oleh guru ini (dari semua kelas yang diajar)
        $studentsInTeacherClasses = Student::whereIn('class_id', $teacherClasses->pluck('id'))
                                           ->orderBy('name')
                                           ->get();

        return view('teacher.attitude_records.history', compact(
            'attitudeRecords',
            'teacherClasses',
            'teacherSubjects',
            'teacherSchedules', // Pastikan ini diteruskan ke view
            'studentsInTeacherClasses',
            'request'
        ));
    }
}

