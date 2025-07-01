<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Classes; // Menggunakan Classes sesuai dengan model Anda
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar penilaian yang dicatat oleh guru.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userId = Auth::id(); // Menggunakan user_id

        $query = Assessment::where('user_id', $userId) // <-- Diubah dari 'user_id' ke 'user_id'
                           ->with(['student.class', 'subject']);

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('class_id')) {
            $query->whereHas('student.class', function ($q) use ($request) {
                $q->where('id', $request->class_id);
            });
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $assessments = $query->orderBy('date', 'desc')->paginate(10);

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;
        $assessmentTypes = ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS'];


        return view('teacher.assessments.index', compact('assessments', 'teacherSubjects', 'teacherClasses', 'assessmentTypes', 'request'));
    }

    /**
     * Menampilkan form untuk membuat penilaian baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;
        $assessmentTypes = ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS'];

        $selectedClass = null;
        $students = collect();

        if ($request->filled('class_id')) {
            $selectedClass = Classes::findOrFail($request->class_id);
            // Hanya ambil siswa dari kelas yang diampu guru ini (opsional, tergantung logic bisnis)
            // Atau cukup semua siswa di kelas yang dipilih
            $students = Student::where('class_id', $selectedClass->id)->get();
        }

        return view('teacher.assessments.create', compact('teacherSubjects', 'teacherClasses', 'assessmentTypes', 'selectedClass', 'students'));
    }

    /**
     * Menyimpan penilaian baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|integer|min:1|max:2',
            'type' => ['required', Rule::in(['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS'])],
            'date' => 'required|date',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score' => 'required|numeric|min:0|max:100',
            'scores.*.notes' => 'nullable|string',
        ]);

        foreach ($request->scores as $data) {
            // Pastikan siswa ada di kelas yang dipilih
            $student = Student::where('id', $data['student_id'])->where('class_id', $request->class_id)->firstOrFail();

            // Cek apakah penilaian sudah ada untuk siswa, mata pelajaran, semester, dan tipe ini
            Assessment::updateOrCreate(
                [
                    'student_id' => $data['student_id'],
                    'subject_id' => $request->subject_id,
                    'semester' => $request->semester,
                    'type' => $request->type, // Pastikan nama kolom di DB adalah 'type'
                ],
                [
                    'user_id' => Auth::id(), // <-- Diubah dari 'user_id' ke 'user_id'
                    'score' => $data['score'],
                    'date' => $request->date,
                    'notes' => $data['notes'],
                    // Jika Anda memiliki 'class_id' di tabel penilaian, tambahkan juga di sini
                    'class_id' => $request->class_id, // Ini penting untuk data yang lebih konsisten
                ]
            );
        }

        return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil dicatat.');
    }

    /**
     * Menampilkan form untuk mengedit penilaian.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\View\View
     */
    public function edit(Assessment $assessment)
    {
        // Pastikan guru yang login adalah guru yang memberikan penilaian
        if ($assessment->user_id !== Auth::id()) { // <-- Diubah dari 'user_id' ke 'user_id'
            abort(403, 'Akses Dilarang.');
        }

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;
        $assessmentTypes = ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS'];

        return view('teacher.assessments.edit', compact('assessment', 'teacherSubjects', 'teacherClasses', 'assessmentTypes'));
    }

    /**
     * Memperbarui data penilaian di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Assessment $assessment)
    {
        // Pastikan guru yang login adalah guru yang memberikan penilaian
        if ($assessment->user_id !== Auth::id()) { // <-- Diubah dari 'user_id' ke 'user_id'
            abort(403, 'Akses Dilarang.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            // Subject, Semester, Type tidak bisa diubah setelah dibuat karena menjadi unique key
        ]);

        $assessment->update([
            'score' => $request->score,
            'date' => $request->date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus penilaian dari database.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Assessment $assessment)
    {
        // Pastikan guru yang login adalah guru yang memberikan penilaian
        if ($assessment->user_id !== Auth::id()) { // <-- Diubah dari 'user_id' ke 'user_id'
            abort(403, 'Akses Dilarang.');
        }

        $assessment->delete();

        return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil dihapus.');
    }
}