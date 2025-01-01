<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }
    
    public function attendanceCorrection()
    {
        return $this->hasOne(AttendanceCorrection::class);
    }
    
    public function getStatusMessage()
    {
        switch( $this->status ) {
            case self::STATUS_OFF_DUTY  :return '勤務外';
            case self::STATUS_WORKING   :return '出勤中';
            case self::STATUS_BREAK     :return '休憩中';
            case self::STATUS_LEFT      :return '退勤済';
            default                     :return '';
        }
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
