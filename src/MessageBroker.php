<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Drivers\Kafka;
use Celysium\MessageBroker\Drivers\RabbitMQ;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

class MessageBroker extends Manager
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * @param $driver
     * @return MessageBrokerInterface
     */
    public function driver($driver = null): MessageBrokerInterface
    {
        $driver = $driver ?: $this->getDefaultDriver();

        return match (strtolower($driver)) {
            'rabbitmq' => new RabbitMQ(),
            'kafka' => new Kafka()
        };
    }

    public function getDefaultDriver()
    {
        return config('message-broker.default');
    }
}
