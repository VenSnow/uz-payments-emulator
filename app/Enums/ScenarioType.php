<?php

namespace App\Enums;

use App\Enums\traits\HelperTrait;
use EnumTools\Traits\HasLabel;

enum ScenarioType: string
{
    use HasLabel, HelperTrait;

    case SUCCESS = 'success';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case TIMEOUT = 'timeout';
    case SIGNATURE_ERROR = 'signature_error';
}
