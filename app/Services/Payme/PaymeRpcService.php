<?php

namespace App\Services\Payme;

use App\Exceptions\PaymeException;
use App\Repositories\TransactionRepository;
use App\Services\Contracts\TransactionHandlerInterface;
use App\Services\Payme\Handlers\CancelTransactionHandler;
use App\Services\Payme\Handlers\CheckTransactionHandler;
use App\Services\Payme\Handlers\CreateTransactionHandler;
use App\Services\Payme\Handlers\PerformTransactionHandler;

class PaymeRpcService
{
    /** @var array<string, TransactionHandlerInterface> */
    private array $handlers = [];

    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly PaymeDebugScenarioHandler $debugScenarioHandler
    ) {
        $this->handlers = [
            'CreateTransaction'  => new CreateTransactionHandler($this->transactionRepository),
            'PerformTransaction' => new PerformTransactionHandler($this->transactionRepository),
            'CheckTransaction'   => new CheckTransactionHandler($this->transactionRepository),
            'CancelTransaction'  => new CancelTransactionHandler($this->transactionRepository),
        ];
    }

    /**
     * Handle Payme RPC request.
     *
     * @param string $method
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     * @throws PaymeException
     */
    public function handle(string $method, array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->debugScenarioHandler->handle($params, $method);
        }

        if (!isset($this->handlers[$method])) {
            throw new PaymeException("Method $method not supported", -31000);
        }

        return $this->handlers[$method]->handle($params);
    }
}

