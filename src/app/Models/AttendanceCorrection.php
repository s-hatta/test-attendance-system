<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'attendance_id',
        'clock_in_at',
        'clock_out_at',
        'remark',
        'status',
    ];
    
    public function breakTime()
    {
        return $this->belongsTo(Attendance::class);
    }
}
