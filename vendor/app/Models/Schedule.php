<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'class_id',
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'academic_year',
        'semester',
    ];

    /**
     * Get the teacher (User) that owns the schedule.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the class that the schedule belongs to.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id'); // Pastikan ini mengarah ke model Classes Anda
    }

    /**
     * Get the subject that the schedule belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}