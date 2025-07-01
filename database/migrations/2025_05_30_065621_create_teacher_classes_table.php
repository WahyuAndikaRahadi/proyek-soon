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
        Schema::create('teacher_classes', function (Blueprint $table) {
            // Foreign Key ke users.id (guru)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Foreign Key ke classes.id
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            // Menandakan apakah guru tersebut wali kelas untuk kelas ini
            $table->boolean('is_homeroom_teacher')->default(false);
            // Menjadikan kombinasi user_id dan class_id sebagai Primary Key
            $table->primary(['user_id', 'class_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_classes');
    }
};
