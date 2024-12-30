<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $fillable = [
        'attendance_id',
        'start_at',
        'end_at',
    ];
    
    public function breakTime()
    {
        return $this->belongsTo(Attendance::class);
    }
}
