<?php

namespace Celysium\MessageBroker\Facades;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageBrokerInterface driver(string $name = null)
 * @method static MessageBrokerInterface send(string $event, array $data)
 * @method static MessageBrokerInterface consumer()
 */
class MessageBroker extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'message-broker';
    }
}