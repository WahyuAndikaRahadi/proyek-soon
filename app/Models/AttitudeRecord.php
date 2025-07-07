<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttitudeRecord extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_id',
        'schedule_id',
        'subject_id',
        'class_id',
        'record_date',
        'start_time',
        'end_time',
        'at_time',
        'attitude_notes',
    ];

    /**
     * Mendapatkan guru yang mencatat catatan sikap ini.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendapatkan siswa yang dicatat dalam catatan sikap ini.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Mendapatkan jadwal yang terkait dengan catatan sikap ini.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Mendapatkan mata pelajaran yang terkait dengan catatan sikap ini.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Mendapatkan kelas yang terkait dengan catatan sikap ini.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // app/Models/AttitudeRecord.php
// ...
protected $casts = [
    'record_date' => 'date', // Pastikan baris ini ada
    // ... cast lainnya
];
// ...
}

