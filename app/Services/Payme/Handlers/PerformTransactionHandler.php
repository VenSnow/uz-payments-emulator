<?php

namespace App\Services\Payme\Handlers;

use App\Enums\TransactionStatus;
use App\Events\Payme\TransactionUpdated;
use App\Exceptions\PaymeException;
use App\Repositories\Payme\TransactionRepository;
use Illuminate\Support\Facades\Validator;

class PerformTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle transaction performance.
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

        if ($transaction->status === TransactionStatus::SUCCESS) {
            return [
                'transaction'  => $transaction->transaction_id,
                'perform_time' => $transaction->updated_at->valueOf(),
                'state'        => 2,
            ];
        }

        $transaction->status = TransactionStatus::SUCCESS;
        $transaction->response_payload = [
            'transaction'  => $transaction->transaction_id,
            'perform_time' => now()->valueOf(),
            'state'        => 2,
        ];
        $transaction->save();

        event(new TransactionUpdated($transaction, $params, true));

        return $transaction->response_payload;
    }
}
