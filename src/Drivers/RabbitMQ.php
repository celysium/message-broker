<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;

class RabbitMQ implements MessageBrokerInterface
{
    public function send(string $event, array $data)
    {
        // TODO: Implement send() method.
    }

    public function consumer()
    {
        // TODO: Implement consumer() method.
    }
}