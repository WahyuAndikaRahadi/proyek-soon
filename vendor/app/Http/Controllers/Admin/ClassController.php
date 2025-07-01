<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes; // Menggunakan model Classes
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    /**
     * Menampilkan daftar semua kelas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $classes = Classes::paginate(10);
        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Menampilkan form untuk membuat kelas baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.classes.create');
    }

    /**
     * Menyimpan kelas baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:classes',
            'grade_level' => 'required|integer|min:1|max:12', // Asumsi tingkat kelas 1-12
        ]);

        Classes::create($request->all());

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kelas.
     *
     * @param  \App\Models\Classes  $class
     * @return \Illuminate\View\View
     */
    public function edit(Classes $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    /**
     * Memperbarui data kelas di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classes  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->ignore($class->id),
            ],
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        $class->update($request->all());

        return redirect()->route('admin.classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus kelas dari database.
     *
     * @param  \App\Models\Classes  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Classes $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
