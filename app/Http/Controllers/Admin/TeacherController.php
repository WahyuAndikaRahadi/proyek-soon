<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classes; // Make sure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Menampilkan daftar semua guru.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $teachers = User::where('role', 'guru')->with('subjects', 'classes')->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Menampilkan form untuk membuat guru baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $subjects = Subject::all();
        $classes = Classes::all();
        return view('admin.teachers.create', compact('subjects', 'classes'));
    }

    /**
     * Menyimpan guru baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nip' => 'nullable|string|max:255|unique:users',
            'photo_url' => 'nullable|url|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'homeroom_class_id' => 'nullable|exists:classes,id', // For the dropdown
            'taught_classes' => 'nullable|array', // For the new checkboxes
            'taught_classes.*' => 'exists:classes,id', // Validation for taught classes
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
            'nip' => $request->nip,
            'photo_url' => $request->photo_url,
        ]);

        if ($request->has('subjects')) {
            $teacher->subjects()->attach($request->subjects);
        }

        // Handle homeroom class (wali kelas)
        if ($request->filled('homeroom_class_id')) {
            $teacher->classes()->attach($request->homeroom_class_id, ['is_homeroom_teacher' => true]);
        }

        // Handle taught classes
        if ($request->has('taught_classes')) {
            // First, get the IDs of the classes that are *not* the homeroom class
            $taughtClassesToAttach = collect($request->taught_classes)->filter(function ($classId) use ($request) {
                return $classId != $request->homeroom_class_id;
            })->mapWithKeys(function ($classId) {
                return [$classId => ['is_homeroom_teacher' => false]];
            })->all();

            // Merge with homeroom class if it exists and attach
            if ($request->filled('homeroom_class_id')) {
                 $taughtClassesToAttach[$request->homeroom_class_id] = ['is_homeroom_teacher' => true];
            }
            $teacher->classes()->sync($taughtClassesToAttach);
        } else {
             // If no taught classes are selected but a homeroom is, ensure only homeroom is synced
             if ($request->filled('homeroom_class_id')) {
                 $teacher->classes()->sync([$request->homeroom_class_id => ['is_homeroom_teacher' => true]]);
             } else {
                 $teacher->classes()->sync([]); // No classes selected, detach all
             }
        }


        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit guru.
     *
     * @param  \App\Models\User  $teacher (Menggunakan route model binding)
     * @return \Illuminate\View\View
     */
    public function edit(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $subjects = Subject::all();
        $classes = Classes::all();

        $teacherSubjects = $teacher->subjects->pluck('id')->toArray();

        // Get the homeroom class ID
        $homeroomClass = $teacher->classes->firstWhere('pivot.is_homeroom_teacher', true);
        $teacherHomeroomClassId = $homeroomClass ? $homeroomClass->id : null;

        // Get the taught class IDs (all classes the teacher is associated with)
        $teacherTaughtClasses = $teacher->classes->pluck('id')->toArray();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes', 'teacherSubjects', 'teacherHomeroomClassId', 'teacherTaughtClasses'));
    }

    /**
     * Memperbarui data guru di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $teacher (Menggunakan route model binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($teacher->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('users')->ignore($teacher->id),
            ],
            'photo_url' => 'nullable|url|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'homeroom_class_id' => 'nullable|exists:classes,id', // For the dropdown
            'taught_classes' => 'nullable|array', // For the new checkboxes
            'taught_classes.*' => 'exists:classes,id', // Validation for taught classes
        ]);

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'photo_url' => $request->photo_url,
            'password' => $request->password ? Hash::make($request->password) : $teacher->password,
        ]);

        $teacher->subjects()->sync($request->subjects ?? []);

        $newClassesToSync = [];

        // Handle homeroom class
        if ($request->filled('homeroom_class_id')) {
            $newClassesToSync[$request->homeroom_class_id] = ['is_homeroom_teacher' => true];
        }

        // Handle taught classes
        if ($request->has('taught_classes')) {
            foreach ($request->taught_classes as $classId) {
                // Ensure the homeroom class (if selected) is marked as such, otherwise false
                $isHomeroom = ($request->filled('homeroom_class_id') && $classId == $request->homeroom_class_id);
                $newClassesToSync[$classId] = ['is_homeroom_teacher' => $isHomeroom];
            }
        }

        // Ensure that if a homeroom class was selected, it's included even if not explicitly in taught_classes
        if ($request->filled('homeroom_class_id') && !array_key_exists($request->homeroom_class_id, $newClassesToSync)) {
            $newClassesToSync[$request->homeroom_class_id] = ['is_homeroom_teacher' => true];
        }


        $teacher->classes()->sync($newClassesToSync);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Menghapus guru dari database.
     *
     * @param  \App\Models\User  $teacher (Menggunakan route model binding)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }
}