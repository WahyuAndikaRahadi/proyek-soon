<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use App\Models\Subject;
use App\Models\Classes;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seed database.
     */
    public function run(): void
    {
        // Membuat Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'remember_token' => Str::random(10),
        ]);

        // Membuat Guru
        $guru1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@guru.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'nip' => 'G1234567890',
            'remember_token' => Str::random(10),
        ]);

        $guru2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@guru.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'nip' => 'G0987654321',
            'remember_token' => Str::random(10),
        ]);

        // Membuat Mata Pelajaran
        // Mengatur tipe dan KTTP sesuai permintaan
        $matematika = Subject::create([
            'name' => 'Matematika',
            'description' => 'Ilmu hitung dan logika',
            'type' => 'umum',
            'kktp' => 75
        ]);
        $fisika = Subject::create([
            'name' => 'Fisika',
            'description' => 'Ilmu alam',
            'type' => 'umum',
            'kktp' => 75
        ]);
        $kimia = Subject::create([
            'name' => 'Kimia',
            'description' => 'Ilmu materi dan perubahannya',
            'type' => 'umum',
            'kktp' => 75
        ]);
        $agama = Subject::create([
            'name' => 'Pendidikan Agama Islam',
            'description' => 'Pendidikan agama Islam',
            'type' => 'umum',
            'kktp' => 75
        ]);
        $bahasaIndonesia = Subject::create([
            'name' => 'Bahasa Indonesia',
            'description' => 'Mempelajari tata bahasa dan sastra Indonesia',
            'type' => 'umum',
            'kktp' => 75
        ]);

        // Contoh mata pelajaran kejuruan
        $pemrogramanWeb = Subject::create([
            'name' => 'Pemrograman Web',
            'description' => 'Mempelajari pengembangan aplikasi web',
            'type' => 'kejuruan',
            'kktp' => 78
        ]);
        $basisData = Subject::create([
            'name' => 'Basis Data',
            'description' => 'Mempelajari pengelolaan basis data',
            'type' => 'kejuruan',
            'kktp' => 78
        ]);

        // Membuat Kelas
        $kelasXIPA1 = Classes::create(['name' => 'X IPA 1', 'grade_level' => 10]);
        $kelasXIPA2 = Classes::create(['name' => 'X IPA 2', 'grade_level' => 10]);
        $kelasXIIPA1 = Classes::create(['name' => 'XI IPA 1', 'grade_level' => 11]);

        // Menghubungkan Guru dengan Mata Pelajaran
        $guru1->subjects()->attach($matematika->id);
        $guru1->subjects()->attach($fisika->id);
        $guru1->subjects()->attach($pemrogramanWeb->id); // Guru1 juga mengajar kejuruan
        $guru2->subjects()->attach($agama->id);
        $guru2->subjects()->attach($bahasaIndonesia->id);
        $guru2->subjects()->attach($basisData->id); // Guru2 juga mengajar kejuruan

        // Menghubungkan Guru sebagai Wali Kelas
        $guru1->classes()->attach($kelasXIPA1->id, ['is_homeroom_teacher' => true]);
        $guru2->classes()->attach($kelasXIPA2->id, ['is_homeroom_teacher' => false]);

        // Membuat Siswa untuk Kelas X IPA 1
        $studentsXIPA1 = collect(); // Gunakan collect untuk menyimpan siswa
        for ($i = 1; $i <= 36; $i++) {
            $studentsXIPA1->push(Student::create([
                'nis' => 'XIPA1-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Siswa XIPA1 ' . $i,
                'class_id' => $kelasXIPA1->id,
                'gender' => ($i % 2 == 0) ? 'L' : 'P',
                'date_of_birth' => Carbon::now()->subYears(15)->addDays($i)->toDateString(),
            ]));
        }

        // Membuat Siswa untuk Kelas X IPA 2
        $studentsXIPA2 = collect(); // Gunakan collect untuk menyimpan siswa
        for ($i = 1; $i <= 35; $i++) {
            $studentsXIPA2->push(Student::create([
                'nis' => 'XIPA2-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Siswa XIPA2 ' . $i,
                'class_id' => $kelasXIPA2->id,
                'gender' => ($i % 2 == 0) ? 'L' : 'P',
                'date_of_birth' => Carbon::now()->subYears(15)->addDays($i)->toDateString(),
            ]));
        }

        // Ambil tahun akademik saat ini (contoh)
        $currentYear = Carbon::now()->year;
        $academicYear = $currentYear . '/' . ($currentYear + 1); // Contoh: "2025/2026"

        // Jadwal Guru Budi (Matematika di X IPA 1)
        Schedule::create([
            'class_id' => $kelasXIPA1->id,
            'subject_id' => $matematika->id,
            'user_id' => $guru1->id,
            'day_of_week' => 'Senin',
            'start_time' => '06:45:00',
            'end_time' => '07:30:00',
            'academic_year' => $academicYear,
            'semester' => 1,
        ]);
        Schedule::create([
            'class_id' => $kelasXIPA1->id,
            'subject_id' => $matematika->id,
            'user_id' => $guru1->id,
            'day_of_week' => 'Senin',
            'start_time' => '07:30:00',
            'end_time' => '08:15:00',
            'academic_year' => $academicYear,
            'semester' => 1,
        ]);

        // Jadwal Guru Siti (Bahasa Indonesia di X IPA 2)
        Schedule::create([
            'class_id' => $kelasXIPA2->id,
            'subject_id' => $bahasaIndonesia->id,
            'user_id' => $guru2->id,
            'day_of_week' => 'Selasa',
            'start_time' => '08:15:00',
            'end_time' => '09:00:00',
            'academic_year' => $academicYear,
            'semester' => 1,
        ]);

        // --- SEEDING DATA PENILAIAN (ASSESSMENTS) ---
        // Penilaian untuk Siswa di Kelas X IPA 1 (oleh Guru Budi - Matematika)
        $studentsXIPA1->each(function ($student) use ($matematika, $guru1, $kelasXIPA1) {
            // Tugas Harian (Semester 1)
            for ($i = 1; $i <= 3; $i++) { // Contoh 3 tugas
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA1->id,
                    'subject_id' => $matematika->id,
                    'user_id' => $guru1->id,
                    'semester' => 1,
                    'type' => 'Tugas ' . $i,
                    'score' => rand(70, 100),
                    'date' => Carbon::now()->subDays(rand(10, 60))->toDateString(),
                    'notes' => 'Catatan Tugas ' . $i . ' siswa ' . $student->name,
                ]);
            }

            // STS (Semester 1)
            Assessment::create([
                'student_id' => $student->id,
                'class_id' => $kelasXIPA1->id,
                'subject_id' => $matematika->id,
                'user_id' => $guru1->id,
                'semester' => 1,
                'type' => 'STS1',
                'score' => rand(65, 95),
                'date' => Carbon::now()->subDays(rand(5, 30))->toDateString(),
                'notes' => 'Catatan STS semester 1 siswa ' . $student->name,
            ]);

            // SAS (Semester 1)
            Assessment::create([
                'student_id' => $student->id,
                'class_id' => $kelasXIPA1->id,
                'subject_id' => $matematika->id,
                'user_id' => $guru1->id,
                'semester' => 1,
                'type' => 'SAS',
                'score' => rand(60, 90),
                'date' => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                'notes' => 'Catatan SAS semester 1 siswa ' . $student->name,
            ]);

            // Penilaian Semester 2 (Contoh: untuk demonstrasi)
            if ($student->id % 2 === 0) { // Hanya untuk beberapa siswa
                // Tugas Harian (Semester 2)
                for ($i = 4; $i <= 6; $i++) { // Contoh tugas 4-6 untuk semester 2
                    Assessment::create([
                        'student_id' => $student->id,
                        'class_id' => $kelasXIPA1->id,
                        'subject_id' => $matematika->id,
                        'user_id' => $guru1->id,
                        'semester' => 2,
                        'type' => 'Tugas ' . $i,
                        'score' => rand(70, 100),
                        'date' => Carbon::now()->subDays(rand(10, 60))->toDateString(),
                        'notes' => 'Catatan Tugas ' . $i . ' siswa ' . $student->name . ' (Semester 2)',
                    ]);
                }

                // STS (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA1->id,
                    'subject_id' => $matematika->id,
                    'user_id' => $guru1->id,
                    'semester' => 2,
                    'type' => 'STS2',
                    'score' => rand(65, 95),
                    'date' => Carbon::now()->subDays(rand(5, 30))->toDateString(),
                    'notes' => 'Catatan STS semester 2 siswa ' . $student->name,
                ]);

                // SAS (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA1->id,
                    'subject_id' => $matematika->id,
                    'user_id' => $guru1->id,
                    'semester' => 2,
                    'type' => 'SAS',
                    'score' => rand(60, 90),
                    'date' => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                    'notes' => 'Catatan SAS semester 2 siswa ' . $student->name,
                ]);

                // SAT (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA1->id,
                    'subject_id' => $matematika->id,
                    'user_id' => $guru1->id,
                    'semester' => 2,
                    'type' => 'SAT',
                    'score' => rand(60, 90),
                    'date' => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                    'notes' => 'Catatan SAT semester 2 siswa ' . $student->name,
                ]);
            }
        });

        // Penilaian untuk Siswa di Kelas X IPA 2 (oleh Guru Siti - Bahasa Indonesia)
        $studentsXIPA2->each(function ($student) use ($bahasaIndonesia, $guru2, $kelasXIPA2) {
            // Tugas Harian (Semester 1)
            for ($i = 1; $i <= 2; $i++) { // Contoh hanya 2 tugas
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA2->id,
                    'subject_id' => $bahasaIndonesia->id,
                    'user_id' => $guru2->id,
                    'semester' => 1,
                    'type' => 'Tugas ' . $i,
                    'score' => rand(75, 100),
                    'date' => Carbon::now()->subDays(rand(15, 45))->toDateString(),
                    'notes' => 'Catatan Tugas ' . $i . ' siswa ' . $student->name,
                ]);
            }

            // STS (Semester 1)
            Assessment::create([
                'student_id' => $student->id,
                'class_id' => $kelasXIPA2->id,
                'subject_id' => $bahasaIndonesia->id,
                'user_id' => $guru2->id,
                'semester' => 1,
                'type' => 'STS1',
                'score' => rand(70, 98),
                'date' => Carbon::now()->subDays(rand(8, 25))->toDateString(),
                'notes' => 'Catatan STS semester 1 siswa ' . $student->name,
            ]);

            // SAS (Semester 1)
            Assessment::create([
                'student_id' => $student->id,
                'class_id' => $kelasXIPA2->id,
                'subject_id' => $bahasaIndonesia->id,
                'user_id' => $guru2->id,
                'semester' => 1,
                'type' => 'SAS',
                'score' => rand(68, 92),
                'date' => Carbon::now()->subDays(rand(2, 7))->toDateString(),
                'notes' => 'Catatan SAS semester 1 siswa ' . $student->name,
            ]);

            // Penilaian Semester 2 untuk beberapa siswa (Contoh)
            if ($student->id % 3 === 0) { // Hanya untuk beberapa siswa
                // STS (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA2->id,
                    'subject_id' => $bahasaIndonesia->id,
                    'user_id' => $guru2->id,
                    'semester' => 2,
                    'type' => 'STS2',
                    'score' => rand(70, 98),
                    'date' => Carbon::now()->subDays(rand(8, 25))->toDateString(),
                    'notes' => 'Catatan STS semester 2 siswa ' . $student->name,
                ]);

                // SAS (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA2->id,
                    'subject_id' => $bahasaIndonesia->id,
                    'user_id' => $guru2->id,
                    'semester' => 2,
                    'type' => 'SAS',
                    'score' => rand(68, 92),
                    'date' => Carbon::now()->subDays(rand(2, 7))->toDateString(),
                    'notes' => 'Catatan SAS semester 2 siswa ' . $student->name,
                ]);

                // SAT (Semester 2)
                Assessment::create([
                    'student_id' => $student->id,
                    'class_id' => $kelasXIPA2->id,
                    'subject_id' => $bahasaIndonesia->id,
                    'user_id' => $guru2->id,
                    'semester' => 2,
                    'type' => 'SAT',
                    'score' => rand(68, 92),
                    'date' => Carbon::now()->subDays(rand(2, 7))->toDateString(),
                    'notes' => 'Catatan SAT semester 2 siswa ' . $student->name,
                ]);
            }
        });
    }
}