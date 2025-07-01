<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah tipe kolom 'role' jika diperlukan, misalnya dari string menjadi enum
            // Pastikan Anda sudah punya kolom 'role' dengan tipe data yang fleksibel
            // Jika role sebelumnya adalah ENUM('admin', 'guru'), ubah menjadi ENUM('admin', 'guru', 'supervisor')
            // Contoh untuk MySQL:
             DB::statement("ALTER TABLE users CHANGE COLUMN role role ENUM('admin', 'guru', 'supervisor') NOT NULL DEFAULT 'guru'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Mengembalikan ke ENUM sebelumnya jika diperlukan
             DB::statement("ALTER TABLE users CHANGE COLUMN role role ENUM('admin', 'guru') NOT NULL DEFAULT 'guru'");
        });
    }
};