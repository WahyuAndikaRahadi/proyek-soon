<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classes extends Model
{
    use HasFactory;

    // Mengubah nama tabel jika nama model tidak sesuai konvensi plural (Class -> Classes)
    protected $table = 'classes';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'grade_level',
    ];

    /**
     * Mendapatkan siswa di kelas ini.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Mendapatkan guru yang mengampu kelas ini (wali kelas).
     */
    public function homeroomTeachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teacher_classes', 'class_id', 'user_id')
                    ->wherePivot('is_homeroom_teacher', true);
    }

    /**
     * Mendapatkan jadwal untuk kelas ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    /**
     * Mendapatkan jurnal yang terkait dengan kelas ini.
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class, 'class_id');
    }
}
