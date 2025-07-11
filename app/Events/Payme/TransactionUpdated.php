<?php

namespace App\Events\Payme;

use App\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;

class TransactionUpdated
{
    use Dispatchable;

    /**
     * @param Transaction $transaction
     * @param array<string, mixed> $params
     * @param bool $notify
     */
    public function __construct(
        public readonly Transaction $transaction,
        public readonly array $params,
        public readonly bool $notify
    ) {}
}
