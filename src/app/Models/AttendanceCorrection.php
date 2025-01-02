<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in_at',
        'clock_out_at',
        'remark',
        'status',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function breakTimeCorrections()
    {
        return $this->hasMany(BreakTimeCorrection::class);
    }
}
