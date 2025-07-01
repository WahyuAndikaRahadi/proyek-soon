<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

      

    protected $fillable = [
        'user_id', // Pastikan ini ada di sini!
        'class_id',
        'subject_id',
        'date',
        'title',
        'start_time',
        'end_time',
        'description',
        'notes',
    ];

    public function teacher() // Nama relasi tetap 'teacher' menunjuk ke user_id
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}