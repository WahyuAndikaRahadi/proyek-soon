<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Journal;
use App\Models\Assessment;
use App\Models\Classes; // Assuming 'Classes' is the model name for classes
use App\Models\Subject;
use App\Models\User; // For teachers
use App\Models\Student; // For students
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border; // Import Border class
use PhpOffice\PhpSpreadsheet\Style\Alignment; // Import Alignment class
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Import Coordinate class for dynamic column calculation

class ReportController extends Controller
{
    /**
     * Display the main report page for admins.
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
     * Display attendance reports for admins.
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
            'user_id' => 'nullable|exists:users,id', // This is for the teacher associated with the schedule
        ]);

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
     * Export attendance reports to Excel for admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAttendance(Request $request): StreamedResponse
    {
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

        $templatePath = storage_path('app/templates/template_absensi.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Data starts from row 13, column B
        $startDataRow = 13;
        $currentRow = $startDataRow;

        foreach ($attendances as $attendance) {
            $sheet->setCellValue('B' . $currentRow, Carbon::parse($attendance->date)->format('Y-m-d'));
            $sheet->setCellValue('C' . $currentRow, $attendance->student->name ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $attendance->student->class->name ?? 'N/A');
            $sheet->setCellValue('E' . $currentRow, $attendance->schedule->subject->name ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $attendance->schedule->start_time ?? 'N/A');
            $sheet->setCellValue('G' . $currentRow, $attendance->schedule->end_time ?? 'N/A');
            $sheet->setCellValue('H' . $currentRow, $attendance->status);
            $sheet->setCellValue('I' . $currentRow, $attendance->notes ?? '-');
            $sheet->setCellValue('J' . $currentRow, $attendance->schedule->teacher->name ?? 'N/A');
            $currentRow++;
        }

        // Apply borders only to the new data rows if they extend beyond the template's initial range
        // Assuming template's pre-defined border ends at row 49, for example.
        // If the number of data rows ($currentRow - 1 - $startDataRow + 1) is greater than (49 - $startDataRow + 1)
        // or simply if $currentRow - 1 > 49, apply borders from row 50 onwards.
        
        $templateBorderEndRow = 49; // Define where your template's border usually ends
        $highestRowWithData = $currentRow - 1; // The last row where data was written

        if ($highestRowWithData > $templateBorderEndRow) {
            // Apply borders from the row AFTER the template's last bordered row
            // to the highest row where data exists.
            $borderStartRow = $templateBorderEndRow + 1;
            $highestColumn = 'J'; // Assuming 'J' is the last column with data in this report

            $borderRange = 'B' . $borderStartRow . ':' . $highestColumn . $highestRowWithData;
            
            $sheet->getStyle($borderRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Auto-size columns from B to J
        foreach (range('B', 'J') as $col) { // Fixed range for auto-size based on the columns with data
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;
        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null;

        $fileNameParts = ['laporan_absensi'];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $fileNameParts[] = 'periode_' . $request->start_date . '_sd_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
            $fileNameParts[] = 'dari_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
            $fileNameParts[] = 'sampai_' . $request->end_date;
        } else {
            $fileNameParts[] = 'semua_tanggal';
        }

        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        $teacherName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru_pengajar';
        $fileNameParts[] = $teacherName;

        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

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
     * Display journal reports for admins.
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



    /**
     * Export journal reports to Excel for admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportJournal(Request $request): StreamedResponse
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

        $journals = $query->get();

        $templatePath = storage_path('app/templates/template_jurnal.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Data starts from row 13, column B
        $startDataRow = 13;
        $currentRow = $startDataRow;

        foreach ($journals as $journal) {
            $sheet->setCellValue('B' . $currentRow, Carbon::parse($journal->date)->format('Y-m-d'));
            $sheet->setCellValue('C' . $currentRow, $journal->teacher->name ?? 'N/A');
            $sheet->setCellValue('D' . $currentRow, $journal->class->name ?? 'N/A');
            $sheet->setCellValue('E' . $currentRow, $journal->subject->name ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $journal->title);
            $sheet->setCellValue('G' . $currentRow, $journal->description ?? '-');
            $sheet->setCellValue('H' . $currentRow, $journal->start_time);
            $sheet->setCellValue('I' . $currentRow, $journal->end_time);
            $currentRow++;
        }

        // Apply borders only to the new data rows if they extend beyond the template's initial range
        $templateBorderEndRow = 49; // Define where your template's border usually ends
        $highestRowWithData = $currentRow - 1; // The last row where data was written

        if ($highestRowWithData > $templateBorderEndRow) {
            $borderStartRow = $templateBorderEndRow + 1;
            $highestColumn = 'I'; // Assuming 'I' is the last column with data in this report

            $borderRange = 'B' . $borderStartRow . ':' . $highestColumn . $highestRowWithData;
            
            $sheet->getStyle($borderRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Auto-size columns from B to I
        foreach (range('B', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;
        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null;

        $fileNameParts = ['laporan_jurnal'];

        $guruName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru';
        $fileNameParts[] = $guruName;

        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $fileNameParts[] = 'periode_' . $request->start_date . '_sd_' . $request->end_date;
        } elseif ($request->filled('start_date')) {
            $fileNameParts[] = 'dari_' . $request->start_date;
        } elseif ($request->filled('end_date')) {
            $fileNameParts[] = 'sampai_' . $request->end_date;
        } else {
            $fileNameParts[] = 'semua_tanggal';
        }

        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

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
     * Display assessment reports for admins.
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
            'user_id' => 'nullable|exists:users,id', // This is for the teacher who recorded the assessment
        ]);

        $query = Student::with(['class']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->paginate(20);

        // Define all possible assessment types
        $allAssessmentTypes = [
            'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6',
            'STS1', 'STS2', 'SAS', 'SAT'
        ];

        // Determine which assessment types to display based on the selected semester
        $selectedSemester = $request->input('semester');
        $assessmentTypesForDisplay = [];

        if ($selectedSemester == '1') {
            $assessmentTypesForDisplay = ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS1', 'SAS'];
        } elseif ($selectedSemester == '2') {
            $assessmentTypesForDisplay = ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS2', 'SAT'];
        } else {
            // If no semester is selected, show all types for display, or you can choose to show none.
            // For now, let's show all for "Semua Semester" view, or adjust as needed.
            $assessmentTypesForDisplay = $allAssessmentTypes;
        }

        $assessmentData = [];

        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::where('role', 'guru')->find($request->user_id) : null;

        foreach ($students as $student) {
            $studentRow = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_name' => $student->class->name ?? 'N/A',
                'teacher_name' => $selectedTeacher->name ?? 'Semua',
                'subject_name' => $selectedSubject->name ?? 'Semua',
            ];

            $assessmentQuery = Assessment::where('student_id', $student->id);

            // Filter assessments by selected semester if provided
            if ($request->filled('semester')) {
                $assessmentQuery->where('semester', $request->semester);
            }

            if ($selectedSubject) {
                $assessmentQuery->where('subject_id', $selectedSubject->id);
            }

            if ($selectedTeacher) {
                $assessmentQuery->where('user_id', $selectedTeacher->id);
            }

            $assessments = $assessmentQuery->get();

            // Populate scores only for the types relevant to the current display
            foreach ($assessmentTypesForDisplay as $type) {
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
            'assessmentTypesForDisplay' // Pass the filtered types to the view
        ));
    }

    
    /**
     * Export assessment reports to Excel for admins.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportAssessment(Request $request): StreamedResponse
    {
        $request->validate([
            'semester' => 'nullable|integer|min:1|max:2',
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        // Define assessment types based on selected semester for export
        $selectedSemester = $request->input('semester');
        $assessmentTypesForExport = [];

        if ($selectedSemester == '1') {
            $assessmentTypesForExport = ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS1', 'SAS'];
        } elseif ($selectedSemester == '2') {
            $assessmentTypesForExport = ['Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6', 'STS2', 'SAT'];
        } else {
            // If no semester is selected, include all types in the export
            $assessmentTypesForExport = [
                'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6',
                'STS1', 'STS2', 'SAS', 'SAT'
            ];
        }

        $studentQuery = Student::with(['class']);

        if ($request->filled('class_id')) {
            $studentQuery->where('class_id', $request->class_id);
        }

        $students = $studentQuery->get();

        $selectedSubject = $request->filled('subject_id') ? Subject::find($request->subject_id) : null;
        $selectedTeacher = $request->filled('user_id') ? User::find($request->user_id) : null;
        $selectedClass = $request->filled('class_id') ? Classes::find($request->class_id) : null;

        $templatePath = storage_path('app/templates/template_penilaian.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Update headers in row 12 based on the selected semester
        // Starting from column B for 'Nama Siswa', 'Kelas', 'Guru', 'Mata Pelajaran'
        // Then dynamically add assessment type headers
        $headerRow = 12; // Your header row in the template
        $currentColIndex = Coordinate::columnIndexFromString('B'); // Start at column B

        // Static Headers
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColIndex++). $headerRow, 'Nama Siswa');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColIndex++). $headerRow, 'Kelas');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColIndex++). $headerRow, 'Guru');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColIndex++). $headerRow, 'Mata Pelajaran');

        // Dynamic Assessment Type Headers
        foreach ($assessmentTypesForExport as $type) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColIndex++). $headerRow, $type);
        }

        // Data starts from row 13, column B
        $startDataRow = 13;
        $currentRow = $startDataRow;

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

            // Populate scores based on assessment types selected for export
            foreach ($assessmentTypesForExport as $type) {
                $score = $studentAssessments->firstWhere('type', $type)->score ?? '-';
                $rowData[] = $score;
            }
            
            // Populate the row in the sheet, starting from column B
            $sheet->fromArray($rowData, NULL, 'B' . $currentRow);
            $currentRow++;
        }

        // Apply borders only to the new data rows if they extend beyond the template's initial range
        $templateBorderEndRow = 49; // Define where your template's border usually ends
        $highestRowWithData = $currentRow - 1; // The last row where data was written

        // Calculate the actual highest column used by assessment types for the border
        $startingColIndex = Coordinate::columnIndexFromString('B'); // 2 for 'B'
        // 4 (Nama Siswa, Kelas, Guru, Mata Pelajaran) + count($assessmentTypesForExport)
        $endColIndex = $startingColIndex + (4 + count($assessmentTypesForExport)) - 1; 
        $highestColumn = Coordinate::stringFromColumnIndex($endColIndex);


        if ($highestRowWithData > $templateBorderEndRow) {
            $borderStartRow = $templateBorderEndRow + 1;

            $borderRange = 'B' . $borderStartRow . ':' . $highestColumn . $highestRowWithData;
            
            $sheet->getStyle($borderRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);
        }

        // Auto-size columns from B to the dynamically calculated highest column
        foreach (range('B', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileNameParts = ['laporan_penilaian'];

        $semesterName = $selectedSemester ? 'semester_' . $selectedSemester : 'semua_semester';
        $fileNameParts[] = $semesterName;

        $className = $selectedClass ? 'kelas_' . str_replace(' ', '_', strtolower($selectedClass->name)) : 'semua_kelas';
        $fileNameParts[] = $className;

        $subjectName = $selectedSubject ? str_replace(' ', '_', strtolower($selectedSubject->name)) : 'semua_mapel';
        $fileNameParts[] = $subjectName;

        $teacherName = $selectedTeacher ? str_replace(' ', '_', strtolower($selectedTeacher->name)) : 'semua_guru';
        $fileNameParts[] = $teacherName;

        $fileName = implode('_', $fileNameParts) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';

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