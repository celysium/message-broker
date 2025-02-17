<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Drivers\RabbitMQ;
use Exception;
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
     * @throws Exception
     */
    public function driver($driver = null): MessageBrokerInterface
    {
        $driver = $driver ?: $this->getDefaultDriver();

        switch (strtolower($driver)) {
            case 'rabbitmq':
                return new RabbitMQ();
            default:
                throw new Exception("driver '$driver' not found");
        }
    }

    public function getDefaultDriver()
    {
        return config('message-broker.default');
    }
}
