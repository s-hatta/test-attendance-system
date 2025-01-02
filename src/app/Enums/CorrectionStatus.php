<?php

namespace App\Enums;

enum CorrectionStatus
{
    case PENDING = 0;
    case APPROVED = 1;
    
    public function getMessage(): string
    {
        return match($this) {
            self::PENDING => '申請中',
            self::APPROVED => '申請済',
        };
    }
}
