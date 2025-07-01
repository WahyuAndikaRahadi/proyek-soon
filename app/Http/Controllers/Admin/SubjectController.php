<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Menampilkan daftar semua mata pelajaran.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $subjects = Subject::paginate(10);
        return view('admin.subjects.index', compact('subjects'));
    }

    /**
     * Menampilkan form untuk membuat mata pelajaran baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Mengirimkan tipe mata pelajaran yang tersedia ke view
        $subjectTypes = ['umum', 'kejuruan'];
        return view('admin.subjects.create', compact('subjectTypes'));
    }

    /**
     * Menyimpan mata pelajaran baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'description' => 'nullable|string',
            'type' => ['required', 'string', Rule::in(['umum', 'kejuruan'])], // Validasi type
            'kktp' => 'nullable|integer|min:0|max:100', // Validasi kktp, tetap nullable
        ]);

        // --- Logika untuk mengatur nilai default kktp dihapus ---
        // if (!isset($validatedData['kktp'])) {
        //     if ($validatedData['type'] === 'umum') {
        //         $validatedData['kktp'] = 75;
        //     } elseif ($validatedData['type'] === 'kejuruan') {
        //         $validatedData['kktp'] = 78;
        //     }
        // }

        Subject::create($validatedData);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit mata pelajaran.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\View\View
     */
    public function edit(Subject $subject)
    {
        // Mengirimkan tipe mata pelajaran yang tersedia ke view
        $subjectTypes = ['umum', 'kejuruan'];
        return view('admin.subjects.edit', compact('subject', 'subjectTypes'));
    }

    /**
     * Memperbarui data mata pelajaran di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Subject $subject)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')->ignore($subject->id),
            ],
            'description' => 'nullable|string',
            'type' => ['required', 'string', Rule::in(['umum', 'kejuruan'])], // Validasi type
            'kktp' => 'nullable|integer|min:0|max:100', // Validasi kktp, tetap nullable
        ]);

        // --- Logika untuk mengatur nilai default kktp dihapus ---
        // if (!isset($validatedData['kktp']) || empty($validatedData['kktp'])) {
        //     if ($validatedData['type'] === 'umum') {
        //         $validatedData['kktp'] = 75;
        //     } elseif ($validatedData['type'] === 'kejuruan') {
        //         $validatedData['kktp'] = 78;
        //     }
        // }

        $subject->update($validatedData);

        return redirect()->route('admin.subjects.index')->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus mata pelajaran dari database.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}