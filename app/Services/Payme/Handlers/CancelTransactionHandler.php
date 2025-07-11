<?php

namespace App\Services\Payme\Handlers;

use App\Enums\TransactionStatus;
use App\Events\Payme\TransactionUpdated;
use App\Exceptions\PaymeException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class CancelTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle transaction cancellation.
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

        if ($transaction->status === TransactionStatus::CANCELLED) {
            return [
                'transaction'  => $transaction->transaction_id,
                'cancel_time'  => $transaction->updated_at->valueOf(),
                'state'        => -1,
            ];
        }

        if ($transaction->status === TransactionStatus::SUCCESS) {
            throw new PaymeException('Transaction already performed and cannot be canceled', -31007);
        }

        $transaction->status = TransactionStatus::CANCELLED;
        $transaction->response_payload = [
            'transaction'  => $transaction->transaction_id,
            'cancel_time'  => now()->valueOf(),
            'state'        => -1,
        ];
        $transaction->save();

        event(new TransactionUpdated($transaction, $params, true));

        return $transaction->response_payload;
    }
}
