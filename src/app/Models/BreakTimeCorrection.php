<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTimeCorrection extends Model
{
    protected $fillable = [
        'break_time_id',
        'start_at',
        'end_at',
    ];
    
    public function breakTime()
    {
        return $this->belongsTo(BreakTime::class);
    }
}
