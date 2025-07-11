<?php

namespace App\Services\Payme;

use App\Enums\ScenarioType;
use App\Services\Payme\Handlers\CreateTransactionHandler;

class PaymeDebugScenarioHandler
{
    /**
     * @param CreateTransactionHandler $createTransactionHandler
     */
    public function __construct(
        private CreateTransactionHandler $createTransactionHandler
    ) {}

    /**
     * Handle debug scenario for Payme requests.
     *
     * @param array<string, mixed> $params
     * @param string $method
     * @return array<string, mixed>
     */
    public function handle(array $params, string $method): array
    {
        $scenario = $params['debug_scenario'] ?? null;
        $now = now()->valueOf();
        $fakeTransactionId = uniqid('payme_');

        return match ($scenario) {
            ScenarioType::SUCCESS->value => $this->handleSuccessScenario($method, $fakeTransactionId, $now, $params),
            ScenarioType::INSUFFICIENT_FUNDS->value => $this->errorResponse(-31001, 'Insufficient funds', 'INSUFFICIENT_FUNDS'),
            ScenarioType::TIMEOUT->value => $this->errorResponse(-31008, 'Timeout', 'TIMEOUT'),
            ScenarioType::SIGNATURE_ERROR->value => $this->errorResponse(-31099, 'Signature error', 'SIGNATURE_ERROR'),
            default => $this->errorResponse(-31050, 'Unknown scenario', 'UNKNOWN_SCENARIO'),
        };
    }

    /**
     * Handle success scenario for debugging.
     *
     * @param string $method
     * @param string $transactionId
     * @param int $time
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function handleSuccessScenario(string $method, string $transactionId, int $time, array $params): array
    {
        if ($method === 'CreateTransaction') {
            $response = $this->createTransactionHandler->handle($params);
            $response['transaction'] = $transactionId;
            return $response;
        }

        return match ($method) {
            'PerformTransaction' => [
                'transaction'  => $transactionId,
                'perform_time' => $time,
                'state'        => 2,
            ],
            'CancelTransaction' => [
                'transaction'  => $transactionId,
                'cancel_time'  => $time,
                'state'        => -1,
            ],
            'CheckTransaction' => [
                'transaction'   => $transactionId,
                'create_time'   => $time,
                'perform_time'  => $time,
                'cancel_time'   => 0,
                'state'         => 2,
                'reason'        => null,
            ],
            default => $this->errorResponse(-31000, 'Scenario not supported', 'UNSUPPORTED_METHOD'),
        };
    }

    /**
     * Generate error response with localized messages.
     *
     * @param int $code
     * @param string $message
     * @param string $data
     * @return array<string, mixed>
     */
    private function errorResponse(int $code, string $message, string $data): array
    {
        return [
            'error' => [
                'code'    => $code,
                'message' => [
                    'en' => $message,
                    'uz' => $this->translateToUz($message),
                    'ua' => $this->translateToUa($message),
                    'ru' => $this->translateToRu($message),
                ],
                'data'    => $data,
            ],
        ];
    }

    /**
     * @param string $message
     * @return string
     */
    private function translateToUz(string $message): string
    {
        return match ($message) {
            'Insufficient funds' => 'Hisobda mablag‘ yetarli emas',
            'Timeout' => 'Taymaut',
            'Signature error' => 'Imzo xatosi',
            'Unknown scenario' => 'Nomaʼlum ssenariy',
            'Scenario not supported' => 'Stsenariy qo\'llab-quvvatlanmaydi',
            default => $message,
        };
    }

    /**
     * @param string $message
     * @return string
     */
    private function translateToUa(string $message): string
    {
        return match ($message) {
            'Insufficient funds' => 'Недостатньо коштів',
            'Timeout' => 'Таймаут',
            'Signature error' => 'Помилка підпису',
            'Unknown scenario' => 'Невідомий сценарій',
            'Scenario not supported' => 'Сценарій не підтримується',
            default => $message,
        };
    }

    /**
     * @param string $message
     * @return string
     */
    private function translateToRu(string $message): string
    {
        return match ($message) {
            'Insufficient funds' => 'Недостаточно средств',
            'Timeout' => 'Таймаут',
            'Signature error' => 'Ошибка подписи',
            'Unknown scenario' => 'Неизвестный сценарий',
            'Scenario not supported' => 'Сценарий не поддерживается',
            default => $message,
        };
    }
}
