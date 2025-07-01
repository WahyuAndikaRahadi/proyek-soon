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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID Guru (dari tabel users)
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade'); // ID Kelas (dari tabel classes)
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade'); // ID Mata Pelajaran (dari tabel subjects)
            $table->enum('day_of_week', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']); // Hari dalam seminggu
            $table->time('start_time'); // Jam mulai
            $table->time('end_time');   // Jam berakhir
            $table->string('academic_year')->nullable(); // Contoh: "2024/2025"
            $table->integer('semester')->nullable(); // Contoh: 1 atau 2
            $table->timestamps();

            // Opsional: Menambahkan indeks unik untuk mencegah jadwal ganda (guru, kelas, mapel, hari, jam yang sama)
            $table->unique(['user_id', 'class_id', 'day_of_week', 'start_time', 'end_time'], 'unique_schedule_slot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};