<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Classes; // Menggunakan model Classes
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar semua siswa.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $students = Student::with('class')->paginate(10);
        return view('admin.students.index', compact('students'));
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
}
