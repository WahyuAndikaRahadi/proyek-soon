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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique(); // Nomor Induk Siswa
            $table->string('name'); // Nama siswa
            // Foreign Key ke classes.id (kelas siswa)
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->enum('gender', ['L', 'P']); // Jenis kelamin: L (Laki-laki), P (Perempuan)
            $table->date('date_of_birth')->nullable(); // Tanggal lahir siswa
            $table->timestamps();
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
