<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_at',
        'clock_out_at',
        'status',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function breakTime()
    {
        return $this->hasMany(BreakTime::class);
    }
    
    public function attendanceCorrection()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }
}
