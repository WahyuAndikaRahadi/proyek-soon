<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User; // Untuk guru
use App\Models\Classes; // Untuk kelas
use App\Models\Subject; // Untuk mata pelajaran
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule untuk validasi

class ScheduleController extends Controller
{
    /**
     * Tampilkan daftar sumber daya.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['teacher', 'class', 'subject']);
        $selectedTeacherId = $request->input('teacher_id');

        if ($selectedTeacherId) {
            $query->where('user_id', $selectedTeacherId);
        }

        $schedules = $query->orderBy('day_of_week')
                            ->orderBy('start_time')
                            ->paginate(10);

        $teachers = User::where('role', 'guru')->get();

        return view('admin.schedules.index', compact('schedules', 'teachers', 'selectedTeacherId'));
    }

    /**
     * Tampilkan formulir untuk membuat sumber daya baru.
     */
    public function create()
    {
        $teachers = User::where('role', 'guru')
                        ->with(['classes', 'subjects'])
                        ->get();

        $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.schedules.create', compact('teachers', 'daysOfWeek'));
    }

    /**
     * Simpan sumber daya yang baru dibuat di penyimpanan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'at_time' => 'nullable|string|max:255', // Validasi untuk kolom 'at_time'
            'academic_year' => 'nullable|string|max:255',
            'semester' => 'nullable|integer|min:1|max:2',
        ]);

        Schedule::create($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    /**
     * Tampilkan formulir untuk mengedit sumber daya yang ditentukan.
     */
    public function edit(Schedule $schedule)
    {
        $teachers = User::where('role', 'guru')
                        ->with(['classes', 'subjects'])
                        ->get();

        $daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('admin.schedules.edit', compact('schedule', 'teachers', 'daysOfWeek'));
    }

    /**
     * Perbarui sumber daya yang ditentukan di penyimpanan.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'day_of_week' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'at_time' => 'nullable|string|max:255', // Validasi untuk kolom 'at_time'
            'academic_year' => 'nullable|string|max:255',
            'semester' => 'nullable|integer|min:1|max:2',
        ]);

        $schedule->update($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * Hapus sumber daya yang ditentukan dari penyimpanan.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}

