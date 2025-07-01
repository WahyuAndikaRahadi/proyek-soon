<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nis',
        'name',
        'class_id',
        'gender',
        'date_of_birth',
    ];

    /**
     * Mendapatkan kelas tempat siswa ini berada.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Mendapatkan absensi siswa ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Mendapatkan penilaian siswa ini.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    
}


