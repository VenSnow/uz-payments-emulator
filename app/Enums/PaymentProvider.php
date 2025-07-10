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

    public static function toArrayWithLabels(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }

}
