<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('attitude_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Guru yang mencatat
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade'); // Siswa yang dicatat
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null'); // Jadwal terkait (opsional)
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null'); // Mata pelajaran terkait (opsional)
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade'); // Kelas siswa saat dicatat
            $table->date('record_date'); // Tanggal catatan sikap
            $table->time('start_time')->nullable(); // Jam mulai pelajaran
            $table->time('end_time')->nullable(); // Jam selesai pelajaran
            $table->integer('at_time')->nullable(); // Jam ke- pelajaran
            $table->text('attitude_notes'); // Catatan sikap siswa
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('attitude_records');
    }
};

