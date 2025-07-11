<?php

namespace App\Services\Payme\Handlers;

use App\Enums\PaymentProvider;
use App\Enums\TransactionStatus;
use App\Events\Payme\TransactionUpdated;
use App\Repositories\Payme\TransactionRepository;
use App\Exceptions\PaymeException;
use Illuminate\Support\Facades\Validator;

class CreateTransactionHandler implements TransactionHandlerInterface
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

    /**
     * Handle transaction creation.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws PaymeException
     */
    public function handle(array $params): array
    {
        $validator = Validator::make($params, [
            'amount' => 'required|numeric|min:0',
            'account.phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new PaymeException('Invalid input parameters', -31002);
        }

        $transaction = $this->transactionRepository->create([
            'provider'           => PaymentProvider::PAYME,
            'transaction_id'     => uniqid('payme_'),
            'order_id'           => $params['account']['phone'] ?? 'undefined',
            'amount'             => $params['amount'],
            'status'             => TransactionStatus::PENDING,
            'requested_payload'  => $params,
            'response_payload'   => null,
        ]);

        $createTime = now()->valueOf();
        $transaction->response_payload = [
            'transaction'  => $transaction->transaction_id,
            'create_time'  => $createTime,
            'state'        => 1,
        ];
        $transaction->save();

        event(new TransactionUpdated($transaction, $params, true));

        return $transaction->response_payload;
    }
}
