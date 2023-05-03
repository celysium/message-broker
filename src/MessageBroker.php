<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use http\Exception\InvalidArgumentException;

class MessageBroker
{
    public function __construct()
    {
        $this->driver();
    }

    /**
     * @param $name
     * @return MessageBrokerInterface
     */
    public function driver($name = null): MessageBrokerInterface
    {
        $name = $name ?: config('MESSAGE_BROKER_DEFAULT_DRIVER');

        if (class_exists($name)) {
            return new $name();
        }

        throw new InvalidArgumentException('Driver not found');
    }
}