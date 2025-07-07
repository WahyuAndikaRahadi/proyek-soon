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
use App\Http\Controllers\Teacher\NotificationController;
use App\Http\Controllers\Teacher\TeacherAttitudeRecordController; // <--- Import Controller Catatan Sikap Guru

// Supervisor Controllers
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\SupervisorPasswordChangeController;
use App\Http\Controllers\Supervisor\SupervisorReportController;

// BK Controllers
use App\Http\Controllers\Bk\DashboardController;
use App\Http\Controllers\Bk\BkAttitudeRecordController;
use App\Http\Controllers\Bk\BkCounselingRecordController;
use App\Http\Controllers\Bk\BkPasswordChangeController;

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
        } elseif (Auth::user()->role === 'bk') { // Tambahkan redirect untuk peran BK
            return redirect()->route('bk.dashboard');
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


    // Rute Catatan Sikap Siswa untuk Guru
    Route::prefix('attitude-records')->name('attitude_records.')->group(function () {
        Route::get('/create', [TeacherAttitudeRecordController::class, 'create'])->name('create');
        Route::post('/', [TeacherAttitudeRecordController::class, 'store'])->name('store');
        Route::get('/{attitudeRecord}/edit', [TeacherAttitudeRecordController::class, 'edit'])->name('edit');
        Route::put('/{attitudeRecord}', [TeacherAttitudeRecordController::class, 'update'])->name('update');
        Route::delete('/{attitudeRecord}', [TeacherAttitudeRecordController::class, 'destroy'])->name('destroy'); // <--- BARIS INI DITAMBAHKAN
        Route::get('/history', [TeacherAttitudeRecordController::class, 'history'])->name('history');
    });


    // Rute Notifikasi untuk Guru
    Route::post('/notifications/dismiss', [NotificationController::class, 'markAsDismissed'])->name('notifications.dismiss');
});

// Grup Rute untuk Supervisor
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

// Grup Rute untuk BK (Tanpa Middleware Auth dan Role)
Route::middleware(['auth', 'role:bk'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rute untuk Catatan Sikap (Attitude Records)
    Route::get('/attitude-records', [BkAttitudeRecordController::class, 'index'])->name('attitude_records.index');

    // Rute untuk Catatan Konseling (Counseling Records)
    // Menggunakan 'names' untuk memastikan nama rute menggunakan underscore
    Route::resource('counseling-records', BkCounselingRecordController::class)->names([
        'index' => 'counseling_records.index',
        'create' => 'counseling_records.create',
        'store' => 'counseling_records.store',
        'show' => 'counseling_records.show',
        'edit' => 'counseling_records.edit',
        'update' => 'counseling_records.update',
        'destroy' => 'counseling_records.destroy',
    ]);

    // Rute untuk Ubah Password BK
    Route::get('/change-password', [BkPasswordChangeController::class, 'edit'])->name('password.edit');
    Route::put('/change-password', [BkPasswordChangeController::class, 'update'])->name('password.update');
});