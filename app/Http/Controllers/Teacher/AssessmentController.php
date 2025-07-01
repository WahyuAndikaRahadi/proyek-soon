<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Classes; // Renamed from 'Class' to 'Classes' for plural consistency
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Validation\Rule; // Add this line
use InvalidArgumentException; // For invalid parameters
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // For 404 errors

class AssessmentController extends Controller
{
    /**
     * Display a paginated list of assessments recorded by the authenticated teacher,
     * with filtering options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        // Build the query for assessments, eager loading related models for performance
        $assessmentsQuery = Assessment::where('user_id', $teacherId)
            ->with(['student.class', 'subject']);

        // Apply filters based on request parameters
        $assessmentsQuery->when($request->filled('semester'), function ($query) use ($request) {
            $query->where('semester', $request->semester);
        });

        $assessmentsQuery->when($request->filled('class_id'), function ($query) use ($request) {
            $query->whereHas('student.class', function ($q) use ($request) {
                $q->where('id', $request->class_id);
            });
        });

        $assessmentsQuery->when($request->filled('subject_id'), function ($query) use ($request) {
            $query->where('subject_id', $request->subject_id);
        });

        $assessmentsQuery->when($request->filled('type'), function ($query) use ($request) {
            $query->where('type', 'like', '%' . $request->type . '%');
        });

        // Order and paginate the results
        $assessments = $assessmentsQuery->orderBy('date', 'desc')->paginate(36);

        // Get subjects and classes assigned to the current teacher for filter dropdowns
        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        // Retrieve all unique assessment types previously entered by this teacher for autosuggest
        $allAssessmentTypes = Assessment::where('user_id', $teacherId)
                                        ->distinct('type')
                                        ->pluck('type')
                                        ->sort() // Sort alphabetically
                                        ->toArray();

        return view('teacher.assessments.index', compact(
            'assessments',
            'teacherSubjects',
            'teacherClasses',
            'allAssessmentTypes',
            'request' // Pass the request to retain filter selections in the view
        ));
    }

    /**
     * Show the form for creating a new assessment or selecting a class for bulk input/upload.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $teacherId = Auth::id();
        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        // Get unique assessment types for autosuggest in the form
        $allAssessmentTypes = Assessment::where('user_id', $teacherId)
                                        ->distinct('type')
                                        ->pluck('type')
                                        ->sort()
                                        ->toArray();

        $selectedClass = null;
        $students = collect(); // Initialize as an empty collection

        // If a class is selected, fetch its details and students
        if ($request->filled('class_id')) {
            $selectedClass = Classes::findOrFail($request->class_id);
            // Ensure the teacher is assigned to this class before fetching students
            if (!$teacherClasses->contains('id', $selectedClass->id)) {
                abort(403, 'Akses Dilarang. Anda tidak mengajar di kelas ini.');
            }
            $students = Student::where('class_id', $selectedClass->id)->orderBy('name')->get();
        }

        return view('teacher.assessments.create', compact(
            'teacherSubjects',
            'teacherClasses',
            'allAssessmentTypes',
            'selectedClass',
            'students'
        ));
    }



    /**
     * Store newly created assessments in the database from manual input.
     * Uses updateOrCreate to prevent duplicates based on student, subject, semester, and type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
        public function store(Request $request)
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id', Rule::in(Auth::user()->classes->pluck('id'))],
            'subject_id' => ['required', 'exists:subjects,id', Rule::in(Auth::user()->subjects->pluck('id'))],
            'semester' => 'required|integer|min:1|max:2',
            'type' => 'required|string|max:100',
            'date' => 'required|date',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score' => 'nullable|numeric|min:0|max:100', // Allow null for score
            'scores.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->scores as $data) {
                // Verify student belongs to the selected class
                $student = Student::where('id', $data['student_id'])
                                    ->where('class_id', $request->class_id)
                                    ->firstOrFail(); // Fails if student not found in this class

                $searchCriteria = [
                    'student_id' => $data['student_id'],
                    'subject_id' => $request->subject_id,
                    'semester' => $request->semester,
                    'type' => $request->type,
                ];

                $updateData = [
                    'user_id' => Auth::id(),
                    'date' => $request->date,
                    'notes' => $data['notes'] ?? null,
                    'class_id' => $request->class_id,
                ];

                // If score is provided and is a valid number, include it in update/create
                if (isset($data['score']) && is_numeric($data['score'])) {
                    $updateData['score'] = (float) $data['score'];
                    // Use updateOrCreate when a score is explicitly given
                    Assessment::updateOrCreate($searchCriteria, $updateData);
                } else {
                    // If no score is provided (empty/null), we just want to ensure
                    // the assessment exists for this type, but don't overwrite
                    // an existing score with null if it already has a value.
                    // If no score is provided and no existing assessment, we might still want to create an entry for notes.
                    $existingAssessment = Assessment::where($searchCriteria)->first();

                    if ($existingAssessment) {
                        // If an assessment exists, only update notes or other non-score fields
                        // unless an explicit score was passed (handled above)
                        $existingAssessment->update([
                            'user_id' => Auth::id(), // Ensure user_id is consistent
                            'date' => $request->date,
                            'notes' => $data['notes'] ?? null,
                            'class_id' => $request->class_id,
                        ]);
                    } else {
                        // If no assessment exists and no score is provided,
                        // you might still want to create an entry if there's a note.
                        // Or, you might want to completely skip creating if no score AND no note.
                        // I'll assume you want to create if at least a note is present, or if it's the very first time.
                        // If you strictly want to skip if no score, you can add another condition here.
                        if (!empty($data['notes'])) { // Only create if there's a note, even if no score
                             Assessment::create(array_merge($searchCriteria, $updateData));
                        }
                    }
                }
            }
            DB::commit();
            return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil dicatat.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the detailed error for debugging
            \Log::error('Error storing assessment: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors('Gagal menyimpan penilaian: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Show the form for editing a specific assessment.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\View\View
     */
    public function edit(Assessment $assessment)
    {
        // Authorization check: Ensure only the creator teacher can edit.
        if ($assessment->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk mengedit penilaian ini.');
        }

        $teacherSubjects = Auth::user()->subjects;
        $teacherClasses = Auth::user()->classes;

        // Fetch existing assessment types for autosuggest in the edit form
        $existingAssessmentTypes = Assessment::where('user_id', Auth::id())
            ->distinct('type')
            ->pluck('type')
            ->sort()
            ->toArray();

        return view('teacher.assessments.edit', compact(
            'assessment',
            'teacherSubjects',
            'teacherClasses',
            'existingAssessmentTypes'
        ));
    }

