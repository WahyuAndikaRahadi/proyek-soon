<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notification_states', function (Blueprint $table) {
            // Hapus batasan default yang lama (jika ada)
            // Ini penting jika Anda mengubah default sebelumnya
            $table->boolean('is_dismissed')->default(false)->change();
        });

        // Opsional: Perbarui semua notifikasi yang ada yang mungkin defaultnya true menjadi false
        // Lakukan ini HANYA JIKA Anda ingin semua notifikasi yang sudah ada menjadi "belum dibaca"
        // jika sebelumnya is_dismissed = true secara tidak sengaja.
        // Jika Anda ingin mempertahankan notifikasi yang sudah ada sebagai dismissed, abaikan baris ini.
        // DB::table('notification_states')->update(['is_dismissed' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_states', function (Blueprint $table) {
            // Kembalikan default ke true jika di rollback
            $table->boolean('is_dismissed')->default(true)->change();
        });
    }
};