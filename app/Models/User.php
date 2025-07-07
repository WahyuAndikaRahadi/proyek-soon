<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nip',
        'photo_url', // Tambahkan ini
    ];

    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Mendapatkan mata pelajaran yang diajar oleh guru.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'user_id', 'subject_id');
    }

    /**
     * Mendapatkan kelas yang diampu oleh guru (wali kelas).
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classes::class, 'teacher_classes', 'user_id', 'class_id')
                    ->withPivot('is_homeroom_teacher');
    }

    /**
     * Mendapatkan jadwal yang diajar oleh guru.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'user_id');
    }

    /**
     * Mendapatkan jurnal yang dibuat oleh guru.
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class, 'user_id');
    }

    /**
     * Mendapatkan absensi yang dicatat oleh guru.
     */
    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    /**
     * Mendapatkan penilaian yang diberikan oleh guru.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'user_id');
    }

    /**
     * Mendapatkan catatan sikap yang dibuat oleh guru.
     */
    public function attitudeRecords(): HasMany
    {
        return $this->hasMany(AttitudeRecord::class, 'user_id');
    }
}

