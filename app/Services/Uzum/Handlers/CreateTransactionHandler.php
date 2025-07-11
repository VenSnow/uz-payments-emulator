<?php

namespace App\Services\Uzum\Handlers;

use App\Enums\PaymentProvider;
use App\Enums\TransactionStatus;
use App\Events\Payme\TransactionUpdated;
use App\Exceptions\UzumException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use Illuminate\Support\Facades\Validator;

class CreateTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle Uzum create request.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws UzumException
     */
    public function handle(array $params): array
    {
        $validator = Validator::make($params, [
            'transId' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'params.account' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new UzumException('Invalid input parameters', '10013');
        }

        $transaction = $this->transactionRepository->create([
            'provider' => PaymentProvider::UZUM,
            'transaction_id' => $params['transId'],
            'order_id' => $params['params']['account'],
            'amount' => $params['amount'] / 100, // Convert tiyiyn to sum
            'status' => TransactionStatus::CREATED,
            'requested_payload' => $params,
            'response_payload' => null,
        ]);

        $transTime = now()->timestamp * 1000;
        $transaction->response_payload = [
            'serviceId' => 101202,
            'transId' => $transaction->transaction_id,
            'status' => 'CREATED',
            'transTime' => $transTime,
            'data' => [
                'account' => ['value' => $params['params']['account']],
                'fio' => ['value' => 'Testov Test Testovich'],
            ],
            'amount' => $params['amount'],
        ];
        $transaction->save();

        event(new TransactionUpdated($transaction, $params, true));

        return $transaction->response_payload;
    }
}
