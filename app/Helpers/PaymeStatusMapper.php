<?php

namespace App\Helpers;

use App\Enums\TransactionStatus;

class PaymeStatusMapper
{
    /**
     * @param TransactionStatus $status
     * @return int
     */
    public static function mapToPaymeState(TransactionStatus $status): int
    {
        return match ($status) {
            TransactionStatus::PENDING => 1,
            TransactionStatus::SUCCESS => 2,
            TransactionStatus::CANCELLED => -1,
            default => 0, // fallback
        };
    }
}
