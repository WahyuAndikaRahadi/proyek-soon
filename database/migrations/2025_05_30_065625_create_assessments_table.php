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
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('semester');
            $table->enum('type', [
                'Tugas 1', 'Tugas 2', 'Tugas 3', 'Tugas 4', 'Tugas 5', 'Tugas 6',
                'STS1', 'STS2', 'SAS', 'SAT'
            ]);
            $table->decimal('score', 5, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
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