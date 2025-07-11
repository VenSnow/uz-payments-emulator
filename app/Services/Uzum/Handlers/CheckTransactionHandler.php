<?php

namespace App\Services\Uzum\Handlers;

use App\Enums\PaymentProvider;
use App\Enums\TransactionStatus;
use App\Exceptions\UzumException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class CheckTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle Uzum check request.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws UzumException
     */
    public function handle(array $params): array
    {
        $validator = Validator::make($params, [
            'params.account' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new UzumException('Invalid input parameters', '10007');
        }

        $transaction = $this->transactionRepository->create([
            'provider' => PaymentProvider::UZUM,
            'transaction_id' => uniqid('uzum_'),
            'order_id' => $params['params']['account'],
            'amount' => 0, // No amount for check
            'status' => TransactionStatus::PENDING,
            'requested_payload' => $params,
            'response_payload' => null,
        ]);

        $transTime = now()->timestamp * 1000;
        $transaction->response_payload = [
            'serviceId' => 101202,
            'transId' => $transaction->transaction_id,
            'status' => 'OK',
            'transTime' => $transTime,
            'data' => [
                'account' => ['value' => $params['params']['account']],
                'fio' => ['value' => 'Testov Test Testovich'],
            ],
            'amount' => 0,
        ];
        $transaction->save();

        return $transaction->response_payload;
    }
}
