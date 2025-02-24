<?php

namespace Celysium\MessageBroker\Facades;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Message;
use Illuminate\Support\Facades\Facade;

/**
 * @method static MessageBrokerInterface driver(string $name = null)
 * @method static MessageBrokerInterface public(Message $message, callable $ack = null, callable $nack = null)
 * @method static MessageBrokerInterface batch(array $messages, callable $ack = null, callable $nack = null)
 * @method static MessageBrokerInterface transaction(array $messages)
 * @method static MessageBrokerInterface consume()
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
