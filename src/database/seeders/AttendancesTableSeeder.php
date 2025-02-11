<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        foreach( $users as $user ) {
            $dt = Carbon::now()->subDay(11);
            
            for( $ix = 10; $ix > 0; $ix-- ) {
                $dt->addDay(1);
                $dt_in = $dt->copy()->setTime(9,0,0);
                $dt_out = $dt->copy()->setTime(18,0,0);
                
                DB::table('attendances')->insert([
                    'user_id' => $user->id,
                    'date' => $dt,
                    'clock_in_at' => $dt_in,
                    'clock_out_at' => $dt_out,
                    'status' => Attendance::STATUS_LEFT,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
