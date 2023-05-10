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
        'queue' => env('RABBITMQ_QUEUE', ''),
        'sleep' => env('RABBITMQ_SLEEP', 1),
        'timeout' => env('RABBITMQ_TIMEOUT', 180),
        'tries' => env('RABBITMQ_TRIES', 5),
        'rest' => env('RABBITMQ_REST', 1)
    ]
];
