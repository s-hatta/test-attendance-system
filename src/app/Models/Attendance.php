<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\AttendanceStatus;

class Attendance extends Model
{
    const STATUS_OFF_DUTY = 0;
    const STATUS_WORKING = 1;
    const STATUS_BREAK = 2;
    const STATUS_LEFT = 3;
    
    protected $fillable = [
        'user_id',
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
        'status' => AttendanceStatus::class,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendanceCorrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
    
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }
    
    public function getStatusMessageAttribute(): string
    {
        return $this->status->getMessage();
    }
    
    /* DateTimeを指定 */
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }
    
    /* 年月を指定 */
    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month);
    }
    
    /* 年月日を指定 */
    public function scopeByDay($query, $year, $month, $day)
    {
        return $query->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereDay('date', $day);
    }
}
