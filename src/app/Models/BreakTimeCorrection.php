<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTimeCorrection extends Model
{
    protected $fillable = [
        'attendance_correction_id',
        'start_at',
        'end_at',
    ];
    
    public function attendanceCorrection()
    {
        return $this->belongsTo(AttendanceCorrection::class);
    }
}
