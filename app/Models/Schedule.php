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
        'at_time', // Tambahkan 'at_time' di sini
        'academic_year',
        'semester',
    ];

    /**
     * Dapatkan guru (Pengguna) yang memiliki jadwal ini.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Dapatkan kelas yang dimiliki jadwal ini.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id'); // Pastikan ini mengarah ke model Classes Anda
    }

    /**
     * Dapatkan mata pelajaran yang dimiliki jadwal ini.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}

