<?php

namespace App\Enums;

enum CorrectionStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    
    public function getMessage(): string
    {
        return match($this) {
            self::PENDING => '承認待ち',
            self::APPROVED => '承認済み',
        };
    }
}
