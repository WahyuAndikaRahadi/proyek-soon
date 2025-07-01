<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Pastikan ini ada di sini!
        'class_id',
        'subject_id',
        'student_id',
        'type', // Contoh: 'harian', 'mid_test', 'final_test'
        'score',
        'date',
        'notes',
        'academic_year',
        'semester',
    ];

    /**
     * Relasi ke model User (guru yang melakukan penilaian).
     */
    public function teacher() // Nama relasi tetap 'teacher' meskipun foreign key-nya 'user_id'
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Classes.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Relasi ke model Subject.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relasi ke model Student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}