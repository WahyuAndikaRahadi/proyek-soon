<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
              Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke students.id
                        $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Guru (dari tabel users)
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            // Foreign Key ke schedules.id (pelajaran yang diabsen)
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->date('date'); // Tanggal absensi
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha']); // Status kehadiran
            $table->text('notes')->nullable(); // Catatan tambahan
            // Foreign Key ke users.id (guru yang mencatat)
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Menambahkan unique constraint untuk mencegah absensi ganda per siswa per jadwal per tanggal
            $table->unique(['student_id', 'schedule_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
