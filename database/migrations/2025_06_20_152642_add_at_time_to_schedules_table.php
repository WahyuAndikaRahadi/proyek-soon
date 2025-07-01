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
        // Pastikan tabel 'schedules' ada sebelum mencoba menambah kolom
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                // Tambahkan kolom 'at_time' setelah 'end_time'
                $table->string('at_time')->nullable()->after('end_time'); // Dapat disesuaikan (misal: "Jam ke-1", "Sesi Pagi")
            });
        }
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        // Pastikan tabel 'schedules' ada sebelum mencoba menghapus kolom
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropColumn('at_time');
            });
        }
    }
};

