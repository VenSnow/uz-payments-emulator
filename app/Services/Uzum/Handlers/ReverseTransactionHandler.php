<?php

namespace App\Services\Uzum\Handlers;

use App\Enums\TransactionStatus;
use App\Events\Payme\TransactionUpdated;
use App\Exceptions\UzumException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class ReverseTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle Uzum reverse request.
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
            throw new UzumException('Invalid input parameters', '10017');
        }

        $transaction = $this->transactionRepository->findByTransactionId($params['transId']);

        if (!$transaction) {
            throw new UzumException('Transaction not found', '10017');
        }

        if ($transaction->status === TransactionStatus::REVERSED) {
            return [
                'serviceId' => 101202,
                'transId' => $transaction->transaction_id,
                'status' => 'REVERSED',
                'reverseTime' => $transaction->updated_at->timestamp * 1000,
                'data' => [
                    'account' => ['value' => $transaction->order_id],
                    'fio' => ['value' => 'Testov Test Testovich'],
                ],
                'amount' => $transaction->amount * 100,
            ];
        }

        if ($transaction->status === TransactionStatus::CONFIRMED) {
            throw new UzumException('Transaction already performed and cannot be canceled', '10017');
        }

        $transaction->status = TransactionStatus::REVERSED;
        $reverseTime = now()->timestamp * 1000;
        $transaction->response_payload = [
            'serviceId' => 101202,
            'transId' => $transaction->transaction_id,
            'status' => 'REVERSED',
            'reverseTime' => $reverseTime,
            'data' => [
                'account' => ['value' => $transaction->order_id],
                'fio' => ['value' => 'Testov Test Testovich'],
            ],
            'amount' => $transaction->amount * 100,
        ];
        $transaction->save();

        event(new TransactionUpdated($transaction, $params, true));

        return $transaction->response_payload;
    }
}
