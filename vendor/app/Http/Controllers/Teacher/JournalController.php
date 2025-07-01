<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pastikan Carbon sudah diimpor

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal harian guru.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        $query = Journal::where('user_id', $teacherId)
                        ->with(['class', 'subject']);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $journals = $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->paginate(10);

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        return view('teacher.journals.index', compact('journals', 'teacherSubjects', 'teacherClasses', 'request'));
    }

    /**
     * Menampilkan form untuk membuat jurnal baru.
     * Kini akan menampilkan daftar jadwal untuk dipilih terlebih dahulu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $teacherId = Auth::id();

        // Dapatkan waktu saat ini dalam zona waktu lokal aplikasi (misal: Asia/Jakarta)
        $today = Carbon::now()->setTimezone('Asia/Jakarta');
        $todayDateString = $today->toDateString();
        $dayOfWeek = $today->isoFormat('dddd');

        // Dapatkan jadwal guru untuk hari ini
        $schedulesToday = Schedule::where('user_id', $teacherId)
                                  ->where('day_of_week', $dayOfWeek)
                                  ->with(['class', 'subject'])
                                  ->orderBy('start_time')
                                  ->get();

        $selectedSchedule = null;
        $journal = null;

        // Jika ada schedule_id yang dipilih dari filter atau request
        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::where('id', $request->schedule_id)
                                        ->where('user_id', $teacherId)
                                        ->with(['class', 'subject'])
                                        ->firstOrFail();

            $journal = new Journal([
                'user_id' => $teacherId,
                'class_id' => $selectedSchedule->class_id,
                'subject_id' => $selectedSchedule->subject_id,
                'date' => $todayDateString,
                // --- PERUBAHAN PENTING DI SINI ---
                // Pastikan start_time dan end_time diformat ke HH:mm
                'start_time' => Carbon::parse($selectedSchedule->start_time)->format('H:i'),
                'end_time' => Carbon::parse($selectedSchedule->end_time)->format('H:i'),
                // --- AKHIR PERUBAHAN PENTING ---
            ]);
        }

        return view('teacher.journals.create', compact('schedulesToday', 'selectedSchedule', 'journal', 'today', 'todayDateString'));
    }


    /**
     * Menyimpan jurnal baru ke database.
     * Validasi disesuaikan untuk menerima schedule_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i', // Validasi ini tetap sama
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        Journal::create([
            'user_id' => Auth::id(),
            'class_id' => $schedule->class_id,
            'subject_id' => $schedule->subject_id,
            'date' => $request->date,
            'start_time' => $request->start_time, // Nilai yang dikirim dari form (sudah HH:mm)
            'end_time' => $request->end_time,     // Nilai yang dikirim dari form (sudah HH:mm)
            'title' => $request->title,
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        return redirect()->route('teacher.journals.index')->with('success', 'Jurnal berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jurnal.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\View\View
     */
    public function edit(Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        // Pastikan juga di sini jika Anda mengedit, format waktu ke HH:mm
        // Jika start_time/end_time di model Journal Anda adalah Carbon object (karena casting),
        // maka Anda bisa langsung format saat mempassing ke view
        // $journal->start_time = $journal->start_time ? $journal->start_time->format('H:i') : null;
        // $journal->end_time = $journal->end_time ? $journal->end_time->format('H:i') : null;
        // Atau, jika tidak ada casting:
        $journal->start_time = $journal->start_time ? Carbon::parse($journal->start_time)->format('H:i') : null;
        $journal->end_time = $journal->end_time ? Carbon::parse($journal->end_time)->format('H:i') : null;


        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        return view('teacher.journals.edit', compact('journal', 'teacherSubjects', 'teacherClasses'));
    }

    /**
     * Memperbarui data jurnal di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i', // Validasi ini tetap sama
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $journal->update($request->all());

        return redirect()->route('teacher.journals.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    /**
     * Menghapus jurnal dari database.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Journal $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        $journal->delete();

        return redirect()->route('teacher.journals.index')->with('success', 'Jurnal berhasil dihapus.');
    }
}