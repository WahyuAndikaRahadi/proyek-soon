<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // Assuming 'users' table contains teachers and other roles
use App\Models\Classes; // Assuming 'classes' is your Class model
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data for Small Boxes
        $totalTeachers = User::where('role', 'guru')->count();
        $totalClasses = Classes::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $totalSchedules = Schedule::count();

        // Data for 'Jumlah Siswa per Kelas' table
        $studentsPerClass = Classes::select('classes.name as class_name', DB::raw('count(students.id) as total_students'))
            ->leftJoin('students', 'classes.id', '=', 'students.class_id')
            ->groupBy('classes.name')
            ->orderBy('classes.name')
            ->get();

        // --- Data for Charts ---

        // 1. Attendance Status Distribution
        // Mengambil data absensi dari tabel 'attendances'
        $attendanceStatus = Attendance::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $attendanceLabels = $attendanceStatus->pluck('status')->toArray();
        $attendanceData = $attendanceStatus->pluck('count')->toArray();
        $attendanceColors = [
            'Hadir' => '#28a745', // Green
            'Sakit' => '#ffc107', // Yellow
            'Izin'  => '#17a2b8', // Blue
            'Alpha' => '#dc3545', // Red
        ];
        $attendanceBackgroundColors = [];
        foreach ($attendanceLabels as $label) {
            $attendanceBackgroundColors[] = $attendanceColors[$label] ?? '#6c757d'; // Default grey if status not defined
        }

        // 2. Students by Gender
        // Mengambil data jumlah siswa berdasarkan gender dari tabel 'students'
        $studentsByGender = Student::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        $genderLabels = $studentsByGender->pluck('gender')->map(function ($gender) {
            return $gender === 'L' ? 'Laki-laki' : 'Perempuan';
        })->toArray();
        $genderData = $studentsByGender->pluck('count')->toArray();
        $genderBackgroundColors = [
            'Laki-laki' => '#007bff', // Blue
            'Perempuan' => '#fd7e14', // Orange
        ];
        $genderChartColors = [];
        foreach ($genderLabels as $label) {
            $genderChartColors[] = $genderBackgroundColors[$label] ?? '#6c757d';
        }

        // 3. Average Assessment Scores by Subject
        // Menghitung rata-rata nilai penilaian per mata pelajaran dari tabel 'assessments'
        $averageScoresBySubject = Assessment::select('subjects.name as subject_name', DB::raw('AVG(assessments.score) as average_score'))
            ->join('subjects', 'assessments.subject_id', '=', 'subjects.id')
            ->groupBy('subjects.name')
            ->orderBy('subjects.name')
            ->get();

        $scoreLabels = $averageScoresBySubject->pluck('subject_name')->toArray();
        $scoreData = $averageScoresBySubject->pluck('average_score')->map(fn($score) => round($score, 2))->toArray();

        // 4. Journals per Teacher
        // Menghitung jumlah jurnal per guru dari tabel 'journals'
        $journalsPerTeacher = Journal::select('users.name as teacher_name', DB::raw('count(journals.id) as total_journals'))
            ->join('users', 'journals.user_id', '=', 'users.id')
            ->groupBy('users.name')
            ->orderBy('total_journals', 'desc')
            ->get();

        $journalTeacherLabels = $journalsPerTeacher->pluck('teacher_name')->toArray();
        $journalTeacherData = $journalsPerTeacher->pluck('total_journals')->toArray();

                // NEW: Data for "Jumlah Guru per Mata Pelajaran"
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


        return view('admin.dashboard', compact(
            'totalTeachers',
            'totalClasses',
            'totalStudents',
            'totalSubjects',
            'totalSchedules',
            'studentsPerClass',
            'attendanceLabels',
            'attendanceData',
            'attendanceBackgroundColors',
            'genderLabels',
            'genderData',
            'genderChartColors',
            'scoreLabels',
            'scoreData',
            'journalTeacherLabels',
            'journalTeacherData',
            'teachersPerSubject' // Pass the new data to the view
        ));
    }
}