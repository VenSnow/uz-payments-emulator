<?php

namespace App\Services\Uzum;

use App\Exceptions\UzumException;
use App\Repositories\TransactionRepository;
use App\Services\Uzum\Handlers\CheckTransactionHandler;
use App\Services\Uzum\Handlers\ConfirmTransactionHandler;
use App\Services\Uzum\Handlers\CreateTransactionHandler;
use App\Services\Uzum\Handlers\ReverseTransactionHandler;
use App\Services\Uzum\Handlers\StatusTransactionHandler;

class UzumService
{
    private array $handlers = [];

    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected UzumDebugScenarioHandler $debugScenarioHandler
    ) {
        $this->handlers = [
            'check'     => new CheckTransactionHandler($this->transactionRepository),
            'create'    => new CreateTransactionHandler($this->transactionRepository),
            'confirm'   => new ConfirmTransactionHandler($this->transactionRepository),
            'reverse'   => new ReverseTransactionHandler($this->transactionRepository),
            'status'    => new StatusTransactionHandler($this->transactionRepository)
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     * @throws UzumException
     */
    public function handle(string $method, array $params): array
    {
        if (isset($params['debug_scenario']) && app()->isLocal()) {
            return $this->debugScenarioHandler->handle($params, $method);
        }

        if (!isset($this->handlers[$method])) {
            throw new UzumException("Method $method not supported", '10000');
        }

        return $this->handlers[$method]->handle($params);
    }
}
