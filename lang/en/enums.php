<?php

return [
    'PaymentProvider.payme'             => 'PayMe',
    'PaymentProvider.uzum'              => 'Uzum',
    'PaymentProvider.click'             => 'Click',

    'TransactionStatus.pending'         => 'Pending',
    'TransactionStatus.success'         => 'Success',
    'TransactionStatus.failed'          => 'Failed',
    'TransactionStatus.cancelled'       => 'Cancelled',
    'TransactionStatus.created'         => 'Created',
    'TransactionStatus.confirmed'       => 'Confirmed',
    'TransactionStatus.reversed'        => 'Reversed',

    'ScenarioType.success'              => 'OK',
    'ScenarioType.insufficient_funds'   => 'Insufficient funds',
    'ScenarioType.timeout'              => 'Gateway Timeout',
    'ScenarioType.signature_error'      => 'Invalid signature',
];
