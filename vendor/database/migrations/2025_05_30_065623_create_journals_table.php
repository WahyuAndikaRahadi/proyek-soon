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
              Schema::create('journals', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke users.id (guru yang membuat jurnal)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Foreign Key ke classes.id (kelas yang diajar)
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            // Foreign Key ke subjects.id (mata pelajaran yang diajar)
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->date('date'); // Tanggal kegiatan
            $table->time('start_time'); // Waktu mulai kegiatan
            $table->time('end_time'); // Waktu selesai kegiatan
            $table->string('title'); // Judul kegiatan (contoh: Presentasi Agama)
            $table->text('description'); // Deskripsi detail kegiatan
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
