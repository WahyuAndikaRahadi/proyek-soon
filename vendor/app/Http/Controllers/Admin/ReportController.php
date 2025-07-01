<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User; // Untuk guru
use App\Models\Student; // <-- Import model Student
use Illuminate\Http\Request;
use Carbon\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman utama laporan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $classes = Classes::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();
        return view('admin.reports.index', compact('classes', 'subjects', 'teachers'));
    }

    /**
     * Menampilkan laporan absensi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
 public function attendance(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // HAPUS 'recorder' dari with() clause
        $query = Attendance::with(['student.class', 'schedule.subject', 'schedule.teacher']);

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
        if ($request->filled('user_id')) {
            $query->whereHas('schedule.teacher', function ($q) use ($request) {
                $q->where('id', $request->user_id);
            });
        }

        $attendances = $query->paginate(20);

        $classes = Classes::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();

        return view('admin.reports.attendance', compact('attendances', 'classes', 'subjects', 'teachers', 'request'));
    }

    /**
     * Mengekspor laporan absensi ke Excel menggunakan PhpSpreadsheet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAttendance(Request $request)
    {
        // HAPUS 'recorder' dari with() clause
        $query = Attendance::with(['student.class', 'schedule.subject', 'schedule.teacher']);

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
        if ($request->filled('user_id')) {
            $query->whereHas('schedule.teacher', function ($q) use ($request) {
                $q->where('id', $request->user_id);
            });
        }

        $attendances = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = [
            'Tanggal', 'Nama Siswa', 'Kelas', 'Mata Pelajaran',
            'Jam Mulai', 'Jam Selesai', 'Status', 'Catatan', 'Guru Pengajar'
        ];
        $sheet->fromArray($headings, NULL, 'A1');

      $row = 2;
        foreach ($attendances as $attendance) {
            $sheet->setCellValue('A' . $row, $attendance->date);
            $sheet->setCellValue('B' . $row, $attendance->student->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $attendance->student->class->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $attendance->schedule->subject->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $attendance->schedule->start_time ?? 'N/A');
            $sheet->setCellValue('F' . $row, $attendance->schedule->end_time ?? 'N/A');
            $sheet->setCellValue('G' . $row, $attendance->status);
            
            // UBAH DI SINI UNTUK CATATAN
            $sheet->setCellValue('H' . $row, $attendance->notes ?? '-'); 
            
            // Mengambil nama guru pencatat secara manual atau menggunakan relasi yang ada
            // Pastikan Anda sudah memilih salah satu opsi dari jawaban sebelumnya
            // Jika Anda menggunakan Opsi 1 (User::find($attendance->recorded_by)), tetap seperti itu
            // Jika Anda ingin menampilkan teacher dari schedule, gunakan yang ini:
            $sheet->setCellValue('I' . $row, $attendance->schedule->teacher->name ?? 'N/A');

            $row++;
        }
        // --- Customizing the file name for Attendance Report ---
        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;
        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null; // Ini guru yang mengajar jadwal
        $selectedRecorder = $request->filled('recorded_by_user_id') ? User::find($request->recorded_by_user_id) : null; // Jika Anda memiliki filter untuk guru pencatat

        $fileNameParts = ['laporan_absensi'];

        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $fileNameParts[] = 'periode_' . $request->start_date . '_sd_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
             $fileNameParts[] = 'dari_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
             $fileNameParts[] = 'sampai_' . $request->end_date;
        } else {
            $fileNameParts[] = 'semua_tanggal';
        }

        // Filter Kelas
        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        // Filter Mata Pelajaran
        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        // Filter Guru Pengajar (dari schedule)
        $teacherName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru_pengajar';
        $fileNameParts[] = $teacherName;

        // Filter Guru Pencatat (jika ada input filter di blade Anda)
        // Jika Anda memiliki input filter terpisah untuk guru pencatat (recorded_by), tambahkan ke blade dan request.
        // Asumsi nama inputnya 'recorded_by_user_id'
        // $recorderName = $selectedRecorder ? str_replace(' ', '_', strtolower($selectedRecorder->name)) : 'semua_guru_pencatat';
        // $fileNameParts[] = $recorderName;


        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        // --- End Customizing the file name ---

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
    /**
     * Menampilkan laporan jurnal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function journal(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $query = Journal::with(['teacher', 'class', 'subject']);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $journals = $query->paginate(20);

        $classes = Classes::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();

        return view('admin.reports.journal', compact('journals', 'classes', 'subjects', 'teachers', 'request'));
    }
   public function exportJournal(Request $request): StreamedResponse
    {
        // --- Validasi Request (opsional, tapi bagus untuk konsistensi) ---
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);
        // --- End Validasi Request ---

        $query = Journal::with(['teacher', 'class', 'subject']);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $journals = $query->get();

        // --- Fetch selected filter data for filename consistency ---
        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;
        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null;
        // --- End Fetch selected filter data ---

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = [
            'Tanggal', 'Guru', 'Kelas', 'Mata Pelajaran',
            'Materi', 'Deskripsi', 'Jam Mulai', 'Jam Selesai'
        ];
        $sheet->fromArray($headings, NULL, 'A1');

        $row = 2;
        foreach ($journals as $journal) {
            $sheet->setCellValue('A' . $row, $journal->date);
            $sheet->setCellValue('B' . $row, $journal->teacher->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $journal->class->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $journal->subject->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $journal->title);
            $sheet->setCellValue('F' . $row, $journal->description);
            $sheet->setCellValue('G' . $row, $journal->start_time);
            $sheet->setCellValue('H' . $row, $journal->end_time);
            $row++;
        }

        // --- Auto-size columns (opsional, tapi bagus) ---
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // --- End Auto-size columns ---

        // --- Customizing the file name to include class (mengikuti syntax exportAssessment) ---
        $fileNameParts = ['laporan_jurnal'];

        // Menambahkan filter Guru
        $guruName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru';
        $fileNameParts[] = $guruName;

        // Menambahkan filter Kelas
        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        // Menambahkan filter Mata Pelajaran
        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        // Menambahkan filter tanggal jika ada
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $fileNameParts[] = 'periode_' . $request->start_date . '_sd_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
             $fileNameParts[] = 'dari_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
             $fileNameParts[] = 'sampai_' . $request->end_date;
        } else {
            // Jika tidak ada filter tanggal spesifik, bisa ditambahkan 'semua_tanggal'
            $fileNameParts[] = 'semua_tanggal';
        }


        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        // --- End Customizing the file name ---

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Menampilkan laporan penilaian.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
public function assessment(Request $request)
    {
        $request->validate([
            'semester' => 'nullable|integer|min:1|max:2',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $query = Student::with(['class']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->paginate(20);

        $assessmentTypes = ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS', 'Latihan 1']; // Add Latihan 1 if you want it displayed

        $assessmentData = [];

        // Fetch selected subject and teacher for display in columns if filters are applied
        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::where('role', 'guru')->find($request->user_id) : null;


        foreach ($students as $student) {
            $studentRow = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_name' => $student->class->name ?? 'N/A',
                // --- Tambah ini untuk Guru dan Mata Pelajaran ---
                'teacher_name' => $selectedTeacher->name ?? 'Semua', // Mengambil nama guru yang difilter atau 'Semua'
                'subject_name' => $selectedSubject->name ?? 'Semua', // Mengambil nama mata pelajaran yang difilter atau 'Semua'
                // --- End Tambahan ---
            ];

            $assessmentQuery = Assessment::where('student_id', $student->id);

            if ($request->filled('semester')) {
                $assessmentQuery->where('semester', $request->semester);
            }

            if ($selectedSubject) { // Use $selectedSubject object here
                $assessmentQuery->where('subject_id', $selectedSubject->id);
            }

            if ($selectedTeacher) { // Use $selectedTeacher object here
                $assessmentQuery->where('user_id', $selectedTeacher->id);
            }

            $assessments = $assessmentQuery->get();

            foreach ($assessmentTypes as $type) {
                $score = $assessments->firstWhere('type', $type)->score ?? '-';
                $studentRow['score_' . str_replace(' ', '_', strtolower($type))] = $score;
            }

            $assessmentData[] = (object) $studentRow;
        }

        $classes = Classes::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'guru')->get();

        return view('admin.reports.assessment', compact(
            'assessmentData',
            'classes',
            'subjects',
            'students',
            'teachers',
            'request',
            'assessmentTypes'
        ));
    }


      public function exportAssessment(Request $request): StreamedResponse
    {
        $request->validate([
            'semester' => 'nullable|integer|min:1|max:2',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $assessmentTypes = ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS'];

        $studentQuery = Student::with(['class']);

        if ($request->filled('class_id')) {
            $studentQuery->where('class_id', $request->class_id);
        }

        $students = $studentQuery->get();

        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null;
        $selectedSemester = $request->input('semester');

        // --- Fetch selected class ---
        // Perhatikan di sini Anda menggunakan 'Classes::find'. Pastikan nama model Anda memang 'Classes' (plural).
        // Jika model Anda bernama 'Class' (singular), ubah menjadi 'Class::find'.
        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;
        // --- END Fetch selected class ---

        $exportData = [];

        $headings = ['Nama Siswa', 'Kelas', 'Guru', 'Mata Pelajaran'];
        foreach ($assessmentTypes as $type) {
            $headings[] = $type;
        }
        $exportData[] = $headings;

        foreach ($students as $student) {
            $rowData = [];
            $rowData[] = $student->name;
            $rowData[] = $student->class->name ?? 'N/A';

            $rowData[] = $selectedTeacher->name ?? 'Semua';
            $rowData[] = $selectedSubject->name ?? 'Semua';

            $studentAssessmentsQuery = Assessment::where('student_id', $student->id);

            if ($request->filled('semester')) {
                $studentAssessmentsQuery->where('semester', $request->semester);
            }
            if ($selectedSubject) {
                $studentAssessmentsQuery->where('subject_id', $selectedSubject->id);
            }
            if ($selectedTeacher) {
                $studentAssessmentsQuery->where('user_id', $selectedTeacher->id);
            }

            $studentAssessments = $studentAssessmentsQuery->get();

            foreach ($assessmentTypes as $type) {
                $score = $studentAssessments->firstWhere('type', $type)->score ?? '-';
                $rowData[] = $score;
            }
            $exportData[] = $rowData;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($exportData, NULL, 'A1');

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- Customizing the file name to include class (dengan logika 'semua') ---
        $fileNameParts = ['laporan_penilaian'];

        // Menambahkan filter Semester
        $semesterName = $selectedSemester ? 'semester_' . $selectedSemester : 'semua_semester';
        $fileNameParts[] = $semesterName;

        // Menambahkan filter Kelas
        // Perhatikan: Anda menggunakan 'Classes::find' di atas. Sesuaikan di sini juga jika modelnya 'Class'.
        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        // Menambahkan filter Mata Pelajaran
        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        // Menambahkan filter Guru
        $teacherName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru';
        $fileNameParts[] = $teacherName;

        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        // --- End Customizing the file name ---

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }


}
