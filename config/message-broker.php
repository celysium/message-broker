<?php

return [
    'MESSAGE_BROKER_DEFAULT_DRIVER' => env('MESSAGE_BROKER_DEFAULT_DRIVER', 'Kafka'),
    'Kafka' => [
        'debug' => env('KAFKA_DEBUG', false),
        'topic' => env('KAFKA_TOPIC', ''),
        'consumer' => env('KAFKA_CONSUMER', '')
    ]
];
