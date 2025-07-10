<?php

namespace App\Enums\traits;

trait HelperTrait
{
    /**
     * @return array
     */
    public static function toArrayWithLabels(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
