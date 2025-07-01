<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

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
        $matematika = Subject::create(['name' => 'Matematika', 'description' => 'Ilmu hitung dan logika']);
        $fisika = Subject::create(['name' => 'Fisika', 'description' => 'Ilmu alam']);
        $kimia = Subject::create(['name' => 'Kimia', 'description' => 'Ilmu materi dan perubahannya']);
        $agama = Subject::create(['name' => 'Pendidikan Agama Islam', 'description' => 'Pendidikan agama Islam']);
        $bahasaIndonesia = Subject::create(['name' => 'Bahasa Indonesia', 'description' => 'Mempelajari tata bahasa dan sastra Indonesia']);

        // Membuat Kelas
        $kelasXIPA1 = Classes::create(['name' => 'X IPA 1', 'grade_level' => 10]);
        $kelasXIPA2 = Classes::create(['name' => 'X IPA 2', 'grade_level' => 10]);
        $kelasXIIPA1 = Classes::create(['name' => 'XI IPA 1', 'grade_level' => 11]);

        // Menghubungkan Guru dengan Mata Pelajaran
        $guru1->subjects()->attach($matematika->id);
        $guru1->subjects()->attach($fisika->id);
        $guru2->subjects()->attach($agama->id);
        $guru2->subjects()->attach($bahasaIndonesia->id);

        // Menghubungkan Guru sebagai Wali Kelas
        $guru1->classes()->attach($kelasXIPA1->id, ['is_homeroom_teacher' => true]);
        $guru2->classes()->attach($kelasXIPA2->id, ['is_homeroom_teacher' => false]);


        // Membuat Siswa untuk Kelas X IPA 1
        for ($i = 1; $i <= 36; $i++) {
            Student::create([
                'nis' => 'XIPA1-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Siswa XIPA1 ' . $i,
                'class_id' => $kelasXIPA1->id,
                'gender' => ($i % 2 == 0) ? 'L' : 'P',
                'date_of_birth' => Carbon::now()->subYears(15)->addDays($i)->toDateString(),
            ]);
        }

        // Membuat Siswa untuk Kelas X IPA 2
        for ($i = 1; $i <= 35; $i++) {
            Student::create([
                'nis' => 'XIPA2-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Siswa XIPA2 ' . $i,
                'class_id' => $kelasXIPA2->id,
                'gender' => ($i % 2 == 0) ? 'L' : 'P',
                'date_of_birth' => Carbon::now()->subYears(15)->addDays($i)->toDateString(),
            ]);
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
            // 'lesson_order' sudah dihapus
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
            // 'lesson_order' sudah dihapus
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
            // 'lesson_order' sudah dihapus
            'academic_year' => $academicYear,
            'semester' => 1,
        ]);
    }
}