<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CounselingRecord; // Import model CounselingRecord
use App\Models\Student; // Import model Student
use Carbon\Carbon; // Untuk tanggal otomatis
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login
use Illuminate\Support\Facades\Validator; // Import Validator facade

class BkCounselingRecordController extends Controller
{
    /**
     * Menampilkan daftar catatan konseling.
     */
    public function index(Request $request)
    {
        $query = CounselingRecord::with(['student', 'bkTeacher']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->where('record_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('record_date', '<=', $request->end_date);
        }

        // Filter berdasarkan siswa
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter berdasarkan guru BK yang mencatat
        if ($request->filled('bk_teacher_id')) {
            $query->where('user_id', $request->bk_teacher_id);
        }

        $counselingRecords = $query->orderBy('record_date', 'desc')
                                   ->paginate(10);

        $students = Student::orderBy('name')->get();
        $bkTeachers = \App\Models\User::where('role', 'bk')->orderBy('name')->get(); // Ambil semua user dengan role 'bk'

        return view('bk.counseling_records.index', compact('counselingRecords', 'students', 'bkTeachers', 'request'));
    }

    /**
     * Menampilkan formulir untuk membuat catatan konseling baru.
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $recordDate = Carbon::now()->format('Y-m-d'); // Tanggal otomatis hari ini
        return view('bk.counseling_records.create', compact('students', 'recordDate'));
    }

    /**
     * Menyimpan catatan konseling baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'record_date' => 'required|date',
            'notes' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            CounselingRecord::create([
                'user_id' => Auth::id(), // ID guru BK yang login
                'student_id' => $request->student_id,
                'record_date' => $request->record_date,
                'notes' => $request->notes,
            ]);

            return redirect()->route('bk.counseling_records.index')->with('success', 'Catatan konseling berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan catatan konseling: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan formulir untuk mengedit catatan konseling.
     */
    public function edit(CounselingRecord $counselingRecord)
    {
        // Pastikan hanya guru BK yang mencatat atau admin yang bisa mengedit
        if ($counselingRecord->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit catatan konseling ini.');
        }

        $students = Student::orderBy('name')->get();
        return view('bk.counseling_records.edit', compact('counselingRecord', 'students'));
    }

    /**
     * Memperbarui catatan konseling yang ada.
     */
    public function update(Request $request, CounselingRecord $counselingRecord)
    {
        // Pastikan hanya guru BK yang mencatat atau admin yang bisa mengedit
        if ($counselingRecord->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk memperbarui catatan konseling ini.');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'record_date' => 'required|date',
            'notes' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $counselingRecord->update([
                'student_id' => $request->student_id,
                'record_date' => $request->record_date,
                'notes' => $request->notes,
            ]);

            return redirect()->route('bk.counseling_records.index')->with('success', 'Catatan konseling berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui catatan konseling: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus catatan konseling.
     */
    public function destroy(CounselingRecord $counselingRecord)
    {
        // Pastikan hanya guru BK yang mencatat atau admin yang bisa menghapus
        if ($counselingRecord->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses untuk menghapus catatan konseling ini.');
        }

        try {
            $counselingRecord->delete();
            return back()->with('success', 'Catatan konseling berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus catatan konseling: ' . $e->getMessage());
        }
    }
}

