<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'date',
        'type',
        'is_dismissed',
    ];

    protected $casts = [
        'is_dismissed' => 'boolean',
        'date' => 'date',
    ];

    // Definisi relasi jika diperlukan (opsional, tapi bagus untuk konsistensi)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}