<?php

namespace App\Enums;

enum AttendanceStatus: int
{
    case OFF_DUTY = 0;
    case WORKING = 1;
    case BREAK = 2;
    case LEFT = 3;

    public function getMessage(): string
    {
        return match($this) {
            self::OFF_DUTY => '勤務外',
            self::WORKING => '出勤中',
            self::BREAK => '休憩中',
            self::LEFT => '退勤済',
        };
    }
}
