<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Menggunakan model User karena guru adalah User dengan role 'guru'
use App\Models\Subject; // Diperlukan untuk menghubungkan mata pelajaran
use App\Models\Classes; // Diperlukan untuk menghubungkan kelas
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
        // Ambil semua user dengan role 'guru', preload relasi subjects dan classes
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
        $subjects = Subject::all(); // Ambil semua mata pelajaran
        $classes = Classes::all();   // Ambil semua kelas
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
            'email' => 'required|string|email|max:255|unique:users', // Email harus unik
            'password' => 'required|string|min:8|confirmed', // Password minimal 8 karakter dan harus dikonfirmasi
            'nip' => 'nullable|string|max:255|unique:users', // NIP bisa kosong, tapi harus unik jika diisi
            'subjects' => 'nullable|array', // Mata pelajaran yang diampu (opsional)
            'subjects.*' => 'exists:subjects,id', // Setiap ID mata pelajaran harus ada di tabel subjects
            'classes' => 'nullable|array', // Kelas yang diampu (opsional)
            'classes.*.id' => 'exists:classes,id', // Setiap ID kelas harus ada di tabel classes
            'classes.*.is_homeroom_teacher' => 'boolean', // Flag apakah guru adalah wali kelas
        ]);

        // Buat user baru dengan role 'guru'
        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password sebelum disimpan
            'role' => 'guru',
            'nip' => $request->nip,
        ]);

        // Menghubungkan mata pelajaran yang diampu oleh guru (pivot table teacher_subjects)
        if ($request->has('subjects')) {
            $teacher->subjects()->attach($request->subjects);
        }

        // Menghubungkan kelas yang diampu oleh guru (pivot table teacher_classes)
        if ($request->has('classes')) {
            $classDataToAttach = [];
            foreach ($request->classes as $classId => $classData) {
                // Pastikan classData memiliki id (dari name="classes[ID][id]")
                // Dan is_homeroom_teacher (dari name="classes[ID][is_homeroom_teacher]")
                $classDataToAttach[$classData['id']] = ['is_homeroom_teacher' => $classData['is_homeroom_teacher'] ?? false];
            }
            $teacher->classes()->attach($classDataToAttach);
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
        // Pastikan pengguna yang sedang diedit memang memiliki peran 'guru'
        if ($teacher->role !== 'guru') {
            abort(404); // Atau redirect ke halaman lain
        }

        $subjects = Subject::all();
        $classes = Classes::all();

        // Ambil ID mata pelajaran yang sudah diampu guru ini
        $teacherSubjects = $teacher->subjects->pluck('id')->toArray();

        // Ambil ID kelas yang sudah diampu guru ini beserta status wali kelasnya
        $teacherClasses = $teacher->classes->mapWithKeys(function ($item) {
            return [$item->id => ['is_homeroom_teacher' => $item->pivot->is_homeroom_teacher]];
        })->toArray();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes', 'teacherSubjects', 'teacherClasses'));
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
        // Pastikan pengguna yang sedang diedit memang memiliki peran 'guru'
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
                // Pastikan email unik, kecuali untuk guru yang sedang diedit
                Rule::unique('users')->ignore($teacher->id),
            ],
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat update
            'nip' => [
                'nullable',
                'string',
                'max:255',
                // Pastikan NIP unik, kecuali untuk guru yang sedang diedit
                Rule::unique('users')->ignore($teacher->id),
            ],
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'classes' => 'nullable|array',
            'classes.*.id' => 'exists:classes,id',
            'classes.*.is_homeroom_teacher' => 'boolean',
        ]);

        // Update data dasar guru
        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            // Perbarui password hanya jika input password tidak kosong
            'password' => $request->password ? Hash::make($request->password) : $teacher->password,
        ]);

        // Sinkronisasi mata pelajaran yang diampu.
        // `sync` akan menambahkan, menghapus, atau mempertahankan relasi sesuai array yang diberikan.
        $teacher->subjects()->sync($request->subjects ?? []);

        // Sinkronisasi kelas yang diampu.
        $newClasses = [];
        if ($request->has('classes')) {
            foreach ($request->classes as $classId => $classData) {
                $newClasses[$classData['id']] = ['is_homeroom_teacher' => $classData['is_homeroom_teacher'] ?? false];
            }
        }
        $teacher->classes()->sync($newClasses);


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
        // Pastikan pengguna yang sedang dihapus memang memiliki peran 'guru'
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        // Hapus guru. Karena ada `onDelete('cascade')` di migrasi pivot,
        // relasi di `teacher_subjects` dan `teacher_classes` juga akan terhapus.
        // Jika ada data lain yang bergantung pada user_id (misal: schedules, journals, attendances, assessments),
        // pastikan migrasi juga menangani onDelete('cascade') untuk relasi tersebut.
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }
}