<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory; // Import IOFactory for reading
use PhpOffice\PhpSpreadsheet\Spreadsheet; // Import Spreadsheet

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa, dengan opsi filter berdasarkan kelas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mulai query untuk Student dengan eager loading relasi 'class'
        $query = Student::with('class');

        // Ambil ID kelas dari request, jika ada
        $selectedClassId = $request->input('class_id');

        // Jika class_id ada dan tidak kosong, tambahkan filter ke query
        if ($selectedClassId) {
            $query->where('class_id', $selectedClassId);
        }

        // Ambil data siswa dengan paginasi
        $students = $query->paginate(36);

        // Ambil semua data kelas untuk dropdown filter
        $classes = Classes::all();

        // Kirim data siswa, semua kelas, dan ID kelas yang dipilih ke view
        return view('admin.students.index', compact('students', 'classes', 'selectedClassId'));
    }

    /**
     * Menampilkan form untuk membuat siswa baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $classes = Classes::all();
        return view('admin.students.create', compact('classes'));
    }

    /**
     * Menyimpan siswa baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:255|unique:students',
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'date_of_birth' => 'nullable|date',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit siswa.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\View\View
     */
    public function edit(Student $student)
    {
        $classes = Classes::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Memperbarui data siswa di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students')->ignore($student->id),
            ],
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'gender' => 'required|in:L,P',
            'date_of_birth' => 'nullable|date',
        ]);

        $student->update($request->all());

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus siswa dari database.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Menampilkan form untuk mengunggah file Excel.
     *
     * @return \Illuminate\View\View
     */
    public function importForm()
    {
        $classes = Classes::all(); // Fetch classes for the dropdown
        return view('admin.students.import', compact('classes'));
    }

    /**
     * Mengimpor data siswa dari file Excel menggunakan PhpOffice\PhpSpreadsheet.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'class_id' => 'required|exists:classes,id',
        ]);

        $file = $request->file('file');
        $classId = $request->input('class_id');
        $importErrors = [];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $nisColumn = 'B';
            $nameColumn = 'C';
            $genderColumn = 'G';
            $startDataRow = 20;

            for ($row = $startDataRow; $row <= $highestRow; $row++) {
                $nis = trim($worksheet->getCell($nisColumn . $row)->getValue());
                $name = trim($worksheet->getCell($nameColumn . $row)->getValue());
                $gender = trim($worksheet->getCell($genderColumn . $row)->getValue());

                // --- MODIFICATION START ---
                // If the NIS column is empty, assume it's the end of the data and break the loop.
                if (empty($nis)) {
                    // You can optionally add a log here if you want to know where it stopped
                    // \Log::info("Import stopped at row $row because NIS column was empty.");
                    break;
                }
                // --- MODIFICATION END ---

                // Basic validation for name and gender (NIS is already checked for emptiness above)
                if (empty($name) || empty($gender)) {
                    $importErrors[] = "Baris $row: Data Nama Siswa atau Gender tidak lengkap."; // Removed NIS from this message
                    continue;
                }

                // Gender validation
                $gender = strtoupper($gender);
                if (!in_array($gender, ['L', 'P'])) {
                    $importErrors[] = "Baris $row: Gender '$gender' tidak valid. Harus 'L' atau 'P'.";
                    continue;
                }

                // Check for unique NIS
                if (Student::where('nis', $nis)->exists()) {
                    $importErrors[] = "Baris $row: NIS '$nis' sudah ada dalam database.";
                    continue;
                }

                // If all validations pass, create the student
                try {
                    Student::create([
                        'nis' => $nis,
                        'name' => $name,
                        'gender' => $gender,
                        'class_id' => $classId,
                        'date_of_birth' => null,
                    ]);
                } catch (\Exception $e) {
                    $importErrors[] = "Baris $row: Gagal menyimpan siswa. Error: " . $e->getMessage();
                }
            }

            if (!empty($importErrors)) {
                return redirect()->back()->with('error', 'Beberapa data gagal diimpor:<br>' . implode('<br>', $importErrors));
            }

            return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diimpor.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}