<?php

namespace App\Services\Uzum\Handlers;

use App\Enums\TransactionStatus;
use App\Exceptions\UzumException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class StatusTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle Uzum status request.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws UzumException
     */
    public function handle(array $params): array
    {
        $validator = Validator::make($params, [
            'transId' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new UzumException('Invalid input parameters', '10014');
        }

        $transaction = $this->transactionRepository->findByTransactionId($params['transId']);

        if (!$transaction) {
            throw new UzumException('Transaction not found', '10014');
        }

        $transTime = $transaction->created_at->timestamp * 1000;
        $confirmTime = $transaction->status === TransactionStatus::CONFIRMED ? $transaction->updated_at->timestamp * 1000 : null;
        $reverseTime = $transaction->status === TransactionStatus::REVERSED ? $transaction->updated_at->timestamp * 1000 : null;

        return [
            'serviceId' => 101202,
            'transId' => $transaction->transaction_id,
            'status' => $transaction->status->value,
            'transTime' => $transTime,
            'confirmTime' => $confirmTime,
            'reverseTime' => $reverseTime,
            'data' => [
                'account' => ['value' => $transaction->order_id],
                'fio' => ['value' => 'Testov Test Testovich'],
            ],
            'amount' => $transaction->amount * 100,
        ];
    }
}
