<?php

use App\Http\Controllers\Admin\AdminPasswordChangeController;
use App\Http\Controllers\Admin\ManageUserPasswordController;
use App\Http\Controllers\Teacher\TeacherPasswordChangeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\Teacher\NotificationController; // <--- Import NotificationController di namespace Teacher

// Supervisor Controllers
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\SupervisorPasswordChangeController; // Import SupervisorPasswordChangeController
use App\Http\Controllers\Supervisor\SupervisorReportController; // <--- ADD THIS LINE




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




// Grup Rute untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/change-user-password', [ManageUserPasswordController::class, 'showChangePasswordForm'])->name('change-user-password.form');
    Route::put('/change-user-password/{user}', [ManageUserPasswordController::class, 'updateUserPassword'])->name('change-user-password.update');

    Route::get('/change-password', [AdminPasswordChangeController::class, 'edit'])->name('password.change.form');
    Route::patch('/change-password', [AdminPasswordChangeController::class, 'update'])->name('password.change.update');

    // Dashboard Admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Guru (menggunakan Resource Controller)
    Route::resource('teachers', TeacherController::class);

    // CRUD Kelas (menggunakan Resource Controller)
    Route::resource('classes', ClassController::class);

    // CRUD Mata Pelajaran (menggunakan Resource Controller)
    Route::resource('subjects', SubjectController::class);

    // CRUD Siswa (menggunakan Resource Controller)
    Route::get('students/import', [StudentController::class, 'importForm'])->name('students.import.form');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');

    Route::resource('students', StudentController::class);
    Route::resource('schedules', AdminScheduleController::class);


    // Rute Laporan (Absensi, Jurnal, Penilaian)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [ReportController::class, 'exportAttendance'])->name('attendance.export');

        Route::get('/journal', [ReportController::class, 'journal'])->name('journal');
        Route::get('/journal/export', [ReportController::class, 'exportJournal'])->name('journal.export');

        Route::get('/assessment', [ReportController::class, 'assessment'])->name('assessment');
        Route::get('/assessment/export', [ReportController::class, 'exportAssessment'])->name('assessment.export');
    });
});


// Grup Rute untuk Guru
Route::middleware(['auth', 'role:guru'])->prefix('teacher')->name('teacher.')->group(function () {
    // Dashboard Guru
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    Route::get('/change-password', [TeacherPasswordChangeController::class, 'edit'])->name('password.change.form');
    Route::patch('/change-password', [TeacherPasswordChangeController::class, 'update'])->name('password.change.update');

    // Jadwal Guru (hanya melihat, tidak ada CRUD langsung dari guru)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    // Jurnal Harian Guru (menggunakan Resource Controller)
    Route::resource('journals', JournalController::class);
    

    // Absensi Siswa (disesuaikan dengan kebutuhan mencatat dan melihat riwayat)
    Route::prefix('attendances')->name('attendances.')->group(function () {
        Route::get('/create', [AttendanceController::class, 'create'])->name('create');
        Route::post('/', [AttendanceController::class, 'store'])->name('store');
       Route::get('/{attendance}/edit', [AttendanceController::class, 'edit'])->name('edit');
    Route::put('/{attendance}', [AttendanceController::class, 'update'])->name('update');

        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
    });

    // Penilaian Siswa (menggunakan Resource Controller)
    Route::resource('assessments', AssessmentController::class);

    Route::post('assessments/import-excel', [AssessmentController::class, 'importExcel'])
        ->name('assessments.importExcel');

        Route::get('assessments/download-template/{class_id}/{semester}', [AssessmentController::class, 'downloadTemplate'])
        ->name('assessments.downloadTemplate');



    // Rute Notifikasi untuk Guru  (PERUBAHAN DI SINI)
    // Sekarang rute ini otomatis memiliki prefix '/teacher' dan nama rute 'teacher.'
    Route::post('/notifications/dismiss', [NotificationController::class, 'markAsDismissed'])->name('notifications.dismiss');
});

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    // Dashboard Supervisor
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');

    // Ubah Password Supervisor
    Route::get('/change-password', [SupervisorPasswordChangeController::class, 'edit'])->name('password.change.form');
    Route::patch('/change-password', [SupervisorPasswordChangeController::class, 'update'])->name('password.change.update');

    // Rute Laporan (Supervisor hanya bisa melihat)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [SupervisorReportController::class, 'index'])->name('index');
        Route::get('/attendance', [SupervisorReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/export', [SupervisorReportController::class, 'exportAttendance'])->name('attendance.export');

        Route::get('/journal', [SupervisorReportController::class, 'journal'])->name('journal');
        Route::get('/journal/export', [SupervisorReportController::class, 'exportJournal'])->name('journal.export');

        Route::get('/assessment', [SupervisorReportController::class, 'assessment'])->name('assessment');
        Route::get('/assessment/export', [SupervisorReportController::class, 'exportAssessment'])->name('assessment.export');
    });

});
