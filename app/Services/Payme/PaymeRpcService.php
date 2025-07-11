<?php

namespace App\Services\Payme;

use App\Enums\PaymentProvider;
use App\Enums\ScenarioType;
use App\Enums\TransactionStatus;
use App\Helpers\PaymeStatusMapper;
use App\Jobs\SendWebhookNotification;
use App\Models\Transaction;
use Exception;

class PaymeRpcService
{
    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function handle(string $method, array $params): array
    {
        return match ($method) {
            'CreateTransaction'  => $this->createTransaction($params),
            'PerformTransaction' => $this->performTransaction($params),
            'CheckTransaction'   => $this->checkTransaction($params),
            'CancelTransaction'  => $this->cancelTransaction($params),
            default => throw new Exception("Method $method not supported"),
        };
    }

    /**
     * @param array $params
     * @return array
     */
    private function createTransaction(array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->respondWithDebugScenario($params, 'CreateTransaction');
        }

        $transaction = Transaction::create([
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

        dispatch(new SendWebhookNotification($params, $transaction, true));

        return $transaction->response_payload;
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    private function performTransaction(array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->respondWithDebugScenario($params, 'PerformTransaction');
        }

        $transaction = Transaction::where('transaction_id', $params['id'])->first();

        if (!$transaction) {
            throw new Exception("Transaction not found");
        }

        if ($transaction->status === TransactionStatus::SUCCESS) {
            return [
                'transaction' => $transaction->transaction_id,
                'perform_time' => $transaction->updated_at->valueOf(),
                'state' => 2,
            ];
        }

        $transaction->status = TransactionStatus::SUCCESS;
        $transaction->response_payload = [
            'transaction' => $transaction->transaction_id,
            'perform_time' => now()->valueOf(),
            'state' => 2,
        ];
        $transaction->save();

        dispatch(new SendWebhookNotification($transaction->requested_payload, $transaction, true));

        return $transaction->response_payload;
    }


    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    private function cancelTransaction(array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->respondWithDebugScenario($params, 'CancelTransaction');
        }

        $transaction = Transaction::where('transaction_id', $params['id'])->first();

        if (!$transaction) {
            throw new Exception("Transaction not found");
        }

        if ($transaction->status === TransactionStatus::CANCELLED) {
            return [
                'transaction'   => $transaction->transaction_id,
                'cancel_time'   => $transaction->updated_at->valueOf(),
                'state'         => -1,
            ];
        }

        if ($transaction->status === TransactionStatus::SUCCESS) {
            throw new Exception("Transaction already performed and cannot be canceled");
        }

        $transaction->status = TransactionStatus::CANCELLED;
        $transaction->response_payload = [
            'transaction' => $transaction->transaction_id,
            'cancel_time' => now()->valueOf(),
            'state'       => -1,
        ];
        $transaction->save();

        dispatch(new SendWebhookNotification($transaction->requested_payload, $transaction, true));

        return $transaction->response_payload;
    }


    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    private function checkTransaction(array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->respondWithDebugScenario($params, 'CheckTransaction');
        }

        $transaction = Transaction::where('transaction_id', $params['id'])->first();

        if (!$transaction) {
            throw new Exception("Transaction not found");
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

    private function respondWithDebugScenario(array $params, string $method): array
    {
        $scenario = $params['debug_scenario'];
        $now = now()->valueOf();
        $fakeTransactionId = uniqid('payme_');

        return match ($scenario) {
            ScenarioType::SUCCESS->value => match ($method) {
                'CreateTransaction' => [
                    'transaction'  => $fakeTransactionId,
                    'create_time'  => $now,
                    'state'        => 1,
                ],
                'PerformTransaction' => [
                    'transaction'  => $fakeTransactionId,
                    'perform_time' => $now,
                    'state'        => 2,
                ],
                'CancelTransaction' => [
                    'transaction'  => $fakeTransactionId,
                    'cancel_time'  => $now,
                    'state'        => -1,
                ],
                'CheckTransaction' => [
                    'transaction'   => $fakeTransactionId,
                    'create_time'   => $now,
                    'perform_time'  => $now,
                    'cancel_time'   => 0,
                    'state'         => 2,
                    'reason'        => null,
                ],
                default => $this->debugError('Unsupported method'),
            },
            ScenarioType::INSUFFICIENT_FUNDS->value => [
                'error' => [
                    'code'    => -31001,
                    'message' => [
                        'en' => 'Insufficient funds',
                        'uz' => 'Hisobda mablag‘ yetarli emas',
                        'ua' => 'Недостатньо коштів',
                        'ru' => 'Недостаточно средств',
                    ],
                    'data'    => 'INSUFFICIENT_FUNDS',
                ],
            ],
            ScenarioType::TIMEOUT->value => [
                'error' => [
                    'code'    => -31008,
                    'message' => [
                        'en' => 'Timeout',
                        'uz' => 'Taymaut',
                        'ua' => 'Таймаут',
                        'ru' => 'Таймаут',
                    ],
                    'data'    => 'TIMEOUT',
                ],
            ],
            ScenarioType::SIGNATURE_ERROR->value => [
                'error' => [
                    'code'    => -31099,
                    'message' => [
                        'en' => 'Signature error',
                        'uz' => 'Imzo xatosi',
                        'ua' => 'Помилка підпису',
                        'ru' => 'Ошибка подписи',
                    ],
                    'data'    => 'SIGNATURE_ERROR',
                ],
            ],
            default => [
                'error' => [
                    'code'    => -31050,
                    'message' => [
                        'en' => 'Unknown scenario',
                        'uz' => 'Nomaʼlum ssenariy',
                        'ua' => 'Невідомий сценарій',
                        'ru' => 'Неизвестный сценарий',
                    ],
                    'data'    => 'UNKNOWN_SCENARIO',
                ],
            ],
        };
    }

    private function debugError(string $code): array
    {
        return [
            'error' => [
                'code'    => -31000,
                'message' => [
                    'en' => 'Scenario not supported',
                    'uz' => 'Ssenariy qo\'llab-quvvatlanmaydi',
                    'ua' => 'Сценарій не підтримується',
                    'ru' => 'Сценарий не поддерживается',
                ],
                'data' => $code,
            ]
        ];
    }

}

