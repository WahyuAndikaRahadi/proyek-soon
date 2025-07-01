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
              Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke students.id
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            // Foreign Key ke subjects.id
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            // Foreign Key ke users.id (guru yang memberi nilai)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('semester'); // Semester (contoh: 1, 2)
            // Jenis penilaian: Harian 1, Harian 2, ..., Harian 6, UTS, UAS
            $table->enum('type', ['Harian 1', 'Harian 2', 'Harian 3', 'Harian 4', 'Harian 5', 'Harian 6', 'UTS', 'UAS']);
            $table->decimal('score', 5, 2); // Nilai (contoh: 85.50)
            $table->date('date'); // Tanggal penilaian
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Menambahkan unique constraint untuk mencegah penilaian ganda per siswa per mata pelajaran per semester per tipe
            $table->unique(['student_id', 'subject_id', 'semester', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
