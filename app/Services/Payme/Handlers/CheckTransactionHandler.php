<?php

namespace App\Services\Payme\Handlers;

use App\Enums\TransactionStatus;
use App\Exceptions\PaymeException;
use App\Helpers\PaymeStatusMapper;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class CheckTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle transaction check.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws PaymeException
     */
    public function handle(array $params): array
    {
        $validator = Validator::make($params, [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new PaymeException('Invalid input parameters', -31002);
        }

        $transaction = $this->transactionRepository->findByTransactionId($params['id']);

        if (!$transaction) {
            throw new PaymeException('Transaction not found', -31003);
        }

        return [
            'transaction'   => $transaction->transaction_id,
            'create_time'   => $transaction->created_at->valueOf(),
            'perform_time'  => $transaction->status === TransactionStatus::SUCCESS ? $transaction->updated_at->valueOf() : 0,
            'cancel_time'   => $transaction->status === TransactionStatus::CANCELLED ? $transaction->updated_at->valueOf() : 0,
            'state'         => PaymeStatusMapper::mapToPaymeState($transaction->status),
            'reason'        => null,
        ];
    }
}
