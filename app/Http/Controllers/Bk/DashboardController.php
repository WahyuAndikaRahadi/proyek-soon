<?php

namespace App\Http\Controllers\Bk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Import model User (untuk guru)
use App\Models\Classes; // Import model Classes
use App\Models\Student; // Import model Student
use App\Models\Subject; // Import model Subject
use App\Models\Schedule; // Import model Schedule
use App\Models\Attendance; // Import model Attendance
use App\Models\Assessment; // Import model Assessment
use App\Models\Journal; // Import model Journal
use Illuminate\Support\Facades\DB; // Untuk query agregasi

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard untuk role BK.
     * Mirip dengan SupervisorDashboardController, namun disesuaikan untuk kebutuhan BK.
     * Misalnya, BK mungkin lebih fokus pada absensi, pelanggaran siswa, atau catatan konseling.
     * Untuk saat ini, fungsionalitasnya akan sama dengan supervisor, tetapi bisa diperluas di masa mendatang.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 1. Data untuk Kotak Kecil (Small Boxes) - Statistik Umum
        $totalTeachers = User::where('role', 'guru')->count();
        $totalClasses = Classes::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSchedules = Schedule::count();

        // 2. Data untuk Laporan: Jumlah Siswa per Kelas
        $studentsPerClass = Student::select('class_id', DB::raw('count(*) as total_students'))
                                   ->with('class')
                                   ->groupBy('class_id')
                                   ->get()
                                   ->map(function ($item) {
                                       return [
                                           'class_name' => $item->class->name ?? 'N/A',
                                           'total_students' => $item->total_students,
                                       ];
                                   });

        // 3. Data untuk Chart: Distribusi Gender Siswa (Doughnut Chart)
        $genderCounts = Student::select('gender', DB::raw('count(*) as total'))
                                ->groupBy('gender')
                                ->pluck('total', 'gender')
                                ->toArray();
        $genderLabels = array_keys($genderCounts);
        $genderData = array_values($genderCounts);
        $genderChartColors = [
            'Laki-laki' => '#007bff', // Biru
            'Perempuan' => '#fd7e14', // Oranye
            'L' => '#007bff', // Alternatif untuk Laki-laki
            'P' => '#fd7e14', // Alternatif untuk Perempuan
            // Tambahkan warna lain jika ada gender lain
            'Other' => '#6c757d', // Abu-abu
        ];
        // Pastikan urutan warna sesuai dengan label
        $actualGenderColors = array_map(function($label) use ($genderChartColors) {
            return $genderChartColors[$label] ?? '#6c757d'; // Default ke abu-abu jika tidak ditemukan
        }, $genderLabels);


        // 4. Data untuk Chart: Status Kehadiran Siswa (Pie Chart)
        $attendanceCounts = Attendance::select('status', DB::raw('count(*) as total'))
                                      ->groupBy('status')
                                      ->pluck('total', 'status')
                                      ->toArray();
        $attendanceLabels = array_keys($attendanceCounts);
        $attendanceData = array_values($attendanceCounts);
        $attendanceBackgroundColors = [
            'Hadir' => '#28a745', // Hijau
            'Izin' => '#ffc107',  // Kuning
            'Sakit' => '#17a2b8', // Biru-hijau
            'Alpha' => '#dc3545', // Merah
        ];
        // Pastikan urutan warna sesuai dengan label
        $actualAttendanceColors = array_map(function($label) use ($attendanceBackgroundColors) {
            return $attendanceBackgroundColors[$label] ?? '#6c757d'; // Default ke abu-abu jika tidak ditemukan
        }, $attendanceLabels);


        // 5. Data untuk Chart: Rata-rata Nilai per Mata Pelajaran (Bar Chart)
        $scoreAverages = Assessment::select('subject_id', DB::raw('AVG(score) as average_score'))
                                   ->with('subject')
                                   ->groupBy('subject_id')
                                   ->get()
                                   ->mapWithKeys(function ($item) {
                                       return [($item->subject->name ?? 'N/A') => round($item->average_score, 2)];
                                   });
        $scoreLabels = array_keys($scoreAverages->toArray());
        $scoreData = array_values($scoreAverages->toArray());

        // 6. Data untuk Chart: Jurnal per Guru (Horizontal Bar Chart)
        $journalTeacherCounts = Journal::select('user_id', DB::raw('count(*) as total_journals'))
                                      ->with('teacher')
                                      ->groupBy('user_id')
                                      ->get()
                                      ->mapWithKeys(function ($item) {
                                          return [($item->teacher->name ?? 'N/A') => $item->total_journals];
                                      });
        $journalTeacherLabels = array_keys($journalTeacherCounts->toArray());
        $journalTeacherData = array_values($journalTeacherCounts->toArray());

        // Data baru: "Jumlah Guru per Mata Pelajaran"
        $teachersPerSubject = DB::table('schedules')
            ->join('users', 'schedules.user_id', '=', 'users.id')
            ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
            ->select(
                'subjects.name as subject_name',
                DB::raw('COUNT(DISTINCT users.id) as total_teachers'),
                DB::raw('GROUP_CONCAT(DISTINCT users.name ORDER BY users.name ASC SEPARATOR ", ") as teacher_names')
            )
            ->where('users.role', 'guru') // Pastikan hanya guru
            ->groupBy('subjects.name')
            ->orderBy('subjects.name')
            ->get();


        return view('bk.dashboard', compact(
            'totalTeachers',
            'totalClasses',
            'totalStudents',
            'totalSubjects',
            'totalSchedules',
            'studentsPerClass',
            'genderLabels',
            'genderData',
            'actualGenderColors',
            'attendanceLabels',
            'attendanceData',
            'actualAttendanceColors',
            'scoreLabels',
            'scoreData',
            'journalTeacherLabels',
            'journalTeacherData',
            'teachersPerSubject' // Teruskan data baru ke view
        ));
    }
}
