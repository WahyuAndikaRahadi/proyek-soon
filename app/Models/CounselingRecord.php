<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounselingRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Guru BK yang mencatat
        'student_id',
        'record_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'record_date' => 'date',
    ];

    /**
     * Get the BK teacher who recorded the counseling.
     */
    public function bkTeacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the student associated with the counseling record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

