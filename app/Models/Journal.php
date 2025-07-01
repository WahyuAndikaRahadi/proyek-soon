<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id', // <--- Pastikan ini ada di sini!
        'class_id',
        'subject_id',
        'date',
        'title',
        'start_time',
        'end_time',
        'description',
        'notes',
        'at_time', // <--- Pastikan ini juga ada di sini agar bisa disimpan langsung!
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, foreignKey: 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    
    public function schedule()
    {
        // Relasi ini tetap diperlukan untuk mendapatkan data jadwal lainnya jika diperlukan,
        // meskipun at_time sekarang diduplikasi di tabel jurnal.
        return $this->belongsTo(Schedule::class);
    }
}
