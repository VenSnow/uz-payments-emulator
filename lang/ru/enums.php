<?php

return [
    'PaymentProvider.payme'             => 'PayMe',
    'PaymentProvider.uzum'              => 'Uzum',
    'PaymentProvider.click'             => 'Click',

    'TransactionStatus.pending'         => 'В ожидании',
    'TransactionStatus.success'         => 'Успешно',
    'TransactionStatus.failed'          => 'Ошибка',
    'TransactionStatus.cancelled'       => 'Отменено',
    'TransactionStatus.created'         => 'Создано',
    'TransactionStatus.confirmed'       => 'Подтверждено',
    'TransactionStatus.reversed'        => 'Возвращено',

    'ScenarioType.success'              => 'Успешная оплата',
    'ScenarioType.insufficient_funds'   => 'Недостаточно средств',
    'ScenarioType.timeout'              => 'Таймаут',
    'ScenarioType.signature_error'      => 'Ошибка подписи',
];
