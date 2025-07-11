<?php

namespace App\Repositories\Payme;

use App\Models\Transaction;

class TransactionRepository
{
    /**
     * Create a new transaction.
     *
     * @param array<string, mixed> $data
     * @return Transaction
     */
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    /**
     * Find transaction by transaction ID.
     *
     * @param string $transactionId
     * @return Transaction|null
     */
    public function findByTransactionId(string $transactionId): ?Transaction
    {
        return Transaction::where('transaction_id', $transactionId)->first();
    }
}
