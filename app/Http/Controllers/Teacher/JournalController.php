<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        // Tidak perlu eager load 'schedule' untuk at_time, karena sekarang ada di Journal.
        // Tapi tetap dimuat untuk class dan subject jika relasi tersebut juga digunakan dari schedule.
        $query = Journal::where('user_id', $teacherId)
                         ->with(['class', 'subject', 'schedule']); 

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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $teacherId = Auth::id();

        $localTime = Carbon::now()->setTimezone('Asia/Jakarta');

        $selectedJournalDateString = $request->input('journal_date', $localTime->toDateString());
        $selectedJournalDate = Carbon::parse($selectedJournalDateString)->setTimezone('Asia/Jakarta');

        $dayOfWeekForSchedule = $selectedJournalDate->isoFormat('dddd');

        $schedulesOnSelectedDay = Schedule::where('user_id', $teacherId)
                                          ->where('day_of_week', $dayOfWeekForSchedule)
                                          ->with(['class', 'subject']) 
                                          ->orderBy('start_time')
                                          ->get();

        $selectedSchedule = null;
        $journal = null;

        if ($request->filled('schedule_id')) {
            $selectedSchedule = Schedule::where('id', $request->schedule_id)
                                        ->where('user_id', $teacherId)
                                        ->with(['class', 'subject'])
                                        ->firstOrFail();

            $journal = Journal::where('user_id', $teacherId)
                              ->where('schedule_id', $selectedSchedule->id)
                              ->whereDate('date', $selectedJournalDateString)
                              ->first();

            if (!$journal) {
                $journal = new Journal([
                    'user_id' => $teacherId,
                    'class_id' => $selectedSchedule->class_id,
                    'subject_id' => $selectedSchedule->subject_id,
                    'date' => $selectedJournalDateString,
                    'start_time' => Carbon::parse($selectedSchedule->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($selectedSchedule->end_time)->format('H:i'),
                    'at_time' => $selectedSchedule->at_time, // <--- Inisialisasi at_time di objek Journal baru
                    'title' => null,
                    'description' => null,
                    'notes' => null,
                ]);
            } else {
                $journal->start_time = Carbon::parse($journal->start_time)->format('H:i');
                $journal->end_time = Carbon::parse($journal->end_time)->format('H:i');
                // Tidak perlu memperbarui at_time di sini jika jurnal sudah ada,
                // karena diasumsikan sudah benar dari proses pembuatan awal.
            }
        }

        $availableDates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $localTime->copy()->subDays($i);
            $availableDates[$date->toDateString()] = $date->isoFormat('dddd, D MMMM Y');
        }
        krsort($availableDates);

        return view('teacher.journals.create', compact('schedulesOnSelectedDay', 'selectedSchedule', 'journal', 'localTime', 'selectedJournalDateString', 'availableDates'));
    }

    /**
     * Menyimpan jurnal baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'journal_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if ($schedule->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

        $inputJournalDate = Carbon::parse($request->journal_date)->setTimezone('Asia/Jakarta')->startOfDay();
        $today = Carbon::now()->setTimezone('Asia/Jakarta')->startOfDay();
        $sevenDaysAgo = $today->copy()->subDays(6)->startOfDay();

        if ($inputJournalDate->gt($today) || $inputJournalDate->lt($sevenDaysAgo)) {
            return redirect()->back()->withErrors(['journal_date' => 'Tanggal jurnal harus hari ini atau maksimal 7 hari ke belakang (termasuk hari yang sama minggu lalu).'])->withInput();
        }

        $existingJournal = Journal::where('user_id', Auth::id())
                                ->where('schedule_id', $request->schedule_id)
                                ->whereDate('date', $request->journal_date)
                                ->first();

        if ($existingJournal) {
            return redirect()->back()->withErrors(['message' => 'Jurnal untuk jadwal dan tanggal ini sudah ada. Silakan edit jurnal yang sudah ada atau pilih jadwal/tanggal lain.'])->withInput();
        }

        Journal::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'class_id' => $schedule->class_id,
            'subject_id' => $schedule->subject_id,
            'date' => $request->journal_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'at_time' => $schedule->at_time, // <--- Simpan at_time dari schedule ke kolom journal.at_time
            'title' => $request->title,
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        return redirect()->route('teacher.journals.index', [
            'schedule_id' => $request->schedule_id,
            'journal_date' => $request->journal_date
        ])->with('success', 'Jurnal berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit jurnal.
     *
     * @param  \App\Models\Journal  $journal
     * @return \Illuminate\View\View
     */
    public function edit(Journal $journal)
    {
        // Memuat relasi 'schedule' (penting untuk mengakses data schedule lainnya seperti nama kelas/mapel dari schedule)
        // at_time akan diambil langsung dari $journal->at_time
        $journal->load('schedule');

        if ($journal->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang.');
        }

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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'notes' => 'nullable|string',
            // at_time tidak divalidasi di sini karena ini adalah input read-only yang disalin dari schedule.
            // Tidak diharapkan ada perubahan at_time melalui form edit ini.
        ]);

        $existingJournal = Journal::where('user_id', Auth::id())
                                ->where('schedule_id', $journal->schedule_id) 
                                ->whereDate('date', $request->date)
                                ->where('id', '!=', $journal->id)
                                ->first();

        if ($existingJournal) {
            return redirect()->back()->withErrors(['message' => 'Jurnal untuk jadwal dan tanggal ini sudah ada. Silakan edit jurnal yang sudah ada atau pilih jadwal/tanggal lain.'])->withInput();
        }

        $updateData = $request->except(['at_time']); // Pastikan at_time tidak diupdate dari request
                                                      // karena seharusnya hanya disalin saat creation
                                                      // atau jika ada logika kompleks untuk perubahan schedule.

        $journal->update($updateData);

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
