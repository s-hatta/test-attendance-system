<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class BreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = Attendance::all();
        
        foreach( $attendances as $attendance ) {
            $dt = Carbon::parse($attendance->time);
            $dt_start = $dt->copy()->setTime(12,0,0);
            $dt_end = $dt->copy()->setTime(13,0,0);
            
            DB::table('break_times')->insert([
                'attendance_id' => $attendance->id,
                'start_at' => $dt_start,
                'end_at' => $dt_end,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
