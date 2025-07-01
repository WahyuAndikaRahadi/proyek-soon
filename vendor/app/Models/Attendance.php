<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    

    protected $fillable = [
        'user_id',
        'class_id',
        'student_id',
        'schedule_id', // <-- Add this
        'date',
        'recorded_by',
        'status',
        'notes',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function schedule() // <-- Add this relationship
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}