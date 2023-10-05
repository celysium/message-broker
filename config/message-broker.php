<?php

return [
    'default' => env('MESSAGE_BROKER_DEFAULT_DRIVER', 'rabbitmq'),
    'kafka' => [
        'debug' => env('KAFKA_DEBUG', false),
        'topic' => env('KAFKA_TOPIC', ''),
        'consumer' => env('KAFKA_CONSUMER', '')
    ],
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', ''),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', ''),
        'password' => env('RABBITMQ_PASSWORD', ''),
        'queue' => env('RABBITMQ_QUEUE', 'default'),
        'exchange' => env('RABBITMQ_EXCHANGE', 'exchange'),
        'exchange_key' => env('RABBITMQ_KEY', 'key'),
    ]
];
