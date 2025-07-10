<?php

return [

    // Namespace used for resolving enums from URL like /api/enums/user-status
    'namespace' => 'App\\Enums',

    // Only these enums can be exposed via EnumController
    'allowed_enums' => [
        'PaymentProvider',
        'ScenarioType',
        'TransactionStatus',
    ],

    // Enable/Disable locale by request
    'enable_locale_middleware' => true,

    // Which languages are available
    'supported_locales' => [
        'en',
        'uk',
        'uz',
        'ru',
    ],
];
