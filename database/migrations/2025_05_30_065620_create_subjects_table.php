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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nama mata pelajaran
            $table->text('description')->nullable(); // Deskripsi mata pelajaran
            // --- Tambahan Baru ---
            $table->enum('type', ['umum', 'kejuruan'])->default('umum'); // Tipe mata pelajaran: umum/kejuruan
            $table->integer('kktp')->nullable(); // Nilai KTTP, akan diisi default di seeder/model
            // --- Akhir Tambahan Baru ---
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};