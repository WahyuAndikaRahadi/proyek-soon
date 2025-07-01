<?php

use App\Http\Controllers\Admin\ManageUserPasswordController;
use App\Http\Controllers\Admin\UserPasswordController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Pastikan Auth diimpor
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;


// Teacher Controllers
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ScheduleController;
use App\Http\Controllers\Teacher\JournalController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\AssessmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute Halaman Utama
// Jika user belum login, akan diarahkan ke halaman login.
// Jika sudah login, akan diarahkan ke dashboard sesuai peran.
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'guru') {
            return redirect()->route('teacher.dashboard');
        }
    }
    return redirect()->route('login');
});


// Grup Rute Autentikasi (Login & Logout)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');



Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordChangeController::class, 'edit'])->name('password.change.form');
    Route::patch('/change-password', [PasswordChangeController::class, 'update'])->name('password.change.update');
});


// Grup Rute untuk Admin
// Dilindungi oleh middleware 'auth' (harus login) dan 'role:admin' (harus memiliki peran 'admin').
// Semua rute di grup ini akan memiliki awalan URL '/admin' dan nama rute 'admin.'.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/change-user-password', [ManageUserPasswordController::class, 'showChangePasswordForm'])->name('change-user-password.form');
    // Rute untuk memproses perubahan password setelah user dipilih dan form diisi
    Route::put('/change-user-password/{user}', [ManageUserPasswordController::class, 'updateUserPassword'])->name('change-user-password.update');

    // --- AKHIR RUTE KHUSUS ---

    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Guru (menggunakan Resource Controller)
    Route::resource('teachers', TeacherController::class);

    // CRUD Kelas (menggunakan Resource Controller)
    Route::resource('classes', ClassController::class);

    // CRUD Mata Pelajaran (menggunakan Resource Controller)
    Route::resource('subjects', SubjectController::class);

    // CRUD Siswa (menggunakan Resource Controller)
    Route::resource('students', StudentController::class);
    Route::resource('schedules', AdminScheduleController::class); // Tambahkan baris ini


    // Rute Laporan (Absensi, Jurnal, Penilaian)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index'); // Halaman utama laporan
        
        // Laporan Absensi
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');

        // Laporan Jurnal
        Route::get('/journal', [ReportController::class, 'journal'])->name('journal');
        Route::get('/journal/export', [ReportController::class, 'exportJournal'])->name('journal.export');

        // Laporan Penilaian
        Route::get('/assessment', [ReportController::class, 'assessment'])->name('assessment');
        Route::get('/assessment/export', [ReportController::class, 'exportAssessment'])->name('assessment.export');
    });
});


// Grup Rute untuk Guru
// Dilindungi oleh middleware 'auth' (harus login) dan 'role:guru' (harus memiliki peran 'guru').
// Semua rute di grup ini akan memiliki awalan URL '/teacher' dan nama rute 'teacher.'.
Route::middleware(['auth', 'role:guru'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard Guru
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Jadwal Guru (hanya melihat, tidak ada CRUD langsung dari guru)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    // Jurnal Harian Guru (menggunakan Resource Controller)
    Route::resource('journals', JournalController::class);

    // Absensi Siswa (disesuaikan dengan kebutuhan mencatat dan melihat riwayat)
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/create', [AttendanceController::class, 'create'])->name('create'); // Form untuk mencatat absensi
        Route::post('/', [AttendanceController::class, 'store'])->name('store');       // Menyimpan absensi
        Route::get('/history', [AttendanceController::class, 'history'])->name('history'); // Melihat riwayat absensi
        // Tambahan: Jika perlu edit/update absensi per individu atau per record, bisa ditambahkan di sini
    });

    // Penilaian Siswa (menggunakan Resource Controller)
    Route::resource('assessments', AssessmentController::class);
});