    /**
     * Update the specified assessment in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Assessment $assessment)
    {
        // Authorization check
        if ($assessment->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk memperbarui penilaian ini.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
            'type' => 'required|string|max:100',
        ]);

        try {
            $assessment->update([
                'score' => (float) $request->score,
                'date' => $request->date,
                'notes' => $request->notes ?? null,
                'type' => $request->type,
            ]);
            return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Error updating assessment: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors('Gagal memperbarui penilaian: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified assessment from the database.
     *
     * @param  \App\Models\Assessment  $assessment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Assessment $assessment)
    {
        // Authorization check
        if ($assessment->user_id !== Auth::id()) {
            abort(403, 'Akses Dilarang. Anda tidak memiliki izin untuk menghapus penilaian ini.');
        }

        try {
            $assessment->delete();
            return redirect()->route('teacher.assessments.index')->with('success', 'Penilaian berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting assessment: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors('Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Downloads an Excel template for assessments, pre-filled with existing data
     * for the selected class and semester.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classId The ID of the class.
     * @param  int  $semester The semester (1 or 2).
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\Response
     * @throws InvalidArgumentException If an invalid semester is provided.
     * @throws NotFoundHttpException If the template file is not found or class not found.
     * @throws \Exception On other errors during spreadsheet processing.
     */
public function downloadTemplate(Request $request, $classId, $semester)
    {
        $teacherId = Auth::id();

        // Fetch the class and ensure the teacher is associated with it
        $class = Classes::with('students')->findOrFail($classId);
        if (!Auth::user()->classes->contains('id', $class->id)) {
            throw new NotFoundHttpException('Kelas tidak ditemukan atau Anda tidak memiliki akses ke kelas ini.');
        }

        // Determine template path and file suffix based on semester
        $templatePath = '';
        $fileNameSuffix = '';
        if ($semester == 1) {
            $templatePath = storage_path('app/templates/template_semester_1.xlsx');
            $fileNameSuffix = 'Semester_1_SAS'; // Assuming SAS for Semester 1 Assessment
        } elseif ($semester == 2) {
            $templatePath = storage_path('app/templates/template_semester_2.xlsx');
            $fileNameSuffix = 'Semester_2_SAT'; // Assuming SAT for Semester 2 Assessment
        } else {
            throw new InvalidArgumentException('Semester tidak valid. Harus 1 atau 2.');
        }

        // Check if the template file exists
        if (!file_exists($templatePath)) {
            throw new NotFoundHttpException('File template tidak ditemukan di server: ' . basename($templatePath));
        }

        try {
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // Update class and semester info on the template
            $sheet->setCellValue('A3', 'Kelas: ' . $class->name);
            $sheet->setCellValue('A4', 'Semester: ' . $semester);

            // Dynamically get assessment type headers from row 12 (e.g., 'Tugas 1', 'UTS')
            // Assumes headers are in columns D to K (now K is the last).
            $assessmentTypeHeaders = [];
            foreach (range('D', 'K') as $col) { // Changed 'L' to 'K'
                $headerValue = trim($sheet->getCell($col . '12')->getValue());
                if (!empty($headerValue)) {
                    $assessmentTypeHeaders[$headerValue] = $col; // Map header name to column letter
                }
            }

            if (empty($assessmentTypeHeaders)) {
                throw new \Exception("Tidak dapat menemukan header tipe penilaian di baris 12 (kolom D-K). Pastikan template sudah benar.");
            }

            // Fetch students for the selected class, ordered by name
            $students = $class->students->sortBy('name');

            // Fetch existing assessments for these students, for the current teacher, and selected subject/semester
            // Group them by student ID and then by assessment type for easy lookup
            $existingAssessments = Assessment::whereIn('student_id', $students->pluck('id'))
                ->where('semester', $semester)
                ->where('user_id', $teacherId)
                ->whereIn('subject_id', Auth::user()->subjects->pluck('id')) // Only subjects the teacher teaches
                ->get()
                ->groupBy(['student_id', 'type']);

            // Populate student data and existing assessment scores starting from row 14
            $startRowData = 14;
            foreach ($students as $index => $student) {
                $currentRow = $startRowData + $index;

                $sheet->setCellValue('B' . $currentRow, $student->nis);
                $sheet->setCellValue('C' . $currentRow, $student->name);

                // Pre-fill existing scores
                if (isset($existingAssessments[$student->id])) {
                    foreach ($assessmentTypeHeaders as $type => $col) {
                        if (isset($existingAssessments[$student->id][$type])) {
                            // Assuming only one score per student/type/semester exists
                            $assessment = $existingAssessments[$student->id][$type]->first();
                            $sheet->setCellValue($col . $currentRow, $assessment->score);
                        }
                    }
                    // Removed the logic for populating the 'Notes' column (Column L)
                }

                // Apply borders to the filled row for consistent styling
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                // Changed 'L' to 'K' for styling
                $sheet->getStyle('B' . $currentRow . ':K' . $currentRow)->applyFromArray($styleArray);
            }

            // Set column widths for better presentation
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            // Changed 'L' to 'K' for column dimensions
            foreach (range('D', 'K') as $col) {
                $sheet->getColumnDimension($col)->setWidth(12); // Adjust width as needed
            }

            // Prepare the filename and send the file as a download
            $fileName = 'Template_Penilaian_' . $class->name . '_' . $fileNameSuffix . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            // Clean any previous output buffer to prevent corrupted file downloads
            if (ob_get_contents()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            exit; // Stop execution after sending the file

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            \Log::error('PhpSpreadsheet error during template download: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \Exception('Terjadi kesalahan saat membuat atau membaca template Excel: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            \Log::error('General error during template download: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \Exception('Terjadi kesalahan umum saat mengunduh template: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Imports assessment scores from an uploaded Excel file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'class_id' => ['required', 'exists:classes,id', Rule::in(Auth::user()->classes->pluck('id'))],
            'subject_id' => ['required', 'exists:subjects,id', Rule::in(Auth::user()->subjects->pluck('id'))],
            'semester' => 'required|integer|min:1|max:2',
            'date' => 'required|date',
        ]);

        $file = $request->file('excel_file');
        $teacherId = Auth::id();
        $classId = $request->class_id;
        $subjectId = $request->subject_id;
        $semester = $request->semester;
        $assessmentDate = $request->date;

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $importedAssessmentsCount = 0;
            $errors = [];
            $startRowData = 14; // Data starts from row 14 in the template

            // Get assessment type headers from row 12 (columns D-K)
            $assessmentTypeHeaders = [];
            foreach (range('D', 'K') as $col) {
                $headerValue = trim($sheet->getCell($col . '12')->getValue());
                if (!empty($headerValue)) {
                    $assessmentTypeHeaders[$col] = $headerValue; // Map column letter to type name
                }
            }

            if (empty($assessmentTypeHeaders)) {
                throw new \Exception("Tidak dapat menemukan header tipe penilaian di baris 12 (kolom D-K). Pastikan template sudah benar.");
            }

            DB::beginTransaction();

            for ($row = $startRowData; $row <= $highestRow; $row++) {
                $nis = trim($sheet->getCell('B' . $row)->getValue());

                // Stop processing if NIS is empty, assuming no more valid data below
                if (empty($nis)) {
                    break;
                }

                $studentName = trim($sheet->getCell('C' . $row)->getValue());

                // Find the student by NIS and class ID, ensuring they belong to the current class
                $student = Student::where('nis', $nis)
                                    ->where('class_id', $classId)
                                    ->first();

                if (!$student) {
                    $errors[] = "Baris $row (NIS: '$nis', Nama: '$studentName'): Siswa tidak ditemukan di kelas yang dipilih atau NIS tidak cocok. Data baris ini dilewati.";
                    continue; // Skip to the next row
                }

                $notes = trim($sheet->getCell('L' . $row)->getValue()); // Column L for Notes

                // Loop through each assessment type identified from headers
                foreach ($assessmentTypeHeaders as $col => $type) {
                    $scoreValue = $sheet->getCell($col . $row)->getCalculatedValue();

                    // --- Perubahan Penting di sini ---
                    // Hanya proses jika scoreValue tidak null atau tidak kosong
                    if ($scoreValue === null || $scoreValue === '') {
                        // Jika nilai kosong di Excel, kita tidak melakukan apa-apa
                        // Ini berarti nilai yang sudah ada di database akan tetap utuh.
                        continue; // Lewati ke tipe penilaian berikutnya
                    }
                    // --- Akhir Perubahan ---

                    // Validate the score from Excel
                    if (!is_numeric($scoreValue) || $scoreValue < 0 || $scoreValue > 100) {
                        $errors[] = "Baris $row (NIS: '$nis', Tipe: '$type'): Nilai '" . htmlspecialchars($scoreValue) . "' tidak valid (harus angka antara 0-100). Penilaian ini tidak disimpan.";
                        continue; // Skip to the next assessment type for this student
                    }

                    Assessment::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subjectId,
                            'semester' => $semester,
                            'type' => $type, // Assessment type from Excel header
                        ],
                        [
                            'user_id' => $teacherId,
                            'score' => (float) $scoreValue,
                            'date' => $assessmentDate,
                            'notes' => $notes ?? null, // General notes for the row
                            'class_id' => $classId, // Store class_id for easier filtering later
                        ]
                    );
                    $importedAssessmentsCount++;
                }
            }

            // If there are any errors, rollback the transaction and show messages
            if (!empty($errors)) {
                DB::rollBack();
                // Use ValidationException to automatically display errors above the form
                throw ValidationException::withMessages(['excel_import_errors' => $errors]);
            }

            DB::commit();

            return redirect()->route('teacher.assessments.index')->with('success', $importedAssessmentsCount . ' penilaian berhasil diimpor dari Excel.');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            DB::rollBack();
            \Log::error('PhpSpreadsheet error during Excel import: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['excel_file' => 'Terjadi kesalahan saat membaca file Excel: ' . $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('General error during Excel import: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['import_error' => 'Terjadi kesalahan umum saat mengimpor: ' . $e->getMessage()])->withInput();
        }
    }
}

