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
        Schema::create('notification_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Guru yang mengabaikan
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade'); // Jadwal terkait
            $table->date('date'); // Tanggal notifikasi muncul
            $table->string('type'); // 'journal' atau 'attendance'
            $table->boolean('is_dismissed')->default(false); 
            $table->timestamps();

            // Memastikan kombinasi user, schedule, date, dan type adalah unik
            // Ini mencegah duplikat untuk notifikasi yang sama
            $table->unique(['user_id', 'schedule_id', 'date', 'type'], 'unique_notification_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_states');
    }
};