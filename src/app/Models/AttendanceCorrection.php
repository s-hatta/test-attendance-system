<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CorrectionStatus;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'date',
        'clock_in_at',
        'clock_out_at',
        'remark',
        'status',
    ];
    
    protected $casts = [
        'date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'status' => CorrectionStatus::class,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
    
    public function breakTimeCorrections()
    {
        return $this->hasMany(BreakTimeCorrection::class);
    }
    
    public function getStatusMessageAttribute(): string
    {
        return $this->status->getMessage();
    }
}
