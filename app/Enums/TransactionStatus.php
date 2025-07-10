<?php

namespace App\Enums;

use App\Enums\traits\HelperTrait;
use EnumTools\Traits\HasLabel;

enum TransactionStatus: string
{
    use HasLabel, HelperTrait;

    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
}
