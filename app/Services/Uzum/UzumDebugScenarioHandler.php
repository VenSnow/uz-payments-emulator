<?php

namespace App\Services\Uzum;


use App\Enums\ScenarioType;
use App\Services\Uzum\Handlers\CreateTransactionHandler;

class UzumDebugScenarioHandler
{
    public function __construct(
        protected CreateTransactionHandler $createTransactionHandler,
    ) {}

    /**
     * Handle debug scenario for Uzum requests.
     *
     * @param array<string, mixed> $params
     * @param string $method
     * @return array<string, mixed>
     */
    public function handle(array $params, string $method): array
    {
        $scenario = $params['debug_scenario'] ?? null;
        $now = now()->valueOf();
        $fakeTransId = $params['transId'] ?? uniqid('uzum_');

        return match ($scenario) {
            ScenarioType::SUCCESS->value => $this->handleSuccessScenario($method, $fakeTransId, $now, $params),
            ScenarioType::INSUFFICIENT_FUNDS->value => $this->errorResponse('FAILED', '10013', $now, $params),
            ScenarioType::TIMEOUT->value => $this->errorResponse('FAILED', '10000', $now, $params),
            ScenarioType::SIGNATURE_ERROR->value => $this->errorResponse('FAILED', '10000', $now, $params),
            default => $this->errorResponse('FAILED', '10000', $now, $params),
        };
    }

    /**
     * Handle success scenario for debugging.
     *
     * @param string $method
     * @param string $transId
     * @param int $time
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function handleSuccessScenario(string $method, string $transId, int $time, array $params): array
    {
        if ($method === 'create') {
            $response = $this->createTransactionHandler->handle($params);
            $response['transId'] = $transId;
            return $response;
        }

        $transTime = $time * 1000;
        return match ($method) {
            'check' => [
                'serviceId' => 101202,
                'transId' => $transId,
                'status' => 'OK',
                'transTime' => $transTime,
                'data' => [
                    'account' => ['value' => $params['account'] ?? ''],
                    'fio' => ['value' => 'Testov Test Testovich'],
                ],
                'amount' => $params['amount'] ?? 0,
            ],
            'confirm' => [
                'serviceId' => 101202,
                'transId' => $transId,
                'status' => 'CONFIRMED',
                'confirmTime' => $transTime,
                'data' => [
                    'account' => ['value' => $params['account'] ?? ''],
                    'fio' => ['value' => 'Testov Test Testovich'],
                ],
                'amount' => $params['amount'] ?? 0,
            ],
            'reverse' => [
                'serviceId' => 101202,
                'transId' => $transId,
                'status' => 'REVERSED',
                'reverseTime' => $transTime,
                'data' => [
                    'account' => ['value' => $params['account'] ?? ''],
                    'fio' => ['value' => 'Testov Test Testovich'],
                ],
                'amount' => $params['amount'] ?? 0,
            ],
            'status' => [
                'serviceId' => 101202,
                'transId' => $transId,
                'status' => 'CONFIRMED',
                'transTime' => $transTime,
                'confirmTime' => $transTime,
                'reverseTime' => null,
                'data' => [
                    'account' => ['value' => $params['account'] ?? ''],
                    'fio' => ['value' => 'Testov Test Testovich'],
                ],
                'amount' => $params['amount'] ?? 0,
            ],
            default => $this->errorResponse('FAILED', '10000', $transTime, $params),
        };
    }

    /**
     * Generate error response in Uzum format.
     *
     * @param string $status
     * @param string $errorCode
     * @param int $time
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function errorResponse(string $status, string $errorCode, int $time, array $params): array
    {
        $transTime = $time * 1000;
        return [
            'serviceId' => 101202,
            'transId' => $params['transId'] ?? uniqid('uzum_'),
            'status' => $status,
            'transTime' => $transTime,
            'errorCode' => $errorCode,
        ];
    }
}
