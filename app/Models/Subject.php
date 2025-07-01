<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type', // <-- Tambahkan
        'kktp', // <-- Tambahkan
    ];

    /**
     * Mendapatkan guru yang mengajar mata pelajaran ini.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'user_id');
    }

    /**
     * Mendapatkan jadwal yang menggunakan mata pelajaran ini.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Mendapatkan jurnal yang terkait dengan mata pelajaran ini.
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Mendapatkan penilaian yang terkait dengan mata pelajaran ini.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}