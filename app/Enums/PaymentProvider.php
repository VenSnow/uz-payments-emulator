<?php

namespace App\Enums;

use App\Enums\traits\HelperTrait;
use EnumTools\Traits\HasLabel;

enum PaymentProvider: string
{
    use HasLabel, HelperTrait;

    case PAYME = 'payme';
    case UZUM = 'uzum';
    case CLICK = 'click';
}
