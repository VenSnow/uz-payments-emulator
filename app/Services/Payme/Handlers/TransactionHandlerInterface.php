<?php

namespace App\Services\Payme\Handlers;

interface TransactionHandlerInterface
{
    /**
     * Handle transaction request.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function handle(array $params): array;
}
