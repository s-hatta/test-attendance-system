<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class ClockTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    /*
        1. 勤怠打刻画面を開く 
        2. 画面に表示されている日時情報を確認する
        
        画面上に表示されている日時が現在の日時と一致すること
    */
    public function test_clock_show(): void
    {
        Carbon::setLocale('ja');
        $now = Carbon::parse('2025-01-23 12:34:56');
        Carbon::setTestNow($now);
        
        $formattedDate = $now->format('y-m-d');
        $formattedTime = $now->format('H:i');
        
        $response = $this->actingAs($this->user)->get('/attendance');
        $response->assertSee( $formattedDate );
        $response->assertSee( $formattedTime );
    }
}
